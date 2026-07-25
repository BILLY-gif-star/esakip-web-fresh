<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('evaluator_opd')) {
            Schema::create('evaluator_opd', function (Blueprint $table) {
                $table->id();
                
                // ⭐ Gunakan tipe yang sama dengan pengguna.id
                // Jika pengguna.id int(10) unsigned, pakai unsignedInteger
                $table->unsignedInteger('evaluator_id');
                $table->unsignedInteger('perangkat_daerah_id');
                
                $table->timestamps();
                
                // Hapus foreign key constraint terlebih dahulu jika ada masalah
                // $table->foreign('evaluator_id')->references('id')->on('pengguna')->onDelete('cascade');
                // $table->foreign('perangkat_daerah_id')->references('id')->on('perangkat_daerah')->onDelete('cascade');
                
                // Gunakan index biasa tanpa foreign key (alternatif)
                $table->index('evaluator_id');
                $table->index('perangkat_daerah_id');
                
                $table->unique(['evaluator_id', 'perangkat_daerah_id'], 'unique_evaluator_opd');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluator_opd');
    }
};