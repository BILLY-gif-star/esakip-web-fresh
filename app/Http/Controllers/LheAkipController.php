<?php

namespace App\Http\Controllers;

use App\Models\LheAkip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LheAkipController extends Controller
{
    /**
     * Daftar seluruh LHE AKIP yang sudah diinput, dengan filter tahun.
     */
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);

        $data = DB::table('lhe_akip')
            ->join('perangkat_daerah', 'lhe_akip.perangkat_daerah_id', '=', 'perangkat_daerah.id')
            ->where('lhe_akip.tahun_evaluasi', $tahun)
            ->select('lhe_akip.*', 'perangkat_daerah.nama as nama_opd')
            ->orderBy('perangkat_daerah.nama')
            ->get();

        // Lengkapi tiap baris dengan nilai live dari lke_penilaian
        $data = $data->map(function ($row) {
            $nilai = LheAkip::hitungNilai($row->perangkat_daerah_id, $row->tahun_evaluasi);
            $row->total_nilai = $nilai['total'];
            $row->kategori    = $nilai['kategori'];
            $row->ada_data_lke = $nilai['ada_data'];
            return $row;
        });

        $daftarTahun = DB::table('lhe_akip')
            ->select('tahun_evaluasi')
            ->distinct()
            ->orderByDesc('tahun_evaluasi')
            ->pluck('tahun_evaluasi');

        if ($daftarTahun->isEmpty()) {
            $daftarTahun = collect([now()->year]);
        }

        return view('lhe-akip.index', compact('data', 'tahun', 'daftarTahun'));
    }

    /**
     * Form input LHE AKIP baru.
     * Admin pilih OPD + tahun dulu → nilai dimuat otomatis dari lke_penilaian.
     */
    public function create()
    {
        $daftarOpd = DB::table('perangkat_daerah')->orderBy('nama')->get();

        $daftarTahun = DB::table('lke_penilaian')
            ->select('tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('lhe-akip.form', [
            'lhe'         => new LheAkip(),
            'daftarOpd'   => $daftarOpd,
            'daftarTahun' => $daftarTahun,
            'nilaiAwal'   => null,
            'mode'        => 'create',
        ]);
    }

    /**
     * Endpoint AJAX — muat preview nilai untuk kombinasi OPD + tahun tertentu.
     */
    public function nilaiPreview(Request $request)
    {
        $request->validate([
            'perangkat_daerah_id' => 'required|integer',
            'tahun'               => 'required|integer',
        ]);

        $nilai = LheAkip::hitungNilai(
            (int) $request->perangkat_daerah_id,
            (int) $request->tahun
        );

        return response()->json($nilai);
    }

    public function store(Request $request)
    {
        $validated = $this->validasi($request);
        $validated = $this->prosesArrayInput($request, $validated);

        $validated['created_by'] = session('user.id');

        LheAkip::create($validated);

        return redirect()
            ->route('lhe-akip.index', ['tahun' => $validated['tahun_evaluasi']])
            ->with('success', 'LHE AKIP berhasil disimpan.');
    }

    public function edit(LheAkip $lhe)
    {
        $daftarOpd = DB::table('perangkat_daerah')->orderBy('nama')->get();

        $daftarTahun = DB::table('lke_penilaian')
            ->select('tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        $nilaiAwal = LheAkip::hitungNilai($lhe->perangkat_daerah_id, $lhe->tahun_evaluasi);

        return view('lhe-akip.form', [
            'lhe'         => $lhe,
            'daftarOpd'   => $daftarOpd,
            'daftarTahun' => $daftarTahun,
            'nilaiAwal'   => $nilaiAwal,
            'mode'        => 'edit',
        ]);
    }

    public function update(Request $request, LheAkip $lhe)
    {
        $validated = $this->validasi($request);
        $validated = $this->prosesArrayInput($request, $validated);

        $validated['updated_by'] = session('user.id');

        $lhe->update($validated);

        return redirect()
            ->route('lhe-akip.index', ['tahun' => $validated['tahun_evaluasi']])
            ->with('success', 'LHE AKIP berhasil diperbarui.');
    }

    public function destroy(LheAkip $lhe)
    {
        $lhe->delete();

        return back()->with('success', 'LHE AKIP berhasil dihapus.');
    }

    /**
     * Halaman cetak/preview — tampilan mirip dokumen surat resmi.
     * Nilai diambil LIVE dari lke_penilaian saat dicetak.
     */
    public function cetak(LheAkip $lhe)
    {
        $opd   = DB::table('perangkat_daerah')->where('id', $lhe->perangkat_daerah_id)->first();
        $nilai = LheAkip::hitungNilai($lhe->perangkat_daerah_id, $lhe->tahun_evaluasi);

        return view('lhe-akip.cetak', compact('lhe', 'opd', 'nilai'));
    }

    // ══════════════════════════════════════════════════════════
    // Helper privat
    // ══════════════════════════════════════════════════════════

    private function validasi(Request $request): array
    {
        return $request->validate([
            'perangkat_daerah_id' => 'required|exists:perangkat_daerah,id',
            'nomor_surat'         => 'required|string|max:255',
            'tanggal_surat'       => 'required|date',
            'tahun_evaluasi'      => 'required|digits:4',
            'nomor_sk_tim'        => 'nullable|string|max:255',
            'periode_mulai'       => 'nullable|date',
            'periode_selesai'     => 'nullable|date|after_or_equal:periode_mulai',

            'uraian_perencanaan'       => 'nullable|string',
            'uraian_pengukuran'        => 'nullable|string',
            'uraian_pelaporan'         => 'nullable|string',
            'uraian_evaluasi_internal' => 'nullable|string',

            'penutup' => 'nullable|string',

            'nama_penandatangan'    => 'nullable|string|max:255',
            'jabatan_penandatangan' => 'nullable|string|max:255',
            'pangkat_penandatangan' => 'nullable|string|max:255',
            'nip_penandatangan'     => 'nullable|string|max:50',

            'status' => 'required|in:draft,final',
        ]);
    }

    /**
     * Poin catatan (textarea per-baris) & rekomendasi → array/JSON.
     */
    private function prosesArrayInput(Request $request, array $data): array
    {
        foreach (['catatan_perencanaan', 'catatan_pengukuran', 'catatan_pelaporan', 'catatan_evaluasi_internal'] as $field) {
            $raw = $request->input($field, '');
            $data[$field] = collect(explode("\n", $raw))
                ->map(fn ($baris) => trim($baris))
                ->filter()
                ->values()
                ->all();
        }

        $labelKomponen = [
            'perencanaan'       => 'Perencanaan Kinerja',
            'pengukuran'        => 'Pengukuran Kinerja',
            'pelaporan'         => 'Pelaporan Kinerja',
            'evaluasi_internal' => 'Evaluasi Akuntabilitas Kinerja Internal',
        ];

        $rekomendasi = [];
        foreach ($labelKomponen as $key => $label) {
            $poin = collect(explode("\n", $request->input('rekomendasi_' . $key, '')))
                ->map(fn ($b) => trim($b))
                ->filter()
                ->values()
                ->all();

            $rekomendasi[] = ['komponen' => $label, 'poin' => $poin];
        }
        $data['rekomendasi'] = $rekomendasi;

        return $data;
    }
}