<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ubah timezone Laravel di config (tidak bisa via migration, tapi bisa via env)
        // Ini hanya untuk record, perubahan permanen tetap di config/app.php
        
        // 2. Update data yang sudah ada ke WITA (UTC+8)
        DB::statement("UPDATE pesan SET created_at = DATE_ADD(created_at, INTERVAL 8 HOUR) WHERE created_at IS NOT NULL");
        DB::statement("UPDATE pesan SET updated_at = DATE_ADD(updated_at, INTERVAL 8 HOUR) WHERE updated_at IS NOT NULL");
        DB::statement("UPDATE percakapan SET created_at = DATE_ADD(created_at, INTERVAL 8 HOUR) WHERE created_at IS NOT NULL");
        DB::statement("UPDATE percakapan SET updated_at = DATE_ADD(updated_at, INTERVAL 8 HOUR) WHERE updated_at IS NOT NULL");
        DB::statement("UPDATE percakapan SET pesan_terakhir_at = DATE_ADD(pesan_terakhir_at, INTERVAL 8 HOUR) WHERE pesan_terakhir_at IS NOT NULL");
        
        // 3. Update notifikasi
        DB::statement("UPDATE notifikasi SET created_at = DATE_ADD(created_at, INTERVAL 8 HOUR) WHERE created_at IS NOT NULL");
        DB::statement("UPDATE notifikasi SET updated_at = DATE_ADD(updated_at, INTERVAL 8 HOUR) WHERE updated_at IS NOT NULL");
        DB::statement("UPDATE notifikasi SET dibaca_at = DATE_ADD(dibaca_at, INTERVAL 8 HOUR) WHERE dibaca_at IS NOT NULL");
        
        // 4. Update pengguna (last_login)
        DB::statement("UPDATE pengguna SET last_login = DATE_ADD(last_login, INTERVAL 8 HOUR) WHERE last_login IS NOT NULL");
        DB::statement("UPDATE pengguna SET created_at = DATE_ADD(created_at, INTERVAL 8 HOUR) WHERE created_at IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: kurangi 8 jam (kembali ke UTC)
        DB::statement("UPDATE pesan SET created_at = DATE_SUB(created_at, INTERVAL 8 HOUR) WHERE created_at IS NOT NULL");
        DB::statement("UPDATE pesan SET updated_at = DATE_SUB(updated_at, INTERVAL 8 HOUR) WHERE updated_at IS NOT NULL");
        DB::statement("UPDATE percakapan SET created_at = DATE_SUB(created_at, INTERVAL 8 HOUR) WHERE created_at IS NOT NULL");
        DB::statement("UPDATE percakapan SET updated_at = DATE_SUB(updated_at, INTERVAL 8 HOUR) WHERE updated_at IS NOT NULL");
        DB::statement("UPDATE percakapan SET pesan_terakhir_at = DATE_SUB(pesan_terakhir_at, INTERVAL 8 HOUR) WHERE pesan_terakhir_at IS NOT NULL");
        
        DB::statement("UPDATE notifikasi SET created_at = DATE_SUB(created_at, INTERVAL 8 HOUR) WHERE created_at IS NOT NULL");
        DB::statement("UPDATE notifikasi SET updated_at = DATE_SUB(updated_at, INTERVAL 8 HOUR) WHERE updated_at IS NOT NULL");
        DB::statement("UPDATE notifikasi SET dibaca_at = DATE_SUB(dibaca_at, INTERVAL 8 HOUR) WHERE dibaca_at IS NOT NULL");
        
        DB::statement("UPDATE pengguna SET last_login = DATE_SUB(last_login, INTERVAL 8 HOUR) WHERE last_login IS NOT NULL");
        DB::statement("UPDATE pengguna SET created_at = DATE_SUB(created_at, INTERVAL 8 HOUR) WHERE created_at IS NOT NULL");
    }
};