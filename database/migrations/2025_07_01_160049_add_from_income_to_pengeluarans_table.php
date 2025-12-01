<?php
// File: 2025_07_01_160049_add_from_income_to_pengeluarans_table.php

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
            // File ini SEKARANG HANYA menambahkan 'from_income'
            // Kita sudah menghapus kode 'struk_id' dari sini
            $table->boolean('from_income')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->dropColumn('from_income');
        });
    }
};