<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Concerns\PerjanjianHelpers;

class PerjanjianController extends Controller
{
    use PerjanjianHelpers;

    // ════════════════════════════════════════════════════
    //  INDEX REGULER
    // ════════════════════════════════════════════════════
    public function index(Request $request, $jenis)
    {
        $config = $this->jenisConfig();

        if (!isset($config[$jenis])) {
            abort(404, 'Jenis dokumen tidak dikenal.');
        }

        $user      = Session::get('user');
        $isAdmin   = $user['role'] === 'admin';
        $cfg       = $config[$jenis];
        $listTahun = $this->listTahun();

        // ✅ Fix: tahun bisa difilter via request
        $tahun = (int) $request->input('tahun', date('Y'));

        $template = DB::table('perjanjian_kinerja')
            ->where('jenis', $cfg['key'])
            ->where('tahun', $tahun)
            ->whereNotNull('nama_file')
            ->first();

        if ($template && $template->nama_file) {
            $templatePath          = storage_path('app/templates/' . $template->nama_file);
            $template->file_exists = file_exists($templatePath);
        }

        $dokOpd = null;
        if ($user['role'] === 'operator') {
            $dokOpd = DB::table('dokumen_opd')
                ->where('perangkat_daerah_id', $user['daerah_id'])
                ->where('jenis', $cfg['opd'])
                ->where('tahun', $tahun)
                ->first();

            if ($dokOpd && $dokOpd->nama_file) {
                $filePath             = storage_path('app/dokumen_opd/' . $dokOpd->nama_file);
                $dokOpd->file_exists  = file_exists($filePath);
            }
        }

        $listOpd = [];
        if ($user['role'] === 'admin') {
            $listOpd = DB::table('dokumen_opd as d')
                ->leftJoin('perangkat_daerah as pd', 'd.perangkat_daerah_id', '=', 'pd.id')
                ->leftJoin('pengguna as p', 'd.uploaded_by', '=', 'p.id')
                ->select('d.*', 'pd.nama as nama_opd', 'p.nama as nama_user')
                ->where('d.jenis', $cfg['opd'])
                ->where('d.tahun', $tahun)
                ->orderByRaw("FIELD(d.status,'menunggu','ditolak','disetujui')")
                ->orderBy('pd.nama')
                ->get();

            foreach ($listOpd as $dok) {
                $filePath          = storage_path('app/dokumen_opd/' . $dok->nama_file);
                $dok->file_exists  = file_exists($filePath);
            }
        }

        $stats = [
            'total'     => $listOpd ? count($listOpd) : 0,
            'disetujui' => $listOpd ? $listOpd->where('status', 'disetujui')->count() : 0,
            'menunggu'  => $listOpd ? $listOpd->where('status', 'menunggu')->count() : 0,
            'ditolak'   => $listOpd ? $listOpd->where('status', 'ditolak')->count() : 0,
        ];

        return view('perjanjian.index', compact(
            'user', 'isAdmin', 'jenis', 'cfg', 'listTahun',
            'tahun', 'template', 'dokOpd', 'listOpd', 'stats'
        ));
    }
}
