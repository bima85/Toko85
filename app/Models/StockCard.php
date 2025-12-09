<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockCard extends Model
{
   use HasFactory;

   protected $fillable = [
      'product_id',
      'batch_id',
      'type',
      'qty',
      'from_location',
      'to_location',
      'reference_type',
      'reference_id',
      'note',
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
    * Relasi ke StockBatch
    */
   public function batch(): BelongsTo
   {
      return $this->belongsTo(StockBatch::class);
   }

   /**
    * Relasi Polymorphic ke Purchase/Sale/Adjustment (reference_type & reference_id)
    */
   public function referenceable(): MorphTo
   {
      return $this->morphTo('referenceable', 'reference_type', 'reference_id');
   }

   /**
    * Relasi khusus ke Purchase
    */
   public function purchase(): BelongsTo
   {
      return $this->belongsTo(Purchase::class, 'reference_id')
         ->where('reference_type', 'purchase');
   }

   /**
    * Relasi khusus ke Sale
    */
   public function sale(): BelongsTo
   {
      return $this->belongsTo(Sale::class, 'reference_id')
         ->where('reference_type', 'sale');
   }

   /**
    * Scope: Filter berdasarkan tipe pergerakan
    */
   public function scopeByType($query, $type)
   {
      return $query->where('type', $type);
   }

   /**
    * Scope: Urutkan berdasarkan created_at DESC
    */
   public function scopeLatestFirst($query)
   {
      return $query->orderBy('created_at', 'desc');
   }
}
