@extends('layouts.app')
@section('title', 'Daftar OPD - LKE AKIP')
@section('page-title', 'Evaluasi Kinerja')
@section('page-sub', 'Daftar Perangkat Daerah yang Telah Mengisi LKE AKIP')

@section('topbar-actions')
<div class="d-flex gap-3">
    <form method="GET" action="{{ route('evaluasi.lke') }}" class="d-flex gap-2 align-items-center">
        <div class="filter-year">
            <label class="text-muted" style="font-size:11px;font-weight:600;letter-spacing:1px;margin-right:8px">📅 TAHUN</label>
            <select name="tahun" class="year-select">
                @foreach($listTahun as $t)
                    <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-filter">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            Terapkan
        </button>
    </form>
</div>
@endsection

@section('content')
<style>
    /* ==================== CUSTOM STYLES ==================== */
    .filter-year {
        background: rgba(255,255,255,0.05);
        backdrop-filter: blur(8px);
        border-radius: 40px;
        padding: 4px 8px 4px 16px;
        border: 1px solid rgba(255,255,255,0.1);
    }
    
    .year-select {
        background: transparent;
        border: none;
        color: #fff;
        font-weight: 600;
        padding: 8px 12px;
        outline: none;
        cursor: pointer;
        font-size: 13px;
    }
    
    .year-select option {
        background: #1e293b;
        color: #fff;
    }
    
    .btn-filter {
        background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(139,92,246,0.2));
        border: 1px solid rgba(99,102,241,0.3);
        border-radius: 40px;
        padding: 8px 20px;
        color: #a5b4fc;
        font-weight: 600;
        font-size: 12px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    
    .btn-filter:hover {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border-color: transparent;
        color: white;
        transform: translateY(-2px);
    }
    
    /* ==================== CARD MODERN ==================== */
    .opd-card-modern {
        background: linear-gradient(135deg, rgba(30,41,59,0.7), rgba(15,23,42,0.8));
        backdrop-filter: blur(12px);
        border-radius: 28px;
        padding: 24px;
        transition: all 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        border: 1px solid rgba(255,255,255,0.08);
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }
    
    .opd-card-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #6366f1, #8b5cf6, #c084fc);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .opd-card-modern:hover {
        transform: translateY(-8px);
        border-color: rgba(99,102,241,0.4);
        box-shadow: 0 20px 40px rgba(0,0,0,0.3), 0 0 0 1px rgba(99,102,241,0.2);
    }
    
    .opd-card-modern:hover::before {
        opacity: 1;
    }
    
    /* Card Header */
    .card-header-modern {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    
    .card-icon {
        width: 52px;
        height: 52px;
        background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(139,92,246,0.2));
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        transition: all 0.3s ease;
    }
    
    .opd-card-modern:hover .card-icon {
        transform: scale(1.05);
        background: linear-gradient(135deg, rgba(99,102,241,0.3), rgba(139,92,246,0.3));
    }
    
    /* Status Badge */
    .status-modern {
        padding: 6px 14px;
        border-radius: 40px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
        backdrop-filter: blur(4px);
    }
    
    .status-complete-modern {
        background: linear-gradient(135deg, rgba(16,185,129,0.2), rgba(6,95,70,0.2));
        border: 1px solid rgba(16,185,129,0.4);
        color: #34d399;
    }
    
    .status-progress-modern {
        background: linear-gradient(135deg, rgba(245,158,11,0.2), rgba(180,83,9,0.2));
        border: 1px solid rgba(245,158,11,0.4);
        color: #fbbf24;
    }
    
    .status-empty-modern {
        background: linear-gradient(135deg, rgba(239,68,68,0.2), rgba(153,27,27,0.2));
        border: 1px solid rgba(239,68,68,0.4);
        color: #f87171;
    }
    
    /* Card Title */
    .card-title-modern {
        font-weight: 800;
        font-size: 18px;
        color: #fff;
        margin-bottom: 8px;
        letter-spacing: -0.3px;
        line-height: 1.3;
    }
    
    /* Stats Section */
    .stats-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 20px 0;
        padding: 12px 0;
        border-top: 1px solid rgba(255,255,255,0.05);
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    
    .stat-number {
        font-size: 28px;
        font-weight: 800;
        background: linear-gradient(135deg, #fff, #a5b4fc);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
    }
    
    .stat-label {
        font-size: 10px;
        color: #94a3b8;
        margin-top: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* Progress Circle */
    .progress-circle {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .progress-circle-inner {
        width: 58px;
        height: 58px;
        background: #0f172a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 16px;
        color: #fff;
    }
    
    /* Progress Bar */
    .progress-bar-modern {
        height: 6px;
        background: rgba(99,102,241,0.2);
        border-radius: 10px;
        overflow: hidden;
        margin: 16px 0;
    }
    
    .progress-fill-modern {
        height: 100%;
        background: linear-gradient(90deg, #6366f1, #8b5cf6, #c084fc);
        border-radius: 10px;
        transition: width 0.5s ease;
        position: relative;
        overflow: hidden;
    }
    
    .progress-fill-modern::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shimmer 1.5s infinite;
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    /* Card Footer */
    .card-footer-modern {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-top: 12px;
    }
    
    .action-link {
        font-size: 12px;
        font-weight: 600;
        color: #8b5cf6;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    
    .opd-card-modern:hover .action-link {
        gap: 10px;
        color: #a78bfa;
    }
    
    /* Empty State */
    .empty-state-modern {
        text-align: center;
        padding: 60px;
        background: linear-gradient(135deg, rgba(30,41,59,0.5), rgba(15,23,42,0.5));
        border-radius: 32px;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.05);
    }
    
    .empty-icon {
        font-size: 64px;
        margin-bottom: 20px;
        display: inline-block;
        filter: drop-shadow(0 0 20px rgba(99,102,241,0.3));
    }
    
    /* Grid */
    .opd-grid-modern {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 24px;
        margin-top: 28px;
    }
    
    .section-title-modern {
        font-size: 16px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin: 32px 0 20px 0;
        padding-left: 12px;
        border-left: 3px solid #8b5cf6;
        display: inline-block;
    }
    
    /* Stats Summary */
    .stats-summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-top: 40px;
        margin-bottom: 20px;
    }
    
    .stat-summary-card {
        background: linear-gradient(135deg, rgba(30,41,59,0.5), rgba(15,23,42,0.5));
        backdrop-filter: blur(8px);
        border-radius: 24px;
        padding: 24px;
        text-align: center;
        border: 1px solid rgba(255,255,255,0.05);
        transition: all 0.3s ease;
    }
    
    .stat-summary-card:hover {
        transform: translateY(-4px);
        border-color: rgba(99,102,241,0.3);
    }
    
    .stat-summary-value {
        font-size: 40px;
        font-weight: 800;
        margin-bottom: 8px;
    }
    
    .stat-summary-label {
        font-size: 12px;
        color: #94a3b8;
        letter-spacing: 0.5px;
    }
</style>

<div class="alert-glass info animate-in" style="background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(139,92,246,0.05)); border-left: 4px solid #6366f1;">
    <div style="display:flex;align-items:center;gap:12px">
        <span style="font-size:24px">✨</span>
        <span>Pilih Perangkat Daerah untuk melihat dan menilai LKE AKIP. Klik pada kartu OPD untuk membuka formulir penilaian.</span>
    </div>
</div>

{{-- OPD YANG SUDAH MENGISI --}}
@php
    $opdCount = count($opdList);
    $belumCount = count($belumMengisi);
    $totalOpd = $opdCount + $belumCount;
@endphp

<h4 class="section-title-modern">✅ OPD YANG TELAH MENGISI ({{ $opdCount }})</h4>
<div class="opd-grid-modern">
    @forelse($opdList as $opd)
        @php
            $persentase = ($opd->total_terisi / $totalSubKomponen) * 100;
            $persentase = min($persentase, 100);
            
            $statusClass = $persentase >= 80 ? 'status-complete-modern' : ($persentase > 0 ? 'status-progress-modern' : 'status-empty-modern');
            $statusText = $persentase >= 80 ? 'Lengkap' : ($persentase > 0 ? 'Sedang Diisi' : 'Belum Diisi');
            $statusIcon = $persentase >= 80 ? '✓' : ($persentase > 0 ? '⟳' : '○');
            
            $terisiCount = $opd->total_terisi;
            $totalCount = $totalSubKomponen;
            
            $circleDeg = $persentase * 3.6;
        @endphp
        <a href="{{ route('evaluasi.lke', ['opd_id' => $opd->id, 'tahun' => $tahun]) }}" style="text-decoration: none;">
            <div class="opd-card-modern">
                <div class="card-header-modern">
                    <div class="card-icon">🏛️</div>
                    <div class="status-modern {{ $statusClass }}">
                        {{ $statusIcon }} {{ $statusText }}
                    </div>
                </div>
                
                <div class="card-title-modern">{{ $opd->nama }}</div>
                
                <div class="stats-section">
                    <div>
                        <div class="stat-number">{{ $terisiCount }}</div>
                        <div class="stat-label">Sub Komponen Terisi</div>
                    </div>
                    <div>
                        <div class="stat-number">{{ $totalCount - $terisiCount }}</div>
                        <div class="stat-label">Belum Terisi</div>
                    </div>
                </div>
                
                <div class="progress-bar-modern">
                    <div class="progress-fill-modern" style="width: {{ $persentase }}%"></div>
                </div>
                
                <div class="card-footer-modern">
                    <div class="action-link">
                        <span>Klik untuk menilai</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </div>
        </a>
    @empty
        <div class="empty-state-modern">
            <div class="empty-icon">📭</div>
            <h3 style="color:#fff;margin-bottom:8px">Belum Ada OPD yang Mengisi</h3>
            <p style="color:#94a3b8">Belum ada OPD yang mengisi LKE untuk tahun {{ $tahun }}</p>
        </div>
    @endforelse
</div>

{{-- OPD YANG BELUM MENGISI --}}
@if($belumCount > 0)
<h4 class="section-title-modern">⏳ OPD YANG BELUM MENGISI ({{ $belumCount }})</h4>
<div class="opd-grid-modern">
    @foreach($belumMengisi as $opd)
        <div class="opd-card-modern" style="opacity:0.6;cursor:default">
            <div class="card-header-modern">
                <div class="card-icon" style="opacity:0.6">🏛️</div>
                <div class="status-modern status-empty-modern">○ Belum Mengisi</div>
            </div>
            
            <div class="card-title-modern">{{ $opd->nama }}</div>
            
            <div class="stats-section">
                <div>
                    <div class="stat-number" style="color:#64748b">0</div>
                    <div class="stat-label">Sub Komponen Terisi</div>
                </div>
                <div>
                    <div class="stat-number" style="color:#94a3b8">{{ $totalSubKomponen }}</div>
                    <div class="stat-label">Belum Terisi</div>
                </div>
            </div>
            
            <div class="progress-bar-modern">
                <div class="progress-fill-modern" style="width: 0%"></div>
            </div>
            
            <div class="card-footer-modern">
                <div class="action-link" style="color:#64748b">
                    <span>Menunggu pengisian operator</span>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif

{{-- Statistik Ringkasan --}}
<div class="stats-summary">
    <div class="stat-summary-card">
        <div class="stat-summary-value" style="color: #6366f1">{{ $opdCount }}</div>
        <div class="stat-summary-label">OPD Telah Mengisi</div>
    </div>
    <div class="stat-summary-card">
        <div class="stat-summary-value" style="color: #f59e0b">{{ $belumCount }}</div>
        <div class="stat-summary-label">OPD Belum Mengisi</div>
    </div>
    <div class="stat-summary-card">
        <div class="stat-summary-value" style="color: #10b981">{{ $totalOpd }}</div>
        <div class="stat-summary-label">Total OPD</div>
    </div>
</div>
@endsection