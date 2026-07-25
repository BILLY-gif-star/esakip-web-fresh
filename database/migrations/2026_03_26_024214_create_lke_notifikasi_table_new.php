<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek apakah tabel sudah ada
        if (!Schema::hasTable('lke_notifikasi')) {
            Schema::create('lke_notifikasi', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('perangkat_daerah_id');
                $table->string('opd_nama');
                $table->year('tahun');
                $table->decimal('nilai_akhir', 10, 2)->default(0);
                $table->decimal('nilai_sebelumnya', 10, 2)->default(0)->nullable();
                $table->string('predikat', 5);
                $table->text('message');
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                
                // Index untuk mempercepat query
                $table->index(['perangkat_daerah_id', 'tahun']);
                $table->index('is_read');
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lke_notifikasi');
    }
};