<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('master_barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang')->unique();
            $table->enum('status', ['progress', 'completed'])->default('progress'); // 👈 status
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_barang');
    }
};