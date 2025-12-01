<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'nip',
        'divisi_id',
        'user_id',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function divisi()
    {
        return $this->belongsTo(Division::class);
    }

        public function user()
    {
        return $this->belongsTo(User::class);
    }
}