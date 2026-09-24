<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Services\NotifikasiService;

class AdminController extends Controller
{
    // ════════════════════════════════════════════════════════════
    //  MANAJEMEN PENGGUNA
    // ════════════════════════════════════════════════════════════

    /**
     * Menampilkan daftar semua pengguna
     */
    public function pengguna()
    {
        $user = Session::get('user');

        if ($user['role'] !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $list = DB::table('pengguna as p')
            ->leftJoin('perangkat_daerah as pd', 'p.perangkat_daerah_id', '=', 'pd.id')
            ->select('p.*', 'pd.nama as nama_daerah')
            ->orderBy('p.role')
            ->orderBy('p.nama')
            ->get();
            foreach ($list as $item) {
        if ($item->role === 'evaluator') {
            $item->assigned_opd_count = DB::table('evaluator_opd')
                ->where('evaluator_id', $item->id)
                ->count();
        } else {
            $item->assigned_opd_count = 0;
        }
    }

        $listOpd = DB::table('perangkat_daerah')->orderBy('nama')->get();

        return view('admin.pengguna', compact('user', 'list', 'listOpd'));
    }

    /**
     * Mengambil data user untuk edit (AJAX)
     */
    public function getUser($id)
    {
        $user = Session::get('user');
        
        if ($user['role'] !== 'admin') {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }
        
        $data = DB::table('pengguna')->where('id', $id)->first();
        if (!$data) {
            return response()->json(['error' => 'User tidak ditemukan'], 404);
        }
        
        return response()->json($data);
    }

    /**
     * Menambah user baru (AJAX)
     */
    public function storeUser(Request $request)
    {
        $user = Session::get('user');
        
        if ($user['role'] !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }
        
        $request->validate([
            'username' => 'required|string|max:100|unique:pengguna,username',
            'nama' => 'required|string|max:200',
            'role' => 'required|in:evaluator,operator',
            'perangkat_daerah_id' => 'required_if:role,operator|nullable|exists:perangkat_daerah,id',
            'password' => 'required|string|min:6',
        ]);
        
        $id = DB::table('pengguna')->insertGetId([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'nama' => $request->nama,
            'role' => $request->role,
            'perangkat_daerah_id' => $request->role === 'operator' ? $request->perangkat_daerah_id : null,
            'is_active' => 1,
            'status_daftar' => 'disetujui',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return response()->json(['success' => true, 'message' => 'User berhasil ditambahkan.', 'id' => $id]);
    }

    /**
     * Update user (AJAX)
     */
    public function updateUser(Request $request, $id)
    {
        $user = Session::get('user');
        
        if ($user['role'] !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }
        
        $request->validate([
            'username' => 'required|string|max:100|unique:pengguna,username,' . $id,
            'nama' => 'required|string|max:200',
            'role' => 'required|in:evaluator,operator',
            'perangkat_daerah_id' => 'required_if:role,operator|nullable|exists:perangkat_daerah,id',
            'is_active' => 'nullable|boolean',
        ]);
        
        DB::table('pengguna')->where('id', $id)->update([
            'username' => $request->username,
            'nama' => $request->nama,
            'role' => $request->role,
            'perangkat_daerah_id' => $request->role === 'operator' ? $request->perangkat_daerah_id : null,
            'is_active' => $request->is_active ? 1 : 0,
            'updated_at' => now(),
        ]);
        
        return response()->json(['success' => true, 'message' => 'User berhasil diupdate.']);
    }

    /**
     * Reset password user (AJAX)
     */
    public function resetPasswordUser(Request $request, $id)
    {
        $user = Session::get('user');
        
        if ($user['role'] !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }
        
        $request->validate([
            'password' => 'required|string|min:6',
        ]);
        
        DB::table('pengguna')->where('id', $id)->update([
            'password' => Hash::make($request->password),
            'updated_at' => now(),
        ]);
        
        return response()->json(['success' => true, 'message' => 'Password berhasil direset.']);
    }

    /**
     * Hapus user (AJAX)
     */
    public function deleteUser($id)
{
    $user = Session::get('user');
    
    if ($user['role'] !== 'admin') {
        return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
    }
    
    if ($id == $user['id']) {
        return response()->json(['success' => false, 'message' => 'Tidak dapat menghapus akun sendiri.'], 422);
    }
    
    $targetUser = DB::table('pengguna')->where('id', $id)->first();
    
    if (!$targetUser) {
        return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
    }
    
    // ⭐ MULAI TRANSAKSI DATABASE
    DB::beginTransaction();
    
    try {
        // 1. Hapus data assign OPD (untuk evaluator)
        DB::table('evaluator_opd')->where('evaluator_id', $id)->delete();
        
        // 2. Hapus notifikasi yang ditujukan ke user ini
        DB::table('notifikasi')->where('user_id', $id)->delete();
        
        // 3. Hapus chat (percakapan dan pesan)
        $percakapanIds = DB::table('percakapan')
            ->where('opd_user_id', $id)
            ->orWhere('admin_user_id', $id)
            ->pluck('id')
            ->toArray();
        
        if (!empty($percakapanIds)) {
            DB::table('pesan')->whereIn('percakapan_id', $percakapanIds)->delete();
            DB::table('percakapan')->whereIn('id', $percakapanIds)->delete();
        }
        
        // 4. Hapus dokumen LKE yang diupload user ini
        $dokumenLke = DB::table('lke_dokumen_kriteria')
            ->where('uploaded_by', $id)
            ->get();
        
        foreach ($dokumenLke as $dok) {
            // Hapus file fisik
            $path = storage_path('app/public/lke_dokumen/' . $dok->nama_file);
            if (file_exists($path)) {
                @unlink($path);
            }
        }
        DB::table('lke_dokumen_kriteria')->where('uploaded_by', $id)->delete();
        
        // 5. Hapus penilaian LKE (jika user adalah operator yang dinilai)
        DB::table('lke_penilaian')->where('perangkat_daerah_id', $targetUser->perangkat_daerah_id)->delete();
        DB::table('lke_penilaian_kriteria')->where('perangkat_daerah_id', $targetUser->perangkat_daerah_id)->delete();
        
        // 6. Hapus dokumen OPD yang diupload user ini
        $dokumenOpd = DB::table('dokumen_opd')
            ->where('uploaded_by', $id)
            ->get();
        
        foreach ($dokumenOpd as $dok) {
            $path = storage_path('app/dokumen_opd/' . $dok->nama_file);
            if (file_exists($path)) {
                @unlink($path);
            }
        }
        DB::table('dokumen_opd')->where('uploaded_by', $id)->delete();
        
        // 7. Hapus data capaian kinerja (jika ada)
        DB::table('capaian_kinerja')->where('perangkat_daerah_id', $targetUser->perangkat_daerah_id)->delete();
        
        // 8. Hapus data pengukuran periodik
        DB::table('pengukuran_periodik')->where('perangkat_daerah_id', $targetUser->perangkat_daerah_id)->delete();
        
        // 9. Hapus user itu sendiri
        DB::table('pengguna')->where('id', $id)->delete();
        
        DB::commit();
        
        return response()->json(['success' => true, 'message' => 'User dan semua data terkait berhasil dihapus.']);
        
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error deleting user: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Gagal menghapus: ' . $e->getMessage()], 500);
    }
}

    // ════════════════════════════════════════════════════════════
    //  PERSETUJUAN AKUN
    // ════════════════════════════════════════════════════════════

    /**
     * Menampilkan daftar pengguna yang menunggu persetujuan
     */
    public function persetujuan()
    {
        $user = Session::get('user');

        if ($user['role'] !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $list = DB::table('pengguna as p')
            ->leftJoin('perangkat_daerah as pd', 'p.perangkat_daerah_id', '=', 'pd.id')
            ->select('p.*', 'pd.nama as nama_daerah')
            ->where('p.status_daftar', 'menunggu')
            ->orderByDesc('p.created_at')
            ->get();

        return view('admin.persetujuan', compact('user', 'list'));
    }

    /**
     * Memproses persetujuan atau penolakan akun pengguna
     */
    public function prosesPersetujuan(Request $request, $id)
    {
        $user = Session::get('user');

        if ($user['role'] !== 'admin') {
            return back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'status' => 'required|in:disetujui,ditolak'
        ]);

        $userTarget = DB::table('pengguna')->where('id', $id)->first();

        if (!$userTarget) {
            return back()->with('error', 'User tidak ditemukan.');
        }

        DB::table('pengguna')->where('id', $id)->update([
            'status_daftar' => $request->status,
            'is_active'     => $request->status === 'disetujui' ? 1 : 0,
        ]);

        $statusIcon = $request->status === 'disetujui' ? '✅' : '❌';
        $statusColor = $request->status === 'disetujui' ? 'green' : 'red';
        
        DB::table('notifikasi')->insert([
            'user_id' => $id,
            'judul' => 'Status Akun',
            'pesan' => $request->status === 'disetujui'
                ? 'Akun kamu telah disetujui oleh admin'
                : 'Akun kamu ditolak oleh admin',
            'tipe' => 'persetujuan_user',
            'ikon' => $statusIcon,
            'warna' => $statusColor,
            'nama_opd' => $userTarget->nama ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pesan = $request->status === 'disetujui'
            ? 'Akun berhasil disetujui dan diaktifkan.'
            : 'Akun ditolak.';

        return back()->with('success', $pesan);
    }

    // ════════════════════════════════════════════════════════════
    //  ARSIP DOKUMEN
    // ════════════════════════════════════════════════════════════

    /**
 * Menampilkan halaman assign OPD untuk evaluator
 */
public function assignOpdEvaluator($id)
{
    $user = Session::get('user');
    
    if ($user['role'] !== 'admin') {
        return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
    }
    
    $evaluator = DB::table('pengguna')->where('id', $id)->where('role', 'evaluator')->first();
    
    if (!$evaluator) {
        return back()->with('error', 'Evaluator tidak ditemukan.');
    }
    
    // OPD yang sudah diassign ke evaluator ini
    $assignedOpd = DB::table('evaluator_opd')
        ->where('evaluator_id', $id)
        ->pluck('perangkat_daerah_id')
        ->toArray();
    
    // Semua OPD
    $listOpd = DB::table('perangkat_daerah')->orderBy('nama')->get();
    
    return view('admin.assign-opd-evaluator', compact('evaluator', 'listOpd', 'assignedOpd'));
}

/**
 * Menyimpan assign OPD untuk evaluator
 */
public function storeAssignOpdEvaluator(Request $request, $id)
{
    $user = Session::get('user');
    
    if ($user['role'] !== 'admin') {
        return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
    }
    
    $evaluator = DB::table('pengguna')->where('id', $id)->where('role', 'evaluator')->first();
    
    if (!$evaluator) {
        return response()->json(['success' => false, 'message' => 'Evaluator tidak ditemukan.'], 404);
    }
    
    $request->validate([
        'opd_ids' => 'array',
        'opd_ids.*' => 'exists:perangkat_daerah,id'
    ]);
    
    $opdIds = $request->input('opd_ids', []);
    
    // ⭐ VALIDASI: Cek apakah OPD sudah diassign ke evaluator lain
    $conflictOpd = [];
    foreach ($opdIds as $opdId) {
        $existingAssign = DB::table('evaluator_opd')
            ->where('perangkat_daerah_id', $opdId)
            ->where('evaluator_id', '!=', $id)
            ->exists();
        
        if ($existingAssign) {
            $opdNama = DB::table('perangkat_daerah')->where('id', $opdId)->value('nama');
            $conflictOpd[] = $opdNama;
        }
    }
    
    if (!empty($conflictOpd)) {
        return response()->json([
            'success' => false, 
            'message' => 'OPD berikut sudah diassign ke evaluator lain: ' . implode(', ', $conflictOpd)
        ], 422);
    }
    
    // Hapus assign yang lama
    DB::table('evaluator_opd')->where('evaluator_id', $id)->delete();
    
    // Tambah assign yang baru
    foreach ($opdIds as $opdId) {
        DB::table('evaluator_opd')->insert([
            'evaluator_id' => $id,
            'perangkat_daerah_id' => $opdId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    return response()->json(['success' => true, 'message' => 'OPD berhasil diassign ke evaluator.']);
}

/**
 * Mendapatkan daftar OPD yang diassign ke evaluator (AJAX)
 */
public function getAssignedOpdEvaluator($id)
{
    $user = Session::get('user');
    
    if ($user['role'] !== 'admin') {
        return response()->json(['error' => 'Akses ditolak.'], 403);
    }
    
    $assignedOpd = DB::table('evaluator_opd')
        ->where('evaluator_id', $id)
        ->pluck('perangkat_daerah_id')
        ->toArray();
    
    return response()->json(['assigned_opd' => $assignedOpd]);
}

    /**
     * Menampilkan halaman arsip dokumen
     */
    public function arsip(Request $request)
    {
        $user = Session::get('user');

        if ($user['role'] !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $tahun = $request->input('tahun', date('Y'));

        $arsip = DB::table('dokumen_opd as d')
            ->leftJoin('perangkat_daerah as pd', 'd.perangkat_daerah_id', '=', 'pd.id')
            ->leftJoin('pengguna as p', 'd.uploaded_by', '=', 'p.id')
            ->leftJoin('pengguna as r', 'd.reviewed_by', '=', 'r.id')
            ->select('d.*', 'pd.nama as nama_opd', 'p.nama as nama_uploader', 'r.nama as nama_reviewer')
            ->where('d.status', 'disetujui')
            ->where('d.tahun', $tahun)
            ->orderBy('pd.nama')
            ->orderBy('d.jenis')
            ->get();

        $listTahun = DB::table('dokumen_opd')
            ->select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();

        if (empty($listTahun)) {
            $listTahun = [date('Y')];
        }

        return view('admin.arsip', compact('user', 'arsip', 'tahun', 'listTahun'));
    }

    /**
     * Menampilkan file arsip
     */
    public function lihatArsip($id)
    {
        $user = Session::get('user');

        if ($user['role'] !== 'admin') {
            return back()->with('error', 'Akses ditolak.');
        }

        $dokumen = DB::table('dokumen_opd')->where('id', $id)->first();

        if (!$dokumen) {
            return back()->with('error', 'Dokumen tidak ditemukan.');
        }

        $path = storage_path('app/dokumen_opd/' . $dokumen->nama_file);

        if (!file_exists($path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        $ext = strtolower(pathinfo($dokumen->nama_file, PATHINFO_EXTENSION));

        if ($ext === 'pdf') {
            return response()->file($path, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $dokumen->nama_file . '"'
            ]);
        }

        return response()->download($path, $dokumen->nama_file);
    }

    /**
     * Menghapus dokumen dari arsip
     */
    public function hapusArsip($id)
    {
        $user = Session::get('user');

        if ($user['role'] !== 'admin') {
            return back()->with('error', 'Akses ditolak.');
        }

        $dokumen = DB::table('dokumen_opd')->where('id', $id)->first();

        if (!$dokumen) {
            return back()->with('error', 'Dokumen tidak ditemukan.');
        }

        $path = storage_path('app/dokumen_opd/' . $dokumen->nama_file);
        if (file_exists($path)) {
            @unlink($path);
        }

        DB::table('dokumen_opd')->where('id', $id)->delete();

        return back()->with('success', 'Dokumen berhasil dihapus dari arsip.');
    }

    // ════════════════════════════════════════════════════════════
    //  KELOLA KLASTER OPD (BARU)
    // ════════════════════════════════════════════════════════════

    /**
     * Menampilkan halaman kelola klaster OPD
     */
    public function kelolaKlaster()
    {
        $user = Session::get('user');
        
        if ($user['role'] !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        // Ambil semua OPD dengan klaster dan tugas
        $opdList = DB::table('perangkat_daerah')
            ->select('id', 'nama', 'singkatan', 'klaster', 'tugas', 'klaster_updated_at', 'klaster_updated_by')
            ->orderBy('klaster')
            ->orderBy('nama')
            ->get();

        // Kelompokkan OPD berdasarkan klaster
        $groupedOpd = [
            'utama' => [],
            'pendukung' => [],
            'tambahan' => [],
            'belum' => [],
        ];

        foreach ($opdList as $opd) {
            if ($opd->klaster === 'utama') {
                $groupedOpd['utama'][] = $opd;
            } elseif ($opd->klaster === 'pendukung') {
                $groupedOpd['pendukung'][] = $opd;
            } elseif ($opd->klaster === 'tambahan') {
                $groupedOpd['tambahan'][] = $opd;
            } else {
                $groupedOpd['belum'][] = $opd;
            }
        }

        // Statistik
        $stats = [
            'total' => $opdList->count(),
            'utama' => count($groupedOpd['utama']),
            'pendukung' => count($groupedOpd['pendukung']),
            'tambahan' => count($groupedOpd['tambahan']),
            'belum' => count($groupedOpd['belum']),
        ];

        return view('admin.kelola-klaster', compact('user', 'groupedOpd', 'stats'));
    }

    /**
     * Update klaster OPD (AJAX)
     */
    public function updateKlasterOpd(Request $request, $id)
    {
        $user = Session::get('user');
        
        if ($user['role'] !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'klaster' => 'required|in:utama,pendukung,tambahan',
        ]);

        $opd = DB::table('perangkat_daerah')->where('id', $id)->first();
        if (!$opd) {
            return response()->json(['success' => false, 'message' => 'OPD tidak ditemukan.'], 404);
        }

        DB::table('perangkat_daerah')->where('id', $id)->update([
            'klaster' => $request->klaster,
            'klaster_updated_at' => now(),
            'klaster_updated_by' => $user['id'],
        ]);

        return response()->json(['success' => true, 'message' => 'Klaster OPD berhasil diubah.']);
    }

    /**
     * Update tugas OPD (AJAX)
     */
    public function updateTugasOpd(Request $request, $id)
    {
        $user = Session::get('user');
        
        if ($user['role'] !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'tugas' => 'nullable|string|max:300',
        ]);

        $opd = DB::table('perangkat_daerah')->where('id', $id)->first();
        if (!$opd) {
            return response()->json(['success' => false, 'message' => 'OPD tidak ditemukan.'], 404);
        }

        DB::table('perangkat_daerah')->where('id', $id)->update([
            'tugas' => $request->tugas ?: null,
            'klaster_updated_at' => now(),
            'klaster_updated_by' => $user['id'],
        ]);

        return response()->json(['success' => true, 'message' => 'Tugas OPD berhasil diubah.']);
    }

    /**
     * Ambil data OPD untuk edit (AJAX)
     */
    public function getOpdKlaster($id)
    {
        $user = Session::get('user');
        
        if ($user['role'] !== 'admin') {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $opd = DB::table('perangkat_daerah')
            ->select('id', 'nama', 'singkatan', 'klaster', 'tugas')
            ->where('id', $id)
            ->first();

        if (!$opd) {
            return response()->json(['error' => 'OPD tidak ditemukan.'], 404);
        }

        return response()->json($opd);
    }

        // ════════════════════════════════════════════════════════════
    //  PERIODE PENGISIAN LKE AKIP
    // ════════════════════════════════════════════════════════════

    public function periodeLke(Request $request)
    {
        $user = Session::get('user');

        if ($user['role'] !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $tahun = (int) $request->input('tahun', date('Y'));

        $periode = DB::table('lke_periode')->where('tahun', $tahun)->first();

        $daftarPeriode = DB::table('lke_periode as lp')
            ->leftJoin('pengguna as p', 'lp.updated_by', '=', 'p.id')
            ->select('lp.*', 'p.nama as nama_updater')
            ->orderByDesc('lp.tahun')
            ->get();

        $listTahun = range(date('Y') + 1, date('Y') - 5);

        return view('admin.periode-lke', compact('user', 'tahun', 'periode', 'daftarPeriode', 'listTahun'));
    }

    public function simpanPeriodeLke(Request $request)
    {
        $user = Session::get('user');

        if ($user['role'] !== 'admin') {
            return back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'tahun'           => 'required|integer',
            'tanggal_mulai'   => 'nullable|date',
            'tanggal_selesai' => 'nullable|date',
            'status_manual'   => 'required|in:otomatis,dibuka_paksa,ditutup_paksa',
            'keterangan'      => 'nullable|string|max:255',
        ]);

        $tahun = (int) $request->tahun;

        // ⭐ Status SEBELUM disimpan (untuk deteksi perubahan)
        $statusSebelum = $this->hitungStatusPeriode($tahun);

        $payload = [
            'tanggal_mulai'   => $request->tanggal_mulai ?: null,
            'tanggal_selesai' => $request->tanggal_selesai ?: null,
            'status_manual'   => $request->status_manual,
            'keterangan'      => $request->keterangan ?: null,
            'updated_by'      => $user['id'],
            'updated_at'      => now(),
        ];

        $existing = DB::table('lke_periode')->where('tahun', $tahun)->first();

        if ($existing) {
            DB::table('lke_periode')->where('id', $existing->id)->update($payload);
        } else {
            DB::table('lke_periode')->insert(array_merge($payload, [
                'tahun'      => $tahun,
                'created_at' => now(),
            ]));
        }

        // ⭐ Status SESUDAH disimpan
        $statusSesudah = $this->hitungStatusPeriode($tahun);

        // ⭐ Kirim notifikasi ke operator HANYA jika statusnya benar-benar berubah
        if ($statusSebelum !== $statusSesudah) {
            $this->notifikasiPerubahanPeriodeLke($tahun, $statusSesudah, $request->keterangan);
        }

        return back()->with('success', 'Periode LKE AKIP tahun ' . $tahun . ' berhasil disimpan.');
    }

    /**
     * Hitung status efektif periode (terbuka/tertutup) untuk tahun tertentu.
     * Logic ini SENGAJA disamakan persis dengan LkeController::cekPeriodeLke()
     * supaya deteksi perubahan status akurat dan konsisten dengan yang
     * benar-benar dialami operator.
     */
    private function hitungStatusPeriode(int $tahun): string
    {
        $periode = DB::table('lke_periode')->where('tahun', $tahun)->first();

        if (!$periode) {
            return 'terbuka'; // default aman, sama seperti LkeController
        }

        if ($periode->status_manual === 'ditutup_paksa') {
            return 'tertutup';
        }

        if ($periode->status_manual === 'dibuka_paksa') {
            return 'terbuka';
        }

        $now = now();

        if ($periode->tanggal_mulai && $now->lt($periode->tanggal_mulai)) {
            return 'tertutup';
        }

        if ($periode->tanggal_selesai && $now->gt($periode->tanggal_selesai)) {
            return 'tertutup';
        }

        return 'terbuka';
    }

    /**
     * Kirim notifikasi ke semua operator aktif saat status periode LKE berubah.
     */
    private function notifikasiPerubahanPeriodeLke(int $tahun, string $statusBaru, ?string $keterangan): void
    {
        $operators = DB::table('pengguna')
            ->where('role', 'operator')
            ->where('is_active', 1)
            ->get();

        if ($statusBaru === 'terbuka') {
            $judul = '🔓 Periode Pengisian LKE AKIP Dibuka';
            $pesan = "Periode pengisian LKE AKIP tahun {$tahun} telah dibuka oleh admin. Silakan lengkapi penilaian mandiri Anda.";
            $ikon  = '🔓';
            $warna = 'green';
        } else {
            $judul = '🔒 Periode Pengisian LKE AKIP Ditutup';
            $pesan = "Periode pengisian LKE AKIP tahun {$tahun} telah ditutup oleh admin. Anda tidak dapat lagi mengubah data.";
            $ikon  = '🔒';
            $warna = 'red';
        }

        if ($keterangan) {
            $pesan .= " Catatan: {$keterangan}";
        }

        foreach ($operators as $op) {
            DB::table('notifikasi')->insert([
                'user_id'    => $op->id,
                'judul'      => $judul,
                'pesan'      => $pesan,
                'tipe'       => 'lke_periode',
                'ikon'       => $ikon,
                'warna'      => $warna,
                'url'        => route('evaluasi.lke', ['tahun' => $tahun]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}