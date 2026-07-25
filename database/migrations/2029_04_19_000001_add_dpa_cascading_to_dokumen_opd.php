<?php
// database/migrations/2025_04_19_000001_add_dpa_cascading_to_dokumen_opd.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan tabel dokumen_opd ada dengan semua kolom yang dibutuhkan
        if (!Schema::hasTable('dokumen_opd')) {
            Schema::create('dokumen_opd', function (Blueprint $table) {
                $table->id();
                $table->foreignId('perangkat_daerah_id')->constrained('perangkat_daerah')->onDelete('cascade');
                $table->string('jenis');          // renstra-iku, perjanjian-kinerja, perjanjian-iku,
                                                  // pelaksanaan-anggaran, dpa, cascading-pohon-kinerja
                $table->integer('tahun');
                $table->string('nama_file');
                $table->string('path_file');
                $table->string('status')->default('menunggu'); // menunggu | disetujui | ditolak
                $table->text('keterangan')->nullable();
                $table->text('catatan_reviewer')->nullable();
                $table->foreignId('uploaded_by')->nullable()->constrained('pengguna')->nullOnDelete();
                $table->foreignId('reviewed_by')->nullable()->constrained('pengguna')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
                $table->index(['perangkat_daerah_id', 'jenis', 'tahun']);
            });
        } else {
            // Tambah kolom yang mungkin belum ada
            Schema::table('dokumen_opd', function (Blueprint $table) {
                if (!Schema::hasColumn('dokumen_opd', 'catatan_reviewer')) {
                    $table->text('catatan_reviewer')->nullable()->after('keterangan');
                }
                if (!Schema::hasColumn('dokumen_opd', 'reviewed_by')) {
                    $table->unsignedBigInteger('reviewed_by')->nullable()->after('catatan_reviewer');
                }
                if (!Schema::hasColumn('dokumen_opd', 'reviewed_at')) {
                    $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('dokumen_opd', function (Blueprint $table) {
            $cols = ['catatan_reviewer', 'reviewed_by', 'reviewed_at'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('dokumen_opd', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};