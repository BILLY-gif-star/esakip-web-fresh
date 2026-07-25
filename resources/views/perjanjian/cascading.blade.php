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
  :root {
    --primary: #4f46e5;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
  }
  
  .filter-glass {
    background: rgba(30,30,46,.8);
    backdrop-filter: blur(8px);
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,.08);
    margin-bottom: 24px;
    overflow: hidden;
  }
  .filter-header {
    padding: 16px 24px;
    border-bottom: 1px solid rgba(255,255,255,.08);
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
  .btn-primary {
    background: linear-gradient(135deg, var(--primary), #6366f1);
    border: none;
    border-radius: 40px;
    padding: 10px 24px;
    color: white;
    font-weight: 600;
    cursor: pointer;
  }
  .card-glass {
    background: linear-gradient(135deg, #1e1e2e, #181825);
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,.08);
    overflow: hidden;
    margin-bottom: 24px;
  }
  .card-header {
    padding: 16px 24px;
    border-bottom: 1px solid rgba(255,255,255,.08);
    background: rgba(255,255,255,.03);
  }
  .card-header h3 {
    font-size: 16px;
    font-weight: 700;
    margin: 0;
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
  .file-info {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(255,255,255,.05);
    border-radius: 12px;
    padding: 12px 16px;
    margin-top: 12px;
  }
  .badge-status {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 600;
  }
  .badge-approved { background: rgba(16,185,129,.2); color: #34d399; }
  .badge-rejected { background: rgba(239,68,68,.2); color: #f87171; }
  .badge-pending { background: rgba(245,158,11,.2); color: #fbbf24; }
  .btn-upload {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border: none;
    border-radius: 40px;
    padding: 12px 24px;
    color: white;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
  }
  .btn-outline {
    background: transparent;
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 40px;
    padding: 6px 14px;
    color: white;
    text-decoration: none;
    font-size: 12px;
  }
  .table-glass {
    width: 100%;
    border-collapse: collapse;
  }
  .table-glass th {
    padding: 12px 16px;
    text-align: left;
    background: rgba(255,255,255,.05);
    font-size: 12px;
    font-weight: 600;
  }
  .table-glass td {
    padding: 12px 16px;
    border-bottom: 1px solid rgba(255,255,255,.08);
  }
</style>

{{-- FILTER TAHUN --}}
<div class="filter-glass">
  <div class="filter-header">
    <span>🔍 Filter Tahun</span>
  </div>
  <div class="filter-body">
    <form method="GET" action="{{ route('perjanjian.cascading') }}">
      <div style="display: flex; gap: 16px; align-items: end; flex-wrap: wrap;">
        @if($isAdmin)
        <div style="flex: 1;">
          <label style="font-size: 11px; color: rgba(255,255,255,.6);">Perangkat Daerah</label>
          <select name="opd_id" class="filter-select">
            <option value="">— Semua OPD —</option>
            @foreach($listOpd as $o)
              <option value="{{ $o->id }}" {{ $opdId == $o->id ? 'selected' : '' }}>{{ $o->nama }}</option>
            @endforeach
          </select>
        </div>
        @endif
        <div>
          <label style="font-size: 11px; color: rgba(255,255,255,.6);">Tahun</label>
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
<div class="card-glass">
  <div class="card-body" style="text-align: center; padding: 60px;">
    <div style="font-size: 48px; margin-bottom: 16px;">🌳</div>
    <h3 style="margin-bottom: 8px;">Pilih OPD Terlebih Dahulu</h3>
    <p style="color: rgba(255,255,255,.5);">Gunakan filter di atas untuk memilih OPD.</p>
  </div>
</div>
@else

{{-- FORM UPLOAD (untuk operator) --}}
@if(!$isAdmin)
<div class="card-glass">
  <div class="card-header">
    <h3>📤 Upload Cascading & Pohon Kinerja</h3>
  </div>
  <div class="card-body">
    <form method="POST" action="{{ route('perjanjian.upload.cascading') }}" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="tahun" value="{{ $tahun }}">
      
      <div class="upload-area">
        <div style="margin-bottom: 16px;">
          <label style="display: block; margin-bottom: 8px; font-weight: 600;">📄 File Cascading</label>
          <input type="file" name="file_cascading" class="filter-select" accept=".pdf,.docx,.xlsx,.doc,.xls" required>
          <small style="color: rgba(255,255,255,.5);">PDF, DOC, DOCX, XLS, XLSX (Max 5MB)</small>
        </div>
        
        <div style="margin-bottom: 16px;">
          <label style="display: block; margin-bottom: 8px; font-weight: 600;">🌳 File Pohon Kinerja</label>
          <input type="file" name="file_pohon_kinerja" class="filter-select" accept=".pdf,.docx,.xlsx,.doc,.xls" required>
          <small style="color: rgba(255,255,255,.5);">PDF, DOC, DOCX, XLS, XLSX (Max 5MB)</small>
        </div>
        
        <div>
          <label style="display: block; margin-bottom: 8px; font-weight: 600;">📝 Keterangan (opsional)</label>
          <textarea name="keterangan" class="filter-select" rows="2" placeholder="Tambahkan keterangan..."></textarea>
        </div>
      </div>
      
      <button type="submit" class="btn-upload">
        📤 Upload Kedua File
      </button>
    </form>
  </div>
</div>

{{-- STATUS UPLOAD TERKINI --}}
<div class="card-glass">
  <div class="card-header">
    <h3>📋 Status Dokumen Tahun {{ $tahun }}</h3>
  </div>
  <div class="card-body">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
      <div>
        <h4 style="margin-bottom: 12px;">📄 Cascading</h4>
        @if($cascadingData)
          <div class="file-info">
            <div>
              <div style="font-weight: 600;">{{ $cascadingData->nama_file }}</div>
              <div style="font-size: 11px; color: rgba(255,255,255,.5);">Upload: {{ \Carbon\Carbon::parse($cascadingData->created_at)->format('d/m/Y H:i') }}</div>
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
            <div style="margin-top: 8px; padding: 8px 12px; background: rgba(239,68,68,.1); border-radius: 8px; font-size: 12px;">
              <strong>Catatan Admin:</strong> {{ $cascadingData->catatan_admin }}
            </div>
          @endif
        @else
          <div style="padding: 20px; text-align: center; background: rgba(255,255,255,.03); border-radius: 12px;">
            Belum upload file Cascading
          </div>
        @endif
      </div>
      
      <div>
        <h4 style="margin-bottom: 12px;">🌳 Pohon Kinerja</h4>
        @if($pohonKinerjaData)
          <div class="file-info">
            <div>
              <div style="font-weight: 600;">{{ $pohonKinerjaData->nama_file }}</div>
              <div style="font-size: 11px; color: rgba(255,255,255,.5);">Upload: {{ \Carbon\Carbon::parse($pohonKinerjaData->created_at)->format('d/m/Y H:i') }}</div>
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
            <div style="margin-top: 8px; padding: 8px 12px; background: rgba(239,68,68,.1); border-radius: 8px; font-size: 12px;">
              <strong>Catatan Admin:</strong> {{ $pohonKinerjaData->catatan_admin }}
            </div>
          @endif
        @else
          <div style="padding: 20px; text-align: center; background: rgba(255,255,255,.03); border-radius: 12px;">
            Belum upload file Pohon Kinerja
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- HISTORI TAHUN SEBELUMNYA --}}
@if($cascadingDataSebelumnya || $pohonKinerjaDataSebelumnya)
<div class="card-glass">
  <div class="card-header">
    <h3>📜 Histori Tahun {{ $tahunSebelumnya }}</h3>
  </div>
  <div class="card-body">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
      <div>
        <h4 style="margin-bottom: 12px;">📄 Cascading {{ $tahunSebelumnya }}</h4>
        @if($cascadingDataSebelumnya)
          <div class="file-info">
            <div>
              <div style="font-weight: 600;">{{ $cascadingDataSebelumnya->nama_file }}</div>
              <div style="font-size: 11px;">Status: {{ $cascadingDataSebelumnya->status }}</div>
            </div>
            <div>
              <a href="{{ route('perjanjian.preview', $cascadingDataSebelumnya->id) }}" target="_blank" class="btn-outline">👁️ Lihat</a>
            </div>
          </div>
        @else
          <div style="padding: 12px; text-align: center; background: rgba(255,255,255,.03); border-radius: 12px;">Tidak ada data</div>
        @endif
      </div>
      <div>
        <h4 style="margin-bottom: 12px;">🌳 Pohon Kinerja {{ $tahunSebelumnya }}</h4>
        @if($pohonKinerjaDataSebelumnya)
          <div class="file-info">
            <div>
              <div style="font-weight: 600;">{{ $pohonKinerjaDataSebelumnya->nama_file }}</div>
              <div style="font-size: 11px;">Status: {{ $pohonKinerjaDataSebelumnya->status }}</div>
            </div>
            <div>
              <a href="{{ route('perjanjian.preview', $pohonKinerjaDataSebelumnya->id) }}" target="_blank" class="btn-outline">👁️ Lihat</a>
            </div>
          </div>
        @else
          <div style="padding: 12px; text-align: center; background: rgba(255,255,255,.03); border-radius: 12px;">Tidak ada data</div>
        @endif
      </div>
    </div>
  </div>
</div>
@endif
@endif

{{-- TABEL UNTUK ADMIN --}}
@if($isAdmin)
<div class="card-glass">
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
            <th>File Pohon Kinerja</th>
            <th>Status</th>
            <th>Tgl Upload</th>
            <th>Aksi</th>
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
              <td><strong>{{ $opdName }}</strong></td>
              <td>
                @if($cascading)
                  <div style="font-size: 12px;">{{ $cascading->nama_file }}</div>
                  <div style="font-size: 10px; color: rgba(255,255,255,.5);">{{ \Carbon\Carbon::parse($cascading->created_at)->format('d/m/Y') }}</div>
                @else
                  <span style="color: rgba(255,255,255,.4);">Belum upload</span>
                @endif
              </td>
              <td>
                @if($pohon)
                  <div style="font-size: 12px;">{{ $pohon->nama_file }}</div>
                  <div style="font-size: 10px; color: rgba(255,255,255,.5);">{{ \Carbon\Carbon::parse($pohon->created_at)->format('d/m/Y') }}</div>
                @else
                  <span style="color: rgba(255,255,255,.4);">Belum upload</span>
                @endif
              </td>
              <td>
                @php
                  $status = $cascading->status ?? $pohon->status ?? 'belum';
                @endphp
                @if($status == 'disetujui')
                  <span class="badge-status badge-approved">✅ Disetujui</span>
                @elseif($status == 'ditolak')
                  <span class="badge-status badge-rejected">❌ Ditolak</span>
                @elseif($status == 'menunggu')
                  <span class="badge-status badge-pending">⏳ Menunggu</span>
                @else
                  <span class="badge-status">📝 Belum</span>
                @endif
              </td>
              <td>{{ $cascading ? \Carbon\Carbon::parse($cascading->created_at)->format('d/m/Y') : ($pohon ? \Carbon\Carbon::parse($pohon->created_at)->format('d/m/Y') : '-') }}</td>
              <td>
                <div style="display: flex; gap: 8px;">
                  @if($cascading)
                    <a href="{{ route('perjanjian.preview', $cascading->id) }}" target="_blank" class="btn-outline">👁️ Cascading</a>
                  @endif
                  @if($pohon)
                    <a href="{{ route('perjanjian.preview', $pohon->id) }}" target="_blank" class="btn-outline">🌳 Pohon</a>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align: center; padding: 40px;">📭 Belum ada data upload</td>
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