<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── Tambah kolom ──────────────────────────────────────
        Schema::table('perangkat_daerah', function (Blueprint $table) {
            $table->enum('klaster', ['utama', 'pendukung', 'tambahan'])
                  ->nullable()
                  ->after('singkatan');
            $table->string('tugas', 300)
                  ->nullable()
                  ->after('klaster');
        });

        // ── Isi data klaster & tugas ──────────────────────────
        $this->seedKlasterData();
    }

    public function down(): void
    {
        Schema::table('perangkat_daerah', function (Blueprint $table) {
            $table->dropColumn(['klaster', 'tugas']);
        });
    }

    private function seedKlasterData(): void
    {
        // Data klaster sesuai file Excel
        $data = [
            // ========== KLASTER UTAMA (12 OPD) ==========
            ['id' => 32, 'klaster' => 'utama', 'tugas' => 'Perencanaan Pembangunan Daerah'],
            ['id' =>  9, 'klaster' => 'utama', 'tugas' => 'Pengawasan Internal'],
            ['id' => 10, 'klaster' => 'utama', 'tugas' => 'Pendidikan'],
            ['id' => 11, 'klaster' => 'utama', 'tugas' => 'Kesehatan'],
            ['id' => 12, 'klaster' => 'utama', 'tugas' => 'Pekerjaan Umum dan Penataan Ruang'],
            ['id' => 13, 'klaster' => 'utama', 'tugas' => 'Ketentraman, Ketertiban, dan Perlindungan Masyarakat'],
            ['id' => 14, 'klaster' => 'utama', 'tugas' => 'Sosial'],
            ['id' => 17, 'klaster' => 'utama', 'tugas' => 'Pertanian'],
            ['id' => 27, 'klaster' => 'utama', 'tugas' => 'Pertanian'], // DISNAK
            ['id' => 25, 'klaster' => 'utama', 'tugas' => 'Kelautan dan Perikanan'],
            ['id' => 26, 'klaster' => 'utama', 'tugas' => 'Pariwisata'],
            ['id' => 28, 'klaster' => 'utama', 'tugas' => 'Perindustrian dan Perdagangan'],

            // ========== KLASTER PENDUKUNG (11 OPD) ==========
            ['id' => 31, 'klaster' => 'pendukung', 'tugas' => 'Tenaga Kerja'],
            ['id' => 16, 'klaster' => 'pendukung', 'tugas' => 'Pemberdayaan Perempuan, Perlindungan Anak, Pengendalian Penduduk dan KB'],
            ['id' => 18, 'klaster' => 'pendukung', 'tugas' => 'Lingkungan Hidup'],
            ['id' => 30, 'klaster' => 'pendukung', 'tugas' => 'Administrasi Kependudukan dan Pencatatan Sipil'],
            ['id' => 19, 'klaster' => 'pendukung', 'tugas' => 'Pemberdayaan Masyarakat dan Desa'],
            ['id' => 20, 'klaster' => 'pendukung', 'tugas' => 'Perhubungan'],
            ['id' => 15, 'klaster' => 'pendukung', 'tugas' => 'Koperasi, Usaha Kecil dan Menengah'],
            ['id' => 22, 'klaster' => 'pendukung', 'tugas' => 'Penanaman Modal'],
            ['id' => 23, 'klaster' => 'pendukung', 'tugas' => 'Kepemudaan dan Olahraga'],
            ['id' => 24, 'klaster' => 'pendukung', 'tugas' => 'Kearsipan'],
            ['id' => 29, 'klaster' => 'pendukung', 'tugas' => 'Energi dan Sumber Daya Mineral'],

            // ========== KLASTER TAMBAHAN (19 OPD) ==========
            ['id' => 21, 'klaster' => 'tambahan', 'tugas' => 'Komunikasi dan Informatika'],
            ['id' =>  8, 'klaster' => 'tambahan', 'tugas' => 'Kesekretariatan'],
            ['id' =>  7, 'klaster' => 'tambahan', 'tugas' => 'Kesekretariatan'],
            ['id' => 35, 'klaster' => 'tambahan', 'tugas' => 'Kepegawaian'],
            ['id' => 36, 'klaster' => 'tambahan', 'tugas' => 'Kepegawaian'], // BPSDMD
            ['id' => 33, 'klaster' => 'tambahan', 'tugas' => 'Keuangan'],
            ['id' => 34, 'klaster' => 'tambahan', 'tugas' => 'Aset'],
            ['id' => 37, 'klaster' => 'tambahan', 'tugas' => 'Kesbangpol'],
            ['id' => 42, 'klaster' => 'tambahan', 'tugas' => 'Rumah Sakit Jiwa'],
            ['id' => 41, 'klaster' => 'tambahan', 'tugas' => 'Rumah Sakit'],
            ['id' => 40, 'klaster' => 'tambahan', 'tugas' => null], // BPNTT JKT
            ['id' =>  1, 'klaster' => 'tambahan', 'tugas' => null], // BIRO PEMERINTAHAN
            ['id' =>  2, 'klaster' => 'tambahan', 'tugas' => null], // BIRO HUKUM
            ['id' =>  3, 'klaster' => 'tambahan', 'tugas' => null], // BIRO PEREKONOMIAN
            ['id' =>  4, 'klaster' => 'tambahan', 'tugas' => null], // BIRO PBJ
            ['id' =>  6, 'klaster' => 'tambahan', 'tugas' => null], // BIRO UMUM
            ['id' =>  5, 'klaster' => 'tambahan', 'tugas' => null], // BIRO ORGANISASI
            ['id' => 39, 'klaster' => 'tambahan', 'tugas' => null], // BPPD
            ['id' => 38, 'klaster' => 'tambahan', 'tugas' => null], // BPBD
        ];

        foreach ($data as $row) {
            DB::table('perangkat_daerah')
                ->where('id', $row['id'])
                ->update([
                    'klaster' => $row['klaster'],
                    'tugas'   => $row['tugas'],
                ]);
        }
    }
};