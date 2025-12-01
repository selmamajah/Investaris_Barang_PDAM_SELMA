<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    //

    public function pengeluaran()
    {
        return $this->hasOne(Pengeluaran::class, 'from_income');
    }
}
