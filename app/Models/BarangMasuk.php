<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_toko',
        'nomor_struk',
        'tanggal',
        'daftar_barang',
        'total',
        'jumlah_item',
        'bukti_pembayaran',
        'pegawai_id',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        'daftar_barang' => 'array',
        'total' => 'decimal:2',
    ];

    /**
     * Hitung total seluruh barang masuk dari semua struk pemasukan.
     */
    public static function totalBarangMasuk()
    {
        return static::sum('jumlah_item');
    }
}
