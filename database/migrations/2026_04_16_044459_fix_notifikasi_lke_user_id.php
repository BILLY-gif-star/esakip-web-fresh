<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Dapatkan user_id untuk operator "kamu" (perangkat_daerah_id = 27)
        $targetUserId = DB::table('pengguna')
            ->where('perangkat_daerah_id', 27)
            ->where('role', 'operator')
            ->value('id');

        if ($targetUserId) {
            // 2. Pindahkan semua notifikasi LKE ke user_id yang benar
            DB::table('notifikasi')
                ->where('tipe', 'lke_penilaian')
                ->update(['user_id' => $targetUserId]);
        }

        // 3. Alternatif: pindahkan berdasarkan nama_opd
        DB::table('notifikasi')
            ->where('tipe', 'lke_penilaian')
            ->where('nama_opd', 'BADAN KEPEGAWAIAN DAERAH')
            ->update(['user_id' => 2]);

        // 4. Hapus notifikasi duplikat yang tidak perlu (opsional)
        // Catatan: pakai havingRaw('COUNT(*) > 1') bukan having('count', '>', 1),
        // karena Postgres tidak izinkan HAVING merujuk ke alias kolom SELECT
        // (beda dengan MySQL yang membolehkannya).
        $duplicates = DB::table('notifikasi')
            ->where('tipe', 'lke_penilaian')
            ->select('pesan', DB::raw('COUNT(*) as count'))
            ->groupBy('pesan')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('pesan');

        foreach ($duplicates as $pesan) {
            $ids = DB::table('notifikasi')
                ->where('tipe', 'lke_penilaian')
                ->where('pesan', $pesan)
                ->orderBy('id')
                ->pluck('id')
                ->toArray();

            // Hapus duplikat, sisakan yang pertama
            array_shift($ids); // Hapus ID pertama dari array
            if (!empty($ids)) {
                DB::table('notifikasi')->whereIn('id', $ids)->delete();
            }
        }
    }

    public function down(): void
    {
        // Rollback: tidak perlu mengembalikan karena ini perbaikan data
        // Migration ini hanya untuk memperbaiki data yang salah
    }
};
