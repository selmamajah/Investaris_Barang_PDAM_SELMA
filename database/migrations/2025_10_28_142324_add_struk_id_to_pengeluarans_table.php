<?php
// File: 2025_10_28_142324_add_struk_id_to_pengeluarans_table.php

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
        Schema::table('pengeluarans', function (Blueprint $table) {
            // Kode 'struk_id' kita pindahkan ke file BARU ini
            // Ini aman karena file ini berjalan SETELAH tabel 'struks' dibuat
            $table->foreignId('struk_id')->nullable()->constrained('struks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->dropForeign(['struk_id']);
            $table->dropColumn('struk_id');
        });
    }
};