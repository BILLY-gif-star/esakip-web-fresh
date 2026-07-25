<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * BASELINE MIGRATION
 * Mencakup semua tabel yang dibuat secara manual (tidak lewat migration).
 * File ini dibuat berdasarkan dump SQL e_sakip database.
 *
 * Urutan pembuatan tabel memperhatikan ketergantungan foreign key:
 *   perangkat_daerah → pengguna, semua tabel lain
 *   lke_komponen     → lke_kriteria, lke_penilaian, dst
 *   klaster_komponen → klaster_kriteria, klaster_penilaian, dst
 */
return new class extends Migration
{
    public function up(): void
    {
        /* ══════════════════════════════════════════
           1. PERANGKAT DAERAH
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('perangkat_daerah')) {
            Schema::create('perangkat_daerah', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nama', 200);
                $table->string('singkatan', 50)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        /* ══════════════════════════════════════════
           2. PENGGUNA
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('pengguna')) {
            Schema::create('pengguna', function (Blueprint $table) {
                $table->increments('id');
                $table->string('username', 100)->unique();
                $table->string('password');
                $table->string('nama', 200);
                $table->enum('role', ['admin', 'operator'])->default('operator');
                $table->unsignedInteger('perangkat_daerah_id')->nullable();
                $table->tinyInteger('is_active')->default(1);
                $table->timestamp('last_login')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->enum('status_daftar', ['menunggu', 'disetujui', 'ditolak'])->default('disetujui');
                $table->text('catatan')->nullable();
            });
        }

        /* ══════════════════════════════════════════
           3. SASARAN STRATEGIS
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('sasaran_strategis')) {
            Schema::create('sasaran_strategis', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('perangkat_daerah_id');
                $table->integer('nomor')->default(1);
                $table->text('uraian');
                $table->integer('tahun');
                $table->timestamp('created_at')->useCurrent();
            });
        }

        /* ══════════════════════════════════════════
           4. IKU
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('iku')) {
            Schema::create('iku', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('sasaran_id');
                $table->unsignedInteger('perangkat_daerah_id');
                $table->text('nama');
                $table->string('penanggung_jawab', 200)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        /* ══════════════════════════════════════════
           5. TARGET KINERJA
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('target_kinerja')) {
            Schema::create('target_kinerja', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('iku_id');
                $table->unsignedInteger('perangkat_daerah_id');
                $table->integer('tahun');
                $table->string('target_nilai', 50)->nullable();
                $table->string('satuan', 50)->nullable();
            });
        }

        /* ══════════════════════════════════════════
           6. CAPAIAN KINERJA
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('capaian_kinerja')) {
            Schema::create('capaian_kinerja', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('iku_id');
                $table->unsignedInteger('perangkat_daerah_id');
                $table->integer('tahun');
                $table->tinyInteger('triwulan');
                $table->decimal('realisasi', 10, 2)->nullable();
                $table->text('keterangan')->nullable();
                $table->string('file_path')->nullable();          // ditambah via migration 2026_04_03_104439
                $table->unsignedInteger('created_by')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        /* ══════════════════════════════════════════
           7. DOKUMEN OPD
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('dokumen_opd')) {
            Schema::create('dokumen_opd', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('perangkat_daerah_id');
                $table->string('jenis', 100);
                $table->integer('tahun');
                $table->string('nama_file');
                $table->text('path_file');
                $table->text('keterangan')->nullable();
                $table->unsignedInteger('uploaded_by')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
                $table->text('catatan_admin')->nullable();
                $table->unsignedInteger('reviewed_by')->nullable();
                $table->timestamp('reviewed_at')->nullable();
            });
        }

        /* ══════════════════════════════════════════
           8. PERJANJIAN KINERJA
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('perjanjian_kinerja')) {
            Schema::create('perjanjian_kinerja', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('perangkat_daerah_id')->nullable();
                $table->integer('tahun');
                $table->string('jenis', 100)->nullable();
                $table->string('nama_file')->nullable();
                $table->text('path_file')->nullable();
                $table->string('pihak_pertama_nama', 200)->nullable();
                $table->string('pihak_pertama_jabatan', 200)->nullable();
                $table->string('pihak_kedua_nama', 200)->nullable();
                $table->string('pihak_kedua_jabatan', 200)->nullable();
                $table->date('tanggal_ttd')->nullable();
                $table->string('status', 50)->nullable();
                $table->unsignedInteger('uploaded_by')->nullable();
                $table->unsignedInteger('created_by')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            });
        }

        /* ══════════════════════════════════════════
           9. TEMPLATE DOKUMEN
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('template_dokumen')) {
            Schema::create('template_dokumen', function (Blueprint $table) {
                $table->increments('id');
                $table->string('jenis', 100);
                $table->integer('tahun');
                $table->string('nama_file');
                $table->text('path_file');
                $table->unsignedInteger('uploaded_by')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            });
        }

        /* ══════════════════════════════════════════
           10. LKE KOMPONEN
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('lke_komponen')) {
            Schema::create('lke_komponen', function (Blueprint $table) {
                $table->increments('id');
                $table->string('kode', 10);
                $table->text('nama');
                $table->decimal('bobot', 5, 2)->default(0);
                $table->unsignedInteger('parent_id')->nullable();
                $table->integer('urutan')->default(1);
                $table->timestamp('created_at')->useCurrent();
            });
        }

        /* ══════════════════════════════════════════
           11. LKE KRITERIA
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('lke_kriteria')) {
            Schema::create('lke_kriteria', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('komponen_id');
                $table->integer('nomor');
                $table->text('uraian');
                $table->timestamp('created_at')->useCurrent();
            });
        }

        /* ══════════════════════════════════════════
           12. LKE PENILAIAN
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('lke_penilaian')) {
            Schema::create('lke_penilaian', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('perangkat_daerah_id');
                $table->integer('tahun');
                $table->unsignedInteger('komponen_id');
                $table->decimal('bobot', 10, 2)->nullable();      // ditambah via migration 2026_03_31_052851
                $table->enum('jawaban', ['AA','A','BB','B','CC','C','D','E'])->nullable();
                $table->decimal('nilai', 5, 2)->default(0);
                $table->decimal('persentase', 10, 2)->default(0);
                $table->text('catatan_admin')->nullable();
                $table->text('catatan_operator')->nullable();
                $table->text('catatan')->nullable();               // ditambah via migration 2026_03_31_054045
                $table->text('daftar_evidence')->nullable();
                $table->unsignedInteger('dinilai_oleh')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            });
        }

        /* ══════════════════════════════════════════
           13. LKE PENILAIAN KRITERIA
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('lke_penilaian_kriteria')) {
            Schema::create('lke_penilaian_kriteria', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('kriteria_id');
                $table->unsignedBigInteger('perangkat_daerah_id');
                $table->integer('tahun');
                $table->string('jawaban', 5)->nullable();
                $table->decimal('nilai', 8, 2)->default(0);
                $table->text('catatan_admin')->nullable();
                $table->text('catatan_operator')->nullable();
                $table->unsignedBigInteger('dinilai_oleh')->nullable();
                $table->timestamps();
                $table->enum('jawaban_admin', ['YA', 'TIDAK'])->nullable();
                $table->text('komentar_admin')->nullable();
                $table->text('daftar_evidence')->nullable();
            });
        }

        /* ══════════════════════════════════════════
           14. LKE DOKUMEN KRITERIA
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('lke_dokumen_kriteria')) {
            Schema::create('lke_dokumen_kriteria', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('kriteria_id');
                $table->unsignedBigInteger('perangkat_daerah_id');
                $table->integer('tahun');
                $table->string('nama_file');
                $table->string('path_file');
                $table->unsignedBigInteger('uploaded_by');
                $table->timestamps();
            });
        }

        /* ══════════════════════════════════════════
           15. LKE DOKUMEN PENDUKUNG
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('lke_dokumen_pendukung')) {
            Schema::create('lke_dokumen_pendukung', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('komponen_id');
                $table->unsignedBigInteger('perangkat_daerah_id');
                $table->integer('tahun');
                $table->string('nama_file');
                $table->string('path_file');
                $table->unsignedBigInteger('uploaded_by');
                $table->timestamps();
            });
        }

        /* ══════════════════════════════════════════
           16. KLASTER KOMPONEN
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('klaster_komponen')) {
            Schema::create('klaster_komponen', function (Blueprint $table) {
                $table->id();
                $table->string('klaster_type', 20)->comment('utama, pendukung, tambahan');
                $table->integer('klaster_level')->comment('1,2,3');
                $table->unsignedBigInteger('parent_id')->nullable()->comment('untuk sub komponen');
                $table->string('kode', 20)->nullable();
                $table->string('nama');
                $table->integer('urutan')->default(0);
                $table->decimal('bobot', 10, 2)->nullable();
                $table->timestamps();
            });
        }

        /* ══════════════════════════════════════════
           17. KLASTER KRITERIA
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('klaster_kriteria')) {
            Schema::create('klaster_kriteria', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('komponen_id')->comment('id sub komponen dari klaster_komponen');
                $table->integer('nomor');
                $table->text('uraian');
                $table->timestamps();
            });
        }

        /* ══════════════════════════════════════════
           18. KLASTER PENILAIAN
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('klaster_penilaian')) {
            Schema::create('klaster_penilaian', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('perangkat_daerah_id');
                $table->year('tahun');
                $table->unsignedBigInteger('komponen_id')->comment('id sub komponen');
                $table->string('jawaban', 5)->nullable()->comment('AA, A, BB, B, CC, C, D, E');
                $table->decimal('nilai', 10, 2)->nullable();
                $table->decimal('persentase', 10, 2)->default(0);
                $table->text('catatan')->nullable();
                $table->unsignedBigInteger('dinilai_oleh')->nullable();
                $table->timestamps();
            });
        }

        /* ══════════════════════════════════════════
           19. KLASTER PENILAIAN KRITERIA
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('klaster_penilaian_kriteria')) {
            Schema::create('klaster_penilaian_kriteria', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('kriteria_id');
                $table->unsignedBigInteger('perangkat_daerah_id');
                $table->year('tahun');
                $table->text('komentar_admin')->nullable();
                $table->text('catatan_operator')->nullable();
                $table->text('daftar_evidence')->nullable();
                $table->timestamps();
            });
        }

        /* ══════════════════════════════════════════
           20. KLASTER DOKUMEN KRITERIA
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('klaster_dokumen_kriteria')) {
            Schema::create('klaster_dokumen_kriteria', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('kriteria_id');
                $table->unsignedBigInteger('perangkat_daerah_id');
                $table->year('tahun');
                $table->string('nama_file');
                $table->string('path_file');
                $table->unsignedBigInteger('uploaded_by');
                $table->timestamps();
            });
        }

        /* ══════════════════════════════════════════
           21. PENGUKURAN PERIODIK
               (file migration sudah ada tapi belum
                terdaftar di tabel migrations)
        ══════════════════════════════════════════ */
        if (!Schema::hasTable('pengukuran_periodik')) {
            Schema::create('pengukuran_periodik', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('perangkat_daerah_id');
                $table->year('tahun');
                $table->text('sasaran_strategis')->nullable();
                $table->string('indikator');
                $table->string('satuan')->nullable();
                $table->decimal('target', 15, 2)->nullable();
                $table->decimal('realisasi', 15, 2)->nullable();
                $table->text('sasaran_program')->nullable();
                $table->string('penanggung_jawab')->nullable();
                $table->decimal('anggaran', 15, 2)->default(0);
                // Target Kinerja per TW
                $table->string('target_kinerja_tw1', 50)->nullable();
                $table->string('target_kinerja_tw2', 50)->nullable();
                $table->string('target_kinerja_tw3', 50)->nullable();
                $table->string('target_kinerja_tw4', 50)->nullable();
                // Target Program per TW
                $table->string('target_program_tw1', 50)->nullable();
                $table->string('target_program_tw2', 50)->nullable();
                $table->string('target_program_tw3', 50)->nullable();
                $table->string('target_program_tw4', 50)->nullable();
                // Anggaran per TW
                $table->decimal('anggaran_tw1', 15, 2)->default(0);
                $table->decimal('anggaran_tw2', 15, 2)->default(0);
                $table->decimal('anggaran_tw3', 15, 2)->default(0);
                $table->decimal('anggaran_tw4', 15, 2)->default(0);
                // Capaian Kinerja per TW
                $table->string('capaian_kinerja_tw1', 50)->nullable();
                $table->string('capaian_kinerja_tw2', 50)->nullable();
                $table->string('capaian_kinerja_tw3', 50)->nullable();
                $table->string('capaian_kinerja_tw4', 50)->nullable();
                // Capaian Program per TW
                $table->string('capaian_program_tw1', 50)->nullable();
                $table->string('capaian_program_tw2', 50)->nullable();
                $table->string('capaian_program_tw3', 50)->nullable();
                $table->string('capaian_program_tw4', 50)->nullable();
                // Capaian Anggaran per TW
                $table->decimal('capaian_anggaran_tw1', 15, 2)->default(0);
                $table->decimal('capaian_anggaran_tw2', 15, 2)->default(0);
                $table->decimal('capaian_anggaran_tw3', 15, 2)->default(0);
                $table->decimal('capaian_anggaran_tw4', 15, 2)->default(0);
                // Lainnya
                $table->text('keterangan')->nullable();
                $table->string('file_path')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Hapus dalam urutan terbalik (yang punya FK dihapus dulu)
        $tables = [
            'pengukuran_periodik',
            'klaster_dokumen_kriteria',
            'klaster_penilaian_kriteria',
            'klaster_penilaian',
            'klaster_kriteria',
            'klaster_komponen',
            'lke_dokumen_pendukung',
            'lke_dokumen_kriteria',
            'lke_penilaian_kriteria',
            'lke_penilaian',
            'lke_kriteria',
            'lke_komponen',
            'template_dokumen',
            'perjanjian_kinerja',
            'dokumen_opd',
            'capaian_kinerja',
            'target_kinerja',
            'iku',
            'sasaran_strategis',
            'pengguna',
            'perangkat_daerah',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};