<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lke_pengisi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perangkat_daerah_id');
            $table->unsignedInteger('tahun');
            $table->string('nama_operator', 150)->nullable();
            $table->string('asal_operator', 150)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['perangkat_daerah_id', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lke_pengisi');
    }
};