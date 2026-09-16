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
     * Hitung nilai per komponen LIVE dari tabel lke_penilaian
     * yang sudah ada & sudah dinilai lewat menu Evaluasi Kinerja.
     *
     * Struktur lke_komponen: 4 komponen induk (parent_id NULL,
     * id 1/2/3/4) masing-masing punya sub-kriteria (parent_id = id induk).
     * lke_penilaian menyimpan nilai di level sub-kriteria (anak),
     * jadi nilai komponen induk = SUM nilai semua anaknya.
     * ══════════════════════════════════════════════════════════
     */
    public static function hitungNilai(int $perangkatDaerahId, int $tahun): array
    {
        // Ambil 4 komponen induk + bobotnya
        $komponenInduk = DB::table('lke_komponen')
            ->whereNull('parent_id')
            ->orderBy('id')
            ->get();

        // Total nilai per komponen induk = SUM nilai sub-kriteria anaknya
        $nilaiPerInduk = DB::table('lke_penilaian')
            ->join('lke_komponen', 'lke_penilaian.komponen_id', '=', 'lke_komponen.id')
            ->where('lke_penilaian.perangkat_daerah_id', $perangkatDaerahId)
            ->where('lke_penilaian.tahun', $tahun)
            ->whereNotNull('lke_komponen.parent_id')
            ->select('lke_komponen.parent_id', DB::raw('SUM(lke_penilaian.nilai) as total_nilai'))
            ->groupBy('lke_komponen.parent_id')
            ->pluck('total_nilai', 'lke_komponen.parent_id');

        $rincian = [];
        $totalNilai = 0;

        foreach ($komponenInduk as $k) {
            $nilai = round((float) ($nilaiPerInduk[$k->id] ?? 0), 2);
            $rincian[] = [
                'id'     => $k->id,
                'kode'   => $k->kode,
                'nama'   => $k->nama,
                'bobot'  => (float) $k->bobot,
                'nilai'  => $nilai,
            ];
            $totalNilai += $nilai;
        }

        $totalNilai = round($totalNilai, 2);

        return [
            'rincian'    => $rincian, // urutan tetap: [0]=Perencanaan [1]=Pengukuran [2]=Pelaporan [3]=Eval.Internal
            'total'      => $totalNilai,
            'kategori'   => self::tentukanKategori($totalNilai),
            'ada_data'   => DB::table('lke_penilaian')
                                ->where('perangkat_daerah_id', $perangkatDaerahId)
                                ->where('tahun', $tahun)
                                ->exists(),
        ];
    }

    public static function tentukanKategori(float $nilai): string
    {
        return match (true) {
            $nilai > 90 => 'AA',
            $nilai > 80 => 'A',
            $nilai > 70 => 'BB',
            $nilai > 60 => 'B',
            $nilai > 50 => 'CC',
            $nilai > 30 => 'C',
            default     => 'D',
        };
    }

    /**
     * Deskripsi kategori standar KemenPAN-RB — dipakai di cetakan surat
     * (mis. "BB (SANGAT BAIK)").
     */
    public static function deskripsiKategori(string $kategori): string
    {
        return match ($kategori) {
            'AA' => 'SANGAT MEMUASKAN',
            'A'  => 'MEMUASKAN',
            'BB' => 'SANGAT BAIK',
            'B'  => 'BAIK',
            'CC' => 'CUKUP (MEMADAI)',
            'C'  => 'KURANG',
            default => 'SANGAT KURANG',
        };
    }

    public static function warnaKategori(string $kategori): string
    {
        return match ($kategori) {
            'AA', 'A' => 'success',
            'BB', 'B' => 'info',
            'CC'      => 'warning',
            default   => 'danger',
        };
    }
}