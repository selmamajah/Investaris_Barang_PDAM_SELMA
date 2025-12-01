<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('master_barangs') && !Schema::hasTable('master_barang')) {
            Schema::rename('master_barangs', 'master_barang');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('master_barang') && !Schema::hasTable('master_barangs')) {
            Schema::rename('master_barang', 'master_barangs');
        }
    }
};
