@extends('layouts.app')
@section('title', 'Kelola Landing Page')
@section('page-title', 'Kelola Landing Page')
@section('page-sub', 'Gambar & Foto')

@section('content')
<style>
.landing-grid{ display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:20px; }
.landing-card{ background:linear-gradient(135deg,#1a1f2e,#141824); border:1px solid rgba(255,255,255,.07); border-radius:14px; overflow:hidden; }
.landing-card-preview{ height:160px; background:#0a0f1a; display:flex; align-items:center; justify-content:center; overflow:hidden; }
.landing-card-preview img{ width:100%; height:100%; object-fit:cover; }
.landing-card-body{ padding:16px; }
.landing-card-label{ font-size:13px; font-weight:700; color:#fff; margin-bottom:2px; }
.landing-card-badge{ font-size:10px; font-weight:600; padding:2px 8px; border-radius:20px; display:inline-block; margin-bottom:12px; }
.badge-custom{ background:rgba(16,185,129,.18); color:#34d399; border:1px solid rgba(16,185,129,.3); }
.badge-default{ background:rgba(255,255,255,.06); color:rgba(255,255,255,.4); border:1px solid rgba(255,255,255,.1); }
.landing-card-form{ display:flex; gap:6px; align-items:center; }
.landing-card-form input[type="file"]{
  flex:1; font-size:11px; color:rgba(255,255,255,.6);
  background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1);
  border-radius:8px; padding:6px 8px;
}

/* ── Galeri Slide ── */
.slide-admin{ margin-top:36px; }
.slide-admin h3{ color:#fff; font-size:16px; margin-bottom:14px; }
.slide-row{ display:grid; grid-template-columns:140px 1fr auto; gap:16px; align-items:center;
  background:linear-gradient(135deg,#1a1f2e,#141824); border:1px solid rgba(255,255,255,.07);
  border-radius:14px; padding:14px; margin-bottom:12px; }
.slide-row img{ width:140px; height:84px; object-fit:cover; border-radius:8px; background:#0a0f1a; }
.slide-row .meta .tag{ font-size:10px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; color:#fbbf24; }
.slide-row .meta .ttl{ font-size:14px; font-weight:600; color:#fff; margin:4px 0; }
.slide-row .meta .sub{ font-size:11px; color:rgba(255,255,255,.45); }
.slide-actions{ display:flex; gap:6px; }
.slide-edit{ grid-column:1 / -1; border-top:1px solid rgba(255,255,255,.07); padding-top:12px; }
.slide-edit summary{ cursor:pointer; font-size:12px; color:#60a5fa; }
.slide-form{ display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:10px; margin-top:12px; }
.slide-form label{ display:block; font-size:11px; color:rgba(255,255,255,.55); margin-bottom:4px; }
.slide-form input[type=text], .slide-form input[type=number], .slide-form input[type=file]{
  width:100%; font-size:12px; color:#fff; background:rgba(255,255,255,.05);
  border:1px solid rgba(255,255,255,.1); border-radius:8px; padding:8px 10px; }
.slide-form .full{ grid-column:1 / -1; }
.slide-form .chk{ display:flex; align-items:center; gap:8px; color:rgba(255,255,255,.7); font-size:12px; }
@media(max-width:700px){ .slide-row{ grid-template-columns:1fr; } .slide-row img{ width:100%; height:140px; } }
</style>

<div class="alert alert-info" style="border-color:#3b82f6;color:#60a5fa;background:rgba(59,130,246,.08);">
  ℹ️ Format gambar: JPG, PNG, WEBP, atau SVG — maksimal 5MB. Perubahan langsung tampil di halaman
  <a href="{{ route('welcome') }}" target="_blank" style="color:#93c5fd;text-decoration:underline;">/welcome</a> setelah diunggah.
</div>

@if(session('success'))
  <div class="alert alert-success" style="margin-bottom:14px;">{{ session('success') }}</div>
@endif

{{-- ═══════════ LOGO & FOTO LATAR ═══════════ --}}
<div class="landing-grid">
  @foreach($slots as $key => $slot)
  <div class="landing-card">
    <div class="landing-card-preview">
      <img src="{{ $slot['current'] }}" alt="{{ $slot['label'] }}" onerror="this.style.opacity=0.2">
    </div>
    <div class="landing-card-body">
      <div class="landing-card-label">{{ $slot['label'] }}</div>
      <span class="landing-card-badge {{ $slot['is_custom'] ? 'badge-custom' : 'badge-default' }}">
        {{ $slot['is_custom'] ? '✓ Kustom' : 'Default' }}
      </span>

      <form method="POST" action="{{ route('admin.landing.update', $key) }}"
            enctype="multipart/form-data" class="landing-card-form">
        @csrf
        <input type="file" name="file" accept=".jpg,.jpeg,.png,.webp,.svg" required>
        <button type="submit" class="btn btn-primary btn-sm">Upload</button>
      </form>

      @if($slot['is_custom'])
      <form method="POST" action="{{ route('admin.landing.reset', $key) }}" style="margin-top:8px;"
            onsubmit="return confirm('Kembalikan gambar ini ke default?')">
        @csrf
        <button type="submit" class="btn btn-outline btn-sm" style="width:100%;">↺ Kembalikan ke Default</button>
      </form>
      @endif
    </div>
  </div>
  @endforeach
</div>

{{-- ═══════════ GALERI SLIDE (CRUD) ═══════════ --}}
<div class="slide-admin">
  <h3>🖼️ Galeri Slide</h3>

  @if($errors->any())
    <div class="alert alert-danger" style="margin-bottom:14px;">
      @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
  @endif

  {{-- TAMBAH --}}
  <div class="landing-card" style="margin-bottom:20px;">
    <div class="landing-card-body">
      <div class="landing-card-label" style="margin-bottom:10px;">＋ Tambah Slide Baru</div>
      <form method="POST" action="{{ route('admin.landing.slides.store') }}" enctype="multipart/form-data" class="slide-form">
        @csrf
        <div>
          <label>Tag</label>
          <input type="text" name="tag" maxlength="60" placeholder="mis. Rapat Koordinasi" value="{{ old('tag') }}" required>
        </div>
        <div>
          <label>Urutan (opsional)</label>
          <input type="number" name="sort_order" min="0" placeholder="otomatis di akhir" value="{{ old('sort_order') }}">
        </div>
        <div class="full">
          <label>Judul</label>
          <input type="text" name="title" maxlength="160" placeholder="Judul yang tampil di slide" value="{{ old('title') }}" required>
        </div>
        <div class="full">
          <label>Gambar</label>
          <input type="file" name="file" accept=".jpg,.jpeg,.png,.webp,.svg" required>
        </div>
        <label class="chk full"><input type="checkbox" name="is_active" value="1" checked> Tampilkan di halaman publik</label>
        <div class="full"><button type="submit" class="btn btn-primary btn-sm">Simpan Slide</button></div>
      </form>
    </div>
  </div>

  {{-- DAFTAR SLIDE --}}
  @forelse($slides as $s)
    <div class="slide-row">
      <img src="{{ $s->image_url }}" alt="{{ $s->title }}" onerror="this.style.opacity=.2">
      <div class="meta">
        <div class="tag">{{ $s->tag }}</div>
        <div class="ttl">{{ $s->title }}</div>
        <div class="sub">Urutan {{ $s->sort_order }} ·
          <span class="landing-card-badge {{ $s->is_active ? 'badge-custom' : 'badge-default' }}" style="margin:0;">
            {{ $s->is_active ? 'Aktif' : 'Disembunyikan' }}
          </span>
        </div>
      </div>
      <div class="slide-actions">
        <form method="POST" action="{{ route('admin.landing.slides.destroy', $s) }}"
              onsubmit="return confirm('Hapus slide ini? Gambarnya juga akan dihapus.')">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-outline btn-sm" style="color:#f87171;border-color:rgba(248,113,113,.4);">🗑 Hapus</button>
        </form>
      </div>

      <details class="slide-edit">
        <summary>✎ Edit slide</summary>
        <form method="POST" action="{{ route('admin.landing.slides.update', $s) }}" enctype="multipart/form-data" class="slide-form">
          @csrf @method('PUT')
          <div>
            <label>Tag</label>
            <input type="text" name="tag" maxlength="60" value="{{ $s->tag }}" required>
          </div>
          <div>
            <label>Urutan</label>
            <input type="number" name="sort_order" min="0" value="{{ $s->sort_order }}">
          </div>
          <div class="full">
            <label>Judul</label>
            <input type="text" name="title" maxlength="160" value="{{ $s->title }}" required>
          </div>
          <div class="full">
            <label>Ganti gambar (kosongkan jika tidak diganti)</label>
            <input type="file" name="file" accept=".jpg,.jpeg,.png,.webp,.svg">
          </div>
          <label class="chk full"><input type="checkbox" name="is_active" value="1" {{ $s->is_active ? 'checked' : '' }}> Tampilkan di halaman publik</label>
          <div class="full"><button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button></div>
        </form>
      </details>
    </div>
  @empty
    <div class="alert alert-info" style="border-color:#3b82f6;color:#60a5fa;background:rgba(59,130,246,.08);">
      Belum ada slide. Section galeri tidak akan tampil di halaman publik sampai ada minimal satu slide aktif.
    </div>
  @endforelse
</div>

@endsection