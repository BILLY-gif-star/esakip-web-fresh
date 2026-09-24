<?php

namespace App\Http\Controllers;

use App\Models\LheAkip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LheAkipController extends Controller
{
   public function index(Request $request)
{
    $daftarTahun = DB::table('lhe_akip')
        ->select('tahun_evaluasi')
        ->distinct()
        ->orderByDesc('tahun_evaluasi')
        ->pluck('tahun_evaluasi');

    if ($daftarTahun->isEmpty()) {
        $daftarTahun = collect([now()->year]);
    }

    // ⭐ Default: tahun dari request, atau tahun terbaru yang punya data,
    //    bukan now()->year — supaya tidak "ketutup" filter tahun kosong
    $tahun = (int) $request->get('tahun', $daftarTahun->first());

    $data = DB::table('lhe_akip')
        ->join('perangkat_daerah', 'lhe_akip.perangkat_daerah_id', '=', 'perangkat_daerah.id')
        ->where('lhe_akip.tahun_evaluasi', $tahun)
        ->select('lhe_akip.*', 'perangkat_daerah.nama as nama_opd')
        ->orderBy('perangkat_daerah.nama')
        ->get();

    // Lengkapi tiap baris dengan nilai live dari lke_penilaian
    $data = $data->map(function ($row) {
        $nilai = LheAkip::hitungNilai($row->perangkat_daerah_id, $row->tahun_evaluasi);
        $row->total_nilai  = $nilai['total'];
        $row->kategori     = $nilai['kategori'];
        $row->ada_data_lke = $nilai['ada_data'];
        return $row;
    });

    return view('lhe-akip.index', compact('data', 'tahun', 'daftarTahun'));
}
    /**
     * Form input LHE AKIP baru.
     * Admin pilih OPD + tahun dulu → nilai & catatan dimuat otomatis.
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
     * Endpoint AJAX — muat preview nilai + catatan untuk kombinasi OPD + tahun tertentu.
     * Dipanggil dari form.blade.php saat klik "Muat Nilai dari Hasil Evaluasi".
     */
    public function nilaiPreview(Request $request)
    {
        $request->validate([
            'perangkat_daerah_id' => 'required|integer',
            'tahun'               => 'required|integer',
        ]);

        $opdId = (int) $request->perangkat_daerah_id;
        $tahun = (int) $request->tahun;

        $nilai = LheAkip::hitungNilai($opdId, $tahun);
        $nilai['catatan'] = LheAkip::hitungCatatan($opdId, $tahun); // array 0..3, selaras $nilai['rincian']

        return response()->json($nilai);
    }

    public function store(Request $request)
    {
        $validated = $this->validasi($request);
        $validated = $this->prosesArrayInput($request, $validated);
        $validated['catatan_terpilih'] = $this->prosesCatatanTerpilih($request); // ⭐ BARU

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

        $nilai = LheAkip::hitungNilai($lhe->perangkat_daerah_id, $lhe->tahun_evaluasi);
        $nilai['catatan'] = LheAkip::hitungCatatan($lhe->perangkat_daerah_id, $lhe->tahun_evaluasi);

        return view('lhe-akip.form', [
            'lhe'         => $lhe,
            'daftarOpd'   => $daftarOpd,
            'daftarTahun' => $daftarTahun,
            'nilaiAwal'   => $nilai,
            'mode'        => 'edit',
        ]);
    }

       public function update(Request $request, LheAkip $lhe)
    {
        $validated = $this->validasi($request);
        $validated = $this->prosesArrayInput($request, $validated);
        $validated['catatan_terpilih'] = $this->prosesCatatanTerpilih($request); // ⭐ BARU

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
     * Nilai (dari lke_penilaian) & catatan (dari komentar Evaluator
     * di lke_penilaian_kriteria) diambil LIVE saat dicetak.
     */
     public function cetak(LheAkip $lhe)
    {
        $opd   = DB::table('perangkat_daerah')->where('id', $lhe->perangkat_daerah_id)->first();
        $nilai = LheAkip::hitungNilai($lhe->perangkat_daerah_id, $lhe->tahun_evaluasi);
        $nilai['catatan'] = $lhe->catatanTerpilihUntukCetak(); // ⭐ DIUBAH

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

            'catatan_pilih' => 'nullable|array',

            'penutup' => 'nullable|string',

            'nama_penandatangan'    => 'nullable|string|max:255',
            'jabatan_penandatangan' => 'nullable|string|max:255',
            'pangkat_penandatangan' => 'nullable|string|max:255',
            'nip_penandatangan'     => 'nullable|string|max:50',

            'status' => 'required|in:draft,final',
        ]);
    }

     private function prosesCatatanTerpilih(Request $request): array
    {
        $raw = $request->input('catatan_pilih', []);

        $hasil = [];
        foreach (range(0, 3) as $idx) {
            $hasil[$idx] = array_map('intval', (array) ($raw[$idx] ?? []));
        }
        return $hasil;
    }


    /**
     * Poin rekomendasi (textarea per-baris) → array/JSON.
     * CATATAN: "catatan_*" TIDAK lagi diproses dari input form —
     * sekarang diambil otomatis dari komentar Evaluator
     * (lihat LheAkip::hitungCatatan(), dipakai di cetak()/nilaiPreview()).
     * Kolom catatan_* di tabel lhe_akip dibiarkan ada tapi tidak lagi dipakai.
     */
    private function prosesArrayInput(Request $request, array $data): array
    {
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