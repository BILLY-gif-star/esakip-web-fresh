<?php
// database/migrations/2025_01_01_000001_create_lkip_perangkat_daerah_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lkip_perangkat_daerah', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perangkat_daerah_id');
            $table->year('tahun');
            $table->string('judul_dokumen');
            $table->text('deskripsi')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_size')->nullable();
            $table->enum('status', ['draft', 'dikirim', 'disetujui', 'ditolak'])->default('draft');
            $table->text('catatan_review')->nullable();
            $table->unsignedBigInteger('uploaded_by');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['perangkat_daerah_id', 'tahun']);
            
            // Tambahkan foreign key setelah tabel sudah ada
            // Gunakan raw SQL untuk menghindari error
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('lkip_perangkat_daerah');
    }
};