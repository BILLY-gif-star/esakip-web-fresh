@extends('layouts.app')
@section('title', 'LKE AKIP')
@section('page-title', 'Evaluasi Kinerja')
@section('page-sub', 'Lembar Kerja Evaluasi AKIP')

@section('topbar-actions')
<div style="display:flex;gap:8px;align-items:center;">
  <a href="{{ route('evaluasi.lke.rekap') }}?tahun={{ $tahun }}"
     style="display:inline-flex;align-items:center;gap:7px;
            background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);
            padding:8px 16px;border-radius:10px;color:#e4e4e7;
            font-weight:600;font-size:12px;text-decoration:none;transition:all .2s;"
     onmouseover="this.style.background='rgba(99,102,241,.25)';this.style.borderColor='rgba(99,102,241,.5)'"
     onmouseout="this.style.background='rgba(255,255,255,.08)';this.style.borderColor='rgba(255,255,255,.15)'">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M3 3v18h18M7 15l4-4 4 4 4-4"/>
    </svg>
    Rekap Nilai
  </a>
  @if(session('user.role') === 'admin')
  <a href="{{ route('evaluasi.lke.dokumen.list') }}?tahun={{ $tahun }}"
     style="display:inline-flex;align-items:center;gap:7px;
            background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);
            padding:8px 16px;border-radius:10px;color:#e4e4e7;
            font-weight:600;font-size:12px;text-decoration:none;transition:all .2s;"
     onmouseover="this.style.background='rgba(99,102,241,.25)';this.style.borderColor='rgba(99,102,241,.5)'"
     onmouseout="this.style.background='rgba(255,255,255,.08)';this.style.borderColor='rgba(255,255,255,.15)'">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
      <polyline points="14 2 14 8 20 8"/>
      <line x1="16" y1="13" x2="8" y2="13"/>
    </svg>
    Semua Dokumen
  </a>
  @endif
</div>
@endsection

@section('content')
<style>
/* ═══════════════════════════════════════════════════════
   DARK THEME — konsisten dengan seluruh layout app
═══════════════════════════════════════════════════════ */
:root {
  --c1: #1a1a24;
  --c2: #141420;
  --c3: #0f0f18;
  --border: rgba(255,255,255,.08);
  --border2: rgba(255,255,255,.13);
  --t1: #f4f4f5;
  --t2: #a1a1aa;
  --t3: #71717a;
  --t4: #52525b;
  --acc: #6366f1;
  --acc2: #4f46e5;
  --ok: #10b981;
  --ok2: #34d399;
  --warn: #f59e0b;
  --warn2: #fbbf24;
  --err: #ef4444;
  --err2: #f87171;
}

@keyframes fadeUp {
  from { opacity:0; transform:translateY(14px); }
  to   { opacity:1; transform:translateY(0); }
}

.klaster-grid {
  display: grid;
  grid-template-columns: repeat(3,1fr);
  gap: 16px;
  margin-bottom: 22px;
  animation: fadeUp .3s ease;
}

.klaster-card {
  background: var(--c1);
  border: 1px solid var(--border);
  border-radius: 18px;
  overflow: hidden;
}

.klaster-header {
  background: linear-gradient(135deg,#1e3a5f,#2563eb);
  padding: 12px 16px;
  color: #fff;
  font-weight: 700;
  font-size: 13px;
}

.klaster-body {
  padding: 6px 0;
  max-height: 320px;
  overflow-y: auto;
}

.klaster-body::-webkit-scrollbar { width:3px; }
.klaster-body::-webkit-scrollbar-track { background:transparent; }
.klaster-body::-webkit-scrollbar-thumb { background:#6366f1;border-radius:4px; }

.klaster-item {
  padding: 7px 14px;
  border-bottom: 1px solid rgba(255,255,255,.04);
  font-size: 11.5px;
  color: rgba(255,255,255,.7);
  display: flex;
  gap: 10px;
  align-items: flex-start;
}

.klaster-item:hover { background: rgba(255,255,255,.04); }
.klaster-nomor { color: #fbbf24; font-weight: 700; flex-shrink:0; min-width:22px; }

.lke-card {
  background: var(--c1);
  border: 1px solid var(--border);
  border-radius: 18px;
  overflow: hidden;
  margin-bottom: 20px;
  animation: fadeUp .35s ease;
}

.lke-card-header {
  padding: 15px 22px;
  border-bottom: 1px solid var(--border);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.lke-card-body { padding: 20px 22px; }

.dk-input, .dk-select {
  width: 100%;
  background: rgba(255,255,255,.05);
  border: 1px solid rgba(255,255,255,.1);
  border-radius: 10px;
  padding: 10px 14px;
  color: #fff;
  font-size: 13px;
  outline: none;
  transition: border-color .2s;
  box-sizing: border-box;
}
.dk-input:focus, .dk-select:focus { border-color: rgba(99,102,241,.5); }
.dk-select option { background: #1a1a24; }
.dk-input:disabled { opacity:.5; cursor:not-allowed; }
.dk-label {
  display: block;
  font-size: 11px;
  font-weight: 600;
  color: var(--t3);
  text-transform: uppercase;
  letter-spacing: .4px;
  margin-bottom: 6px;
}

.dk-textarea {
  width: 100%;
  background: rgba(255,255,255,.04);
  border: 1px solid rgba(255,255,255,.08);
  border-radius: 8px;
  padding: 8px 10px;
  color: var(--t2);
  font-size: 11.5px;
  resize: none;
  overflow: hidden;
  outline: none;
  transition: border-color .2s, height .1s ease;
  box-sizing: border-box;
  line-height: 1.5;
}
.dk-textarea:focus { border-color: rgba(99,102,241,.4); }
.dk-textarea::placeholder { color: var(--t4); }

.dk-alert {
  border-radius: 12px;
  padding: 13px 18px;
  margin-bottom: 18px;
  font-size: 13px;
  display: flex;
  align-items: flex-start;
  gap: 10px;
  animation: fadeUp .3s ease;
}
.dk-alert.info { background:rgba(99,102,241,.1); border:1px solid rgba(99,102,241,.25); color:#a5b4fc; }
.dk-alert.warn { background:rgba(245,158,11,.1); border:1px solid rgba(245,158,11,.25); color:#fbbf24; }

.btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 14px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  text-decoration: none;
  transition: all .2s;
  white-space: nowrap;
}
.btn:hover { text-decoration: none; }
.btn-acc  { background:linear-gradient(135deg,var(--acc),var(--acc2)); color:#fff; }
.btn-acc:hover { opacity:.85; color:#fff; }
.btn-ok   { background:rgba(16,185,129,.15); border:1px solid rgba(16,185,129,.3); color:#34d399; }
.btn-ok:hover { background:rgba(16,185,129,.25); }
.btn-ghost { background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1); color:#a1a1aa; }
.btn-ghost:hover { background:rgba(255,255,255,.12); color:#fff; }
.btn-upload { background:rgba(99,102,241,.15); border:1px solid rgba(99,102,241,.3); color:#a5b4fc; font-size:11px; padding:5px 10px; border-radius:6px; }
.btn-upload:hover { background:rgba(99,102,241,.25); }
.btn-del { background:rgba(239,68,68,.12); border:1px solid rgba(239,68,68,.25); color:#f87171; font-size:11px; padding:5px 8px; border-radius:6px; }
.btn-del:hover { background:rgba(239,68,68,.22); }
.btn-lihat { background:rgba(59,130,246,.15); border:1px solid rgba(59,130,246,.3); color:#93c5fd; font-size:10px; padding:3px 8px; border-radius:5px; }
.btn-lihat:hover { background:rgba(59,130,246,.25); }

/* ⭐ PERBAIKAN: Input Nilai lebih lebar dan jelas */
.input-nilai {
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(99,102,241,.3);
  border-radius: 8px;
  padding: 8px 6px;
  width: 95px;
  text-align: center;
  color: #34d399;
  font-size: 15px;
  font-weight: 800;
  outline: none;
  transition: border-color .2s;
}
.input-nilai:focus { border-color: rgba(99,102,241,.5); }

/* Style untuk dropdown predikat */
.jawaban-select {
    width: 220px;
    padding: 8px 10px;
    border-radius: 8px;
    background: #1a1a2e !important;
    color: #fff;
    border: 1px solid rgba(255,255,255,.15);
    font-size: 12px;
    cursor: pointer;
}

.jawaban-select option {
    background: #1a1a2e;
    padding: 10px;
}

.jawaban-select option[value="AA"] { color: #059669; font-weight: bold; }
.jawaban-select option[value="A"] { color: #059669;  font-weight: bold; }
.jawaban-select option[value="BB"] { color: #059669; font-weight: bold; }
.jawaban-select option[value="B"] { color: #059669; font-weight: bold; }
.jawaban-select option[value="CC"] { color: #059669; font-weight: bold; }
.jawaban-select option[value="C"] { color: #059669; font-weight: bold; }
.jawaban-select option[value="D"] { color: #059669; font-weight: bold; }
.jawaban-select option[value="E"] { color: #059669; font-weight: bold; }

/* ⭐ Nilai display untuk operator */
.nilai-display {
  font-size: 15px;
  font-weight: 800;
  color: #34d399;
  background: rgba(16,185,129,.1);
  padding: 6px 12px;
  border-radius: 8px;
  display: inline-block;
  min-width: 70px;
  text-align: center;
}

.nilai-ok   { background:rgba(16,185,129,.15); color:#34d399; padding:5px 12px; border-radius:8px; font-weight:800; font-size:14px; display:inline-block; }
.nilai-dim  { background:rgba(255,255,255,.06); color:#71717a; padding:5px 10px; border-radius:8px; font-size:12px; }

.badge { display:inline-flex;align-items:center;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700; }
.badge-ok   { background:rgba(16,185,129,.15); border:1px solid rgba(16,185,129,.3); color:#34d399; }
.badge-warn { background:rgba(245,158,11,.15);  border:1px solid rgba(245,158,11,.3);  color:#fbbf24; }
.badge-dim  { background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1); color:#71717a; }

/* ⭐ PERBAIKAN: Tabel lebih lebar agar tidak terpotong */
.lke-table-wrap {
  overflow-x: auto;
  border-radius: 18px;
  border: 1px solid var(--border);
  margin-bottom: 100px;
  animation: fadeUp .4s ease;
}

.lke-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 1300px;
}

.lke-table thead th {
  background: var(--c3);
  color: var(--t3);
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .4px;
  padding: 13px 10px;
  text-align: center;
  border-bottom: 1px solid var(--border);
  white-space: nowrap;
}

/* Lebar kolom spesifik */
.lke-table th:nth-child(1) { min-width: 50px; }
.lke-table th:nth-child(2) { min-width: 280px; white-space: normal; }
.lke-table th:nth-child(3) { min-width: 60px; }
.lke-table th:nth-child(4) { min-width: 110px; }
.lke-table th:nth-child(5) { min-width: 240px; }
.lke-table th:nth-child(6) { min-width: 150px; }
.lke-table th:nth-child(7) { min-width: 150px; }
.lke-table th:nth-child(8) { min-width: 200px; }

.lke-table td {
  padding: 10px 8px;
  vertical-align: middle;
}

.lke-table td:nth-child(2) {
  white-space: normal;
}

/* Row types */
.row-komponen td {
  background: linear-gradient(135deg,#1e3a5f22,#2563eb18);
  border-top: 2px solid rgba(37,99,235,.4);
  border-bottom: 1px solid rgba(37,99,235,.2);
  padding: 13px 12px;
  color: var(--t1);
  font-weight: 700;
}

.row-sub td {
  background: rgba(99,102,241,.07);
  border-bottom: 1px solid rgba(99,102,241,.12);
  padding: 11px 12px;
  color: var(--t1);
}

.row-kriteria td {
  padding: 10px 12px;
  border-bottom: 1px solid rgba(255,255,255,.04);
  color: var(--t2);
  vertical-align: top;
}

.row-kriteria:nth-child(even) td { background: rgba(255,255,255,.015); }
.row-kriteria:hover td { background: rgba(255,255,255,.03); }

.row-total td {
  background: rgba(16,185,129,.08);
  border-top: 1px solid rgba(16,185,129,.25);
  border-bottom: 1px solid rgba(16,185,129,.15);
  padding: 12px;
  color: var(--ok2);
  font-weight: 700;
}

.row-spacer td { background: var(--c2); height: 6px; }

.row-grand td {
  background: linear-gradient(135deg,#1e293b,#0f172a);
  padding: 16px 12px;
  border-top: 2px solid rgba(255,255,255,.1);
}

.sub-nomor {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  background: rgba(37,99,235,.2);
  border: 1px solid rgba(37,99,235,.4);
  border-radius: 10px;
  font-weight: 800;
  font-size: 13px;
  color: #93c5fd;
}

.kr-nomor {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  background: rgba(255,255,255,.06);
  border: 1px solid rgba(255,255,255,.1);
  border-radius: 7px;
  font-weight: 700;
  font-size: 11px;
  color: var(--t3);
}

.catatan-admin {
  background: rgba(245,158,11,.08);
  border-left: 3px solid rgba(245,158,11,.4);
  border-radius: 0 8px 8px 0;
  padding: 8px 10px;
  font-size: 11px;
  color: #fbbf24;
  line-height: 1.5;
}

.catatan-opd {
  background: rgba(59,130,246,.08);
  border-left: 3px solid rgba(59,130,246,.3);
  border-radius: 0 8px 8px 0;
  padding: 8px 10px;
  font-size: 11px;
  color: #93c5fd;
  line-height: 1.5;
}

.doc-item {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 5px 8px;
  background: rgba(255,255,255,.04);
  border: 1px solid rgba(255,255,255,.07);
  border-radius: 7px;
  margin-top: 5px;
}

.doc-name {
  font-size: 10.5px;
  color: var(--t2);
  flex: 1;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 130px;
}

.upload-row {
  display: flex;
  gap: 6px;
  align-items: center;
  margin-top: 8px;
}

.upload-row input[type="file"] {
  flex: 1;
  background: rgba(255,255,255,.04);
  border: 1px solid rgba(255,255,255,.08);
  border-radius: 7px;
  padding: 5px 8px;
  color: var(--t2);
  font-size: 10.5px;
  outline: none;
}

.sticky-footer {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 100;
  background: rgba(20,20,32,.95);
  backdrop-filter: blur(20px);
  border-top: 1px solid rgba(255,255,255,.08);
  padding: 14px 32px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}

.empty-lke {
  text-align: center;
  padding: 60px 20px;
  background: var(--c1);
  border: 1px solid var(--border);
  border-radius: 18px;
}

@media (max-width: 768px) {
  .klaster-grid { grid-template-columns: 1fr; }
}
</style>

<div class="lke-card">
  <div class="lke-card-header">
    <div style="font-size:14px;font-weight:700;color:var(--t1);display:flex;align-items:center;gap:8px;">
      🔍 Pilih OPD &amp; Tahun
    </div>
  </div>
  <div class="lke-card-body">
    <form method="GET" action="{{ route('evaluasi.lke') }}">
      <div style="display:grid;grid-template-columns:1fr 140px auto;gap:12px;align-items:end;">
        <div>
          <label class="dk-label">Perangkat Daerah</label>
          @if(session('user.role') === 'admin')
            <select class="dk-select" name="opd_id" required>
              <option value="">— Pilih OPD —</option>
              @foreach($listOpd as $o)
                <option value="{{ $o->id }}" {{ $opdId == $o->id ? 'selected' : '' }}>
                  {{ $o->nama }}
                </option>
              @endforeach
            </select>
          @elseif(session('user.role') === 'evaluator')
            <select class="dk-select" name="opd_id" required>
              <option value="">— Pilih OPD —</option>
              @foreach($listOpd as $o)
                <option value="{{ $o->id }}" {{ $opdId == $o->id ? 'selected' : '' }}>
                  {{ $o->nama }}
                </option>
              @endforeach
            </select>
            @if($listOpd->isEmpty())
              <small style="color: #f59e0b; display: block; margin-top: 6px;">
                ⚠️ Belum ada OPD yang diassign. Hubungi admin.
              </small>
            @endif
          @else
            <input class="dk-input" type="text" disabled
                   value="{{ session('user.nama_daerah') ?? $listOpd->firstWhere('id', $opdId)?->nama ?? '' }}">
          @endif
        </div>
        <div>
          <label class="dk-label">Tahun</label>
          <select class="dk-select" name="tahun">
            @foreach($listTahun as $t)
              <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-acc" style="padding:10px 20px;">
          🔍 Tampilkan
        </button>
      </div>
    </form>
  </div>
</div>

@if(!$opdId)
  <div class="empty-lke">
    <div style="font-size:44px;margin-bottom:14px;opacity:.35;">📊</div>
    <div style="font-size:15px;font-weight:700;color:var(--t1);margin-bottom:8px;">Pilih OPD terlebih dahulu</div>
    <div style="font-size:13px;color:var(--t3);">Gunakan filter di atas untuk memilih OPD yang akan dievaluasi.</div>
  </div>
@else

@php
  $isAdmin  = session('user.role') === 'admin';
  $namaOpd  = $listOpd->firstWhere('id', $opdId)?->nama ?? session('user.nama_daerah') ?? '';
  $warnaMap = [
    'AA'=>'#059669','A'=>'#2563eb','BB'=>'#7c3aed','B'=>'#d4982e',
    'CC'=>'#f59e0b','C'=>'#dc2626','D'=>'#991b1b','E'=>'#6b7280',
  ];
@endphp

@if($isAdmin)
<div class="dk-alert warn">
  <span>✏️ Pilih <strong>Jawaban (Predikat)</strong> untuk setiap sub-komponen. Nilai akan terisi otomatis berdasarkan bobot.</span>
</div>
@elseif(session('user.role') === 'evaluator')
<div class="dk-alert info">
  <span>📊 Anda sebagai <strong>Evaluator</strong> dapat memilih predikat jawaban untuk penilaian LKE AKIP.</span>
</div>
@else
<div class="dk-alert info">
  <span>📝 Anda sebagai <strong>Operator</strong> dapat mengisi Catatan, Daftar Evidence, dan mengupload dokumen pendukung. Penilaian dilakukan oleh Evaluator.</span>
</div>
@endif

<form method="POST" action="{{ route('evaluasi.lke.simpan') }}" id="formLke">
  @csrf
  <input type="hidden" name="tahun"  value="{{ $tahun }}">
  <input type="hidden" name="opd_id" value="{{ $opdId }}">

  <div class="lke-table-wrap">
    <table class="lke-table">
      <thead>
        <tr>
          <th style="width:44px;">No</th>
          <th style="text-align:left;min-width:260px;">Komponen / Sub / Kriteria</th>
          <th style="width:68px;">Bobot</th>
          <th style="width:110px;">Nilai</th>
          <th style="width:240px;">Jawaban (Predikat)</th>
          <th style="width:160px;">Catatan</th>
          <th style="width:160px;">Komentar Evaluator</th>
          <th style="width:200px;">Evidence &amp; Dokumen</th>
        </tr>
      </thead>
      <tbody>

      @foreach($komponenUtama as $k)
      @php $nilaiK = $nilaiKomponenUtama[$k->id] ?? 0; @endphp

      <tr class="row-komponen">
        <td style="text-align:center;">
          <span style="font-size:16px;font-weight:800;color:#93c5fd;">{{ $k->urutan }}</span>
        </td>
        <td>
          <div style="font-size:14px;font-weight:800;color:var(--t1);">{{ $k->nama }}</div>
          <div style="font-size:10px;color:#93c5fd;margin-top:3px;">Kode: {{ $k->kode }}</div>
        <td>
        <td style="text-align:center;">
          <span style="font-size:15px;font-weight:800;color:#93c5fd;">{{ $k->bobot }}</span>
        </td>
        <td style="text-align:center;">
          <span id="total_komponen_{{ $k->id }}"
                style="font-size:18px;font-weight:900;color:#60a5fa;">
            {{ number_format($nilaiK, 2) }}
          </span>
        </td>
        <td colspan="4" style="color:rgba(255,255,255,.25);text-align:center;font-size:11px;">—</td>
      </tr>

      @foreach($subKomponen->where('parent_id', $k->id) as $s)
      @php
        $penSub    = $nilaiSubKomponen[$s->id] ?? null;
        $nilaiS    = $nilaiPerSubKomponen[$s->id] ?? 0;
        $krList    = $kriteriaPerKomponen[$s->id] ?? collect();
        $jawabanS  = $penSub?->jawaban ?? '';
        $warnaJwb  = $jawabanS ? ($warnaMap[$jawabanS] ?? '#71717a') : '#71717a';
        $subKode   = preg_replace('/[^a-zA-Z]/', '', $s->kode);
      @endphp

      <tr class="row-sub">
        <td style="text-align:center;">
          <div class="sub-nomor">{{ $k->urutan }}{{ $subKode }}</div>
        </td>
        <td>
          <div style="font-size:13px;font-weight:700;color:var(--t1);">{{ $s->nama }}</div>
          <div style="font-size:10px;color:var(--t3);margin-top:3px;">{{ $krList->count() }} kriteria</div>
        </td>
        <td style="text-align:center;font-weight:700;color:#a5b4fc;">{{ $s->bobot }}</td>

        {{-- NILAI (Readonly, otomatis terisi, TAMPAK JELAS) --}}
        <td style="text-align:center;">
          @if($isAdmin || session('user.role') === 'evaluator')
            <input type="number"
                   name="nilai[{{ $s->id }}]"
                   id="nilai_{{ $s->id }}"
                   value="{{ $nilaiS > 0 ? number_format($nilaiS, 2) : '' }}"
                   step="0.01"
                   min="0"
                   max="{{ $s->bobot }}"
                   class="input-nilai"
                   data-komponen="{{ $k->id }}"
                   data-bobot="{{ $s->bobot }}"
                   readonly
                   style="background: rgba(255,255,255,.08); text-align: center; font-weight: 800; font-size: 14px; width: 90px; color: #34d399;">
            <div style="font-size: 10px; color: #6ee7b7; margin-top: 3px;">
              Max: {{ $s->bobot }}
            </div>
          @else
            @if($nilaiS > 0)
              <div class="nilai-display">{{ number_format($nilaiS, 2) }}</div>
            @else
              <span class="nilai-dim">Belum Dinilai</span>
            @endif
            <input type="hidden" name="nilai[{{ $s->id }}]" value="{{ $nilaiS }}">
          @endif
        </td>

        {{-- JAWABAN (Dropdown Predikat) --}}
        <td style="text-align:center;">
          @if($isAdmin || session('user.role') === 'evaluator')
            <select name="jawaban[{{ $s->id }}]"
                    class="jawaban-select"
                    data-sub="{{ $s->id }}"
                    data-komponen="{{ $k->id }}"
                    data-bobot="{{ $s->bobot }}"
                    style="width: 220px; padding: 8px 10px; border-radius: 8px; background: rgba(255,255,255,.06); color: #fff; border: 1px solid rgba(255,255,255,.12); font-size: 12px;"
                    onchange="updateNilaiDariJawaban(this)">
              <option value="">-- Pilih Predikat --</option>
              <option value="AA" {{ $jawabanS == 'AA' ? 'selected' : '' }}>AA </option>
              <option value="A" {{ $jawabanS == 'A' ? 'selected' : ''   }}>A </option>
              <option value="BB" {{ $jawabanS == 'BB' ? 'selected' : '' }}>BB </option>
              <option value="B" {{ $jawabanS == 'B' ? 'selected' : ''   }}>B </option>
              <option value="CC" {{ $jawabanS == 'CC' ? 'selected' : '' }}>CC </option>
              <option value="C" {{ $jawabanS == 'C' ? 'selected' : ''   }}>C </option>
              <option value="D" {{ $jawabanS == 'D' ? 'selected' : ''   }}>D </option>
              <option value="E" {{ $jawabanS == 'E' ? 'selected' : ''   }}>E </option>
            </select>
            <div style="font-size: 10px; color: #94a3b8; margin-top: 4px;">
              Bobot: {{ $s->bobot }}
            </div>
          @else
            <span style="font-size:14px;font-weight:800;color:{{ $warnaJwb }};">
              {{ $jawabanS ?: '—' }}
            </span>
            <input type="hidden" name="jawaban[{{ $s->id }}]" value="{{ $jawabanS }}">
          @endif
        </td>

        {{-- CATATAN SUB --}}
        <td>
          @if($isAdmin)
            <textarea name="catatan_sub[{{ $s->id }}]" rows="2" class="dk-textarea"
                      placeholder="Catatan evaluasi...">{{ $penSub?->catatan ?? '' }}</textarea>
          @else
            @if($penSub?->catatan)
              <div class="catatan-admin">📝 {{ $penSub->catatan }}</div>
            @else
              <span style="font-size:11px;color:var(--t4);font-style:italic;">—</span>
            @endif
          @endif
        </td>

        <td style="text-align:center;font-size:10px;color:var(--t4);">(per kriteria)</td>
        <td style="text-align:center;font-size:10px;color:var(--t4);">(per kriteria)</td>
      </tr>

      @foreach($krList as $krIdx => $kr)
      @php
        $cat  = $catatanPerKriteria[$kr->id] ?? null;
        $docs = $dokumenPerKriteria[$kr->id] ?? [];
      @endphp

      <tr class="row-kriteria">
        <td style="text-align:center;vertical-align:top;padding-top:13px;">
          <div class="kr-nomor">{{ $kr->nomor }}</div>
        </td>
        <td style="vertical-align:top;">
          <div style="font-size:12px;color:var(--t2);line-height:1.55;">{{ $kr->uraian }}</div>
        </td>
        <td colspan="3" style="text-align:center;color:var(--t4);font-size:11px;">—</td>

        <td style="vertical-align:top;">
          @if(!$isAdmin)
            <textarea name="catatan_operator[{{ $kr->id }}]" rows="2" class="dk-textarea"
                      placeholder="Catatan unit/OPD...">{{ $cat?->catatan_operator ?? '' }}</textarea>
          @else
            @if($cat?->catatan_operator)
              <div class="catatan-opd">{{ $cat->catatan_operator }}</div>
            @else
              <span style="font-size:11px;color:var(--t4);font-style:italic;">—</span>
            @endif
          @endif
        </td>

        <td style="vertical-align:top;">
         @if($isAdmin || session('user.role') === 'evaluator')
         <textarea name="komentar_admin[{{ $kr->id }}]" rows="2" class="dk-textarea"
            placeholder="Komentar evaluator...">{{ $cat?->komentar_admin ?? '' }}</textarea>
          @else
            @if($cat?->komentar_admin)
              <div class="catatan-admin">💬 {{ $cat->komentar_admin }}</div>
            @else
              <span style="font-size:11px;color:var(--t4);font-style:italic;">Belum ada komentar</span>
            @endif
          @endif
        </td>

               <td style="vertical-align:top;">
          <div id="evidence_{{ $kr->id }}_display" style="{{ $cat?->daftar_evidence ? '' : 'display:none;' }}">
            <div style="font-size:11px;line-height:1.6;color:var(--t2);background:rgba(255,255,255,.03);
                        border:1px solid rgba(255,255,255,.07);border-radius:8px;padding:8px 10px;
                        margin-bottom:6px;white-space:pre-line;">
              {!! \App\Helpers\TextHelper::linkify($cat->daftar_evidence ?? '') !!}
            </div>
            <button type="button" class="btn btn-ghost" style="font-size:10px;padding:3px 8px;margin-bottom:6px;"
                    onclick="toggleEvidenceEdit({{ $kr->id }})">
              ✏️ Edit
            </button>
          </div>

          <div id="evidence_{{ $kr->id }}_edit" style="{{ $cat?->daftar_evidence ? 'display:none;' : '' }}">
            <textarea name="daftar_evidence[{{ $kr->id }}]"
                      rows="2" class="dk-textarea" style="margin-bottom:6px;"
                      placeholder="- RPJMD 2021-2026&#10;- Renstra Dinas">{{ $cat?->daftar_evidence ?? '' }}</textarea>
          </div>

          @if(!$isAdmin)
          <div class="upload-row">
            <input type="file" id="file_kr_{{ $kr->id }}"
                   accept=".pdf,.docx,.xlsx,.doc,.xls,.jpg,.jpeg,.png">
            <button type="button" class="btn btn-upload"
                    onclick="ajaxUpload({{ $kr->id }}, {{ $tahun }}, {{ $opdId }}, this)">
              📤
            </button>
          </div>
          @endif

          <div id="docs_kr_{{ $kr->id }}">
            @if(count($docs) > 0)
              @foreach($docs as $doc)
              <div class="doc-item" id="doc_item_{{ $doc->id }}">
                <span style="font-size:12px;">📄</span>
                <span class="doc-name" title="{{ $doc->nama_file }}">{{ $doc->nama_file }}</span>
                <a href="{{ route('evaluasi.lke.lihat.dokumen', $doc->id) }}"
                   target="_blank" class="btn btn-lihat">👁</a>
                @if(!$isAdmin)
                <button type="button" class="btn btn-del"
                        onclick="ajaxHapus({{ $doc->id }}, this)">🗑</button>
                @endif
              </div>
              @endforeach
            @else
              <div id="no_doc_{{ $kr->id }}"
                   style="padding:5px 8px;font-size:10.5px;color:var(--t4);font-style:italic;
                          background:rgba(255,255,255,.02);border-radius:6px;margin-top:5px;text-align:center;">
                Belum ada dokumen
              </div>
            @endif
          </div>
        </td>
      </tr>
      @endforeach
      <tr class="row-total">
        <td colspan="3" style="text-align:right;padding-right:16px;">
          Total — {{ $k->nama }}
        </td>
        <td style="text-align:center;">
          <span id="total_bawah_{{ $k->id }}"
                style="font-size:18px;font-weight:900;">
            {{ number_format($nilaiKomponenUtama[$k->id] ?? 0, 2) }}
          </span>
        </td>
        <td colspan="4" style="color:var(--t4);">—</td>
      </tr>
      <tr class="row-spacer"><td colspan="8"></td></tr>

      @endforeach
      @endforeach

      <tr class="row-grand">
        <td colspan="3" style="text-align:right;padding-right:16px;
                                font-size:14px;font-weight:800;color:var(--t1);">
          NILAI AKHIR LKE AKIP
        </td>
        <td style="text-align:center;">
          <span id="total_akhir"
                style="font-size:26px;font-weight:900;color:#fff;">
            {{ number_format($nilaiAkhir, 2) }}
          </span>
        </td>
        <td colspan="4" style="text-align:center;">
          @if($nilaiAkhir > 0)
          <span id="predikat_badge"
                style="font-size:15px;font-weight:800;
                       background:{{ $predikat['color'] }};
                       padding:6px 18px;border-radius:40px;display:inline-block;color:#fff;">
            {{ $predikat['kode'] }} — {{ $predikat['label'] }}
          </span>
          @endif
        </td>
      </tr>

      </tbody>
    </table>
  </div>

  <div class="sticky-footer">
    <div style="font-size:13px;color:var(--t2);">
      📋 <strong style="color:var(--t1);">{{ $namaOpd }}</strong>
      &nbsp;·&nbsp; Tahun <strong style="color:var(--t1);">{{ $tahun }}</strong>
      @if($nilaiAkhir > 0)
      &nbsp;·&nbsp;
      <span id="footer_nilai"
            style="font-weight:700;color:{{ $predikat['color'] }};">
        Nilai: {{ number_format($nilaiAkhir,2) }} ({{ $predikat['kode'] }})
      </span>
      @endif
    </div>
    <div style="display:flex;gap:10px;">
      <a href="{{ route('evaluasi.lke.rekap') }}?tahun={{ $tahun }}" class="btn btn-ghost">
        📊 Rekap
      </a>
      <button type="submit" form="formLke" class="btn btn-ok"
              style="padding:10px 22px;font-size:13px;">
        💾 Simpan
      </button>
    </div>
  </div>

</form>
@endif

@endsection

@section('scripts')
<script>
var CSRF_TOKEN     = '{{ csrf_token() }}';
var ROUTE_UPLOAD   = '{{ route("evaluasi.lke.upload.dokumen") }}';
var ROUTE_LIHAT_B  = '{{ url("evaluasi/lke/dokumen") }}';
var ROUTE_HAPUS_B  = '{{ url("evaluasi/lke/dokumen") }}';

/* ── AJAX Upload ──────────────────────────────── */
function ajaxUpload(kriteriaId, tahun, opdId, btn) {
  var fi = document.getElementById('file_kr_' + kriteriaId);
  if (!fi || !fi.files.length) { alert('Pilih file terlebih dahulu.'); return; }

  var fd = new FormData();
  fd.append('_token',      CSRF_TOKEN);
  fd.append('kriteria_id', kriteriaId);
  fd.append('tahun',       tahun);
  fd.append('opd_id',      opdId);
  fd.append('file',        fi.files[0]);

  btn.disabled = true;
  btn.textContent = '⏳';

  fetch(ROUTE_UPLOAD, { method:'POST', body:fd })
    .then(function(r) { if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
    .then(function(data) {
      if (data.ok) {
        var noDoc = document.getElementById('no_doc_' + kriteriaId);
        if (noDoc) noDoc.remove();
        var container = document.getElementById('docs_kr_' + kriteriaId);
        var div = document.createElement('div');
        div.className = 'doc-item';
        div.id = 'doc_item_' + data.id;
        div.innerHTML =
          '<span style="font-size:12px;">📄</span>' +
          '<span class="doc-name" title="'+data.nama_file+'">'+data.nama_file+'</span>' +
          '<a href="'+ROUTE_LIHAT_B+'/'+data.id+'" target="_blank" class="btn btn-lihat">👁</a>' +
          '<button type="button" class="btn btn-del" onclick="ajaxHapus('+data.id+', this)">🗑</button>';
        container.appendChild(div);
        fi.value = '';
      } else {
        alert(data.message || 'Gagal mengupload.');
      }
    })
    .catch(function(e) { console.error(e); alert('Terjadi kesalahan saat mengupload.'); })
    .finally(function() { btn.disabled = false; btn.textContent = '📤'; });
}

function ajaxHapus(docId, btn) {
  if (!confirm('Hapus dokumen ini?')) return;
  btn.disabled = true;
  btn.textContent = '⏳';

  fetch(ROUTE_HAPUS_B+'/'+docId, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN':CSRF_TOKEN, 'Content-Type':'application/json', 'Accept':'application/json' }
  })
  .then(function(r) { if(!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
  .then(function(data) {
    if (data.ok) {
      var el = document.getElementById('doc_item_'+docId);
      if (el) el.remove();
    } else {
      alert('Gagal menghapus.');
      btn.disabled = false; btn.textContent = '🗑';
    }
  })
  .catch(function(e) {
    console.error(e); alert('Terjadi kesalahan.');
    btn.disabled = false; btn.textContent = '🗑';
  });
}

function toggleEvidenceEdit(krId) {
  document.getElementById('evidence_' + krId + '_display').style.display = 'none';
  var editBox = document.getElementById('evidence_' + krId + '_edit');
  editBox.style.display = 'block';
  var ta = editBox.querySelector('textarea');
  ta.focus();
  autoGrow(ta);
}
/* ── Auto-resize textarea ─────────────────────── */
function autoGrow(el) {
  el.style.height = 'auto';
  el.style.height = el.scrollHeight + 'px';
}

document.addEventListener('DOMContentLoaded', function () {
  // Set tinggi awal sesuai isi yang sudah ada (termasuk data lama)
  document.querySelectorAll('.dk-textarea').forEach(function (el) {
    autoGrow(el);
  });
});

// Auto-resize setiap kali user mengetik, termasuk textarea yang baru
// dimunculkan lewat tombol Edit (event delegation, bukan per-elemen)
document.addEventListener('input', function (e) {
  if (e.target.classList.contains('dk-textarea')) {
    autoGrow(e.target);
  }
});

function getPersentaseDariJawaban(jawaban) {
  const mapping = { 'AA': 100, 'A': 90, 'BB': 80, 'B': 70, 'CC': 60, 'C': 50, 'D': 30, 'E': 0 };
  return mapping[jawaban] || 0;
}

function hitungNilai(persentase, bobot) {
  return (persentase * bobot) / 100;
}

function updateNilaiDariJawaban(selectElement) {
  var jawaban = selectElement.value;
  var subId = selectElement.dataset.sub;
  var bobot = parseFloat(selectElement.dataset.bobot) || 0;
  var nilaiInput = document.getElementById('nilai_' + subId);
  
  if (!nilaiInput) return;
  
  if (jawaban) {
    var persentase = getPersentaseDariJawaban(jawaban);
    var nilai = hitungNilai(persentase, bobot);
    nilai = Math.round(nilai * 100) / 100;
    nilaiInput.value = nilai;
  } else {
    nilaiInput.value = '';
  }
  
  updateAllTotals();
}

function getPredikat(n) {
  if (n >= 90) return { kode:'AA', label:'Sangat Memuaskan', color:'#059669' };
  if (n >= 80) return { kode:'A',  label:'Memuaskan',        color:'#2563eb' };
  if (n >= 70) return { kode:'BB', label:'Sangat Baik',      color:'#7c3aed' };
  if (n >= 60) return { kode:'B',  label:'Baik',             color:'#d4982e' };
  if (n >= 50) return { kode:'CC', label:'Cukup Baik',       color:'#f59e0b' };
  if (n >= 30) return { kode:'C',  label:'Kurang',           color:'#dc2626' };
  if (n  > 0)  return { kode:'D',  label:'Sangat Kurang',    color:'#991b1b' };
  return              { kode:'E',  label:'Tidak Ada Upaya',  color:'#6b7280' };
}

function updateAllTotals() {
  var komMap = {}, total = 0;

  document.querySelectorAll('input[name^="nilai["]').forEach(function(inp) {
    var m = inp.getAttribute('name').match(/\[(\d+)\]/);
    if (!m) return;
    var v = parseFloat(inp.value) || 0;
    var k = inp.dataset.komponen || '0';
    komMap[k] = (komMap[k] || 0) + v;
    total += v;
  });

  Object.keys(komMap).forEach(function(k) {
    var eh = document.getElementById('total_komponen_' + k);
    if (eh) eh.textContent = komMap[k].toFixed(2);
    var eb = document.getElementById('total_bawah_' + k);
    if (eb) eb.textContent = komMap[k].toFixed(2);
  });

  var ea = document.getElementById('total_akhir');
  if (ea) ea.textContent = total.toFixed(2);

  var ef = document.getElementById('footer_nilai');
  if (ef) ef.textContent = 'Nilai: ' + total.toFixed(2);

  var eb2 = document.getElementById('predikat_badge');
  if (eb2) {
    var p = getPredikat(total);
    eb2.style.background = p.color;
    eb2.textContent = p.kode + ' — ' + p.label;
    if (ef) ef.style.color = p.color;
  }
}

document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('select[name^="jawaban["]').forEach(function(select) {
    select.addEventListener('change', function() {
      updateNilaiDariJawaban(this);
    });
  });
  updateAllTotals();
});
</script>
@endsection