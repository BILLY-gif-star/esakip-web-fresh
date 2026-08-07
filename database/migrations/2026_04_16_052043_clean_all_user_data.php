<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nonaktifkan foreign key check sementara (portable: MySQL & Postgres)
        Schema::disableForeignKeyConstraints();

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
        Schema::enableForeignKeyConstraints();

        // 10. Reset auto increment untuk tabel yang sudah dikosongkan
        // Note: truncate() di atas sebenarnya sudah reset auto-increment/sequence
        // otomatis di kedua driver (MySQL & Postgres), tapi kita tetap set eksplisit
        // untuk tabel yang datanya dihapus pakai delete() (bukan truncate), seperti
        // 'pengguna' yang butuh next id = 2.
        $driver = DB::connection()->getDriverName();

        $tables = [
            'notifikasi' => 1,
            'pesan' => 1,
            'percakapan' => 1,
            'dokumen_opd' => 1,
            'perjanjian_kinerja' => 1,
            'lke_penilaian' => 1,
            'lke_penilaian_kriteria' => 1,
            'lke_dokumen_kriteria' => 1,
            'klaster_penilaian' => 1,
            'klaster_penilaian_kriteria' => 1,
            'klaster_dokumen_kriteria' => 1,
            'pengukuran_periodik' => 1,
            'pengguna' => 2,
        ];

        foreach ($tables as $table => $nextValue) {
            if ($driver === 'pgsql') {
                // Postgres pakai sequence, bukan AUTO_INCREMENT
                DB::statement("SELECT setval(pg_get_serial_sequence('{$table}', 'id'), {$nextValue}, false)");
            } else {
                // MySQL / MariaDB
                DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = {$nextValue}");
            }
        }
    }

    public function down(): void
    {
        // Rollback tidak diperlukan
    }
};
