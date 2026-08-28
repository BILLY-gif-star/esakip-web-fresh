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
</style>

<div class="alert alert-info" style="border-color:#3b82f6;color:#60a5fa;background:rgba(59,130,246,.08);">
  ℹ️ Format gambar: JPG, PNG, WEBP, atau SVG — maksimal 5MB. Perubahan langsung tampil di halaman
  <a href="{{ route('welcome') }}" target="_blank" style="color:#93c5fd;text-decoration:underline;">/welcome</a> setelah diunggah.
</div>

<div class="landing-grid">
  @foreach($slots as $key => $slot)
  <div class="landing-card">
    <div class="landing-card-preview">
      <img src="{{ $slot['current'] }}" alt="{{ $slot['label'] }}"
           onerror="this.style.opacity=0.2">
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

@endsection
