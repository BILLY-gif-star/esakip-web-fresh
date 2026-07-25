<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DokumenHasilController extends Controller
{
    // ── Admin: upload dokumen ke OPD tertentu ─────────────
    public function upload(Request $request)
    {
        $user = Session::get('user');
        if ($user['role'] !== 'admin') abort(403);

        $request->validate([
            'judul'               => 'required|string|max:200',
            'deskripsi'           => 'nullable|string|max:500',
            'tahun'               => 'required|integer',
            'perangkat_daerah_id' => 'required|integer',
            'file'                => 'required|file|mimes:pdf|max:20480',
        ], [
            'file.mimes' => 'File harus berformat PDF',
            'file.max'   => 'Ukuran file maksimal 5MB',
        ]);

        $opdId = $request->perangkat_daerah_id;
        $ext   = $request->file('file')->getClientOriginalExtension();
        $fname = 'hasil_' . $opdId . '_' . $request->tahun . '_' . time() . '.' . $ext;

        $folderPath = storage_path('app/dokumen_hasil');
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0775, true);
        }

        $request->file('file')->move($folderPath, $fname);

        if (!file_exists($folderPath . '/' . $fname)) {
            return back()->with('error', 'File gagal disimpan.');
        }

        DB::table('dokumen_hasil')->insertGetId([
            'judul'               => $request->judul,
            'deskripsi'           => $request->deskripsi ?? '',
            'nama_file'           => $fname,
            'tahun'               => $request->tahun,
            'perangkat_daerah_id' => $opdId,
            'uploaded_by'         => $user['id'],
            'dibaca_at'           => null,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        $opd     = DB::table('perangkat_daerah')->where('id', $opdId)->first();
        $namaOpd = $opd->nama ?? 'OPD';

        $operator = DB::table('pengguna')
            ->where('perangkat_daerah_id', $opdId)
            ->where('role', 'operator')
            ->first();

        if ($operator) {
            DB::table('notifikasi')->insert([
                'user_id'    => $operator->id,
                'judul'      => '📄 Dokumen Hasil Tersedia',
                'pesan'      => "Admin mengupload dokumen \"{$request->judul}\" untuk {$namaOpd} tahun {$request->tahun}. Silakan unduh melalui menu Rekap Hasil.",
                'tipe'       => 'dokumen_hasil',
                'ikon'       => '📄',
                'warna'      => 'indigo',
                'url'        => '/rekap-hasil-opd?tahun=' . $request->tahun,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Dokumen berhasil diupload ke ' . $namaOpd . '!');
    }

    // ── Admin: hapus dokumen ───────────────────────────────
    public function hapus($id)
    {
        $user = Session::get('user');
        if ($user['role'] !== 'admin') abort(403);

        $doc = DB::table('dokumen_hasil')->find($id);
        if (!$doc) abort(404);

        $path = storage_path('app/dokumen_hasil/' . $doc->nama_file);
        if (file_exists($path)) {
            unlink($path);
        }

        DB::table('dokumen_hasil')->where('id', $id)->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }

    // ── Unduh dokumen (admin & operator OPD terkait) ───────
    public function unduh($id)
    {
        $user = Session::get('user');

        $doc = DB::table('dokumen_hasil')->find($id);
        if (!$doc) abort(404);

        if ($user['role'] === 'operator' && $doc->perangkat_daerah_id != $user['daerah_id']) {
            abort(403);
        }

        $path = storage_path('app/dokumen_hasil/' . $doc->nama_file);
        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        if ($user['role'] === 'operator' && is_null($doc->dibaca_at)) {
            DB::table('dokumen_hasil')->where('id', $id)->update(['dibaca_at' => now()]);
        }

        return response()->download($path, $doc->nama_file);
    }

    // ── Halaman rekap hasil OPD (untuk operator) ───────────
    public function rekapOpd(Request $request)
    {
        $user  = Session::get('user');
        $tahun = (int) $request->input('tahun', date('Y') - 1);

        if ($user['role'] !== 'operator') {
            abort(403, 'Halaman ini hanya untuk operator.');
        }

        $opdId = $user['daerah_id'] ?? null;

        if (!$opdId) {
            return view('rekap-hasil-opd', [
                'user'      => $user,
                'tahun'     => $tahun,
                'listTahun' => range(date('Y'), date('Y') - 5),
                'dokumen'   => collect(),   // ✅ fix: was 'dokumenList'
                'opdNama'   => null,
                'error'     => 'OPD tidak ditemukan untuk akun Anda.',
            ]);
        }

        $opd     = DB::table('perangkat_daerah')->where('id', $opdId)->first();
        $opdNama = $opd->nama ?? 'OPD';

        $dokumen = DB::table('dokumen_hasil')   // ✅ fix: was $dokumenList
            ->where('perangkat_daerah_id', $opdId)
            ->where('tahun', $tahun)
            ->orderByDesc('created_at')
            ->get();

        $listTahun = range(date('Y'), date('Y') - 5);

        return view('rekap-hasil-opd', compact('user', 'tahun', 'listTahun', 'dokumen', 'opdNama')); // ✅
    }

    // ── API: ambil daftar dokumen per OPD (untuk modal admin) ─
    public function listPerOpd(Request $request)
    {
        $user = Session::get('user');
        if ($user['role'] !== 'admin') abort(403);

        $opdId = $request->input('opd_id');
        $tahun = $request->input('tahun', date('Y') - 1);

        $dokumen = DB::table('dokumen_hasil as d')
            ->leftJoin('pengguna as p', 'd.uploaded_by', '=', 'p.id')
            ->select('d.*', 'p.nama as uploader_nama')
            ->where('d.perangkat_daerah_id', $opdId)
            ->where('d.tahun', $tahun)
            ->orderByDesc('d.created_at')
            ->get();

        return response()->json($dokumen);
    }
}