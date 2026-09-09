@extends('layouts.app')
@section('title', 'Cascading / Pohon Kinerja')
@section('page-title', 'Perencanaan Kinerja')
@section('page-sub', 'Cascading / Pohon Kinerja')

@section('topbar-actions')
  @if(session('user.role') === 'admin')
    <a href="{{ route('perjanjian.cascading') }}?tahun={{ $tahun }}" class="btn-rekap-glass">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M3 3v18h18M7 15l4-4 4 4 4-4"/>
      </svg>
      Refresh
    </a>
  @endif
@endsection

@section('content')

<style>
  /* ==================== CSS KONSISTEN DENGAN TEMA GELAP ==================== */
  :root {
    --primary: #4f46e5;
    --primary-light: #6366f1;
    --success: #10b981;
    --success-light: rgba(16,185,129,0.15);
    --warning: #f59e0b;
    --warning-light: rgba(245,158,11,0.15);
    --danger: #ef4444;
    --danger-light: rgba(239,68,68,0.15);
    --info: #3b82f6;
    --info-light: rgba(59,130,246,0.15);
    --bg-card: linear-gradient(135deg, #1a1f2e, #141824);
    --border-light: rgba(255,255,255,0.07);
    --text-white: #ffffff;
    --text-gray: rgba(255,255,255,0.6);
  }

  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .animate-in {
    animation: fadeInUp 0.4s ease forwards;
  }

  .filter-glass {
    background: rgba(30,30,46,.8);
    backdrop-filter: blur(8px);
    border-radius: 20px;
    border: 1px solid var(--border-light);
    margin-bottom: 24px;
    overflow: hidden;
  }
  .filter-header {
    padding: 16px 24px;
    border-bottom: 1px solid var(--border-light);
  }
  .filter-header span {
    color: var(--text-white);
    font-weight: 600;
  }
  .filter-body {
    padding: 20px 24px;
  }
  .filter-select {
    background: rgba(0,0,0,.3);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 12px;
    padding: 10px 16px;
    color: white;
    width: 100%;
  }
  .filter-select option {
    background: #1a1f2e;
  }
  .btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    border: none;
    border-radius: 40px;
    padding: 10px 24px;
    color: white;
    font-weight: 600;
    cursor: pointer;
  }
  .btn-primary:hover {
    box-shadow: 0 4px 12px rgba(79,70,229,0.3);
  }

  .card-glass {
    background: var(--bg-card);
    border-radius: 20px;
    border: 1px solid var(--border-light);
    overflow: hidden;
    margin-bottom: 24px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
  }
  .card-glass:hover {
    box-shadow: 0 8px 32px rgba(0,0,0,0.35);
  }
  .card-header {
    padding: 16px 24px;
    border-bottom: 1px solid var(--border-light);
    background: rgba(255,255,255,.03);
  }
  .card-header h3 {
    font-size: 16px;
    font-weight: 700;
    margin: 0;
    color: var(--text-white);
  }
  .card-body {
    padding: 24px;
  }

  .upload-area {
    background: rgba(255,255,255,.05);
    border: 2px dashed rgba(255,255,255,.15);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 20px;
  }
  .upload-area label {
    color: var(--text-white);
    font-weight: 600;
  }
  .upload-area small {
    color: var(--text-gray);
  }

  .file-info {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(255,255,255,.05);
    border-radius: 12px;
    padding: 12px 16px;
    margin-top: 12px;
  }
  .file-info div {
    color: var(--text-white);
  }

  .badge-status {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 600;
  }
  .badge-approved {
    background: var(--success-light);
    color: #34d399;
    border: 1px solid rgba(16,185,129,0.28);
  }
  .badge-rejected {
    background: var(--danger-light);
    color: #f87171;
    border: 1px solid rgba(239,68,68,0.28);
  }
  .badge-pending {
    background: var(--warning-light);
    color: #fbbf24;
    border: 1px solid rgba(245,158,11,0.28);
  }
  .badge-default {
    background: rgba(255,255,255,0.05);
    color: var(--text-gray);
    border: 1px solid rgba(255,255,255,0.08);
  }

  .btn-upload {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border: none;
    border-radius: 40px;
    padding: 12px 24px;
    color: white;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
    transition: all 0.3s ease;
  }
  .btn-upload:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245,158,11,0.3);
  }

  .btn-outline {
    background: transparent;
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 40px;
    padding: 6px 14px;
    color: white;
    text-decoration: none;
    font-size: 12px;
    transition: all 0.2s ease;
  }
  .btn-outline:hover {
    background: rgba(255,255,255,.08);
    border-color: rgba(255,255,255,.35);
    text-decoration: none;
    color: white;
  }

  .btn-delete {
    background: transparent;
    border: 1px solid rgba(239,68,68,0.3);
    border-radius: 40px;
    padding: 4px 10px;
    color: #f87171;
    font-size: 11px;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .btn-delete:hover {
    background: rgba(239,68,68,0.15);
    border-color: rgba(239,68,68,0.5);
  }

  .table-glass {
    width: 100%;
    border-collapse: collapse;
  }
  .table-glass th {
    padding: 12px 16px;
    text-align: left;
    background: rgba(255,255,255,.05);
    font-size: 11px;
    font-weight: 600;
    color: var(--text-gray);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    border-bottom: 1px solid var(--border-light);
  }
  .table-glass td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--border-light);
    color: var(--text-white);
  }
  .table-glass tbody tr:hover td {
    background: rgba(255,255,255,.03);
  }

  /* Dropdown review */
  .review-select {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 6px;
    padding: 3px 8px;
    color: white;
    font-size: 10px;
    cursor: pointer;
    outline: none;
    transition: all 0.2s ease;
  }
  .review-select:hover {
    border-color: rgba(255,255,255,0.3);
  }
  .review-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 2px rgba(79,70,229,0.2);
  }
  .review-select option {
    background: #1a1f2e;
    color: white;
  }

  .file-label {
    font-size: 9px;
    color: var(--text-gray);
    background: rgba(255,255,255,0.05);
    padding: 2px 8px;
    border-radius: 10px;
    display: inline-block;
  }

  .alert-glass {
    border-radius: 12px;
    padding: 12px 16px;
    margin-bottom: 16px;
    border-left: 3px solid;
  }
  .alert-success {
    background: var(--success-light);
    border-color: var(--success);
    color: #34d399;
  }
  .alert-danger {
    background: var(--danger-light);
    border-color: var(--danger);
    color: #f87171;
  }
  .alert-warning {
    background: var(--warning-light);
    border-color: var(--warning);
    color: #fbbf24;
  }
</style>

{{-- FLASH MESSAGES --}}
@if(session('success'))
  <div class="alert-glass alert-success animate-in">
    ✅ {{ session('success') }}
  </div>
@endif
@if(session('error'))
  <div class="alert-glass alert-danger animate-in">
    ❌ {{ session('error') }}
  </div>
@endif

{{-- FILTER TAHUN --}}
<div class="filter-glass animate-in">
  <div class="filter-header">
    <span>🔍 Filter</span>
  </div>
  <div class="filter-body">
    <form method="GET" action="{{ route('perjanjian.cascading') }}">
      <div style="display: flex; gap: 16px; align-items: end; flex-wrap: wrap;">
        @if($isAdmin)
        <div style="flex: 1;">
          <label style="font-size: 11px; color: var(--text-gray);">Perangkat Daerah</label>
          <select name="opd_id" class="filter-select">
            <option value="">— Semua OPD —</option>
            @foreach($listOpd as $o)
              <option value="{{ $o->id }}" {{ $opdId == $o->id ? 'selected' : '' }}>{{ $o->nama }}</option>
            @endforeach
          </select>
        </div>
        @endif
        <div>
          <label style="font-size: 11px; color: var(--text-gray);">Tahun</label>
          <select name="tahun" class="filter-select" style="width: 120px;">
            @foreach($listTahun as $t)
              <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn-primary">🔍 Tampilkan</button>
      </div>
    </form>
  </div>
</div>

@if(!$opdId && !$isAdmin)
<div class="card-glass animate-in">
  <div class="card-body" style="text-align: center; padding: 60px;">
    <div style="font-size: 48px; margin-bottom: 16px;">🌳</div>
    <h3 style="color: var(--text-white); margin-bottom: 8px;">Pilih OPD Terlebih Dahulu</h3>
    <p style="color: var(--text-gray);">Gunakan filter di atas untuk memilih OPD.</p>
  </div>
</div>
@else

{{-- FORM UPLOAD (untuk operator) --}}
@if(!$isAdmin)
<div class="card-glass animate-in">
  <div class="card-header">
    <h3>📤 Upload Cascading & Pohon Kinerja</h3>
  </div>
  <div class="card-body">
    <form method="POST" action="{{ route('perjanjian.upload.cascading') }}" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="tahun" value="{{ $tahun }}">
      
      <div class="upload-area">
        <div style="margin-bottom: 16px;">
          <label>📄 File Cascading</label>
          <input type="file" name="file_cascading" class="filter-select" accept=".pdf,.docx,.xlsx,.doc,.xls" required>
          <small>PDF, DOC, DOCX, XLS, XLSX (Max 5MB)</small>
        </div>
        
        <div style="margin-bottom: 16px;">
          <label>🌳 File Pohon Kinerja</label>
          <input type="file" name="file_pohon_kinerja" class="filter-select" accept=".pdf,.docx,.xlsx,.doc,.xls" required>
          <small>PDF, DOC, DOCX, XLS, XLSX (Max 5MB)</small>
        </div>
        
        <div>
          <label>📝 Keterangan (opsional)</label>
          <textarea name="keterangan" class="filter-select" rows="2" placeholder="Tambahkan keterangan..."></textarea>
        </div>
      </div>
      
      <button type="submit" class="btn-upload">📤 Upload Kedua File</button>
    </form>
  </div>
</div>

{{-- STATUS UPLOAD TERKINI --}}
<div class="card-glass animate-in">
  <div class="card-header">
    <h3>📋 Status Dokumen Tahun {{ $tahun }}</h3>
  </div>
  <div class="card-body">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
      <div>
        <h4 style="color: var(--text-white); margin-bottom: 12px;">📄 Cascading</h4>
        @if($cascadingData)
          <div class="file-info">
            <div>
              <div style="font-weight: 600;">{{ $cascadingData->nama_file }}</div>
              <div style="font-size: 11px; color: var(--text-gray);">Upload: {{ \Carbon\Carbon::parse($cascadingData->created_at)->format('d/m/Y H:i') }}</div>
            </div>
            <div>
              @if($cascadingData->status == 'disetujui')
                <span class="badge-status badge-approved">✅ Disetujui</span>
              @elseif($cascadingData->status == 'ditolak')
                <span class="badge-status badge-rejected">❌ Ditolak</span>
              @else
                <span class="badge-status badge-pending">⏳ Menunggu</span>
              @endif
            </div>
          </div>
          @if($cascadingData->catatan_admin)
            <div style="margin-top: 8px; padding: 8px 12px; background: rgba(239,68,68,.1); border-radius: 8px; font-size: 12px; color: #f87171;">
              <strong>Catatan Admin:</strong> {{ $cascadingData->catatan_admin }}
            </div>
          @endif
        @else
          <div style="padding: 20px; text-align: center; background: rgba(255,255,255,.03); border-radius: 12px; color: var(--text-gray);">
            Belum upload file Cascading
          </div>
        @endif
      </div>
      
      <div>
        <h4 style="color: var(--text-white); margin-bottom: 12px;">🌳 Pohon Kinerja</h4>
        @if($pohonKinerjaData)
          <div class="file-info">
            <div>
              <div style="font-weight: 600;">{{ $pohonKinerjaData->nama_file }}</div>
              <div style="font-size: 11px; color: var(--text-gray);">Upload: {{ \Carbon\Carbon::parse($pohonKinerjaData->created_at)->format('d/m/Y H:i') }}</div>
            </div>
            <div>
              @if($pohonKinerjaData->status == 'disetujui')
                <span class="badge-status badge-approved">✅ Disetujui</span>
              @elseif($pohonKinerjaData->status == 'ditolak')
                <span class="badge-status badge-rejected">❌ Ditolak</span>
              @else
                <span class="badge-status badge-pending">⏳ Menunggu</span>
              @endif
            </div>
          </div>
          @if($pohonKinerjaData->catatan_admin)
            <div style="margin-top: 8px; padding: 8px 12px; background: rgba(239,68,68,.1); border-radius: 8px; font-size: 12px; color: #f87171;">
              <strong>Catatan Admin:</strong> {{ $pohonKinerjaData->catatan_admin }}
            </div>
          @endif
        @else
          <div style="padding: 20px; text-align: center; background: rgba(255,255,255,.03); border-radius: 12px; color: var(--text-gray);">
            Belum upload file Pohon Kinerja
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- HISTORI TAHUN SEBELUMNYA --}}
@if($cascadingDataSebelumnya || $pohonKinerjaDataSebelumnya)
<div class="card-glass animate-in">
  <div class="card-header">
    <h3>📜 Histori Tahun {{ $tahunSebelumnya }}</h3>
  </div>
  <div class="card-body">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
      <div>
        <h4 style="color: var(--text-white); margin-bottom: 12px;">📄 Cascading {{ $tahunSebelumnya }}</h4>
        @if($cascadingDataSebelumnya)
          <div class="file-info">
            <div>
              <div style="font-weight: 600;">{{ $cascadingDataSebelumnya->nama_file }}</div>
              <div style="font-size: 11px; color: var(--text-gray);">Status: {{ $cascadingDataSebelumnya->status }}</div>
            </div>
            <div>
              <a href="{{ route('perjanjian.preview', $cascadingDataSebelumnya->id) }}" target="_blank" class="btn-outline">👁️ Lihat</a>
            </div>
          </div>
        @else
          <div style="padding: 12px; text-align: center; background: rgba(255,255,255,.03); border-radius: 12px; color: var(--text-gray);">Tidak ada data</div>
        @endif
      </div>
      <div>
        <h4 style="color: var(--text-white); margin-bottom: 12px;">🌳 Pohon Kinerja {{ $tahunSebelumnya }}</h4>
        @if($pohonKinerjaDataSebelumnya)
          <div class="file-info">
            <div>
              <div style="font-weight: 600;">{{ $pohonKinerjaDataSebelumnya->nama_file }}</div>
              <div style="font-size: 11px; color: var(--text-gray);">Status: {{ $pohonKinerjaDataSebelumnya->status }}</div>
            </div>
            <div>
              <a href="{{ route('perjanjian.preview', $pohonKinerjaDataSebelumnya->id) }}" target="_blank" class="btn-outline">👁️ Lihat</a>
            </div>
          </div>
        @else
          <div style="padding: 12px; text-align: center; background: rgba(255,255,255,.03); border-radius: 12px; color: var(--text-gray);">Tidak ada data</div>
        @endif
      </div>
    </div>
  </div>
</div>
@endif
@endif

{{-- ════════════════════════════════════════════
     TABEL UNTUK ADMIN (dengan dropdown review)
════════════════════════════════════════════ --}}
@if($isAdmin)
<div class="card-glass animate-in">
  <div class="card-header">
    <h3>📋 Daftar Upload Cascading & Pohon Kinerja</h3>
  </div>
  <div class="card-body">
    <div style="overflow-x: auto;">
      <table class="table-glass">
        <thead>
          <tr>
            <th>OPD</th>
            <th>File Cascading</th>
            <th>Status</th>
            <th>File Pohon Kinerja</th>
            <th>Status</th>
            <th style="min-width: 120px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($listOpdData as $opdId => $files)
            @php
              $cascading = $files->where('sub_jenis', 'cascading')->first();
              $pohon = $files->where('sub_jenis', 'pohon_kinerja')->first();
              $opdName = $cascading->nama_opd ?? $pohon->nama_opd ?? '-';
            @endphp
            <tr>
              {{-- OPD --}}
              <td><strong>{{ $opdName }}</strong></td>
              
              {{-- File Cascading --}}
              <td>
                @if($cascading)
                  <div style="font-size: 12px;">{{ $cascading->nama_file }}</div>
                  <div style="font-size: 10px; color: var(--text-gray);">
                    {{ \Carbon\Carbon::parse($cascading->created_at)->format('d/m/Y') }}
                    @if($cascading->catatan_admin)
                      <br><span style="color: #f87171;">📝 {{ $cascading->catatan_admin }}</span>
                    @endif
                  </div>
                @else
                  <span style="color: var(--text-gray);">Belum upload</span>
                @endif
              </td>
              
              {{-- Status Cascading --}}
              <td>
                @if($cascading)
                  @if($cascading->status == 'disetujui')
                    <span class="badge-status badge-approved">✅ Disetujui</span>
                  @elseif($cascading->status == 'ditolak')
                    <span class="badge-status badge-rejected">❌ Ditolak</span>
                  @else
                    <span class="badge-status badge-pending">⏳ Menunggu</span>
                  @endif
                @else
                  <span class="badge-status badge-default">—</span>
                @endif
              </td>
              
              {{-- File Pohon Kinerja --}}
              <td>
                @if($pohon)
                  <div style="font-size: 12px;">{{ $pohon->nama_file }}</div>
                  <div style="font-size: 10px; color: var(--text-gray);">
                    {{ \Carbon\Carbon::parse($pohon->created_at)->format('d/m/Y') }}
                    @if($pohon->catatan_admin)
                      <br><span style="color: #f87171;">📝 {{ $pohon->catatan_admin }}</span>
                    @endif
                  </div>
                @else
                  <span style="color: var(--text-gray);">Belum upload</span>
                @endif
              </td>
              
              {{-- Status Pohon Kinerja --}}
              <td>
                @if($pohon)
                  @if($pohon->status == 'disetujui')
                    <span class="badge-status badge-approved">✅ Disetujui</span>
                  @elseif($pohon->status == 'ditolak')
                    <span class="badge-status badge-rejected">❌ Ditolak</span>
                  @else
                    <span class="badge-status badge-pending">⏳ Menunggu</span>
                  @endif
                @else
                  <span class="badge-status badge-default">—</span>
                @endif
              </td>
              
              {{-- AKSI --}}
              <td>
                <div style="display: flex; flex-direction: column; gap: 6px;">
                  
                  {{-- Review Cascading --}}
                  @if($cascading)
                    <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                      <span class="file-label">📄 Casc:</span>
                      <form method="POST" action="{{ route('perjanjian.review.cascading', $cascading->id) }}" style="display: inline;">
                        @csrf
                        <select name="status" class="review-select" onchange="this.form.submit()">
                          <option value="menunggu" {{ $cascading->status == 'menunggu' ? 'selected' : '' }}>⏳ Menunggu</option>
                          <option value="disetujui" {{ $cascading->status == 'disetujui' ? 'selected' : '' }}>✅ Setujui</option>
                          <option value="ditolak" {{ $cascading->status == 'ditolak' ? 'selected' : '' }}>❌ Tolak</option>
                        </select>
                      </form>
                      <a href="{{ route('perjanjian.preview', $cascading->id) }}" target="_blank" class="btn-outline" style="padding: 2px 8px; font-size: 10px;">👁️</a>
                      <form method="POST" action="{{ route('perjanjian.hapus.cascading', $cascading->id) }}" style="display: inline;" onsubmit="return confirm('Hapus file Cascading ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete" style="padding: 2px 8px; font-size: 10px;">🗑️</button>
                      </form>
                    </div>
                  @endif
                  
                  {{-- Review Pohon Kinerja --}}
                  @if($pohon)
                    <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                      <span class="file-label">🌳 Pohon:</span>
                      <form method="POST" action="{{ route('perjanjian.review.cascading', $pohon->id) }}" style="display: inline;">
                        @csrf
                        <select name="status" class="review-select" onchange="this.form.submit()">
                          <option value="menunggu" {{ $pohon->status == 'menunggu' ? 'selected' : '' }}>⏳ Menunggu</option>
                          <option value="disetujui" {{ $pohon->status == 'disetujui' ? 'selected' : '' }}>✅ Setujui</option>
                          <option value="ditolak" {{ $pohon->status == 'ditolak' ? 'selected' : '' }}>❌ Tolak</option>
                        </select>
                      </form>
                      <a href="{{ route('perjanjian.preview', $pohon->id) }}" target="_blank" class="btn-outline" style="padding: 2px 8px; font-size: 10px;">👁️</a>
                      <form method="POST" action="{{ route('perjanjian.hapus.cascading', $pohon->id) }}" style="display: inline;" onsubmit="return confirm('Hapus file Pohon Kinerja ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete" style="padding: 2px 8px; font-size: 10px;">🗑️</button>
                      </form>
                    </div>
                  @endif
                  
                  @if(!$cascading && !$pohon)
                    <span style="color: var(--text-gray); font-size: 11px;">Belum ada file</span>
                  @endif
                  
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-gray);">
                📭 Belum ada data upload
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif

@endif

@endsection