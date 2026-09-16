@extends('layouts.app')
@section('title', 'LKE AKIP')
@section('page-title', 'Evaluasi Kinerja')
@section('page-sub', 'Lembar Kerja Evaluasi AKIP')

@php
  $userRole    = session('user.role');
  $isAdmin     = $userRole === 'admin';
  $isEvaluator = $userRole === 'evaluator';
  $isOperator  = $userRole === 'operator';
  $defaultTab  = ($isOperator || $isEvaluator) ? 'operator' : 'evaluator';
@endphp

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

  {{-- 🆕 TOMBOL TOGGLE LEMBAR KERJA --}}
  <button type="button"
          id="btnToggleLembar"
          onclick="toggleLembarKerja()"
          style="display:inline-flex;align-items:center;gap:7px;
                 background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.35);
                 padding:8px 16px;border-radius:10px;color:#6ee7b7;
                 font-weight:600;font-size:12px;text-decoration:none;
                 cursor:pointer;font-family:inherit;transition:all .2s;"
          onmouseover="this.style.background='rgba(16,185,129,.25)'"
          onmouseout="this.style.background='rgba(16,185,129,.15)'">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z"/>
    </svg>
    <span id="btnToggleLembarLabel">
      {{ $isOperator ? 'Lihat Lembar Verifikator' : ($isEvaluator ? 'Lihat Lembar Evaluator' : 'Lihat Lembar Operator') }}
    </span>
  </button>

  @if($isAdmin)
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

/* ═══════════════════════════════════════════════════════
   TAB LEMBAR KERJA (Operator / Evaluator)
═══════════════════════════════════════════════════════ */
.lke-tab-bar {
  display: flex;
  gap: 8px;
  margin: 22px 0 0;
  padding: 0 4px;
  border-bottom: 1px solid var(--border);
  animation: fadeUp .3s ease;
}

.lke-tab-btn {
  padding: 11px 22px;
  border-radius: 12px 12px 0 0;
  border: 1px solid transparent;
  border-bottom: none;
  background: rgba(255,255,255,.03);
  color: var(--t3);
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  font-family: inherit;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all .18s ease;
  position: relative;
  bottom: -1px;
}
.lke-tab-btn:hover {
  background: rgba(255,255,255,.07);
  color: var(--t1);
}
.lke-tab-btn.active {
  background: rgba(99,102,241,.12);
  color: #a5b4fc;
  border-color: rgba(99,102,241,.35);
  border-bottom: 1px solid var(--c1);
}
.lke-tab-btn .tab-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: currentColor;
  opacity: .7;
}

.lke-tab-panel {
  display: none;
  animation: fadeUp .28s ease both;
}
.lke-tab-panel.active {
  display: block;
}

.btn-topbar-active {
  background: rgba(99,102,241,.25) !important;
  border-color: rgba(99,102,241,.5) !important;
  color: #c7d2fe !important;
}
</style>

{{-- ═══════════════════════════════════════════════════════
     FILTER OPD & TAHUN
═══════════════════════════════════════════════════════ --}}
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
          @if($isAdmin || $isEvaluator)
            <select class="dk-select" name="opd_id" required>
              <option value="">— Pilih OPD —</option>
              @foreach($listOpd as $o)
                <option value="{{ $o->id }}" {{ $opdId == $o->id ? 'selected' : '' }}>
                  {{ $o->nama }}
                </option>
              @endforeach
            </select>
            @if($isEvaluator && $listOpd->isEmpty())
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
  $namaOpd  = $listOpd->firstWhere('id', $opdId)?->nama ?? session('user.nama_daerah') ?? '';
  $warnaMap = [
    'AA'=>'#059669','A'=>'#2563eb','BB'=>'#7c3aed','B'=>'#d4982e',
    'CC'=>'#f59e0b','C'=>'#dc2626','D'=>'#991b1b','E'=>'#6b7280',
  ];
@endphp

@if($isOperator)
<div class="dk-alert info">
  <span>📝 Ini adalah <strong>lembar kerja Anda sendiri</strong>. Isi Jawaban (Predikat), Catatan, Evidence, dan upload dokumen pendukung. Nilai ini akan direview oleh Evaluator untuk ditetapkan sebagai nilai resmi.</span>
</div>
@elseif($isEvaluator)
<div class="dk-alert info">
  <span>📊 Anda sebagai <strong>Evaluator</strong> mengisi nilai resmi. Gunakan tombol di atas untuk berpindah antara lembar Operator dan lembar Evaluator.</span>
</div>
@else
<div class="dk-alert warn">
  <span>👁️ Anda sebagai <strong>Admin</strong> hanya dapat <strong>melihat</strong> kedua lembar kerja di bawah — tidak dapat mengubah nilai.</span>
</div>
@endif

<form method="POST" action="{{ route('evaluasi.lke.simpan') }}" id="formLke">
  @csrf
  <input type="hidden" name="tahun"  value="{{ $tahun }}">
  <input type="hidden" name="opd_id" value="{{ $opdId }}">

  {{-- ═══════════════════════════════════════════════════ --}}
  {{-- PANEL 1: LEMBAR EVALUATOR                          --}}
  {{-- ═══════════════════════════════════════════════════ --}}
  <div class="lke-tab-panel {{ $defaultTab === 'evaluator' ? 'active' : '' }}" id="panelEvaluator">
    @include('lke.partials.tabel-nilai', [
        'mode'                => 'evaluator',
        'editable'            => $isEvaluator,
        'judulLembar'         => 'Lembar Kerja Evaluator (Nilai Resmi — dipakai di Rekap)',
        'nilaiPerSubX'        => $nilaiPerSubKomponen,
        'nilaiKomponenUtamaX' => $nilaiKomponenUtama,
        'nilaiAkhirX'         => $nilaiAkhir,
    ])
  </div>

  {{-- ═══════════════════════════════════════════════════ --}}
  {{-- PANEL 2: LEMBAR OPERATOR                           --}}
  {{-- ═══════════════════════════════════════════════════ --}}
  <div class="lke-tab-panel {{ $defaultTab === 'operator' ? 'active' : '' }}" id="panelOperator">
    @include('lke.partials.tabel-nilai', [
        'mode'                => 'operator',
        'editable'            => $isOperator,
        'judulLembar'         => 'Lembar Kerja Operator (Nilai Asli)',
        'nilaiPerSubX'        => $nilaiOperatorPerSubKomponen,
        'nilaiKomponenUtamaX' => $nilaiKomponenUtamaOperator,
        'nilaiAkhirX'         => $nilaiAkhirOperator,
    ])
  </div>

  <div class="sticky-footer">
    <div style="font-size:13px;color:var(--t2);">
      📋 <strong style="color:var(--t1);">{{ $namaOpd }}</strong>
      &nbsp;·&nbsp; Tahun <strong style="color:var(--t1);">{{ $tahun }}</strong>
      @if($nilaiAkhir > 0)
      &nbsp;·&nbsp;
      <span id="footer_nilai" style="font-weight:700;color:{{ $predikat['color'] ?? '#a1a1aa' }};">
        Nilai Resmi: {{ number_format($nilaiAkhir,2) }} ({{ $predikat['kode'] ?? '-' }})
      </span>
      @endif
    </div>
    <div style="display:flex;gap:10px;">
      <a href="{{ route('evaluasi.lke.rekap') }}?tahun={{ $tahun }}" class="btn btn-ghost">📊 Rekap</a>
      @if($isOperator || $isEvaluator)
      <button type="submit" form="formLke" class="btn btn-ok" style="padding:10px 22px;font-size:13px;">💾 Simpan</button>
      @endif
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
var USER_ROLE      = '{{ $userRole }}';
var DEFAULT_TAB    = '{{ $defaultTab }}';
var STORAGE_KEY    = 'lkeActiveTab_' + USER_ROLE; // per-role biar gak bentrok

/* ══════════════════════════════════════════════════════════
   TAB SWITCHER — Lembar Operator / Lembar Evaluator
══════════════════════════════════════════════════════════ */
function switchLkeTab(mode) {
  var panelEval = document.getElementById('panelEvaluator');
  var panelOp   = document.getElementById('panelOperator');
  var btnEval   = document.getElementById('tabBtnEvaluator');
  var btnOp     = document.getElementById('tabBtnOperator');
  var toggleLbl = document.getElementById('btnToggleLembarLabel');

  if (!panelEval || !panelOp) return;

  if (mode === 'operator') {
    panelEval.classList.remove('active');
    panelOp.classList.add('active');
    if (btnEval) btnEval.classList.remove('active');
    if (btnOp)   btnOp.classList.add('active');
if (toggleLbl) {
  toggleLbl.textContent = (USER_ROLE === 'operator')
    ? 'Lihat Lembar Verifikator'
    : 'Lihat Lembar Evaluator';
}
  } else {
    panelEval.classList.add('active');
    panelOp.classList.remove('active');
    if (btnEval) btnEval.classList.add('active');
    if (btnOp)   btnOp.classList.remove('active');
    if (toggleLbl) toggleLbl.textContent = 'Lihat Lembar Evaluator';
  }

  // simpan preferensi di localStorage (per-role)
  try { localStorage.setItem(STORAGE_KEY, mode); } catch(e) {}

  // scroll ke atas tabel biar nyaman
  var tabBar = document.querySelector('.lke-tab-bar');
  if (tabBar) {
    var y = tabBar.getBoundingClientRect().top + window.pageYOffset - 80;
    window.scrollTo({ top: y, behavior: 'smooth' });
  }

  // re-grow textarea di panel yang baru aktif (hidden panel punya scrollHeight = 0)
  setTimeout(function() {
    var activePanel = document.querySelector('.lke-tab-panel.active');
    if (activePanel) {
      activePanel.querySelectorAll('.dk-textarea').forEach(function(el) {
        autoGrow(el);
      });
    }
  }, 50);
} // ⚠️ INI YANG HILANG — kurung penutup fungsi switchLkeTab

/* Toggle cepat dari tombol topbar */
function toggleLembarKerja() {
  var panelOp = document.getElementById('panelOperator');
  if (!panelOp) return;
  var isOperatorActive = panelOp.classList.contains('active');
  switchLkeTab(isOperatorActive ? 'evaluator' : 'operator');
}
function toggleLembarKerja() {
  var panelOp = document.getElementById('panelOperator');
  if (!panelOp) return;
  var isOperatorActive = panelOp.classList.contains('active');
  switchLkeTab(isOperatorActive ? 'evaluator' : 'operator');
}

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
  document.querySelectorAll('.dk-textarea').forEach(function (el) {
    autoGrow(el);
  });
});

document.addEventListener('input', function (e) {
  if (e.target.classList.contains('dk-textarea')) {
    autoGrow(e.target);
  }
});

/* ── Hitung Nilai dari Jawaban ────────────────── */
function getPersentaseDariJawaban(jawaban) {
  const mapping = { 'AA': 100, 'A': 90, 'BB': 80, 'B': 70, 'CC': 60, 'C': 50, 'D': 30, 'E': 0 };
  return mapping[jawaban] || 0;
}

function hitungNilai(persentase, bobot) {
  return (persentase * bobot) / 100;
}

function updateNilaiDariJawaban(selectElement) {
  var prefix = selectElement.dataset.prefix;
  var jawaban = selectElement.value;
  var subId = selectElement.dataset.sub;
  var bobot = parseFloat(selectElement.dataset.bobot) || 0;
  var nilaiInput = document.getElementById(prefix + '_nilai_' + subId);

  if (!nilaiInput) return;

  if (jawaban) {
    var persentase = getPersentaseDariJawaban(jawaban);
    var nilai = Math.round(((persentase * bobot) / 100) * 100) / 100;
    nilaiInput.value = nilai;
  } else {
    nilaiInput.value = '';
  }

  updateAllTotals(prefix);
}

document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('select[data-prefix]').forEach(function(select) {
    select.addEventListener('change', function() { updateNilaiDariJawaban(this); });
  });
});

/* ── Predikat ─────────────────────────────────── */
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

function updateAllTotals(prefix) {
  if (!prefix) {
    // panggil untuk dua prefix
    updateAllTotals('operator');
    updateAllTotals('evaluator');
    return;
  }

  var komMap = {}, total = 0;

  document.querySelectorAll('input[id^="' + prefix + '_nilai_"]').forEach(function(inp) {
    var m = inp.id.match(/_nilai_(\d+)/);
    if (!m) return;
    var v = parseFloat(inp.value) || 0;
    var selectEl = document.getElementById(prefix + '_jawaban_' + m[1]);
    var k = selectEl ? selectEl.dataset.komponen : '0';
    komMap[k] = (komMap[k] || 0) + v;
    total += v;
  });

  Object.keys(komMap).forEach(function(k) {
    var eh = document.getElementById(prefix + '_total_komponen_' + k);
    if (eh) eh.textContent = komMap[k].toFixed(2);
    var eb = document.getElementById(prefix + '_total_bawah_' + k);
    if (eb) eb.textContent = komMap[k].toFixed(2);
  });

  var ea = document.getElementById(prefix + '_total_akhir');
  if (ea) ea.textContent = total.toFixed(2);

  if (prefix === 'evaluator') {
    var ef = document.getElementById('footer_nilai');
    if (ef) {
      var p = getPredikat(total);
      ef.textContent = 'Nilai Resmi: ' + total.toFixed(2) + ' (' + p.kode + ')';
      ef.style.color = p.color;
    }
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

/* ── Set tab default sesuai role saat halaman dibuka ── */
document.addEventListener('DOMContentLoaded', function() {
  var saved = null;
  try { saved = localStorage.getItem(STORAGE_KEY); } catch(e) {}

  // pakai saved kalau valid, kalau tidak pakai default role
  if (saved === 'operator' || saved === 'evaluator') {
    switchLkeTab(saved);
  } else {
    switchLkeTab(DEFAULT_TAB);
  }
});
</script>
@endsection