<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class PerjanjianController extends Controller
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

    // ════════════════════════════════════════════════════
    //  CASCADING / POHON KINERJA
    // ════════════════════════════════════════════════════
    public function cascadingIndex(Request $request)
    {
        $user    = Session::get('user');
        $isAdmin = $user['role'] === 'admin';

        $tahun           = (int) $request->input('tahun', date('Y'));
        $tahunSebelumnya = $tahun - 1;
        $listTahun       = $this->listTahun();

        if ($isAdmin) {
            $opdId   = $request->input('opd_id');
            $listOpd = DB::table('perangkat_daerah')->orderBy('nama')->get();
        } else {
            $opdId   = $user['daerah_id'];
            $listOpd = collect();
        }

        $cascadingData             = null;
        $pohonKinerjaData          = null;
        $cascadingDataSebelumnya   = null;
        $pohonKinerjaDataSebelumnya = null;

        if ($opdId) {
            $cascadingData = DB::table('dokumen_opd')
                ->where('perangkat_daerah_id', $opdId)
                ->where('jenis', 'cascading_pohon_kinerja_opd')
                ->where('sub_jenis', 'cascading')
                ->where('tahun', $tahun)
                ->first();

            $pohonKinerjaData = DB::table('dokumen_opd')
                ->where('perangkat_daerah_id', $opdId)
                ->where('jenis', 'cascading_pohon_kinerja_opd')
                ->where('sub_jenis', 'pohon_kinerja')
                ->where('tahun', $tahun)
                ->first();

            $cascadingDataSebelumnya = DB::table('dokumen_opd')
                ->where('perangkat_daerah_id', $opdId)
                ->where('jenis', 'cascading_pohon_kinerja_opd')
                ->where('sub_jenis', 'cascading')
                ->where('tahun', $tahunSebelumnya)
                ->first();

            $pohonKinerjaDataSebelumnya = DB::table('dokumen_opd')
                ->where('perangkat_daerah_id', $opdId)
                ->where('jenis', 'cascading_pohon_kinerja_opd')
                ->where('sub_jenis', 'pohon_kinerja')
                ->where('tahun', $tahunSebelumnya)
                ->first();
        }

        $listOpdData = [];
        if ($isAdmin) {
            $listOpdData = DB::table('dokumen_opd as d')
                ->leftJoin('perangkat_daerah as pd', 'd.perangkat_daerah_id', '=', 'pd.id')
                ->leftJoin('pengguna as p', 'd.uploaded_by', '=', 'p.id')
                ->select('d.*', 'pd.nama as nama_opd', 'p.nama as nama_user')
                ->where('d.jenis', 'cascading_pohon_kinerja_opd')
                ->where('d.tahun', $tahun)
                ->orderBy('pd.nama')
                ->get()
                ->groupBy('perangkat_daerah_id');
        }

        return view('perjanjian.cascading', compact(
            'user', 'isAdmin', 'tahun', 'tahunSebelumnya', 'listTahun',
            'opdId', 'listOpd', 'cascadingData', 'pohonKinerjaData',
            'cascadingDataSebelumnya', 'pohonKinerjaDataSebelumnya', 'listOpdData'
        ));
    }

    // ════════════════════════════════════════════════════
    //  UPLOAD CASCADING (2 file sekaligus)
    // ════════════════════════════════════════════════════
    public function uploadCascading(Request $request)
    {
        $user = Session::get('user');

        if (!$user || $user['role'] !== 'operator') {
            return back()->with('error', 'Hanya operator yang dapat mengupload dokumen.');
        }

        $request->validate([
            'tahun'              => 'required|integer',
            'file_cascading'     => 'required|file|mimes:pdf,docx,xlsx,doc,xls|max:20480',
            'file_pohon_kinerja' => 'required|file|mimes:pdf,docx,xlsx,doc,xls|max:20480',
            'keterangan'         => 'nullable|string|max:500',
        ]);

        $opdId  = $user['daerah_id'];
        $tahun  = $request->tahun;
        $folder = storage_path('app/dokumen_opd');

        if (!file_exists($folder)) mkdir($folder, 0777, true);

        // ── Cascading ──────────────────────────────────
        $fileCasc  = $request->file('file_cascading');
        $ext1      = $fileCasc->getClientOriginalExtension();
        // ✅ Tambah time() agar nama unik saat revisi
        $fnameCasc = 'cascading_' . $opdId . '_' . $tahun . '_' . time() . '.' . $ext1;

        $existingCasc = DB::table('dokumen_opd')
            ->where('perangkat_daerah_id', $opdId)
            ->where('jenis', 'cascading_pohon_kinerja_opd')
            ->where('sub_jenis', 'cascading')
            ->where('tahun', $tahun)
            ->first();

        // ✅ Hapus file lama SEBELUM move file baru
        if ($existingCasc) {
            $oldPath = storage_path('app/dokumen_opd/' . $existingCasc->nama_file);
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        $fileCasc->move($folder, $fnameCasc);

        if ($existingCasc) {
            DB::table('dokumen_opd')->where('id', $existingCasc->id)->update([
                'nama_file'   => $fnameCasc,
                'path_file'   => 'dokumen_opd/' . $fnameCasc,
                'keterangan'  => $request->keterangan ?? '',
                'uploaded_by' => $user['id'],
                'status'      => 'menunggu',
                'updated_at'  => now(),
            ]);
        } else {
            DB::table('dokumen_opd')->insert([
                'perangkat_daerah_id' => $opdId,
                'jenis'       => 'cascading_pohon_kinerja_opd',
                'sub_jenis'   => 'cascading',
                'tahun'       => $tahun,
                'nama_file'   => $fnameCasc,
                'path_file'   => 'dokumen_opd/' . $fnameCasc,
                'keterangan'  => $request->keterangan ?? '',
                'uploaded_by' => $user['id'],
                'status'      => 'menunggu',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // ── Pohon Kinerja ──────────────────────────────
        $filePohon  = $request->file('file_pohon_kinerja');
        $ext2       = $filePohon->getClientOriginalExtension();
        // ✅ Tambah time() agar nama unik saat revisi
        $fnamePohon = 'pohon_kinerja_' . $opdId . '_' . $tahun . '_' . time() . '.' . $ext2;

        $existingPohon = DB::table('dokumen_opd')
            ->where('perangkat_daerah_id', $opdId)
            ->where('jenis', 'cascading_pohon_kinerja_opd')
            ->where('sub_jenis', 'pohon_kinerja')
            ->where('tahun', $tahun)
            ->first();

        // ✅ Hapus file lama SEBELUM move file baru
        if ($existingPohon) {
            $oldPath = storage_path('app/dokumen_opd/' . $existingPohon->nama_file);
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        $filePohon->move($folder, $fnamePohon);

        if ($existingPohon) {
            DB::table('dokumen_opd')->where('id', $existingPohon->id)->update([
                'nama_file'   => $fnamePohon,
                'path_file'   => 'dokumen_opd/' . $fnamePohon,
                'keterangan'  => $request->keterangan ?? '',
                'uploaded_by' => $user['id'],
                'status'      => 'menunggu',
                'updated_at'  => now(),
            ]);
        } else {
            DB::table('dokumen_opd')->insert([
                'perangkat_daerah_id' => $opdId,
                'jenis'       => 'cascading_pohon_kinerja_opd',
                'sub_jenis'   => 'pohon_kinerja',
                'tahun'       => $tahun,
                'nama_file'   => $fnamePohon,
                'path_file'   => 'dokumen_opd/' . $fnamePohon,
                'keterangan'  => $request->keterangan ?? '',
                'uploaded_by' => $user['id'],
                'status'      => 'menunggu',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // Notifikasi ke admin
        $opd     = DB::table('perangkat_daerah')->where('id', $opdId)->first();
        $namaOpd = $opd->nama ?? 'OPD';

        foreach (DB::table('pengguna')->where('role', 'admin')->get() as $admin) {
            DB::table('notifikasi')->insert([
                'user_id'    => $admin->id,
                'judul'      => 'Upload Cascading & Pohon Kinerja',
                'pesan'      => "Operator {$namaOpd} mengupload dokumen Cascading dan Pohon Kinerja untuk tahun {$tahun}. Status: menunggu persetujuan.",
                'tipe'       => 'dokumen_upload',
                'ikon'       => '📎',
                'warna'      => 'amber',
                'url'        => route('perjanjian.cascading'),
                'nama_opd'   => $namaOpd,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('perjanjian.cascading', ['tahun' => $tahun, 'opd_id' => $opdId])
            ->with('success', 'Dokumen Cascading dan Pohon Kinerja berhasil diupload!');
    }

    // ════════════════════════════════════════════════════
    //  REVIEW CASCADING (admin)
    // ════════════════════════════════════════════════════
    public function reviewCascading(Request $request, $id)
    {
        $user = Session::get('user');
        if ($user['role'] !== 'admin') {
            return back()->with('error', 'Hanya admin yang dapat mereview dokumen.');
        }

        $request->validate([
            'status'  => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string|max:500',
        ]);

        $dok = DB::table('dokumen_opd')->find($id);
        if (!$dok) return back()->with('error', 'Dokumen tidak ditemukan.');

        DB::table('dokumen_opd')->where('id', $id)->update([
            'status'        => $request->status,
            'catatan_admin' => $request->catatan ?? '',
            'reviewed_by'   => $user['id'],
            'reviewed_at'   => now(),
            'updated_at'    => now(),
        ]);

        if ($dok->perangkat_daerah_id) {
            $operator = DB::table('pengguna')
                ->where('perangkat_daerah_id', $dok->perangkat_daerah_id)
                ->where('role', 'operator')
                ->first();

            if ($operator) {
                $statusText  = $request->status === 'disetujui' ? 'disetujui' : 'ditolak';
                $statusIcon  = $request->status === 'disetujui' ? '✅' : '❌';
                $statusColor = $request->status === 'disetujui' ? 'green' : 'red';

                DB::table('notifikasi')->insert([
                    'user_id'      => $operator->id,
                    'judul'        => 'Dokumen Cascading ' . ucfirst($statusText),
                    'pesan'        => "Dokumen Cascading/Pohon Kinerja Anda telah {$statusText} oleh admin."
                                      . ($request->catatan ? " Catatan: {$request->catatan}" : ''),
                    'tipe'         => 'dokumen_review',
                    'ikon'         => $statusIcon,
                    'warna'        => $statusColor,
                    'url'          => route('perjanjian.cascading'),
                    'referensi_id' => $id,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }

        return back()->with('success', 'Dokumen Cascading berhasil direview.');
    }

    // ════════════════════════════════════════════════════
    //  HAPUS CASCADING (admin)
    // ════════════════════════════════════════════════════
    public function hapusCascading($id)
    {
        $user = Session::get('user');
        if ($user['role'] !== 'admin') {
            return back()->with('error', 'Hanya admin yang dapat menghapus dokumen.');
        }

        $dok = DB::table('dokumen_opd')->find($id);
        if (!$dok) return back()->with('error', 'Dokumen tidak ditemukan.');

        $path = storage_path('app/dokumen_opd/' . $dok->nama_file);
        if (file_exists($path)) @unlink($path);

        DB::table('dokumen_opd')->where('id', $id)->delete();

        return back()->with('success', 'Dokumen Cascading berhasil dihapus.');
    }

    // ════════════════════════════════════════════════════
    //  UPLOAD TEMPLATE (admin only)
    // ════════════════════════════════════════════════════
    public function uploadTemplate(Request $request)
    {
        $user = Session::get('user');
        if ($user['role'] !== 'admin') {
            return back()->with('error', 'Hanya admin yang dapat mengupload template.');
        }

        $request->validate([
            'jenis' => 'required|string',
            'tahun' => 'required|integer',
            'file'  => 'required|file|mimes:pdf,docx,xlsx,doc,xls|max:20480',
        ]);

        $file   = $request->file('file');
        $ext    = $file->getClientOriginalExtension();
        $fname  = 'template_' . $request->jenis . '_' . $request->tahun . '.' . $ext;
        $folder = storage_path('app/templates');

        if (!file_exists($folder)) mkdir($folder, 0777, true);

        $existing = DB::table('perjanjian_kinerja')
            ->where('jenis', $request->jenis)
            ->where('tahun', $request->tahun)
            ->whereNull('perangkat_daerah_id')
            ->first();

        // ✅ Hapus file lama SEBELUM move file baru
        if ($existing) {
            $oldPath = storage_path('app/templates/' . $existing->nama_file);
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        $file->move($folder, $fname);

        

        if ($existing) {
            DB::table('perjanjian_kinerja')->where('id', $existing->id)->update([
                'nama_file'   => $fname,
                'path_file'   => 'templates/' . $fname,
                'uploaded_by' => $user['id'],
                'updated_at'  => now(),
            ]);
        } else {
            DB::table('perjanjian_kinerja')->insert([
                'jenis'       => $request->jenis,
                'tahun'       => $request->tahun,
                'nama_file'   => $fname,
                'path_file'   => 'templates/' . $fname,
                'uploaded_by' => $user['id'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $cfg        = $this->getCfgByKey($request->jenis);
        $label      = $cfg['label'] ?? $request->jenis;
        $jenisRoute = $this->getJenisRouteForTemplate($request->jenis);

        foreach (DB::table('pengguna')->where('role', 'operator')->get() as $operator) {
            DB::table('notifikasi')->insert([
                'user_id'    => $operator->id,
                'judul'      => 'Template Dokumen Baru',
                'pesan'      => "Admin mengupload template {$label} untuk tahun {$request->tahun}. Silakan unduh template terbaru.",
                'tipe'       => 'template_upload',
                'ikon'       => '📄',
                'warna'      => 'indigo',
                'url'        => route('perjanjian.index', $jenisRoute),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Template berhasil diupload: ' . $fname);
    }

    // ════════════════════════════════════════════════════
    //  DOWNLOAD TEMPLATE
    // ════════════════════════════════════════════════════
    public function downloadTemplate($id)
    {
        $tpl = DB::table('perjanjian_kinerja')->find($id);
        if (!$tpl || !$tpl->nama_file) abort(404, 'Template tidak ditemukan.');

        $path = $this->cariFile('templates', $tpl->nama_file, $tpl->path_file);
        if (!$path) abort(404, 'File template tidak ditemukan di server.');

        return response()->download($path, $tpl->nama_file);
    }

    // ════════════════════════════════════════════════════
    //  PREVIEW TEMPLATE
    // ════════════════════════════════════════════════════
    public function previewTemplate($id)
    {
        $user = Session::get('user');
        if (!in_array($user['role'], ['admin', 'operator'])) {
            abort(403, 'Anda tidak memiliki akses untuk preview template.');
        }

        $tpl = DB::table('perjanjian_kinerja')->find($id);
        if (!$tpl || !$tpl->nama_file) abort(404, 'Template tidak ditemukan.');

        $path = $this->cariFile('templates', $tpl->nama_file, $tpl->path_file);
        if (!$path) abort(404, 'File template tidak ditemukan di server.');

        $ext = strtolower(pathinfo($tpl->nama_file, PATHINFO_EXTENSION));
        if ($ext === 'pdf') {
            return response()->file($path, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $tpl->nama_file . '"',
            ]);
        }

        return response()->download($path, $tpl->nama_file);
    }

    // ════════════════════════════════════════════════════
    //  HAPUS TEMPLATE (admin only)
    // ════════════════════════════════════════════════════
    public function hapusTemplate($id)
    {
        $user = Session::get('user');
        if ($user['role'] !== 'admin') {
            return back()->with('error', 'Hanya admin yang dapat menghapus template.');
        }

        $tpl = DB::table('perjanjian_kinerja')->find($id);
        if (!$tpl) return back()->with('error', 'Template tidak ditemukan.');

        $path = storage_path('app/templates/' . $tpl->nama_file);
        if (file_exists($path)) @unlink($path);

        DB::table('perjanjian_kinerja')->where('id', $id)->delete();

        return back()->with('success', 'Template berhasil dihapus.');
    }

    // ════════════════════════════════════════════════════
    //  UPLOAD DOKUMEN (operator) — ✅ BUG FIXED
    // ════════════════════════════════════════════════════
    public function uploadDokumen(Request $request)
    {
        $user = Session::get('user');

        if (!$user || $user['role'] !== 'operator') {
            return back()->with('error', 'Hanya operator yang dapat mengupload dokumen.');
        }

        $request->validate([
            'jenis'      => 'required|string',
            'tahun'      => 'required|integer',
            'file'       => 'required|file|mimes:pdf,docx,xlsx,doc,xls|max:20480',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $file   = $request->file('file');
        $ext    = $file->getClientOriginalExtension();
        $folder = storage_path('app/dokumen_opd');

        if (!file_exists($folder)) mkdir($folder, 0777, true);

        // ✅ STEP 1: Cari dokumen lama TERLEBIH DAHULU
        $existing = DB::table('dokumen_opd')
            ->where('perangkat_daerah_id', $user['daerah_id'])
            ->where('jenis', $request->jenis)
            ->where('tahun', $request->tahun)
            ->first();

        // ✅ STEP 2: Hapus file lama SEBELUM move file baru
        //    (mencegah file baru terhapus jika nama sama)
        if ($existing) {
            $oldPath = storage_path('app/dokumen_opd/' . $existing->nama_file);
            if (file_exists($oldPath)) @unlink($oldPath);
        }

        // ✅ STEP 3: Nama file dengan time() agar selalu unik
        $fname = 'opd_' . $user['daerah_id'] . '_' . $request->jenis . '_' . $request->tahun . '_' . time() . '.' . $ext;

        // ✅ STEP 4: Move file baru
        $file->move($folder, $fname);
        

        // ✅ STEP 5: Simpan / update DB
        if ($existing) {
            DB::table('dokumen_opd')->where('id', $existing->id)->update([
                'nama_file'   => $fname,
                'path_file'   => 'dokumen_opd/' . $fname,
                'keterangan'  => $request->keterangan ?? '',
                'uploaded_by' => $user['id'],
                'status'      => 'menunggu',
                'updated_at'  => now(),
            ]);
        } else {
            DB::table('dokumen_opd')->insert([
                'perangkat_daerah_id' => $user['daerah_id'],
                'jenis'       => $request->jenis,
                'tahun'       => $request->tahun,
                'nama_file'   => $fname,
                'path_file'   => 'dokumen_opd/' . $fname,
                'keterangan'  => $request->keterangan ?? '',
                'uploaded_by' => $user['id'],
                'status'      => 'menunggu',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // Notifikasi ke admin
        $cfg        = $this->getCfgByOpd($request->jenis);
        $label      = $cfg['label'] ?? $request->jenis;
        $opd        = DB::table('perangkat_daerah')->where('id', $user['daerah_id'])->first();
        $namaOpd    = $opd->nama ?? 'OPD';
        $jenisRoute = $this->getJenisRoute($request->jenis);

        foreach (DB::table('pengguna')->where('role', 'admin')->get() as $admin) {
            DB::table('notifikasi')->insert([
                'user_id'    => $admin->id,
                'judul'      => 'Dokumen Baru Diupload',
                'pesan'      => "Operator {$namaOpd} mengupload dokumen {$label} untuk tahun {$request->tahun}. Status: menunggu persetujuan.",
                'tipe'       => 'dokumen_upload',
                'ikon'       => '📎',
                'warna'      => 'amber',
                'url'        => route('perjanjian.index', $jenisRoute),
                'nama_opd'   => $namaOpd,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Dokumen berhasil diupload!');
    }

    // ════════════════════════════════════════════════════
    //  LIHAT / PREVIEW DOKUMEN
    // ════════════════════════════════════════════════════
   public function lihatDokumen($id)
{
    $user = Session::get('user');
    $dok  = DB::table('dokumen_opd')->find($id);
    if (!$dok) abort(404);

    if ($user['role'] === 'operator' && $dok->perangkat_daerah_id != $user['daerah_id']) {
        abort(403);
    }

    $path = $this->cariFile('dokumen_opd', $dok->nama_file, $dok->path_file);

    if (!$path) {
        return response(
            '<div style="font-family:sans-serif;padding:40px;text-align:center">'
            . '<h2>⚠️ File Tidak Ditemukan</h2>'
            . '<p>File <strong>' . e($dok->nama_file) . '</strong> tidak tersedia.</p>'
            . '</div>',
            404
        );
    }

    $ext = strtolower(pathinfo($dok->nama_file, PATHINFO_EXTENSION));

    if ($ext === 'pdf') {
        return response()->file($path, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $dok->nama_file . '"',
            // ✅ Paksa browser tidak cache file
            'Cache-Control'       => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ]);
    }

    return response()->download($path, $dok->nama_file, [
        'Cache-Control' => 'no-store, no-cache, must-revalidate',
    ]);
}

    public function previewDokumen($id)
    {
        return $this->lihatDokumen($id);
    }

    // ════════════════════════════════════════════════════
    //  REVIEW DOKUMEN (admin)
    // ════════════════════════════════════════════════════
    public function reviewDokumen(Request $request, $id)
    {
        $user = Session::get('user');
        if ($user['role'] !== 'admin') {
            return back()->with('error', 'Hanya admin yang dapat mereview dokumen.');
        }

        $request->validate([
            'status'  => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string|max:500',
        ]);

        $dok = DB::table('dokumen_opd')->find($id);
        if (!$dok) return back()->with('error', 'Dokumen tidak ditemukan.');

        DB::table('dokumen_opd')->where('id', $id)->update([
            'status'        => $request->status,
            'catatan_admin' => $request->catatan ?? '',
            'reviewed_by'   => $user['id'],
            'reviewed_at'   => now(),
            'updated_at'    => now(),
        ]);

        if ($dok->perangkat_daerah_id) {
            $operator = DB::table('pengguna')
                ->where('perangkat_daerah_id', $dok->perangkat_daerah_id)
                ->where('role', 'operator')
                ->first();

            if ($operator) {
                $statusText  = $request->status === 'disetujui' ? 'disetujui' : 'ditolak';
                $statusIcon  = $request->status === 'disetujui' ? '✅' : '❌';
                $statusColor = $request->status === 'disetujui' ? 'green' : 'red';
                $jenisRoute  = $this->getJenisRoute($dok->jenis);

                DB::table('notifikasi')->insert([
                    'user_id'      => $operator->id,
                    'judul'        => 'Dokumen ' . ucfirst($statusText),
                    'pesan'        => "Dokumen perjanjian kinerja Anda telah {$statusText} oleh admin."
                                      . ($request->catatan ? " Catatan: {$request->catatan}" : ''),
                    'tipe'         => 'dokumen_review',
                    'ikon'         => $statusIcon,
                    'warna'        => $statusColor,
                    'url'          => route('perjanjian.index', $jenisRoute),
                    'referensi_id' => $id,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }

        $pesan = $request->status === 'disetujui'
            ? 'Dokumen berhasil disetujui.'
            : 'Dokumen ditolak, operator perlu revisi.';

        return back()->with('success', $pesan);
    }

    // ════════════════════════════════════════════════════
    //  EDIT DOKUMEN (admin)
    // ════════════════════════════════════════════════════
    public function editDokumen(Request $request, $id)
    {
        $user = Session::get('user');
        if ($user['role'] !== 'admin') {
            return back()->with('error', 'Hanya admin yang dapat mengedit dokumen.');
        }

        $request->validate([
            'keterangan' => 'nullable|string|max:500',
            'status'     => 'required|in:menunggu,disetujui,ditolak',
            'catatan'    => 'nullable|string|max:500',
            'file'       => 'nullable|file|mimes:pdf,docx,xlsx,doc,xls|max:20480',
        ]);

        $dok = DB::table('dokumen_opd')->find($id);
        if (!$dok) return back()->with('error', 'Dokumen tidak ditemukan.');

        $data = [
            'keterangan'    => $request->keterangan ?? '',
            'status'        => $request->status,
            'catatan_admin' => $request->catatan ?? '',
            'reviewed_by'   => $user['id'],
            'reviewed_at'   => now(),
            'updated_at'    => now(),
        ];

        if ($request->hasFile('file')) {
            $ext    = $request->file('file')->getClientOriginalExtension();
            $folder = storage_path('app/dokumen_opd');
            if (!file_exists($folder)) mkdir($folder, 0777, true);

            // ✅ Hapus file lama dulu
            $pathLama = storage_path('app/dokumen_opd/' . $dok->nama_file);
            if (file_exists($pathLama)) @unlink($pathLama);

            // ✅ Nama unik dengan time()
            $fname = 'opd_' . $dok->perangkat_daerah_id . '_' . $dok->jenis . '_' . $dok->tahun . '_' . time() . '.' . $ext;
            $request->file('file')->move($folder, $fname);

            $data['nama_file'] = $fname;
            $data['path_file'] = 'dokumen_opd/' . $fname;
        }

        DB::table('dokumen_opd')->where('id', $id)->update($data);

        if ($dok->status != $request->status && $dok->perangkat_daerah_id) {
            $operator = DB::table('pengguna')
                ->where('perangkat_daerah_id', $dok->perangkat_daerah_id)
                ->where('role', 'operator')
                ->first();

            if ($operator) {
                $statusText  = $request->status === 'disetujui' ? 'disetujui'
                             : ($request->status === 'ditolak'   ? 'ditolak' : 'menunggu revisi');
                $statusIcon  = $request->status === 'disetujui' ? '✅'
                             : ($request->status === 'ditolak'   ? '❌' : '🔄');
                $statusColor = $request->status === 'disetujui' ? 'green'
                             : ($request->status === 'ditolak'   ? 'red' : 'amber');
                $jenisRoute  = $this->getJenisRoute($dok->jenis);

                DB::table('notifikasi')->insert([
                    'user_id'      => $operator->id,
                    'judul'        => 'Status Dokumen Berubah',
                    'pesan'        => "Status dokumen perjanjian kinerja Anda berubah menjadi {$statusText}."
                                      . ($request->catatan ? " Catatan: {$request->catatan}" : ''),
                    'tipe'         => 'dokumen_update',
                    'ikon'         => $statusIcon,
                    'warna'        => $statusColor,
                    'url'          => route('perjanjian.index', $jenisRoute),
                    'referensi_id' => $id,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }

        return back()->with('success', 'Dokumen berhasil diperbarui.');
    }

    // ════════════════════════════════════════════════════
    //  HAPUS DOKUMEN (admin)
    // ════════════════════════════════════════════════════
    public function hapusDokumen(Request $request, $id)
    {
        $user = Session::get('user');
        if ($user['role'] !== 'admin') {
            return back()->with('error', 'Hanya admin yang dapat menghapus dokumen.');
        }

        $dok = DB::table('dokumen_opd')->find($id);
        if (!$dok) return back()->with('error', 'Dokumen tidak ditemukan.');

        $path = storage_path('app/dokumen_opd/' . $dok->nama_file);
        if (file_exists($path)) @unlink($path);

        DB::table('dokumen_opd')->where('id', $id)->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}