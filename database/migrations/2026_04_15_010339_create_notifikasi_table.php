<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('judul');
            $table->text('pesan');
            $table->string('tipe');
            $table->string('ikon')->default('📄');
            $table->string('warna')->default('indigo');
            $table->string('url')->nullable();
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->string('nama_opd')->nullable();
            $table->timestamp('dibaca_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'dibaca_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};