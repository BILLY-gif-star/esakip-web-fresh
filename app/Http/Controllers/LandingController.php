<?php

namespace App\Http\Controllers;

use App\Models\LandingSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LandingController extends Controller
{
    /**
     * Slot gambar tunggal yang bisa dikelola admin (logo & foto latar).
     * Galeri slide dikelola terpisah lewat tabel landing_slides.
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

        $slides = LandingSlide::active()->ordered()->get();

        return view('welcome', ['img' => $images, 'slides' => $slides]);
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
                'label'     => $slot['label'],
                'current'   => $saved[$key] ?? asset($slot['default']),
                'is_custom' => isset($saved[$key]),
            ];
        }

        $slides = LandingSlide::ordered()->get();

        return view('admin.landing', compact('slots', 'slides'));
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
            mkdir($folder, 0755, true);
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

    // ════════════════════════════════════════════════════
    //  GALERI SLIDE — CRUD
    // ════════════════════════════════════════════════════
    private function slideRules(bool $fileRequired): array
    {
        return [
            'tag'        => 'required|string|max:60',
            'title'      => 'required|string|max:160',
            'sort_order' => 'nullable|integer|min:0',
            'file'       => ($fileRequired ? 'required' : 'nullable') . '|file|mimes:jpg,jpeg,png,webp,svg|max:5120',
        ];
    }

    private function slideMessages(): array
    {
        return [
            'tag.required'   => 'Tag wajib diisi.',
            'title.required' => 'Judul wajib diisi.',
            'file.required'  => 'Gambar wajib diunggah.',
            'file.mimes'     => 'Format harus JPG, PNG, WEBP, atau SVG.',
            'file.max'       => 'Ukuran file maksimal 5MB.',
        ];
    }

    private function saveSlideImage($file): string
    {
        $folder = public_path('assets/landing/slides');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $fname = 'slide_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
        $file->move($folder, $fname);

        return 'assets/landing/slides/' . $fname;
    }

    private function deleteSlideImage(?string $path): void
    {
        // Hanya hapus file yang memang diupload lewat panel ini
        if ($path && Str::startsWith($path, 'assets/landing/slides/')) {
            $full = public_path($path);
            if (is_file($full)) {
                @unlink($full);
            }
        }
    }

    // POST /admin/landing/slides
    public function slideStore(Request $request)
    {
        $request->validate($this->slideRules(true), $this->slideMessages());

        LandingSlide::create([
            'tag'        => $request->tag,
            'title'      => $request->title,
            'image'      => $this->saveSlideImage($request->file('file')),
            'sort_order' => $request->filled('sort_order')
                                ? (int) $request->sort_order
                                : ((int) LandingSlide::max('sort_order') + 1),
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Slide baru berhasil ditambahkan.');
    }

    // PUT /admin/landing/slides/{slide}
    public function slideUpdate(Request $request, LandingSlide $slide)
    {
        $request->validate($this->slideRules(false), $this->slideMessages());

        $data = [
            'tag'        => $request->tag,
            'title'      => $request->title,
            'sort_order' => $request->filled('sort_order') ? (int) $request->sort_order : $slide->sort_order,
            'is_active'  => $request->boolean('is_active'),
        ];

        if ($request->hasFile('file')) {
            $this->deleteSlideImage($slide->image);
            $data['image'] = $this->saveSlideImage($request->file('file'));
        }

        $slide->update($data);

        return back()->with('success', 'Slide "' . $slide->title . '" berhasil diperbarui.');
    }

    // DELETE /admin/landing/slides/{slide}
    public function slideDestroy(LandingSlide $slide)
    {
        $this->deleteSlideImage($slide->image);
        $slide->delete();

        return back()->with('success', 'Slide berhasil dihapus.');
    }
}