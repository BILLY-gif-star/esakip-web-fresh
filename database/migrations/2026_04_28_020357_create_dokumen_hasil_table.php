<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('dokumen_hasil')) {
            Schema::create('dokumen_hasil', function (Blueprint $table) {
                $table->id();
                $table->string('judul');
                $table->text('deskripsi')->nullable();
                $table->string('nama_file');
                $table->integer('tahun');
                $table->unsignedBigInteger('uploaded_by');
                $table->unsignedBigInteger('perangkat_daerah_id');
                $table->timestamp('dibaca_at')->nullable();
                $table->timestamps();

                $table->foreign('uploaded_by')->references('id')->on('pengguna')->onDelete('cascade');
                $table->foreign('perangkat_daerah_id')->references('id')->on('perangkat_daerah')->onDelete('cascade');
                $table->index(['perangkat_daerah_id', 'tahun']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_hasil');
    }
};