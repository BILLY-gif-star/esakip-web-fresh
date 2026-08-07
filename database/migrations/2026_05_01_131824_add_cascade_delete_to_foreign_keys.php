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

    /**
     * Daftar foreign key yang perlu diubah jadi ON DELETE CASCADE.
     * Format: [table, constraint_name, column, referenced_table, referenced_column]
     */
    private array $foreignKeys = [
        ['lke_dokumen_kriteria', 'lke_dokumen_kriteria_uploaded_by_foreign', 'uploaded_by', 'pengguna', 'id'],
        ['notifikasi', 'notifikasi_user_id_pengguna_foreign', 'user_id', 'pengguna', 'id'],
        ['percakapan', 'percakapan_admin_user_id_foreign', 'admin_user_id', 'pengguna', 'id'],
        ['percakapan', 'percakapan_opd_user_id_foreign', 'opd_user_id', 'pengguna', 'id'],
        ['pesan', 'pesan_pengirim_id_foreign', 'pengirim_id', 'pengguna', 'id'],
    ];

    public function up(): void
    {
        foreach ($this->foreignKeys as [$table, $constraint, $column, $refTable, $refColumn]) {
            try {
                Schema::table($table, function (Blueprint $blueprint) use ($constraint) {
                    $blueprint->dropForeign($constraint);
                });
            } catch (\Exception $e) {
                // Constraint tidak ada, lanjut saja
            }

            Schema::table($table, function (Blueprint $blueprint) use ($constraint, $column, $refTable, $refColumn) {
                $blueprint->foreign($column, $constraint)
                    ->references($refColumn)->on($refTable)
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        // Kembalikan ke RESTRICT (tanpa CASCADE)
        foreach ($this->foreignKeys as [$table, $constraint, $column, $refTable, $refColumn]) {
            try {
                Schema::table($table, function (Blueprint $blueprint) use ($constraint) {
                    $blueprint->dropForeign($constraint);
                });
            } catch (\Exception $e) {
                // Constraint tidak ada, lanjut saja
            }

            Schema::table($table, function (Blueprint $blueprint) use ($constraint, $column, $refTable, $refColumn) {
                $blueprint->foreign($column, $constraint)
                    ->references($refColumn)->on($refTable);
            });
        }
    }
};
