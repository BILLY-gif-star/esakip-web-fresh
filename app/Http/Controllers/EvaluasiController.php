<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class EvaluasiController extends Controller
{
    // ════════════════════════════════════════════════════
    //  HELPER — cari file template di semua lokasi
    // ════════════════════════════════════════════════════
    private function cariFileTemplate(string $namaFile): ?string
    {
        $candidates = [
            storage_path('app/public/templates/' . $namaFile),
            storage_path('app/templates/' . $namaFile),
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    // ════════════════════════════════════════════════════
    //  JUKNIS
    // ════════════════════════════════════════════════════
    public function juknis()
    {
        $user = Session::get('user');
        $list = DB::table('perjanjian_kinerja')
            ->where('jenis', 'juknis')
            ->whereNotNull('nama_file')
            ->orderByDesc('tahun')
            ->get();

        return view('evaluasi.index', compact('user', 'list'))
            ->with('judul', 'Juknis')
            ->with('jenisUpload', 'juknis');
    }

    // ════════════════════════════════════════════════════
    //  LKE AKIP
    // ════════════════════════════════════════════════════
    public function lke()
    {
        $user = Session::get('user');
        $list = DB::table('perjanjian_kinerja')
            ->where('jenis', 'lke_akip')
            ->whereNotNull('nama_file')
            ->orderByDesc('tahun')
            ->get();

        return view('evaluasi.index', compact('user', 'list'))
            ->with('judul', 'LKE AKIP')
            ->with('jenisUpload', 'lke_akip');
    }

    // ════════════════════════════════════════════════════
    //  UPLOAD TEMPLATE (admin only)
    //  ⭐ PERBAIKAN:
    //    1. Simpan ke storage/app/public/templates/ agar konsisten
    //    2. Buat folder otomatis jika belum ada
    //    3. Ganti NotifikasiService dengan DB::table langsung
    //    4. Wrap dalam try-catch agar error notifikasi tidak
    //       membatalkan upload
    // ════════════════════════════════════════════════════
    public function upload(Request $request)
    {
        $user = Session::get('user');
        if ($user['role'] !== 'admin') {
            abort(403, 'Hanya admin yang dapat mengupload template.');
        }

        $request->validate([
            'jenis' => 'required|string',
            'tahun' => 'required|integer',
            'file'  => 'required|file|mimes:pdf,docx,xlsx,doc,xls|max:20480',
        ]);

        // ── Simpan file fisik ─────────────────────────────
        $file  = $request->file('file');
        $ext   = $file->getClientOriginalExtension();
        $fname = $request->jenis . '_' . $request->tahun . '.' . $ext;

        // Pastikan folder ada
        $folderPath = storage_path('app/public/templates');
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $file->move($folderPath, $fname);

        // ── Simpan ke database ────────────────────────────
        $existing = DB::table('perjanjian_kinerja')
            ->where('jenis', $request->jenis)
            ->where('tahun', $request->tahun)
            ->whereNull('perangkat_daerah_id')
            ->first();

        if ($existing) {
            // Hapus file lama jika berbeda nama
            if ($existing->nama_file && $existing->nama_file !== $fname) {
                $pathLama = $this->cariFileTemplate($existing->nama_file);
                if ($pathLama) @unlink($pathLama);
            }

            DB::table('perjanjian_kinerja')->where('id', $existing->id)->update([
                'nama_file'   => $fname,
                'path_file'   => 'public/templates/' . $fname,
                'uploaded_by' => $user['id'],
                'updated_at'  => now(),
            ]);

            $documentId = $existing->id;
        } else {
            $documentId = DB::table('perjanjian_kinerja')->insertGetId([
                'jenis'       => $request->jenis,
                'tahun'       => $request->tahun,
                'nama_file'   => $fname,
                'path_file'   => 'public/templates/' . $fname,
                'uploaded_by' => $user['id'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // ── Notifikasi ke semua operator ──────────────────
        // Wrap dalam try-catch agar error notifikasi tidak
        // menggagalkan upload yang sudah berhasil
        try {
            $jenisLabel = match($request->jenis) {
                'juknis'   => 'Juknis',
                'lke_akip' => 'LKE AKIP',
                default    => $request->jenis,
            };

            $url = $request->jenis === 'juknis'
                ? route('evaluasi.juknis')
                : route('evaluasi.lke');

            $operatorUsers = DB::table('pengguna')
                ->where('role', 'operator')
                ->where('is_active', 1)
                ->get();

            foreach ($operatorUsers as $operator) {
                DB::table('notifikasi')->insert([
                    'user_id'      => $operator->id,
                    'judul'        => "Template {$jenisLabel} Tersedia",
                    'pesan'        => "Admin mengupload template {$jenisLabel} tahun {$request->tahun}. Silakan unduh template terbaru.",
                    'tipe'         => 'template_upload',
                    'ikon'         => '📄',
                    'warna'        => 'indigo',
                    'url'          => $url,
                    'referensi_id' => $documentId,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        } catch (\Exception $e) {
            // Notifikasi gagal tidak membatalkan upload
            // Log error jika diperlukan
        }

        return back()->with('success', 'File ' . $fname . ' berhasil diupload.');
    }

    // ════════════════════════════════════════════════════
    //  DOWNLOAD TEMPLATE
    //  ⭐ PERBAIKAN: gunakan helper cariFileTemplate()
    //     agar mencari di semua lokasi kemungkinan
    // ════════════════════════════════════════════════════
    public function download($id)
    {
        $tpl = DB::table('perjanjian_kinerja')->find($id);

        if (!$tpl || !$tpl->nama_file) {
            abort(404, 'Template tidak ditemukan di database.');
        }

        $path = $this->cariFileTemplate($tpl->nama_file);

        if (!$path) {
            abort(404, 'File "' . $tpl->nama_file . '" tidak ditemukan di server. Silakan upload ulang.');
        }

        return response()->download($path, $tpl->nama_file);
    }
}