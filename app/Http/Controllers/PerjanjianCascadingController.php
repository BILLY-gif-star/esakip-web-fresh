<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Concerns\PerjanjianHelpers;

class PerjanjianCascadingController extends Controller
{
    use PerjanjianHelpers;

    // ════════════════════════════════════════════════════
    //  CASCADING / POHON KINERJA
    // ════════════════════════════════════════════════════
    public function index(Request $request)
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
    public function upload(Request $request)
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
    public function hapus($id)
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
}
