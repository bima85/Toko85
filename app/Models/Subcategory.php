<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = ['kode_subkategori', 'nama_subkategori', 'description', 'category_id'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->kode_subkategori)) {
                $model->kode_subkategori = 'SUB' . strtoupper(uniqid());
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
