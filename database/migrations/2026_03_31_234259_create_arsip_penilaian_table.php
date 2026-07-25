<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arsip_penilaian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perangkat_daerah_id');
            $table->year('tahun');
            $table->text('data_penilaian')->nullable(); // JSON data
            $table->decimal('nilai_akhir', 10, 2)->default(0);
            $table->string('predikat', 5);
            $table->unsignedBigInteger('dinilai_oleh');
            $table->timestamp('dinilai_pada');
            $table->text('catatan_arsip')->nullable();
            $table->timestamps();
            
            $table->index(['perangkat_daerah_id', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arsip_penilaian');
    }
};