<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel percakapan
        Schema::create('percakapan', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('opd_user_id');   // INT, sama dengan pengguna.id
            $table->unsignedInteger('admin_user_id'); // INT, sama dengan pengguna.id
            $table->timestamp('pesan_terakhir_at')->nullable();
            $table->timestamps();

            $table->foreign('opd_user_id')->references('id')->on('pengguna')->onDelete('cascade');
            $table->foreign('admin_user_id')->references('id')->on('pengguna')->onDelete('cascade');
            $table->unique(['opd_user_id', 'admin_user_id']);
        });

        // Tabel pesan
        Schema::create('pesan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('percakapan_id');
            $table->unsignedInteger('pengirim_id'); // INT, sama dengan pengguna.id
            $table->text('isi');
            $table->timestamp('dibaca_at')->nullable();
            $table->timestamps();

            $table->foreign('percakapan_id')->references('id')->on('percakapan')->onDelete('cascade');
            $table->foreign('pengirim_id')->references('id')->on('pengguna')->onDelete('cascade');
            $table->index(['percakapan_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesan');
        Schema::dropIfExists('percakapan');
    }
};