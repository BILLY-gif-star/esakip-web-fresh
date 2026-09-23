<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Concerns\PerjanjianHelpers;

class PerjanjianController extends Controller
{
    use PerjanjianHelpers;

    // ════════════════════════════════════════════════════
    //  INDEX REGULER
    // ════════════════════════════════════════════════════
    public function index(Request $request, $jenis)
    {
        $config = $this->jenisConfig();

        if (!isset($config[$jenis])) {
            abort(404, 'Jenis dokumen tidak dikenal.');
        }

        $user      = Session::get('user');
        $isAdmin   = $user['role'] === 'admin';
        $cfg       = $config[$jenis];
        $listTahun = $this->listTahun();
        $isDual    = $jenis === 'perjanjian-kinerja'; // ⭐ BARU — jenis ini punya 2 file

        $tahun = (int) $request->input('tahun', date('Y'));

        $template = DB::table('perjanjian_kinerja')
            ->where('jenis', $cfg['key'])
            ->where('tahun', $tahun)
            ->whereNotNull('nama_file')
            ->first();

        if ($template && $template->nama_file) {
            $templatePath          = storage_path('app/templates/' . $template->nama_file);
            $template->file_exists = file_exists($templatePath);
        }

        // ── ⭐ BARU — perjanjian-kinerja: 2 dokumen terpisah (Murni & Revisi) ──
        $dokMurni  = null;
        $dokRevisi = null;
        $dokOpd    = null; // dipertahankan untuk jenis lain (non-dual)

        if ($user['role'] === 'operator') {
            if ($isDual) {
                $dokMurni = DB::table('dokumen_opd')
                    ->where('perangkat_daerah_id', $user['daerah_id'])
                    ->where('jenis', $cfg['opd'])
                    ->where('tahun', $tahun)
                    ->where(function ($q) {
                        $q->where('sub_jenis', 'murni')->orWhereNull('sub_jenis'); // data lama tanpa sub_jenis dianggap "murni"
                    })
                    ->orderByDesc('id')
                    ->first();

                $dokRevisi = DB::table('dokumen_opd')
                    ->where('perangkat_daerah_id', $user['daerah_id'])
                    ->where('jenis', $cfg['opd'])
                    ->where('tahun', $tahun)
                    ->where('sub_jenis', 'revisi')
                    ->first();

                foreach ([$dokMurni, $dokRevisi] as $d) {
                    if ($d && $d->nama_file) {
                        $d->file_exists = file_exists(storage_path('app/dokumen_opd/' . $d->nama_file));
                    }
                }
            } else {
                $dokOpd = DB::table('dokumen_opd')
                    ->where('perangkat_daerah_id', $user['daerah_id'])
                    ->where('jenis', $cfg['opd'])
                    ->where('tahun', $tahun)
                    ->first();

                if ($dokOpd && $dokOpd->nama_file) {
                    $filePath            = storage_path('app/dokumen_opd/' . $dokOpd->nama_file);
                    $dokOpd->file_exists = file_exists($filePath);
                }
            }
        }

        // ── LIST OPD (admin) ──
        $listOpd     = [];
        $listOpdDual = []; // ⭐ BARU — grouped per OPD untuk tampilan dual

        if ($user['role'] === 'admin') {
            if ($isDual) {
                $rows = DB::table('dokumen_opd as d')
                    ->leftJoin('perangkat_daerah as pd', 'd.perangkat_daerah_id', '=', 'pd.id')
                    ->leftJoin('pengguna as p', 'd.uploaded_by', '=', 'p.id')
                    ->select('d.*', 'pd.nama as nama_opd', 'p.nama as nama_user')
                    ->where('d.jenis', $cfg['opd'])
                    ->where('d.tahun', $tahun)
                    ->orderBy('pd.nama')
                    ->get();

                foreach ($rows as $dok) {
                    $dok->file_exists = file_exists(storage_path('app/dokumen_opd/' . $dok->nama_file));
                }

                $listOpdDual = $rows->groupBy('perangkat_daerah_id');
            } else {
                $listOpd = DB::table('dokumen_opd as d')
                    ->leftJoin('perangkat_daerah as pd', 'd.perangkat_daerah_id', '=', 'pd.id')
                    ->leftJoin('pengguna as p', 'd.uploaded_by', '=', 'p.id')
                    ->select('d.*', 'pd.nama as nama_opd', 'p.nama as nama_user')
                    ->where('d.jenis', $cfg['opd'])
                    ->where('d.tahun', $tahun)
                    ->orderByRaw("FIELD(d.status,'menunggu','ditolak','disetujui')")
                    ->orderBy('pd.nama')
                    ->get();

                foreach ($listOpd as $dok) {
                    $filePath         = storage_path('app/dokumen_opd/' . $dok->nama_file);
                    $dok->file_exists = file_exists($filePath);
                }
            }
        }

        $stats = [
            'total'     => $listOpd ? count($listOpd) : 0,
            'disetujui' => $listOpd ? $listOpd->where('status', 'disetujui')->count() : 0,
            'menunggu'  => $listOpd ? $listOpd->where('status', 'menunggu')->count() : 0,
            'ditolak'   => $listOpd ? $listOpd->where('status', 'ditolak')->count() : 0,
        ];

        return view('perjanjian.index', compact(
            'user', 'isAdmin', 'jenis', 'cfg', 'listTahun',
            'tahun', 'template', 'dokOpd', 'listOpd', 'stats',
            'isDual', 'dokMurni', 'dokRevisi', 'listOpdDual' // ⭐ BARU
        ));
    }

    // ════════════════════════════════════════════════════
    //  UPLOAD DUAL — khusus Perjanjian Kinerja (Murni + Revisi)
    // ════════════════════════════════════════════════════
    public function uploadDual(Request $request)
    {
        $user = Session::get('user');

        if (!$user || $user['role'] !== 'operator') {
            return back()->with('error', 'Hanya operator yang dapat mengupload dokumen.');
        }

        $request->validate([
            'tahun'              => 'required|integer',
            'file_murni'         => 'nullable|file|mimes:pdf,docx,xlsx,doc,xls|max:20480',
            'file_revisi'        => 'nullable|file|mimes:pdf,docx,xlsx,doc,xls|max:20480',
            'keterangan'         => 'nullable|string|max:500',
        ]);

        if (!$request->hasFile('file_murni') && !$request->hasFile('file_revisi')) {
            return back()->with('error', 'Pilih minimal salah satu file (Murni atau Revisi) untuk diupload.');
        }

        $opdId  = $user['daerah_id'];
        $tahun  = $request->tahun;
        $jenis  = 'perjanjian_kinerja_opd';
        $folder = storage_path('app/dokumen_opd');

        if (!file_exists($folder)) mkdir($folder, 0777, true);

        $simpanFile = function ($fileKey, $subJenis) use ($request, $opdId, $tahun, $jenis, $folder, $user) {
            if (!$request->hasFile($fileKey)) return;

            $file  = $request->file($fileKey);
            $ext   = $file->getClientOriginalExtension();
            $fname = 'perjanjian_kinerja_' . $subJenis . '_' . $opdId . '_' . $tahun . '_' . time() . '.' . $ext;

            // cari record lama (termasuk data lama tanpa sub_jenis, kalau subJenis = murni)
            $query = DB::table('dokumen_opd')
                ->where('perangkat_daerah_id', $opdId)
                ->where('jenis', $jenis)
                ->where('tahun', $tahun);

            if ($subJenis === 'murni') {
                $query->where(function ($q) {
                    $q->where('sub_jenis', 'murni')->orWhereNull('sub_jenis');
                });
            } else {
                $query->where('sub_jenis', $subJenis);
            }

            $existing = $query->first();

            if ($existing) {
                $oldPath = storage_path('app/dokumen_opd/' . $existing->nama_file);
                if (file_exists($oldPath)) @unlink($oldPath);
            }

            $file->move($folder, $fname);

            if ($existing) {
                DB::table('dokumen_opd')->where('id', $existing->id)->update([
                    'sub_jenis'   => $subJenis, // pastikan data lama ikut ter-normalisasi
                    'nama_file'   => $fname,
                    'path_file'   => 'dokumen_opd/' . $fname,
                    'keterangan'  => $request->keterangan ?? '',
                    'uploaded_by' => $user['id'],
                    'status'      => 'menunggu',
                    'updated_at'  => now(),
                ]);
            } else {
                DB::table('dokumen_opd')->insert([
                    'perangkat_daerah_id' => $opdId,
                    'jenis'       => $jenis,
                    'sub_jenis'   => $subJenis,
                    'tahun'       => $tahun,
                    'nama_file'   => $fname,
                    'path_file'   => 'dokumen_opd/' . $fname,
                    'keterangan'  => $request->keterangan ?? '',
                    'uploaded_by' => $user['id'],
                    'status'      => 'menunggu',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        };

        $simpanFile('file_murni', 'murni');
        $simpanFile('file_revisi', 'revisi');

        // Notifikasi ke admin
        $opd     = DB::table('perangkat_daerah')->where('id', $opdId)->first();
        $namaOpd = $opd->nama ?? 'OPD';

        $label = [];
        if ($request->hasFile('file_murni'))  $label[] = 'Perjanjian Kinerja Murni';
        if ($request->hasFile('file_revisi')) $label[] = 'Perjanjian Kinerja Revisi';

        foreach (DB::table('pengguna')->where('role', 'admin')->get() as $admin) {
            DB::table('notifikasi')->insert([
                'user_id'    => $admin->id,
                'judul'      => 'Upload Perjanjian Kinerja',
                'pesan'      => "Operator {$namaOpd} mengupload " . implode(' & ', $label) . " untuk tahun {$tahun}. Status: menunggu persetujuan.",
                'tipe'       => 'dokumen_upload',
                'ikon'       => '🤝',
                'warna'      => 'amber',
                'url'        => route('perjanjian.index', 'perjanjian-kinerja'),
                'nama_opd'   => $namaOpd,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('perjanjian.index', ['jenis' => 'perjanjian-kinerja', 'tahun' => $tahun])
            ->with('success', 'Dokumen berhasil diupload!');
    }
}