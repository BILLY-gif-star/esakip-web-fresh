<?php
// database/migrations/2025_04_19_000002_create_template_dokumen_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('template_dokumen')) {
            Schema::create('template_dokumen', function (Blueprint $table) {
                $table->id();
                $table->string('jenis');          // renstra-iku, perjanjian-kinerja, perjanjian-iku,
                                                  // pelaksanaan-anggaran, dpa, cascading-pohon-kinerja
                $table->string('nama');           // Nama template yang ditampilkan
                $table->string('nama_file');      // Nama file asli
                $table->string('path_file');      // Path file di storage
                $table->foreignId('uploaded_by')->constrained('pengguna');
                $table->timestamps();
                
                $table->index('jenis');
            });
        } else {
            // Tambah kolom nama jika belum ada
            Schema::table('template_dokumen', function (Blueprint $table) {
                if (!Schema::hasColumn('template_dokumen', 'nama')) {
                    $table->string('nama')->nullable()->after('jenis');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('template_dokumen');
    }
};