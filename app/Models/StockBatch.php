<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'location_type',
        'location_id',
        'locationable_id',
        'locationable_type',
        'nama_tumpukan',
        'qty',
        'note',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
    ];

    /**
     * Relasi ke Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi polymorphic ke Store atau Warehouse
     */
    public function location(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relasi ke StockCard (histori pergerakan stok)
     */
    public function stockCards(): HasMany
    {
        return $this->hasMany(StockCard::class, 'batch_id');
    }

    /**
     * Scope: Ambil batch aktif (qty > 0)
     */
    public function scopeActive($query)
    {
        return $query->where('qty', '>', 0);
    }

    /**
     * Scope: Filter berdasarkan lokasi
     */
    public function scopeByLocation($query, $locationType)
    {
        return $query->where('location_type', $locationType);
    }

    /**
     * Scope: Urutkan berdasarkan updated_at DESC
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderBy('updated_at', 'desc');
    }

    /**
     * Scope: Group by tumpukan (slot) dengan total qty
     */
    public function scopeGroupByTumpukan($query)
    {
        return $query->selectRaw('nama_tumpukan, location_type, SUM(qty) as total_qty, COUNT(*) as batch_count')
            ->groupBy('nama_tumpukan', 'location_type')
            ->orderBy('nama_tumpukan');
    }

    /**
     * Static method: Get summary stok per tumpukan
     */
    public static function getTumpukanSummary($locationType = null)
    {
        $query = self::active()->with('product');

        if ($locationType) {
            $query->where('location_type', $locationType);
        }

        return $query->selectRaw('nama_tumpukan, location_type, product_id, SUM(qty) as total_qty, COUNT(*) as batch_count')
            ->groupBy('nama_tumpukan', 'location_type', 'product_id')
            ->orderBy('nama_tumpukan')
            ->get();
    }

    /**
     * Static method: Get total qty semua tumpukan
     */
    public static function getTotalQtyAllTumpukan($locationType = null)
    {
        $query = self::active();

        if ($locationType) {
            $query->where('location_type', $locationType);
        }

        return $query->sum('qty');
    }

    /**
     * Boot: Keep batch even if qty = 0 for historical records
     * Batch dengan qty=0 masih bisa digunakan untuk tracking history
     */
    protected static function boot()
    {
        parent::boot();

        // Commented out auto-delete to preserve batch history
        // static::updated(function ($model) {
        //     if ($model->qty <= 0) {
        //         $model->delete();
        //     }
        // });
    }
}
