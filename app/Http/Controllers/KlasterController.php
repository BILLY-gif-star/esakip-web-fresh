<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Services\NotifikasiService;

class KlasterController extends Controller
{
    private function tahunEvaluasi(): int
    {
        return (int) date('Y') - 1;
    }

    private function listTahun(): array
    {
        $base = $this->tahunEvaluasi();
        return range($base, $base - 5);
    }

    public function index(Request $request)
    {
        $user  = Session::get('user');
        $type  = $request->input('type', 'utama');
        $level = (int) $request->input('level', 1);
        $tahun = (int) $request->input('tahun', $this->tahunEvaluasi());
        $opdId = $user['role'] === 'operator' ? $user['daerah_id'] : $request->input('opd_id');

        $listOpd   = DB::table('perangkat_daerah')->orderBy('nama')->get();
        $listTahun = $this->listTahun();

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
            'listOpd', 'listTahun',
            'komponenUtama', 'subKomponen', 'kriteriaPerKomponen',
            'nilaiPerSubKomponen', 'nilaiKomponenUtama',
            'nilaiSubKomponen', 'catatanPerKriteria', 'dokumenPerKriteria',
            'nilaiAkhir', 'predikat'
        ));
    }

    public function simpan(Request $request)
    {
        $user  = Session::get('user');
        $tahun = (int) $request->input('tahun', $this->tahunEvaluasi());
        $type  = $request->input('type', 'utama');
        $level = (int) $request->input('level', 1);
        $opdId = $user['role'] === 'operator' ? $user['daerah_id'] : $request->input('opd_id');

        if (!$opdId) return back()->with('error', 'Pilih OPD terlebih dahulu.');

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

        return redirect()->route('klaster.' . $type . '.' . $level, ['tahun' => $tahun, 'opd_id' => $opdId])
            ->with('success', $user['role'] === 'admin'
                ? 'Penilaian Klaster ' . ucfirst($type) . ' Level ' . $level . ' tahun ' . $tahun . ' berhasil disimpan!'
                : 'Catatan berhasil disimpan!');
    }

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
        $mime = ['pdf'=>'application/pdf','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png'];
        return isset($mime[$ext]) ? response()->file($path, ['Content-Type' => $mime[$ext]]) : response()->download($path, $doc->nama_file);
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

    public function rekap(Request $request)
    {
        // Implementasi rekap
        return view('klaster.rekap');
    }

    private function getJawabanDariNilai(float $nilai): string
    {
        if ($nilai >= 90) return 'AA';
        if ($nilai >= 80) return 'A';
        if ($nilai >= 70) return 'BB';
        if ($nilai >= 60) return 'B';
        if ($nilai >= 50) return 'CC';
        if ($nilai >= 30) return 'C';
        if ($nilai > 0) return 'D';
        return 'E';
    }

    private function getPredikat(float $nilai): array
    {
        if ($nilai >= 90) return ['kode'=>'AA','label'=>'Sangat Memuaskan','color'=>'#059669'];
        if ($nilai >= 80) return ['kode'=>'A','label'=>'Memuaskan','color'=>'#2563eb'];
        if ($nilai >= 70) return ['kode'=>'BB','label'=>'Sangat Baik','color'=>'#7c3aed'];
        if ($nilai >= 60) return ['kode'=>'B','label'=>'Baik','color'=>'#d4982e'];
        if ($nilai >= 50) return ['kode'=>'CC','label'=>'Cukup Baik','color'=>'#f59e0b'];
        if ($nilai >= 30) return ['kode'=>'C','label'=>'Kurang','color'=>'#dc2626'];
        if ($nilai > 0) return ['kode'=>'D','label'=>'Sangat Kurang','color'=>'#991b1b'];
        return ['kode'=>'E','label'=>'Tidak Ada Upaya','color'=>'#6b7280'];
    }
}