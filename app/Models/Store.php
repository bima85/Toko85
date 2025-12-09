<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'kode_toko',
        'nama_toko',
        'alamat',
        'telepon',
        'pic',
        'tipe_toko',
        'keterangan',
    ];

    protected $casts = [
        'tipe_toko' => 'string',
    ];
}
