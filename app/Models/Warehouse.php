<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'kode_gudang',
        'nama_gudang',
        'alamat',
        'telepon',
        'pic',
        'keterangan',
    ];
}
