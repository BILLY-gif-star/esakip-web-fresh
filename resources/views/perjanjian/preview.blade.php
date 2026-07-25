@extends('layouts.app')

@section('title', 'Preview Dokumen - ' . ($dok->nama_file ?? 'Dokumen'))

@section('content')
<style>
    .preview-container {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #f8fafc;
        z-index: 1000;
        display: flex;
        flex-direction: column;
    }
    
    .preview-header {
        background: white;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        padding: 16px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }
    
    .preview-content {
        flex: 1;
        overflow: auto;
        background: #e2e8f0;
        padding: 20px;
    }
    
    .preview-iframe {
        width: 100%;
        height: 100%;
        border: none;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .error-container {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        min-height: 500px;
    }
    
    .error-card {
        background: white;
        border-radius: 24px;
        padding: 48px;
        text-align: center;
        max-width: 500px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    }
    
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 20px;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 40px;
        color: #475569;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    
    .btn-back:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        transform: translateX(-2px);
        text-decoration: none;
        color: #1e293b;
    }
    
    .btn-download {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 24px;
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        border-radius: 40px;
        color: white;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s ease;
        text-decoration: none;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
    }
    
    .btn-download:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        text-decoration: none;
        color: white;
    }
    
    .file-info {
        background: #f8fafc;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 16px;
        border: 1px solid #e2e8f0;
    }
</style>

<div class="preview-container">
    <!-- Header -->
    <div class="preview-header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ url()->previous() }}" class="btn-back">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Kembali
            </a>
            <div>
                <div class="fw-bold text-dark" style="font-size: 16px; line-height: 1.3;">
                    {{ $dok->nama_file }}
                </div>
                <div class="text-muted" style="font-size: 12px;">
                    {{ $dok->nama_opd ?? 'Dokumen Perangkat Daerah' }}
                </div>
            </div>
        </div>
        <a href="{{ route('perjanjian.stream', $dok->id) }}" download class="btn-download">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
            </svg>
            Download File
        </a>
    </div>
    
    <!-- Content -->
    <div class="preview-content">
        @if(!$fileExists)
            <div class="error-container">
                <div class="error-card">
                    <div style="font-size: 64px; margin-bottom: 20px;">📄</div>
                    <h3 class="text-danger mb-3">File Tidak Ditemukan</h3>
                    <p class="text-muted mb-4">
                        File <strong class="text-dark">{{ $dok->nama_file }}</strong> tidak ditemukan di server.<br>
                        File mungkin telah dihapus atau belum diupload dengan benar.
                    </p>
                    <div class="file-info mb-3">
                        <small class="text-muted">💡 Silakan upload ulang dokumen melalui sistem.</small>
                    </div>
                    <div class="mt-3">
                        <a href="{{ url()->previous() }}" class="btn-back">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 12H5M12 19l-7-7 7-7"/>
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        @elseif($ext === 'pdf')
            <iframe src="{{ route('perjanjian.stream', $dok->id) }}" class="preview-iframe"></iframe>
        @elseif(in_array($ext, ['doc', 'docx', 'xls', 'xlsx']))
            <div class="error-container">
                <div class="error-card">
                    <div style="font-size: 64px; margin-bottom: 20px;">
                        @if(in_array($ext, ['doc', 'docx'])) 📝
                        @else 📊
                        @endif
                    </div>
                    <h3 class="mb-3">Preview Tidak Tersedia</h3>
                    <p class="text-muted mb-4">
                        File <strong class="text-dark">{{ strtoupper($ext) }}</strong> tidak mendukung preview langsung di browser.<br>
                        Silakan download file untuk melihat isi dokumen.
                    </p>
                    <div class="file-info mb-3">
                        <small class="text-muted">💡 Tips: Untuk melihat file {{ strtoupper($ext) }} langsung di browser, upload dalam format PDF.</small>
                    </div>
                    <a href="{{ route('perjanjian.stream', $dok->id) }}" download class="btn-download">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                        </svg>
                        Download File
                    </a>
                </div>
            </div>
        @else
            <div class="error-container">
                <div class="error-card">
                    <div style="font-size: 64px; margin-bottom: 20px;">❓</div>
                    <h3 class="mb-3">Format Tidak Didukung</h3>
                    <p class="text-muted mb-4">
                        Format file <strong class="text-dark">{{ strtoupper($ext) }}</strong> tidak didukung untuk preview.<br>
                        Silakan download file untuk melihat isi dokumen.
                    </p>
                    <a href="{{ route('perjanjian.stream', $dok->id) }}" download class="btn-download">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                        </svg>
                        Download File
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection