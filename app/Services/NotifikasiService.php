<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class NotifikasiService
{
    /**
     * Kirim notifikasi ke semua admin.
     */
    public static function kirimKeAdmin(
        string $judul,
        string $pesan,
        string $tipe,
        string $ikon = '📄',
        string $warna = 'indigo',
        ?string $url = null,
        ?int $referensiId = null,
        ?string $namaOpd = null
    ): void {
        $adminIds = DB::table('pengguna')
            ->where('role', 'admin')
            ->pluck('id');

        $data = [];
        $now  = now();

        foreach ($adminIds as $adminId) {
            $data[] = [
                'user_id'      => $adminId,
                'judul'        => $judul,
                'pesan'        => $pesan,
                'tipe'         => $tipe,
                'ikon'         => $ikon,
                'warna'        => $warna,
                'url'          => $url,
                'referensi_id' => $referensiId,
                'nama_opd'     => $namaOpd,
                'dibaca_at'    => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }

        if (!empty($data)) {
            DB::table('notifikasi')->insert($data);
        }
    }

    /**
     * ⭐ BARU: Kirim notifikasi ke semua operator aktif.
     */
    public static function kirimKeSemuaOperator(
        string $judul,
        string $pesan,
        string $tipe,
        string $ikon = '📄',
        string $warna = 'indigo',
        ?string $url = null,
        ?int $referensiId = null
    ): void {
        $operatorIds = DB::table('pengguna')
            ->where('role', 'operator')
            ->where('is_active', 1)
            ->pluck('id');

        $data = [];
        $now  = now();

        foreach ($operatorIds as $operatorId) {
            $data[] = [
                'user_id'      => $operatorId,
                'judul'        => $judul,
                'pesan'        => $pesan,
                'tipe'         => $tipe,
                'ikon'         => $ikon,
                'warna'        => $warna,
                'url'          => $url,
                'referensi_id' => $referensiId,
                'nama_opd'     => null,
                'dibaca_at'    => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }

        if (!empty($data)) {
            DB::table('notifikasi')->insert($data);
        }
    }

    /**
     * ⭐ BARU: Kirim notifikasi ke satu user tertentu (by user_id).
     */
    public static function kirimKeUser(
        int $userId,
        string $judul,
        string $pesan,
        string $tipe,
        string $ikon = '📄',
        string $warna = 'indigo',
        ?string $url = null,
        ?int $referensiId = null,
        ?string $namaOpd = null
    ): void {
        DB::table('notifikasi')->insert([
            'user_id'      => $userId,
            'judul'        => $judul,
            'pesan'        => $pesan,
            'tipe'         => $tipe,
            'ikon'         => $ikon,
            'warna'        => $warna,
            'url'          => $url,
            'referensi_id' => $referensiId,
            'nama_opd'     => $namaOpd,
            'dibaca_at'    => null,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    // ── Shortcut per tipe ─────────────────────────────────────

    public static function dokumenDiupload(
        string $namaOpd,
        string $jenisDokumen,
        int $dokumenId,
        string $url = '#'
    ): void {
        self::kirimKeAdmin(
            judul:       'Dokumen Baru Diunggah',
            pesan:       "{$namaOpd} mengunggah dokumen {$jenisDokumen}.",
            tipe:        'dokumen_upload',
            ikon:        '📄',
            warna:       'indigo',
            url:         $url,
            referensiId: $dokumenId,
            namaOpd:     $namaOpd,
        );
    }

    public static function pengukuranDiinput(
        string $namaOpd,
        string $periode,
        int $pengukuranId,
        string $url = '#'
    ): void {
        self::kirimKeAdmin(
            judul:       'Pengukuran Kinerja Diinput',
            pesan:       "{$namaOpd} menginput capaian kinerja {$periode}.",
            tipe:        'pengukuran_input',
            ikon:        '📈',
            warna:       'green',
            url:         $url,
            referensiId: $pengukuranId,
            namaOpd:     $namaOpd,
        );
    }

    public static function lkeDisubmit(
        string $namaOpd,
        int $lkeId,
        string $url = '#'
    ): void {
        self::kirimKeAdmin(
            judul:       'LKE AKIP Disubmit',
            pesan:       "{$namaOpd} telah mengirimkan LKE AKIP untuk dievaluasi.",
            tipe:        'lke_submit',
            ikon:        '📊',
            warna:       'amber',
            url:         $url,
            referensiId: $lkeId,
            namaOpd:     $namaOpd,
        );
    }

    /**
     * ⭐ BARU: Template baru diupload oleh admin → notif ke semua operator.
     */
    public static function templateDiupload(
        string $jenisLabel,
        int $tahun,
        int $documentId,
        string $url = '#'
    ): void {
        self::kirimKeSemuaOperator(
            judul:       "Template {$jenisLabel} Tersedia",
            pesan:       "Admin mengupload template {$jenisLabel} tahun {$tahun}. Silakan unduh template terbaru.",
            tipe:        'template_upload',
            ikon:        '📄',
            warna:       'indigo',
            url:         $url,
            referensiId: $documentId,
        );
    }
} 