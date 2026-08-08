<?php

namespace App\Http\Controllers\Concerns;

/**
 * Helper bersama untuk semua controller di bawah "Perjanjian Kinerja"
 * (PerjanjianController, PerjanjianCascadingController,
 *  PerjanjianTemplateController, PerjanjianDokumenController).
 *
 * Dipindah ke trait supaya tidak ada logic yang terduplikasi antar
 * controller setelah PerjanjianController dipecah — isinya SAMA PERSIS
 * seperti sebelumnya, tidak ada perubahan behavior.
 */
trait PerjanjianHelpers
{
    // ════════════════════════════════════════════════════
    //  KONFIGURASI JENIS DOKUMEN
    // ════════════════════════════════════════════════════
    private function jenisConfig(): array
    {
        return [
            'perjanjian-kinerja' => [
                'key'              => 'perjanjian_kinerja',
                'opd'              => 'perjanjian_kinerja_opd',
                'label'            => 'Perjanjian Kinerja',
                'icon'             => '🤝',
                'desc'             => 'Dokumen perjanjian kinerja tahunan',
                'fitur_tahun_lalu' => false,
            ],
            'perjanjian-iku' => [
                'key'              => 'perjanjian_iku',
                'opd'              => 'perjanjian_iku_opd',
                'label'            => 'Perjanjian Kinerja — IKU',
                'icon'             => '🎯',
                'desc'             => 'Indikator Kinerja Utama',
                'fitur_tahun_lalu' => false,
            ],
            'renstra-iku' => [
                'key'              => 'renstra_iku',
                'opd'              => 'renstra_iku_opd',
                'label'            => 'RENSTRA / IKU',
                'icon'             => '📋',
                'desc'             => 'Rencana Strategis dan Indikator Kinerja Utama',
                'fitur_tahun_lalu' => false,
            ],
            'pelaksanaan-anggaran' => [
                'key'              => 'pelaksanaan_anggaran',
                'opd'              => 'pelaksanaan_anggaran_opd',
                'label'            => 'Rencana Aksi',
                'icon'             => '📊',
                'desc'             => 'Dokumen Rencana Aksi',
                'fitur_tahun_lalu' => false,
            ],
            'dpa' => [
                'key'              => 'dpa',
                'opd'              => 'dpa_opd',
                'label'            => 'DPA',
                'icon'             => '📊',
                'desc'             => 'Dokumen Pelaksanaan Anggaran',
                'fitur_tahun_lalu' => false,
            ],
            'cascading-pohon-kinerja' => [
                'key'              => 'cascading_pohon_kinerja',
                'opd'              => 'cascading_pohon_kinerja_opd',
                'label'            => 'Cascading / Pohon Kinerja',
                'icon'             => '🌳',
                'desc'             => 'Cascading dan Pohon Kinerja OPD',
                'fitur_tahun_lalu' => true,
            ],
        ];
    }

    private function listTahun(): array
    {
        $base = (int) date('Y');
        return range($base, $base - 5);
    }

    private function getJenisRoute($jenis)
    {
        $map = [
            'perjanjian_kinerja_opd'      => 'perjanjian-kinerja',
            'perjanjian_iku_opd'          => 'perjanjian-iku',
            'renstra_iku_opd'             => 'renstra-iku',
            'pelaksanaan_anggaran_opd'    => 'pelaksanaan-anggaran',
            'dpa_opd'                     => 'dpa',
            'cascading_pohon_kinerja_opd' => 'cascading-pohon-kinerja',
        ];
        return $map[$jenis] ?? 'perjanjian-kinerja';
    }

    private function getJenisRouteForTemplate($jenis)
    {
        $map = [
            'perjanjian_kinerja'      => 'perjanjian-kinerja',
            'perjanjian_iku'          => 'perjanjian-iku',
            'renstra_iku'             => 'renstra-iku',
            'pelaksanaan_anggaran'    => 'pelaksanaan-anggaran',
            'dpa'                     => 'dpa',
            'cascading_pohon_kinerja' => 'cascading-pohon-kinerja',
        ];
        return $map[$jenis] ?? 'perjanjian-kinerja';
    }

    private function getCfgByKey($key)
    {
        foreach ($this->jenisConfig() as $cfg) {
            if ($cfg['key'] === $key) return $cfg;
        }
        return null;
    }

    private function getCfgByOpd($opdJenis)
    {
        foreach ($this->jenisConfig() as $cfg) {
            if ($cfg['opd'] === $opdJenis) return $cfg;
        }
        return null;
    }

    // ════════════════════════════════════════════════════
    //  HELPER: CARI FILE
    // ════════════════════════════════════════════════════
    private function cariFile($folder, $namaFile, $pathDariDb = null)
    {
        $candidates = [
            storage_path('app/' . $folder . '/' . $namaFile),
            storage_path('app/public/' . $folder . '/' . $namaFile),
            // Coba path dari DB langsung sebagai absolute path
            $pathDariDb && file_exists($pathDariDb) ? $pathDariDb : null,
            // Coba path dari DB sebagai relative dari storage
            $pathDariDb ? storage_path('app/' . $pathDariDb) : null,
            $pathDariDb ? storage_path('app/public/' . $pathDariDb) : null,
            // Coba ambil nama file dari path_file di DB (bisa berbeda dengan nama_file)
            $pathDariDb ? storage_path('app/' . $folder . '/' . basename($pathDariDb)) : null,
        ];

        foreach ($candidates as $p) {
            if ($p && file_exists($p)) return $p;
        }
        return null;
    }
}
