<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_toko');
            $table->string('nomor_struk');
            $table->date('tanggal');
            $table->json('daftar_barang');
            $table->decimal('total', 15, 2);
            $table->integer('jumlah_item');
            $table->string('bukti_pembayaran')->nullable();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawais')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluarans');
    }
};
