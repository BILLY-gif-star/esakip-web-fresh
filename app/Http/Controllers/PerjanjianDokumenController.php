<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Concerns\PerjanjianHelpers;

class PerjanjianDokumenController extends Controller
{
    use PerjanjianHelpers;

    // ════════════════════════════════════════════════════
    //  UPLOAD DOKUMEN (operator)
    // ════════════════════════════════════════════════════
    public function upload(Request $request)
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
    public function lihat($id)
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

    public function preview($id)
    {
        return $this->lihat($id);
    }

    // ════════════════════════════════════════════════════
    //  REVIEW DOKUMEN (admin)
    // ════════════════════════════════════════════════════
    public function review(Request $request, $id)
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
    public function edit(Request $request, $id)
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
    public function hapus(Request $request, $id)
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
