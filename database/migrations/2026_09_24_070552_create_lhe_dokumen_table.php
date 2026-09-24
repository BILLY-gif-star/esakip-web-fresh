<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lhe_dokumen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perangkat_daerah_id');
            $table->unsignedInteger('tahun');
            $table->string('nama_file');
            $table->string('path_file');
            $table->string('keterangan', 255)->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->index(['perangkat_daerah_id', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lhe_dokumen');
    }
};