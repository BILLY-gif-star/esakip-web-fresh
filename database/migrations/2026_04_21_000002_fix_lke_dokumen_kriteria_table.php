<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus foreign key yang bermasalah jika ada
        try {
            DB::statement('ALTER TABLE `lke_dokumen_kriteria` DROP FOREIGN KEY `lke_dokumen_kriteria_kriteria_id_foreign`');
        } catch (\Exception $e) {}
        
        try {
            DB::statement('ALTER TABLE `lke_dokumen_kriteria` DROP FOREIGN KEY `lke_dokumen_kriteria_perangkat_daerah_id_foreign`');
        } catch (\Exception $e) {}
        
        try {
            DB::statement('ALTER TABLE `lke_dokumen_kriteria` DROP FOREIGN KEY `lke_dokumen_kriteria_uploaded_by_foreign`');
        } catch (\Exception $e) {}

        // Ubah tipe data kolom agar sesuai
        DB::statement('ALTER TABLE `lke_dokumen_kriteria` MODIFY `kriteria_id` INT(10) UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `lke_dokumen_kriteria` MODIFY `perangkat_daerah_id` INT(10) UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `lke_dokumen_kriteria` MODIFY `uploaded_by` INT(10) UNSIGNED NOT NULL');

        // Tambah index untuk performa query
        Schema::table('lke_dokumen_kriteria', function (Blueprint $table) {
            $table->index(['perangkat_daerah_id', 'tahun', 'kriteria_id'], 'lke_dokumen_filter_index');
            $table->index(['created_at'], 'lke_dokumen_created_index');
        });

        // Tambah foreign key baru
        DB::statement('ALTER TABLE `lke_dokumen_kriteria` 
            ADD CONSTRAINT `lke_dokumen_kriteria_kriteria_id_foreign` 
            FOREIGN KEY (`kriteria_id`) REFERENCES `lke_kriteria`(`id`) ON DELETE CASCADE');
            
        DB::statement('ALTER TABLE `lke_dokumen_kriteria` 
            ADD CONSTRAINT `lke_dokumen_kriteria_perangkat_daerah_id_foreign` 
            FOREIGN KEY (`perangkat_daerah_id`) REFERENCES `perangkat_daerah`(`id`) ON DELETE CASCADE');
            
        DB::statement('ALTER TABLE `lke_dokumen_kriteria` 
            ADD CONSTRAINT `lke_dokumen_kriteria_uploaded_by_foreign` 
            FOREIGN KEY (`uploaded_by`) REFERENCES `pengguna`(`id`) ON DELETE CASCADE');
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE `lke_dokumen_kriteria` DROP FOREIGN KEY `lke_dokumen_kriteria_kriteria_id_foreign`');
        } catch (\Exception $e) {}
        
        try {
            DB::statement('ALTER TABLE `lke_dokumen_kriteria` DROP FOREIGN KEY `lke_dokumen_kriteria_perangkat_daerah_id_foreign`');
        } catch (\Exception $e) {}
        
        try {
            DB::statement('ALTER TABLE `lke_dokumen_kriteria` DROP FOREIGN KEY `lke_dokumen_kriteria_uploaded_by_foreign`');
        } catch (\Exception $e) {}

        Schema::table('lke_dokumen_kriteria', function (Blueprint $table) {
            $table->dropIndex('lke_dokumen_filter_index');
            $table->dropIndex('lke_dokumen_created_index');
        });
    }
};