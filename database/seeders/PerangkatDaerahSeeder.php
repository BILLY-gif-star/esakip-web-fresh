<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerangkatDaerahSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama agar tidak terjadi duplicate entry
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('perangkat_daerah')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('perangkat_daerah')->insert([
            ['id' =>  1, 'nama' => 'BIRO PEMERINTAHAN',                                                                                  'singkatan' => 'BIRO PEM',       'created_at' => '2026-03-21 15:18:21'],
            ['id' =>  2, 'nama' => 'BIRO HUKUM',                                                                                         'singkatan' => 'BIRO HKM',       'created_at' => '2026-03-21 15:18:21'],
            ['id' =>  3, 'nama' => 'BIRO PEREKONOMIAN DAN ADMINISTRASI PEMBANGUNAN',                                                      'singkatan' => 'BIRO EK',        'created_at' => '2026-03-21 15:18:21'],
            ['id' =>  4, 'nama' => 'BIRO PENGADAAN BARANG DAN JASA',                                                                      'singkatan' => 'BIRO PBJ',       'created_at' => '2026-03-21 15:18:21'],
            ['id' =>  5, 'nama' => 'BIRO ORGANISASI',                                                                                     'singkatan' => 'BIRO ORG',       'created_at' => '2026-03-21 15:18:21'],
            ['id' =>  6, 'nama' => 'BIRO UMUM',                                                                                           'singkatan' => 'BIRO UM',        'created_at' => '2026-03-21 15:18:21'],
            ['id' =>  7, 'nama' => 'BIRO ADMINISTRASI PIMPINAN',                                                                          'singkatan' => 'BIRO ADPIM',     'created_at' => '2026-03-21 15:18:21'],
            ['id' =>  8, 'nama' => 'SEKRETARIAT DPRD',                                                                                    'singkatan' => 'SET DPRD',       'created_at' => '2026-03-21 15:18:21'],
            ['id' =>  9, 'nama' => 'INSPEKTORAT DAERAH',                                                                                  'singkatan' => 'INSPEKT',        'created_at' => '2026-03-21 15:18:21'],
            ['id' => 10, 'nama' => 'DINAS PENDIDIKAN DAN KEBUDAYAAN',                                                                     'singkatan' => 'DIKBUD',         'created_at' => '2026-03-21 15:18:21'],
            ['id' => 11, 'nama' => 'DINAS KESEHATAN',                                                                                     'singkatan' => 'DINKES',         'created_at' => '2026-03-21 15:18:21'],
            ['id' => 12, 'nama' => 'DINAS PEKERJAAN UMUM DAN PERUMAHAN RAKYAT',                                                           'singkatan' => 'PUPR',           'created_at' => '2026-03-21 15:18:21'],
            ['id' => 13, 'nama' => 'SATUAN POLISI PAMONG PRAJA',                                                                          'singkatan' => 'SATPOL PP',      'created_at' => '2026-03-21 15:18:21'],
            ['id' => 14, 'nama' => 'DINAS SOSIAL',                                                                                        'singkatan' => 'DINSOS',         'created_at' => '2026-03-21 15:18:21'],
            ['id' => 15, 'nama' => 'DINAS KOPERASI, USAHA KECIL DAN MENENGAH',                                                            'singkatan' => 'DISKOP UKM',     'created_at' => '2026-03-21 15:18:21'],
            ['id' => 16, 'nama' => 'DINAS PEMBERDAYAAN PEREMPUAN, PERLINDUNGAN ANAK, PENGENDALIAN PENDUDUK DAN KELUARGA BERENCANA',        'singkatan' => 'DP3AKB',         'created_at' => '2026-03-21 15:18:21'],
            ['id' => 17, 'nama' => 'DINAS PERTANIAN DAN KETAHANAN PANGAN',                                                                'singkatan' => 'DISTANAK',       'created_at' => '2026-03-21 15:18:21'],
            ['id' => 18, 'nama' => 'DINAS LINGKUNGAN HIDUP DAN KEHUTANAN',                                                                'singkatan' => 'DLHK',           'created_at' => '2026-03-21 15:18:21'],
            ['id' => 19, 'nama' => 'DINAS PEMBERDAYAAN MASYARAKAT DAN DESA',                                                              'singkatan' => 'DPMD',           'created_at' => '2026-03-21 15:18:21'],
            ['id' => 20, 'nama' => 'DINAS PERHUBUNGAN',                                                                                   'singkatan' => 'DISHUB',         'created_at' => '2026-03-21 15:18:21'],
            ['id' => 21, 'nama' => 'DINAS KOMUNIKASI DAN INFORMATIKA',                                                                    'singkatan' => 'DISKOMINFO',     'created_at' => '2026-03-21 15:18:21'],
            ['id' => 22, 'nama' => 'DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU',                                              'singkatan' => 'DPMPTSP',        'created_at' => '2026-03-21 15:18:21'],
            ['id' => 23, 'nama' => 'DINAS KEPEMUDAAN DAN OLAHRAGA',                                                                       'singkatan' => 'DISPORA',        'created_at' => '2026-03-21 15:18:21'],
            ['id' => 24, 'nama' => 'DINAS KEARSIPAN DAN PERPUSTAKAAN',                                                                    'singkatan' => 'DIARPUS',        'created_at' => '2026-03-21 15:18:21'],
            ['id' => 25, 'nama' => 'DINAS KELAUTAN DAN PERIKANAN',                                                                        'singkatan' => 'DKP',            'created_at' => '2026-03-21 15:18:21'],
            ['id' => 26, 'nama' => 'DINAS PARIWISATA DAN EKONOMI KREATIF',                                                                'singkatan' => 'DISPAREKRAF',    'created_at' => '2026-03-21 15:18:21'],
            ['id' => 27, 'nama' => 'DINAS PETERNAKAN',                                                                                    'singkatan' => 'DISNAK',         'created_at' => '2026-03-21 15:18:21'],
            ['id' => 28, 'nama' => 'DINAS PERINDUSTRIAN DAN PERDAGANGAN',                                                                 'singkatan' => 'DISPERINDAG',    'created_at' => '2026-03-21 15:18:21'],
            ['id' => 29, 'nama' => 'DINAS ENERGI DAN SUMBER DAYA MINERAL',                                                                'singkatan' => 'DESDM',          'created_at' => '2026-03-21 15:18:21'],
            ['id' => 30, 'nama' => 'DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL',                                                             'singkatan' => 'DISDUKCAPIL',    'created_at' => '2026-03-21 15:18:21'],
            ['id' => 31, 'nama' => 'DINAS KETENAGAKERJAAN DAN TRANSMIGRASI',                                                              'singkatan' => 'DISNAKERTRANS', 'created_at' => '2026-03-21 15:18:21'],
            ['id' => 32, 'nama' => 'BADAN PERENCANAAN PEMBANGUNAN, RISET DAN INOVASI DAERAH',                                             'singkatan' => 'BAPPERIDA',      'created_at' => '2026-03-21 15:18:21'],
            ['id' => 33, 'nama' => 'BADAN KEUANGAN DAERAH',                                                                               'singkatan' => 'BKD',            'created_at' => '2026-03-21 15:18:21'],
            ['id' => 34, 'nama' => 'BADAN PENDAPATAN DAN ASET DAERAH',                                                                    'singkatan' => 'BPAD',           'created_at' => '2026-03-21 15:18:21'],
            ['id' => 35, 'nama' => 'BADAN KEPEGAWAIAN DAERAH',                                                                            'singkatan' => 'BKD KEP',        'created_at' => '2026-03-21 15:18:21'],
            ['id' => 36, 'nama' => 'BADAN PENGEMBANGAN SUMBER DAYA MANUSIA DAERAH',                                                       'singkatan' => 'BPSDMD',         'created_at' => '2026-03-21 15:18:21'],
            ['id' => 37, 'nama' => 'BADAN KESATUAN BANGSA DAN POLITIK',                                                                   'singkatan' => 'BAKESBANGPOL',   'created_at' => '2026-03-21 15:18:21'],
            ['id' => 38, 'nama' => 'BADAN PENANGGULANGAN BENCANA DAERAH',                                                                 'singkatan' => 'BPBD',           'created_at' => '2026-03-21 15:18:21'],
            ['id' => 39, 'nama' => 'BADAN PENGELOLA PERBATASAN DAERAH',                                                                   'singkatan' => 'BPPD',           'created_at' => '2026-03-21 15:18:21'],
            ['id' => 40, 'nama' => 'BADAN PENGHUBUNG PROVINSI NTT DI JAKARTA',                                                            'singkatan' => 'BPNTT JKT',      'created_at' => '2026-03-21 15:18:21'],
            ['id' => 41, 'nama' => 'RSUD PROF DR. W. Z. JOHANNES KUPANG',                                                                 'singkatan' => 'RSUD WZ',        'created_at' => '2026-03-21 15:18:21'],
            ['id' => 42, 'nama' => 'RS KHUSUS JIWA NAIMATA KUPANG',                                                                       'singkatan' => 'RSKJ NAIMATA',   'created_at' => '2026-03-21 15:18:21'],
        ]);
    }
}