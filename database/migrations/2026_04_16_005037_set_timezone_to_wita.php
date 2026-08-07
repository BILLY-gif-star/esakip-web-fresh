<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom created_at/updated_at/dibaca_at/pesan_terakhir_at/last_login
     * yang perlu digeser waktunya, dikelompokkan per tabel.
     */
    private array $columnsByTable = [
        'pesan' => ['created_at', 'updated_at'],
        'percakapan' => ['created_at', 'updated_at', 'pesan_terakhir_at'],
        'notifikasi' => ['created_at', 'updated_at', 'dibaca_at'],
        'pengguna' => ['last_login', 'created_at'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ubah timezone Laravel di config (tidak bisa via migration, tapi bisa via env)
        // Ini hanya untuk record, perubahan permanen tetap di config/app.php

        // 2-4. Update data yang sudah ada, geser +8 jam ke WITA
        $this->shiftHours(8);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: kurangi 8 jam (kembali ke UTC)
        $this->shiftHours(-8);
    }

    private function shiftHours(int $hours): void
    {
        $driver = DB::connection()->getDriverName();

        foreach ($this->columnsByTable as $table => $columns) {
            foreach ($columns as $column) {
                if ($driver === 'pgsql') {
                    // Postgres: pakai operator interval, sign bisa +/- lewat make_interval
                    DB::statement("UPDATE {$table} SET {$column} = {$column} + (INTERVAL '1 hour' * {$hours}) WHERE {$column} IS NOT NULL");
                } else {
                    // MySQL / MariaDB
                    $fn = $hours >= 0 ? 'DATE_ADD' : 'DATE_SUB';
                    $absHours = abs($hours);
                    DB::statement("UPDATE {$table} SET {$column} = {$fn}({$column}, INTERVAL {$absHours} HOUR) WHERE {$column} IS NOT NULL");
                }
            }
        }
    }
};