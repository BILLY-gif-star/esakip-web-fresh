@extends('layouts.app')

@section('title', 'Arsip Dokumen LKE AKIP')
@section('page-title', 'Arsip Dokumen')
@section('page-sub', 'Riwayat Penilaian LKE AKIP')

@section('content')

<style>
    /* ============================================ */
    /* GLASSMORPHISM STYLES - RESPONSIVE           */
    /* ============================================ */
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Container utama */
    .arsip-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Filter Glass */
    .filter-glass {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(12px);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        margin-bottom: 32px;
        overflow: hidden;
    }

    .filter-header {
        background: rgba(255, 255, 255, 0.03);
        padding: 16px 24px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .filter-header .card-title {
        font-size: 14px;
        font-weight: 700;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-body {
        padding: 20px 24px;
    }

    .filter-select-modern {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 14px;
        padding: 12px 16px;
        transition: all 0.3s ease;
        color: #fff;
        width: 100%;
        font-size: 14px;
    }

    .filter-select-modern:focus {
        outline: none;
        border-color: #6366f1;
    }

    .filter-select-modern option {
        background: #1e1e2e;
    }

    /* Tombol */
    .btn-primary-glass {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border: none;
        padding: 12px 28px;
        border-radius: 40px;
        color: white;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .btn-primary-glass:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
    }

    .btn-outline-glass {
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 10px 20px;
        border-radius: 40px;
        color: #fff;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-outline-glass:hover {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border-color: transparent;
        color: white;
        transform: translateY(-2px);
        text-decoration: none;
    }

    .btn-danger-glass {
        background: transparent;
        border: 1px solid rgba(239, 68, 68, 0.4);
        padding: 10px 20px;
        border-radius: 40px;
        color: #ef4444;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-danger-glass:hover {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border-color: transparent;
        color: white;
        transform: translateY(-2px);
    }

    /* Grid Card Arsip */
    .arsip-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(500px, 1fr));
        gap: 24px;
    }

    /* Card Glassmorphism */
    .arsip-card {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.7), rgba(15, 23, 42, 0.8));
        backdrop-filter: blur(12px);
        border-radius: 24px;
        padding: 24px;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .arsip-card:hover {
        transform: translateY(-6px);
        border-color: rgba(99, 102, 241, 0.4);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    /* Header Card */
    .card-header-custom {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .opd-name {
        font-size: 20px;
        font-weight: 800;
        color: #fff;
        line-height: 1.3;
    }

    .opd-jenis {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Badge Status */
    .badge-status {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 40px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-status.disctujui {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .badge-status.ditolak {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    /* Info Grid */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin: 20px 0;
    }

    .info-item {
        background: rgba(255, 255, 255, 0.03);
        border-radius: 14px;
        padding: 12px 14px;
    }

    .info-item .label {
        font-size: 10px;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .info-item .value {
        font-size: 14px;
        font-weight: 600;
        color: #fff;
        word-break: break-word;
    }

    /* File Preview */
    .file-preview {
        background: rgba(0, 0, 0, 0.4);
        border-radius: 14px;
        padding: 12px 16px;
        margin: 16px 0;
        font-size: 13px;
        font-family: monospace;
        color: #a5b4fc;
        display: flex;
        align-items: center;
        gap: 10px;
        word-break: break-all;
    }

    /* Tombol Aksi */
    .action-buttons {
        display: flex;
        gap: 12px;
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        flex-wrap: wrap;
    }

    .action-buttons .btn-outline-glass,
    .action-buttons .btn-danger-glass {
        flex: 1;
        justify-content: center;
        text-align: center;
    }

    /* Empty State */
    .empty-state-glass {
        text-align: center;
        padding: 80px 40px;
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.5), rgba(15, 23, 42, 0.5));
        border-radius: 32px;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .empty-state-glass .icon {
        font-size: 72px;
        margin-bottom: 24px;
        display: inline-block;
    }

    .empty-state-glass h3 {
        color: #fff;
        margin-bottom: 12px;
        font-size: 20px;
    }

    .empty-state-glass p {
        color: #94a3b8;
        font-size: 14px;
    }

    /* Form Label */
    .form-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #a1a1aa;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
    }

    /* Flex untuk filter */
    .d-flex {
        display: flex;
        gap: 16px;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    /* ============================================ */
    /* RESPONSIVE BREAKPOINTS                       */
    /* ============================================ */
    
    @media (max-width: 768px) {
        .arsip-container {
            padding: 0 16px;
        }
        
        .arsip-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .arsip-card {
            padding: 18px;
        }
        
        .opd-name {
            font-size: 18px;
        }
        
        .info-grid {
            grid-template-columns: 1fr;
            gap: 10px;
        }
        
        .filter-body {
            padding: 16px;
        }
        
        .filter-header {
            padding: 12px 16px;
        }
        
        .btn-primary-glass {
            padding: 10px 20px;
            font-size: 13px;
        }
        
        .btn-outline-glass,
        .btn-danger-glass {
            padding: 8px 16px;
            font-size: 12px;
        }
        
        .action-buttons {
            flex-direction: column;
        }
    }

    @media (min-width: 769px) and (max-width: 1024px) {
        .arsip-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
    }

    @media (min-width: 1025px) {
        .arsip-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1400px) {
        .arsip-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* Animasi */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-in {
        animation: fadeInUp 0.4s ease-out;
    }

    .arsip-card {
        animation: fadeInUp 0.4s ease-out;
        animation-fill-mode: both;
    }

    .arsip-card:nth-child(1) { animation-delay: 0.05s; }
    .arsip-card:nth-child(2) { animation-delay: 0.1s; }
    .arsip-card:nth-child(3) { animation-delay: 0.15s; }
    .arsip-card:nth-child(4) { animation-delay: 0.2s; }
    .arsip-card:nth-child(5) { animation-delay: 0.25s; }
    .arsip-card:nth-child(6) { animation-delay: 0.3s; }
</style>

<div class="arsip-container">
    {{-- FILTER SECTION --}}
    <div class="filter-glass animate-in">
        <div class="filter-header">
            <div class="card-title">
                <span>📂</span> Filter Arsip
            </div>
        </div>
        <div class="filter-body">
            <form method="GET" action="{{ route('admin.arsip') }}" class="d-flex">
                <div style="flex: 1; min-width: 150px;">
                    <label class="form-label">TAHUN</label>
                    <select name="tahun" class="filter-select-modern">
                        @foreach($listTahun as $t)
                            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-primary-glass">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Tampilkan
                </button>
            </form>
        </div>
    </div>

    {{-- DAFTAR ARSIP --}}
    @if(count($arsip) > 0)
    <div class="arsip-grid">
        @foreach($arsip as $item)
        <div class="arsip-card">
            {{-- Header --}}
            <div class="card-header-custom">
                <div>
                    <div class="opd-name">{{ $item->nama_opd ?? 'OPD' }}</div>
                    <div class="opd-jenis">
                        <span>📄</span> Jenis: {{ $item->jenis ?? 'dokumen_lke' }}
                    </div>
                </div>
                <div>
                    @php
                        $statusClass = $item->status == 'disetujui' ? 'disctujui' : 'ditolak';
                        $statusIcon = $item->status == 'disetujui' ? '✅' : '❌';
                        $statusText = ucfirst($item->status ?? 'Disetujui');
                    @endphp
                    <span class="badge-status {{ $statusClass }}">
                        {{ $statusIcon }} {{ $statusText }}
                    </span>
                </div>
            </div>
            
            {{-- Info Grid --}}
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">👤 Uploader</div>
                    <div class="value">{{ $item->nama_uploader ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="label">🔍 Reviewer</div>
                    <div class="value">{{ $item->nama_reviewer ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="label">📅 Waktu Upload</div>
                    <div class="value">
                        {{ isset($item->created_at) ? \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i') : '-' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="label">✅ Waktu Persetujuan</div>
                    <div class="value">
                        {{ isset($item->updated_at) ? \Carbon\Carbon::parse($item->updated_at)->format('d M Y H:i') : '-' }}
                    </div>
                </div>
            </div>
            
            {{-- Nama File --}}
            <div class="file-preview">
                <span>📄</span>
                <span>{{ $item->nama_file ?? 'Tidak ada file' }}</span>
            </div>
            
            {{-- Tombol Aksi --}}
            <div class="action-buttons">
                <a href="{{ route('admin.arsip.lihat', $item->id) }}" target="_blank" class="btn-outline-glass">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    Lihat Dokumen
                </a>
                <form method="POST" action="{{ route('admin.arsip.hapus', $item->id) }}" onsubmit="return confirm('Hapus arsip ini?')" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger-glass">
                        🗑️ Hapus
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state-glass">
        <div class="icon">📭</div>
        <h3>Belum Ada Arsip</h3>
        <p>Belum ada dokumen yang disetujui untuk tahun {{ $tahun }}</p>
    </div>
    @endif
</div>

@endsection