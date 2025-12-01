<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('pengeluarans', function (Blueprint $table) {
            if (!Schema::hasColumn('pengeluarans', 'nomor_struk')) {
                $table->string('nomor_struk')->after('tanggal');
            }

            if (!Schema::hasColumn('pengeluarans', 'nama_barang')) {
                $table->string('nama_barang')->nullable()->after('nomor_struk');
            }

            if (!Schema::hasColumn('pengeluarans', 'harga_satuan')) {
                $table->decimal('harga_satuan', 12, 2)->nullable()->after('nama_barang');
            }

            if (!Schema::hasColumn('pengeluarans', 'sub_total')) {
                $table->decimal('sub_total', 12, 2)->nullable()->after('harga_satuan');
            }
        });
    }

    public function down()
    {
        Schema::table('pengeluarans', function (Blueprint $table) {
            $columns = ['nomor_struk', 'nama_barang', 'harga_satuan', 'sub_total'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('pengeluarans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
