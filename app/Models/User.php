<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nip',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // kasih tahu Laravel kalau login pakai nip
    public function getAuthIdentifierName()
    {
        return 'nip';
    }

    // relasi ke Pegawai
    public function pegawai()
    {
        return $this->hasOne(Pegawai::class, 'user_id');
    }
}
