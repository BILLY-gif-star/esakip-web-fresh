<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LkeAkipSeeder extends Seeder
{
    public function run()
    {
        // Hapus data lama
        DB::table('lke_kriteria')->delete();
        DB::table('lke_komponen')->delete();
        
        // ==================== KOMPONEN UTAMA ====================
        $komponenUtama = [
            ['kode' => '1', 'nama' => 'PERENCANAAN KINERJA', 'bobot' => 30, 'parent_id' => null, 'urutan' => 1],
            ['kode' => '2', 'nama' => 'PENGUKURAN KINERJA', 'bobot' => 30, 'parent_id' => null, 'urutan' => 2],
            ['kode' => '3', 'nama' => 'PELAPORAN KINERJA', 'bobot' => 15, 'parent_id' => null, 'urutan' => 3],
            ['kode' => '4', 'nama' => 'EVALUASI AKUNTABILITAS KINERJA INTERNAL', 'bobot' => 25, 'parent_id' => null, 'urutan' => 4],
        ];
        
        foreach ($komponenUtama as $komp) {
            DB::table('lke_komponen')->insert($komp);
        }
        
        // Ambil ID komponen utama
        $komp1 = DB::table('lke_komponen')->where('kode', '1')->first();
        $komp2 = DB::table('lke_komponen')->where('kode', '2')->first();
        $komp3 = DB::table('lke_komponen')->where('kode', '3')->first();
        $komp4 = DB::table('lke_komponen')->where('kode', '4')->first();
        
        // ==================== SUB KOMPONEN ====================
        $subKomponen = [
            // Komponen 1
            ['kode' => '1.a', 'nama' => 'Dokumen Perencanaan kinerja telah tersedia', 'bobot' => 6, 'parent_id' => $komp1->id, 'urutan' => 1],
            ['kode' => '1.b', 'nama' => 'Dokumen Perencanaan kinerja telah memenuhi standar yang baik', 'bobot' => 9, 'parent_id' => $komp1->id, 'urutan' => 2],
            ['kode' => '1.c', 'nama' => 'Perencanaan Kinerja telah dimanfaatkan untuk mewujudkan hasil yang berkesinambungan', 'bobot' => 15, 'parent_id' => $komp1->id, 'urutan' => 3],
            
            // Komponen 2
            ['kode' => '2.a', 'nama' => 'Pengukuran Kinerja telah dilakukan', 'bobot' => 6, 'parent_id' => $komp2->id, 'urutan' => 1],
            ['kode' => '2.b', 'nama' => 'Pengukuran Kinerja telah menjadi kebutuhan dalam mewujudkan Kinerja secara Efektif dan Efisien', 'bobot' => 9, 'parent_id' => $komp2->id, 'urutan' => 2],
            ['kode' => '2.c', 'nama' => 'Pengukuran Kinerja telah dijadikan dasar dalam pemberian Reward dan Punishment', 'bobot' => 15, 'parent_id' => $komp2->id, 'urutan' => 3],
            
            // Komponen 3
            ['kode' => '3.a', 'nama' => 'Terdapat Dokumen Laporan yang menggambarkan Kinerja', 'bobot' => 3, 'parent_id' => $komp3->id, 'urutan' => 1],
            ['kode' => '3.b', 'nama' => 'Dokumen Laporan Kinerja telah memenuhi Standar menggambarkan Kualitas atas Pencapaian Kinerja', 'bobot' => 4.5, 'parent_id' => $komp3->id, 'urutan' => 2],
            ['kode' => '3.c', 'nama' => 'Pelaporan Kinerja telah memberikan dampak yang besar dalam penyesuaian strategi/kebijakan', 'bobot' => 7.5, 'parent_id' => $komp3->id, 'urutan' => 3],
            
            // Komponen 4
            ['kode' => '4.a', 'nama' => 'Evaluasi Akuntabilitas Kinerja Internal telah dilaksanakan', 'bobot' => 5, 'parent_id' => $komp4->id, 'urutan' => 1],
            ['kode' => '4.b', 'nama' => 'Evaluasi Akuntabilitas Kinerja Internal telah dilaksanakan secara berkualitas dengan Sumber Daya yang memadai', 'bobot' => 7.5, 'parent_id' => $komp4->id, 'urutan' => 2],
            ['kode' => '4.c', 'nama' => 'Implementasi SAKIP telah meningkat karena evaluasi Akuntabilitas Kinerja Internal', 'bobot' => 12.5, 'parent_id' => $komp4->id, 'urutan' => 3],
        ];
        
        foreach ($subKomponen as $sub) {
            DB::table('lke_komponen')->insert($sub);
        }
        
        // ==================== KRITERIA ====================
        // Ambil ID sub komponen
        $sub1a = DB::table('lke_komponen')->where('kode', '1.a')->first();
        $sub1b = DB::table('lke_komponen')->where('kode', '1.b')->first();
        $sub1c = DB::table('lke_komponen')->where('kode', '1.c')->first();
        $sub2a = DB::table('lke_komponen')->where('kode', '2.a')->first();
        $sub2b = DB::table('lke_komponen')->where('kode', '2.b')->first();
        $sub2c = DB::table('lke_komponen')->where('kode', '2.c')->first();
        $sub3a = DB::table('lke_komponen')->where('kode', '3.a')->first();
        $sub3b = DB::table('lke_komponen')->where('kode', '3.b')->first();
        $sub3c = DB::table('lke_komponen')->where('kode', '3.c')->first();
        $sub4a = DB::table('lke_komponen')->where('kode', '4.a')->first();
        $sub4b = DB::table('lke_komponen')->where('kode', '4.b')->first();
        $sub4c = DB::table('lke_komponen')->where('kode', '4.c')->first();
        
        $kriteria = [
            // Kriteria 1.a (6 kriteria)
            ['komponen_id' => $sub1a->id, 'nomor' => 1, 'uraian' => 'Terdapat pedoman teknis perencanaan kinerja.'],
            ['komponen_id' => $sub1a->id, 'nomor' => 2, 'uraian' => 'Terdapat dokumen perencanaan kinerja jangka panjang.'],
            ['komponen_id' => $sub1a->id, 'nomor' => 3, 'uraian' => 'Terdapat dokumen perencanaan kinerja jangka menengah.'],
            ['komponen_id' => $sub1a->id, 'nomor' => 4, 'uraian' => 'Terdapat dokumen perencanaan kinerja jangka pendek.'],
            ['komponen_id' => $sub1a->id, 'nomor' => 5, 'uraian' => 'Terdapat dokumen perencanaan aktivitas yang mendukung kinerja.'],
            ['komponen_id' => $sub1a->id, 'nomor' => 6, 'uraian' => 'Terdapat dokumen perencanaan anggaran yang mendukung kinerja.'],
            
            // Kriteria 1.b (11 kriteria)
            ['komponen_id' => $sub1b->id, 'nomor' => 1, 'uraian' => 'Dokumen Perencanaan Kinerja telah diformalkan.'],
            ['komponen_id' => $sub1b->id, 'nomor' => 2, 'uraian' => 'Dokumen Perencanaan Kinerja telah dipublikasikan tepat waktu.'],
            ['komponen_id' => $sub1b->id, 'nomor' => 3, 'uraian' => 'Dokumen Perencanaan Kinerja telah menggambarkan Kebutuhan atas Kinerja sebenarnya yang perlu dicapai.'],
            ['komponen_id' => $sub1b->id, 'nomor' => 4, 'uraian' => 'Kualitas Rumusan Hasil (Tujuan/Sasaran) telah jelas menggambarkan kondisi kinerja yang akan dicapai.'],
            ['komponen_id' => $sub1b->id, 'nomor' => 5, 'uraian' => 'Indikator Kinerja Utama (IKU) telah menggambarkan kondisi Kinerja Utama yang harus dicapai, tertuang secara berkelanjutan.'],
            ['komponen_id' => $sub1b->id, 'nomor' => 6, 'uraian' => 'Ukuran Keberhasilan (Indikator Kinerja) telah memenuhi kriteria SMART.'],
            ['komponen_id' => $sub1b->id, 'nomor' => 7, 'uraian' => 'Target yang ditetapkan dalam Perencanaan Kinerja dapat dicapai (achievable), menantang, dan realistis.'],
            ['komponen_id' => $sub1b->id, 'nomor' => 8, 'uraian' => 'Setiap Dokumen Perencanaan Kinerja menggambarkan hubungan yang berkesinambungan, serta selaras antara Kondisi/Hasil yang akan dicapai di setiap level jabatan (Cascading).'],
            ['komponen_id' => $sub1b->id, 'nomor' => 9, 'uraian' => 'Perencanaan kinerja dapat memberikan informasi tentang hubungan kinerja, strategi, kebijakan, bahkan aktivitas antar bidang/dengan tugas dan fungsi lain yang berkaitan (Crosscutting).'],
            ['komponen_id' => $sub1b->id, 'nomor' => 10, 'uraian' => 'Setiap unit/satuan kerja merumuskan dan menetapkan Perencanaan Kinerja.'],
            ['komponen_id' => $sub1b->id, 'nomor' => 11, 'uraian' => 'Setiap pegawai merumuskan dan menetapkan Perencanaan Kinerja.'],
            
            // Kriteria 1.c (8 kriteria)
            ['komponen_id' => $sub1c->id, 'nomor' => 1, 'uraian' => 'Anggaran yang ditetapkan telah mengacu pada Kinerja yang ingin dicapai.'],
            ['komponen_id' => $sub1c->id, 'nomor' => 2, 'uraian' => 'Aktivitas yang dilaksanakan telah mendukung Kinerja yang ingin dicapai.'],
            ['komponen_id' => $sub1c->id, 'nomor' => 3, 'uraian' => 'Target yang ditetapkan dalam Perencanaan Kinerja telah dicapai dengan baik, atau setidaknya masih on the right track.'],
            ['komponen_id' => $sub1c->id, 'nomor' => 4, 'uraian' => 'Rencana aksi kinerja dapat berjalan dinamis karena capaian kinerja selalu dipantau secara berkala.'],
            ['komponen_id' => $sub1c->id, 'nomor' => 5, 'uraian' => 'Terdapat perbaikan/penyempurnaan Dokumen Perencanaan Kinerja yang ditetapkan dari hasil analisis perbaikan kinerja sebelumnya.'],
            ['komponen_id' => $sub1c->id, 'nomor' => 6, 'uraian' => 'Terdapat perbaikan/penyempurnaan Dokumen Perencanaan Kinerja dalam mewujudkan kondisi/hasil yang lebih baik.'],
            ['komponen_id' => $sub1c->id, 'nomor' => 7, 'uraian' => 'Setiap unit/satuan kerja memahami dan peduli, serta berkomitmen dalam mencapai kinerja yang telah direncanakan.'],
            ['komponen_id' => $sub1c->id, 'nomor' => 8, 'uraian' => 'Setiap Pegawai memahami dan peduli, serta berkomitmen dalam mencapai kinerja yang telah direncanakan.'],
            
            // Kriteria 2.a (3 kriteria)
            ['komponen_id' => $sub2a->id, 'nomor' => 1, 'uraian' => 'Terdapat pedoman teknis pengukuran kinerja dan pengumpulan data kinerja.'],
            ['komponen_id' => $sub2a->id, 'nomor' => 2, 'uraian' => 'Terdapat Definisi Operasional yang jelas atas kinerja dan cara mengukur indikator kinerja.'],
            ['komponen_id' => $sub2a->id, 'nomor' => 3, 'uraian' => 'Terdapat mekanisme yang jelas terhadap pengumpulan data kinerja yang dapat diandalkan.'],
            
            // Kriteria 2.b (7 kriteria)
            ['komponen_id' => $sub2b->id, 'nomor' => 1, 'uraian' => 'Pimpinan selalu terlibat sebagai pengambil keputusan (Decision Maker) dalam mengukur capaian kinerja.'],
            ['komponen_id' => $sub2b->id, 'nomor' => 2, 'uraian' => 'Data kinerja yang dikumpulkan telah relevan untuk mengukur capaian kinerja yang diharapkan.'],
            ['komponen_id' => $sub2b->id, 'nomor' => 3, 'uraian' => 'Data kinerja yang dikumpulkan telah mendukung capaian kinerja yang diharapkan.'],
            ['komponen_id' => $sub2b->id, 'nomor' => 4, 'uraian' => 'Pengukuran kinerja telah dilakukan secara berkala.'],
            ['komponen_id' => $sub2b->id, 'nomor' => 5, 'uraian' => 'Setiap level organisasi melakukan pemantauan atas pengukuran capaian kinerja unit dibawahnya secara berjenjang.'],
            ['komponen_id' => $sub2b->id, 'nomor' => 6, 'uraian' => 'Pengumpulan data kinerja telah memanfaatkan Teknologi Informasi (Aplikasi).'],
            ['komponen_id' => $sub2b->id, 'nomor' => 7, 'uraian' => 'Pengukuran capaian kinerja telah memanfaatkan Teknologi Informasi (Aplikasi).'],
            
            // Kriteria 2.c (10 kriteria)
            ['komponen_id' => $sub2c->id, 'nomor' => 1, 'uraian' => 'Pengukuran Kinerja telah menjadi dasar dalam penyesuaian (pemberian/pengurangan) tunjangan kinerja/penghasilan.'],
            ['komponen_id' => $sub2c->id, 'nomor' => 2, 'uraian' => 'Pengukuran Kinerja telah menjadi dasar dalam penempatan/penghapusan Jabatan baik struktural maupun fungsional.'],
            ['komponen_id' => $sub2c->id, 'nomor' => 3, 'uraian' => 'Pengukuran kinerja telah mempengaruhi penyesuaian (Refocusing) Organisasi.'],
            ['komponen_id' => $sub2c->id, 'nomor' => 4, 'uraian' => 'Pengukuran kinerja telah mempengaruhi penyesuaian Strategi dalam mencapai kinerja.'],
            ['komponen_id' => $sub2c->id, 'nomor' => 5, 'uraian' => 'Pengukuran kinerja telah mempengaruhi penyesuaian Kebijakan dalam mencapai kinerja.'],
            ['komponen_id' => $sub2c->id, 'nomor' => 6, 'uraian' => 'Pengukuran kinerja telah mempengaruhi penyesuaian Aktivitas dalam mencapai kinerja.'],
            ['komponen_id' => $sub2c->id, 'nomor' => 7, 'uraian' => 'Pengukuran kinerja telah mempengaruhi penyesuaian Anggaran dalam mencapai kinerja.'],
            ['komponen_id' => $sub2c->id, 'nomor' => 8, 'uraian' => 'Terdapat efisiensi atas penggunaan anggaran dalam mencapai kinerja.'],
            ['komponen_id' => $sub2c->id, 'nomor' => 9, 'uraian' => 'Setiap unit/satuan kerja memahami dan peduli atas hasil pengukuran kinerja.'],
            ['komponen_id' => $sub2c->id, 'nomor' => 10, 'uraian' => 'Setiap pegawai memahami dan peduli atas hasil pengukuran kinerja.'],
            
            // Kriteria 3.a (6 kriteria)
            ['komponen_id' => $sub3a->id, 'nomor' => 1, 'uraian' => 'Dokumen Laporan Kinerja telah disusun.'],
            ['komponen_id' => $sub3a->id, 'nomor' => 2, 'uraian' => 'Dokumen Laporan Kinerja telah disusun secara berkala.'],
            ['komponen_id' => $sub3a->id, 'nomor' => 3, 'uraian' => 'Dokumen Laporan Kinerja telah diformalkan.'],
            ['komponen_id' => $sub3a->id, 'nomor' => 4, 'uraian' => 'Dokumen Laporan Kinerja telah direviu.'],
            ['komponen_id' => $sub3a->id, 'nomor' => 5, 'uraian' => 'Dokumen Laporan Kinerja telah dipublikasikan.'],
            ['komponen_id' => $sub3a->id, 'nomor' => 6, 'uraian' => 'Dokumen Laporan Kinerja telah disampaikan tepat waktu.'],
            
            // Kriteria 3.b (9 kriteria)
            ['komponen_id' => $sub3b->id, 'nomor' => 1, 'uraian' => 'Dokumen Laporan Kinerja disusun secara berkualitas sesuai dengan standar.'],
            ['komponen_id' => $sub3b->id, 'nomor' => 2, 'uraian' => 'Dokumen Laporan Kinerja telah mengungkap seluruh informasi tentang pencapaian kinerja.'],
            ['komponen_id' => $sub3b->id, 'nomor' => 3, 'uraian' => 'Dokumen Laporan Kinerja telah menginfokan perbandingan realisasi kinerja dengan target tahunan.'],
            ['komponen_id' => $sub3b->id, 'nomor' => 4, 'uraian' => 'Dokumen Laporan Kinerja telah menginfokan perbandingan realisasi kinerja dengan target jangka menengah.'],
            ['komponen_id' => $sub3b->id, 'nomor' => 5, 'uraian' => 'Dokumen Laporan Kinerja telah menginfokan perbandingan realisasi kinerja dengan realisasi kinerja tahun-tahun sebelumnya.'],
            ['komponen_id' => $sub3b->id, 'nomor' => 6, 'uraian' => 'Dokumen Laporan Kinerja telah menginfokan perbandingan realisasi kinerja dengan realisasi kinerja di level nasional/internasional (Benchmark Kinerja).'],
            ['komponen_id' => $sub3b->id, 'nomor' => 7, 'uraian' => 'Dokumen Laporan Kinerja telah menginfokan kualitas atas capaian kinerja beserta upaya nyata dan/atau hambatannya.'],
            ['komponen_id' => $sub3b->id, 'nomor' => 8, 'uraian' => 'Dokumen Laporan Kinerja telah menginfokan efisiensi atas penggunaan sumber daya dalam mencapai kinerja.'],
            ['komponen_id' => $sub3b->id, 'nomor' => 9, 'uraian' => 'Dokumen Laporan Kinerja telah menginfokan upaya perbaikan dan penyempurnaan kinerja ke depan (Rekomendasi perbaikan kinerja).'],
            
            // Kriteria 3.c (7 kriteria)
            ['komponen_id' => $sub3c->id, 'nomor' => 1, 'uraian' => 'Informasi dalam laporan kinerja selalu menjadi perhatian utama pimpinan (Bertanggung Jawab).'],
            ['komponen_id' => $sub3c->id, 'nomor' => 2, 'uraian' => 'Penyajian informasi dalam laporan kinerja menjadi kepedulian seluruh pegawai.'],
            ['komponen_id' => $sub3c->id, 'nomor' => 3, 'uraian' => 'Informasi dalam laporan kinerja berkala telah digunakan dalam penyesuaian aktivitas untuk mencapai kinerja.'],
            ['komponen_id' => $sub3c->id, 'nomor' => 4, 'uraian' => 'Informasi dalam laporan kinerja berkala telah digunakan dalam penyesuaian penggunaan anggaran untuk mencapai kinerja.'],
            ['komponen_id' => $sub3c->id, 'nomor' => 5, 'uraian' => 'Informasi dalam laporan kinerja telah digunakan dalam evaluasi pencapaian keberhasilan kinerja.'],
            ['komponen_id' => $sub3c->id, 'nomor' => 6, 'uraian' => 'Informasi dalam laporan kinerja telah digunakan dalam penyesuaian perencanaan kinerja yang akan dihadapi berikutnya.'],
            ['komponen_id' => $sub3c->id, 'nomor' => 7, 'uraian' => 'Informasi dalam laporan kinerja selalu mempengaruhi perubahan budaya kinerja organisasi.'],
            
            // Kriteria 4.a (3 kriteria)
            ['komponen_id' => $sub4a->id, 'nomor' => 1, 'uraian' => 'Terdapat pedoman teknis Evaluasi Akuntabilitas Kinerja Internal.'],
            ['komponen_id' => $sub4a->id, 'nomor' => 2, 'uraian' => 'Evaluasi Akuntabilitas Kinerja Internal telah dilaksanakan pada seluruh unit kerja/perangkat daerah.'],
            ['komponen_id' => $sub4a->id, 'nomor' => 3, 'uraian' => 'Evaluasi Akuntabilitas Kinerja Internal telah dilaksanakan secara berjenjang.'],
            
            // Kriteria 4.b (5 kriteria)
            ['komponen_id' => $sub4b->id, 'nomor' => 1, 'uraian' => 'Evaluasi Akuntabilitas Kinerja Internal telah dilaksanakan sesuai standar.'],
            ['komponen_id' => $sub4b->id, 'nomor' => 2, 'uraian' => 'Evaluasi Akuntabilitas Kinerja Internal telah dilaksanakan oleh SDM yang memadai.'],
            ['komponen_id' => $sub4b->id, 'nomor' => 3, 'uraian' => 'Evaluasi Akuntabilitas Kinerja Internal telah dilaksanakan dengan pendalaman yang memadai.'],
            ['komponen_id' => $sub4b->id, 'nomor' => 4, 'uraian' => 'Evaluasi Akuntabilitas Kinerja Internal telah dilaksanakan pada seluruh unit kerja/perangkat daerah.'],
            ['komponen_id' => $sub4b->id, 'nomor' => 5, 'uraian' => 'Evaluasi Akuntabilitas Kinerja Internal telah dilaksanakan menggunakan Teknologi Informasi (Aplikasi).'],
            
            // Kriteria 4.c (5 kriteria)
            ['komponen_id' => $sub4c->id, 'nomor' => 1, 'uraian' => 'Seluruh rekomendasi atas hasil evaluasi akuntabilitas kinerja internal telah ditindaklanjuti.'],
            ['komponen_id' => $sub4c->id, 'nomor' => 2, 'uraian' => 'Telah terjadi peningkatan implementasi SAKIP dengan melaksanakan tindak lanjut atas rekomendasi hasil evaluasi akuntabilitas Kinerja internal.'],
            ['komponen_id' => $sub4c->id, 'nomor' => 3, 'uraian' => 'Hasil Evaluasi Akuntabilitas Kinerja Internal telah dimanfaatkan untuk perbaikan dan peningkatan akuntabilitas kinerja.'],
            ['komponen_id' => $sub4c->id, 'nomor' => 4, 'uraian' => 'Hasil dari Evaluasi Akuntabilitas Kinerja Internal telah dimanfaatkan dalam mendukung efektifitas dan efisiensi kinerja.'],
            ['komponen_id' => $sub4c->id, 'nomor' => 5, 'uraian' => 'Telah terjadi perbaikan dan peningkatan kinerja dengan memanfaatkan hasil evaluasi akuntabilitas kinerja internal.'],
        ];
        
        foreach ($kriteria as $krit) {
            DB::table('lke_kriteria')->insert($krit);
        }
        
        echo "Seeder LKE AKIP selesai!\n";
        echo "Komponen Utama: 4\n";
        echo "Sub Komponen: 12\n";
        echo "Kriteria: " . count($kriteria) . "\n";
    }
}