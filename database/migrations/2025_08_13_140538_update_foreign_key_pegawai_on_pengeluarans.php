<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus constraint lama
        DB::statement('ALTER TABLE public.pengeluarans DROP CONSTRAINT pengeluarans_pegawai_id_foreign');

        // Buat constraint baru dengan ON DELETE RESTRICT
        DB::statement('ALTER TABLE public.pengeluarans
            ADD CONSTRAINT pengeluarans_pegawai_id_foreign
            FOREIGN KEY (pegawai_id)
            REFERENCES public.pegawais (id)
            ON UPDATE NO ACTION
            ON DELETE RESTRICT');
    }

    public function down(): void
    {
        // Balik ke ON DELETE SET NULL kalau di-rollback
        DB::statement('ALTER TABLE public.pengeluarans DROP CONSTRAINT pengeluarans_pegawai_id_foreign');

        DB::statement('ALTER TABLE public.pengeluarans
            ADD CONSTRAINT pengeluarans_pegawai_id_foreign
            FOREIGN KEY (pegawai_id)
            REFERENCES public.pegawais (id)
            ON UPDATE NO ACTION
            ON DELETE SET NULL');
    }
};
