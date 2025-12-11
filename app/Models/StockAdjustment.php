<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    protected $fillable = [
        'product_id',
        'store_id',
        'warehouse_id',
        'adjustment_type',
        'quantity',
        'stok_awal',
        'stok_masuk',
        'total_stok',
        'unit_id',
        'reason',
        'adjustment_date',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'adjustment_date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function getLocationAttribute()
    {
        if ($this->store_id) {
            return 'Toko: ' . $this->store->nama_toko;
        } elseif ($this->warehouse_id) {
            return 'Gudang: ' . $this->warehouse->nama_gudang;
        }
        return 'Tidak diketahui';
    }

    protected static function boot()
    {
        parent::boot();

        // DISABLED: Logika update batch sudah ditangani di StockBatchService
        // Observer ini menyebabkan bug dimana batch qty ter-update tidak sesuai
        // karena menghitung ulang dari semua adjustment tanpa memperhatikan nama_tumpukan

        // Ketika adjustment dibuat, update batch qty
        // static::created(function ($adjustment) {
        //     $adjustment->updateStockBatch();
        // });

        // Ketika adjustment diupdate, update batch qty
        // static::updated(function ($adjustment) {
        //     $adjustment->updateStockBatch();
        // });

        // Ketika adjustment dihapus, update batch qty
        // static::deleted(function ($adjustment) {
        //     $adjustment->updateStockBatchOnDelete();
        // });
    }

    /**
     * Update atau create StockBatch saat adjustment dibuat/diupdate
     */
    private function updateStockBatch()
    {
        $locationType = $this->store_id ? 'store' : 'warehouse';
        $locationId = $this->store_id ?? $this->warehouse_id;

        // Cari atau buat batch untuk produk ini di lokasi ini
        $batch = StockBatch::where('product_id', $this->product_id)
            ->where('location_type', $locationType)
            ->where('location_id', $locationId)
            ->first();

        if ($batch) {
            // Update qty berdasarkan semua adjustment
            $totalQty = StockAdjustment::where('product_id', $this->product_id)
                ->where($locationType === 'store' ? 'store_id' : 'warehouse_id', $locationId)
                ->get()
                ->reduce(function ($total, $adj) {
                    if ($adj->adjustment_type === 'add') {
                        return $total + $adj->quantity;
                    } else {
                        return $total - $adj->quantity;
                    }
                }, 0);

            $batch->update(['qty' => max(0, $totalQty)]);
        } else {
            // Create batch baru
            $initialQty = $this->adjustment_type === 'add' ? $this->quantity : -$this->quantity;
            StockBatch::create([
                'product_id' => $this->product_id,
                'location_type' => $locationType,
                'location_id' => $locationId,
                'nama_tumpukan' => 'Default',
                'qty' => max(0, $initialQty),
            ]);
        }
    }

    /**
     * Update StockBatch saat adjustment dihapus
     */
    private function updateStockBatchOnDelete()
    {
        $locationType = $this->store_id ? 'store' : 'warehouse';
        $locationId = $this->store_id ?? $this->warehouse_id;

        // Cari batch untuk produk ini di lokasi ini
        $batch = StockBatch::where('product_id', $this->product_id)
            ->where('location_type', $locationType)
            ->where('location_id', $locationId)
            ->first();

        if ($batch) {
            // Hitung ulang qty dari adjustment yang tersisa (exclude yang dihapus)
            $totalQty = StockAdjustment::where('product_id', $this->product_id)
                ->where($locationType === 'store' ? 'store_id' : 'warehouse_id', $locationId)
                ->get()
                ->reduce(function ($total, $adj) {
                    if ($adj->adjustment_type === 'add') {
                        return $total + $adj->quantity;
                    } else {
                        return $total - $adj->quantity;
                    }
                }, 0);

            if ($totalQty <= 0) {
                // Jika qty jadi 0 atau negatif, hapus batch
                $batch->delete();
            } else {
                // Update qty
                $batch->update(['qty' => $totalQty]);
            }
        }
    }
}
