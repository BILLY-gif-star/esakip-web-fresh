<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lke_periode', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('tahun')->unique();
            $table->dateTime('tanggal_mulai')->nullable();
            $table->dateTime('tanggal_selesai')->nullable();
            $table->enum('status_manual', ['otomatis', 'dibuka_paksa', 'ditutup_paksa'])->default('otomatis');
            $table->string('keterangan', 255)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lke_periode');
    }
};