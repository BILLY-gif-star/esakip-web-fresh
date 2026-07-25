@extends('layouts.app')
@section('title', 'Petunjuk Teknis')
@section('page-title', 'Pelaporan Kinerja')
@section('page-sub', 'Petunjuk Teknis LKE AKIP')

@section('content')

<style>
  :root {
    --primary: #4f46e5;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --text-dark: #1f2937;
    --text-gray: #6b7280;
    --border-light: #e5e7eb;
  }
  
  .card-glass {
    background: white;
    border-radius: 20px;
    border: 1px solid var(--border-light);
    overflow: hidden;
    margin-bottom: 24px;
  }
  .card-header-glass {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    padding: 16px 24px;
    border-bottom: 1px solid var(--border-light);
  }
  .card-header-glass h3 {
    font-size: 16px;
    font-weight: 700;
    margin: 0;
  }
  .card-body-glass {
    padding: 24px;
  }
  .table-glass {
    width: 100%;
    border-collapse: collapse;
  }
  .table-glass th {
    background: #f8fafc;
    padding: 12px 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
    color: var(--text-gray);
    border-bottom: 1px solid var(--border-light);
  }
  .table-glass td {
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    color: var(--text-dark);
  }
  .table-glass tbody tr:hover {
    background: #fefce8;
  }
  .btn-outline-glass {
    background: transparent;
    border: 1px solid var(--border-light);
    border-radius: 40px;
    padding: 6px 14px;
    color: var(--text-gray);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .btn-outline-glass:hover {
    background: #f3f4f6;
    border-color: var(--primary);
    color: var(--primary);
  }
  .empty-state {
    text-align: center;
    padding: 48px;
    color: var(--text-gray);
  }
  .badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 600;
  }
  .badge-active { background: #d1fae5; color: #065f46; }
</style>

<div class="card-glass">
  <div class="card-header-glass">
    <h3>📘 Petunjuk Teknis LKE AKIP</h3>
  </div>
  <div class="card-body-glass">
    <table class="table-glass">
      <thead>
        <tr>
          <th>No</th>
          <th>Judul</th>
          <th>Nama File</th>
          <th>Tanggal Upload</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($juknisList as $index => $j)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $j->judul }}</td>
          <td>{{ $j->file_name }}</td>
          <td>{{ \Carbon\Carbon::parse($j->created_at)->format('d/m/Y') }}</td>
          <td>
            <div style="display:flex;gap:6px;">
              <a href="{{ route('juknis.preview', $j->id) }}" target="_blank" class="btn-outline-glass">👁️ Preview</a>
              <a href="{{ route('juknis.download', $j->id) }}" class="btn-outline-glass">⬇️ Download</a>
            </div>
          </td>
        </tr>
        @empty
          <tr>
            <td colspan="5" class="empty-state">📭 Belum ada Petunjuk Teknis yang diupload</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection