@extends('layouts.app')
@section('title', $judul)
@section('page-title', 'Evaluasi Kinerja')
@section('page-sub', $judul)

@section('topbar-actions')
  @if(session('user.role') === 'admin')
    <button class="btn btn-gold" onclick="document.getElementById('modalUpload').classList.add('open')">
      📤 Upload {{ $judul }}
    </button>
  @endif
@endsection

@section('content')
<div class="alert alert-info">
  ℹ️ {{ $judul }} — {{ session('user.role')==='admin' ? 'Upload dokumen agar dapat diunduh oleh Operator Daerah.' : 'Download dokumen yang telah disiapkan Admin .' }}
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">{{ $judul === 'Juknis' ? '📋' : '📊' }} {{ $judul }}</div>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>Nama Dokumen</th><th>Tahun</th><th>Nama File</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        @forelse($list as $t)
        <tr>
          <td>{{ $judul === 'Juknis' ? '📋 Juknis Evaluasi AKIP' : '📊 LKE AKIP' }}</td>
          <td>{{ $t->tahun }}</td>
          <td style="font-size:12px">{{ $t->nama_file }}</td>
          <td><span class="badge badge-success">✅ Tersedia</span></td>
          <td>
            <a href="{{ route('evaluasi.download', $t->id) }}" class="btn btn-primary btn-sm">⬇️ Download</a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5">
            <div class="empty-state">
              <span class="icon">{{ $judul === 'Juknis' ? '📋' : '📊' }}</span>
              <h3>Belum ada {{ $judul }}</h3>
              <p>{{ session('user.role')==='admin' ? 'Klik Upload '.$judul.' untuk menambahkan.' : 'Hubungi Admin .' }}</p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@if(session('user.role') === 'admin')
<div class="modal-wrap" id="modalUpload">
  <div class="modal-box">
    <div class="modal-hd">
      <div class="modal-title">📤 Upload {{ $judul }}</div>
      <button class="modal-close" onclick="document.getElementById('modalUpload').classList.remove('open')">✕</button>
    </div>
    <form method="POST" action="{{ route('evaluasi.upload') }}" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="jenis" value="{{ $jenisUpload }}">
      <div class="modal-bd">
        <div class="alert alert-info" style="font-size:12px">📁 Format: PDF, Word (.docx), Excel (.xlsx) — Maks 5MB</div>
        <div class="form-group" style="margin-top:12px">
          <label class="form-label">Tahun</label>
          <input type="number" class="form-control" name="tahun" value="{{ date('Y') }}" required>
        </div>
        <div class="form-group">
          <label class="form-label">Pilih File</label>
          <input type="file" class="form-control" name="file" accept=".pdf,.docx,.xlsx,.doc,.xls" required>
        </div>
      </div>
      <div class="modal-ft">
        <button type="button" class="btn btn-outline" onclick="document.getElementById('modalUpload').classList.remove('open')">Batal</button>
        <button type="submit" class="btn btn-gold">📂 Upload</button>
      </div>
    </form>
  </div>
</div>
@endif
@endsection

@section('scripts')
<script>
document.querySelectorAll('.modal-wrap').forEach(function(m) {
  m.addEventListener('click', function(e) { if(e.target===this) this.classList.remove('open'); });
});
</script>
@endsection
