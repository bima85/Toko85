<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'kode_pelanggan',
        'nama_pelanggan',
        'alamat',
        'telepon',
        'email',
        'keterangan',
    ];
}
