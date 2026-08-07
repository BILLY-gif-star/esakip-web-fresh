<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Matikan auto-wrap transaction Laravel. Wajib untuk migration ini karena
     * ada try/catch di sekitar DDL (dropForeign) yang mungkin gagal (constraint
     * tidak ada). Di Postgres, 1 statement gagal dalam transaction akan
     * memblokir semua statement berikutnya sampai di-rollback, walau exception-nya
     * sudah ditangkap try/catch di level PHP. MySQL tidak punya masalah ini.
     */
    public $withinTransaction = false;

    public function up(): void
    {
        // Hapus foreign key yang bermasalah jika ada (portable Schema Builder)
        foreach ([
            'lke_dokumen_kriteria_kriteria_id_foreign',
            'lke_dokumen_kriteria_perangkat_daerah_id_foreign',
            'lke_dokumen_kriteria_uploaded_by_foreign',
        ] as $constraint) {
            try {
                Schema::table('lke_dokumen_kriteria', function (Blueprint $table) use ($constraint) {
                    $table->dropForeign($constraint);
                });
            } catch (\Exception $e) {
                // Constraint tidak ada, lanjut saja
            }
        }

        // Ubah tipe data kolom agar sesuai
        Schema::table('lke_dokumen_kriteria', function (Blueprint $table) {
            $table->unsignedInteger('kriteria_id')->change();
            $table->unsignedInteger('perangkat_daerah_id')->change();
            $table->unsignedInteger('uploaded_by')->change();
        });

        // Tambah index untuk performa query
        Schema::table('lke_dokumen_kriteria', function (Blueprint $table) {
            $table->index(['perangkat_daerah_id', 'tahun', 'kriteria_id'], 'lke_dokumen_filter_index');
            $table->index(['created_at'], 'lke_dokumen_created_index');
        });

        // Tambah foreign key baru
        Schema::table('lke_dokumen_kriteria', function (Blueprint $table) {
            $table->foreign('kriteria_id', 'lke_dokumen_kriteria_kriteria_id_foreign')
                ->references('id')->on('lke_kriteria')
                ->onDelete('cascade');

            $table->foreign('perangkat_daerah_id', 'lke_dokumen_kriteria_perangkat_daerah_id_foreign')
                ->references('id')->on('perangkat_daerah')
                ->onDelete('cascade');

            $table->foreign('uploaded_by', 'lke_dokumen_kriteria_uploaded_by_foreign')
                ->references('id')->on('pengguna')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        foreach ([
            'lke_dokumen_kriteria_kriteria_id_foreign',
            'lke_dokumen_kriteria_perangkat_daerah_id_foreign',
            'lke_dokumen_kriteria_uploaded_by_foreign',
        ] as $constraint) {
            try {
                Schema::table('lke_dokumen_kriteria', function (Blueprint $table) use ($constraint) {
                    $table->dropForeign($constraint);
                });
            } catch (\Exception $e) {}
        }

        Schema::table('lke_dokumen_kriteria', function (Blueprint $table) {
            $table->dropIndex('lke_dokumen_filter_index');
            $table->dropIndex('lke_dokumen_created_index');
        });
    }
};
