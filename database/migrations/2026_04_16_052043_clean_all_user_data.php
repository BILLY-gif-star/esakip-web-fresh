<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nonaktifkan foreign key check sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // 1. Hapus semua notifikasi
        DB::table('notifikasi')->truncate();
        
        // 2. Hapus semua pesan chat (harus sebelum percakapan)
        DB::table('pesan')->truncate();
        
        // 3. Hapus semua percakapan
        DB::table('percakapan')->truncate();
        
        // 4. Hapus semua dokumen OPD
        DB::table('dokumen_opd')->truncate();
        
        // 5. Hapus semua template perjanjian kinerja
        DB::table('perjanjian_kinerja')->truncate();
        
        // 6. Hapus semua penilaian LKE
        DB::table('lke_penilaian')->truncate();
        DB::table('lke_penilaian_kriteria')->truncate();
        DB::table('lke_dokumen_kriteria')->truncate();
        
        // 7. Hapus semua penilaian Klaster
        DB::table('klaster_penilaian')->truncate();
        DB::table('klaster_penilaian_kriteria')->truncate();
        DB::table('klaster_dokumen_kriteria')->truncate();
        
        // 8. Hapus semua data pengukuran periodik
        DB::table('pengukuran_periodik')->truncate();
        
        // 9. Hapus semua user (kecuali admin id=1)
        DB::table('pengguna')->where('id', '!=', 1)->delete();
        
        // Aktifkan kembali foreign key check
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // 10. Reset auto increment untuk tabel yang sudah dikosongkan
        DB::statement('ALTER TABLE notifikasi AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE pesan AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE percakapan AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE dokumen_opd AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE perjanjian_kinerja AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE lke_penilaian AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE lke_penilaian_kriteria AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE lke_dokumen_kriteria AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE klaster_penilaian AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE klaster_penilaian_kriteria AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE klaster_dokumen_kriteria AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE pengukuran_periodik AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE pengguna AUTO_INCREMENT = 2');
    }

    public function down(): void
    {
        // Rollback tidak diperlukan
    }
};