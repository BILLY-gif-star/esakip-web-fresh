<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateFileSize
{
    // ── Batas maksimal ukuran file ──────────────────────
    const MAX_SIZE = 5 * 1024 * 1024; // 5 MB dalam bytes

    // ── Tipe file yang diizinkan ────────────────────────
    const ALLOWED_MIMES = [
        'application/pdf',                                                        // PDF
        'application/vnd.ms-excel',                                               // XLS
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',      // XLSX
    ];

    const ALLOWED_EXTENSIONS = ['pdf', 'xls', 'xlsx'];

    public function handle(Request $request, Closure $next)
    {
        foreach ($request->allFiles() as $key => $file) {
            // Support single file maupun array file
            $files = is_array($file) ? $file : [$file];

            foreach ($files as $f) {

                // ── Cek ekstensi ──────────────────────────────
                $ext = strtolower($f->getClientOriginalExtension());
                if (!in_array($ext, self::ALLOWED_EXTENSIONS)) {
                    $pesanTipe = 'Format file "' . $f->getClientOriginalName() . '" tidak diizinkan. '
                               . 'Hanya file PDF dan Excel (XLS/XLSX) yang dapat diupload.';

                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => $pesanTipe,
                        ], 422);
                    }

                    return back()
                        ->with('file_error', $pesanTipe)
                        ->with('file_error_type', 'type')
                        ->withInput();
                }

                // ── Cek MIME type (double check keamanan) ─────
                $mime = $f->getMimeType();
                if (!in_array($mime, self::ALLOWED_MIMES)) {
                    $pesanMime = 'Tipe file "' . $f->getClientOriginalName() . '" tidak valid. '
                               . 'Hanya file PDF dan Excel (XLS/XLSX) yang dapat diupload.';

                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => $pesanMime,
                        ], 422);
                    }

                    return back()
                        ->with('file_error', $pesanMime)
                        ->with('file_error_type', 'type')
                        ->withInput();
                }

                // ── Cek ukuran file ───────────────────────────
                if ($f->getSize() > self::MAX_SIZE) {
                    $pesanUkuran = 'Ukuran file "' . $f->getClientOriginalName() . '" melebihi batas maksimal 5MB. '
                                 . 'Silakan pilih file yang lebih kecil.';

                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => $pesanUkuran,
                        ], 422);
                    }

                    return back()
                        ->with('file_error', $pesanUkuran)
                        ->with('file_error_type', 'size')
                        ->withInput();
                }
            }
        }

        return $next($request);
    }
}