<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Models\Juknis;

class JuknisController extends Controller
{
    // ── Helper: ambil user dari session ──────────────────
    private function getUser(): array
    {
        $user = Session::get('user');
        if (!$user) abort(401, 'Silakan login terlebih dahulu.');
        return $user;
    }

    // ════════════════════════════════════════════════════
    //  ADMIN: Daftar juknis (upload, edit, hapus)
    // ════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $user = $this->getUser();

        if ($user['role'] !== 'admin') {
            abort(403, 'Hanya admin yang dapat mengakses halaman ini.');
        }

        $juknisList = Juknis::orderBy('urutan', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('juknis.index', compact('user', 'juknisList'));
    }

    // ════════════════════════════════════════════════════
    //  OPERATOR & ADMIN: Lihat daftar juknis aktif
    // ════════════════════════════════════════════════════
    public function userIndex(Request $request)
    {
        $user = $this->getUser();

        // Admin bisa lihat semua, operator hanya yang aktif
        if ($user['role'] === 'admin') {
            $juknisList = Juknis::orderBy('urutan', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $juknisList = Juknis::where('is_active', true)
                ->orderBy('urutan', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('juknis.user_index', compact('user', 'juknisList'));
    }

    // ════════════════════════════════════════════════════
    //  ADMIN: Upload juknis baru
    // ════════════════════════════════════════════════════
    public function upload(Request $request)
    {
        $user = $this->getUser();

        if ($user['role'] !== 'admin') {
            return back()->with('error', 'Hanya admin yang dapat upload juknis.');
        }

        $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'file'      => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:20480',
            'urutan'    => 'nullable|integer|min:0',
        ], [
            'file.mimes' => 'Format file harus PDF, DOC, DOCX, XLS, atau XLSX.',
            'file.max'   => 'Ukuran file maksimal 5MB.',
        ]);

        $file     = $request->file('file');
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
        $filePath = $file->storeAs('juknis', $fileName, 'public');

        if (!$filePath) {
            return back()->with('error', 'Gagal menyimpan file. Silakan coba lagi.');
        }

        $juknis = Juknis::create([
            'judul'       => $request->judul,
            'deskripsi'   => $request->deskripsi ?? '',
            'file_path'   => $filePath,
            'file_name'   => $file->getClientOriginalName(),
            'file_size'   => $file->getSize(),
            'urutan'      => $request->urutan ?? 0,
            'is_active'   => true,
            'uploaded_by' => $user['id'],
        ]);

        // ✅ KIRIM NOTIFIKASI HANYA KE OPERATOR (BUKAN KE ADMIN)
        $operators = DB::table('pengguna')
            ->where('role', 'operator')
            ->where('is_active', 1)
            ->get();

        foreach ($operators as $operator) {
            DB::table('notifikasi')->insert([
                'user_id'    => $operator->id,
                'judul'      => '📖 Juknis Baru Tersedia',
                'pesan'      => "Admin telah mengupload Juknis baru: " . $request->judul,
                'tipe'       => 'juknis',
                'ikon'       => '📘',
                'warna'      => 'blue',
                'url'        => route('juknis.user'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('juknis.index')
            ->with('success', 'Juknis berhasil diupload!');
    }

    // ════════════════════════════════════════════════════
    //  ADMIN: Update juknis
    // ════════════════════════════════════════════════════
    public function update(Request $request, $id)
    {
        $user = $this->getUser();

        if ($user['role'] !== 'admin') {
            return back()->with('error', 'Hanya admin yang dapat mengedit juknis.');
        }

        $juknis = Juknis::findOrFail($id);

        $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'urutan'    => 'nullable|integer|min:0',
            'is_active' => 'nullable',
            'file'      => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:20480',
        ], [
            'file.mimes' => 'Format file harus PDF, DOC, DOCX, XLS, atau XLSX.',
            'file.max'   => 'Ukuran file maksimal 5MB.',
        ]);

        $data = [
            'judul'     => $request->judul,
            'deskripsi' => $request->deskripsi ?? '',
            'urutan'    => $request->urutan ?? 0,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : $juknis->is_active,
        ];

        // Ganti file jika ada upload baru
        if ($request->hasFile('file')) {
            // Hapus file lama
            if ($juknis->file_path && Storage::disk('public')->exists($juknis->file_path)) {
                Storage::disk('public')->delete($juknis->file_path);
            }

            $file     = $request->file('file');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $filePath = $file->storeAs('juknis', $fileName, 'public');

            if (!$filePath) {
                return back()->with('error', 'Gagal menyimpan file baru.');
            }

            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
        }

        $juknis->update($data);

        return redirect()->route('juknis.index')
            ->with('success', 'Juknis berhasil diperbarui!');
    }

    // ════════════════════════════════════════════════════
    //  ADMIN: Hapus juknis
    // ════════════════════════════════════════════════════
    public function delete($id)
    {
        $user = $this->getUser();

        if ($user['role'] !== 'admin') {
            return back()->with('error', 'Hanya admin yang dapat menghapus juknis.');
        }

        $juknis = Juknis::findOrFail($id);

        // Hapus file fisik
        if ($juknis->file_path && Storage::disk('public')->exists($juknis->file_path)) {
            Storage::disk('public')->delete($juknis->file_path);
        }

        $juknis->delete();

        return redirect()->route('juknis.index')
            ->with('success', 'Juknis berhasil dihapus!');
    }

    // ════════════════════════════════════════════════════
    //  SEMUA USER: Download juknis
    // ════════════════════════════════════════════════════
    public function download($id)
    {
        $user   = $this->getUser();
        $juknis = Juknis::findOrFail($id);

        // Operator hanya bisa download yang aktif
        if ($user['role'] !== 'admin' && !$juknis->is_active) {
            abort(403, 'Dokumen ini tidak tersedia.');
        }

        $path = storage_path('app/public/' . $juknis->file_path);

        if (!file_exists($path)) {
            return back()->with('error', 'File tidak ditemukan di server. Silakan hubungi Admin.');
        }

        return response()->download($path, $juknis->file_name);
    }

    // ════════════════════════════════════════════════════
    //  SEMUA USER: Preview juknis
    // ════════════════════════════════════════════════════
    public function preview($id)
    {
        $user   = $this->getUser();
        $juknis = Juknis::findOrFail($id);

        // Operator hanya bisa preview yang aktif
        if ($user['role'] !== 'admin' && !$juknis->is_active) {
            abort(403, 'Dokumen ini tidak tersedia.');
        }

        $path = storage_path('app/public/' . $juknis->file_path);

        if (!file_exists($path)) {
            return back()->with('error', 'File tidak ditemukan di server. Silakan hubungi Admin.');
        }

        $ext = strtolower(pathinfo($juknis->file_name, PATHINFO_EXTENSION));

        if ($ext === 'pdf') {
            return response()->file($path, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $juknis->file_name . '"',
                'Cache-Control'       => 'no-store, no-cache, must-revalidate',
            ]);
        }

        return response()->download($path, $juknis->file_name);
    }
}