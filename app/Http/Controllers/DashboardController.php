<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Services\NotifikasiService;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user  = Session::get('user');
        $tahun = date('Y');

        if ($user['role'] === 'admin') {
            return $this->indexAdmin($user, $tahun);
        }

        if ($user['role'] === 'evaluator') {
            return $this->indexEvaluator($user, $tahun);
        }

        return $this->indexOperator($user, $tahun);
    }

    // ════════════════════════════════════════════════════
    //  ADMIN
    // ════════════════════════════════════════════════════
    private function indexAdmin(array $user, int $tahun)
    {
        // Cache stats selama 5 menit
        $stats = Cache::remember('dashboard_stats_' . $tahun, 300, function() use ($tahun) {
            return [
                'opd'              => DB::table('perangkat_daerah')->count(),
                'operator'         => DB::table('pengguna')->where('role', 'operator')->where('is_active', 1)->count(),
                'evaluator'        => DB::table('pengguna')->where('role', 'evaluator')->where('is_active', 1)->count(),
                'template'         => DB::table('perjanjian_kinerja')->count(),
                'menunggu'         => DB::table('pengguna')->where('status_daftar', 'menunggu')->count(),
                'dokumen_uploaded' => DB::table('dokumen_opd')->where('tahun', $tahun)->count(),
            ];
        });

        // Cache rekap klaster selama 10 menit (jarang berubah)
        $rekapKlaster = Cache::remember('rekap_klaster', 600, function() {
            return DB::table('perangkat_daerah')
                ->select('klaster', DB::raw('COUNT(*) as jumlah'))
                ->whereNotNull('klaster')
                ->groupBy('klaster')
                ->get()
                ->keyBy('klaster');
        });

        $opdDokumenStatus = $this->getOpdDokumenStatus($tahun);

        return view('dashboard-admin', compact('user', 'stats', 'tahun', 'opdDokumenStatus', 'rekapKlaster'));
    }

    // ════════════════════════════════════════════════════
    //  EVALUATOR
    // ════════════════════════════════════════════════════
    private function indexEvaluator(array $user, int $tahun)
    {
        $evaluatorId = $user['id'];
        $tahunPenilaian = $tahun - 1; // Tahun evaluasi LKE (tahun lalu)
        
        // Ambil OPD yang diassign ke evaluator ini
        $assignedOpdIds = DB::table('evaluator_opd')
            ->where('evaluator_id', $evaluatorId)
            ->pluck('perangkat_daerah_id')
            ->toArray();
        
        // Data OPD yang diassign
        $assignedOpd = DB::table('perangkat_daerah')
            ->whereIn('id', $assignedOpdIds)
            ->get();
        
        $assignedOpdCount = count($assignedOpdIds);
        
        // OPD yang sudah dinilai (memiliki nilai LKE)
        $sudahDinilaiIds = DB::table('lke_penilaian')
            ->whereIn('perangkat_daerah_id', $assignedOpdIds)
            ->where('tahun', $tahunPenilaian)
            ->distinct('perangkat_daerah_id')
            ->pluck('perangkat_daerah_id')
            ->toArray();
        
        $sudahDinilaiCount = count($sudahDinilaiIds);
        $belumDinilaiCount = $assignedOpdCount - $sudahDinilaiCount;
        
        // OPD yang belum dinilai
        $belumDinilai = DB::table('perangkat_daerah')
            ->whereIn('id', array_diff($assignedOpdIds, $sudahDinilaiIds))
            ->get();
        
        // Rata-rata nilai dari OPD yang sudah dinilai
        $rataNilai = DB::table('lke_penilaian')
            ->whereIn('perangkat_daerah_id', $assignedOpdIds)
            ->where('tahun', $tahunPenilaian)
            ->avg('nilai') ?? 0;
        
        // Progress penilaian (persentase)
        $progress = $assignedOpdCount > 0 
            ? round(($sudahDinilaiCount / $assignedOpdCount) * 100, 2) 
            : 0;
        
        // Target nilai (misal 80 untuk predikat A)
        $target = 80;
        $selisihTarget = $rataNilai - $target;
        
        // Data untuk view
        $data = [
            'user' => $user,
            'tahun' => $tahun,
            'tahun_penilaian' => $tahunPenilaian,
            'assigned_opd_count' => $assignedOpdCount,
            'assigned_opd' => $assignedOpd,
            'sudah_dinilai_count' => $sudahDinilaiCount,
            'belum_dinilai_count' => $belumDinilaiCount,
            'belum_dinilai' => $belumDinilai,
            'rata_nilai' => $rataNilai,
            'progress' => $progress,
            'target' => $target,
            'selisih_target' => $selisihTarget,
        ];
        
        return view('dashboard-user', $data);
    }

    // ════════════════════════════════════════════════════
    //  OPERATOR
    // ════════════════════════════════════════════════════
    private function indexOperator(array $user, int $tahun)
    {
        $capaian = DB::table('capaian_kinerja')
            ->where('perangkat_daerah_id', $user['daerah_id'])
            ->where('tahun', $tahun)
            ->select('triwulan', DB::raw('COUNT(*) as n'))
            ->groupBy('triwulan')
            ->pluck('n', 'triwulan')
            ->toArray();

        $stats = [
            'tw1' => $capaian[1] ?? 0,
            'tw2' => $capaian[2] ?? 0,
            'tw3' => $capaian[3] ?? 0,
        ];

        // ⭐ AMBIL DATA KLASTER OPD YANG LOGIN
        $opdUser = null;
        if (!empty($user['daerah_id'])) {
            $opdUser = DB::table('perangkat_daerah')
                ->select('klaster', 'tugas', 'nama', 'singkatan')
                ->where('id', $user['daerah_id'])
                ->first();
        }

        // ⭐ AMBIL DATA JUKNIS UNTUK OPERATOR (3 terbaru)
        $juknisList = DB::table('juknis')
            ->where('is_active', 1)
            ->orderBy('urutan', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        return view('dashboard-user', compact('user', 'stats', 'tahun', 'opdUser', 'juknisList'));
    }

    // ════════════════════════════════════════════════════
    //  HELPER — Status dokumen per OPD
    // ════════════════════════════════════════════════════
    private function getOpdDokumenStatus(int $tahun): array
    {
        $opds = DB::table('perangkat_daerah')
            ->orderBy('nama')
            ->get(['id', 'nama']);

        $mapJenis = [
            'Perjanjian Kinerja'        => 'perjanjian_kinerja',
            'Perjanjian Kinerja — IKU'  => 'perjanjian_iku',
            'RENSTRA / IKU'             => 'renstra_iku',
            'Pelaksanaan Anggaran'      => 'pelaksanaan_anggaran_opd',
        ];

        $dokumens = DB::table('dokumen_opd')
            ->where('tahun', $tahun)
            ->get(['perangkat_daerah_id', 'jenis', 'status']);

        $index = [];
        foreach ($dokumens as $d) {
            $key = $mapJenis[$d->jenis] ?? null;
            if (!$key) continue;

            if (($index[$d->perangkat_daerah_id][$key] ?? null) !== 'disetujui') {
                $index[$d->perangkat_daerah_id][$key] = $d->status;
            }
        }

        $periodikIds = DB::table('pengukuran_periodik')
            ->where('tahun', $tahun)
            ->pluck('perangkat_daerah_id')
            ->unique()
            ->toArray();

        $result = [];
        foreach ($opds as $opd) {
            $dok = $index[$opd->id] ?? [];

            $result[] = [
                'nama'                 => $opd->nama,
                'perjanjian_kinerja'   => $dok['perjanjian_kinerja'] ?? null,
                'perjanjian_iku'       => $dok['perjanjian_iku'] ?? null,
                'renstra'              => $dok['renstra_iku'] ?? null,
                'pelaksanaan_anggaran' => $dok['pelaksanaan_anggaran_opd'] ?? null,
                'pengukuran_periodik'  => in_array($opd->id, $periodikIds) ? 'disetujui' : null,
            ];
        }

        return $result;
    }
}