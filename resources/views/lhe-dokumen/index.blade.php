@extends('layouts.app')
@section('title', 'LHE Dokumen')
@section('page-title', 'Evaluasi Kinerja')
@section('page-sub', 'LHE Dokumen')

@section('content')
<style>
  .card { background:var(--bg-card); border:1px solid var(--border-color); border-radius:16px; padding:22px; margin-bottom:20px; }
  .card h3 { font-size:14px; font-weight:700; color:var(--text-primary); margin-bottom:14px; }
  .frm-label { font-size:11px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.4px; display:block; margin-bottom:6px; }
  .frm-input, .frm-select { width:100%; background:var(--bg-input); border:1px solid var(--border-input); border-radius:10px; padding:10px 12px; color:var(--text-primary); font-size:13px; outline:none; }
  .frm-select option { background:var(--bg-card-solid); }
  .frm-field { margin-bottom:16px; }
  .btn-simpan { background:linear-gradient(135deg,#6366f1,#4f46e5); border:none; color:#fff; padding:11px 26px; border-radius:10px; font-weight:700; font-size:13px; cursor:pointer; }
  .btn-aksi { display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border-radius:8px; font-size:11px; font-weight:600; cursor:pointer; text-decoration:none; border:none; }
  .btn-lihat { background:rgba(59,130,246,.15); border:1px solid rgba(59,130,246,.3); color:#60a5fa; }
  .btn-unduh { background:rgba(16,185,129,.15); border:1px solid rgba(16,185,129,.3); color:#34d399; }
  .btn-hapus { background:rgba(239,68,68,.15); border:1px solid rgba(239,68,68,.3); color:#f87171; }
  table { width:100%; border-collapse:collapse; }
  th { text-align:left; padding:10px 14px; font-size:11px; text-transform:uppercase; color:var(--text-muted); border-bottom:1px solid var(--border-color); }
  td { padding:12px 14px; font-size:13px; color:var(--text-secondary); border-bottom:1px solid var(--border-light); }
</style>

<div class="card">
  <h3>🔍 Filter</h3>
  <form method="GET">
    <div style="display:flex;gap:12px;align-items:end;flex-wrap:wrap;">
      @if($user['role'] !== 'operator')
      <div style="min-width:220px;">
        <label class="frm-label">Perangkat Daerah</label>
        <select name="opd_id" class="frm-select">
          <option value="">— Semua OPD —</option>
          @foreach($listOpd as $o)
            <option value="{{ $o->id }}" {{ ($opdId ?? '') == $o->id ? 'selected' : '' }}>{{ $o->nama }}</option>
          @endforeach
        </select>
      </div>
      @endif
      <div style="min-width:140px;">
        <label class="frm-label">Tahun</label>
        <select name="tahun" class="frm-select">
          @foreach($listTahun as $t)
            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn-simpan" style="padding:10px 22px;">Tampilkan</button>
    </div>
  </form>
</div>

@if($user['role'] === 'admin')
<div class="card">
  <h3>📤 Upload Dokumen LHE AKIP</h3>
  <form method="POST" action="{{ route('evaluasi.lhe-dokumen.upload') }}" enctype="multipart/form-data">
    @csrf
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
      <div class="frm-field">
        <label class="frm-label">Perangkat Daerah *</label>
        <select name="perangkat_daerah_id" class="frm-select" required>
          <option value="">-- Pilih OPD --</option>
          @foreach($listOpd as $o)
            <option value="{{ $o->id }}">{{ $o->nama }}</option>
          @endforeach
        </select>
      </div>
      <div class="frm-field">
        <label class="frm-label">Tahun *</label>
        <select name="tahun" class="frm-select" required>
          @foreach($listTahun as $t)
            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="frm-field">
      <label class="frm-label">File Dokumen (PDF/DOC/DOCX) *</label>
      <input type="file" name="file" class="frm-input" accept=".pdf,.doc,.docx" required>
    </div>
    <div class="frm-field">
      <label class="frm-label">Keterangan <span style="font-weight:400;color:var(--text-light);">(opsional)</span></label>
      <input type="text" name="keterangan" class="frm-input" placeholder="Contoh: LHE AKIP final, sudah ditandatangani">
    </div>
    <button type="submit" class="btn-simpan">📤 Upload Dokumen</button>
  </form>
</div>
@endif

<div class="card">
  <h3>📋 Daftar Dokumen LHE AKIP Tahun {{ $tahun }}</h3>
  <div style="overflow-x:auto;">
    <table>
      <thead>
        <tr>
          @if($user['role'] !== 'operator')<th>OPD</th>@endif
          <th>Nama File</th>
          <th>Keterangan</th>
          <th>Diupload</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($dokumen as $d)
        <tr>
          @if($user['role'] !== 'operator')<td>{{ $d->nama_opd ?? '—' }}</td>@endif
          <td>
            {{ $d->nama_file }}
            @if(!$d->file_exists)
              <span style="color:#f87171;font-size:10px;display:block;">⚠️ File tidak ditemukan</span>
            @endif
          </td>
          <td>{{ $d->keterangan ?: '—' }}</td>
          <td>
            {{ \Carbon\Carbon::parse($d->created_at)->translatedFormat('d M Y H:i') }}<br>
            <span style="font-size:10px;color:var(--text-light);">oleh {{ $d->nama_uploader ?? '—' }}</span>
          </td>
          <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap;">
              @if($d->file_exists)
                <a href="{{ route('evaluasi.lhe-dokumen.lihat', $d->id) }}" target="_blank" class="btn-aksi btn-lihat">👁 Lihat</a>
                <a href="{{ route('evaluasi.lhe-dokumen.unduh', $d->id) }}" class="btn-aksi btn-unduh">⬇ Unduh</a>
              @endif
              @if($user['role'] === 'admin')
                <form method="POST" action="{{ route('evaluasi.lhe-dokumen.hapus', $d->id) }}"
                      onsubmit="return confirm('Hapus dokumen ini?')" style="display:inline;">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn-aksi btn-hapus">🗑 Hapus</button>
                </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="{{ $user['role'] !== 'operator' ? 5 : 4 }}" style="text-align:center;padding:40px;color:var(--text-muted);">
            📭 Belum ada dokumen LHE AKIP untuk tahun {{ $tahun }}
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection