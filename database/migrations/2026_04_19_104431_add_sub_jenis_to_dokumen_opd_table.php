<?php
// database/migrations/2025_04_19_000004_add_sub_jenis_to_dokumen_opd_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumen_opd', function (Blueprint $table) {
            if (!Schema::hasColumn('dokumen_opd', 'sub_jenis')) {
                $table->string('sub_jenis')->nullable()->after('jenis');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dokumen_opd', function (Blueprint $table) {
            if (Schema::hasColumn('dokumen_opd', 'sub_jenis')) {
                $table->dropColumn('sub_jenis');
            }
        });
    }
};