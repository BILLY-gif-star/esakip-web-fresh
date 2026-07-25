<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LkeKomponenSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('lke_komponen')->insert([
            ['id' => 1,  'kode' => '1',   'nama' => 'PERENCANAAN KINERJA',                          'bobot' => 30.00, 'parent_id' => null, 'urutan' => 1, 'created_at' => '2026-03-25 02:30:38'],
            ['id' => 2,  'kode' => '2',   'nama' => 'PENGUKURAN KINERJA',                           'bobot' => 30.00, 'parent_id' => null, 'urutan' => 2, 'created_at' => '2026-03-25 02:30:38'],
            ['id' => 3,  'kode' => '3',   'nama' => 'PELAPORAN KINERJA',                            'bobot' => 15.00, 'parent_id' => null, 'urutan' => 3, 'created_at' => '2026-03-25 02:30:38'],
            ['id' => 4,  'kode' => '4',   'nama' => 'EVALUASI AKUNTABILITAS KINERJA INTERNAL',      'bobot' => 25.00, 'parent_id' => null, 'urutan' => 4, 'created_at' => '2026-03-25 02:30:38'],
            ['id' => 11, 'kode' => '1.a', 'nama' => 'Dokumen Perencanaan Kinerja telah tersedia',   'bobot' => 6.00,  'parent_id' => 1,    'urutan' => 1, 'created_at' => '2026-03-25 02:30:38'],
            ['id' => 12, 'kode' => '1.b', 'nama' => 'Dokumen Perencanaan Kinerja telah memenuhi standar yang baik (SMART, cascading, crosscutting)', 'bobot' => 9.00, 'parent_id' => 1, 'urutan' => 2, 'created_at' => '2026-03-25 02:30:38'],
            ['id' => 13, 'kode' => '1.c', 'nama' => 'Perencanaan Kinerja telah dimanfaatkan untuk mewujudkan hasil yang berkesinambungan', 'bobot' => 15.00, 'parent_id' => 1, 'urutan' => 3, 'created_at' => '2026-03-25 02:30:38'],
            ['id' => 21, 'kode' => '2.a', 'nama' => 'Pengukuran Kinerja telah dilakukan',          'bobot' => 6.00,  'parent_id' => 2,    'urutan' => 1, 'created_at' => '2026-03-25 02:30:38'],
            ['id' => 22, 'kode' => '2.b', 'nama' => 'Pengukuran Kinerja telah menjadi kebutuhan dalam mewujudkan Kinerja secara Efektif dan Efisien (berjenjang)', 'bobot' => 9.00, 'parent_id' => 2, 'urutan' => 2, 'created_at' => '2026-03-25 02:30:38'],
            ['id' => 23, 'kode' => '2.c', 'nama' => 'Pengukuran Kinerja telah dijadikan dasar pemberian Reward dan Punishment serta penyesuaian strategi', 'bobot' => 15.00, 'parent_id' => 2, 'urutan' => 3, 'created_at' => '2026-03-25 02:30:38'],
            ['id' => 31, 'kode' => '3.a', 'nama' => 'Terdapat Dokumen Laporan yang menggambarkan Kinerja', 'bobot' => 3.00, 'parent_id' => 3, 'urutan' => 1, 'created_at' => '2026-03-25 02:30:38'],
            ['id' => 32, 'kode' => '3.b', 'nama' => 'Dokumen Laporan Kinerja telah memenuhi Standar (kualitas, keberhasilan/kegagalan, upaya perbaikan)', 'bobot' => 4.50, 'parent_id' => 3, 'urutan' => 2, 'created_at' => '2026-03-25 02:30:38'],
            ['id' => 33, 'kode' => '3.c', 'nama' => 'Pelaporan Kinerja telah memberikan dampak besar dalam penyesuaian strategi/kebijakan', 'bobot' => 7.50, 'parent_id' => 3, 'urutan' => 3, 'created_at' => '2026-03-25 02:30:38'],
            ['id' => 41, 'kode' => '4.a', 'nama' => 'Evaluasi Akuntabilitas Kinerja Internal telah dilaksanakan', 'bobot' => 5.00, 'parent_id' => 4, 'urutan' => 1, 'created_at' => '2026-03-25 02:30:38'],
            ['id' => 42, 'kode' => '4.b', 'nama' => 'Evaluasi Akuntabilitas Kinerja Internal telah dilaksanakan secara berkualitas dengan Sumber Daya yang memadai', 'bobot' => 7.50, 'parent_id' => 4, 'urutan' => 2, 'created_at' => '2026-03-25 02:30:38'],
            ['id' => 43, 'kode' => '4.c', 'nama' => 'Implementasi SAKIP telah meningkat karena evaluasi sehingga memberikan kesan nyata (dampak) efektifitas kinerja', 'bobot' => 12.50, 'parent_id' => 4, 'urutan' => 3, 'created_at' => '2026-03-25 02:30:38'],
        ]);
    }
}