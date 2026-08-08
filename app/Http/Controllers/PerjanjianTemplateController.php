<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Concerns\PerjanjianHelpers;

class PerjanjianTemplateController extends Controller
{
    use PerjanjianHelpers;

    // ════════════════════════════════════════════════════
    //  UPLOAD TEMPLATE (admin only)
    // ════════════════════════════════════════════════════
    public function upload(Request $request)
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
    public function download($id)
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
    public function preview($id)
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
    public function hapus($id)
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
}
