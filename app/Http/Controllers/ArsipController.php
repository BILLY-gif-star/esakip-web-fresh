<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ArsipController extends Controller
{
    /**
     * Menampilkan daftar arsip dokumen
     */
    public function index(Request $request)
    {
        try {
            $user = Session::get('user');
            
            // Cek akses: admin atau evaluator
            if (!in_array($user['role'], ['admin', 'evaluator'])) {
                return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
            }
            
            $tahun = $request->input('tahun', date('Y'));
            $opdId = $request->input('opd_id');
            $jenis = $request->input('jenis');
            
            // Query utama untuk ambil data dokumen yang sudah disetujui
            $query = DB::table('dokumen_opd as d')
                ->leftJoin('perangkat_daerah as pd', 'd.perangkat_daerah_id', '=', 'pd.id')
                ->leftJoin('pengguna as p', 'd.uploaded_by', '=', 'p.id')
                ->leftJoin('pengguna as r', 'd.reviewed_by', '=', 'r.id')
                ->select(
                    'd.*', 
                    'pd.nama as nama_opd', 
                    'p.nama as nama_uploader', 
                    'r.nama as nama_reviewer'
                )
                ->where('d.status', 'disetujui')
                ->where('d.tahun', $tahun);
            
            // Filter OPD
            if ($opdId) {
                $query->where('d.perangkat_daerah_id', $opdId);
            }
            
            // Filter jenis dokumen
            if ($jenis) {
                $query->where('d.jenis', $jenis);
            }
            
            $arsip = $query->orderBy('pd.nama')
                ->orderBy('d.jenis')
                ->get();
            
            // Statistik per jenis dokumen
            $statistik = DB::table('dokumen_opd')
                ->select('jenis', DB::raw('COUNT(*) as total'))
                ->where('status', 'disetujui')
                ->where('tahun', $tahun)
                ->groupBy('jenis')
                ->get();
            
            // Jumlah OPD yang sudah upload
            $opdCount = DB::table('dokumen_opd')
                ->where('status', 'disetujui')
                ->where('tahun', $tahun)
                ->distinct('perangkat_daerah_id')
                ->count('perangkat_daerah_id');
            
            // Daftar OPD untuk filter
            $listOpd = DB::table('perangkat_daerah')
                ->orderBy('nama')
                ->get();
            
            // Daftar tahun untuk filter
            $listTahun = DB::table('dokumen_opd')
                ->select('tahun')
                ->distinct()
                ->orderBy('tahun', 'desc')
                ->pluck('tahun')
                ->toArray();
            
            if (empty($listTahun)) {
                $listTahun = [date('Y')];
            }
            
            // Daftar jenis dokumen untuk filter
            $listJenis = DB::table('dokumen_opd')
                ->select('jenis')
                ->where('status', 'disetujui')
                ->distinct()
                ->pluck('jenis')
                ->toArray();
            
            return view('admin.arsip', compact(
                'user', 
                'arsip', 
                'tahun', 
                'statistik', 
                'opdCount', 
                'listOpd', 
                'listTahun',
                'listJenis',
                'opdId',
                'jenis'
            ));
            
        } catch (\Exception $e) {
            Log::error('ArsipController index error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Menampilkan file dokumen
     */
    public function lihat($id)
    {
        try {
            $user = Session::get('user');
            
            // Cek akses
            if (!in_array($user['role'], ['admin', 'evaluator'])) {
                return back()->with('error', 'Akses ditolak.');
            }
            
            $dokumen = DB::table('dokumen_opd')
                ->where('id', $id)
                ->first();
            
            if (!$dokumen) {
                return back()->with('error', 'Dokumen tidak ditemukan.');
            }
            
            $path = storage_path('app/dokumen_opd/' . $dokumen->nama_file);
            
            if (!file_exists($path)) {
                return back()->with('error', 'File tidak ditemukan di server.');
            }
            
            $ext = strtolower(pathinfo($dokumen->nama_file, PATHINFO_EXTENSION));
            
            // Untuk file PDF, tampilkan inline
            if ($ext === 'pdf') {
                return response()->file($path, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $dokumen->nama_file . '"'
                ]);
            }
            
            // Untuk file gambar
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                return response()->file($path, [
                    'Content-Type' => 'image/' . ($ext === 'jpg' ? 'jpeg' : $ext),
                    'Content-Disposition' => 'inline; filename="' . $dokumen->nama_file . '"'
                ]);
            }
            
            // Untuk file lain, download
            return response()->download($path, $dokumen->nama_file);
            
        } catch (\Exception $e) {
            Log::error('ArsipController lihat error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Menampilkan detail arsip (halaman detail)
     */
    public function detail($id)
    {
        try {
            $user = Session::get('user');
            
            // Cek akses: admin atau evaluator
            if (!in_array($user['role'], ['admin', 'evaluator'])) {
                return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
            }
            
            // Ambil detail dokumen
            $arsip = DB::table('dokumen_opd as d')
                ->leftJoin('perangkat_daerah as pd', 'd.perangkat_daerah_id', '=', 'pd.id')
                ->leftJoin('pengguna as p', 'd.uploaded_by', '=', 'p.id')
                ->leftJoin('pengguna as r', 'd.reviewed_by', '=', 'r.id')
                ->select(
                    'd.*', 
                    'pd.nama as opd_nama', 
                    'p.nama as nama_uploader', 
                    'r.nama as nama_reviewer'
                )
                ->where('d.id', $id)
                ->first();
            
            if (!$arsip) {
                return redirect()->route('admin.arsip')->with('error', 'Arsip tidak ditemukan.');
            }
            
            // Decode data penilaian jika ada (JSON)
            $dataPenilaian = [];
            if (!empty($arsip->data_penilaian)) {
                $dataPenilaian = json_decode($arsip->data_penilaian, true);
            }
            
            // Ambil riwayat review (catatan dari reviewer)
            $riwayatReview = [];
            if (DB::getSchemaBuilder()->hasTable('review_logs')) {
                $riwayatReview = DB::table('review_logs')
                    ->where('dokumen_id', $id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
            
            return view('admin.detail-arsip', compact(
                'user', 
                'arsip', 
                'dataPenilaian',
                'riwayatReview'
            ));
            
        } catch (\Exception $e) {
            Log::error('ArsipController detail error: ' . $e->getMessage());
            return redirect()->route('admin.arsip')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Menghapus dokumen dari arsip
     */
    public function hapus($id)
    {
        try {
            $user = Session::get('user');
            
            // Hanya admin yang bisa hapus
            if ($user['role'] !== 'admin') {
                return back()->with('error', 'Hanya admin yang dapat menghapus arsip.');
            }
            
            $dokumen = DB::table('dokumen_opd')->where('id', $id)->first();
            
            if (!$dokumen) {
                return back()->with('error', 'Dokumen tidak ditemukan.');
            }
            
            // Hapus file fisik
            $path = storage_path('app/dokumen_opd/' . $dokumen->nama_file);
            if (file_exists($path)) {
                @unlink($path);
            }
            
            // Hapus record dari database
            DB::table('dokumen_opd')->where('id', $id)->delete();
            
            // Catat log aktivitas
            Log::info('Dokumen dihapus oleh ' . $user['nama'] . ' (ID: ' . $id . ')');
            
            return redirect()->route('admin.arsip')->with('success', 'Dokumen berhasil dihapus dari arsip.');
            
        } catch (\Exception $e) {
            Log::error('ArsipController hapus error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Export arsip ke Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $user = Session::get('user');
            
            if ($user['role'] !== 'admin') {
                return back()->with('error', 'Akses ditolak.');
            }
            
            $tahun = $request->input('tahun', date('Y'));
            
            $data = DB::table('dokumen_opd as d')
                ->leftJoin('perangkat_daerah as pd', 'd.perangkat_daerah_id', '=', 'pd.id')
                ->leftJoin('pengguna as p', 'd.uploaded_by', '=', 'p.id')
                ->select('pd.nama as opd', 'd.jenis', 'd.nama_file', 'd.tahun', 'p.nama as uploader', 'd.created_at')
                ->where('d.status', 'disetujui')
                ->where('d.tahun', $tahun)
                ->orderBy('pd.nama')
                ->get();
            
            // Generate Excel (implementasi sesuai kebutuhan)
            // return Excel::download(new ArsipExport($data), 'arsip_' . $tahun . '.xlsx');
            
            return back()->with('info', 'Fitur export Excel sedang dalam pengembangan.');
            
        } catch (\Exception $e) {
            Log::error('ArsipController export error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}