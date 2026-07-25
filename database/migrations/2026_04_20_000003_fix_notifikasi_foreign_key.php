<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hapus foreign key constraint yang bermasalah
        try {
            DB::statement('ALTER TABLE `notifikasi` DROP FOREIGN KEY `notifikasi_user_id_foreign`');
        } catch (\Exception $e) {}
        
        try {
            DB::statement('ALTER TABLE `notifikasi` DROP FOREIGN KEY `notifikasi_user_id_foreign_1`');
        } catch (\Exception $e) {}
        
        try {
            DB::statement('ALTER TABLE `notifikasi` DROP FOREIGN KEY `notifikasi_user_id_pengguna_foreign`');
        } catch (\Exception $e) {}

        // 2. Ubah tipe data user_id dari bigint menjadi int(10) unsigned
        DB::statement('ALTER TABLE `notifikasi` MODIFY `user_id` INT(10) UNSIGNED NOT NULL');

        // 3. Isi tabel users dengan data dari pengguna
        DB::statement("
            INSERT IGNORE INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `updated_at`)
            SELECT 
                `id`, 
                `nama`, 
                CONCAT(`username`, '@localhost'), 
                `password`, 
                `created_at`, 
                `created_at`
            FROM `pengguna`
        ");

        // 4. Tambah foreign key baru ke pengguna.id
        DB::statement('
            ALTER TABLE `notifikasi` 
            ADD CONSTRAINT `notifikasi_user_id_pengguna_foreign` 
            FOREIGN KEY (`user_id`) 
            REFERENCES `pengguna`(`id`) 
            ON DELETE CASCADE
        ');
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE `notifikasi` DROP FOREIGN KEY `notifikasi_user_id_pengguna_foreign`');
        } catch (\Exception $e) {}
        
        // Kembalikan tipe data ke bigint
        DB::statement('ALTER TABLE `notifikasi` MODIFY `user_id` BIGINT(20) UNSIGNED NOT NULL');
    }
};