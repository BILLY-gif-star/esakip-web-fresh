@extends('layouts.app')
@section('title', 'LKIP Perangkat Daerah')
@section('page-title', 'Pelaporan Kinerja')
@section('page-sub', 'LKIP Perangkat Daerah')

@section('topbar-actions')
  @if(session('user.role') === 'admin')
    <button class="btn-upload" onclick="document.getElementById('modalUpload').classList.add('open')">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 3v12m0 0-3-3m3 3 3-3M5 21h14"/>
      </svg>
      Upload Template LKIP
    </button>
  @endif
@endsection

@section('content')

<style>
  /* ==================== KONSISTEN DENGAN LAYOUT LAIN ==================== */
  :root {
    --primary: #4f46e5;
    --primary-light: #6366f1;
    --primary-soft: rgba(99,102,241,0.15);
    --success: #10b981;
    --success-light: rgba(16,185,129,0.15);
    --warning: #f59e0b;
    --warning-light: rgba(245,158,11,0.15);
    --danger: #ef4444;
    --danger-light: rgba(239,68,68,0.15);
    --info: #3b82f6;
    --info-light: rgba(59,130,246,0.15);
    --bg-dark: #1a1f2e;
    --bg-card: linear-gradient(135deg, #1a1f2e, #141824);
    --text-white: #ffffff;
    --text-gray: rgba(255,255,255,0.6);
    --text-light: rgba(255,255,255,0.4);
    --border-light: rgba(255,255,255,0.07);
    --shadow-sm: 0 1px 2px 0 rgba(0,0,0,0.05);
    --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
  }

  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @keyframes fadeInLeft {
    from { opacity: 0; transform: translateX(-20px); }
    to { opacity: 1; transform: translateX(0); }
  }

  .animate-in {
    animation: fadeInUp 0.4s ease forwards;
  }

  /* ── Alert ── */
  .alert-friendly {
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 24px;
    animation: fadeInLeft 0.4s ease;
  }

  .alert-friendly.info {
    background: var(--info-light);
    border-left: 4px solid var(--info);
    color: #60a5fa;
  }

  /* ── Cards ── */
  .card-template {
    background: var(--bg-card);
    border-radius: 20px;
    border: 1px solid var(--border-light);
    overflow: hidden;
    margin-bottom: 28px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
  }

  .card-template:hover {
    box-shadow: 0 8px 32px rgba(0,0,0,0.35);
    transform: translateY(-2px);
  }

  .card-template-header {
    background: rgba(255,255,255,0.03);
    padding: 18px 24px;
    border-bottom: 1px solid var(--border-light);
  }

  .card-template-header div {
    color: var(--text-white);
  }

  .card-template-body {
    padding: 20px 24px;
    color: var(--text-white);
  }

  /* ── Stats Cards ── */
  .stats-grid-friendly {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 28px;
  }

  .stat-card-friendly {
    background: var(--bg-card);
    border-radius: 20px;
    padding: 20px;
    border: 1px solid var(--border-light);
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
  }

  .stat-card-friendly:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.35);
  }

  .stat-icon-friendly {
    font-size: 32px;
    margin-bottom: 12px;
  }

  .stat-value-friendly {
    font-size: 32px;
    font-weight: 800;
    color: var(--primary-light);
    line-height: 1.2;
  }

  .stat-label-friendly {
    font-size: 12px;
    color: var(--text-gray);
    margin-top: 4px;
  }

  /* ── Table ── */
  .table-friendly {
    background: var(--bg-card);
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid var(--border-light);
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
  }

  .table-friendly table {
    width: 100%;
    border-collapse: collapse;
  }

  .table-friendly th {
    background: rgba(255,255,255,0.04);
    color: rgba(255,255,255,0.6);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 14px 16px;
    border-bottom: 1px solid var(--border-light);
  }

  .table-friendly td {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    color: rgba(255,255,255,0.85);
  }

  .table-friendly tbody tr:hover td {
    background: rgba(255,255,255,0.03);
  }

  /* ── Badges ── */
  .badge-status {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 600;
  }

  .badge-approved {
    background: rgba(16,185,129,0.18);
    color: #34d399;
    border: 1px solid rgba(16,185,129,0.28);
  }

  .badge-rejected {
    background: rgba(239,68,68,0.18);
    color: #f87171;
    border: 1px solid rgba(239,68,68,0.28);
  }

  .badge-pending {
    background: rgba(245,158,11,0.18);
    color: #fbbf24;
    border: 1px solid rgba(245,158,11,0.28);
  }

  /* ── Buttons ── */
  .btn-upload {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border: none;
    border-radius: 40px;
    padding: 8px 20px;
    color: white;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
    cursor: pointer;
  }

  .btn-upload:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
  }

  .btn-primary-friendly {
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    border: none;
    border-radius: 40px;
    padding: 6px 14px;
    color: white;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    cursor: pointer;
  }

  .btn-primary-friendly:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(79,70,229,0.3);
    text-decoration: none;
    color: white;
  }

  .btn-outline-friendly {
    background: transparent;
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 40px;
    padding: 6px 14px;
    color: rgba(255,255,255,0.7);
    font-size: 12px;
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    cursor: pointer;
  }

  .btn-outline-friendly:hover {
    background: rgba(255,255,255,0.08);
    border-color: rgba(255,255,255,0.3);
    color: white;
    text-decoration: none;
  }

  .btn-danger-friendly {
    background: transparent;
    border: 1px solid rgba(239,68,68,0.3);
    border-radius: 40px;
    padding: 6px 14px;
    color: #f87171;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    cursor: pointer;
  }

  .btn-danger-friendly:hover {
    background: rgba(239,68,68,0.15);
    border-color: rgba(239,68,68,0.5);
    text-decoration: none;
  }

  .btn-success-friendly {
    background: linear-gradient(135deg, var(--success), #059669);
    border: none;
    border-radius: 40px;
    padding: 6px 14px;
    color: white;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    cursor: pointer;
  }

  .btn-success-friendly:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16,185,129,0.3);
    text-decoration: none;
    color: white;
  }

  /* ── Modal ── */
  .modal-friendly {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(8px);
    z-index: 1000;
    align-items: center;
    justify-content: center;
  }

  .modal-friendly.open {
    display: flex;
  }

  .modal-content-friendly {
    background: var(--bg-card);
    border-radius: 24px;
    width: 500px;
    max-width: 90%;
    box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    border: 1px solid var(--border-light);
    animation: fadeInUp 0.3s ease;
  }

  .modal-header-friendly {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-light);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .modal-title-friendly {
    font-size: 18px;
    font-weight: 700;
    color: var(--text-white);
  }

  .modal-close-friendly {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.6);
    cursor: pointer;
    transition: all 0.2s;
    font-size: 16px;
  }

  .modal-close-friendly:hover {
    background: rgba(239,68,68,0.2);
    color: #f87171;
  }

  .modal-body-friendly {
    padding: 24px;
  }

  .modal-footer-friendly {
    padding: 16px 24px;
    border-top: 1px solid var(--border-light);
    display: flex;
    justify-content: flex-end;
    gap: 12px;
  }

  /* ── Empty State ── */
  .empty-state-friendly {
    text-align: center;
    padding: 48px;
    background: rgba(255,255,255,0.03);
    border-radius: 20px;
  }

  .empty-state-friendly .icon {
    font-size: 64px;
    margin-bottom: 16px;
    display: block;
  }

  .empty-state-friendly h3 {
    color: rgba(255,255,255,0.85);
    margin-bottom: 8px;
  }

  .empty-state-friendly p {
    color: var(--text-gray);
  }

  /* ── Upload Area ── */
  .upload-area-friendly {
    background: rgba(255,255,255,0.05);
    border: 2px dashed rgba(255,255,255,0.15);
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s ease;
  }

  .upload-area-friendly:hover {
    border-color: var(--primary);
    background: rgba(99,102,241,0.08);
  }

  .form-control-friendly {
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 12px;
    padding: 10px 12px;
    color: var(--text-white);
    width: 100%;
    transition: all 0.2s ease;
  }

  .form-control-friendly:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
  }

  .form-control-friendly::placeholder {
    color: rgba(255,255,255,0.3);
  }
</style>
{{-- INFO --}}
<div class="alert-friendly info animate-in">
  <div style="display:flex;align-items:center;gap:12px">
    <span style="font-size:20px">ℹ️</span>
    <span>{{ session('user.role')==='admin'
      ? 'Upload template LKIP agar dapat diunduh Operator. Pantau dan review dokumen LKIP yang diupload.'
      : 'Download template dari Admin, isi LKIP, lalu upload kembali untuk direview.' }}</span>
  </div>
</div>

{{-- TEMPLATE LKIP DARI ADMIN --}}
<div class="card-template animate-in">
  <div class="card-template-header">
    <div style="display:flex;justify-content:space-between;align-items:center">
      <div>
        <div style="font-size:16px;font-weight:700;color:var(--text-dark)">📄 Template LKIP Perangkat Daerah</div>
        <div style="font-size:12px;color:var(--text-gray);margin-top:4px">Tahun {{ $tahun }}</div>
      </div>
      @if($template)
        <span class="badge-status badge-approved">✓ Template Tersedia</span>
      @else
        <span class="badge-status badge-rejected">✗ Belum ada template</span>
      @endif
    </div>
  </div>
  <div class="card-template-body">
    @if($template)
      @php
        $templatePath = storage_path('app/public/' . $template->file_path);
        $templateExists = file_exists($templatePath);
        $fileExt = strtolower(pathinfo($template->file_name, PATHINFO_EXTENSION));
      @endphp
      
      @if(!$templateExists)
        <div class="alert-friendly" style="background:var(--danger-light);border-left:4px solid var(--danger);margin-bottom:16px;color:#991b1b">
          ⚠️ File template <strong>{{ $template->file_name }}</strong> tidak ditemukan di server. Silakan upload ulang template.
        </div>
      @endif
      
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px">
        <div style="display:flex;align-items:center;gap:12px">
          <div style="width:48px;height:48px;background:var(--primary-soft);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:24px">
            @if($fileExt == 'pdf') 📄
            @elseif($fileExt == 'docx') 📝
            @elseif($fileExt == 'xlsx') 📊
            @else 📁
            @endif
          </div>
          <div>
            <div style="font-weight:600;color:var(--text-dark)">{{ $template->file_name }}</div>
            <div style="font-size:11px;color:var(--text-gray)">Diupload: {{ \Carbon\Carbon::parse($template->updated_at)->format('d M Y H:i') }}</div>
          </div>
        </div>
        <div style="display:flex;gap:8px">
          @if($templateExists && $template->id)
            @if($fileExt == 'pdf')
              <a href="{{ route('lkip.preview', $template->id) }}" target="_blank" class="btn-primary-friendly">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
                Lihat Template
              </a>
            @else
              <a href="{{ route('lkip.download', $template->id) }}" class="btn-primary-friendly" download>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                </svg>
                Download Template
              </a>
            @endif
          @endif
          
          @if(session('user.role')==='admin')
            <button class="btn-outline-friendly" onclick="document.getElementById('modalUpload').classList.add('open')">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 3v12m0 0-3-3m3 3 3-3M5 21h14"/>
              </svg>
              Ganti
            </button>
          @endif
          
          @if(session('user.role')==='admin' && $templateExists && $template->id)
            <form method="POST" action="{{ route('lkip.hapus', $template->id) }}" onsubmit="return confirm('Hapus template {{ addslashes($template->file_name) }} ?')" style="display:inline">
              @csrf
              <button type="submit" class="btn-danger-friendly">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="3 6 5 6 21 6"/>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                </svg>
                Hapus
              </button>
            </form>
          @endif
        </div>
      </div>
    @else
      <div class="empty-state-friendly" style="padding:32px">
        <div class="icon">📋</div>
        <h3 style="color:var(--text-dark);margin-bottom:8px">Template LKIP belum tersedia</h3>
        <p style="color:var(--text-gray);margin-bottom:20px">
          @if(session('user.role')==='admin')
            Klik Upload Template LKIP untuk menambahkan.
          @else
            Template LKIP belum diupload oleh Admin. Silakan hubungi Admin.
          @endif
        </p>
        @if(session('user.role')==='admin')
        <button class="btn-upload" onclick="document.getElementById('modalUpload').classList.add('open')">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 3v12m0 0-3-3m3 3 3-3M5 21h14"/>
          </svg>
          Upload Template LKIP
        </button>
        @endif
      </div>
    @endif
  </div>
</div>

{{-- UPLOAD DOKUMEN LKIP (OPERATOR) --}}
@if(session('user.role') === 'operator')
<div class="card-template animate-in">
  <div class="card-template-header">
    <div style="display:flex;justify-content:space-between;align-items:center">
      <div>
        <div style="font-size:16px;font-weight:700;color:var(--text-dark)">📤 Upload LKIP</div>
        <div style="font-size:12px;color:var(--text-gray);margin-top:4px">{{ session('user.nama_daerah') }} — Tahun {{ $tahun }}</div>
      </div>
      @if($dokOpd)
        @if($dokOpd->status === 'disetujui')
          <span class="badge-status badge-approved">✓ Disetujui</span>
        @elseif($dokOpd->status === 'ditolak')
          <span class="badge-status badge-rejected">✗ Perlu Revisi</span>
        @else
          <span class="badge-status badge-pending">⏳ Menunggu Review</span>
        @endif
      @endif
    </div>
  </div>
  <div class="card-template-body">
    @php
      $fileExists = false;
      if ($dokOpd && $dokOpd->file_name) {
          $filePath = storage_path('app/public/' . $dokOpd->file_path);
          $fileExists = file_exists($filePath);
      }
    @endphp
    
    @if($dokOpd && $dokOpd->status === 'ditolak' && $dokOpd->catatan_review)
      <div class="alert-friendly" style="background:var(--danger-light);border-left:4px solid var(--danger);margin-bottom:16px;color:#991b1b">
        <strong>❌ LKIP perlu direvisi.</strong><br>
        <span style="font-size:12px">Catatan Admin: <em>{{ $dokOpd->catatan_review }}</em></span>
      </div>
    @elseif($dokOpd && $dokOpd->status === 'disetujui')
      <div class="alert-friendly" style="background:var(--success-light);border-left:4px solid var(--success);margin-bottom:16px;color:#065f46">
        ✅ <strong>LKIP telah disetujui!</strong> Tidak perlu mengupload ulang.
      </div>
    @endif

    @if(!$dokOpd || $dokOpd->status !== 'disetujui')
      @if($dokOpd)
        <div class="alert-friendly" style="background:var(--warning-light);border-left:4px solid var(--warning);margin-bottom:16px;color:#92400e">
          📄 File terakhir: <strong>{{ $dokOpd->file_name }}</strong><br>
          <span style="font-size:11px">Upload: {{ \Carbon\Carbon::parse($dokOpd->updated_at)->format('d M Y H:i') }}</span>
          @if(!$fileExists)
            <br><span style="color:#dc2626">⚠️ File tidak ditemukan di server. Silakan upload ulang.</span>
          @endif
        </div>
      @endif
      
      <div class="upload-area-friendly">
        <form method="POST" action="{{ route('lkip.upload') }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="tahun" value="{{ $tahun }}">
          <input type="hidden" name="opd_id" value="{{ $opdId }}">
          
          <div style="margin-bottom:16px">
            <label style="font-size:12px;font-weight:600;color:var(--text-gray);display:block;margin-bottom:8px">📝 Judul LKIP</label>
            <input type="text" name="judul_dokumen" class="form-control-friendly" 
                   value="{{ $dokOpd->judul_dokumen ?? '' }}" 
                   placeholder="Contoh: LKIP Tahun 2024" required>
          </div>
          
          <div style="margin-bottom:16px">
            <label style="font-size:12px;font-weight:600;color:var(--text-gray);display:block;margin-bottom:8px">📁 Pilih File LKIP</label>
            <input type="file" name="file" class="form-control-friendly" accept=".pdf,.docx,.xlsx,.doc,.xls" {{ $dokOpd ? '' : 'required' }}>
            <small style="font-size:10px;color:var(--text-light);display:block;margin-top:4px">Maksimal 5MB. Format: PDF, DOC, DOCX, XLS, XLSX</small>
          </div>
          
          <div style="margin-bottom:20px">
            <label style="font-size:12px;font-weight:600;color:var(--text-gray);display:block;margin-bottom:8px">📝 Deskripsi (opsional)</label>
            <textarea name="deskripsi" rows="2" class="form-control-friendly" 
                      placeholder="Deskripsi singkat tentang LKIP...">{{ $dokOpd->deskripsi ?? '' }}</textarea>
          </div>
          
          <button type="submit" class="btn-upload" style="width:100%;justify-content:center;padding:12px">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 3v12m0 0-3-3m3 3 3-3M5 21h14"/>
            </svg>
            {{ $dokOpd ? 'Upload Ulang / Revisi LKIP' : 'Upload LKIP' }}
          </button>
        </form>
      </div>
    @endif
  </div>
</div>
@endif

{{-- TABEL OPD (ADMIN) --}}
@if(session('user.role') === 'admin')
  @if($listOpd->count() > 0)
  @php
    $menunggu = $listOpd->where('status','dikirim')->count();
    $disetujui = $listOpd->where('status','disetujui')->count();
    $ditolak = $listOpd->where('status','ditolak')->count();
  @endphp
  
  <div class="stats-grid-friendly animate-in">
    <div class="stat-card-friendly">
      <div class="stat-icon-friendly">⏳</div>
      <div class="stat-value-friendly">{{ $menunggu }}</div>
      <div class="stat-label-friendly">Menunggu Review</div>
    </div>
    <div class="stat-card-friendly">
      <div class="stat-icon-friendly">✅</div>
      <div class="stat-value-friendly">{{ $disetujui }}</div>
      <div class="stat-label-friendly">Disetujui</div>
    </div>
    <div class="stat-card-friendly">
      <div class="stat-icon-friendly">❌</div>
      <div class="stat-value-friendly">{{ $ditolak }}</div>
      <div class="stat-label-friendly">Perlu Revisi</div>
    </div>
    <div class="stat-card-friendly">
      <div class="stat-icon-friendly">📂</div>
      <div class="stat-value-friendly">{{ $listOpd->count() }}</div>
      <div class="stat-label-friendly">Total OPD Upload</div>
    </div>
  </div>
  @endif

<div class="table-friendly animate-in">
  <div class="table-wrap">
    <table style="width:100%; border-collapse: collapse;">
      <thead>
        <tr>
          <th style="padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 700; color: #1f2937;">OPD / Operator</th>
          <th style="padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 700; color: #1f2937;">Judul / Nama File</th>
          <th style="padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 700; color: #1f2937;">Deskripsi</th>
          <th style="padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 700; color: #1f2937;">Tgl Upload</th>
          <th style="padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 700; color: #1f2937;">Status</th>
          <th style="padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 700; color: #1f2937;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($listOpd as $d)
        @php
          $filePath = storage_path('app/public/' . $d->file_path);
          $fileExists = file_exists($filePath);
          $fileExt = strtolower(pathinfo($d->file_name ?? '', PATHINFO_EXTENSION));
          $fileIcon = $fileExt === 'pdf' ? '📄' : ($fileExt === 'docx' ? '📝' : ($fileExt === 'xlsx' ? '📊' : '📁'));
        @endphp
        <tr style="border-bottom: 1px solid #f1f5f9;">
          <td style="padding: 14px 16px;">
            <div style="font-weight: 600; color: #1f2937;">{{ $d->nama_opd ?? '—' }}</div>
            <div style="font-size: 11px; color: #6b7280;">{{ $d->nama_user ?? '—' }}</div>
          </td>
          <td style="padding: 14px 16px;">
            <div style="display: flex; align-items: center; gap: 8px;">
              <span style="font-size: 18px;">{{ $fileIcon }}</span>
              <div>
                <div style="font-size: 13px; font-weight: 500; color: #1f2937;">{{ $d->judul_dokumen ?? $d->file_name }}</div>
                <div style="font-size: 10px; color: #9ca3af;">{{ $d->file_name ?? '-' }}</div>
              </div>
            </div>
            @if(!$fileExists && $d->file_name)
              <div style="margin-top: 4px;">
                <span style="display: inline-block; background: #fee2e2; color: #dc2626; font-size: 10px; padding: 2px 8px; border-radius: 12px;">⚠️ File tidak ditemukan</span>
              </div>
            @endif
          </td>
          <td style="padding: 14px 16px; font-size: 12px; color: #4b5563;">
            {{ $d->deskripsi ?: '—' }}
          </td>
          <td style="padding: 14px 16px; font-size: 12px; color: #4b5563;">
            {{ $d->updated_at ? \Carbon\Carbon::parse($d->updated_at)->format('d/m/Y H:i') : '-' }}
          </td>
          <td style="padding: 14px 16px;">
            @if($d->status === 'disetujui')
              <span class="badge-status badge-approved">✓ Disetujui</span>
            @elseif($d->status === 'ditolak')
              <span class="badge-status badge-rejected">✗ Perlu Revisi</span>
            @elseif($d->status === 'dikirim')
              <span class="badge-status badge-pending">⏳ Menunggu Review</span>
            @else
              <span class="badge-status badge-pending">📝 Draft</span>
            @endif
          </td>
          <td style="padding: 14px 16px;">
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
              @if($fileExists && $d->dokumen_id)
                @if(in_array($fileExt, ['doc', 'docx']))
                  <a href="{{ route('lkip.download', $d->dokumen_id) }}" class="btn-primary-friendly" download>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                    </svg>
                    Download
                  </a>
                @else
                  <a href="{{ route('lkip.preview', $d->dokumen_id) }}" target="_blank" class="btn-primary-friendly">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                    Preview
                  </a>
                @endif
              @elseif($d->file_name)
                <span class="btn-outline-friendly" style="opacity:0.5;cursor:not-allowed">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                  Preview
                </span>
              @endif
              
              @if($d->dokumen_id)
              <button onclick="openEditModal({{ $d->dokumen_id }}, '{{ addslashes($d->nama_opd) }}', '{{ addslashes($d->judul_dokumen ?? '') }}', '{{ addslashes($d->deskripsi ?? '') }}', '{{ $d->status }}', '{{ addslashes($d->catatan_review ?? '') }}')" 
                      class="btn-outline-friendly">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"/>
                  <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"/>
                </svg>
                Edit
              </button>
              @endif
              
              @if($d->status !== 'disetujui' && $d->dokumen_id)
                <form method="POST" action="{{ route('lkip.review', $d->dokumen_id) }}" style="display:inline" onsubmit="return confirm('Setujui LKIP {{ addslashes($d->nama_opd) }} ?')">
                  @csrf
                  <input type="hidden" name="status" value="disetujui">
                  <input type="hidden" name="catatan_review" value="">
                  <button type="submit" class="btn-success-friendly">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Setujui
                  </button>
                </form>
              @endif
              
              @if($d->status !== 'ditolak' && $d->dokumen_id)
                <form method="POST" action="{{ route('lkip.review', $d->dokumen_id) }}" style="display:inline" onsubmit="return confirm('Tolak LKIP {{ addslashes($d->nama_opd) }} ?')">
                  @csrf
                  <input type="hidden" name="status" value="ditolak">
                  <input type="hidden" name="catatan_review" value="">
                  <button type="submit" class="btn-danger-friendly">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <line x1="18" y1="6" x2="6" y2="18"/>
                      <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                    Tolak
                  </button>
                </form>
              @endif
              
              @if($d->dokumen_id)
              <form method="POST" action="{{ route('lkip.hapus', $d->dokumen_id) }}" onsubmit="return confirm('Hapus LKIP {{ addslashes($d->judul_dokumen ?? $d->file_name) }} ?')" style="display:inline">
                @csrf
                <button type="submit" class="btn-danger-friendly">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                  </svg>
                  Hapus
                </button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
          <tr>
            <td colspan="6" style="padding: 60px; text-align: center;">
              <div style="font-size: 48px; margin-bottom: 16px;">📂</div>
              <h3 style="color: #1f2937; margin-bottom: 8px;">Belum ada LKIP dari OPD</h3>
              <p style="color: #6b7280;">Operator belum mengupload LKIP</p>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endif

{{-- MODAL UPLOAD TEMPLATE LKIP --}}
@if(session('user.role') === 'admin')
<div class="modal-friendly" id="modalUpload">
  <div class="modal-content-friendly">
    <div class="modal-header-friendly">
      <div class="modal-title-friendly">📤 Upload Template LKIP</div>
      <button class="modal-close-friendly" onclick="document.getElementById('modalUpload').classList.remove('open')">✕</button>
    </div>
    <form method="POST" action="{{ route('lkip.upload') }}" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="tahun" value="{{ $tahun }}">
      <input type="hidden" name="opd_id" value="{{ $opdId }}">
      <input type="hidden" name="is_template" value="1">
      <div class="modal-body-friendly">
        <div style="margin-bottom:16px">
          <label style="font-size:12px;font-weight:600;color:var(--text-gray);display:block;margin-bottom:8px">📝 Judul Template</label>
          <input type="text" name="judul_dokumen" class="form-control-friendly" 
                 value="Template LKIP Tahun {{ $tahun }}" required>
        </div>
        <div class="upload-area-friendly">
          <div style="margin-bottom:12px">📁 Format: PDF, Word (.docx), Excel (.xlsx) — Maks 5MB</div>
          <input type="file" name="file" class="form-control-friendly" accept=".pdf,.docx,.xlsx,.doc,.xls" required>
        </div>
      </div>
      <div class="modal-footer-friendly">
        <button type="button" class="btn-outline-friendly" onclick="document.getElementById('modalUpload').classList.remove('open')">Batal</button>
        <button type="submit" class="btn-upload">📂 Upload Template</button>
      </div>
    </form>
  </div>
</div>
@endif

{{-- MODAL EDIT DOKUMEN --}}
<div class="modal-friendly" id="modalEdit">
  <div class="modal-content-friendly">
    <div class="modal-header-friendly">
      <div class="modal-title-friendly">✏️ Edit LKIP</div>
      <button class="modal-close-friendly" onclick="closeEditModal()">✕</button>
    </div>
    <form method="POST" id="formEdit" enctype="multipart/form-data">
      @csrf
      @method('POST')
      <div class="modal-body-friendly">
        <div id="editInfo" style="padding:12px;background:var(--primary-soft);border-radius:12px;margin-bottom:16px;font-size:13px;color:var(--primary)"></div>
        
        <div style="margin-bottom:16px">
          <label style="font-size:12px;font-weight:600;color:var(--text-gray);display:block;margin-bottom:8px">📝 Judul LKIP</label>
          <input type="text" name="judul_dokumen" id="editJudul" class="form-control-friendly" placeholder="Judul LKIP...">
        </div>
        
        <div style="margin-bottom:16px">
          <label style="font-size:12px;font-weight:600;color:var(--text-gray);display:block;margin-bottom:8px">📝 Deskripsi</label>
          <textarea name="deskripsi" id="editDeskripsi" rows="2" class="form-control-friendly" placeholder="Deskripsi..."></textarea>
        </div>
        
        <div style="margin-bottom:16px">
          <label style="font-size:12px;font-weight:600;color:var(--text-gray);display:block;margin-bottom:8px">📊 Status</label>
          <select name="status" id="editStatus" class="form-control-friendly">
            <option value="draft">📝 Draft</option>
            <option value="dikirim">📤 Dikirim</option>
            <option value="disetujui">✅ Disetujui</option>
            <option value="ditolak">❌ Ditolak</option>
          </select>
        </div>
        
        <div style="margin-bottom:16px">
          <label style="font-size:12px;font-weight:600;color:var(--text-gray);display:block;margin-bottom:8px">📝 Catatan Admin</label>
          <textarea name="catatan_review" id="editCatatan" rows="2" class="form-control-friendly" placeholder="Catatan untuk operator..."></textarea>
        </div>
        
        <div>
          <label style="font-size:12px;font-weight:600;color:var(--text-gray);display:block;margin-bottom:8px">📁 Ganti File (opsional)</label>
          <input type="file" name="file" class="form-control-friendly" accept=".pdf,.docx,.xlsx,.doc,.xls">
          <small style="font-size:10px;color:var(--text-light)">Kosongkan jika tidak ingin mengganti file</small>
        </div>
      </div>
      <div class="modal-footer-friendly">
        <button type="button" class="btn-outline-friendly" onclick="closeEditModal()">Batal</button>
        <button type="submit" class="btn-upload">💾 Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditModal(id, namaOpd, judul, deskripsi, status, catatan) {
  var modal = document.getElementById('modalEdit');
  var info = document.getElementById('editInfo');
  var form = document.getElementById('formEdit');
  
  form.action = '/lkip/review/' + id;
  info.innerHTML = 'OPD: <strong>' + namaOpd + '</strong>';
  document.getElementById('editJudul').value = judul || '';
  document.getElementById('editDeskripsi').value = deskripsi || '';
  document.getElementById('editStatus').value = status || 'draft';
  document.getElementById('editCatatan').value = catatan || '';
  modal.classList.add('open');
}

function closeEditModal() {
  document.getElementById('modalEdit').classList.remove('open');
}

document.querySelectorAll('.modal-friendly').forEach(function(modal) {
  modal.addEventListener('click', function(e) {
    if (e.target === this) {
      this.classList.remove('open');
    }
  });
});
</script>
@endsection