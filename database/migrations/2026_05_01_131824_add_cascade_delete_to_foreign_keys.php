<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // 1. TABEL lke_dokumen_kriteria
        DB::statement('ALTER TABLE lke_dokumen_kriteria DROP FOREIGN KEY lke_dokumen_kriteria_uploaded_by_foreign');
        DB::statement('ALTER TABLE lke_dokumen_kriteria 
            ADD CONSTRAINT lke_dokumen_kriteria_uploaded_by_foreign 
            FOREIGN KEY (uploaded_by) REFERENCES pengguna(id) ON DELETE CASCADE');

        // 2. TABEL notifikasi
        DB::statement('ALTER TABLE notifikasi DROP FOREIGN KEY notifikasi_user_id_pengguna_foreign');
        DB::statement('ALTER TABLE notifikasi 
            ADD CONSTRAINT notifikasi_user_id_pengguna_foreign 
            FOREIGN KEY (user_id) REFERENCES pengguna(id) ON DELETE CASCADE');

        // 3. TABEL percakapan (admin_user_id)
        DB::statement('ALTER TABLE percakapan DROP FOREIGN KEY percakapan_admin_user_id_foreign');
        DB::statement('ALTER TABLE percakapan 
            ADD CONSTRAINT percakapan_admin_user_id_foreign 
            FOREIGN KEY (admin_user_id) REFERENCES pengguna(id) ON DELETE CASCADE');

        // 4. TABEL percakapan (opd_user_id)
        DB::statement('ALTER TABLE percakapan DROP FOREIGN KEY percakapan_opd_user_id_foreign');
        DB::statement('ALTER TABLE percakapan 
            ADD CONSTRAINT percakapan_opd_user_id_foreign 
            FOREIGN KEY (opd_user_id) REFERENCES pengguna(id) ON DELETE CASCADE');

        // 5. TABEL pesan
        DB::statement('ALTER TABLE pesan DROP FOREIGN KEY pesan_pengirim_id_foreign');
        DB::statement('ALTER TABLE pesan 
            ADD CONSTRAINT pesan_pengirim_id_foreign 
            FOREIGN KEY (pengirim_id) REFERENCES pengguna(id) ON DELETE CASCADE');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Kembalikan ke RESTRICT (tanpa CASCADE)
        DB::statement('ALTER TABLE lke_dokumen_kriteria DROP FOREIGN KEY lke_dokumen_kriteria_uploaded_by_foreign');
        DB::statement('ALTER TABLE lke_dokumen_kriteria 
            ADD CONSTRAINT lke_dokumen_kriteria_uploaded_by_foreign 
            FOREIGN KEY (uploaded_by) REFERENCES pengguna(id)');

        DB::statement('ALTER TABLE notifikasi DROP FOREIGN KEY notifikasi_user_id_pengguna_foreign');
        DB::statement('ALTER TABLE notifikasi 
            ADD CONSTRAINT notifikasi_user_id_pengguna_foreign 
            FOREIGN KEY (user_id) REFERENCES pengguna(id)');

        DB::statement('ALTER TABLE percakapan DROP FOREIGN KEY percakapan_admin_user_id_foreign');
        DB::statement('ALTER TABLE percakapan 
            ADD CONSTRAINT percakapan_admin_user_id_foreign 
            FOREIGN KEY (admin_user_id) REFERENCES pengguna(id)');

        DB::statement('ALTER TABLE percakapan DROP FOREIGN KEY percakapan_opd_user_id_foreign');
        DB::statement('ALTER TABLE percakapan 
            ADD CONSTRAINT percakapan_opd_user_id_foreign 
            FOREIGN KEY (opd_user_id) REFERENCES pengguna(id)');

        DB::statement('ALTER TABLE pesan DROP FOREIGN KEY pesan_pengirim_id_foreign');
        DB::statement('ALTER TABLE pesan 
            ADD CONSTRAINT pesan_pengirim_id_foreign 
            FOREIGN KEY (pengirim_id) REFERENCES pengguna(id)');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};