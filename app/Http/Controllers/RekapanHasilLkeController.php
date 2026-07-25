<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapanLkeExport;
use Barryvdh\DomPDF\Facade\Pdf;

class RekapanHasilLkeController extends Controller
{
    // ════════════════════════════════════════════════════
    //  INDEX — tampil halaman rekapan
    // ════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $user        = Session::get('user');
        $tahun       = (int) $request->input('tahun', date('Y') - 1);
        $dataRekapan = $this->getDataRekapan($tahun);
        $listTahun   = range(date('Y'), date('Y') - 5);
        $listOpd     = DB::table('perangkat_daerah')->orderBy('nama')->get();

        return view('rekapan-hasil', compact('user', 'tahun', 'listTahun', 'dataRekapan', 'listOpd'));
    }

    // ════════════════════════════════════════════════════
    //  DOWNLOAD EXCEL
    // ════════════════════════════════════════════════════
    public function downloadExcel(Request $request)
    {
        $tahun       = (int) $request->input('tahun', date('Y') - 1);
        $dataRekapan = $this->getDataRekapan($tahun);
        $namaFile    = 'Rekapan_LKE_AKIP_' . $tahun . '_' . date('Ymd') . '.xlsx';

        return Excel::download(new RekapanLkeExport($dataRekapan, $tahun), $namaFile);
    }

    // ════════════════════════════════════════════════════
    //  DOWNLOAD PDF
    // ════════════════════════════════════════════════════
    public function downloadPdf(Request $request)
    {
        $tahun       = (int) $request->input('tahun', date('Y') - 1);
        $dataRekapan = $this->getDataRekapan($tahun);
        $listTahun   = range(date('Y'), date('Y') - 5);

        $pdf = Pdf::loadView('rekapan-hasil-pdf', compact('dataRekapan', 'tahun', 'listTahun'))
                   ->setPaper('a4', 'landscape');

        $namaFile = 'Rekapan_LKE_AKIP_' . $tahun . '_' . date('Ymd') . '.pdf';

        return $pdf->download($namaFile);
    }

    // ════════════════════════════════════════════════════
    //  HELPER — ambil & proses data rekapan
    // ════════════════════════════════════════════════════
    private function getDataRekapan(int $tahun): array
    {
        $rekapan = DB::table('lke_penilaian as lp')
            ->join('perangkat_daerah as pd', 'lp.perangkat_daerah_id', '=', 'pd.id')
            ->join('lke_komponen as lk',     'lp.komponen_id',         '=', 'lk.id')
            ->where('lp.tahun', $tahun)
            ->whereNotNull('lk.parent_id')
            ->select(
                'pd.id as opd_id',
                'pd.nama as opd_nama',
                'lk.parent_id as komponen_parent',
                DB::raw('SUM(lp.nilai) as total_nilai')
            )
            ->groupBy('pd.id', 'pd.nama', 'lk.parent_id')
            ->get();

        $opdMap = [];
        foreach ($rekapan as $item) {
            if (!isset($opdMap[$item->opd_id])) {
                $opdMap[$item->opd_id] = [
                    'id'     => $item->opd_id,
                    'nama'   => $item->opd_nama,
                    'nilai1' => 0,
                    'nilai2' => 0,
                    'nilai3' => 0,
                    'nilai4' => 0,
                ];
            }

            if ($item->komponen_parent == 1)      $opdMap[$item->opd_id]['nilai1'] = $item->total_nilai;
            elseif ($item->komponen_parent == 2)  $opdMap[$item->opd_id]['nilai2'] = $item->total_nilai;
            elseif ($item->komponen_parent == 3)  $opdMap[$item->opd_id]['nilai3'] = $item->total_nilai;
            elseif ($item->komponen_parent == 4)  $opdMap[$item->opd_id]['nilai4'] = $item->total_nilai;
        }

        $data = [];
        foreach ($opdMap as $opd) {
            $total  = $opd['nilai1'] + $opd['nilai2'] + $opd['nilai3'] + $opd['nilai4'];
            $data[] = [
                'id'       => $opd['id'],
                'nama'     => $opd['nama'],
                'nilai1'   => $opd['nilai1'],
                'nilai2'   => $opd['nilai2'],
                'nilai3'   => $opd['nilai3'],
                'nilai4'   => $opd['nilai4'],
                'total'    => $total,
                'kualitas' => $total,
                'predikat' => $this->getPredikat($total),
            ];
        }

        usort($data, fn($a, $b) => $b['total'] <=> $a['total']);

        foreach ($data as $i => &$item) {
            $item['peringkat'] = $i + 1;
        }

        return $data;
    }

    // ════════════════════════════════════════════════════
    //  HELPER — predikat dari nilai
    // ════════════════════════════════════════════════════
    private function getPredikat($nilai): array
    {
        if ($nilai >= 90) return ['kode' => 'AA', 'label' => 'Sangat Memuaskan', 'color' => '#059669'];
        if ($nilai >= 80) return ['kode' => 'A',  'label' => 'Memuaskan',        'color' => '#2563eb'];
        if ($nilai >= 70) return ['kode' => 'BB', 'label' => 'Sangat Baik',      'color' => '#7c3aed'];
        if ($nilai >= 60) return ['kode' => 'B',  'label' => 'Baik',             'color' => '#d4982e'];
        if ($nilai >= 50) return ['kode' => 'CC', 'label' => 'Cukup Baik',       'color' => '#f59e0b'];
        if ($nilai >= 30) return ['kode' => 'C',  'label' => 'Kurang',           'color' => '#dc2626'];
        if ($nilai >  0)  return ['kode' => 'D',  'label' => 'Sangat Kurang',    'color' => '#991b1b'];
        return                   ['kode' => 'E',  'label' => 'Tidak Ada Upaya',  'color' => '#6b7280'];
    }
}