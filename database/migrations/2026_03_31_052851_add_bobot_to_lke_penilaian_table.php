<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('lke_penilaian', 'bobot')) {
            Schema::table('lke_penilaian', function (Blueprint $table) {
                $table->decimal('bobot', 10, 2)->nullable()->after('komponen_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('lke_penilaian', 'bobot')) {
            Schema::table('lke_penilaian', function (Blueprint $table) {
                $table->dropColumn('bobot');
            });
        }
    }
};