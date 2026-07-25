<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LkeController extends Controller
{
    // ════════════════════════════════════════════════════
    //  HELPER — tahun evaluasi = tahun lalu
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
    //  HELPER — cari file di semua kemungkinan lokasi
    // ════════════════════════════════════════════════════
    private function cariFileLke(string $namaFile): ?string
    {
        $candidates = [
            storage_path('app/public/lke_dokumen/' . $namaFile),
            storage_path('app/lke_dokumen/' . $namaFile),
            public_path('storage/lke_dokumen/' . $namaFile),
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    // ════════════════════════════════════════════════════
    //  INDEX — tampil form evaluasi LKE AKIP
    // ════════════════════════════════════════════════════
public function index(Request $request)
{
    $user  = Session::get('user');
    $tahun = (int) $request->input('tahun', $this->tahunEvaluasi());

    // ⭐ Ambil daftar OPD yang memiliki user operator aktif
    $opdDenganOperator = DB::table('pengguna')
        ->where('role', 'operator')
        ->where('is_active', 1)
        ->whereNotNull('perangkat_daerah_id')
        ->pluck('perangkat_daerah_id')
        ->unique()
        ->toArray();

    // ==================== LOGIKA BERDASARKAN ROLE ====================
    if ($user['role'] === 'operator') {
        $opdId = $user['daerah_id'];
        
        if (!in_array($opdId, $opdDenganOperator)) {
            $listOpd = collect();
            $opdId = null;
        } else {
            $listOpd = DB::table('perangkat_daerah')
                ->where('id', $opdId)
                ->orderBy('nama')
                ->get();
        }
            
    } elseif ($user['role'] === 'evaluator') {
        $assignedOpdIds = DB::table('evaluator_opd')
            ->where('evaluator_id', $user['id'])
            ->pluck('perangkat_daerah_id')
            ->toArray();
        
        $availableOpdIds = array_intersect($assignedOpdIds, $opdDenganOperator);
        
        if (empty($availableOpdIds)) {
            $listOpd = collect();
            $opdId = null;
        } else {
            $listOpd = DB::table('perangkat_daerah')
                ->whereIn('id', $availableOpdIds)
                ->orderBy('nama')
                ->get();
            
            $opdId = $request->input('opd_id');
            if ($opdId && !in_array($opdId, $availableOpdIds)) {
                $opdId = null;
            }
        }
        
    } else {
        $opdId = $request->input('opd_id');
        
        if (empty($opdDenganOperator)) {
            $listOpd = collect();
            $opdId = null;
        } else {
            $listOpd = DB::table('perangkat_daerah')
                ->whereIn('id', $opdDenganOperator)
                ->orderBy('nama')
                ->get();
            
            if ($opdId && !in_array($opdId, $opdDenganOperator)) {
                $opdId = null;
            }
        }
    }

    $listTahun = $this->listTahun();

    $komponenUtama = DB::table('lke_komponen')
        ->whereNull('parent_id')
        ->orderBy('urutan')
        ->get();

    $subKomponen = DB::table('lke_komponen')
        ->whereNotNull('parent_id')
        ->orderBy('parent_id')
        ->orderBy('urutan')
        ->get();

    $kriteriaPerKomponen = DB::table('lke_kriteria')
        ->orderBy('komponen_id')
        ->orderBy('nomor')
        ->get()
        ->groupBy('komponen_id');

    $nilaiPerSubKomponen = [];
    $nilaiKomponenUtama  = [];
    $nilaiSubKomponen    = collect();
    $catatanPerKriteria  = collect();
    $dokumenPerKriteria  = [];
    $nilaiAkhir          = 0;
    $predikat            = $this->getPredikat(0);

    // ⭐ MAP PERSENTASE UNTUK KONVERSI JAWABAN KE NILAI
    $persentaseMap = [
        'AA' => 100,
        'A' => 90,
        'BB' => 80,
        'B' => 70,
        'CC' => 60,
        'C' => 50,
        'D' => 30,
        'E' => 0
    ];

    if ($opdId) {
        $rows = DB::table('lke_penilaian')
            ->where('perangkat_daerah_id', $opdId)
            ->where('tahun', $tahun)
            ->get();

        // ⭐ BANGUN MAP BOBOT SUB KOMPONEN UNTUK PERHITUNGAN CEPAT
        $bobotSubMap = [];
        foreach ($subKomponen as $s) {
            $bobotSubMap[$s->id] = floatval($s->bobot ?? 0);
        }

        foreach ($rows as $r) {
            $komponenId = $r->komponen_id;
            
            // ⭐ PRIORITAS: Jika ada jawaban, hitung nilai dari jawaban × bobot
            if (!empty($r->jawaban) && isset($persentaseMap[$r->jawaban])) {
                $bobot = $bobotSubMap[$komponenId] ?? 0;
                $persentase = $persentaseMap[$r->jawaban];
                $nilaiTerhitung = ($persentase * $bobot) / 100;
                $nilaiPerSubKomponen[$komponenId] = round($nilaiTerhitung, 2);
            } else {
                // Fallback: gunakan nilai yang tersimpan
                $nilaiPerSubKomponen[$komponenId] = floatval($r->nilai ?? 0);
            }
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

        $catatanPerKriteria = DB::table('lke_penilaian_kriteria')
            ->where('perangkat_daerah_id', $opdId)
            ->where('tahun', $tahun)
            ->whereIn('kriteria_id', $kriteriaIds)
            ->get()
            ->keyBy('kriteria_id');

        $dokumenRows = DB::table('lke_dokumen_kriteria')
            ->where('perangkat_daerah_id', $opdId)
            ->where('tahun', $tahun)
            ->whereIn('kriteria_id', $kriteriaIds)
            ->get();

        foreach ($dokumenRows as $d) {
            $dokumenPerKriteria[$d->kriteria_id][] = $d;
        }

        $predikat = $this->getPredikat($nilaiAkhir);
    }

    return view('lke.index', compact(
        'user', 'tahun', 'opdId', 'listOpd', 'listTahun',
        'komponenUtama', 'subKomponen', 'kriteriaPerKomponen',
        'nilaiPerSubKomponen', 'nilaiKomponenUtama',
        'nilaiSubKomponen', 'catatanPerKriteria', 'dokumenPerKriteria',
        'nilaiAkhir', 'predikat'
    ));
}

  // ════════════════════════════════════════════════════
//  SIMPAN — simpan nilai & catatan LKE AKIP
// ════════════════════════════════════════════════════
public function simpan(Request $request)
{
    $user  = Session::get('user');
    $tahun = (int) $request->input('tahun', $this->tahunEvaluasi());

    if ($user['role'] === 'operator') {
        $opdId = $user['daerah_id'];
    } else {
        $opdId = $request->input('opd_id');
    }

    if (!$opdId) {
        return back()->with('error', 'Pilih OPD terlebih dahulu.');
    }

    // ⭐ VALIDASI: Pastikan OPD memiliki user operator aktif
    $hasOperator = DB::table('pengguna')
        ->where('role', 'operator')
        ->where('perangkat_daerah_id', $opdId)
        ->where('is_active', 1)
        ->exists();
    
    if (!$hasOperator) {
        return back()->with('error', 'OPD ini belum memiliki user operator. Tidak dapat melakukan penilaian.');
    }

    // ⭐ AMBIL JAWABAN (PREDIKAT) DARI FORM, BUKAN NILAI
    $jawabanInput = $request->input('jawaban', []);
    $catatanInput = $request->input('catatan_sub', []);

    // ⭐ KONVERSI PREDIKAT KE PERSENTASE
    $persentaseMap = [
        'AA' => 100,
        'A' => 90,
        'BB' => 80,
        'B' => 70,
        'CC' => 60,
        'C' => 50,
        'D' => 30,
        'E' => 0
    ];

    foreach ($jawabanInput as $komponenId => $jawaban) {
        if ($jawaban === null || $jawaban === '') continue;

        // Dapatkan bobot sub komponen
        $subKomp = DB::table('lke_komponen')->where('id', $komponenId)->first();
        $bobotSub = floatval($subKomp->bobot ?? 0);

        // Hitung nilai dari jawaban dan bobot
        $persentase = $persentaseMap[$jawaban] ?? 0;
        $nilai = ($persentase * $bobotSub) / 100;
        $nilai = round($nilai, 2);
        $catatan = $catatanInput[$komponenId] ?? '';

        DB::table('lke_penilaian')->updateOrInsert(
            [
                'perangkat_daerah_id' => $opdId,
                'tahun'               => $tahun,
                'komponen_id'         => $komponenId,
            ],
            [
                'nilai'        => $nilai,
                'persentase'   => $persentase,
                'jawaban'      => $jawaban,
                'catatan'      => $catatan,
                'dinilai_oleh' => $user['id'],
                'updated_at'   => now(),
                'created_at'   => now(),
            ]
        );
    }

    // ⭐ KOMENTAR ADMIN, CATATAN OPERATOR, EVIDENCE (tetap sama)
    $komAdmin = $request->input('komentar_admin', []);
    $catOp    = $request->input('catatan_operator', []);
    $daftarEv = $request->input('daftar_evidence', []);
    $ids      = array_unique(array_merge(
        array_keys($komAdmin),
        array_keys($catOp),
        array_keys($daftarEv)
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

            if ($user['role'] === 'admin' && isset($daftarEv[$krId]) && $daftarEv[$krId] !== '') {
                $upd['daftar_evidence'] = $daftarEv[$krId];
            }

            DB::table('lke_penilaian_kriteria')->updateOrInsert(
                [
                    'kriteria_id'         => $krId,
                    'perangkat_daerah_id' => $opdId,
                    'tahun'               => $tahun,
                ],
                $upd
            );
        } catch (\Exception $e) {
            // Lanjut ke kriteria berikutnya jika error
        }
    }

    // Hitung total nilai
    $totalNilai = DB::table('lke_penilaian')
        ->where('perangkat_daerah_id', $opdId)
        ->where('tahun', $tahun)
        ->sum('nilai');

    $nilaiAkhir = floatval($totalNilai);
    $predikat   = $this->getPredikat($nilaiAkhir);

    $opd     = DB::table('perangkat_daerah')->where('id', $opdId)->first();
    $namaOpd = $opd->nama ?? 'OPD';

    if ($user['role'] === 'operator') {
        $adminUsers = DB::table('pengguna')->where('role', 'admin')->get();
        foreach ($adminUsers as $admin) {
            DB::table('notifikasi')->insert([
                'user_id'    => $admin->id,
                'judul'      => 'Input LKE AKIP',
                'pesan'      => "Operator {$namaOpd} menginput data LKE AKIP untuk tahun {$tahun}.",
                'tipe'       => 'lke_input',
                'ikon'       => '📊',
                'warna'      => 'indigo',
                'url'        => route('evaluasi.lke', ['tahun' => $tahun, 'opd_id' => $opdId]),
                'nama_opd'   => $namaOpd,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    } else {
        $operator = DB::table('pengguna')
            ->where('perangkat_daerah_id', $opdId)
            ->where('role', 'operator')
            ->first();

        if ($operator) {
            DB::table('notifikasi')->insert([
                'user_id'    => $operator->id,
                'judul'      => '✅ Penilaian LKE AKIP Selesai',
                'pesan'      => "Admin telah menilai LKE AKIP {$namaOpd} tahun {$tahun}. Nilai akhir: {$nilaiAkhir} ({$predikat['kode']}).",
                'tipe'       => 'lke_penilaian',
                'ikon'       => '📊',
                'warna'      => 'green',
                'url'        => route('evaluasi.lke.rekap', ['tahun2' => $tahun, 'opd_id' => $opdId]),
                'nama_opd'   => $namaOpd,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    return redirect()
        ->route('evaluasi.lke', ['tahun' => $tahun, 'opd_id' => $opdId])
        ->with('success', 'Data LKE AKIP tahun ' . $tahun . ' berhasil disimpan!');
}

// ════════════════════════════════════════════════════
//  SIMPAN NILAI SAJA
// ════════════════════════════════════════════════════
public function simpanNilai(Request $request)
{
    $user  = Session::get('user');
    $tahun = (int) $request->input('tahun', $this->tahunEvaluasi());

    if ($user['role'] === 'operator') {
        $opdId = $user['daerah_id'];
    } else {
        $opdId = $request->input('opd_id');
    }

    if (!$opdId) {
        return back()->with('error', 'Pilih OPD terlebih dahulu.');
    }

    $hasOperator = DB::table('pengguna')
        ->where('role', 'operator')
        ->where('perangkat_daerah_id', $opdId)
        ->where('is_active', 1)
        ->exists();
    
    if (!$hasOperator) {
        return back()->with('error', 'OPD ini belum memiliki user operator. Tidak dapat melakukan penilaian.');
    }

    // ⭐ AMBIL JAWABAN, BUKAN NILAI
    $jawabanInput = $request->input('jawaban', []);
    $savedCount = 0;

    $persentaseMap = [
        'AA' => 100, 'A' => 90, 'BB' => 80, 'B' => 70,
        'CC' => 60, 'C' => 50, 'D' => 30, 'E' => 0
    ];

    foreach ($jawabanInput as $komponenId => $jawaban) {
        if ($jawaban === null || $jawaban === '') continue;

        $subKomp = DB::table('lke_komponen')->where('id', $komponenId)->first();
        $bobotSub = floatval($subKomp->bobot ?? 0);

        $persentase = $persentaseMap[$jawaban] ?? 0;
        $nilai = ($persentase * $bobotSub) / 100;
        $nilai = round($nilai, 2);

        DB::table('lke_penilaian')->updateOrInsert(
            [
                'perangkat_daerah_id' => $opdId,
                'tahun'               => $tahun,
                'komponen_id'         => $komponenId,
            ],
            [
                'nilai'        => $nilai,
                'persentase'   => $persentase,
                'jawaban'      => $jawaban,
                'dinilai_oleh' => $user['id'],
                'updated_at'   => now(),
                'created_at'   => now(),
            ]
        );
        $savedCount++;
    }

    return redirect()->back()->with('success', $savedCount . ' nilai berhasil disimpan!');
}
    // ════════════════════════════════════════════════════
    //  REKAP
    // ════════════════════════════════════════════════════
    public function rekap(Request $request)
    {
        $user  = Session::get('user');
        $base  = $this->tahunEvaluasi();

        $tahun1 = (int) $request->input('tahun1', $base - 1);
        $tahun2 = (int) $request->input('tahun2', $base);

        if ($user['role'] === 'operator') {
            $opdId = $user['daerah_id'];
        } else {
            $opdId = $request->input('opd_id');
        }

        $listOpd   = DB::table('perangkat_daerah')->orderBy('nama')->get();
        $listTahun = $this->listTahun();

        $komponenUtama = DB::table('lke_komponen')
            ->whereNull('parent_id')
            ->orderBy('urutan')
            ->get();

        $subKomponen = DB::table('lke_komponen')
            ->whereNotNull('parent_id')
            ->get()
            ->groupBy('parent_id');

        if ($user['role'] === 'operator') {
            $daftarOpd = $listOpd->where('id', $opdId);
        } else {
            $daftarOpd = $opdId ? $listOpd->where('id', $opdId) : $listOpd;
        }

        $rekapData = [];

        foreach ($daftarOpd as $opd) {
            $pen1 = DB::table('lke_penilaian')
                ->where('perangkat_daerah_id', $opd->id)
                ->where('tahun', $tahun1)
                ->get()->keyBy('komponen_id');

            $pen2 = DB::table('lke_penilaian')
                ->where('perangkat_daerah_id', $opd->id)
                ->where('tahun', $tahun2)
                ->get()->keyBy('komponen_id');

            if ($pen1->isEmpty() && $pen2->isEmpty() && $user['role'] !== 'operator') continue;

            $totalT1       = 0;
            $totalT2       = 0;
            $totalBobotMax = 0;
            $komData       = [];

            foreach ($komponenUtama as $k) {
                $subs        = $subKomponen[$k->id] ?? collect();
                $nilaiT1Komp = 0;
                $nilaiT2Komp = 0;

                foreach ($subs as $s) {
                    $nilaiT1Komp += floatval($pen1[$s->id]->nilai ?? 0);
                    $nilaiT2Komp += floatval($pen2[$s->id]->nilai ?? 0);
                }

                $totalT1       += $nilaiT1Komp;
                $totalT2       += $nilaiT2Komp;
                $totalBobotMax += floatval($k->bobot ?? 0);

                $komData[] = [
                    'nama'     => $k->nama,
                    'bobot'    => floatval($k->bobot ?? 0),
                    'nilai_t1' => $nilaiT1Komp,
                    'nilai_t2' => $nilaiT2Komp,
                ];
            }

            $nilaiAkhirT1 = $totalBobotMax > 0 ? round(($totalT1 / $totalBobotMax) * 100, 2) : 0;
            $nilaiAkhirT2 = $totalBobotMax > 0 ? round(($totalT2 / $totalBobotMax) * 100, 2) : 0;

            $rekapData[] = [
                'opd_id'       => $opd->id,
                'opd'          => $opd->nama,
                'nilai_tahun1' => $nilaiAkhirT1,
                'nilai_tahun2' => $nilaiAkhirT2,
                'komponen'     => $komData,
            ];
        }

        usort($rekapData, fn($a, $b) => $b['nilai_tahun2'] <=> $a['nilai_tahun2']);

        return view('lke.rekap', compact(
            'user', 'tahun1', 'tahun2', 'opdId',
            'rekapData', 'listOpd', 'listTahun', 'komponenUtama'
        ));
    }

    // ════════════════════════════════════════════════════
    //  UPLOAD DOKUMEN (AJAX — return JSON)
    //  ⭐ PERBAIKAN: simpan ke public/lke_dokumen agar
    //     bisa diakses via php artisan storage:link
    // ════════════════════════════════════════════════════
    public function uploadDokumen(Request $request)
    {
        $user = Session::get('user');

        $request->validate([
            'kriteria_id' => 'required|integer',
            'tahun'       => 'required|integer',
            'file'        => 'required|file|mimes:pdf,docx,xlsx,doc,xls,jpg,jpeg,png|max:20480',
        ]);

        $opdId = $user['role'] === 'operator'
               ? $user['daerah_id']
               : $request->input('opd_id');

        if (!$opdId) {
            return response()->json(['ok' => false, 'message' => 'OPD tidak ditemukan.'], 422);
        }

        $file  = $request->file('file');
        $ext   = $file->getClientOriginalExtension();
        $fname = 'lke_' . $opdId
               . '_kr' . $request->kriteria_id
               . '_' . $request->tahun
               . '_' . time() . '.' . $ext;

        // ⭐ Simpan ke storage/app/public/lke_dokumen/
        // Folder ini bisa diakses via public/storage/lke_dokumen/ setelah storage:link
        $folderPath = storage_path('app/public/lke_dokumen');
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
        $file->move($folderPath, $fname);

        try {
            $id = DB::table('lke_dokumen_kriteria')->insertGetId([
                'kriteria_id'         => $request->kriteria_id,
                'perangkat_daerah_id' => $opdId,
                'tahun'               => $request->tahun,
                'nama_file'           => $fname,
                'path_file'           => 'public/lke_dokumen/' . $fname,
                'uploaded_by'         => $user['id'],
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            // Notifikasi ke admin jika yang upload adalah operator
            if ($user['role'] === 'operator') {
                $opd     = DB::table('perangkat_daerah')->where('id', $opdId)->first();
                $namaOpd = $opd->nama ?? 'OPD';

                $adminUsers = DB::table('pengguna')->where('role', 'admin')->get();
                foreach ($adminUsers as $admin) {
                    DB::table('notifikasi')->insert([
                        'user_id'      => $admin->id,
                        'judul'        => 'Upload Dokumen LKE',
                        'pesan'        => "Operator {$namaOpd} mengupload dokumen pendukung LKE AKIP tahun {$request->tahun}.",
                        'tipe'         => 'lke_dokumen',
                        'ikon'         => '📎',
                        'warna'        => 'amber',
                        'url'          => route('evaluasi.lke.dokumen.list', [
                            'tahun'  => $request->tahun,
                            'opd_id' => $opdId,
                        ]),
                        'referensi_id' => $request->kriteria_id,
                        'nama_opd'     => $namaOpd,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
            }

            return response()->json([
                'ok'        => true,
                'id'        => $id,
                'nama_file' => $fname,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ok'      => false,
                'message' => 'Gagal menyimpan dokumen: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ════════════════════════════════════════════════════
    //  LIHAT DOKUMEN
    //  ⭐ PERBAIKAN: gunakan helper cariFileLke()
    // ════════════════════════════════════════════════════
    public function lihatDokumen($id)
    {
        $user = Session::get('user');
        $doc  = DB::table('lke_dokumen_kriteria')->find($id);

        if (!$doc) abort(404);

        if ($user['role'] === 'operator' && $doc->perangkat_daerah_id != $user['daerah_id']) {
            abort(403);
        }

        $path = $this->cariFileLke($doc->nama_file);

        if (!$path) {
            abort(404, 'File tidak ditemukan di server. Nama file: ' . $doc->nama_file);
        }

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

    // ════════════════════════════════════════════════════
    //  HAPUS DOKUMEN (AJAX — return JSON)
    //  ⭐ PERBAIKAN: gunakan helper cariFileLke()
    // ════════════════════════════════════════════════════
    public function hapusDokumen($id)
    {
        $user = Session::get('user');
        $doc  = DB::table('lke_dokumen_kriteria')->find($id);

        if (!$doc) {
            return response()->json(['ok' => false, 'message' => 'Dokumen tidak ditemukan.'], 404);
        }

        if ($user['role'] === 'operator' && $doc->perangkat_daerah_id != $user['daerah_id']) {
            return response()->json(['ok' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $path = $this->cariFileLke($doc->nama_file);
        if ($path) {
            @unlink($path);
        }

        DB::table('lke_dokumen_kriteria')->where('id', $id)->delete();

        return response()->json(['ok' => true]);
    }

    // ════════════════════════════════════════════════════
    //  DAFTAR SEMUA DOKUMEN (admin only)
    // ════════════════════════════════════════════════════
    public function daftarDokumen(Request $request)
    {
        $user = Session::get('user');

        if ($user['role'] !== 'admin') {
            abort(403, 'Hanya admin yang dapat mengakses halaman ini.');
        }

        $tahun     = (int) $request->input('tahun', $this->tahunEvaluasi());
        $opdId     = $request->input('opd_id');
        $listOpd   = DB::table('perangkat_daerah')->orderBy('nama')->get();
        $listTahun = $this->listTahun();

        $query = DB::table('lke_dokumen_kriteria as d')
            ->leftJoin('perangkat_daerah as pd', 'd.perangkat_daerah_id', '=', 'pd.id')
            ->leftJoin('lke_kriteria as k',       'd.kriteria_id',         '=', 'k.id')
            ->leftJoin('lke_komponen as kp',       'k.komponen_id',         '=', 'kp.id')
            ->leftJoin('pengguna as p',             'd.uploaded_by',         '=', 'p.id')
            ->select(
                'd.*',
                'pd.nama as nama_opd',
                'k.nomor as kriteria_nomor',
                'kp.nama as komponen_nama',
                'p.nama as uploader_nama'
            )
            ->where('d.tahun', $tahun)
            ->orderBy('d.created_at', 'desc');

        if ($opdId) {
            $query->where('d.perangkat_daerah_id', $opdId);
        }

        $dokumen = $query->get();

        $stats = [
            'total'   => $dokumen->count(),
            'per_opd' => $dokumen->groupBy('nama_opd')->map->count(),
        ];

        return view('lke.dokumen', compact(
            'user', 'dokumen', 'tahun', 'listTahun',
            'listOpd', 'opdId', 'stats'
        ));
    }

    // ════════════════════════════════════════════════════
    //  HELPERS PRIVATE
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
        if ($nilai >= 90) return ['kode' => 'AA', 'label' => 'AA — Sangat Memuaskan', 'color' => '#059669'];
        if ($nilai >= 80) return ['kode' => 'A',  'label' => 'A — Memuaskan',         'color' => '#2563eb'];
        if ($nilai >= 70) return ['kode' => 'BB', 'label' => 'BB — Sangat Baik',      'color' => '#7c3aed'];
        if ($nilai >= 60) return ['kode' => 'B',  'label' => 'B — Baik',              'color' => '#d4982e'];
        if ($nilai >= 50) return ['kode' => 'CC', 'label' => 'CC — Cukup Baik',       'color' => '#f59e0b'];
        if ($nilai >= 30) return ['kode' => 'C',  'label' => 'C — Kurang',            'color' => '#dc2626'];
        if ($nilai >  0)  return ['kode' => 'D',  'label' => 'D — Sangat Kurang',     'color' => '#991b1b'];
        return                   ['kode' => 'E',  'label' => 'E — Tidak Ada Upaya',   'color' => '#6b7280'];
    }
}