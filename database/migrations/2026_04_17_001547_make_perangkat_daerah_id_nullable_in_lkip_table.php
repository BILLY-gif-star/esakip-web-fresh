<?php
// database/migrations/2025_04_17_000001_make_perangkat_daerah_id_nullable_in_lkip_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lkip_perangkat_daerah', function (Blueprint $table) {
            $table->unsignedBigInteger('perangkat_daerah_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('lkip_perangkat_daerah', function (Blueprint $table) {
            $table->unsignedBigInteger('perangkat_daerah_id')->nullable(false)->change();
        });
    }
};