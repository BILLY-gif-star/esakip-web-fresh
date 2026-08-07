<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Matikan auto-wrap transaction Laravel. Wajib untuk migration ini karena
     * ada try/catch di sekitar DDL (dropForeign/foreign) yang mungkin gagal.
     * Di Postgres, 1 statement gagal dalam transaction akan memblokir semua
     * statement berikutnya sampai di-rollback, walau exception-nya sudah
     * ditangkap try/catch di level PHP. MySQL tidak punya masalah ini.
     */
    public $withinTransaction = false;

    public function up(): void
    {
        // Tambah index untuk performa
        Schema::table('notifikasi', function (Blueprint $table) {
            $table->index(['user_id', 'dibaca_at'], 'notifikasi_user_dibaca_index');
        });

        // Hapus foreign key yang bermasalah (portable Schema Builder)
        try {
            Schema::table('notifikasi', function (Blueprint $table) {
                $table->dropForeign('notifikasi_user_id_foreign');
            });
        } catch (\Exception $e) {
            // Constraint tidak ada, lanjut saja
        }

        // Tambah foreign key ke pengguna
        try {
            Schema::table('notifikasi', function (Blueprint $table) {
                $table->foreign('user_id', 'notifikasi_user_id_pengguna_foreign')
                    ->references('id')->on('pengguna')
                    ->onDelete('cascade');
            });
        } catch (\Exception $e) {
            // Jika gagal, biarkan saja tanpa foreign key
        }
    }

    public function down(): void
    {
        try {
            Schema::table('notifikasi', function (Blueprint $table) {
                $table->dropForeign('notifikasi_user_id_pengguna_foreign');
            });
        } catch (\Exception $e) {}

        Schema::table('notifikasi', function (Blueprint $table) {
            $table->dropIndex('notifikasi_user_dibaca_index');
        });
    }
};
