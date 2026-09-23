<?php

namespace App\Http\Controllers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

    $opdDenganOperator = DB::table('pengguna')
        ->where('role', 'operator')
        ->where('is_active', 1)
        ->whereNotNull('perangkat_daerah_id')
        ->pluck('perangkat_daerah_id')
        ->unique()
        ->toArray();

    if ($user['role'] === 'operator') {
        $opdId = $user['daerah_id'];
        if (!in_array($opdId, $opdDenganOperator)) {
            $listOpd = collect();
            $opdId = null;
        } else {
            $listOpd = DB::table('perangkat_daerah')->where('id', $opdId)->orderBy('nama')->get();
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
            $listOpd = DB::table('perangkat_daerah')->whereIn('id', $availableOpdIds)->orderBy('nama')->get();
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
            $listOpd = DB::table('perangkat_daerah')->whereIn('id', $opdDenganOperator)->orderBy('nama')->get();
            if ($opdId && !in_array($opdId, $opdDenganOperator)) {
                $opdId = null;
            }
        }
    }

    $listTahun = $this->listTahun();

    $komponenUtama = DB::table('lke_komponen')->whereNull('parent_id')->orderBy('urutan')->get();
    $subKomponen   = DB::table('lke_komponen')->whereNotNull('parent_id')->orderBy('parent_id')->orderBy('urutan')->get();

    $kriteriaPerKomponen = DB::table('lke_kriteria')
        ->orderBy('komponen_id')->orderBy('nomor')->get()->groupBy('komponen_id');

    // ── Evaluator (resmi) ──
    $nilaiPerSubKomponen = [];
    $nilaiKomponenUtama  = [];
    $nilaiSubKomponen    = collect();
    $nilaiAkhir          = 0;
    $predikat            = $this->getPredikat(0);

    // ⭐ BARU — Operator (asli/self-assessment)
    $nilaiOperatorPerSubKomponen = [];
    $nilaiKomponenUtamaOperator  = [];
    $nilaiAkhirOperator          = 0;

    $catatanPerKriteria = collect();
    $dokumenPerKriteria = [];

    $persentaseMap = ['AA'=>100,'A'=>90,'BB'=>80,'B'=>70,'CC'=>60,'C'=>50,'D'=>30,'E'=>0];

    if ($opdId) {
        $rows = DB::table('lke_penilaian')
            ->where('perangkat_daerah_id', $opdId)
            ->where('tahun', $tahun)
            ->get();

        $bobotSubMap = [];
        foreach ($subKomponen as $s) {
            $bobotSubMap[$s->id] = floatval($s->bobot ?? 0);
        }

        foreach ($rows as $r) {
            $komponenId = $r->komponen_id;
            $bobot = $bobotSubMap[$komponenId] ?? 0;

            // Evaluator (resmi)
            if (!empty($r->jawaban) && isset($persentaseMap[$r->jawaban])) {
                $nilaiPerSubKomponen[$komponenId] = round(($persentaseMap[$r->jawaban] * $bobot) / 100, 2);
            } else {
                $nilaiPerSubKomponen[$komponenId] = floatval($r->nilai ?? 0);
            }

            // ⭐ BARU — Operator (asli)
            if (!empty($r->jawaban_operator) && isset($persentaseMap[$r->jawaban_operator])) {
                $nilaiOperatorPerSubKomponen[$komponenId] = round(($persentaseMap[$r->jawaban_operator] * $bobot) / 100, 2);
            } else {
                $nilaiOperatorPerSubKomponen[$komponenId] = floatval($r->nilai_operator ?? 0);
            }
        }

        $nilaiSubKomponen = $rows->keyBy('komponen_id');

        foreach ($komponenUtama as $k) {
            $total = 0;
            $totalOperator = 0; // ⭐ BARU
            foreach ($subKomponen->where('parent_id', $k->id) as $s) {
                $total         += $nilaiPerSubKomponen[$s->id] ?? 0;
                $totalOperator += $nilaiOperatorPerSubKomponen[$s->id] ?? 0; // ⭐ BARU
            }
            $nilaiKomponenUtama[$k->id]         = $total;
            $nilaiKomponenUtamaOperator[$k->id] = $totalOperator; // ⭐ BARU
            $nilaiAkhir         += $total;
            $nilaiAkhirOperator += $totalOperator; // ⭐ BARU
        }

        $kriteriaIds = $kriteriaPerKomponen->flatten()->pluck('id');

        $catatanPerKriteria = DB::table('lke_penilaian_kriteria')
            ->where('perangkat_daerah_id', $opdId)->where('tahun', $tahun)
            ->whereIn('kriteria_id', $kriteriaIds)->get()->keyBy('kriteria_id');

        $dokumenRows = DB::table('lke_dokumen_kriteria')
            ->where('perangkat_daerah_id', $opdId)->where('tahun', $tahun)
            ->whereIn('kriteria_id', $kriteriaIds)->get();

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
        'nilaiAkhir', 'predikat',
        'nilaiOperatorPerSubKomponen', 'nilaiKomponenUtamaOperator', 'nilaiAkhirOperator' // ⭐ BARU
    ));
}

  // ════════════════════════════════════════════════════
//  SIMPAN — simpan nilai & catatan LKE AKIP
// ════════════════════════════════════════════════════
public function simpan(Request $request)
{
    $user  = Session::get('user');
    $tahun = (int) $request->input('tahun', $this->tahunEvaluasi());

    $opdId = $user['role'] === 'operator' ? $user['daerah_id'] : $request->input('opd_id');

    if (!$opdId) {
        return back()->with('error', 'Pilih OPD terlebih dahulu.');
    }

    $hasOperator = DB::table('pengguna')
        ->where('role', 'operator')->where('perangkat_daerah_id', $opdId)
        ->where('is_active', 1)->exists();

    if (!$hasOperator) {
        return back()->with('error', 'OPD ini belum memiliki user operator. Tidak dapat melakukan penilaian.');
    }

    $persentaseMap = ['AA'=>100,'A'=>90,'BB'=>80,'B'=>70,'CC'=>60,'C'=>50,'D'=>30,'E'=>0];

    // ══ OPERATOR — isi nilai_operator (lembar asli miliknya) ══
    if ($user['role'] === 'operator') {
        $jawabanInput = $request->input('jawaban_operator', []);

        foreach ($jawabanInput as $komponenId => $jawaban) {
            if ($jawaban === null || $jawaban === '') continue;

            $subKomp    = DB::table('lke_komponen')->where('id', $komponenId)->first();
            $bobotSub   = floatval($subKomp->bobot ?? 0);
            $persentase = $persentaseMap[$jawaban] ?? 0;
            $nilai      = round(($persentase * $bobotSub) / 100, 2);

            DB::table('lke_penilaian')->updateOrInsert(
                ['perangkat_daerah_id' => $opdId, 'tahun' => $tahun, 'komponen_id' => $komponenId],
                [
                    'nilai_operator'        => $nilai,
                    'persentase_operator'   => $persentase,
                    'jawaban_operator'      => $jawaban,
                    'dinilai_operator_oleh' => $user['id'],
                    'updated_at'            => now(),
                    'created_at'            => now(),
                ]
            );
        }
    }

    // ══ EVALUATOR — isi nilai resmi (dipakai Rekap) ══
    if ($user['role'] === 'evaluator') {
        $jawabanInput = $request->input('jawaban', []);
        $catatanInput = $request->input('catatan_sub', []);

        foreach ($jawabanInput as $komponenId => $jawaban) {
            if ($jawaban === null || $jawaban === '') continue;

            $subKomp    = DB::table('lke_komponen')->where('id', $komponenId)->first();
            $bobotSub   = floatval($subKomp->bobot ?? 0);
            $persentase = $persentaseMap[$jawaban] ?? 0;
            $nilai      = round(($persentase * $bobotSub) / 100, 2);
            $catatan    = $catatanInput[$komponenId] ?? '';

            DB::table('lke_penilaian')->updateOrInsert(
                ['perangkat_daerah_id' => $opdId, 'tahun' => $tahun, 'komponen_id' => $komponenId],
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
    }
    // Admin: tidak ada blok di sini sama sekali — admin tidak bisa menilai.

    // ══ CATATAN OPERATOR / KOMENTAR EVALUATOR / EVIDENCE ══
    $catOp   = $request->input('catatan_operator', []);
    $komEval = $request->input('komentar_admin', []); // nama kolom DB tetap, sekarang milik evaluator
    $daftarEv = $request->input('daftar_evidence', []);
    $ids = array_unique(array_merge(array_keys($catOp), array_keys($komEval), array_keys($daftarEv)));

    foreach ($ids as $krId) {
        try {
            $upd = ['updated_at' => now(), 'created_at' => now()];

            if ($user['role'] === 'evaluator') {
                $upd['komentar_admin'] = $komEval[$krId] ?? '';
            }

            if ($user['role'] === 'operator') {
                $upd['catatan_operator'] = $catOp[$krId] ?? '';
                $upd['daftar_evidence']  = $daftarEv[$krId] ?? '';
            }

            DB::table('lke_penilaian_kriteria')->updateOrInsert(
                ['kriteria_id' => $krId, 'perangkat_daerah_id' => $opdId, 'tahun' => $tahun],
                $upd
            );
        } catch (\Exception $e) {}
    }

    $totalNilai = DB::table('lke_penilaian')
        ->where('perangkat_daerah_id', $opdId)->where('tahun', $tahun)->sum('nilai');
    $nilaiAkhir = floatval($totalNilai);
    $predikat   = $this->getPredikat($nilaiAkhir);

    $opd     = DB::table('perangkat_daerah')->where('id', $opdId)->first();
    $namaOpd = $opd->nama ?? 'OPD';

    // ⭐ Notifikasi disesuaikan: operator submit → notif ke EVALUATOR yang di-assign (bukan admin lagi)
    if ($user['role'] === 'operator') {
        $evaluatorIds = DB::table('evaluator_opd')->where('perangkat_daerah_id', $opdId)->pluck('evaluator_id');
        $evaluators   = DB::table('pengguna')->whereIn('id', $evaluatorIds)->get();

        foreach ($evaluators as $ev) {
            DB::table('notifikasi')->insert([
                'user_id'    => $ev->id,
                'judul'      => 'Operator Mengisi Nilai LKE AKIP',
                'pesan'      => "Operator {$namaOpd} telah mengisi penilaian mandiri LKE AKIP tahun {$tahun}. Silakan tinjau dan tentukan nilai resmi.",
                'tipe'       => 'lke_input',
                'ikon'       => '📊',
                'warna'      => 'indigo',
                'url'        => route('evaluasi.lke', ['tahun' => $tahun, 'opd_id' => $opdId]),
                'nama_opd'   => $namaOpd,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    } elseif ($user['role'] === 'evaluator') {
        $operator = DB::table('pengguna')->where('perangkat_daerah_id', $opdId)->where('role', 'operator')->first();
        if ($operator) {
            DB::table('notifikasi')->insert([
                'user_id'    => $operator->id,
                'judul'      => '✅ Penilaian LKE AKIP Selesai',
                'pesan'      => "Evaluator telah menetapkan nilai resmi LKE AKIP {$namaOpd} tahun {$tahun}. Nilai akhir: {$nilaiAkhir} ({$predikat['kode']}).",
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

     public function exportExcel(Request $request)
    {
        $user  = Session::get('user');
        $tahun = (int) $request->input('tahun', $this->tahunEvaluasi());
        $opdId = $user['role'] === 'operator' ? $user['daerah_id'] : $request->input('opd_id');
 
        if (!$opdId) {
            return back()->with('error', 'Pilih OPD terlebih dahulu sebelum mencetak.');
        }
 
        $opd     = DB::table('perangkat_daerah')->where('id', $opdId)->first();
        $namaOpd = $opd->nama ?? 'OPD';
 
        $komponenUtama = DB::table('lke_komponen')->whereNull('parent_id')->orderBy('urutan')->get();
        $subKomponen   = DB::table('lke_komponen')->whereNotNull('parent_id')->orderBy('parent_id')->orderBy('urutan')->get();
        $kriteriaAll   = DB::table('lke_kriteria')->orderBy('komponen_id')->orderBy('nomor')->get()->groupBy('komponen_id');
 
        $penilaian = DB::table('lke_penilaian')
            ->where('perangkat_daerah_id', $opdId)->where('tahun', $tahun)
            ->get()->keyBy('komponen_id'); // komponen_id di sini = id sub-komponen
 
        $kriteriaIds = $kriteriaAll->flatten()->pluck('id');
 
        $catatanKriteria = DB::table('lke_penilaian_kriteria')
            ->where('perangkat_daerah_id', $opdId)->where('tahun', $tahun)
            ->whereIn('kriteria_id', $kriteriaIds)
            ->get()->keyBy('kriteria_id');
 
        $dokumenPerKriteria = DB::table('lke_dokumen_kriteria')
            ->where('perangkat_daerah_id', $opdId)->where('tahun', $tahun)
            ->whereIn('kriteria_id', $kriteriaIds)
            ->get()->groupBy('kriteria_id');
 
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // buang sheet kosong default
 
// ── Sheet 1: Lembar Evaluator (mode 'operator' = self-assessment) ──
$sheetOp = new Worksheet($spreadsheet, 'Lembar Evaluator');
$spreadsheet->addSheet($sheetOp);
$this->tulisLembarLkeKeSheet(
    $sheetOp, 'operator', $namaOpd, $tahun,
    $komponenUtama, $subKomponen, $kriteriaAll,
    $penilaian, $catatanKriteria, $dokumenPerKriteria
);

// ── Sheet 2: Lembar Verifikator (mode 'evaluator' = nilai resmi) ──
$sheetEv = new Worksheet($spreadsheet, 'Lembar Verifikator');
$spreadsheet->addSheet($sheetEv);
$this->tulisLembarLkeKeSheet(
    $sheetEv, 'evaluator', $namaOpd, $tahun,
    $komponenUtama, $subKomponen, $kriteriaAll,
    $penilaian, $catatanKriteria, $dokumenPerKriteria
);
 
        $spreadsheet->setActiveSheetIndex(1); // buka di tab Evaluator (nilai resmi) saat file dibuka
 
        // ── Output ──
        $filename = 'LKE_AKIP_' . str_replace(' ', '_', $namaOpd) . '_' . $tahun . '.xlsx';
 
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
 
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
 
    /**
     * Tulis satu lembar kerja (operator ATAU evaluator) ke satu Worksheet.
     * Kolom & sumber data disamakan persis dengan
     * lke/partials/tabel-nilai.blade.php untuk mode yang sama.
     */
    private function tulisLembarLkeKeSheet(
        $sheet, string $mode, string $namaOpd, int $tahun,
        $komponenUtama, $subKomponen, $kriteriaAll,
        $penilaian, $catatanKriteria, $dokumenPerKriteria
    ): void {
        $fieldJawaban = $mode === 'operator' ? 'jawaban_operator' : 'jawaban';
        $fieldNilai   = $mode === 'operator' ? 'nilai_operator'   : 'nilai';
 
        if ($mode === 'operator') {
            $header     = ['No', 'Komponen / Sub Komponen / Kriteria', 'Bobot', 'Jawaban (Predikat)', 'Nilai',
                           'Catatan Operator', 'Komentar Verifikator', 'Evidence & Dokumen'];
            $lastCol    = 'H';
            $colDokumen = 'H'; // kolom tempat link dokumen ditulis di baris tambahan
        } else {
            $header     = ['No', 'Komponen / Sub Komponen / Kriteria', 'Bobot', 'Jawaban (Predikat)', 'Nilai',
                           'Catatan Evaluasi', 'Catatan Operator', 'Komentar Verifikator', 'Evidence & Dokumen'];
            $lastCol    = 'I';
            $colDokumen = 'I';
        }
 
        $judul = $mode === 'operator'
            ? 'LEMBAR EVALUATOR (SELF-ASSESSMENT)'
            : 'LEMBAR VERIFIKATOR ( DIPAKAI DI REKAP)';
 
        $bgHeader = $mode === 'operator' ? '1D4E6B' : '6B1D1D';
 
        $sheet->setCellValue('A1', $judul);
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->setCellValue('A2', strtoupper($namaOpd) . ' — TAHUN ' . $tahun);
        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
 
        $headerRow = 4;
        $sheet->fromArray($header, null, 'A' . $headerRow);
        $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($bgHeader);
        $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
 
        $row = $headerRow + 1;
        $nilaiAkhir = 0;
 
        foreach ($komponenUtama as $k) {
            $sheet->setCellValue('B' . $row, strtoupper($k->nama));
            $sheet->setCellValue('C' . $row, number_format($k->bobot, 2, ',', '.'));
            $sheet->mergeCells('D' . $row . ':' . $lastCol . $row);
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F2E9E9');
            $row++;
 
            $totalKomponen = 0;
 
            foreach ($subKomponen->where('parent_id', $k->id) as $s) {
                $p = $penilaian[$s->id] ?? null;
                $jawaban  = $p->{$fieldJawaban} ?? '-';
                $nilaiSub = $p ? floatval($p->{$fieldNilai} ?? 0) : 0;
                $totalKomponen += $nilaiSub;
 
                $sheet->setCellValue('B' . $row, '  ' . $s->nama);
                $sheet->setCellValue('C' . $row, number_format($s->bobot, 2, ',', '.'));
                $sheet->setCellValue('D' . $row, $jawaban);
                $sheet->setCellValue('E' . $row, number_format($nilaiSub, 2, ',', '.'));
                $sheet->getStyle('D' . $row . ':E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
 
                if ($mode === 'evaluator') {
                    $sheet->setCellValue('F' . $row, $p->catatan ?? '');
                    $sheet->mergeCells('G' . $row . ':' . $lastCol . $row);
                    $sheet->getStyle('F' . $row)->getAlignment()->setWrapText(true);
                } else {
                    $sheet->mergeCells('F' . $row . ':' . $lastCol . $row);
                }
                $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->getFont()->setItalic(true);
                $row++;
 
                foreach (($kriteriaAll[$s->id] ?? []) as $kr) {
                    $pk = $catatanKriteria[$kr->id] ?? null;
 
                    $catOperator   = $pk->catatan_operator ?? '';
                    $komentarVerif = $pk->komentar_admin ?? '';
                    $evidenceRaw   = $pk->daftar_evidence ?? '';
                    $docs          = $dokumenPerKriteria[$kr->id] ?? collect();
 
                    // ⭐ Pisahkan teks evidence: baris yang berupa URL (http/https)
                    // dijadikan link tersendiri, sisanya tetap teks biasa.
                    $evidenceTeksBiasa = [];
                    $evidenceLinks     = [];
                    foreach (preg_split('/\r\n|\r|\n/', trim($evidenceRaw)) as $baris) {
                        $baris = trim($baris);
                        if ($baris === '') continue;
                        if (preg_match('/^https?:\/\/\S+/i', $baris, $m)) {
                            $evidenceLinks[] = $m[0];
                            $sisaTeks = trim(substr($baris, strlen($m[0])));
                            if ($sisaTeks !== '') $evidenceTeksBiasa[] = $sisaTeks;
                        } else {
                            $evidenceTeksBiasa[] = $baris;
                        }
                    }
                    $evidenceText = implode("\n", $evidenceTeksBiasa);
 
                    $sheet->setCellValue('A' . $row, $kr->nomor);
                    $sheet->setCellValue('B' . $row, '      ' . $kr->uraian);
 
                    if ($mode === 'operator') {
                        $sheet->setCellValue('F' . $row, $catOperator);
                        $sheet->setCellValue('G' . $row, $komentarVerif);
                        $sheet->setCellValue('H' . $row, $evidenceText); // teks evidence non-link saja, link & dokumen menyusul di baris bawah
                        $sheet->getStyle('F' . $row . ':H' . $row)->getAlignment()->setWrapText(true);
                    } else {
                        $sheet->setCellValue('G' . $row, $catOperator);
                        $sheet->setCellValue('H' . $row, $komentarVerif);
                        $sheet->setCellValue('I' . $row, $evidenceText);
                        $sheet->getStyle('G' . $row . ':I' . $row)->getAlignment()->setWrapText(true);
                    }
 
                    $sheet->getStyle('A' . $row)->getFont()->setSize(9);
                    $sheet->getStyle('B' . $row . ':' . $lastCol . $row)->getFont()->setSize(9);
                    $sheet->getStyle('B' . $row)->getAlignment()->setWrapText(true);
                    $row++;
 
                    // ⭐ Baris tambahan per URL yang ditulis manual di teks evidence — jadi hyperlink
                    foreach ($evidenceLinks as $link) {
                        $sheet->setCellValue('B' . $row, '        ↳ link evidence');
                        $sheet->getStyle('B' . $row)->getFont()->setSize(9)->setItalic(true);
 
                        $sheet->setCellValue($colDokumen . $row, '🔗 ' . $link);
                        $sheet->getCell($colDokumen . $row)->getHyperlink()->setUrl($link);
                        $sheet->getStyle($colDokumen . $row)->getFont()
                            ->setSize(9)->setUnderline(true)->getColor()->setRGB('2563EB');
 
                        $row++;
                    }
 
                    // ⭐ Baris tambahan per dokumen upload — nama file jadi hyperlink yang bisa diklik
                    foreach ($docs as $doc) {
                        $url = route('evaluasi.lke.lihat.dokumen', $doc->id);
 
                        $sheet->setCellValue('B' . $row, '        ↳ dokumen pendukung');
                        $sheet->getStyle('B' . $row)->getFont()->setSize(9)->setItalic(true);
 
                        $sheet->setCellValue($colDokumen . $row, '📄 ' . $doc->nama_file);
                        $sheet->getCell($colDokumen . $row)->getHyperlink()->setUrl($url);
                        $sheet->getStyle($colDokumen . $row)->getFont()
                            ->setSize(9)->setUnderline(true)->getColor()->setRGB('2563EB');
 
                        $row++;
                    }
                }
            }
 
            $sheet->setCellValue('B' . $row, 'Subtotal ' . $k->nama);
            $sheet->setCellValue('E' . $row, number_format($totalKomponen, 2, ',', '.'));
            $sheet->mergeCells('B' . $row . ':D' . $row);
            $sheet->mergeCells('F' . $row . ':' . $lastCol . $row);
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->getFont()->setBold(true);
            $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FDF6E3');
            $row++;
 
            $nilaiAkhir += $totalKomponen;
        }
 
        $predikat = $this->getPredikat($nilaiAkhir);
        $row++;
        $sheet->setCellValue('B' . $row, $mode === 'operator' ? 'TOTAL NILAI OPERATOR (ASLI)' : 'NILAI AKHIR RESMI (EVALUATOR)');
        $sheet->setCellValue('E' . $row, number_format($nilaiAkhir, 2, ',', '.'));
        $sheet->mergeCells('B' . $row . ':D' . $row);
        $sheet->mergeCells('F' . $row . ':' . $lastCol . $row);
        $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($bgHeader);
        $row++;
        $sheet->setCellValue('B' . $row, 'KATEGORI');
        $sheet->setCellValue('E' . $row, $predikat['kode'] . ' — ' . str_replace($predikat['kode'] . ' — ', '', $predikat['label']));
        $sheet->mergeCells('B' . $row . ':D' . $row);
        $sheet->mergeCells('F' . $row . ':' . $lastCol . $row);
        $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->getFont()->setBold(true);
 
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(48);
        $sheet->getColumnDimension('C')->setWidth(8);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(9);
        $sheet->getColumnDimension('F')->setWidth(26);
        $sheet->getColumnDimension('G')->setWidth(26);
        $sheet->getColumnDimension('H')->setWidth(28);
        if ($mode === 'evaluator') {
            $sheet->getColumnDimension('I')->setWidth(28);
        }
 
        $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $row)
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
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