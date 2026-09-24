<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lhe_akip', function (Blueprint $table) {
            $table->json('catatan_terpilih')->nullable()->after('rekomendasi');
        });
    }

    public function down(): void
    {
        Schema::table('lhe_akip', function (Blueprint $table) {
            $table->dropColumn('catatan_terpilih');
        });
    }
};