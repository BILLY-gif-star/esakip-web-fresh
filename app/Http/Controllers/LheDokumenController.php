<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LheDokumenController extends Controller
{
    // ════════════════════════════════════════════════════
    //  INDEX
    // ════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $user  = Session::get('user');
        $tahun = (int) $request->input('tahun', date('Y'));

        $listOpd = DB::table('perangkat_daerah')->orderBy('nama')->get();

        if ($user['role'] === 'operator') {
            $opdId = $user['daerah_id'];

            $dokumen = DB::table('lhe_dokumen as d')
                ->leftJoin('pengguna as p', 'd.uploaded_by', '=', 'p.id')
                ->select('d.*', 'p.nama as nama_uploader')
                ->where('d.perangkat_daerah_id', $opdId)
                ->where('d.tahun', $tahun)
                ->orderByDesc('d.created_at')
                ->get();

            foreach ($dokumen as $dok) {
                $dok->file_exists = file_exists(storage_path('app/lhe_dokumen/' . $dok->nama_file));
            }

        } else {
            // admin & evaluator: lihat semua OPD
            $opdId = $request->input('opd_id');

            $query = DB::table('lhe_dokumen as d')
                ->leftJoin('perangkat_daerah as pd', 'd.perangkat_daerah_id', '=', 'pd.id')
                ->leftJoin('pengguna as p', 'd.uploaded_by', '=', 'p.id')
                ->select('d.*', 'pd.nama as nama_opd', 'p.nama as nama_uploader')
                ->where('d.tahun', $tahun)
                ->orderBy('pd.nama')
                ->orderByDesc('d.created_at');

            if ($opdId) {
                $query->where('d.perangkat_daerah_id', $opdId);
            }

            $dokumen = $query->get();

            foreach ($dokumen as $dok) {
                $dok->file_exists = file_exists(storage_path('app/lhe_dokumen/' . $dok->nama_file));
            }
        }

        $listTahun = range(date('Y') + 1, date('Y') - 5);

        return view('lhe-dokumen.index', compact(
            'user', 'tahun', 'listTahun', 'listOpd', 'opdId', 'dokumen'
        ));
    }

    // ════════════════════════════════════════════════════
    //  UPLOAD (admin only)
    // ════════════════════════════════════════════════════
    public function upload(Request $request)
    {
        $user = Session::get('user');

        if ($user['role'] !== 'admin') {
            return back()->with('error', 'Hanya admin yang dapat mengupload dokumen LHE.');
        }

        $request->validate([
            'perangkat_daerah_id' => 'required|exists:perangkat_daerah,id',
            'tahun'               => 'required|integer',
            'file'                => 'required|file|mimes:pdf,doc,docx|max:20480',
            'keterangan'          => 'nullable|string|max:255',
        ]);

        $opdId  = $request->perangkat_daerah_id;
        $tahun  = $request->tahun;
        $folder = storage_path('app/lhe_dokumen');

        if (!file_exists($folder)) mkdir($folder, 0777, true);

        $file  = $request->file('file');
        $ext   = $file->getClientOriginalExtension();
        $fname = 'lhe_' . $opdId . '_' . $tahun . '_' . time() . '.' . $ext;

        $file->move($folder, $fname);

        DB::table('lhe_dokumen')->insert([
            'perangkat_daerah_id' => $opdId,
            'tahun'               => $tahun,
            'nama_file'           => $fname,
            'path_file'           => 'lhe_dokumen/' . $fname,
            'keterangan'          => $request->keterangan ?: null,
            'uploaded_by'         => $user['id'],
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        // Notifikasi ke operator OPD terkait
        $operator = DB::table('pengguna')
            ->where('perangkat_daerah_id', $opdId)
            ->where('role', 'operator')
            ->first();

        if ($operator) {
            $opd     = DB::table('perangkat_daerah')->where('id', $opdId)->first();
            $namaOpd = $opd->nama ?? 'OPD';

            DB::table('notifikasi')->insert([
                'user_id'    => $operator->id,
                'judul'      => '📄 Dokumen LHE AKIP Tersedia',
                'pesan'      => "Admin telah mengupload dokumen LHE AKIP tahun {$tahun} untuk {$namaOpd}. Silakan unduh di menu Evaluasi Kinerja → LHE Dokumen.",
                'tipe'       => 'lhe_dokumen',
                'ikon'       => '📄',
                'warna'      => 'green',
                'url'        => route('evaluasi.lhe-dokumen', ['tahun' => $tahun]),
                'nama_opd'   => $namaOpd,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('success', 'Dokumen LHE AKIP berhasil diupload.');
    }

    // ════════════════════════════════════════════════════
    //  LIHAT / PREVIEW
    // ════════════════════════════════════════════════════
    public function lihat($id)
    {
        $user = Session::get('user');
        $dok  = DB::table('lhe_dokumen')->find($id);

        if (!$dok) abort(404);

        if ($user['role'] === 'operator' && $dok->perangkat_daerah_id != $user['daerah_id']) {
            abort(403);
        }

        $path = storage_path('app/lhe_dokumen/' . $dok->nama_file);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        $ext  = strtolower(pathinfo($dok->nama_file, PATHINFO_EXTENSION));
        $mime = ['pdf' => 'application/pdf'];

        return isset($mime[$ext])
            ? response()->file($path, ['Content-Type' => $mime[$ext]])
            : response()->download($path, $dok->nama_file);
    }

    // ════════════════════════════════════════════════════
    //  UNDUH
    // ════════════════════════════════════════════════════
    public function unduh($id)
    {
        $user = Session::get('user');
        $dok  = DB::table('lhe_dokumen')->find($id);

        if (!$dok) abort(404);

        if ($user['role'] === 'operator' && $dok->perangkat_daerah_id != $user['daerah_id']) {
            abort(403);
        }

        $path = storage_path('app/lhe_dokumen/' . $dok->nama_file);

        if (!file_exists($path)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        return response()->download($path, $dok->nama_file);
    }

    // ════════════════════════════════════════════════════
    //  HAPUS (admin only)
    // ════════════════════════════════════════════════════
    public function hapus($id)
    {
        $user = Session::get('user');

        if ($user['role'] !== 'admin') {
            return back()->with('error', 'Hanya admin yang dapat menghapus dokumen.');
        }

        $dok = DB::table('lhe_dokumen')->find($id);
        if (!$dok) {
            return back()->with('error', 'Dokumen tidak ditemukan.');
        }

        $path = storage_path('app/lhe_dokumen/' . $dok->nama_file);
        if (file_exists($path)) {
            @unlink($path);
        }

        DB::table('lhe_dokumen')->where('id', $id)->delete();

        return back()->with('success', 'Dokumen LHE AKIP berhasil dihapus.');
    }
}