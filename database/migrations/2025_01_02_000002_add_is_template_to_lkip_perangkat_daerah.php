<?php
// database/migrations/2025_01_02_000002_add_is_template_to_lkip_perangkat_daerah.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lkip_perangkat_daerah', function (Blueprint $table) {
            // Cek apakah kolom is_template sudah ada
            if (!Schema::hasColumn('lkip_perangkat_daerah', 'is_template')) {
                $table->boolean('is_template')->default(0)->after('id');
            }
        });
    }
    
    public function down()
    {
        Schema::table('lkip_perangkat_daerah', function (Blueprint $table) {
            if (Schema::hasColumn('lkip_perangkat_daerah', 'is_template')) {
                $table->dropColumn('is_template');
            }
        });
    }
};