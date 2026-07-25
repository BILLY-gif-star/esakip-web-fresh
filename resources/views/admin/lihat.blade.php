@extends('layouts.app')
@section('title', 'Detail Arsip - ' . ($arsip->opd_nama ?? 'Arsip'))
@section('page-title', 'Detail Arsip Penilaian')
@section('page-sub', ($arsip->opd_nama ?? '') . ' - Tahun ' . ($arsip->tahun ?? ''))

@section('topbar-actions')
<a href="{{ route('admin.arsip') }}" class="btn-outline-glass">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
    </svg>
    Kembali ke Arsip
</a>
@endsection

@section('content')
<style>
    .detail-card {
        background: linear-gradient(135deg, rgba(30,41,59,0.7), rgba(15,23,42,0.8));
        backdrop-filter: blur(12px);
        border-radius: 24px;
        padding: 24px;
        margin-bottom: 24px;
        border: 1px solid rgba(255,255,255,0.08);
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .info-label {
        font-size: 12px;
        color: #94a3b8;
    }

    .info-value {
        font-size: 14px;
        font-weight: 600;
        color: #fff;
    }

    .json-preview {
        background: #0f172a;
        border-radius: 16px;
        padding: 16px;
        overflow-x: auto;
        font-family: monospace;
        font-size: 12px;
        color: #a5b4fc;
        max-height: 500px;
        overflow-y: auto;
    }

    .predikat-badge {
        display: inline-block;
        padding: 8px 20px;
        border-radius: 40px;
        font-weight: 700;
    }

    .btn-outline-glass {
        background: transparent;
        border: 2px solid #e2e8f0;
        padding: 8px 20px;
        border-radius: 40px;
        color: #475569;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-outline-glass:hover {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border-color: transparent;
        color: white;
        transform: translateY(-2px);
        text-decoration: none;
    }
</style>

@php
    $warnaPredikat = [
        'AA' => '#059669', 'A' => '#2563eb', 'BB' => '#7c3aed',
        'B' => '#d4982e', 'CC' => '#f59e0b', 'C' => '#dc2626',
        'D' => '#991b1b', 'E' => '#6b7280'
    ];
    $bgPredikat = $warnaPredikat[$arsip->predikat] ?? '#6b7280';
@endphp

<div class="detail-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px">
        <div>
            <div style="font-size:24px; font-weight:800; color:#fff">{{ $arsip->opd_nama }}</div>
            <div style="font-size:12px; color:#94a3b8">Tahun {{ $arsip->tahun }}</div>
        </div>
        <div class="predikat-badge" style="background:{{ $bgPredikat }}20; color:{{ $bgPredikat }}; border:1px solid {{ $bgPredikat }}40">
            {{ $arsip->predikat }}
        </div>
    </div>

    <div class="info-row">
        <span class="info-label">Nilai Akhir</span>
        <span class="info-value">{{ number_format($arsip->nilai_akhir, 2) }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Dinilai Oleh</span>
        <span class="info-value">{{ $arsip->penilai_nama }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Waktu Penilaian</span>
        <span class="info-value">{{ \Carbon\Carbon::parse($arsip->dinilai_pada)->format('d F Y H:i:s') }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Catatan Arsip</span>
        <span class="info-value">{{ $arsip->catatan_arsip ?? '-' }}</span>
    </div>
</div>

<div class="detail-card">
    <div style="font-size:16px; font-weight:700; margin-bottom:16px">📋 Data Penilaian Lengkap</div>
    <div class="json-preview">
        <pre style="margin:0; color:#a5b4fc">{{ json_encode($dataPenilaian, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
</div>
@endsection