@extends('layouts.app')
@section('title', 'Rekap Hasil')
@section('page-title', 'Rekap Hasil')
@section('page-sub', 'Dokumen hasil penilaian dari Admin')

@section('topbar-actions')
<form method="GET" action="{{ route('rekap.hasil.opd') }}"
      style="display:flex;gap:8px;align-items:center;">
    <select name="tahun" onchange="this.form.submit()"
            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);
                   border-radius:10px;padding:8px 14px;color:#fff;font-size:12px;
                   font-weight:600;outline:none;cursor:pointer;">
        @foreach($listTahun as $t)
            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}
                    style="background:#1a1a24;">{{ $t }}</option>
        @endforeach
    </select>
</form>
@endsection

@section('content')
<style>
:root {
    --dark-card: #1a1a24;
    --dark-border: rgba(255,255,255,.08);
    --text-primary: #ffffff;
    --text-sub: #a1a1aa;
    --text-muted: #71717a;
}

.dok-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 18px;
    margin-top: 8px;
}

.dok-card {
    background: var(--dark-card);
    border-radius: 18px;
    border: 1px solid var(--dark-border);
    padding: 22px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    transition: border-color .2s, transform .2s;
    position: relative;
    overflow: hidden;
}

.dok-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #6366f1, #8b5cf6);
}

.dok-card:hover {
    border-color: rgba(99,102,241,.35);
    transform: translateY(-3px);
}

.dok-card.baru::before {
    background: linear-gradient(90deg, #10b981, #34d399);
}

.badge-baru {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(16,185,129,.15);
    border: 1px solid rgba(16,185,129,.3);
    border-radius: 999px;
    padding: 3px 10px;
    font-size: 10px;
    font-weight: 700;
    color: #34d399;
    position: absolute;
    top: 14px;
    right: 14px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: var(--dark-card);
    border-radius: 20px;
    border: 1px solid var(--dark-border);
}
</style>

{{-- Info box --}}
<div style="background:rgba(99,102,241,.08);border:1px solid rgba(99,102,241,.2);
            border-radius:16px;padding:14px 20px;margin-bottom:20px;
            display:flex;align-items:center;gap:12px;">
    <span style="font-size:20px;">📋</span>
    <div style="font-size:12px;color:#a1a1aa;">
        Berikut adalah dokumen hasil penilaian yang dikirimkan oleh
        <strong style="color:#fff;">Admin Biro Organisasi</strong>
        untuk OPD Anda — Tahun <strong style="color:#fff;">{{ $tahun }}</strong>.
    </div>
</div>

@if($dokumen->count() > 0)
<div class="dok-grid">
    @foreach($dokumen as $dok)
    @php $sudahDibaca = !is_null($dok->dibaca_at); @endphp
    <div class="dok-card {{ !$sudahDibaca ? 'baru' : '' }}">

        @if(!$sudahDibaca)
        <div class="badge-baru">
            <span style="width:6px;height:6px;border-radius:50%;background:#34d399;"></span>
            Baru
        </div>
        @endif

        {{-- Ikon & Judul --}}
        <div style="display:flex;align-items:flex-start;gap:14px;">
            <div style="width:46px;height:46px;border-radius:14px;flex-shrink:0;
                        background:rgba(99,102,241,.15);border:1px solid rgba(99,102,241,.3);
                        display:flex;align-items:center;justify-content:center;font-size:22px;">
                📄
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:14px;font-weight:700;color:#fff;
                            margin-bottom:4px;line-height:1.3;">
                    {{ $dok->judul }}
                </div>
                @if($dok->deskripsi)
                <div style="font-size:12px;color:#71717a;line-height:1.5;">
                    {{ $dok->deskripsi }}
                </div>
                @endif
            </div>
        </div>

        {{-- Meta info --}}
        <div style="display:flex;align-items:center;gap:16px;
                    padding:10px 0;border-top:1px solid var(--dark-border);
                    border-bottom:1px solid var(--dark-border);">
            <div style="font-size:11px;color:#52525b;">
                📅 {{ \Carbon\Carbon::parse($dok->created_at)->translatedFormat('d F Y') }}
            </div>
            <div style="font-size:11px;color:#52525b;">
                📁 PDF
            </div>
            @if($sudahDibaca)
            <div style="font-size:11px;color:#34d399;">
                ✓ Sudah diunduh
            </div>
            @endif
        </div>

        {{-- Tombol unduh --}}
        <a href="{{ route('dokumen.hasil.unduh', $dok->id) }}"
           style="display:flex;align-items:center;justify-content:center;gap:8px;
                  padding:10px;border-radius:12px;
                  background:linear-gradient(135deg,#6366f1,#4f46e5);
                  color:#fff;font-weight:600;font-size:13px;
                  text-decoration:none;transition:opacity .2s;"
           onmouseover="this.style.opacity='.85'"
           onmouseout="this.style.opacity='1'">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Unduh Dokumen
        </a>
    </div>
    @endforeach
</div>

@else
<div class="empty-state">
    <div style="font-size:48px;margin-bottom:16px;opacity:.4;">📭</div>
    <div style="font-size:15px;font-weight:600;color:#fff;margin-bottom:8px;">
        Belum Ada Dokumen
    </div>
    <div style="font-size:12px;color:#71717a;">
        Belum ada dokumen hasil yang dikirimkan Admin untuk tahun {{ $tahun }}
    </div>
</div>
@endif

@endsection