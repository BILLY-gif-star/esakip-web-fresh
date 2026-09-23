@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;1,400&family=Cinzel:wght@600;700&family=Inter:wght@400;500;600;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;1,400&family=Cinzel:wght@600;700&family=Inter:wght@400;500;600;700&display=swap');
:root{
    --card-bg: var(--bg-card);
    --border: var(--border-color);
    --accent: #6366f1;
    --text-main: var(--text-primary);
    --text-sub: var(--text-muted);
}

.card{
    background: var(--card-bg);
    border-radius: 22px;
    padding: 24px;
    border: 1px solid var(--border);
    position: relative;
    overflow: hidden;
    color: var(--text-main);
    box-shadow: var(--shadow-card);
}
.card::after{
    content:'';
    position:absolute;
    right:-40px;
    bottom:-40px;
    width:140px;
    height:140px;
    background: radial-gradient(circle, rgba(99,102,241,.25), transparent 70%);
}

.welcome-small{
    font-family:'Cormorant Garamond', serif;
    font-size:14px;
    font-style: italic;
    letter-spacing:2px;
    color:#818cf8;
}
[data-theme="light"] .welcome-small{
    color: #6366f1;
}

.welcome-name{
    font-family:'Cinzel', serif;
    font-size:38px;
    font-weight:700;
    margin:6px 0;
    letter-spacing:2px;
    background-image: linear-gradient(90deg,#ffffff,#a5b4fc,#818cf8,#c4b5fd);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 0 0 12px rgba(99,102,241,.25);
}

/* Mode Terang: gradient putih-terang nggak kebaca di atas
   background terang, ganti ke gradient ungu-indigo pekat */
[data-theme="light"] .welcome-name{
    background-image: linear-gradient(90deg,#4338ca,#6366f1,#7c3aed,#9333ea);
    text-shadow: none;
}
.welcome-sub{
    font-size:13px;
    color:#c4c4d0;
    display:flex;
    align-items:center;
    gap:10px;
}

.welcome-sub::before{
    content:'';
    width:28px;
    height:2px;
    background:#6366f1;
}

.klaster-badge {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 20px;
    border-radius: 16px;
    margin-top: 16px;
    width: 100%;
    transition: transform 0.2s ease;
}
.klaster-badge:hover {
    transform: translateY(-2px);
}
.klaster-icon {
    width: 46px;
    height: 46px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}
.klaster-info {
    flex: 1;
}
.klaster-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    margin-bottom: 3px;
}
.klaster-tugas {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.3;
}
.klaster-tugas-empty {
    color: rgba(255,255,255,.4);
    font-style: italic;
    font-size: 12px;
}
.klaster-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 20px;
}
.stat-card {
    background: var(--bg-card)
    border-radius: 20px;
    padding: 20px;
    text-align: center;
    border: 1px solid var(--border-color)
}
.stat-value {
    font-size: 32px;
    font-weight: 800;
    color: #f0b84a;
}
.stat-label {
    font-size: 12px;
    color: #94a3b8;
    margin-top: 8px;
}
.progress-bar-wrapper {
    background: rgba(255,255,255,0.1);
    border-radius: 30px;
    overflow: hidden;
    height: 30px;
    margin: 15px 0;
}
.progress-bar {
    background: linear-gradient(135deg, #10b981, #059669);
    height: 100%;
    border-radius: 30px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding-right: 12px;
    transition: width 0.5s ease;
}
.progress-text {
    color: var(--text-primary)
    font-size: 12px;
    font-weight: 700;
}
.text-success { color: #34d399; }
.text-warning { color: #fbbf24; }

.action-card{
    padding:18px;
    border-radius:18px;
    border:1px solid rgba(99,102,241,.3);
    background: rgba(99,102,241,.08);
    transition:.25s;
    text-decoration:none;
    color: var(--text-primary);
    display: block;
}
.action-card:hover{
    transform: translateY(-6px);
    background: rgba(99,102,241,.18);
    box-shadow: 0 10px 25px rgba(0,0,0,.4);
}
.action-title{
    font-weight:600;
}
.action-desc{
    font-size:12px;
    color:var(--text-sub);
}
.section-title{
    font-size:12px;
    color:var(--text-sub);
    margin-bottom:10px;
    letter-spacing:.5px;
}
</style>

@php
    $role = session('user.role', 'guest');
@endphp

{{-- ============================================================ --}}
{{-- DASHBOARD UNTUK EVALUATOR --}}
{{-- ============================================================ --}}
@if($role === 'evaluator')

<div class="card" style="margin-bottom:20px;">
    <div class="welcome-small">Selamat Datang, Verifikator</div>
    <div class="welcome-name">{{ strtoupper($user['nama']) }}</div>
    <div class="welcome-sub">Anda bertugas menilai LKE AKIP</div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">{{ $assigned_opd_count ?? 0 }}</div>
        <div class="stat-label">Total OPD Dinilai</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $sudah_dinilai_count ?? 0 }}</div>
        <div class="stat-label">Sudah Dinilai</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $belum_dinilai_count ?? 0 }}</div>
        <div class="stat-label">Belum Dinilai</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ number_format($rata_nilai ?? 0, 2) }}</div>
        <div class="stat-label">Rata-rata Nilai</div>
    </div>
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="section-title">🎯 PROGRESS PENILAIAN LKE AKIP</div>
    <div class="progress-bar-wrapper">
        <div class="progress-bar" style="width: {{ $progress ?? 0 }}%;">
            <span class="progress-text">{{ $progress ?? 0 }}%</span>
        </div>
    </div>
    <div style="text-align:center; font-size:12px; color:#94a3b8;">
        {{ $sudah_dinilai_count ?? 0 }} dari {{ $assigned_opd_count ?? 0 }} OPD selesai dinilai
    </div>
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="section-title">🎯 TARGET NILAI LKE (MINIMAL 80)</div>
    <div style="text-align:center;">
        <div style="font-size: 28px; font-weight: 800; {{ ($rata_nilai ?? 0) >= 80 ? 'color:#34d399' : 'color:#fbbf24' }}">
            {{ number_format($rata_nilai ?? 0, 2) }}
        </div>
        <div style="font-size: 12px; margin-top: 8px;">
            @php $selisih = ($rata_nilai ?? 0) - 80; @endphp
            @if($selisih >= 0)
                <span class="text-success">✅ Telah mencapai target (+{{ number_format($selisih, 2) }})</span>
            @else
                <span class="text-warning">⚠️ Masih kurang {{ number_format(abs($selisih), 2) }} poin dari target</span>
            @endif
        </div>
    </div>
</div>

@if(isset($belum_dinilai) && count($belum_dinilai) > 0)
<div class="card" style="margin-bottom:20px;">
    <div class="section-title">⚠️ PRIORITAS: OPD BELUM DINILAI</div>
    <div class="table-wrap">
        <table style="width:100%;">
            <thead>
                <tr><th>OPD</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @foreach($belum_dinilai as $opd)
                <tr>
                    <td><strong>{{ $opd->nama }}</strong></td>
                    <td>
                        <a href="{{ route('evaluasi.lke', ['opd_id' => $opd->id, 'tahun' => date('Y') - 1]) }}" 
                           class="btn btn-sm btn-primary" style="background:#2563eb; padding:4px 12px; border-radius:20px; color:white; text-decoration:none;">
                            📝 Nilai Sekarang
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="card">
    <div class="section-title">AKSES CEPAT</div>
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;">
        <a href="{{ route('evaluasi.lke') }}" class="action-card">
            <div style="font-size:20px;margin-bottom:6px;">📊</div>
            <div class="action-title">LKE AKIP</div>
            <div class="action-desc">Lakukan penilaian</div>
        </a>
        <a href="{{ route('admin.arsip') }}" class="action-card">
            <div style="font-size:20px;margin-bottom:6px;">🗂️</div>
            <div class="action-title">Arsip Dokumen</div>
            <div class="action-desc">Lihat dokumen terupload</div>
        </a>
    </div>
</div>

{{-- ============================================================ --}}
{{-- DASHBOARD UNTUK OPERATOR --}}
{{-- ============================================================ --}}
@elseif($role === 'operator')

<div class="card" style="margin-bottom:20px;">
    <div class="welcome-small">
        @if($user['role'] === 'admin')
            Selamat Datang, Administrator
        @elseif($user['role'] === 'Verivikator')
            Selamat Datang, Verivikator
        @else
            Selamat Datang
        @endif
    </div>
    <div class="welcome-name">{{ strtoupper($user['nama']) }}</div>
    
    {{-- ⭐ BADGE ROLE --}}
    <div class="welcome-sub">
        @if($user['role'] === 'admin')
            <span class="badge badge-danger">Administrator</span>
        @elseif($user['role'] === 'verifikator')
            <span class="badge badge-warning">verifikator</span>
        @else
            <span class="badge badge-info">Operator</span>
        @endif
    </div>

    @if(isset($opdUser) && $opdUser && $opdUser->klaster)
        @php
            $klasterConfig = [
                'utama' => ['label' => 'Klaster Utama', 'icon' => '🏆', 'bg' => 'rgba(99,102,241,.15)', 'border' => 'rgba(99,102,241,.4)', 'color' => '#a5b4fc', 'dot' => '#6366f1'],
                'pendukung' => ['label' => 'Klaster Pendukung', 'icon' => '🛡️', 'bg' => 'rgba(16,185,129,.12)', 'border' => 'rgba(16,185,129,.4)', 'color' => '#6ee7b7', 'dot' => '#10b981'],
                'tambahan' => ['label' => 'Klaster Tambahan', 'icon' => '➕', 'bg' => 'rgba(245,158,11,.12)', 'border' => 'rgba(245,158,11,.4)', 'color' => '#fcd34d', 'dot' => '#f59e0b'],
            ];
            $kc = $klasterConfig[$opdUser->klaster] ?? null;
        @endphp
        @if($kc)
        <div class="klaster-badge" style="background: {{ $kc['bg'] }}; border: 1px solid {{ $kc['border'] }};">
            <div class="klaster-icon" style="background: {{ $kc['bg'] }}; border: 1px solid {{ $kc['border'] }};">{{ $kc['icon'] }}</div>
            <div class="klaster-info">
                <div class="klaster-label" style="color: {{ $kc['color'] }};">{{ $kc['label'] }}</div>
                <div class="klaster-tugas">
                    @if($opdUser->tugas)
                        {{ $opdUser->tugas }}
                    @else
                        <span class="klaster-tugas-empty">Tugas belum ditentukan</span>
                    @endif
                </div>
            </div>
            <div class="klaster-dot" style="background: {{ $kc['dot'] }}; box-shadow: 0 0 8px {{ $kc['dot'] }};"></div>
        </div>
        @endif
    @else
        <div class="klaster-badge" style="background: rgba(255,255,255,.05); border: 1px dashed rgba(255,255,255,.15);">
            <div class="klaster-icon" style="background: rgba(255,255,255,.05); border: 1px dashed rgba(255,255,255,.15);">⚠️</div>
            <div class="klaster-info">
                <div class="klaster-label" style="color: var(--text-muted)">Klaster OPD</div>
                <div class="klaster-tugas-empty">Klaster OPD belum ditentukan. Silakan hubungi admin.</div>
            </div>
        </div>
    @endif
</div>

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-bottom:22px;">
    @for($i = 1; $i <= 3; $i++)
    @php 
        $juknis = isset($juknisList[$i-1]) ? $juknisList[$i-1] : null; 
    @endphp
    <div class="card" style="text-align:center; padding:20px; transition:transform 0.2s; cursor:pointer;" 
         onmouseover="this.style.transform='translateY(-5px)'" 
         onmouseout="this.style.transform='translateY(0)'"
         onclick="if({{ $juknis ? 'true' : 'false' }}) window.open('{{ $juknis ? route('juknis.preview', $juknis->id) : '#' }}', '_blank')">
        @if($juknis)
            @php 
                $fileExt = strtolower(pathinfo($juknis->file_name, PATHINFO_EXTENSION)); 
                $fileIcon = $fileExt === 'pdf' ? '📄' : ($fileExt === 'docx' ? '📝' : ($fileExt === 'xlsx' ? '📊' : '📁')); 
            @endphp
            <div style="font-size:34px;font-weight:800;line-height:1.2;">{{ $fileIcon }}</div>
            <div style="font-size:12px;color:var(--text-sub);margin-top:8px;">{{ Str::limit($juknis->judul, 30) }}</div>
            <div style="font-size:10px;color:#6b7280;margin-top:6px;">{{ \Carbon\Carbon::parse($juknis->created_at)->format('d M Y') }}</div>
            <div style="display:inline-block;background:rgba(99,102,241,.15);border-radius:20px;padding:2px 8px;font-size:9px;color:#a5b4fc;margin-top:6px;">👁️ Klik untuk Lihat</div>
        @else
            <div style="font-size:34px;font-weight:800;line-height:1.2;">📭</div>
            <div style="font-size:12px;color:var(--text-sub);margin-top:8px;">Juknis {{ $i }}</div>
            <div style="font-size:10px;color:#6b7280;margin-top:6px;">Belum tersedia</div>
            <div style="display:inline-block;background:rgba(99,102,241,.15);border-radius:20px;padding:2px 8px;font-size:9px;color:#a5b4fc;margin-top:6px;">Menunggu Upload</div>
        @endif
    </div>
    @endfor
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="section-title">AKSES CEPAT</div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
        <a href="{{ route('perjanjian.index','perjanjian-kinerja') }}" class="action-card">
            <div style="font-size:20px;margin-bottom:6px;">📄</div>
            <div class="action-title">Perjanjian Kinerja</div>
            <div class="action-desc">Upload & kelola dokumen</div>
        </a>
        <a href="{{ route('evaluasi.lke') }}" class="action-card">
            <div style="font-size:20px;margin-bottom:6px;">📊</div>
            <div class="action-title">LKE AKIP</div>
            <div class="action-desc">Isi evaluasi kinerja</div>
        </a>
        <a href="{{ route('pengukuran.periodik') }}" class="action-card">
            <div style="font-size:20px;margin-bottom:6px;">📈</div>
            <div class="action-title">Pengukuran</div>
            <div class="action-desc">Input capaian periodik</div>
        </a>
    </div>
</div>

<div class="card">
    <div class="section-title">INFO</div>
    <div style="font-size:13px;color:#d4d4d8;line-height:1.6;">
        Pastikan semua dokumen kinerja telah diunggah dan diperbarui sesuai periode berjalan.
        Gunakan menu di atas untuk mengelola data OPD Anda.
    </div>
</div>

@endif

@endsection