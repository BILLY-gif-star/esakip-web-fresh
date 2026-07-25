<?php
// app/Http/Controllers/LkipController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Models\LkipPerangkatDaerah;

class LkipController extends Controller
{
    private function tahunEvaluasi(): int
    {
        return (int) date('Y') - 1;
    }
    
    private function listTahun(): array
    {
        $base = $this->tahunEvaluasi();
        return range($base, $base - 5);
    }
    
    public function index(Request $request)
    {
        $user = Session::get('user');
        $tahun = (int) $request->input('tahun', $this->tahunEvaluasi());
        
        if ($user['role'] === 'operator') {
            $opdId = $user['daerah_id'];
        } else {
            $opdId = $request->input('opd_id');
        }
        
        $listOpd = DB::table('perangkat_daerah')->orderBy('nama')->get();
        $listTahun = $this->listTahun();
        
        // Ambil template LKIP
        $template = LkipPerangkatDaerah::where('is_template', 1)
            ->where('tahun', $tahun)
            ->first();
        
        $lkipData = null;
        $statusHistori = collect();
        $dokOpd = null;
        
        if ($opdId) {
            $lkipData = LkipPerangkatDaerah::where('perangkat_daerah_id', $opdId)
                ->where('tahun', $tahun)
                ->where('is_template', 0)
                ->first();
            
            if ($user['role'] === 'operator') {
                $dokOpd = $lkipData;
            }
            
            $statusHistori = LkipPerangkatDaerah::where('perangkat_daerah_id', $opdId)
                ->where('is_template', 0)
                ->orderBy('tahun', 'desc')
                ->get();
        }
        
        // Untuk admin, ambil semua data OPD yang sudah upload
        if ($user['role'] === 'admin') {
            $listOpd = DB::table('perangkat_daerah')
                ->leftJoin('lkip_perangkat_daerah', function($join) use ($tahun) {
                    $join->on('perangkat_daerah.id', '=', 'lkip_perangkat_daerah.perangkat_daerah_id')
                         ->where('lkip_perangkat_daerah.tahun', '=', $tahun)
                         ->where('lkip_perangkat_daerah.is_template', '=', 0);
                })
                ->leftJoin('pengguna', function($join) {
                    $join->on('perangkat_daerah.id', '=', 'pengguna.perangkat_daerah_id')
                         ->where('pengguna.role', '=', 'operator');
                })
                ->select(
                    'perangkat_daerah.id',
                    'perangkat_daerah.nama as nama_opd',
                    'pengguna.nama as nama_user',
                    'lkip_perangkat_daerah.id as dokumen_id',
                    'lkip_perangkat_daerah.judul_dokumen',
                    'lkip_perangkat_daerah.deskripsi',
                    'lkip_perangkat_daerah.file_name',
                    'lkip_perangkat_daerah.file_path',
                    'lkip_perangkat_daerah.status',
                    'lkip_perangkat_daerah.catatan_review',
                    'lkip_perangkat_daerah.updated_at',
                    'lkip_perangkat_daerah.created_at'
                )
                ->orderBy('perangkat_daerah.nama')
                ->get();
        }
        
        return view('lkip.index', compact(
            'user', 'tahun', 'opdId', 'listOpd', 'listTahun',
            'lkipData', 'statusHistori', 'template', 'dokOpd'
        ));
    }
    
    public function upload(Request $request)
    {
        $user = Session::get('user');
        
        $request->validate([
            'tahun' => 'required|integer',
            'judul_dokumen' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:20480',
        ]);
        
        $isTemplate = $request->has('is_template') ? 1 : 0;
        
        if ($user['role'] === 'operator') {
            $opdId = $user['daerah_id'];
        } else {
            $opdId = $request->input('opd_id');
        }
        
        if (!$opdId && !$isTemplate) {
            return back()->with('error', 'OPD tidak ditemukan.');
        }
        
        $tahun = $request->tahun;
        
        // Cek apakah ini upload template
        if ($isTemplate) {
            $existing = LkipPerangkatDaerah::where('is_template', 1)
                ->where('tahun', $tahun)
                ->first();
        } else {
            $existing = LkipPerangkatDaerah::where('perangkat_daerah_id', $opdId)
                ->where('tahun', $tahun)
                ->where('is_template', 0)
                ->first();
        }
        
        if ($existing && $user['role'] !== 'admin' && !$isTemplate) {
            return back()->with('error', 'LKIP untuk tahun ' . $tahun . ' sudah diupload. Silakan hubungi admin untuk perubahan.');
        }
        
        $file = $request->file('file');
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
        
        if ($isTemplate) {
            $filePath = $file->storeAs('lkip/templates/' . $tahun, $fileName, 'public');
        } else {
            $filePath = $file->storeAs('lkip/' . $tahun . '/' . $opdId, $fileName, 'public');
        }
        
        $fileSize = $file->getSize();
        
        if ($user['role'] === 'admin') {
            $status = $isTemplate ? 'disetujui' : 'disetujui';
        } else {
            $status = 'dikirim';
        }
        
        $data = [
            'tahun' => $tahun,
            'judul_dokumen' => $request->judul_dokumen,
            'deskripsi' => $request->deskripsi,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $fileSize,
            'status' => $status,
            'uploaded_by' => $user['id'],
            'is_template' => $isTemplate ? 1 : 0,
            'updated_at' => now(),
        ];
        
        if (!$isTemplate) {
            $data['perangkat_daerah_id'] = $opdId;
        }
        
        if ($existing) {
            if ($existing->file_path && Storage::disk('public')->exists($existing->file_path)) {
                Storage::disk('public')->delete($existing->file_path);
            }
            $existing->update($data);
            $message = $isTemplate ? 'Template LKIP berhasil diperbarui!' : 'LKIP berhasil diperbarui!';
        } else {
            $data['created_at'] = now();
            LkipPerangkatDaerah::create($data);
            $message = $isTemplate ? 'Template LKIP berhasil diupload!' : 'LKIP berhasil diupload!';
        }
        
        // Kirim notifikasi jika operator upload
        if ($user['role'] === 'operator' && !$isTemplate) {
            $opd = DB::table('perangkat_daerah')->where('id', $opdId)->first();
            $namaOpd = $opd->nama ?? 'OPD';
            
            $adminUsers = DB::table('pengguna')->where('role', 'admin')->get();
            
            foreach ($adminUsers as $admin) {
                DB::table('notifikasi')->insert([
                    'user_id' => $admin->id,
                    'judul' => 'Upload LKIP Baru',
                    'pesan' => "Operator {$namaOpd} mengupload LKIP tahun {$tahun}",
                    'tipe' => 'lkip_upload',
                    'ikon' => '📄',
                    'warna' => 'indigo',
                    'url' => route('lkip.index') . '?tahun=' . $tahun . '&opd_id=' . $opdId,
                    'nama_opd' => $namaOpd,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        if ($isTemplate) {
            return redirect()->route('lkip.index', ['tahun' => $tahun])
                ->with('success', $message);
        }
        
        return redirect()->route('lkip.index', ['tahun' => $tahun, 'opd_id' => $opdId])
            ->with('success', $message);
    }
    
    public function review(Request $request, $id)
    {
        $user = Session::get('user');
        
        if ($user['role'] !== 'admin') {
            return back()->with('error', 'Hanya admin yang dapat melakukan review.');
        }
        
        $request->validate([
            'status' => 'required|in:disetujui,ditolak,dikirim,draft',
            'catatan_review' => 'nullable|string',
        ]);
        
        $lkip = LkipPerangkatDaerah::findOrFail($id);
        
        $lkip->update([
            'status' => $request->status,
            'catatan_review' => $request->catatan_review,
            'reviewed_by' => $user['id'],
            'reviewed_at' => now(),
        ]);
        
        // Kirim notifikasi ke operator
        $operator = DB::table('pengguna')
            ->where('perangkat_daerah_id', $lkip->perangkat_daerah_id)
            ->where('role', 'operator')
            ->first();
        
        if ($operator) {
            $opd = DB::table('perangkat_daerah')->where('id', $lkip->perangkat_daerah_id)->first();
            $statusText = $request->status === 'disetujui' ? 'disetujui' : ($request->status === 'ditolak' ? 'ditolak' : 'diperbarui');
            
            DB::table('notifikasi')->insert([
                'user_id' => $operator->id,
                'judul' => 'Review LKIP',
                'pesan' => "LKIP tahun {$lkip->tahun} {$statusText} oleh admin." . ($request->catatan_review ? " Catatan: {$request->catatan_review}" : ''),
                'tipe' => 'lkip_review',
                'ikon' => $request->status === 'disetujui' ? '✅' : ($request->status === 'ditolak' ? '❌' : '✏️'),
                'warna' => $request->status === 'disetujui' ? 'green' : ($request->status === 'ditolak' ? 'red' : 'yellow'),
                'url' => route('lkip.index') . '?tahun=' . $lkip->tahun . '&opd_id=' . $lkip->perangkat_daerah_id,
                'nama_opd' => $opd->nama ?? 'OPD',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        return back()->with('success', 'LKIP berhasil direview.');
    }
    
    public function download($id)
    {
        $user = Session::get('user');
        $lkip = LkipPerangkatDaerah::findOrFail($id);
        
        // Template bisa diakses semua user
        if ($lkip->is_template != 1) {
            if ($user['role'] === 'operator' && $lkip->perangkat_daerah_id != $user['daerah_id']) {
                abort(403);
            }
        }
        
        $path = storage_path('app/public/' . $lkip->file_path);
        
        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }
        
        return response()->download($path, $lkip->file_name);
    }
    
    public function preview($id)
    {
        $user = Session::get('user');
        $lkip = LkipPerangkatDaerah::findOrFail($id);
        
        // Template bisa diakses semua user
        if ($lkip->is_template != 1) {
            if ($user['role'] === 'operator' && $lkip->perangkat_daerah_id != $user['daerah_id']) {
                abort(403);
            }
        }
        
        $path = storage_path('app/public/' . $lkip->file_path);
        
        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }
        
        $extension = strtolower(pathinfo($lkip->file_name, PATHINFO_EXTENSION));
        
        if ($extension === 'pdf') {
            return response()->file($path, ['Content-Type' => 'application/pdf']);
        } else {
            return response()->download($path, $lkip->file_name);
        }
    }
    
    public function hapus($id)
    {
        $user = Session::get('user');
        $lkip = LkipPerangkatDaerah::findOrFail($id);
        
        // Template hanya bisa dihapus admin
        if ($lkip->is_template == 1) {
            if ($user['role'] !== 'admin') {
                abort(403);
            }
        } else {
            if ($user['role'] === 'operator') {
                if ($lkip->perangkat_daerah_id != $user['daerah_id']) {
                    abort(403);
                }
                if ($lkip->status !== 'draft' && $lkip->status !== 'dikirim') {
                    return back()->with('error', 'Hanya dokumen dengan status Draft atau Dikirim yang dapat dihapus.');
                }
            }
        }
        
        if ($lkip->file_path && Storage::disk('public')->exists($lkip->file_path)) {
            Storage::disk('public')->delete($lkip->file_path);
        }
        
        $lkip->delete();
        
        return redirect()->route('lkip.index')
            ->with('success', $lkip->is_template ? 'Template LKIP berhasil dihapus.' : 'LKIP berhasil dihapus.');
    }
}