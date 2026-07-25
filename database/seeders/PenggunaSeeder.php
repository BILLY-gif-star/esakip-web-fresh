<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        $pengguna = [
            [
                'username'            => 'admin',
                'password'            => bcrypt('Admin2026'),
                'nama'                => 'Administrator ',
                'role'                => 'admin',
                'perangkat_daerah_id' => null,
                'is_active'           => 1,
                'last_login'          => null,
                'created_at'          => now(),
                'status_daftar'       => 'disetujui',
                'catatan'             => null,
            ],
        ];

        foreach ($pengguna as $p) {
            DB::table('pengguna')->updateOrInsert(
                ['username' => $p['username']],  // cari berdasarkan username
                $p                               // update/insert semua kolom
            );
        }
    }
}