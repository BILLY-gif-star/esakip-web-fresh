<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah index untuk performa
        Schema::table('notifikasi', function (Blueprint $table) {
            // Tambah index jika belum ada
            $table->index(['user_id', 'dibaca_at'], 'notifikasi_user_dibaca_index');
        });

        // Hapus foreign key yang bermasalah
        try {
            DB::statement('ALTER TABLE `notifikasi` DROP FOREIGN KEY `notifikasi_user_id_foreign`');
        } catch (\Exception $e) {}

        // Tambah foreign key ke pengguna
        try {
            DB::statement('
                ALTER TABLE `notifikasi` 
                ADD CONSTRAINT `notifikasi_user_id_pengguna_foreign` 
                FOREIGN KEY (`user_id`) 
                REFERENCES `pengguna`(`id`) 
                ON DELETE CASCADE
            ');
        } catch (\Exception $e) {
            // Jika gagal, biarkan saja tanpa foreign key
        }
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE `notifikasi` DROP FOREIGN KEY `notifikasi_user_id_pengguna_foreign`');
        } catch (\Exception $e) {}

        Schema::table('notifikasi', function (Blueprint $table) {
            $table->dropIndex('notifikasi_user_dibaca_index');
        });
    }
};