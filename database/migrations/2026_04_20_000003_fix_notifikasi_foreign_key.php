<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
        // 1. Hapus foreign key constraint yang bermasalah (portable Schema Builder)
        foreach (['notifikasi_user_id_foreign', 'notifikasi_user_id_foreign_1', 'notifikasi_user_id_pengguna_foreign'] as $constraint) {
            try {
                Schema::table('notifikasi', function (Blueprint $table) use ($constraint) {
                    $table->dropForeign($constraint);
                });
            } catch (\Exception $e) {
                // Constraint tidak ada, lanjut saja
            }
        }

        // 2. Ubah tipe data user_id jadi unsigned integer
        // (butuh doctrine/dbal kalau Laravel < 11; Laravel 11+ sudah native)
        Schema::table('notifikasi', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->change();
        });

        // 3. Isi tabel users dengan data dari pengguna (portable, tanpa raw CONCAT/INSERT IGNORE)
        $penggunaRows = DB::table('pengguna')->select('id', 'nama', 'username', 'password', 'created_at')->get();

        $rows = $penggunaRows->map(function ($row) {
            return [
                'id' => $row->id,
                'name' => $row->nama,
                'email' => $row->username . '@localhost',
                'password' => $row->password,
                'created_at' => $row->created_at,
                'updated_at' => $row->created_at,
            ];
        })->toArray();

        if (! empty($rows)) {
            // insertOrIgnore = portable, setara "INSERT IGNORE" di MySQL & "ON CONFLICT DO NOTHING" di Postgres
            DB::table('users')->insertOrIgnore($rows);
        }

        // 4. Tambah foreign key baru ke pengguna.id
        Schema::table('notifikasi', function (Blueprint $table) {
            $table->foreign('user_id', 'notifikasi_user_id_pengguna_foreign')
                ->references('id')->on('pengguna')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        try {
            Schema::table('notifikasi', function (Blueprint $table) {
                $table->dropForeign('notifikasi_user_id_pengguna_foreign');
            });
        } catch (\Exception $e) {}

        // Kembalikan tipe data ke bigint
        Schema::table('notifikasi', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
        });
    }
};
