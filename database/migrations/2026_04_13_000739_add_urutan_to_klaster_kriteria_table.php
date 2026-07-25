<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('klaster_kriteria', 'urutan')) {
            Schema::table('klaster_kriteria', function (Blueprint $table) {
                $table->integer('urutan')->default(0)->after('uraian');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('klaster_kriteria', 'urutan')) {
            Schema::table('klaster_kriteria', function (Blueprint $table) {
                $table->dropColumn('urutan');
            });
        }
    }
};