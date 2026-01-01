<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'description',
        'satuan',
        'supplier_id',
        'category_id',
        'subcategory_id'
    ];

    protected $casts = [
        //
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    /**
     * Relasi ke StockBatch
     */
    public function stockBatches(): HasMany
    {
        return $this->hasMany(StockBatch::class);
    }

    /**
     * Hitung total stok dari semua batch aktif
     */
    public function getTotalStockAttribute(): float
    {
        return $this->stockBatches()->active()->sum('qty');
    }
}
