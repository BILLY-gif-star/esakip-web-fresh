<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengukuran_periodik', function (Blueprint $table) {
            // Hapus kolom lama (jika ada)
            if (Schema::hasColumn('pengukuran_periodik', 'target')) {
                $table->dropColumn('target');
            }
            if (Schema::hasColumn('pengukuran_periodik', 'realisasi')) {
                $table->dropColumn('realisasi');
            }
            if (Schema::hasColumn('pengukuran_periodik', 'anggaran')) {
                $table->dropColumn('anggaran');
            }
            if (Schema::hasColumn('pengukuran_periodik', 'file_path')) {
                $table->dropColumn('file_path');
            }

            // Tambah kolom untuk 4 triwulan - Target Kinerja
            if (!Schema::hasColumn('pengukuran_periodik', 'target_kinerja_tw1')) {
                $table->string('target_kinerja_tw1', 100)->nullable();
            }
            if (!Schema::hasColumn('pengukuran_periodik', 'target_kinerja_tw2')) {
                $table->string('target_kinerja_tw2', 100)->nullable();
            }
            if (!Schema::hasColumn('pengukuran_periodik', 'target_kinerja_tw3')) {
                $table->string('target_kinerja_tw3', 100)->nullable();
            }
            if (!Schema::hasColumn('pengukuran_periodik', 'target_kinerja_tw4')) {
                $table->string('target_kinerja_tw4', 100)->nullable();
            }

            // Target Program/Kegiatan
            if (!Schema::hasColumn('pengukuran_periodik', 'target_program_tw1')) {
                $table->string('target_program_tw1', 100)->nullable();
            }
            if (!Schema::hasColumn('pengukuran_periodik', 'target_program_tw2')) {
                $table->string('target_program_tw2', 100)->nullable();
            }
            if (!Schema::hasColumn('pengukuran_periodik', 'target_program_tw3')) {
                $table->string('target_program_tw3', 100)->nullable();
            }
            if (!Schema::hasColumn('pengukuran_periodik', 'target_program_tw4')) {
                $table->string('target_program_tw4', 100)->nullable();
            }

            // Anggaran
            if (!Schema::hasColumn('pengukuran_periodik', 'anggaran_tw1')) {
                $table->decimal('anggaran_tw1', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('pengukuran_periodik', 'anggaran_tw2')) {
                $table->decimal('anggaran_tw2', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('pengukuran_periodik', 'anggaran_tw3')) {
                $table->decimal('anggaran_tw3', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('pengukuran_periodik', 'anggaran_tw4')) {
                $table->decimal('anggaran_tw4', 15, 2)->nullable();
            }

            // Capaian Kinerja
            if (!Schema::hasColumn('pengukuran_periodik', 'capaian_kinerja_tw1')) {
                $table->string('capaian_kinerja_tw1', 100)->nullable();
            }
            if (!Schema::hasColumn('pengukuran_periodik', 'capaian_kinerja_tw2')) {
                $table->string('capaian_kinerja_tw2', 100)->nullable();
            }
            if (!Schema::hasColumn('pengukuran_periodik', 'capaian_kinerja_tw3')) {
                $table->string('capaian_kinerja_tw3', 100)->nullable();
            }
            if (!Schema::hasColumn('pengukuran_periodik', 'capaian_kinerja_tw4')) {
                $table->string('capaian_kinerja_tw4', 100)->nullable();
            }

            // Capaian Program
            if (!Schema::hasColumn('pengukuran_periodik', 'capaian_program_tw1')) {
                $table->string('capaian_program_tw1', 100)->nullable();
            }
            if (!Schema::hasColumn('pengukuran_periodik', 'capaian_program_tw2')) {
                $table->string('capaian_program_tw2', 100)->nullable();
            }
            if (!Schema::hasColumn('pengukuran_periodik', 'capaian_program_tw3')) {
                $table->string('capaian_program_tw3', 100)->nullable();
            }
            if (!Schema::hasColumn('pengukuran_periodik', 'capaian_program_tw4')) {
                $table->string('capaian_program_tw4', 100)->nullable();
            }

            // Capaian Anggaran
            if (!Schema::hasColumn('pengukuran_periodik', 'capaian_anggaran_tw1')) {
                $table->decimal('capaian_anggaran_tw1', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('pengukuran_periodik', 'capaian_anggaran_tw2')) {
                $table->decimal('capaian_anggaran_tw2', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('pengukuran_periodik', 'capaian_anggaran_tw3')) {
                $table->decimal('capaian_anggaran_tw3', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('pengukuran_periodik', 'capaian_anggaran_tw4')) {
                $table->decimal('capaian_anggaran_tw4', 15, 2)->nullable();
            }

            // Kolom file bukti
            if (!Schema::hasColumn('pengukuran_periodik', 'file_bukti')) {
                $table->string('file_bukti')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengukuran_periodik', function (Blueprint $table) {
            // Daftar kolom yang akan dihapus jika rollback
            $columns = [
                'target_kinerja_tw1', 'target_kinerja_tw2', 'target_kinerja_tw3', 'target_kinerja_tw4',
                'target_program_tw1', 'target_program_tw2', 'target_program_tw3', 'target_program_tw4',
                'anggaran_tw1', 'anggaran_tw2', 'anggaran_tw3', 'anggaran_tw4',
                'capaian_kinerja_tw1', 'capaian_kinerja_tw2', 'capaian_kinerja_tw3', 'capaian_kinerja_tw4',
                'capaian_program_tw1', 'capaian_program_tw2', 'capaian_program_tw3', 'capaian_program_tw4',
                'capaian_anggaran_tw1', 'capaian_anggaran_tw2', 'capaian_anggaran_tw3', 'capaian_anggaran_tw4',
                'file_bukti'
            ];
            $table->dropColumn($columns);
            
            // Kembalikan kolom lama
            $table->string('target', 100)->nullable();
            $table->string('realisasi', 100)->nullable();
            $table->decimal('anggaran', 15, 2)->nullable();
            $table->string('file_path')->nullable();
        });
    }
};