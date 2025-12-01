<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Struk extends Model
{
    protected $fillable = [
        'nama_toko',
        'nomor_struk',
        'tanggal_struk',
        'tanggal_keluar', // ⬅️ tambahkan ini
        'items',
        'total_harga',
        'status',
        'foto_struk'
    ];
}
