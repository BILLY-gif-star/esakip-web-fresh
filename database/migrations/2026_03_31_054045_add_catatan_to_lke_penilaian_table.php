<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('lke_penilaian', 'catatan')) {
            Schema::table('lke_penilaian', function (Blueprint $table) {
                $table->text('catatan')->nullable()->after('persentase');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('lke_penilaian', 'catatan')) {
            Schema::table('lke_penilaian', function (Blueprint $table) {
                $table->dropColumn('catatan');
            });
        }
    }
};