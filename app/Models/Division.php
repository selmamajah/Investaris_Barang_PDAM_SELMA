<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi (mass assignment)
    protected $fillable = ['name']; 
    protected $table = 'divisions'; 


// Division.php
public function pegawais()
{
    return $this->hasMany(Pegawai::class, 'divisi_id');
}

}
