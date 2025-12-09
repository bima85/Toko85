<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Store;
use App\Models\Warehouse;

class Purchase extends Model
{
    protected $fillable = [
        'no_invoice',
        'tanggal_pembelian',
        'supplier_id',
        'store_id',
        'warehouse_id',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pembelian' => 'date',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    /**
     * Relasi ke StockCard (audit trail dari transaksi pembelian)
     */
    public function stockCards()
    {
        return $this->hasMany(StockCard::class, 'reference_id')
            ->where('reference_type', 'purchase');
    }

    public function getTotalPembelianAttribute()
    {
        return $this->purchaseItems->sum('total');
    }
}
