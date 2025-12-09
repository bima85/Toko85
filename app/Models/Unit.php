<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = [
        'kode_unit',
        'nama_unit',
        'description',
        'parent_unit_id',
        'conversion_value',
        'is_base_unit',
    ];

    protected $casts = [
        'is_base_unit' => 'boolean',
        'conversion_value' => 'decimal:6',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'satuan', 'nama_unit');
    }

    public function parentUnit()
    {
        return $this->belongsTo(Unit::class, 'parent_unit_id');
    }

    public function childUnits()
    {
        return $this->hasMany(Unit::class, 'parent_unit_id');
    }
}
