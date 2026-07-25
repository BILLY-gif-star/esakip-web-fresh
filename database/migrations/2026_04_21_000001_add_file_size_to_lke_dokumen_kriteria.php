<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lke_dokumen_kriteria', function (Blueprint $table) {
            $table->bigInteger('file_size')->nullable()->after('nama_file');
        });
    }

    public function down(): void
    {
        Schema::table('lke_dokumen_kriteria', function (Blueprint $table) {
            $table->dropColumn('file_size');
        });
    }
};