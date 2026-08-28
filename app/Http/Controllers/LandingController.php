<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LandingController extends Controller
{
    /**
     * Daftar slot gambar yang bisa dikelola admin.
     * key       => dipakai sebagai kolom `key` di tabel landing_settings
     * label     => nama yang ditampilkan di panel admin
     * default   => path fallback (asset()) kalau admin belum pernah upload
     */
    private function slots(): array
    {
        return [
            'logo' => [
                'label'   => 'Logo / Lambang (letterhead)',
                'default' => 'assets/logo_ntt.png',
            ],
            'staff_photo' => [
                'label'   => 'Foto Latar Belakang (seluruh halaman)',
                'default' => 'images/staff-photo.jpg',
            ],
            'gallery_1' => [
                'label'   => 'Galeri — Slide 1 (Rapat Koordinasi)',
                'default' => 'assets/rapat-koordinasi.png',
            ],
            'gallery_2' => [
                'label'   => 'Galeri — Slide 2 (Cascading Kinerja)',
                'default' => 'assets/cascading-kinerja.png',
            ],
            'gallery_3' => [
                'label'   => 'Galeri — Slide 3 (Pelaporan LKIP)',
                'default' => 'assets/pelaporan-lkip.png',
            ],
        ];
    }

    // ════════════════════════════════════════════════════
    //  HALAMAN PUBLIK — /welcome
    // ════════════════════════════════════════════════════
    public function show()
    {
        $saved = DB::table('landing_settings')->pluck('value', 'key');

        $images = [];
        foreach ($this->slots() as $key => $slot) {
            $images[$key] = $saved[$key] ?? asset($slot['default']);
        }

        return view('welcome', ['img' => $images]);
    }

    // ════════════════════════════════════════════════════
    //  ADMIN — GET /admin/landing
    // ════════════════════════════════════════════════════
    public function admin()
    {
        $saved = DB::table('landing_settings')->pluck('value', 'key');

        $slots = [];
        foreach ($this->slots() as $key => $slot) {
            $slots[$key] = [
                'label'   => $slot['label'],
                'current' => $saved[$key] ?? asset($slot['default']),
                'is_custom' => isset($saved[$key]),
            ];
        }

        return view('admin.landing', compact('slots'));
    }

    // ════════════════════════════════════════════════════
    //  ADMIN — POST /admin/landing/{key}  (upload 1 gambar)
    // ════════════════════════════════════════════════════
    public function update(Request $request, string $key)
    {
        if (!array_key_exists($key, $this->slots())) {
            abort(404);
        }

        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,svg|max:5120',
        ], [
            'file.mimes' => 'Format harus JPG, PNG, WEBP, atau SVG.',
            'file.max'   => 'Ukuran file maksimal 5MB.',
        ]);

        $file   = $request->file('file');
        $ext    = $file->getClientOriginalExtension();
        $fname  = $key . '_' . time() . '.' . $ext;
        $folder = public_path('assets/landing');

        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        $file->move($folder, $fname);
        $url = asset('assets/landing/' . $fname);

        DB::table('landing_settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $url, 'updated_at' => now(), 'created_at' => now()]
        );

        return back()->with('success', 'Gambar "' . $this->slots()[$key]['label'] . '" berhasil diperbarui.');
    }

    // ════════════════════════════════════════════════════
    //  ADMIN — POST /admin/landing/{key}/reset (kembalikan default)
    // ════════════════════════════════════════════════════
    public function reset(string $key)
    {
        if (!array_key_exists($key, $this->slots())) {
            abort(404);
        }

        DB::table('landing_settings')->where('key', $key)->delete();

        return back()->with('success', 'Gambar "' . $this->slots()[$key]['label'] . '" dikembalikan ke default.');
    }
}
