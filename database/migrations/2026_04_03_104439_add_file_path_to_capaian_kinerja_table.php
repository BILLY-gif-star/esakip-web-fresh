<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('capaian_kinerja', 'file_path')) {
            Schema::table('capaian_kinerja', function (Blueprint $table) {
                $table->string('file_path')->nullable()->after('keterangan');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('capaian_kinerja', 'file_path')) {
            Schema::table('capaian_kinerja', function (Blueprint $table) {
                $table->dropColumn('file_path');
            });
        }
    }
};