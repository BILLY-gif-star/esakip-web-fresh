<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('klaster_evaluasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perangkat_daerah_id');
            $table->year('tahun');
            $table->string('klaster'); // utama, pendukung, tambahan
            $table->integer('level'); // 1,2,3
            $table->string('indikator');
            $table->decimal('target', 15, 2)->nullable();
            $table->decimal('realisasi', 15, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('klaster_evaluasi');
    }
};