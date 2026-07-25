{{--
  ╔══════════════════════════════════════════════════════════════════╗
  ║  ALUR PERHITUNGAN — SAMA PERSIS DENGAN LKE                      ║
  ║                                                                  ║
  ║  RUMUS:                                                          ║
  ║    nilaiKomponenUtama[k] = Σ nilai_sub  (SUM langsung)         ║
  ║    nilaiAkhir            = Σ nilaiKomponenUtama                 ║
  ║    Jawaban sub           = nilai_sub / bobot_sub × 100 → kode   ║
  ║    (admin input nilai SUDAH dalam skala bobot, maks = bobot)    ║
  ╚══════════════════════════════════════════════════════════════════╝
--}}

@extends('layouts.app')
@section('title', $title . ' - ' . $subtitle)
@section('page-title', 'Klaster Evaluasi')
@section('page-sub', $title . ' - ' . $subtitle)

@section('topbar-actions')
  @if(session('user.role') === 'admin')
    <a href="{{ route('klaster.rekap') }}?tahun={{ $tahun }}&type={{ $type }}&level={{ $level }}" class="btn-rekap-glass">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M3 3v18h18M7 15l4-4 4 4 4-4"/>
      </svg>
      Rekap Klaster
    </a>
  @endif
@endsection
@section('content')

<style>
  :root {
    --primary: #4f46e5;
    --primary-light: #6366f1;
    --primary-soft: #eef2ff;
    --success: #10b981;
    --success-light: #d1fae5;
    --warning: #f59e0b;
    --warning-light: #fef3c7;
    --danger: #ef4444;
    --danger-light: #fee2e2;
    --info: #3b82f6;
    --info-light: #dbeafe;
    --bg-white: #ffffff;
    --bg-soft: #f9fafb;
    --text-dark: #1f2937;
    --text-gray: #6b7280;
    --text-light: #9ca3af;
    --border-light: #e5e7eb;
    --shadow-sm: 0 1px 2px 0 rgba(0,0,0,0.05);
    --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1),0 2px 4px -1px rgba(0,0,0,0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1),0 4px 6px -2px rgba(0,0,0,0.05);
  }

  @keyframes fadeInUp  { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
  @keyframes fadeInLeft{ from{opacity:0;transform:translateX(-20px)} to{opacity:1;transform:translateX(0)} }
  .animate-in { animation:fadeInUp 0.4s ease forwards }

  /* ── Filter ── */
  .filter-glass { background:var(--bg-white);backdrop-filter:blur(12px);border-radius:24px;border:1px solid var(--border-light);box-shadow:var(--shadow-sm);transition:all .3s;margin-bottom:24px;overflow:hidden }
  .filter-glass:hover { box-shadow:var(--shadow-md);transform:translateY(-2px) }
  .filter-header { background:linear-gradient(135deg,#f8fafc,#f1f5f9);padding:16px 24px;border-bottom:1px solid var(--border-light) }
  .filter-header .card-title { font-size:14px;font-weight:700;color:var(--text-dark);display:flex;align-items:center;gap:8px }
  .filter-body { padding:20px 24px }
  .filter-select-modern { border:2px solid var(--border-light);border-radius:14px;padding:10px 16px;transition:all .3s;background:white;width:100% }
  .filter-select-modern:focus { border-color:var(--primary);box-shadow:0 0 0 3px rgba(79,70,229,.1);outline:none }

  /* ── Buttons ── */
  .btn-primary-glass { background:linear-gradient(135deg,var(--primary),var(--primary-light));border:none;padding:10px 24px;border-radius:40px;color:white;font-weight:600;transition:all .3s;box-shadow:0 4px 12px rgba(79,70,229,.3);display:inline-flex;align-items:center;gap:8px }
  .btn-primary-glass:hover { transform:translateY(-2px);box-shadow:0 8px 20px rgba(79,70,229,.4) }
  .btn-rekap-glass { background:transparent;border:2px solid var(--border-light);padding:8px 20px;border-radius:40px;color:var(--text-gray);font-weight:600;transition:all .3s;display:inline-flex;align-items:center;gap:8px;text-decoration:none }
  .btn-rekap-glass:hover { background:linear-gradient(135deg,var(--primary),var(--primary-light));border-color:transparent;color:white;transform:translateY(-2px);text-decoration:none }
  .btn-save-glass { background:linear-gradient(135deg,var(--success),#059669);border:none;padding:12px 32px;border-radius:40px;color:white;font-weight:700;font-size:14px;transition:all .3s;box-shadow:0 4px 12px rgba(16,185,129,.3);display:inline-flex;align-items:center;gap:8px }
  .btn-save-glass:hover { transform:translateY(-2px);box-shadow:0 8px 24px rgba(16,185,129,.4) }
  .btn-outline-glass { background:transparent;border:2px solid var(--border-light);padding:10px 24px;border-radius:40px;color:var(--text-gray);font-weight:600;transition:all .3s;display:inline-flex;align-items:center;gap:8px;text-decoration:none }
  .btn-outline-glass:hover { background:linear-gradient(135deg,var(--primary),var(--primary-light));border-color:transparent;color:white;transform:translateY(-2px);text-decoration:none }

  /* ── Alert ── */
  .alert-glass { border-radius:20px;border:none;padding:16px 24px;margin-bottom:24px;backdrop-filter:blur(8px);animation:fadeInLeft .5s ease }
  .alert-glass.info    { background:var(--info-light);border-left:4px solid var(--info);color:#1e40af }
  .alert-glass.warning { background:var(--warning-light);border-left:4px solid var(--warning);color:#92400e }
  .alert-glass.success { background:var(--success-light);border-left:4px solid var(--success);color:#065f46 }
  .alert-glass.danger  { background:var(--danger-light);border-left:4px solid var(--danger);color:#991b1b }

  /* ── Table ── */
  .table-glass { border-radius:24px;overflow:hidden;box-shadow:var(--shadow-sm);transition:all .3s;background:white }
  .table-glass:hover { box-shadow:var(--shadow-md) }
  .table-glass table { width:100%;border-collapse:collapse }
  .table-glass th { background:linear-gradient(135deg,#1e293b,#0f172a);color:white;font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;padding:16px 12px;border-bottom:2px solid rgba(255,255,255,.1) }
  .table-glass td { padding:14px 12px;border-bottom:1px solid #f1f5f9;transition:background .2s }
  .table-glass tbody tr:hover td { background:#fefce8 }

  .komponen-header-glass { background:linear-gradient(135deg,#1e3a5f,#2563eb);position:relative;overflow:hidden }
  .komponen-header-glass td { color:white!important }
  .komponen-header-glass::after { content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,.2),transparent);transition:left .6s ease }
  .komponen-header-glass:hover::after { left:100% }

  /* ── Inputs ── */
  .input-number-modern { border:2px solid var(--border-light);border-radius:12px;padding:8px 12px;width:80px;text-align:center;font-weight:700;transition:all .3s;background:white }
  .input-number-modern:focus { border-color:var(--primary);box-shadow:0 0 0 3px rgba(79,70,229,.1);outline:none }
  .textarea-modern { border:2px solid var(--border-light);border-radius:12px;padding:8px 12px;font-size:12px;transition:all .3s;background:#fafcff;width:100%;resize:vertical }
  .textarea-modern:focus { border-color:var(--primary);box-shadow:0 0 0 3px rgba(79,70,229,.1);outline:none }

  /* ── Badges ── */
  .nilai-badge   { display:inline-block;padding:4px 12px;border-radius:30px;font-weight:700;font-size:13px;background:#f0fdf4;color:#15803d }
  .jawaban-live  { transition:background .25s,color .25s,border .25s }
  .bobot-komponen { font-size:18px;font-weight:800;background:rgba(255,255,255,.2);padding:4px 12px;border-radius:30px;display:inline-block }

  /* ── Misc ── */
  .sticky-footer-glass { position:sticky;bottom:20px;background:rgba(255,255,255,.98);backdrop-filter:blur(20px);border-radius:60px;margin-top:24px;padding:12px 28px;z-index:100;box-shadow:0 -4px 20px rgba(0,0,0,.08),0 8px 20px rgba(0,0,0,.1);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px }
  .sub-nomor { font-weight:800;font-size:14px;color:#1d4ed8;background:#e8f0fe;display:inline-block;width:40px;height:40px;line-height:40px;text-align:center;border-radius:12px }
  .kriteria-nomor { width:32px;height:32px;background:linear-gradient(135deg,#f0f7ff,#e6f0ff);border:1px solid #bfdbfe;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:#2563eb;margin:0 auto }
  .empty-state-glass { text-align:center;padding:48px;background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-radius:24px }
  .empty-state-glass .icon { font-size:48px;margin-bottom:16px;display:inline-block }

  ::-webkit-scrollbar { width:8px;height:8px }
  ::-webkit-scrollbar-track { background:#f1f5f9;border-radius:10px }
  ::-webkit-scrollbar-thumb { background:linear-gradient(135deg,var(--primary),var(--primary-light));border-radius:10px }
  
  /* Warning PDF */
  .pdf-warning {
    color: #dc2626;
    font-size: 10px;
    margin-top: 4px;
    display: none;
    align-items: center;
    gap: 4px;
  }
  .pdf-warning.show {
    display: flex;
  }
</style>

{{-- ═══════ FLASH MESSAGES ═══════ --}}
@if(session('success'))
  <div class="alert-glass success animate-in">
    <div style="display:flex;align-items:center;gap:12px">
      <span style="font-size:20px">✅</span>
      <span>{{ session('success') }}</span>
    </div>
  </div>
@endif
@if(session('error'))
  <div class="alert-glass danger animate-in">
    <div style="display:flex;align-items:center;gap:12px">
      <span style="font-size:20px">❌</span>
      <span>{{ session('error') }}</span>
    </div>
  </div>
@endif

{{-- ═══════ FILTER ═══════ --}}
<div class="filter-glass animate-in">
  <div class="filter-header">
    <div class="card-title"><span>🔍</span> Pilih OPD & Tahun — {{ $title }}</div>
  </div>
  <div class="filter-body">
    <form method="GET" action="{{ route('klaster.' . $type . '.' . $level) }}">
      <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:12px;align-items:end">

        @if(session('user.role') === 'admin')
          <div class="form-group" style="margin-bottom:0">
            <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-gray)">Perangkat Daerah</label>
            <select class="filter-select-modern" name="opd_id" required>
              <option value="">— Pilih OPD —</option>
              @foreach($listOpd as $o)
                <option value="{{ $o->id }}" {{ $opdId == $o->id ? 'selected' : '' }}>{{ $o->nama }}</option>
              @endforeach
            </select>
          </div>
        @else
          <div class="form-group" style="margin-bottom:0">
            <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-gray)">Perangkat Daerah</label>
            <input type="text" class="filter-select-modern" value="{{ session('user.nama_daerah') }}" disabled style="background:#f1f5f9">
          </div>
        @endif

        <div class="form-group" style="margin-bottom:0">
          <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-gray)">Tahun</label>
          <select class="filter-select-modern" name="tahun" style="width:120px">
            @foreach($listTahun as $t)
              <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
          </select>
        </div>

        <button type="submit" class="btn-primary-glass">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          Tampilkan
        </button>
      </div>
    </form>
  </div>
</div>

@if(!$opdId)
  <div class="empty-state-glass animate-in">
    <div class="icon">📊</div>
    <h3 style="font-weight:700;margin-bottom:8px">Pilih OPD terlebih dahulu</h3>
    <p style="color:var(--text-gray)">Gunakan filter di atas untuk memilih OPD yang akan dievaluasi.</p>
  </div>

@elseif($komponenUtama->isEmpty())
  <div class="empty-state-glass animate-in">
    <div class="icon">📭</div>
    <h3 style="font-weight:700;margin-bottom:8px">Data komponen belum tersedia</h3>
    <p style="color:var(--text-gray)">
      Belum ada data komponen untuk <strong>{{ $title }}</strong>.<br>
      Pastikan tabel <code>klaster_komponen</code> sudah diisi dengan
      <code>klaster_type = '{{ $type }}'</code> dan <code>klaster_level = {{ $level }}</code>.
    </p>
  </div>
@else

@php
  $isAdmin  = session('user.role') === 'admin';
  $namaOpd  = $listOpd->firstWhere('id', $opdId)->nama ?? '';
  $warnaMap = [
    'AA' => '#059669', 'A'  => '#2563eb', 'BB' => '#7c3aed',
    'B'  => '#d4982e', 'CC' => '#f59e0b', 'C'  => '#dc2626',
    'D'  => '#991b1b', 'E'  => '#6b7280',
  ];
@endphp

{{-- ═══════ INFO ALERT ═══════ --}}
@if(!$isAdmin)
  <div class="alert-glass info animate-in">
    <div style="display:flex;align-items:center;gap:12px">
      <span style="font-size:20px">ℹ️</span>
      <span>Isi <strong>Catatan</strong>, <strong>Daftar Evidence</strong>, dan upload <strong>dokumen pendukung (PDF)</strong> untuk setiap kriteria. Nilai akan diisi oleh Admin.</span>
    </div>
  </div>
@else
  <div class="alert-glass warning animate-in">
    <div style="display:flex;align-items:center;gap:12px">
      <span style="font-size:20px">✏️</span>
      <span>
        Isi kolom <strong>Nilai</strong> untuk setiap sub-komponen.
        Nilai maksimal = bobot sub-komponen (contoh: bobot 30 → isi maks 30).
        Kolom <strong>Jawaban</strong> dan <strong>Total</strong> otomatis terhitung.
      </span>
    </div>
  </div>
@endif

{{-- ═══════ FORM UTAMA ═══════ --}}
<form method="POST" action="{{ route('klaster.simpan') }}" id="formKlaster">
  @csrf
  {{-- Hidden: identitas klaster ini, agar controller bisa redirect kembali dengan benar --}}
  <input type="hidden" name="tahun"  value="{{ $tahun }}">
  <input type="hidden" name="opd_id" value="{{ $opdId }}">
  <input type="hidden" name="type"   value="{{ $type }}">
  <input type="hidden" name="level"  value="{{ $level }}">

  <div class="table-glass animate-in">
    <div style="overflow-x:auto">
      <table style="width:100%;border-collapse:collapse;min-width:1300px">

        <thead>
          <tr>
            <th style="width:5%;text-align:center">No</th>
            <th style="text-align:left">Komponen / Sub Komponen / Kriteria</th>
            <th style="width:8%;text-align:center">Bobot</th>
            <th style="width:10%;text-align:center">Nilai</th>
            <th style="width:10%;text-align:center">Jawaban</th>
            <th style="width:14%">Catatan</th>
            <th style="width:14%">Komentar Admin</th>
            <th style="width:19%">Daftar Evidence</th>
          </tr>
        </thead>

        <tbody>
        @foreach($komponenUtama as $k)
        @php $nilaiK = $nilaiKomponenUtama[$k->id] ?? 0; @endphp

        {{-- ── BARIS KOMPONEN UTAMA ── --}}
        <tr class="komponen-header-glass">
          <td style="text-align:center;font-weight:800;font-size:14px;padding:14px 12px">{{ $k->urutan }}</td>
          <td style="padding:14px 16px">
            <div style="font-size:15px;font-weight:800">{{ $k->nama }}</div>
            <div style="font-size:11px;opacity:.7;margin-top:2px">Kode: {{ $k->kode }}</div>
          </td>
          <td style="text-align:center;padding:14px 12px">
            <span class="bobot-komponen">{{ $k->bobot }}</span>
          </td>
          {{-- Total nilai komponen ini (diupdate JS) --}}
          <td style="text-align:center;padding:14px 12px">
            <span id="total_komponen_{{ $k->id }}"
                  class="nilai-badge"
                  style="background:rgba(255,255,255,.2);color:white;font-size:18px;padding:4px 12px">
              {{ number_format($nilaiK, 2) }}
            </span>
          </td>
          <td style="text-align:center;padding:14px 12px;color:rgba(255,255,255,.5)">—</td>
          <td colspan="3" style="padding:14px 12px;color:rgba(255,255,255,.5)">—</td>
        </tr>

        {{-- ── LOOP SUB KOMPONEN ── --}}
        @foreach($subKomponen->where('parent_id', $k->id) as $s)
        @php
          $penSub       = $nilaiSubKomponen[$s->id] ?? null;
          $nilaiSub     = $nilaiPerSubKomponen[$s->id] ?? 0;
          $kriteriaList = $kriteriaPerKomponen[$s->id] ?? collect();
          $jawabanSub   = $penSub ? ($penSub->jawaban ?? '') : '';
          $warnaJawaban = $jawabanSub ? ($warnaMap[$jawabanSub] ?? '#94a3b8') : '#94a3b8';
          $subKode      = preg_replace('/[^a-z]/i', '', $s->kode ?? '');
        @endphp

        <tr style="background:#fefce8;border-left:4px solid #2563eb">
          <td style="text-align:center;vertical-align:middle;padding:12px">
            <div class="sub-nomor">{{ $k->urutan }}{{ $subKode }}</div>
          </td>
          <td style="padding:12px 16px;vertical-align:middle">
            <div style="font-weight:700;color:#1e3a5f">{{ $s->nama }}</div>
            <div style="font-size:10px;color:#3b82f6;margin-top:4px">{{ $kriteriaList->count() }} kriteria</div>
          </td>

          {{-- Bobot sub --}}
          <td style="text-align:center;vertical-align:middle;padding:12px">
            <span style="font-weight:700;color:#1d4ed8;font-size:14px">{{ $s->bobot }}</span>
          </td>

          {{-- Kolom Nilai --}}
          <td style="text-align:center;vertical-align:middle;padding:12px">
            @if($isAdmin)
              <input
                type="number"
                name="nilai[{{ $s->id }}]"
                id="nilai_sub_{{ $s->id }}"
                value="{{ $nilaiSub > 0 ? $nilaiSub : '' }}"
                step="0.01" min="0" max="{{ $s->bobot }}"
                class="input-number-modern"
                data-sub="{{ $s->id }}"
                data-komponen="{{ $k->id }}"
                data-bobot="{{ $s->bobot }}"
                placeholder="0"
              >
            @else
              <span class="nilai-badge" style="background:{{ $warnaJawaban }}15;color:{{ $warnaJawaban }}">
                {{ $nilaiSub > 0 ? number_format($nilaiSub, 2) : '—' }}
              </span>
            @endif
          </td>

          {{-- Kolom Jawaban --}}
          <td style="text-align:center;vertical-align:middle;padding:12px">
            <span id="jawaban_sub_{{ $s->id }}"
                  class="jawaban-live"
                  style="font-size:16px;font-weight:900;
                         display:inline-block;padding:4px 14px;border-radius:30px;
                         background:{{ $jawabanSub ? $warnaMap[$jawabanSub].'20' : 'transparent' }};
                         color:{{ $warnaJawaban }};
                         border:{{ $jawabanSub ? '1px solid '.$warnaMap[$jawabanSub].'50' : 'none' }}">
              {{ $jawabanSub ?: '—' }}
            </span>
            <input type="hidden" name="jawaban[{{ $s->id }}]"
                   id="hidden_jawaban_{{ $s->id }}"
                   value="{{ $jawabanSub }}">
          </td>

          {{-- Catatan sub (Admin input) --}}
          <td style="vertical-align:middle;padding:12px">
            @if($isAdmin)
              <textarea name="catatan_sub[{{ $s->id }}]" rows="2" class="textarea-modern"
                        placeholder="Catatan evaluasi...">{{ $penSub->catatan ?? '' }}</textarea>
            @else
              @if($penSub && $penSub->catatan)
                <div style="font-size:12px;background:#fffbeb;border-left:3px solid #f59e0b;padding:6px 10px;border-radius:0 8px 8px 0">{{ $penSub->catatan }}</div>
              @else
                <span style="font-size:12px;color:#94a3b8;font-style:italic">—</span>
              @endif
            @endif
          </td>

          <td style="text-align:center;font-size:11px;color:#94a3b8;font-style:italic;padding:12px">(per kriteria)</td>
          <td style="text-align:center;font-size:11px;color:#94a3b8;font-style:italic;padding:12px">(per kriteria)</td>
        </tr>

        {{-- ── LOOP KRITERIA ── --}}
        @foreach($kriteriaList as $krIdx => $kr)
        @php
          $cat  = $catatanPerKriteria[$kr->id] ?? null;
          $docs = $dokumenPerKriteria[$kr->id] ?? [];
          $bgKr = $krIdx % 2 == 0 ? '#fff' : '#fafcff';
        @endphp

        <tr style="background:{{ $bgKr }};border-bottom:1px solid #f1f5f9">
          <td style="text-align:center;vertical-align:top;padding:12px">
            <div class="kriteria-nomor">{{ $kr->nomor }}</div>
          </td>
          <td style="vertical-align:top;padding:12px 16px">
            <div style="font-size:12px;color:#0f172a;line-height:1.5">{{ $kr->uraian }}</div>
          </td>
          <td colspan="2" style="text-align:center;color:#94a3b8;font-size:12px;padding:12px">—</td>

          {{-- Catatan Operator --}}
          <td style="vertical-align:top;padding:12px">
            @if(!$isAdmin)
              <textarea name="catatan_operator[{{ $kr->id }}]" rows="2" class="textarea-modern"
                        placeholder="Isi catatan unit/OPD...">{{ $cat->catatan_operator ?? '' }}</textarea>
            @else
              @if($cat && $cat->catatan_operator)
                <div style="font-size:12px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:8px 10px;color:#1e3a5f">{{ $cat->catatan_operator }}</div>
              @else
                <span style="font-size:12px;color:#94a3b8;font-style:italic">—</span>
              @endif
            @endif
          </td>

          {{-- Komentar Admin --}}
          <td style="vertical-align:top;padding:12px;{{ $isAdmin ? 'background:#fffbeb' : '' }}">
            @if($isAdmin)
              <textarea name="komentar_admin[{{ $kr->id }}]" rows="2" class="textarea-modern"
                        style="background:#fff;border-color:#fde68a"
                        placeholder="Komentar evaluator...">{{ $cat->komentar_admin ?? '' }}</textarea>
            @else
              @if($cat && $cat->komentar_admin)
                <div style="font-size:12px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:8px 10px;color:#78350f">{{ $cat->komentar_admin }}</div>
              @else
                <span style="font-size:12px;color:#94a3b8;font-style:italic">—</span>
              @endif
            @endif
          </td>

          {{-- Daftar Evidence + Upload (HANYA PDF) --}}
          <td style="vertical-align:top;padding:12px">
            @if(!$isAdmin)
              <textarea name="daftar_evidence[{{ $kr->id }}]" rows="2" class="textarea-modern"
                        style="margin-bottom:8px"
                        placeholder="Contoh:&#10;- RPJMD 2021-2026&#10;- Renstra Dinas">{{ $cat->daftar_evidence ?? '' }}</textarea>
              
              {{-- Form Upload dengan validasi PDF --}}
              <form method="POST" action="{{ route('klaster.upload.dokumen') }}"
                    enctype="multipart/form-data"
                    class="upload-form"
                    style="display:flex;gap:6px;align-items:center"
                    onsubmit="return validatePDFUpload(this)">
                @csrf
                <input type="hidden" name="kriteria_id" value="{{ $kr->id }}">
                <input type="hidden" name="tahun"       value="{{ $tahun }}">
                <input type="file" name="file"
                       class="pdf-input"
                       data-kriteria="{{ $kr->id }}"
                       style="flex:1;padding:6px;border:1px solid #e2e8f0;border-radius:8px;font-size:10px"
                       accept=".pdf"
                       required>
                <button type="submit" class="btn-outline-glass" style="padding:6px 12px;font-size:11px">📤 Upload PDF</button>
              </form>
              
              {{-- Warning untuk file non-PDF --}}
              <div class="pdf-warning" id="pdfWarning{{ $kr->id }}">
                <span>⚠️</span> Hanya file PDF yang diperbolehkan!
              </div>
              
              {{-- Daftar dokumen yang sudah diupload --}}
              @foreach($docs as $doc)
                <div style="display:flex;align-items:center;gap:6px;margin-top:6px;padding:6px 10px;background:#f0f9ff;border-radius:10px">
                  <span>📄</span>
                  <span style="font-size:11px;color:#0369a1;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $doc->nama_file }}">{{ $doc->nama_file }}</span>
                  <a href="{{ route('klaster.lihat.dokumen', $doc->id) }}" target="_blank"
                     style="background:#3b82f6;color:white;padding:2px 8px;border-radius:6px;text-decoration:none;font-size:10px">👁️</a>
                  <form method="POST" action="{{ route('klaster.hapus.dokumen', $doc->id) }}"
                        style="display:inline" onsubmit="return confirm('Hapus dokumen ini?')">
                    @csrf @method('DELETE')
                    <button style="background:#ef4444;color:white;padding:2px 8px;border-radius:6px;border:none;font-size:10px;cursor:pointer">🗑️</button>
                  </form>
                </div>
              @endforeach
            @else
              {{-- Admin view --}}
              @if($cat && $cat->daftar_evidence)
                <div style="font-size:11px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:8px 10px;margin-bottom:8px;white-space:pre-line">{{ $cat->daftar_evidence }}</div>
              @else
                <div style="font-size:11px;color:#94a3b8;font-style:italic;margin-bottom:8px">Belum ada evidence.</div>
              @endif
              @foreach($docs as $doc)
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;padding:4px 8px;background:#f0f9ff;border-radius:8px">
                  <span>📄</span>
                  <span style="font-size:10px;color:#0369a1;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $doc->nama_file }}</span>
                  <a href="{{ route('klaster.lihat.dokumen', $doc->id) }}" target="_blank"
                     style="background:#3b82f6;color:white;padding:2px 8px;border-radius:6px;text-decoration:none;font-size:10px">👁️ Lihat</a>
                </div>
              @endforeach
              @if(empty($docs) && !($cat && $cat->daftar_evidence))
                <span style="font-size:11px;color:#94a3b8;font-style:italic">Belum ada dokumen.</span>
              @endif
            @endif
          </td>
        </tr>
        @endforeach {{-- end kriteria --}}

        @endforeach {{-- end sub komponen --}}

        {{-- ── TOTAL BARIS KOMPONEN ── --}}
        <tr style="background:#f0fdf4;border-top:2px solid #bbf7d0">
          <td colspan="3" style="text-align:right;font-weight:700;color:#15803d;padding:12px 16px">
            Total — {{ $k->nama }}
          </td>
          <td style="text-align:center;padding:12px">
            <span id="total_komponen_{{ $k->id }}"
                  style="font-size:20px;font-weight:900;color:#15803d">
              {{ number_format($nilaiKomponenUtama[$k->id] ?? 0, 2) }}
            </span>
          </td>
          <td colspan="4" style="color:#94a3b8;padding:12px">—</td>
        </tr>
        <tr><td colspan="8" style="height:8px;background:#f8fafc"></td></tr>

        @endforeach {{-- end komponen utama --}}

        {{-- ── NILAI AKHIR ── --}}
        <tr style="background:linear-gradient(135deg,#1e293b,#0f172a);color:white">
          <td colspan="3" style="text-align:right;font-weight:800;font-size:16px;padding:16px">
            NILAI AKHIR — {{ strtoupper($title) }}
          </td>
          <td style="text-align:center;padding:16px">
            <span id="total_akhir" style="font-size:28px;font-weight:900">
              {{ number_format($nilaiAkhir, 2) }}
            </span>
          </td>
          <td colspan="4" style="text-align:center;padding:16px">
            @if($nilaiAkhir > 0)
              <span id="predikat_badge"
                    style="font-size:18px;font-weight:800;background:{{ $predikat['color'] }};
                           color:white;padding:6px 20px;border-radius:40px;display:inline-block">
                {{ $predikat['kode'] }} — {{ $predikat['label'] }}
              </span>
            @else
              <span id="predikat_badge"
                    style="font-size:14px;font-weight:600;color:rgba(255,255,255,.4)">
                Belum ada nilai
              </span>
            @endif
          </td>
        </table>

        </tbody>
      </table>
    </div>
  </div>

  {{-- ═══════ STICKY FOOTER ═══════ --}}
  <div class="sticky-footer-glass">
    <div style="font-size:13px;color:var(--text-gray)">
      📋 <strong>{{ $title }}</strong> — <strong>{{ $namaOpd }}</strong> — Tahun <strong>{{ $tahun }}</strong>
      @if($nilaiAkhir > 0)
        &nbsp;|&nbsp;
        <span id="footer_nilai" style="font-weight:700;color:{{ $predikat['color'] }}">
          Nilai: {{ number_format($nilaiAkhir, 2) }} ({{ $predikat['kode'] }})
        </span>
      @else
        <span id="footer_nilai" style="font-weight:600;color:#94a3b8">Nilai: 0.00</span>
      @endif
    </div>
    <div style="display:flex;gap:10px">
      @if($isAdmin)
        <a href="{{ route('klaster.rekap') }}?tahun={{ $tahun }}&type={{ $type }}&level={{ $level }}" class="btn-outline-glass">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 3v18h18M7 15l4-4 4 4 4-4"/>
          </svg>
          Rekap
        </a>
      @endif
      <button type="submit" form="formKlaster" class="btn-save-glass">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
          <polyline points="17 21 17 13 7 13 7 21"/>
          <polyline points="7 3 7 8 15 8"/>
        </svg>
        💾 {{ $isAdmin ? 'Simpan Penilaian' : 'Simpan' }}
      </button>
    </div>
  </div>

</form>
@endif
@endsection

@section('scripts')
<script>
// ═══════════════════════════════════════════════════════
//  PERHITUNGAN REAL-TIME — KLASTER EVALUASI
// ═══════════════════════════════════════════════════════

var WARNA = {
  'AA': '#059669', 'A' : '#2563eb', 'BB': '#7c3aed',
  'B' : '#d4982e', 'CC': '#f59e0b', 'C' : '#dc2626',
  'D' : '#991b1b', 'E' : '#6b7280'
};

function getJawabanDariPersen(persen) {
  if (persen >= 90) return 'AA';
  if (persen >= 80) return 'A';
  if (persen >= 70) return 'BB';
  if (persen >= 60) return 'B';
  if (persen >= 50) return 'CC';
  if (persen >= 30) return 'C';
  if (persen >   0) return 'D';
  return 'E';
}

function getPredikat(nilai) {
  if (nilai >= 90) return { kode: 'AA', label: 'Sangat Memuaskan', color: '#059669' };
  if (nilai >= 80) return { kode: 'A',  label: 'Memuaskan',        color: '#2563eb' };
  if (nilai >= 70) return { kode: 'BB', label: 'Sangat Baik',      color: '#7c3aed' };
  if (nilai >= 60) return { kode: 'B',  label: 'Baik',             color: '#d4982e' };
  if (nilai >= 50) return { kode: 'CC', label: 'Cukup Baik',       color: '#f59e0b' };
  if (nilai >= 30) return { kode: 'C',  label: 'Kurang',           color: '#dc2626' };
  if (nilai >   0) return { kode: 'D',  label: 'Sangat Kurang',    color: '#991b1b' };
  return                  { kode: 'E',  label: 'Tidak Ada Upaya',  color: '#6b7280' };
}

function renderJawaban(subId, nilai, bobot) {
  var span   = document.getElementById('jawaban_sub_' + subId);
  var hidden = document.getElementById('hidden_jawaban_' + subId);
  if (!span) return;

  if (nilai <= 0 || bobot <= 0) {
    span.textContent         = '—';
    span.style.background    = 'transparent';
    span.style.color         = '#94a3b8';
    span.style.border        = 'none';
    if (hidden) hidden.value = '';
    return;
  }

  var persen  = (nilai / bobot) * 100;
  var jawaban = getJawabanDariPersen(persen);
  var warna   = WARNA[jawaban] || '#94a3b8';

  span.textContent         = jawaban;
  span.style.background    = warna + '20';
  span.style.color         = warna;
  span.style.border        = '1px solid ' + warna + '50';
  if (hidden) hidden.value = jawaban;
}

function updateAllTotals() {
  var komponenMap = {};
  var totalAkhir  = 0;

  document.querySelectorAll('input[name^="nilai["]').forEach(function (input) {
    var nilai  = parseFloat(input.value) || 0;
    var subId  = input.dataset.sub      || '';
    var kompId = input.dataset.komponen || '0';
    var bobot  = parseFloat(input.dataset.bobot) || 0;

    if (subId) renderJawaban(subId, nilai, bobot);

    komponenMap[kompId] = (komponenMap[kompId] || 0) + nilai;
    totalAkhir += nilai;
  });

  Object.keys(komponenMap).forEach(function (kompId) {
    document.querySelectorAll('[id="total_komponen_' + kompId + '"]').forEach(function (el) {
      el.textContent = komponenMap[kompId].toFixed(2);
    });
  });

  var elAkhir = document.getElementById('total_akhir');
  if (elAkhir) elAkhir.textContent = totalAkhir.toFixed(2);

  var elFooter = document.getElementById('footer_nilai');
  if (elFooter) {
    if (totalAkhir > 0) {
      var p = getPredikat(totalAkhir);
      elFooter.textContent = 'Nilai: ' + totalAkhir.toFixed(2) + ' (' + p.kode + ')';
      elFooter.style.color = p.color;
    } else {
      elFooter.textContent = 'Nilai: 0.00';
      elFooter.style.color = '#94a3b8';
    }
  }

  var elBadge = document.getElementById('predikat_badge');
  if (elBadge) {
    if (totalAkhir > 0) {
      var p = getPredikat(totalAkhir);
      elBadge.style.background = p.color;
      elBadge.style.color      = 'white';
      elBadge.textContent      = p.kode + ' — ' + p.label;
    } else {
      elBadge.style.background = 'transparent';
      elBadge.style.color      = 'rgba(255,255,255,.4)';
      elBadge.textContent      = 'Belum ada nilai';
    }
  }
}

// ═══════════════════════════════════════════════════════
//  VALIDASI UPLOAD PDF
// ═══════════════════════════════════════════════════════

function validatePDFUpload(form) {
  var fileInput = form.querySelector('input[type="file"]');
  if (fileInput && fileInput.files.length > 0) {
    var fileName = fileInput.files[0].name;
    var fileExt = fileName.split('.').pop().toLowerCase();
    
    if (fileExt !== 'pdf') {
      alert('⚠️ PERINGATAN!\n\nHanya file PDF yang diperbolehkan untuk diupload.\nFile yang Anda pilih: ' + fileName + '\nEkstensi: .' + fileExt);
      fileInput.value = '';
      return false;
    }
  }
  return true;
}

// Validasi saat memilih file
document.addEventListener('DOMContentLoaded', function() {
  // Event untuk input nilai
  var inputs = document.querySelectorAll('input[name^="nilai["]');
  inputs.forEach(function (inp) {
    inp.addEventListener('input',  updateAllTotals);
    inp.addEventListener('change', updateAllTotals);
  });
  updateAllTotals();
  
  // Event untuk validasi file PDF
  var pdfInputs = document.querySelectorAll('.pdf-input');
  pdfInputs.forEach(function(input) {
    input.addEventListener('change', function() {
      var fileName = this.files[0]?.name || '';
      var fileExt = fileName.split('.').pop().toLowerCase();
      var kriteriaId = this.dataset.kriteria;
      var warningDiv = document.getElementById('pdfWarning' + kriteriaId);
      
      if (fileExt !== 'pdf' && fileName !== '') {
        if (warningDiv) warningDiv.classList.add('show');
        alert('⚠️ Hanya file PDF yang diperbolehkan!\nFile yang Anda pilih: ' + fileName);
        this.value = '';
      } else {
        if (warningDiv) warningDiv.classList.remove('show');
      }
    });
  });
});
</script>
@endsection