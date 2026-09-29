<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_slides', function (Blueprint $table) {
            $table->id();
            $table->string('tag', 60);
            $table->string('title', 160);
            $table->string('image');                       // path relatif (assets/...) atau URL lama
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pindahkan 3 slide lama (termasuk gambar kustom yang sudah pernah diupload)
        $saved = DB::table('landing_settings')
            ->whereIn('key', ['gallery_1', 'gallery_2', 'gallery_3'])
            ->pluck('value', 'key');

        $seed = [
            ['gallery_1', 'Rapat Koordinasi',   'Evaluasi Kinerja Perangkat Daerah se-NTT',            'assets/rapat-koordinasi.png'],
            ['gallery_2', 'Cascading Kinerja',  'Sinkronisasi Indikator OPD dengan Sasaran Provinsi',  'assets/cascading-kinerja.png'],
            ['gallery_3', 'Pelaporan LKIP',     'Dokumentasi Akuntabilitas Kinerja Terpadu',           'assets/pelaporan-lkip.png'],
        ];

        foreach ($seed as $i => [$key, $tag, $title, $default]) {
            DB::table('landing_slides')->insert([
                'tag'        => $tag,
                'title'      => $title,
                'image'      => $saved[$key] ?? $default,
                'sort_order' => $i + 1,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('landing_settings')
            ->whereIn('key', ['gallery_1', 'gallery_2', 'gallery_3'])
            ->delete();
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_slides');
    }
};