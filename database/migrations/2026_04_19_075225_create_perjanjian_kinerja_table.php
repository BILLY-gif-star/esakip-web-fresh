<?php
// database/migrations/2025_04_19_000003_create_perjanjian_kinerja_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('perjanjian_kinerja')) {
            Schema::create('perjanjian_kinerja', function (Blueprint $table) {
                $table->id();
                $table->string('jenis');           // perjanjian_kinerja, perjanjian_iku, renstra_iku, pelaksanaan_anggaran, dpa, cascading_pohon_kinerja
                $table->integer('tahun');
                $table->string('nama_file')->nullable();
                $table->string('path_file')->nullable();
                $table->unsignedBigInteger('perangkat_daerah_id')->nullable();
                $table->foreignId('uploaded_by')->constrained('pengguna');
                $table->timestamps();
                
                $table->index(['jenis', 'tahun']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('perjanjian_kinerja');
    }
};