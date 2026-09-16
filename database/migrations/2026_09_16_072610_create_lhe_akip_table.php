<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lhe_akip', function (Blueprint $table) {
            $table->id();

            // Relasi ke OPD asli (bukan pengguna/akun — pengguna.nama itu nama akun,
            // OPD sebenarnya ada di tabel perangkat_daerah)
            $table->unsignedInteger('perangkat_daerah_id');
            $table->foreign('perangkat_daerah_id')->references('id')->on('perangkat_daerah')->onDelete('cascade');

            // ── Header Surat ────────────────────────────────────
            $table->string('nomor_surat');
            $table->date('tanggal_surat');
            $table->year('tahun_evaluasi'); // dipakai untuk join ke lke_penilaian.tahun

            // ── Dasar Pelaksanaan ───────────────────────────────
            $table->string('nomor_sk_tim')->nullable();
            $table->date('periode_mulai')->nullable();
            $table->date('periode_selesai')->nullable();

            // CATATAN: nilai & kategori TIDAK disimpan di sini.
            // Nilai diambil live dari tabel lke_penilaian yang sudah ada
            // (lihat LheAkip::hitungNilai() di Model).

            // ── Uraian Narasi per Komponen ──────────────────────
            $table->text('uraian_perencanaan')->nullable();
            $table->text('uraian_pengukuran')->nullable();
            $table->text('uraian_pelaporan')->nullable();
            $table->text('uraian_evaluasi_internal')->nullable();

            // ── Poin Catatan Perbaikan per Komponen (list) ──────
            $table->json('catatan_perencanaan')->nullable();
            $table->json('catatan_pengukuran')->nullable();
            $table->json('catatan_pelaporan')->nullable();
            $table->json('catatan_evaluasi_internal')->nullable();

            // ── Rekomendasi (dikelompokkan per komponen) ────────
            $table->json('rekomendasi')->nullable();

            $table->text('penutup')->nullable();

            // ── Penandatangan ───────────────────────────────────
            $table->string('nama_penandatangan')->nullable();
            $table->string('jabatan_penandatangan')->nullable();
            $table->string('pangkat_penandatangan')->nullable();
            $table->string('nip_penandatangan')->nullable();

            $table->enum('status', ['draft', 'final'])->default('draft');

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();

            $table->timestamps();

            $table->unique(['perangkat_daerah_id', 'tahun_evaluasi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lhe_akip');
    }
};