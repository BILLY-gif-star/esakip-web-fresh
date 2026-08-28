<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerjanjianController;
use App\Http\Controllers\EvaluasiController;
use App\Http\Controllers\PengukuranController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\LkeController;
use App\Http\Controllers\KlasterController;
use App\Http\Controllers\KlasterEvaluasiController;
use App\Http\Controllers\PengukuranPeriodikController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\LkipController;
use App\Http\Controllers\JuknisController;
use App\Http\Controllers\RekapanHasilLkeController;
use App\Http\Controllers\DokumenHasilController;
use App\Http\Controllers\PerjanjianCascadingController;
use App\Http\Controllers\PerjanjianTemplateController;
use App\Http\Controllers\PerjanjianDokumenController;


Route::get('/debug-env', function () {
    return [
        'app_key_via_env' => env('APP_KEY'),
        'app_key_via_config' => config('app.key'),
        'env_file_exists' => file_exists(base_path('.env')),
        'env_file_readable' => is_readable(base_path('.env')),
    ];
});

// ════════════════════════════════════════════════════════════
//  PUBLIK — tidak butuh login
// ════════════════════════════════════════════════════════════
Route::get('/welcome', [LandingController::class, 'show'])->name('welcome');

Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/daftar', [AuthController::class, 'daftar'])->name('daftar');

// ════════════════════════════════════════════════════════════
//  SEMUA ROUTE YANG BUTUH LOGIN
// ════════════════════════════════════════════════════════════
Route::middleware('auth.esakip')->group(function () {

    // ── Dashboard ────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Penjelasan Penilaian ─────────────────────────────────
    Route::get('/penjelasan-penilaian', function () {
        return view('penjelasan-penilaian');
    })->name('penjelasan.penilaian');

    // ════════════════════════════════════════════════════════
    //  NOTIFIKASI
    // ════════════════════════════════════════════════════════
    Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
        Route::get('/ambil',       [NotifikasiController::class, 'ambil'])->name('ambil');
        Route::post('/{id}/baca',  [NotifikasiController::class, 'baca'])->name('baca');
        Route::post('/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('bacaSemua');
    });

    // ════════════════════════════════════════════════════════
    //  CHAT
    // ════════════════════════════════════════════════════════
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/',                          [ChatController::class, 'index'])->name('index');
        Route::get('/with/{userId}',             [ChatController::class, 'chatWith'])->name('with');
        Route::post('/send/{userId}',            [ChatController::class, 'send'])->name('send');
        Route::get('/messages/{percakapanId}',   [ChatController::class, 'messages'])->name('messages');
        Route::post('/mark-read/{percakapanId}', [ChatController::class, 'markAsRead'])->name('mark-read');
        Route::get('/unread-count',              [ChatController::class, 'unreadCount'])->name('unread-count');
        Route::delete('/clear/{percakapanId}',   [ChatController::class, 'clearChat'])->name('clear');
    });

    // ════════════════════════════════════════════════════════
    //  DOKUMEN HASIL
    //  Upload/hapus: admin only | Unduh & rekap: semua
    // ════════════════════════════════════════════════════════
    Route::middleware('role.admin')->group(function () {
        Route::post('/dokumen-hasil/upload',    [DokumenHasilController::class, 'upload'])->name('dokumen.hasil.upload');
        Route::post('/dokumen-hasil/hapus/{id}',[DokumenHasilController::class, 'hapus'])->name('dokumen.hasil.hapus');
        Route::get('/dokumen-hasil/list',       [DokumenHasilController::class, 'listPerOpd'])->name('dokumen.hasil.list');
    });
    Route::get('/dokumen-hasil/unduh/{id}', [DokumenHasilController::class, 'unduh'])->name('dokumen.hasil.unduh');
    Route::get('/rekap-hasil-opd',          [DokumenHasilController::class, 'rekapOpd'])->name('rekap.hasil.opd');

    // ════════════════════════════════════════════════════════
    //  REKAPAN HASIL (admin only)
    // ════════════════════════════════════════════════════════
    Route::middleware('role.admin')->group(function () {
        Route::get('/rekapan-hasil',                [RekapanHasilLkeController::class, 'index'])->name('rekapan.hasil');
        Route::get('/rekapan-hasil/download-excel', [RekapanHasilLkeController::class, 'downloadExcel'])->name('rekapan.hasil.excel');
        Route::get('/rekapan-hasil/download-pdf',   [RekapanHasilLkeController::class, 'downloadPdf'])->name('rekapan.hasil.pdf');
    });

    // ════════════════════════════════════════════════════════
    //  KLASTER EVALUASI
    // ════════════════════════════════════════════════════════
    Route::prefix('klaster')->name('klaster.')->group(function () {

        // ── 9 halaman klaster → satu controller method ───────
        Route::get('/utama/1', function (\Illuminate\Http\Request $r) {
            return app(KlasterController::class)->index($r->merge(['type' => 'utama', 'level' => 1]));
        })->name('utama.1');

        Route::get('/utama/2', function (\Illuminate\Http\Request $r) {
            return app(KlasterController::class)->index($r->merge(['type' => 'utama', 'level' => 2]));
        })->name('utama.2');

        Route::get('/utama/3', function (\Illuminate\Http\Request $r) {
            return app(KlasterController::class)->index($r->merge(['type' => 'utama', 'level' => 3]));
        })->name('utama.3');

        Route::get('/pendukung/1', function (\Illuminate\Http\Request $r) {
            return app(KlasterController::class)->index($r->merge(['type' => 'pendukung', 'level' => 1]));
        })->name('pendukung.1');

        Route::get('/pendukung/2', function (\Illuminate\Http\Request $r) {
            return app(KlasterController::class)->index($r->merge(['type' => 'pendukung', 'level' => 2]));
        })->name('pendukung.2');

        Route::get('/pendukung/3', function (\Illuminate\Http\Request $r) {
            return app(KlasterController::class)->index($r->merge(['type' => 'pendukung', 'level' => 3]));
        })->name('pendukung.3');

        Route::get('/tambahan/1', function (\Illuminate\Http\Request $r) {
            return app(KlasterController::class)->index($r->merge(['type' => 'tambahan', 'level' => 1]));
        })->name('tambahan.1');

        Route::get('/tambahan/2', function (\Illuminate\Http\Request $r) {
            return app(KlasterController::class)->index($r->merge(['type' => 'tambahan', 'level' => 2]));
        })->name('tambahan.2');

        Route::get('/tambahan/3', function (\Illuminate\Http\Request $r) {
            return app(KlasterController::class)->index($r->merge(['type' => 'tambahan', 'level' => 3]));
        })->name('tambahan.3');

        // ── CRUD klaster ──────────────────────────────────────
        Route::post('/simpan',               [KlasterController::class, 'simpan'])->name('simpan');
        Route::post('/upload-dokumen',       [KlasterController::class, 'uploadDokumen'])->name('upload.dokumen');
        Route::get('/lihat-dokumen/{id}',    [KlasterController::class, 'lihatDokumen'])->name('lihat.dokumen');
        Route::delete('/hapus-dokumen/{id}', [KlasterController::class, 'hapusDokumen'])->name('hapus.dokumen');
        Route::post('/hapus-dokumen/{id}',   [KlasterController::class, 'hapusDokumen']);   // fallback non-DELETE
        Route::get('/rekap',                 [KlasterController::class, 'rekap'])->name('rekap');
        Route::get('/hasil-lke-gabungan',    [KlasterEvaluasiController::class, 'hasilLkeGabungan'])->name('hasil.lke.gabungan');

     
    });

    // ════════════════════════════════════════════════════════
    //  EVALUASI KINERJA (LKE)
    // ════════════════════════════════════════════════════════
    Route::prefix('evaluasi')->name('evaluasi.')->group(function () {
        Route::get('/juknis',        [EvaluasiController::class, 'juknis'])->name('juknis');
        Route::post('/upload',       [EvaluasiController::class, 'upload'])->name('upload');
        Route::get('/download/{id}', [EvaluasiController::class, 'download'])->name('download');

        // LKE AKIP
        Route::get('/lke/dokumen',         [LkeController::class, 'daftarDokumen'])->name('lke.dokumen.list');
        Route::get('/lke',                 [LkeController::class, 'index'])->name('lke');
        Route::post('/lke/simpan',         [LkeController::class, 'simpan'])->name('lke.simpan');
        Route::post('/lke/simpan-nilai',   [LkeController::class, 'simpanNilai'])->name('lke.simpan.nilai');
        Route::get('/lke/rekap',           [LkeController::class, 'rekap'])->name('lke.rekap');
        Route::post('/lke/upload-dokumen', [LkeController::class, 'uploadDokumen'])->name('lke.upload.dokumen');
        Route::get('/lke/dokumen/{id}',    [LkeController::class, 'lihatDokumen'])->name('lke.lihat.dokumen');
        Route::delete('/lke/dokumen/{id}', [LkeController::class, 'hapusDokumen'])->name('lke.hapus.dokumen');
        Route::post('/lke/dokumen/{id}',   [LkeController::class, 'hapusDokumen']);   // fallback non-DELETE
    });

    // ════════════════════════════════════════════════════════
    //  LKIP PERANGKAT DAERAH
    // ════════════════════════════════════════════════════════
    Route::prefix('lkip')->name('lkip.')->group(function () {
        Route::get('/',              [LkipController::class, 'index'])->name('index');
        Route::post('/upload',       [LkipController::class, 'upload'])->name('upload');
        Route::post('/review/{id}',  [LkipController::class, 'review'])->name('review');
        Route::get('/download/{id}', [LkipController::class, 'download'])->name('download');
        Route::get('/preview/{id}',  [LkipController::class, 'preview'])->name('preview');
        Route::post('/hapus/{id}',   [LkipController::class, 'hapus'])->name('hapus');
    });

    // ════════════════════════════════════════════════════════
    //  PERJANJIAN KINERJA
    // ════════════════════════════════════════════════════════
    Route::prefix('perjanjian')->name('perjanjian.')->group(function () {
        Route::get('/cascading',               [PerjanjianCascadingController::class, 'index'])->name('cascading');
        Route::post('/upload-cascading',       [PerjanjianCascadingController::class, 'upload'])->name('upload.cascading');
        Route::post('/review-cascading/{id}',  [PerjanjianCascadingController::class, 'review'])->name('review.cascading');
        Route::delete('/hapus-cascading/{id}', [PerjanjianCascadingController::class, 'hapus'])->name('hapus.cascading');

        Route::get('/{jenis}',                 [PerjanjianController::class, 'index'])->name('index');
        Route::post('/upload-template',        [PerjanjianTemplateController::class, 'upload'])->name('upload.template');
        Route::get('/download-template/{id}',  [PerjanjianTemplateController::class, 'download'])->name('download.template');
        Route::get('/preview-template/{id}',   [PerjanjianTemplateController::class, 'preview'])->name('preview.template');
        Route::delete('/hapus-template/{id}',  [PerjanjianTemplateController::class, 'hapus'])->name('hapus.template');
        Route::post('/upload-dokumen',         [PerjanjianDokumenController::class, 'upload'])->name('upload.dokumen');
        Route::get('/lihat/{id}',              [PerjanjianDokumenController::class, 'lihat'])->name('lihat');
        Route::get('/preview/{id}',            [PerjanjianDokumenController::class, 'preview'])->name('preview');
        Route::post('/review/{id}',            [PerjanjianDokumenController::class, 'review'])->name('review');
        Route::delete('/hapus/{id}',           [PerjanjianDokumenController::class, 'hapus'])->name('hapus');
        Route::post('/hapus/{id}',             [PerjanjianDokumenController::class, 'hapus']);
        Route::put('/edit/{id}',               [PerjanjianDokumenController::class, 'edit'])->name('edit');
    });

    // ════════════════════════════════════════════════════════
    //  PENGUKURAN KINERJA
    // ════════════════════════════════════════════════════════
    Route::prefix('pengukuran')->name('pengukuran.')->group(function () {
        // Periodik
        Route::get('/periodik',                      [PengukuranPeriodikController::class, 'index'])->name('periodik');
        Route::post('/periodik/simpan',              [PengukuranPeriodikController::class, 'simpan'])->name('periodik.simpan');
        Route::put('/periodik/update-all',           [PengukuranPeriodikController::class, 'updateAll'])->name('periodik.update-all');
        Route::delete('/periodik/hapus/{id}',        [PengukuranPeriodikController::class, 'hapus'])->name('periodik.hapus');
        Route::get('/periodik/download/{id}',        [PengukuranPeriodikController::class, 'download'])->name('periodik.download');

        // Edit indikator (AJAX)
        Route::put('/periodik/{id}/update',          [PengukuranPeriodikController::class, 'update'])->name('periodik.update');
        Route::post('/periodik/{id}/update',         [PengukuranPeriodikController::class, 'updateIndikator'])->name('periodik.update-indikator');
        Route::delete('/periodik/{id}/delete',       [PengukuranPeriodikController::class, 'deleteIndikator'])->name('periodik.delete-indikator');
        Route::get('/periodik/{id}/data',            [PengukuranPeriodikController::class, 'getData'])->name('periodik.data');

        // Sasaran
        Route::post('/periodik/update-sasaran',      [PengukuranPeriodikController::class, 'updateSasaran'])->name('periodik.update-sasaran');
        Route::post('/periodik/delete-sasaran',      [PengukuranPeriodikController::class, 'deleteSasaran'])->name('periodik.delete-sasaran');

        // Minimal
        Route::get('/minimal',                       [PengukuranController::class, 'index'])->name('minimal');
        Route::post('/minimal/simpan',               [PengukuranController::class, 'simpan'])->name('minimal.simpan');
        Route::get('/minimal/download/{ikuId}',      [PengukuranController::class, 'download'])->name('pengukuran.download');
    });

    // ════════════════════════════════════════════════════════
    //  JUKNIS
    // ════════════════════════════════════════════════════════
    Route::prefix('juknis')->name('juknis.')->group(function () {
        Route::get('/',              [JuknisController::class, 'index'])->name('index');
        Route::get('/user',          [JuknisController::class, 'userIndex'])->name('user');
        Route::post('/upload',       [JuknisController::class, 'upload'])->name('upload');
        Route::post('/update/{id}',  [JuknisController::class, 'update'])->name('update');
        Route::post('/delete/{id}',  [JuknisController::class, 'delete'])->name('delete');
        Route::delete('/delete/{id}', [JuknisController::class, 'delete']); 
        Route::get('/download/{id}', [JuknisController::class, 'download'])->name('download');
        Route::get('/preview/{id}',  [JuknisController::class, 'preview'])->name('preview');
    });

    // ════════════════════════════════════════════════════════
    //  ADMIN ONLY
    // ════════════════════════════════════════════════════════
    Route::middleware('role.admin')->prefix('admin')->name('admin.')->group(function () {


         Route::get('/landing', [LandingController::class, 'admin'])->name('landing');
         Route::post('/landing/{key}', [LandingController::class, 'update'])->name('landing.update');
         Route::post('/landing/{key}/reset', [LandingController::class, 'reset'])->name('landing.reset');

        // ── Manajemen Pengguna ────────────────────────────────
        Route::get('/pengguna',                      [AdminController::class, 'pengguna'])->name('pengguna');
        Route::get('/pengguna/{id}/data',            [AdminController::class, 'getUser'])->name('pengguna.data');
        Route::post('/pengguna/store',               [AdminController::class, 'storeUser'])->name('pengguna.store');
        Route::post('/pengguna/{id}/update',         [AdminController::class, 'updateUser'])->name('pengguna.update');
        Route::post('/pengguna/{id}/reset-password', [AdminController::class, 'resetPasswordUser'])->name('pengguna.reset');
        Route::delete('/pengguna/{id}/delete',       [AdminController::class, 'deleteUser'])->name('pengguna.delete');

        // ── Persetujuan Akun ──────────────────────────────────
        Route::get('/persetujuan',       [AdminController::class, 'persetujuan'])->name('persetujuan');
        Route::post('/persetujuan/{id}', [AdminController::class, 'prosesPersetujuan'])->name('persetujuan.proses');

        // ── Arsip Dokumen ─────────────────────────────────────
        Route::get('/arsip',               [ArsipController::class, 'index'])->name('arsip');
        Route::get('/arsip/lihat/{id}',    [ArsipController::class, 'lihat'])->name('arsip.lihat');
        Route::get('/arsip/{id}/detail',   [ArsipController::class, 'detail'])->name('arsip.detail');
        Route::delete('/arsip/hapus/{id}', [ArsipController::class, 'hapus'])->name('arsip.hapus');

        // ── Kelola Klaster OPD ────────────────────────────────
        Route::get('/klaster',           [AdminController::class, 'kelolaKlaster'])->name('klaster');
        Route::post('/klaster/{id}/klaster', [AdminController::class, 'updateKlasterOpd'])->name('klaster.update');
        Route::post('/klaster/{id}/tugas',   [AdminController::class, 'updateTugasOpd'])->name('klaster.tugas');
        Route::get('/klaster/{id}/data',     [AdminController::class, 'getOpdKlaster'])->name('klaster.data');

        // ── Assign OPD ke Evaluator ───────────────────────────
        // ✅ HANYA di sini — tidak ada duplikat di group klaster
        // URL: GET  /admin/evaluator/{id}/assign-opd
        // URL: POST /admin/evaluator/{id}/assign-opd
        // URL: GET  /admin/evaluator/{id}/assigned-opd
        Route::get('/evaluator/{id}/assign-opd',   [AdminController::class, 'assignOpdEvaluator'])->name('evaluator.assign-opd');
        Route::post('/evaluator/{id}/assign-opd',  [AdminController::class, 'storeAssignOpdEvaluator'])->name('evaluator.store-assign-opd');
        Route::get('/evaluator/{id}/assigned-opd', [AdminController::class, 'getAssignedOpdEvaluator'])->name('evaluator.assigned-opd');
    });

}); // end middleware auth.esakip