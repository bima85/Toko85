<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_invoice',
        'delivery_note_number',
        'delivery_date',
        'delivery_notes',
        'tanggal_penjualan',
        'customer_id',
        'store_id',
        'warehouse_id',
        'status',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_penjualan' => 'datetime',
        'delivery_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Relasi ke StockCard (audit trail dari transaksi penjualan)
     */
    public function stockCards()
    {
        return $this->hasMany(StockCard::class, 'reference_id')
            ->where('reference_type', 'sale');
    }
}
