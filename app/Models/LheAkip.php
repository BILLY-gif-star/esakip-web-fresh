<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LheAkip extends Model
{
    protected $table = 'lhe_akip';

    protected $fillable = [
        'perangkat_daerah_id',
        'nomor_surat',
        'tanggal_surat',
        'tahun_evaluasi',
        'nomor_sk_tim',
        'periode_mulai',
        'periode_selesai',
        'uraian_perencanaan',
        'uraian_pengukuran',
        'uraian_pelaporan',
        'uraian_evaluasi_internal',
        'catatan_perencanaan',
        'catatan_pengukuran',
        'catatan_pelaporan',
        'catatan_evaluasi_internal',
        'rekomendasi',
        'penutup',
        'nama_penandatangan',
        'jabatan_penandatangan',
        'pangkat_penandatangan',
        'nip_penandatangan',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_surat'    => 'date',
        'periode_mulai'    => 'date',
        'periode_selesai'  => 'date',
        'catatan_perencanaan'       => 'array',
        'catatan_pengukuran'        => 'array',
        'catatan_pelaporan'         => 'array',
        'catatan_evaluasi_internal' => 'array',
        'rekomendasi'               => 'array',
    ];

    /**
     * Relasi ke OPD asli. Pakai query manual (bukan Eloquent model PerangkatDaerah)
     * supaya tidak bentrok kalau Anda sudah/belum punya Model itu.
     */
    public function opd()
    {
        return DB::table('perangkat_daerah')->where('id', $this->perangkat_daerah_id)->first();
    }

    /**
     * ══════════════════════════════════════════════════════════
     * Hitung nilai per komponen LIVE dari tabel lke_penilaian.
     *
     * PENTING: logika ini SENGAJA disamakan persis dengan
     * LkeController::rekap() — supaya nilai yang tampil di surat
     * LHE selalu identik dengan yang tampil di halaman Rekap Nilai
     * LKE AKIP. Kalau nanti logika rekap() diubah, method ini
     * harus ikut disesuaikan.
     *
     * Sumber nilai: kolom `lke_penilaian.nilai` (nilai RESMI dari
     * Evaluator, bukan nilai_operator). Nilai per sub-komponen
     * dijumlah ke komponen induknya (parent_id), lalu totalnya
     * dinormalisasi: (total / total_bobot_komponen_induk) * 100
     * — persis seperti $nilaiAkhirT1/$nilaiAkhirT2 di rekap().
     * ══════════════════════════════════════════════════════════
     */
    public static function hitungNilai(int $perangkatDaerahId, int $tahun): array
    {
        // Komponen induk (parent_id NULL), urut sesuai `urutan` — sama seperti rekap()
        $komponenUtama = DB::table('lke_komponen')
            ->whereNull('parent_id')
            ->orderBy('urutan')
            ->get();

        // Semua sub-komponen, dikelompokkan per induk — sama seperti rekap()
        $subKomponen = DB::table('lke_komponen')
            ->whereNotNull('parent_id')
            ->get()
            ->groupBy('parent_id');

        // Nilai resmi (kolom `nilai`) untuk OPD + tahun ini, keyBy komponen_id — sama seperti rekap()
        $penilaian = DB::table('lke_penilaian')
            ->where('perangkat_daerah_id', $perangkatDaerahId)
            ->where('tahun', $tahun)
            ->get()
            ->keyBy('komponen_id');

        $rincian       = [];
        $totalRaw      = 0;
        $totalBobotMax = 0;

        foreach ($komponenUtama as $k) {
            $subs      = $subKomponen[$k->id] ?? collect();
            $nilaiKomp = 0;
            $subRincian = [];

            foreach ($subs as $s) {
                $nilaiSub = floatval($penilaian[$s->id]->nilai ?? 0);
                $nilaiKomp += $nilaiSub;

                $subRincian[] = [
                    'id'    => $s->id,
                    'nama'  => $s->nama,
                    'bobot' => (float) $s->bobot,
                    'nilai' => round($nilaiSub, 2),
                ];
            }

            $rincian[] = [
                'id'    => $k->id,
                'kode'  => $k->kode ?? null,
                'nama'  => $k->nama,
                'bobot' => (float) $k->bobot,
                'nilai' => round($nilaiKomp, 2), // nilai mentah per komponen — sama seperti kolom di tabel Rekap
                'sub'   => $subRincian,          // rincian sub-komponen di bawahnya
            ];

            $totalRaw      += $nilaiKomp;
            $totalBobotMax += floatval($k->bobot ?? 0);
        }

        // Normalisasi total — persis seperti rekap(): (total / total_bobot) * 100
        $totalNilai = $totalBobotMax > 0 ? round(($totalRaw / $totalBobotMax) * 100, 2) : 0.0;

        return [
            'rincian'    => $rincian, // urutan tetap: [0]=Perencanaan [1]=Pengukuran [2]=Pelaporan [3]=Eval.Internal
            'total'      => $totalNilai,
            'kategori'   => self::tentukanKategori($totalNilai),
            'ada_data'   => $penilaian->isNotEmpty(),
        ];
    }

    /**
     * ══════════════════════════════════════════════════════════
     * Ambil poin catatan perbaikan OTOMATIS dari komentar Evaluator
     * (lke_penilaian_kriteria.komentar_admin) — sumber yang SAMA
     * dengan panel "Komentar Admin" di halaman Rekap Nilai LKE AKIP.
     *
     * lke_kriteria.komponen_id merujuk ke SUB-komponen (bukan induk),
     * jadi harus naik satu level lagi lewat sub.parent_id untuk tahu
     * komentar itu milik komponen induk yang mana.
     *
     * Return: array 0..3 (selaras urutan hitungNilai()['rincian']),
     * masing-masing berisi list string poin catatan.
     * ══════════════════════════════════════════════════════════
     */
    public static function hitungCatatan(int $perangkatDaerahId, int $tahun): array
    {
        $komponenUtama = DB::table('lke_komponen')
            ->whereNull('parent_id')
            ->orderBy('urutan')
            ->get();

        $rows = DB::table('lke_penilaian_kriteria as lpc')
            ->join('lke_kriteria as lk', 'lpc.kriteria_id', '=', 'lk.id')
            ->join('lke_komponen as sub', 'lk.komponen_id', '=', 'sub.id')
            ->where('lpc.perangkat_daerah_id', $perangkatDaerahId)
            ->where('lpc.tahun', $tahun)
            ->whereNotNull('lpc.komentar_admin')
            ->where('lpc.komentar_admin', '!=', '')
            ->orderBy('lk.nomor')
            ->select('lpc.komentar_admin', 'lk.nomor', 'sub.parent_id as induk_id')
            ->get()
            ->groupBy('induk_id');

        $hasil = [];
        foreach ($komponenUtama as $idx => $k) {
            $poin = [];
            foreach (($rows[$k->id] ?? []) as $r) {
                $poin[] = 'Kriteria ' . $r->nomor . ': ' . $r->komentar_admin;
            }
            $hasil[$idx] = $poin;
        }

        return $hasil;
    }

    /**
     * ══════════════════════════════════════════════════════════
     * Ambil catatan perbaikan per komponen LIVE dari komentar
     * Evaluator (lke_penilaian_kriteria.komentar_admin) — sumber
     * yang sama dengan panel "Komentar Evaluator" di halaman Rekap.
     *
     * Dikelompokkan ke komponen INDUK (bukan sub-komponen), supaya
     * cocok dengan struktur "Evaluasi atas [Komponen]" di surat LHE.
     * Key hasil = id komponen induk (sama seperti 'id' di rincian
     * hasil hitungNilai()).
     * ══════════════════════════════════════════════════════════
     */
    public static function ambilCatatan(int $perangkatDaerahId, int $tahun): array
    {
        $rows = DB::table('lke_penilaian_kriteria as lpc')
            ->join('lke_kriteria as lk', 'lpc.kriteria_id', '=', 'lk.id')
            ->join('lke_komponen as kp', 'lk.komponen_id', '=', 'kp.id') // kp = sub-komponen
            ->where('lpc.perangkat_daerah_id', $perangkatDaerahId)
            ->where('lpc.tahun', $tahun)
            ->whereNotNull('lpc.komentar_admin')
            ->where('lpc.komentar_admin', '!=', '')
            ->orderBy('lk.nomor')
            ->select('lpc.komentar_admin', 'kp.parent_id')
            ->get();

        $hasil = [];
        foreach ($rows as $r) {
            $hasil[$r->parent_id][] = $r->komentar_admin;
        }

        return $hasil; // [komponen_induk_id => [komentar1, komentar2, ...]]
    }

    /**
     * Kategori/predikat — ambang batas disamakan persis dengan
     * LkeController::getPredikat() (termasuk kategori "E" untuk nilai 0).
     */
    public static function tentukanKategori(float $nilai): string
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

    /**
     * Deskripsi kategori standar KemenPAN-RB — dipakai di cetakan surat
     * (mis. "BB (SANGAT BAIK)"). Disamakan dengan label di getPredikat().
     */
    public static function deskripsiKategori(string $kategori): string
    {
        return match ($kategori) {
            'AA' => 'SANGAT MEMUASKAN',
            'A'  => 'MEMUASKAN',
            'BB' => 'SANGAT BAIK',
            'B'  => 'BAIK',
            'CC' => 'CUKUP BAIK',
            'C'  => 'KURANG',
            'D'  => 'SANGAT KURANG',
            default => 'TIDAK ADA UPAYA',
        };
    }

    public static function warnaKategori(string $kategori): string
    {
        return match ($kategori) {
            'AA', 'A' => 'success',
            'BB', 'B' => 'info',
            'CC'      => 'warning',
            default   => 'danger', // C, D, E
        };
    }
}