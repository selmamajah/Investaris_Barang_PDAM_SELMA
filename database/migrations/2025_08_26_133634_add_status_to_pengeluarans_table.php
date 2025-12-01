<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->string('status')->default('progress')->after('jumlah_item');
        });
    }

    public function down()
    {
        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};