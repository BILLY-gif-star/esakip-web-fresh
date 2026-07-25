<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Services\NotifikasiService;

class KlasterEvaluasiController extends Controller
{
    // ════════════════════════════════════════════════════
    //  HELPERS PRIVATE
    // ════════════════════════════════════════════════════
    private function tahunEvaluasi(): int
    {
        return (int) date('Y') - 1;
    }

    private function listTahun(): array
    {
        $base = $this->tahunEvaluasi();
        return range($base, $base - 5);
    }

    // ════════════════════════════════════════════════════
    //  9 ENTRY POINTS
    // ════════════════════════════════════════════════════
    public function utama1(Request $request)     { return $this->showIndex($request, 'utama',     1); }
    public function utama2(Request $request)     { return $this->showIndex($request, 'utama',     2); }
    public function utama3(Request $request)     { return $this->showIndex($request, 'utama',     3); }
    public function pendukung1(Request $request) { return $this->showIndex($request, 'pendukung', 1); }
    public function pendukung2(Request $request) { return $this->showIndex($request, 'pendukung', 2); }
    public function pendukung3(Request $request) { return $this->showIndex($request, 'pendukung', 3); }
    public function tambahan1(Request $request)  { return $this->showIndex($request, 'tambahan',  1); }
    public function tambahan2(Request $request)  { return $this->showIndex($request, 'tambahan',  2); }
    public function tambahan3(Request $request)  { return $this->showIndex($request, 'tambahan',  3); }

    // ════════════════════════════════════════════════════
    //  INDEX — logic utama
    // ════════════════════════════════════════════════════
    private function showIndex(Request $request, string $type, int $level)
    {
        $user  = Session::get('user');
        $tahun = (int) $request->input('tahun', $this->tahunEvaluasi());
        $opdId = $user['role'] === 'operator'
               ? $user['daerah_id']
               : $request->input('opd_id');

        $listOpd   = DB::table('perangkat_daerah')->orderBy('nama')->get();
        $listTahun = $this->listTahun();

        $typeLabel = ['utama' => 'Utama', 'pendukung' => 'Pendukung', 'tambahan' => 'Tambahan'];
        $title     = 'Klaster ' . ($typeLabel[$type] ?? ucfirst($type)) . ' Level ' . $level;
        $subtitle  = 'Evaluasi Akuntabilitas Kinerja';

        $komponenUtama = DB::table('klaster_komponen')
            ->whereNull('parent_id')
            ->where('klaster_type', $type)
            ->where('klaster_level', $level)
            ->orderBy('urutan')
            ->get();

        $subKomponen = DB::table('klaster_komponen')
            ->whereNotNull('parent_id')
            ->where('klaster_type', $type)
            ->where('klaster_level', $level)
            ->orderBy('parent_id')
            ->orderBy('urutan')
            ->get();

        $subIds = $subKomponen->pluck('id');

        $kriteriaPerKomponen = $subIds->isNotEmpty()
            ? DB::table('klaster_kriteria')
                ->whereIn('komponen_id', $subIds)
                ->orderBy('komponen_id')
                ->orderBy('urutan')
                ->get()
                ->groupBy('komponen_id')
            : collect();

        $nilaiPerSubKomponen = [];
        $nilaiKomponenUtama  = [];
        $nilaiSubKomponen    = collect();
        $catatanPerKriteria  = collect();
        $dokumenPerKriteria  = [];
        $nilaiAkhir          = 0;
        $predikat            = $this->getPredikat(0);

        if ($opdId && $subIds->isNotEmpty()) {
            $rows = DB::table('klaster_penilaian')
                ->where('perangkat_daerah_id', $opdId)
                ->where('tahun', $tahun)
                ->whereIn('komponen_id', $subIds)
                ->get();

            foreach ($rows as $r) {
                $nilaiPerSubKomponen[$r->komponen_id] = floatval($r->nilai ?? 0);
            }

            $nilaiSubKomponen = $rows->keyBy('komponen_id');

            foreach ($komponenUtama as $k) {
                $total = 0;
                foreach ($subKomponen->where('parent_id', $k->id) as $s) {
                    $total += $nilaiPerSubKomponen[$s->id] ?? 0;
                }
                $nilaiKomponenUtama[$k->id] = $total;
                $nilaiAkhir += $total;
            }

            $kriteriaIds = $kriteriaPerKomponen->flatten()->pluck('id');

            if ($kriteriaIds->isNotEmpty()) {
                $catatanPerKriteria = DB::table('klaster_penilaian_kriteria')
                    ->where('perangkat_daerah_id', $opdId)
                    ->where('tahun', $tahun)
                    ->whereIn('kriteria_id', $kriteriaIds)
                    ->get()
                    ->keyBy('kriteria_id');

                $dokumenRows = DB::table('klaster_dokumen_kriteria')
                    ->where('perangkat_daerah_id', $opdId)
                    ->where('tahun', $tahun)
                    ->whereIn('kriteria_id', $kriteriaIds)
                    ->get();

                foreach ($dokumenRows as $d) {
                    $dokumenPerKriteria[$d->kriteria_id][] = $d;
                }
            }

            $predikat = $this->getPredikat($nilaiAkhir);
        }

        return view('klaster.index', compact(
            'user', 'tahun', 'type', 'level', 'opdId',
            'title', 'subtitle',
            'listOpd', 'listTahun',
            'komponenUtama', 'subKomponen', 'kriteriaPerKomponen',
            'nilaiPerSubKomponen', 'nilaiKomponenUtama',
            'nilaiSubKomponen', 'catatanPerKriteria', 'dokumenPerKriteria',
            'nilaiAkhir', 'predikat'
        ));
    }

    // ════════════════════════════════════════════════════
    //  SIMPAN
    // ════════════════════════════════════════════════════
    public function simpan(Request $request)
    {
        $user  = Session::get('user');
        $tahun = (int) $request->input('tahun', $this->tahunEvaluasi());
        $type  = $request->input('type', 'utama');
        $level = (int) $request->input('level', 1);
        $opdId = $user['role'] === 'operator'
               ? $user['daerah_id']
               : $request->input('opd_id');

        if (!$opdId) return back()->with('error', 'Pilih OPD terlebih dahulu.');

        $nilaiSebelumnya = null;
        
        // Ambil nilai sebelumnya untuk perbandingan (jika admin yang menilai)
        if ($user['role'] === 'admin') {
            $nilaiInput = $request->input('nilai', []);
            $firstKomponenId = array_key_first($nilaiInput);
            if ($firstKomponenId) {
                $existing = DB::table('klaster_penilaian')
                    ->where('perangkat_daerah_id', $opdId)
                    ->where('tahun', $tahun)
                    ->where('komponen_id', $firstKomponenId)
                    ->first();
                if ($existing) {
                    $nilaiSebelumnya = $existing->nilai;
                }
            }
        }

        if ($user['role'] === 'admin') {
            $nilaiInput   = $request->input('nilai', []);
            $catatanInput = $request->input('catatan_sub', []);

            foreach ($nilaiInput as $komponenId => $nilaiRaw) {
                if ($nilaiRaw === null || $nilaiRaw === '') continue;

                $nilai    = min(100, max(0, floatval(str_replace(',', '.', $nilaiRaw))));
                $subKomp  = DB::table('klaster_komponen')->where('id', $komponenId)->first();
                $bobotSub = floatval($subKomp->bobot ?? 0);

                $persentase = $bobotSub > 0 ? ($nilai / $bobotSub) * 100 : 0;
                $jawaban    = $this->getJawabanDariNilai($persentase);
                $catatan    = $catatanInput[$komponenId] ?? '';

                DB::table('klaster_penilaian')->updateOrInsert(
                    [
                        'perangkat_daerah_id' => $opdId,
                        'tahun'               => $tahun,
                        'komponen_id'         => $komponenId,
                    ],
                    [
                        'nilai'        => $nilai,
                        'persentase'   => round($persentase, 2),
                        'jawaban'      => $jawaban,
                        'catatan'      => $catatan,
                        'dinilai_oleh' => $user['id'],
                        'updated_at'   => now(),
                        'created_at'   => now(),
                    ]
                );
            }
        }

        $komAdmin = $request->input('komentar_admin', []);
        $catOp    = $request->input('catatan_operator', []);
        $daftarEv = $request->input('daftar_evidence', []);
        $ids      = array_unique(array_merge(
            array_keys($komAdmin), array_keys($catOp), array_keys($daftarEv)
        ));

        foreach ($ids as $krId) {
            try {
                $upd = ['updated_at' => now(), 'created_at' => now()];
                if ($user['role'] === 'admin') {
                    $upd['komentar_admin'] = $komAdmin[$krId] ?? '';
                }
                if ($user['role'] === 'operator') {
                    $upd['catatan_operator'] = $catOp[$krId] ?? '';
                    $upd['daftar_evidence']  = $daftarEv[$krId] ?? '';
                }
                DB::table('klaster_penilaian_kriteria')->updateOrInsert(
                    ['kriteria_id' => $krId, 'perangkat_daerah_id' => $opdId, 'tahun' => $tahun],
                    $upd
                );
            } catch (\Exception $e) {}
        }

        // ⭐ NOTIFIKASI: Kirim notifikasi ke operator jika admin menilai
        if ($user['role'] === 'admin') {
            $opd = DB::table('perangkat_daerah')->where('id', $opdId)->first();
            $namaOpd = $opd->nama ?? 'OPD';
            
            // Ambil nilai akhir setelah simpan
            $totalNilai = DB::table('klaster_penilaian')
                ->where('perangkat_daerah_id', $opdId)
                ->where('tahun', $tahun)
                ->sum('nilai');
            
            $predikat = $this->getPredikat($totalNilai);
            
            // Kirim notifikasi ke operator OPD tersebut
            $operator = DB::table('pengguna')
                ->where('perangkat_daerah_id', $opdId)
                ->where('role', 'operator')
                ->first();
            
            if ($operator) {
                $perubahan = '';
                if ($nilaiSebelumnya !== null) {
                    $perubahan = " (sebelumnya: {$nilaiSebelumnya})";
                }
                
                NotifikasiService::create(
                    $operator->id,
                    'Penilaian Klaster Evaluasi',
                    "Penilaian Klaster {$type} Level {$level} untuk {$namaOpd} telah selesai. Nilai akhir: {$totalNilai}{$perubahan} - Predikat: {$predikat['kode']}",
                    'klaster_penilaian',
                    '🏆',
                    'indigo',
                    route('klaster.' . $type . '.' . $level, ['tahun' => $tahun, 'opd_id' => $opdId]),
                    null,
                    $namaOpd
                );
            }
        }

        return redirect()->route('klaster.' . $type . '.' . $level, ['tahun' => $tahun, 'opd_id' => $opdId])
            ->with('success', $user['role'] === 'admin'
                ? 'Penilaian Klaster ' . ucfirst($type) . ' Level ' . $level . ' tahun ' . $tahun . ' berhasil disimpan!'
                : 'Catatan berhasil disimpan!');
    }

    // ════════════════════════════════════════════════════
    //  REKAP
    // ════════════════════════════════════════════════════
    public function rekap(Request $request)
    {
        $user   = Session::get('user');
        $base   = $this->tahunEvaluasi();
        $tahun1 = (int) $request->input('tahun1', $base - 1);
        $tahun2 = (int) $request->input('tahun2', $base);
        $tahun  = $tahun2;
        $type   = $request->input('type', 'utama');
        $level  = (int) $request->input('level', 1);
        $opdId  = $request->input('opd_id');

        $listOpd   = DB::table('perangkat_daerah')->orderBy('nama')->get();
        $listTahun = $this->listTahun();

        $typeLabel = ['utama' => 'Utama', 'pendukung' => 'Pendukung', 'tambahan' => 'Tambahan'];
        $title     = 'Klaster ' . ($typeLabel[$type] ?? ucfirst($type)) . ' Level ' . $level;

        $komponenUtama = DB::table('klaster_komponen')
            ->whereNull('parent_id')
            ->where('klaster_type', $type)
            ->where('klaster_level', $level)
            ->orderBy('urutan')
            ->get();

        $subKomponen = DB::table('klaster_komponen')
            ->whereNotNull('parent_id')
            ->where('klaster_type', $type)
            ->where('klaster_level', $level)
            ->get()->groupBy('parent_id');

        $subIds = DB::table('klaster_komponen')
            ->whereNotNull('parent_id')
            ->where('klaster_type', $type)
            ->where('klaster_level', $level)
            ->pluck('id');

        $daftarOpd = $opdId ? $listOpd->where('id', $opdId) : $listOpd;
        $rekapData = [];

        foreach ($daftarOpd as $opd) {
            $pen1 = DB::table('klaster_penilaian')
                ->where('perangkat_daerah_id', $opd->id)
                ->where('tahun', $tahun1)
                ->whereIn('komponen_id', $subIds)
                ->get()->keyBy('komponen_id');

            $pen2 = DB::table('klaster_penilaian')
                ->where('perangkat_daerah_id', $opd->id)
                ->where('tahun', $tahun2)
                ->whereIn('komponen_id', $subIds)
                ->get()->keyBy('komponen_id');

            if ($pen1->isEmpty() && $pen2->isEmpty()) continue;

            $totalT1 = $totalT2 = $totalBobotMax = 0;
            $komData = [];

            foreach ($komponenUtama as $k) {
                $subs = $subKomponen[$k->id] ?? collect();
                $t1 = $t2 = 0;
                foreach ($subs as $s) {
                    $t1 += floatval($pen1[$s->id]->nilai ?? 0);
                    $t2 += floatval($pen2[$s->id]->nilai ?? 0);
                }
                $totalT1        += $t1;
                $totalT2        += $t2;
                $totalBobotMax  += floatval($k->bobot ?? 0);
                $komData[]       = [
                    'nama'     => $k->nama,
                    'bobot'    => floatval($k->bobot ?? 0),
                    'nilai_t1' => $t1,
                    'nilai_t2' => $t2,
                ];
            }

            $nilaiAkhirT1 = $totalBobotMax > 0 ? round(($totalT1 / $totalBobotMax) * 100, 2) : 0;
            $nilaiAkhirT2 = $totalBobotMax > 0 ? round(($totalT2 / $totalBobotMax) * 100, 2) : 0;

            $rekapData[] = [
                'opd_id'       => $opd->id,
                'opd'          => $opd->nama,
                'nilai'        => $nilaiAkhirT2,
                'nilai_tahun1' => $nilaiAkhirT1,
                'nilai_tahun2' => $nilaiAkhirT2,
                'predikat'     => $this->getPredikat($nilaiAkhirT2),
                'komponen'     => $komData,
            ];
        }

        usort($rekapData, fn($a, $b) => $b['nilai_tahun2'] <=> $a['nilai_tahun2']);

        return view('klaster.rekap', compact(
            'user', 'tahun', 'tahun1', 'tahun2', 'type', 'level', 'opdId',
            'title', 'rekapData', 'listOpd', 'listTahun', 'komponenUtama'
        ));
    }

    // ════════════════════════════════════════════════════
    //  UPLOAD / LIHAT / HAPUS DOKUMEN
    // ════════════════════════════════════════════════════
    public function uploadDokumen(Request $request)
    {
        $user = Session::get('user');
        $request->validate([
            'kriteria_id' => 'required|integer',
            'tahun'       => 'required|integer',
            'file'        => 'required|file|mimes:pdf,docx,xlsx,doc,xls,jpg,jpeg,png|max:20480',
        ]);

        $opdId = $user['role'] === 'operator' ? $user['daerah_id'] : $request->input('opd_id');
        if (!$opdId) return back()->with('error', 'OPD tidak ditemukan.');

        $ext   = $request->file('file')->getClientOriginalExtension();
        $fname = 'klaster_' . $opdId . '_kr' . $request->kriteria_id . '_' . $request->tahun . '_' . time() . '.' . $ext;
        $request->file('file')->storeAs('klaster_dokumen', $fname);

        try {
            DB::table('klaster_dokumen_kriteria')->insert([
                'kriteria_id'         => $request->kriteria_id,
                'perangkat_daerah_id' => $opdId,
                'tahun'               => $request->tahun,
                'nama_file'           => $fname,
                'path_file'           => $fname,
                'uploaded_by'         => $user['id'],
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
            
            // ⭐ NOTIFIKASI: Kirim notifikasi ke admin saat operator upload dokumen
            if ($user['role'] === 'operator') {
                $opd = DB::table('perangkat_daerah')->where('id', $opdId)->first();
                $namaOpd = $opd->nama ?? 'OPD';
                
                $adminUsers = DB::table('pengguna')->where('role', 'admin')->get();
                
                foreach ($adminUsers as $admin) {
                    NotifikasiService::create(
                        $admin->id,
                        'Upload Dokumen Klaster',
                        "Operator {$namaOpd} mengupload dokumen pendukung klaster evaluasi",
                        'dokumen_upload',
                        '📎',
                        'amber',
                        route('klaster.hasil.lke.gabungan'),
                        $request->kriteria_id,
                        $namaOpd
                    );
                }
            }
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal upload dokumen.');
        }

        return back()->with('success', 'Dokumen pendukung berhasil diupload.');
    }

    public function lihatDokumen($id)
    {
        $user = Session::get('user');
        $doc  = DB::table('klaster_dokumen_kriteria')->find($id);
        if (!$doc) abort(404);
        if ($user['role'] === 'operator' && $doc->perangkat_daerah_id != $user['daerah_id']) abort(403);

        $path = storage_path('app/klaster_dokumen/' . $doc->nama_file);
        if (!file_exists($path)) abort(404, 'File tidak ditemukan.');

        $ext  = strtolower(pathinfo($doc->nama_file, PATHINFO_EXTENSION));
        $mime = [
            'pdf'  => 'application/pdf',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
        ];

        return isset($mime[$ext])
            ? response()->file($path, ['Content-Type' => $mime[$ext]])
            : response()->download($path, $doc->nama_file);
    }

    public function hapusDokumen($id)
    {
        $user = Session::get('user');
        $doc  = DB::table('klaster_dokumen_kriteria')->find($id);
        if (!$doc) abort(404);
        if ($user['role'] === 'operator' && $doc->perangkat_daerah_id != $user['daerah_id']) abort(403);

        $path = storage_path('app/klaster_dokumen/' . $doc->nama_file);
        if (file_exists($path)) unlink($path);
        DB::table('klaster_dokumen_kriteria')->where('id', $id)->delete();

        return back()->with('success', 'Dokumen dihapus.');
    }

    // ════════════════════════════════════════════════════
    //  HASIL LKE GABUNGAN
    // ════════════════════════════════════════════════════
    public function hasilLkeGabungan(Request $request)
    {
        $user      = Session::get('user');
        $tahun     = (int) $request->input('tahun', $this->tahunEvaluasi());
        $opdId     = $user['role'] === 'operator' ? $user['daerah_id'] : $request->input('opd_id');
        $listOpd   = DB::table('perangkat_daerah')->orderBy('nama')->get();
        $listTahun = $this->listTahun();

        $allData               = [];
        $totalNilaiAkhir       = 0;
        $totalBobotKeseluruhan = 0;

        $types  = ['utama', 'pendukung', 'tambahan'];
        $levels = [1, 2, 3];

        foreach ($types as $type) {
            foreach ($levels as $level) {

                $komponenUtama = DB::table('klaster_komponen')
                    ->whereNull('parent_id')
                    ->where('klaster_type', $type)
                    ->where('klaster_level', $level)
                    ->orderBy('urutan')
                    ->get();

                $subKomponen = DB::table('klaster_komponen')
                    ->whereNotNull('parent_id')
                    ->where('klaster_type', $type)
                    ->where('klaster_level', $level)
                    ->orderBy('parent_id')
                    ->orderBy('urutan')
                    ->get();

                $subIds = $subKomponen->pluck('id');

                $kriteriaPerKomponen = $subIds->isNotEmpty()
                    ? DB::table('klaster_kriteria')
                        ->whereIn('komponen_id', $subIds)
                        ->orderBy('komponen_id')
                        ->orderBy('nomor')
                        ->get()
                        ->groupBy('komponen_id')
                    : collect();

                $nilaiPerSub = [];
                if ($opdId && $subIds->isNotEmpty()) {
                    $rows = DB::table('klaster_penilaian')
                        ->where('perangkat_daerah_id', $opdId)
                        ->where('tahun', $tahun)
                        ->whereIn('komponen_id', $subIds)
                        ->get();
                    foreach ($rows as $r) {
                        $nilaiPerSub[$r->komponen_id] = floatval($r->nilai ?? 0);
                    }
                }

                $nilaiKomponen = [];
                foreach ($komponenUtama as $k) {
                    $total = 0;
                    foreach ($subKomponen->where('parent_id', $k->id) as $s) {
                        $total += $nilaiPerSub[$s->id] ?? 0;
                    }
                    $nilaiKomponen[$k->id]  = $total;
                    $totalNilaiAkhir       += $total;
                    $totalBobotKeseluruhan += floatval($k->bobot ?? 0);
                }

                foreach ($komponenUtama as $k) {
                    $subData = [];
                    foreach ($subKomponen->where('parent_id', $k->id) as $s) {
                        $nilai        = $nilaiPerSub[$s->id] ?? 0;
                        $kriteriaList = $kriteriaPerKomponen[$s->id] ?? collect();

                        $kriteriaData = [];
                        foreach ($kriteriaList as $kr) {
                            $kriteriaData[] = [
                                'nomor'  => $kr->nomor,
                                'uraian' => $kr->uraian,
                            ];
                        }

                        $subData[] = [
                            'nama'                => $s->nama,
                            'bobot'               => $s->bobot,
                            'nilai_instansi'      => $nilai,
                            'nilai_utama'         => ($type === 'utama'     && $level === 1) ? $nilai : 0,
                            'nilai_pendukung'     => ($type === 'pendukung' && $level === 2) ? $nilai : 0,
                            'nilai_tambahan'      => ($type === 'tambahan'  && $level === 3) ? $nilai : 0,
                            'nilai_unit'          => $nilai,
                            'nilai_akuntabilitas' => $nilai,
                            'kriteria'            => $kriteriaData,
                            'type'                => $type,
                            'level'               => $level,
                        ];
                    }

                    $allData[] = [
                        'komponen_nama'  => $k->nama,
                        'bobot_komponen' => $k->bobot,
                        'nilai_komponen' => $nilaiKomponen[$k->id] ?? 0,
                        'sub_komponen'   => $subData,
                        'type'           => $type,
                        'level'          => $level,
                    ];
                }
            }
        }

        $nilaiAkhir = $totalBobotKeseluruhan > 0
            ? round(($totalNilaiAkhir / $totalBobotKeseluruhan) * 100, 2)
            : 0;

        $predikat = $this->getPredikat($nilaiAkhir);

        return view('klaster.hasil-lke-gabungan', compact(
            'user', 'tahun', 'opdId', 'listOpd', 'listTahun',
            'allData', 'nilaiAkhir', 'predikat'
        ));
    }
   
    // ════════════════════════════════════════════════════
    //  HELPERS
    // ════════════════════════════════════════════════════
    private function getJawabanDariNilai(float $nilai): string
    {
        if ($nilai >= 90) return 'AA';
        if ($nilai >= 80) return 'A';
        if ($nilai >= 70) return 'BB';
        if ($nilai >= 60) return 'B';
        if ($nilai >= 50) return 'CC';
        if ($nilai >= 30) return 'C';
        if ($nilai >  0)  return 'D';
        return 'E';
    }

    private function getPredikat(float $nilai): array
    {
        if ($nilai >= 90) return ['kode' => 'AA', 'label' => 'Sangat Memuaskan', 'color' => '#059669'];
        if ($nilai >= 80) return ['kode' => 'A',  'label' => 'Memuaskan',        'color' => '#2563eb'];
        if ($nilai >= 70) return ['kode' => 'BB', 'label' => 'Sangat Baik',      'color' => '#7c3aed'];
        if ($nilai >= 60) return ['kode' => 'B',  'label' => 'Baik',             'color' => '#d4982e'];
        if ($nilai >= 50) return ['kode' => 'CC', 'label' => 'Cukup Baik',       'color' => '#f59e0b'];
        if ($nilai >= 30) return ['kode' => 'C',  'label' => 'Kurang',           'color' => '#dc2626'];
        if ($nilai >  0)  return ['kode' => 'D',  'label' => 'Sangat Kurang',    'color' => '#991b1b'];
        return                   ['kode' => 'E',  'label' => 'Tidak Ada Upaya',  'color' => '#6b7280'];
    }
}