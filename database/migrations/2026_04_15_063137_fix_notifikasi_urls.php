<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Perbaiki URL notifikasi yang salah
        DB::table('notifikasi')
            ->where('url', 'like', '%perjanjian_kinerja_opd%')
            ->update(['url' => DB::raw("REPLACE(url, 'perjanjian_kinerja_opd', 'perjanjian-kinerja')")]);
        
        DB::table('notifikasi')
            ->where('url', 'like', '%perjanjian_iku_opd%')
            ->update(['url' => DB::raw("REPLACE(url, 'perjanjian_iku_opd', 'perjanjian-iku')")]);
        
        DB::table('notifikasi')
            ->where('url', 'like', '%renstra_iku_opd%')
            ->update(['url' => DB::raw("REPLACE(url, 'renstra_iku_opd', 'renstra-iku')")]);
        
        DB::table('notifikasi')
            ->where('url', 'like', '%pelaksanaan_anggaran_opd%')
            ->update(['url' => DB::raw("REPLACE(url, 'pelaksanaan_anggaran_opd', 'pelaksanaan-anggaran')")]);
        
        // Hapus juga yang menggunakan URL lengkap
        DB::table('notifikasi')
            ->where('url', 'like', '%/perjanjian/perjanjian_kinerja_opd%')
            ->update(['url' => DB::raw("REPLACE(url, '/perjanjian/perjanjian_kinerja_opd', '/perjanjian/perjanjian-kinerja')")]);
        
        DB::table('notifikasi')
            ->where('url', 'like', '%/perjanjian/perjanjian_iku_opd%')
            ->update(['url' => DB::raw("REPLACE(url, '/perjanjian/perjanjian_iku_opd', '/perjanjian/perjanjian-iku')")]);
        
        DB::table('notifikasi')
            ->where('url', 'like', '%/perjanjian/renstra_iku_opd%')
            ->update(['url' => DB::raw("REPLACE(url, '/perjanjian/renstra_iku_opd', '/perjanjian/renstra-iku')")]);
        
        DB::table('notifikasi')
            ->where('url', 'like', '%/perjanjian/pelaksanaan_anggaran_opd%')
            ->update(['url' => DB::raw("REPLACE(url, '/perjanjian/pelaksanaan_anggaran_opd', '/perjanjian/pelaksanaan-anggaran')")]);
    }

    public function down(): void
    {
        // Rollback: kembalikan ke format lama
        DB::table('notifikasi')
            ->where('url', 'like', '%perjanjian-kinerja%')
            ->update(['url' => DB::raw("REPLACE(url, 'perjanjian-kinerja', 'perjanjian_kinerja_opd')")]);
        
        DB::table('notifikasi')
            ->where('url', 'like', '%perjanjian-iku%')
            ->update(['url' => DB::raw("REPLACE(url, 'perjanjian-iku', 'perjanjian_iku_opd')")]);
        
        DB::table('notifikasi')
            ->where('url', 'like', '%renstra-iku%')
            ->update(['url' => DB::raw("REPLACE(url, 'renstra-iku', 'renstra_iku_opd')")]);
        
        DB::table('notifikasi')
            ->where('url', 'like', '%pelaksanaan-anggaran%')
            ->update(['url' => DB::raw("REPLACE(url, 'pelaksanaan-anggaran', 'pelaksanaan_anggaran_opd')")]);
    }
};