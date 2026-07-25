<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Matikan FK check agar truncate tidak error karena relasi antar tabel
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate semua tabel master (child dulu, baru parent)
        DB::table('pengguna')->truncate();
        DB::table('lke_kriteria')->truncate();
        DB::table('lke_komponen')->truncate();
        DB::table('klaster_kriteria')->truncate();
        DB::table('klaster_komponen')->truncate();
        DB::table('perangkat_daerah')->truncate();

        // Nyalakan kembali FK check
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call([
            PerangkatDaerahSeeder::class,
            PenggunaSeeder::class,
            LkeKomponenSeeder::class,
            LkeKriteriaSeeder::class,
            KlasterKomponenSeeder::class,
            KlasterKriteriaSeeder::class,
        ]);
    }
}