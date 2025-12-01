<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('struks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_toko');
            $table->string('nomor_struk');
            $table->date('tanggal_struk');
            $table->json('items');
            $table->integer('total_harga');
            $table->string('foto_struk')->nullable();
            
            // INI YANG PALING PENTING:
            $table->string('status')->default('pending'); 
            
            $table->boolean('is_used')->default(false);
            $table->date('tanggal_keluar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('struks');
    }
};