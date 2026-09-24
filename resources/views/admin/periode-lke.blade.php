@extends('layouts.app')
@section('title', 'Periode Pengisian LKE AKIP')
@section('page-title', 'Manajemen Pengguna')
@section('page-sub', 'Periode Pengisian LKE AKIP')

@section('content')
<style>
  .card { background:#1a1a24; border:1px solid rgba(255,255,255,.08); border-radius:16px; padding:22px; margin-bottom:20px; }
  .card h3 { font-size:14px; font-weight:700; color:#f4f4f5; margin-bottom:14px; }
  .frm-label { font-size:11px; font-weight:600; color:#a1a1aa; text-transform:uppercase; letter-spacing:.4px; display:block; margin-bottom:6px; }
  .frm-input, .frm-select { width:100%; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); border-radius:10px; padding:10px 12px; color:#fff; font-size:13px; outline:none; }
  .frm-select option { background:#1a1a24; }
  .frm-field { margin-bottom:16px; }
  .btn-simpan { background:linear-gradient(135deg,#6366f1,#4f46e5); border:none; color:#fff; padding:11px 26px; border-radius:10px; font-weight:700; font-size:13px; cursor:pointer; }
  .badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:999px; font-size:11px; font-weight:700; }
  .badge-ok   { background:rgba(16,185,129,.15); border:1px solid rgba(16,185,129,.3); color:#34d399; }
  .badge-err  { background:rgba(239,68,68,.15);  border:1px solid rgba(239,68,68,.3);  color:#f87171; }
  .badge-warn { background:rgba(245,158,11,.15); border:1px solid rgba(245,158,11,.3); color:#fbbf24; }
  table { width:100%; border-collapse:collapse; }
  th { text-align:left; padding:10px 14px; font-size:11px; text-transform:uppercase; color:#71717a; border-bottom:1px solid rgba(255,255,255,.08); }
  td { padding:12px 14px; font-size:13px; color:#a1a1aa; border-bottom:1px solid rgba(255,255,255,.05); }
  .alert { border-radius:12px; padding:12px 16px; margin-bottom:16px; font-size:13px; }
  .alert-success { background:rgba(16,185,129,.12); border:1px solid rgba(16,185,129,.3); color:#34d399; }
  .alert-danger  { background:rgba(239,68,68,.12);  border:1px solid rgba(239,68,68,.3);  color:#f87171; }
</style>

@if(session('success'))<div class="alert alert-success">✅ {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">❌ {{ session('error') }}</div>@endif

<div class="card">
  <h3>⏱️ Atur Periode Pengisian LKE AKIP</h3>

  <form method="GET" style="margin-bottom:18px;">
    <div style="display:flex;gap:10px;align-items:end;">
      <div style="min-width:160px;">
        <label class="frm-label">Pilih Tahun</label>
        <select name="tahun" class="frm-select" onchange="this.form.submit()">
          @foreach($listTahun as $t)
            <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
          @endforeach
        </select>
      </div>
    </div>
  </form>

  <form method="POST" action="{{ route('admin.periode-lke.simpan') }}">
    @csrf
    <input type="hidden" name="tahun" value="{{ $tahun }}">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
      <div class="frm-field">
        <label class="frm-label">Tanggal &amp; Jam Mulai</label>
        <input type="datetime-local" name="tanggal_mulai" class="frm-input"
               value="{{ $periode?->tanggal_mulai ? \Carbon\Carbon::parse($periode->tanggal_mulai)->format('Y-m-d\TH:i') : '' }}">
      </div>
      <div class="frm-field">
        <label class="frm-label">Tanggal &amp; Jam Selesai (tenggang waktu)</label>
        <input type="datetime-local" name="tanggal_selesai" class="frm-input"
               value="{{ $periode?->tanggal_selesai ? \Carbon\Carbon::parse($periode->tanggal_selesai)->format('Y-m-d\TH:i') : '' }}">
      </div>
    </div>

    <div class="frm-field">
      <label class="frm-label">Status</label>
      <select name="status_manual" class="frm-select">
        <option value="otomatis" {{ ($periode->status_manual ?? 'otomatis') === 'otomatis' ? 'selected' : '' }}>
          Otomatis (ikuti tanggal mulai/selesai di atas)
        </option>
        <option value="dibuka_paksa" {{ ($periode->status_manual ?? '') === 'dibuka_paksa' ? 'selected' : '' }}>
          🔓 Paksa Buka (abaikan tanggal, operator selalu bisa isi)
        </option>
        <option value="ditutup_paksa" {{ ($periode->status_manual ?? '') === 'ditutup_paksa' ? 'selected' : '' }}>
          🔒 Paksa Tutup (abaikan tanggal, operator tidak bisa isi apa pun)
        </option>
      </select>
    </div>

    <div class="frm-field">
      <label class="frm-label">Keterangan / Pesan ke Operator <span style="font-weight:400;color:#71717a;">(opsional)</span></label>
      <input type="text" name="keterangan" class="frm-input"
             placeholder="Contoh: Batas akhir pengisian LKE telah berakhir, hubungi admin jika ada kendala."
             value="{{ $periode->keterangan ?? '' }}">
    </div>

    <button type="submit" class="btn-simpan">💾 Simpan Periode Tahun {{ $tahun }}</button>
  </form>
</div>

<div class="card">
  <h3>📋 Riwayat Periode Semua Tahun</h3>
  <table>
    <thead>
      <tr>
        <th>Tahun</th>
        <th>Mulai</th>
        <th>Selesai</th>
        <th>Status</th>
        <th>Diubah Oleh</th>
      </tr>
    </thead>
    <tbody>
      @forelse($daftarPeriode as $p)
      <tr>
        <td style="color:#f4f4f5;font-weight:600;">{{ $p->tahun }}</td>
        <td>{{ $p->tanggal_mulai ? \Carbon\Carbon::parse($p->tanggal_mulai)->translatedFormat('d M Y H:i') : '—' }}</td>
        <td>{{ $p->tanggal_selesai ? \Carbon\Carbon::parse($p->tanggal_selesai)->translatedFormat('d M Y H:i') : '—' }}</td>
        <td>
          @if($p->status_manual === 'ditutup_paksa')
            <span class="badge badge-err">🔒 Ditutup Paksa</span>
          @elseif($p->status_manual === 'dibuka_paksa')
            <span class="badge badge-ok">🔓 Dibuka Paksa</span>
          @else
            <span class="badge badge-warn">⚙️ Otomatis</span>
          @endif
        </td>
        <td>{{ $p->nama_updater ?? '—' }}</td>
      </tr>
      @empty
      <tr><td colspan="5" style="text-align:center;padding:30px;">Belum ada periode yang diatur.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection