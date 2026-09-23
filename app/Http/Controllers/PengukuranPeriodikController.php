<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengukuranPeriodik;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PengukuranPeriodikController extends Controller
{
    // ════════════════════════════════════════════════════
    //  INDEX
    // ════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $user  = Session::get('user');
        $tahun = (int) $request->input('tahun', date('Y'));

        if ($user['role'] === 'operator') {
            $opdId = $user['daerah_id'];
        } else {
            $opdId = $request->input('opd_id');
        }

        $listOpd   = DB::table('perangkat_daerah')->orderBy('nama')->get();
        $listTahun = range(date('Y'), date('Y') - 5);
        $data      = collect();

        if ($opdId) {
            $rawData = DB::table('pengukuran_periodik')
                ->where('perangkat_daerah_id', $opdId)
                ->where('tahun', $tahun)
                ->orderBy('sasaran_strategis')
                ->orderBy('indikator')
                ->orderBy('id')
                ->get()
                ->unique(function ($item) {
                    return $item->sasaran_strategis . '|||' . $item->indikator;
                })
                ->values();

            $data = $rawData->groupBy('sasaran_strategis');
        }

        return view('pengukuran.periodik', compact(
            'user', 'tahun', 'opdId', 'listOpd', 'listTahun', 'data'
        ));
    }

    // ════════════════════════════════════════════════════
    //  GET DATA SINGLE — untuk modal edit indikator (AJAX)
    // ════════════════════════════════════════════════════
    public function getData($id)
    {
        $user = Session::get('user');

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $query = DB::table('pengukuran_periodik')->where('id', $id);

        // Operator hanya bisa akses data miliknya
        if ($user['role'] === 'operator') {
            $query->where('perangkat_daerah_id', $user['daerah_id']);
        }

        $row = $query->first();

        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        return response()->json([
            'id'               => $row->id,
            'indikator'        => $row->indikator,
            'satuan'           => $row->satuan,
            'sasaran_program'  => $row->sasaran_program,
            'penanggung_jawab' => $row->penanggung_jawab,
        ]);
    }

    // ════════════════════════════════════════════════════
    //  UPDATE INDIKATOR — edit nama, satuan, dsb (AJAX)
    // ════════════════════════════════════════════════════
    public function updateIndikator(Request $request, $id)
    {
        $user = Session::get('user');

        if (!$user || $user['role'] !== 'operator') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $row = DB::table('pengukuran_periodik')
            ->where('id', $id)
            ->where('perangkat_daerah_id', $user['daerah_id'])
            ->first();

        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        $indikator = trim($request->input('indikator', ''));
        if (!$indikator) {
            return response()->json(['success' => false, 'message' => 'Indikator kinerja wajib diisi.'], 422);
        }

        DB::table('pengukuran_periodik')
            ->where('id', $id)
            ->update([
                'indikator'        => $indikator,
                'satuan'           => $request->input('satuan'),
                'sasaran_program'  => $request->input('sasaran_program'),
                'penanggung_jawab' => $request->input('penanggung_jawab'),
                'updated_at'       => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Indikator berhasil diperbarui.'
        ]);
    }

    // ════════════════════════════════════════════════════
    //  DELETE INDIKATOR (AJAX)
    // ════════════════════════════════════════════════════
    public function deleteIndikator($id)
    {
        $user = Session::get('user');

        if (!$user || $user['role'] !== 'operator') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $row = DB::table('pengukuran_periodik')
            ->where('id', $id)
            ->where('perangkat_daerah_id', $user['daerah_id'])
            ->first();

        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        // Hapus file fisik jika ada
        if ($row->file_bukti) {
            $path = storage_path('app/public/' . $row->file_bukti);
            if (file_exists($path)) @unlink($path);
        }

        DB::table('pengukuran_periodik')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Indikator berhasil dihapus.'
        ]);
    }

    // ════════════════════════════════════════════════════
    //  UPDATE SASARAN STRATEGIS (AJAX)
    //  Mengubah nama sasaran untuk semua indikator terkait
    // ════════════════════════════════════════════════════
    public function updateSasaran(Request $request)
    {
        $user = Session::get('user');

        if (!$user || $user['role'] !== 'operator') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $sasaranLama = trim($request->input('sasaran_lama', ''));
        $sasaranBaru = trim($request->input('sasaran_baru', ''));
        $tahun       = (int) $request->input('tahun', date('Y'));
        $opdId       = $user['daerah_id']; // selalu gunakan OPD dari session

        if (!$sasaranBaru) {
            return response()->json(['success' => false, 'message' => 'Sasaran strategis baru wajib diisi.'], 422);
        }

        if ($sasaranLama === $sasaranBaru) {
            return response()->json(['success' => false, 'message' => 'Sasaran tidak berubah.'], 422);
        }

        // Pastikan sasaran lama memang milik operator ini
        $count = DB::table('pengukuran_periodik')
            ->where('perangkat_daerah_id', $opdId)
            ->where('tahun', $tahun)
            ->where('sasaran_strategis', $sasaranLama)
            ->count();

        if ($count === 0) {
            return response()->json(['success' => false, 'message' => 'Sasaran tidak ditemukan.'], 404);
        }

        // Cek apakah sasaran baru sudah ada (hindari duplikat)
        $duplikat = DB::table('pengukuran_periodik')
            ->where('perangkat_daerah_id', $opdId)
            ->where('tahun', $tahun)
            ->where('sasaran_strategis', $sasaranBaru)
            ->exists();

        if ($duplikat) {
            return response()->json([
                'success' => false,
                'message' => 'Sasaran "' . $sasaranBaru . '" sudah ada. Gunakan nama lain.'
            ], 422);
        }

        // Update semua indikator dengan sasaran lama → sasaran baru
        $updated = DB::table('pengukuran_periodik')
            ->where('perangkat_daerah_id', $opdId)
            ->where('tahun', $tahun)
            ->where('sasaran_strategis', $sasaranLama)
            ->update([
                'sasaran_strategis' => $sasaranBaru,
                'updated_at'        => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => "Sasaran berhasil diperbarui. {$updated} indikator terpengaruh."
        ]);
    }

    // ════════════════════════════════════════════════════
    //  DELETE SASARAN STRATEGIS (AJAX)
    //  Menghapus semua indikator dalam sasaran tersebut
    // ════════════════════════════════════════════════════
    public function deleteSasaran(Request $request)
    {
        $user = Session::get('user');

        if (!$user || $user['role'] !== 'operator') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $sasaran = trim($request->input('sasaran', ''));
        $tahun   = (int) $request->input('tahun', date('Y'));
        $opdId   = $user['daerah_id']; // selalu gunakan OPD dari session

        if (!$sasaran) {
            return response()->json(['success' => false, 'message' => 'Sasaran tidak boleh kosong.'], 422);
        }

        // Ambil semua row yang akan dihapus (untuk hapus file fisik)
        $rows = DB::table('pengukuran_periodik')
            ->where('perangkat_daerah_id', $opdId)
            ->where('tahun', $tahun)
            ->where('sasaran_strategis', $sasaran)
            ->get();

        if ($rows->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Sasaran tidak ditemukan.'], 404);
        }

        // Hapus file fisik masing-masing indikator
        foreach ($rows as $row) {
            if ($row->file_bukti) {
                $path = storage_path('app/public/' . $row->file_bukti);
                if (file_exists($path)) @unlink($path);
            }
        }

        // Hapus semua indikator dalam sasaran ini
        $deleted = DB::table('pengukuran_periodik')
            ->where('perangkat_daerah_id', $opdId)
            ->where('tahun', $tahun)
            ->where('sasaran_strategis', $sasaran)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Sasaran beserta {$deleted} indikator berhasil dihapus."
        ]);
    }

    // ════════════════════════════════════════════════════
    //  HELPER — parse angka format ribuan
    // ════════════════════════════════════════════════════
    private function parseAngka($value)
    {
        if ($value === null || $value === '') return null;
        $clean = str_replace('.', '', $value);
        $clean = str_replace(',', '.', $clean);
        return floatval($clean) ?: null;
    }

    // ════════════════════════════════════════════════════
    //  SIMPAN — form tambah (DIPERBAIKI - cegah notif ganda)
    // ════════════════════════════════════════════════════
    public function simpan(Request $request)
    {
        $user = Session::get('user');

        if ($user['role'] !== 'operator') {
            return redirect()
                ->route('pengukuran.periodik')
                ->with('error', 'Hanya operator yang dapat menginput data.');
        }
        $opdId            = $user['daerah_id'];
        $tahun            = $request->tahun ?? date('Y');
        $sasaranStrategis = trim($request->sasaran_strategis ?? '');

        $indikatorCount = 0;
        $errors         = [];

        // 🔒 CEK APAKAH SUDAH PERNAH DISUBMIT (mencegah double submit)
        if ($request->has('submitted') && $request->submitted == '1') {
            return redirect()
                ->route('pengukuran.periodik', ['opd_id' => $opdId, 'tahun' => $tahun])
                ->with('warning', 'Data sudah diproses, jangan double submit!');
        }
        foreach ($request->indikator ?? [] as $i => $indikator) {
            $indikator = trim($indikator ?? '');
            if (!$indikator) continue;

            $filePath = null;
            if ($request->hasFile("file.{$i}")) {
                $file     = $request->file("file.{$i}");
                $fileName = 'pengukuran_' . $opdId . '_' . time() . '_' . $i . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('pengukuran_periodik', $fileName, 'public');
            }
            try {
                $payload = [
                    'satuan'               => $request->satuan[$i] ?? null,
                    'sasaran_program'      => $request->sasaran_program[$i] ?? null,
                    'penanggung_jawab'     => $request->penanggung_jawab[$i] ?? null,

                    'target_kinerja_tw1'   => $request->target_kinerja_tw1[$i] ?? null,
                    'target_kinerja_tw2'   => $request->target_kinerja_tw2[$i] ?? null,
                    'target_kinerja_tw3'   => $request->target_kinerja_tw3[$i] ?? null,
                    'target_kinerja_tw4'   => $request->target_kinerja_tw4[$i] ?? null,

                    'target_program_tw1'   => $request->target_program_tw1[$i] ?? null,
                    'target_program_tw2'   => $request->target_program_tw2[$i] ?? null,
                    'target_program_tw3'   => $request->target_program_tw3[$i] ?? null,
                    'target_program_tw4'   => $request->target_program_tw4[$i] ?? null,

                    'anggaran_tw1'         => $this->parseAngka($request->anggaran_tw1[$i] ?? null) ?? 0,
                    'anggaran_tw2'         => $this->parseAngka($request->anggaran_tw2[$i] ?? null) ?? 0,
                    'anggaran_tw3'         => $this->parseAngka($request->anggaran_tw3[$i] ?? null) ?? 0,
                    'anggaran_tw4'         => $this->parseAngka($request->anggaran_tw4[$i] ?? null) ?? 0,

                    'capaian_kinerja_tw1'  => $request->capaian_kinerja_tw1[$i] ?? null,
                    'capaian_kinerja_tw2'  => $request->capaian_kinerja_tw2[$i] ?? null,
                    'capaian_kinerja_tw3'  => $request->capaian_kinerja_tw3[$i] ?? null,
                    'capaian_kinerja_tw4'  => $request->capaian_kinerja_tw4[$i] ?? null,

                    'capaian_program_tw1'  => $request->capaian_program_tw1[$i] ?? null,
                    'capaian_program_tw2'  => $request->capaian_program_tw2[$i] ?? null,
                    'capaian_program_tw3'  => $request->capaian_program_tw3[$i] ?? null,
                    'capaian_program_tw4'  => $request->capaian_program_tw4[$i] ?? null,

                    'capaian_anggaran_tw1' => $this->parseAngka($request->capaian_anggaran_tw1[$i] ?? null) ?? 0,
                    'capaian_anggaran_tw2' => $this->parseAngka($request->capaian_anggaran_tw2[$i] ?? null) ?? 0,
                    'capaian_anggaran_tw3' => $this->parseAngka($request->capaian_anggaran_tw3[$i] ?? null) ?? 0,
                    'capaian_anggaran_tw4' => $this->parseAngka($request->capaian_anggaran_tw4[$i] ?? null) ?? 0,

                    'keterangan'           => $request->keterangan[$i] ?? null,
                    'updated_at'           => now(),
                ];

                if ($filePath) {
                    $payload['file_bukti'] = $filePath;
                }

                $kunci = [
                    'perangkat_daerah_id' => $opdId,
                    'tahun'               => $tahun,
                    'sasaran_strategis'   => $sasaranStrategis,
                    'indikator'           => $indikator,
                ];

                $existing = DB::table('pengukuran_periodik')->where($kunci)->first();

                if ($existing) {
                    DB::table('pengukuran_periodik')
                        ->where('id', $existing->id)
                        ->update($payload);
                } else {
                    DB::table('pengukuran_periodik')->insert(
                        array_merge($kunci, $payload, ['created_at' => now()])
                    );
                }

                $indikatorCount++;

            } catch (\Exception $e) {
                $errors[] = "Indikator '{$indikator}' gagal: " . $e->getMessage();
            }
        }

        // ⭐ NOTIFIKASI HANYA SEKALI
        if ($indikatorCount > 0) {
            $this->kirimNotifikasi(
                $opdId,
                'Pengukuran Kinerja Diinput',
                "Operator mengisi {$indikatorCount} indikator pengukuran kinerja tahun {$tahun}.",
                'pengukuran_input',
                '📊',
                'green',
                route('pengukuran.periodik', ['opd_id' => $opdId, 'tahun' => $tahun])
            );
        }

        $message = $indikatorCount . ' data berhasil disimpan.';
        if (!empty($errors)) {
            $message .= ' Error: ' . implode(', ', $errors);
        }

        // ⭐ REDIRECT HANYA SEKALI
        return redirect()
            ->route('pengukuran.periodik', ['opd_id' => $opdId, 'tahun' => $tahun])
            ->with('success', $message);
    }

    // ════════════════════════════════════════════════════
    //  UPDATE SINGLE FIELD (AJAX auto-save)
    // ════════════════════════════════════════════════════
    public function update(Request $request, $id)
    {
        $user = Session::get('user');

        if (!$user || $user['role'] !== 'operator') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $row = DB::table('pengukuran_periodik')
            ->where('id', $id)
            ->where('perangkat_daerah_id', $user['daerah_id'])
            ->first();

        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        $field = $request->input('field');
        $value = $request->input('value');

        $allowed = [
            'target_kinerja_tw1','target_kinerja_tw2','target_kinerja_tw3','target_kinerja_tw4',
            'target_program_tw1','target_program_tw2','target_program_tw3','target_program_tw4',
            'anggaran_tw1','anggaran_tw2','anggaran_tw3','anggaran_tw4',
            'capaian_kinerja_tw1','capaian_kinerja_tw2','capaian_kinerja_tw3','capaian_kinerja_tw4',
            'capaian_program_tw1','capaian_program_tw2','capaian_program_tw3','capaian_program_tw4',
            'capaian_anggaran_tw1','capaian_anggaran_tw2','capaian_anggaran_tw3','capaian_anggaran_tw4',
            'satuan','sasaran_program','penanggung_jawab','keterangan',
        ];

        if (!in_array($field, $allowed)) {
            return response()->json(['success' => false, 'message' => 'Field tidak valid.'], 400);
        }

        if (str_contains($field, 'anggaran')) {
            $value = $this->parseAngka($value) ?? 0;
        }

        DB::table('pengukuran_periodik')
            ->where('id', $id)
            ->update([$field => $value, 'updated_at' => now()]);

        return response()->json(['success' => true]);
    }

    // ════════════════════════════════════════════════════
    //  UPDATE ALL (fallback, tidak digunakan)
    // ════════════════════════════════════════════════════
    public function updateAll(Request $request)
    {
        return back()->with('success', 'Data diperbarui via auto-save.');
    }

    // ════════════════════════════════════════════════════
    //  HAPUS SINGLE (redirect — dari tombol lama)
    // ════════════════════════════════════════════════════
    public function hapus($id)
    {
        $user = Session::get('user');

        if ($user['role'] !== 'operator') {
            return back()->with('error', 'Hanya operator yang dapat menghapus数据.');
        }

        $row = DB::table('pengukuran_periodik')
            ->where('id', $id)
            ->where('perangkat_daerah_id', $user['daerah_id'])
            ->first();

        if (!$row) {
            return back()->with('error', 'Data tidak ditemukan.');
        }

        if ($row->file_bukti) {
            $path = storage_path('app/public/' . $row->file_bukti);
            if (file_exists($path)) @unlink($path);
        }

        DB::table('pengukuran_periodik')->where('id', $id)->delete();

        return back()->with('success', 'Data berhasil dihapus.');
    }


        // ════════════════════════════════════════════════════
    //  EXPORT EXCEL
    // ════════════════════════════════════════════════════
    public function exportExcel(Request $request)
    {
        $user  = Session::get('user');
        $tahun = (int) $request->input('tahun', date('Y'));

        $opdId = $user['role'] === 'operator' ? $user['daerah_id'] : $request->input('opd_id');

        if (!$opdId) {
            return back()->with('error', 'Pilih OPD terlebih dahulu sebelum mengunduh.');
        }

        $opd     = DB::table('perangkat_daerah')->where('id', $opdId)->first();
        $namaOpd = $opd->nama ?? 'OPD';

        $rawData = DB::table('pengukuran_periodik')
            ->where('perangkat_daerah_id', $opdId)
            ->where('tahun', $tahun)
            ->orderBy('sasaran_strategis')
            ->orderBy('indikator')
            ->orderBy('id')
            ->get()
            ->unique(function ($item) {
                return $item->sasaran_strategis . '|||' . $item->indikator;
            })
            ->values();

        $data = $rawData->groupBy('sasaran_strategis');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pengukuran Periodik');

        $judul = 'MINIMAL PENGUKURAN KINERJA PERIODIK';
        $sheet->setCellValue('A1', $judul);
        $sheet->mergeCells('A1:AC1');
        $sheet->setCellValue('A2', strtoupper($namaOpd) . ' — TAHUN ' . $tahun);
        $sheet->mergeCells('A2:AC2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headerRow1 = 4;
        $headerRow2 = 5;

        $groupHeaders = [
            'Target Kinerja'           => 4,
            'Target Program/Kegiatan'  => 4,
            'Anggaran'                 => 4,
            'Capaian Kinerja'          => 4,
            'Capaian Program/Kegiatan' => 4,
            'Capaian Anggaran'         => 4,
        ];

        $col = 1;
        $fixedCols = ['No', 'Sasaran Strategis', '#', 'Indikator Kinerja'];
        foreach ($fixedCols as $fc) {
            $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($letter . $headerRow1, $fc);
            $sheet->mergeCells($letter . $headerRow1 . ':' . $letter . $headerRow2);
            $col++;
        }

        $twLabels = ['TW1', 'TW2', 'TW3', 'TW4'];

        foreach ($groupHeaders as $label => $jmlTw) {
            $startCol = $col;
            foreach ($twLabels as $tw) {
                $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $sheet->setCellValue($letter . $headerRow2, $tw);
                $col++;
            }
            $endCol = $col - 1;
            $startLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol);
            $endLetter   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endCol);
            $sheet->setCellValue($startLetter . $headerRow1, $label);
            $sheet->mergeCells($startLetter . $headerRow1 . ':' . $endLetter . $headerRow1);

            if ($label === 'Target Kinerja') {
                $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $sheet->setCellValue($letter . $headerRow1, 'Sasaran Program/Kegiatan');
                $sheet->mergeCells($letter . $headerRow1 . ':' . $letter . $headerRow2);
                $col++;
            }
            if ($label === 'Target Program/Kegiatan') {
                $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $sheet->setCellValue($letter . $headerRow1, 'Penanggung Jawab');
                $sheet->mergeCells($letter . $headerRow1 . ':' . $letter . $headerRow2);
                $col++;
            }
        }

        $letterAksi = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
        $sheet->setCellValue($letterAksi . $headerRow1, 'Keterangan');
        $sheet->mergeCells($letterAksi . $headerRow1 . ':' . $letterAksi . $headerRow2);
        $lastCol = $letterAksi;

        $sheet->getStyle('A' . $headerRow1 . ':' . $lastCol . $headerRow2)
            ->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A' . $headerRow1 . ':' . $lastCol . $headerRow2)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1F3864');
        $sheet->getStyle('A' . $headerRow1 . ':' . $lastCol . $headerRow2)
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);

        $row = $headerRow2 + 1;
        $no  = 1;

        foreach ($data as $sasaran => $indikators) {
            $jumlahInd = $indikators->count();

            foreach ($indikators as $idx => $item) {
                $c = 1;
                $set = function ($val) use ($sheet, &$c, $row) {
                    $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                    $sheet->setCellValue($letter . $row, $val);
                    $c++;
                };

                if ($idx === 0) {
                    $sheet->setCellValue('A' . $row, $no);
                    $sheet->setCellValue('B' . $row, $sasaran ?: 'Tanpa Sasaran Strategis');
                    if ($jumlahInd > 1) {
                        $sheet->mergeCells('A' . $row . ':A' . ($row + $jumlahInd - 1));
                        $sheet->mergeCells('B' . $row . ':B' . ($row + $jumlahInd - 1));
                    }
                }
                $c = 3;
                $set($idx + 1);
                $set($item->indikator ?? '-');
                foreach (['target_kinerja_tw1','target_kinerja_tw2','target_kinerja_tw3','target_kinerja_tw4'] as $f) $set($item->$f ?? '');
                $set($item->sasaran_program ?? '-');
                foreach (['target_program_tw1','target_program_tw2','target_program_tw3','target_program_tw4'] as $f) $set($item->$f ?? '');
                $set($item->penanggung_jawab ?? '-');
                foreach (['anggaran_tw1','anggaran_tw2','anggaran_tw3','anggaran_tw4'] as $f) $set($item->$f ?? 0);
                foreach (['capaian_kinerja_tw1','capaian_kinerja_tw2','capaian_kinerja_tw3','capaian_kinerja_tw4'] as $f) $set($item->$f ?? '');
                foreach (['capaian_program_tw1','capaian_program_tw2','capaian_program_tw3','capaian_program_tw4'] as $f) $set($item->$f ?? '');
                foreach (['capaian_anggaran_tw1','capaian_anggaran_tw2','capaian_anggaran_tw3','capaian_anggaran_tw4'] as $f) $set($item->$f ?? 0);
                $set($item->keterangan ?? '-');

                $row++;
            }
            $no++;
        }

        $sheet->getStyle('A' . $headerRow1 . ':' . $lastCol . ($row - 1))
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        foreach (range('A', $lastCol) as $colLetter) {
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(28);

        $filename = 'Pengukuran_Periodik_' . str_replace(' ', '_', $namaOpd) . '_' . $tahun . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    // ════════════════════════════════════════════════════
    //  DOWNLOAD FILE BUKTI
    // ════════════════════════════════════════════════════
    public function download($id)
    {
        $user = Session::get('user');
        $row  = DB::table('pengukuran_periodik')->where('id', $id)->first();

        if (!$row || !$row->file_bukti) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        if ($user['role'] === 'operator' && $row->perangkat_daerah_id != $user['daerah_id']) {
            return back()->with('error', 'Akses ditolak.');
        }

        $path = storage_path('app/public/' . $row->file_bukti);

        if (!file_exists($path)) {
            return back()->with('error', 'File fisik tidak ditemukan di server.');
        }

        return response()->download($path);
    }

    // ════════════════════════════════════════════════════
    //  HELPER — kirim notifikasi ke semua admin
    // ════════════════════════════════════════════════════
    private function kirimNotifikasi(
        int    $opdId,
        string $judul,
        string $pesan,
        string $tipe,
        string $ikon,
        string $warna,
        string $url
    ): void {
        $opd     = DB::table('perangkat_daerah')->where('id', $opdId)->first();
        $namaOpd = $opd->nama ?? 'OPD';

        $admins = DB::table('pengguna')->where('role', 'admin')->get();
        foreach ($admins as $admin) {
            DB::table('notifikasi')->insert([
                'user_id'    => $admin->id,
                'judul'      => $judul,
                'pesan'      => $pesan,
                'tipe'       => $tipe,
                'ikon'       => $ikon,
                'warna'      => $warna,
                'url'        => $url,
                'nama_opd'   => $namaOpd,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}