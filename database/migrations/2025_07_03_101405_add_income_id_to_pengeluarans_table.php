<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->unsignedBigInteger('income_id')->nullable()->after('struk_id');
            $table->foreign('income_id')->references('id')->on('incomes')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->dropForeign(['income_id']);
            $table->dropColumn('income_id');
        });
    }
};
