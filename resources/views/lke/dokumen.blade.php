@extends('layouts.app')
@section('title', 'Dokumen LKE AKIP')
@section('page-title', 'Evaluasi Kinerja')
@section('page-sub', 'Semua Dokumen Pendukung LKE AKIP')

@section('topbar-actions')
  <a href="{{ route('evaluasi.lke') }}?tahun={{ $tahun }}" class="btn-rekap-glass">
    ← Kembali ke Evaluasi
  </a>
@endsection

@section('content')

<style>
  .filter-glass {
    background: white;
    border-radius: 20px;
    border: 1px solid #e5e7eb;
    margin-bottom: 24px;
    overflow: hidden;
  }
  .filter-header {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    padding: 16px 24px;
    border-bottom: 1px solid #e5e7eb;
  }
  .filter-body {
    padding: 20px 24px;
  }
  .filter-select {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 10px 16px;
    width: 100%;
    background: white;
  }
  .btn-primary {
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    border: none;
    border-radius: 40px;
    padding: 10px 24px;
    color: white;
    font-weight: 600;
    cursor: pointer;
  }
  .table-glass {
    background: white;
    border-radius: 20px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
  }
  .table-glass th {
    background: #f8fafc;
    padding: 14px 16px;
    font-size: 12px;
    font-weight: 700;
    border-bottom: 1px solid #e5e7eb;
    text-align: left;
  }
  .table-glass td {
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
  }
  .btn-outline {
    background: transparent;
    border: 1px solid #e5e7eb;
    border-radius: 40px;
    padding: 6px 14px;
    font-size: 12px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #374151;
  }
  .btn-outline:hover {
    background: #f3f4f6;
  }
  .btn-danger {
    background: transparent;
    border: 1px solid #fee2e2;
    border-radius: 40px;
    padding: 6px 14px;
    font-size: 12px;
    color: #dc2626;
    cursor: pointer;
  }
  .btn-danger:hover {
    background: #fee2e2;
  }
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
  }
  .stat-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    border: 1px solid #e5e7eb;
    text-align: center;
  }
  .stat-value {
    font-size: 28px;
    font-weight: 800;
    color: #4f46e5;
  }
  .stat-label {
    font-size: 12px;
    color: #6b7280;
    margin-top: 4px;
  }
  .file-icon {
    width: 32px;
    height: 32px;
    background: #eef2ff;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
  }
</style>

{{-- FILTER --}}
<div class="filter-glass">
  <div class="filter-header">
    <div class="card-title">🔍 Filter Dokumen</div>
  </div>
  <div class="filter-body">
    <form method="GET" action="{{ route('evaluasi.lke.dokumen.list') }}">
      <div style="display: flex; gap: 16px; align-items: end; flex-wrap: wrap;">
        <div style="flex: 1;">
          <label style="font-size: 12px; font-weight: 600; display: block; margin-bottom: 6px;">Perangkat Daerah</label>
          <select name="opd_id" class="filter-select">
            <option value="">— Semua OPD —</option>
            @foreach($listOpd as $o)
              <option value="{{ $o->id }}" {{ $opdId == $o->id ? 'selected' : '' }}>{{ $o->nama }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label style="font-size: 12px; font-weight: 600; display: block; margin-bottom: 6px;">Tahun</label>
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

{{-- STATISTIK --}}
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-value">{{ $stats['total'] }}</div>
    <div class="stat-label">Total Dokumen</div>
  </div>
  <div class="stat-card">
    <div class="stat-value">{{ count($stats['per_opd'] ?? []) }}</div>
    <div class="stat-label">OPD Upload</div>
  </div>
</div>

{{-- TABEL DOKUMEN --}}
<div class="table-glass">
  <div style="overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse;">
      <thead>
        <tr>
          <th style="width: 5%;">No</th>
          <th style="width: 15%;">OPD</th>
          <th style="width: 25%;">Komponen / Kriteria</th>
          <th style="width: 25%;">Nama File</th>
          <th style="width: 10%;">Uploader</th>
          <th style="width: 10%;">Tgl Upload</th>
          <th style="width: 10%;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($dokumen as $index => $d)
        <tr>
          <td style="text-align: center;">{{ $loop->iteration }}</td>
          <td>
            <div style="font-weight: 600;">{{ $d->nama_opd ?? '-' }}</div>
            <div style="font-size: 10px; color: #6b7280;">ID: {{ $d->perangkat_daerah_id }}</div>
           </td>
          <td>
            <div style="font-weight: 500;">{{ $d->komponen_nama ?? '-' }}</div>
            <div style="font-size: 11px; color: #6b7280;">Kriteria {{ $d->kriteria_nomor ?? '-' }}</div>
            <div style="font-size: 10px; color: #9ca3af; margin-top: 4px;">ID Kriteria: {{ $d->kriteria_id }}</div>
           </td>
          <td>
            <div style="display: flex; align-items: center; gap: 10px;">
              <div class="file-icon">
                @php
                  $ext = pathinfo($d->nama_file, PATHINFO_EXTENSION);
                @endphp
                @if($ext == 'pdf') 📄
                @elseif($ext == 'docx' || $ext == 'doc') 📝
                @elseif($ext == 'xlsx' || $ext == 'xls') 📊
                @elseif($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png') 🖼️
                @else 📁
                @endif
              </div>
              <div>
                <div style="font-weight: 500;">{{ $d->nama_file }}</div>
                <div style="font-size: 10px; color: #9ca3af;">{{ strtoupper($ext) }}</div>
              </div>
            </div>
           </td>
          <td>
            <div>{{ $d->uploader_nama ?? '-' }}</div>
            <div style="font-size: 10px; color: #9ca3af;">ID: {{ $d->uploaded_by }}</div>
           </td>
          <td>
            <div>{{ \Carbon\Carbon::parse($d->created_at)->format('d/m/Y') }}</div>
            <div style="font-size: 10px; color: #9ca3af;">{{ \Carbon\Carbon::parse($d->created_at)->format('H:i') }}</div>
           </td>
          <td>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
              <a href="{{ route('evaluasi.lke.lihat.dokumen', $d->id) }}" target="_blank" class="btn-outline">
                👁️ Lihat
              </a>
              <form method="POST" action="{{ route('evaluasi.lke.hapus.dokumen', $d->id) }}" 
                    onsubmit="return confirm('Yakin ingin menghapus dokumen {{ addslashes($d->nama_file) }}?')" 
                    style="display: inline;">
                @csrf
                <button type="submit" class="btn-danger">🗑️ Hapus</button>
              </form>
            </div>
           </td>
         </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align: center; padding: 60px;">
              <div style="font-size: 48px; margin-bottom: 12px;">📭</div>
              <div style="font-size: 16px; font-weight: 600; margin-bottom: 4px;">Belum Ada Dokumen</div>
              <div style="font-size: 13px; color: #6b7280;">Belum ada dokumen pendukung LKE AKIP yang diupload</div>
             </td>
           </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection