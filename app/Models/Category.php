<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['kode_kategori', 'nama_kategori', 'description'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->kode_kategori)) {
                $model->kode_kategori = 'KAT' . strtoupper(uniqid());
            }
        });
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(Subcategory::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
