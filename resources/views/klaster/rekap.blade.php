@extends('layouts.app')
@section('title', 'Rekap Klaster')
@section('page-title', 'Klaster')
@section('page-sub', 'Rekap Nilai ' . ucfirst($type) . ' Level ' . $level)

@section('topbar-actions')
<div style="display:flex;gap:10px;">
    <button onclick="window.print();" style="background:#4f46e5;color:white;padding:8px 20px;border-radius:40px;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;font-weight:600;transition:all .3s">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 3 18 3 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Cetak / Download PDF
    </button>
    <a href="{{ route('klaster.utama.1') }}?type={{ $type }}&level={{ $level }}" style="background:transparent;border:2px solid #e5e7eb;padding:8px 20px;border-radius:40px;color:#6b7280;font-weight:600;display:inline-flex;align-items:center;gap:8px;text-decoration:none;">
        ← Kembali ke Evaluasi
    </a>
</div>
@endsection

@section('content')

@php
    $warnaPredikat = [
        'AA'=>'#059669','A'=>'#2563eb','BB'=>'#7c3aed','B'=>'#d4982e',
        'CC'=>'#f59e0b','C'=>'#dc2626','D'=>'#991b1b','E'=>'#6b7280',
    ];
    $typeColor = ['utama'=>'#1e3a6e','pendukung'=>'#3b1f6e','tambahan'=>'#1a4a2e'];
    $coklat    = $typeColor[$type] ?? '#1e3a6e';

    function klasterPredikatKode(float $n): string {
        if ($n >= 90) return 'AA';
        if ($n >= 80) return 'A';
        if ($n >= 70) return 'BB';
        if ($n >= 60) return 'B';
        if ($n >= 50) return 'CC';
        if ($n >= 30) return 'C';
        if ($n >  0)  return 'D';
        return 'E';
    }
@endphp

<style>
:root {
    --coklat-tua: {{ $coklat }};
    --shadow-md: 0 4px 16px rgba(0,0,0,0.12);
    --border-light: #e5e7eb;
}
@keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
.fade-up{animation:fadeUp .4s ease forwards}

.filter-wrap{background:white;border-radius:20px;padding:20px 24px;margin-bottom:24px;box-shadow:0 1px 4px rgba(0,0,0,.07);border:1px solid var(--border-light);transition:box-shadow .3s,transform .3s}
.filter-wrap:hover{box-shadow:var(--shadow-md);transform:translateY(-2px)}
.filter-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#6b7280;display:block;margin-bottom:5px}
.filter-select{border:2px solid #e5e7eb;border-radius:12px;padding:9px 14px;font-weight:600;background:white;transition:border-color .2s}
.filter-select:focus{border-color:#4f46e5;outline:none}
.btn-tampilkan{background:linear-gradient(135deg,#4f46e5,#6366f1);color:white;border:none;padding:10px 28px;border-radius:40px;font-weight:700;cursor:pointer;transition:all .3s}
.btn-tampilkan:hover{transform:translateY(-2px)}

/* Navigasi type & level */
.nav-pill{padding:7px 18px;border-radius:40px;font-weight:700;font-size:12px;text-decoration:none;border:2px solid #e5e7eb;color:#6b7280;transition:all .3s;display:inline-block}
.nav-pill:hover,.nav-pill.active{color:white;border-color:transparent;text-decoration:none}
.nav-pill.utama.active,.nav-pill.utama:hover{background:#2563eb}
.nav-pill.pendukung.active,.nav-pill.pendukung:hover{background:#7c3aed}
.nav-pill.tambahan.active,.nav-pill.tambahan:hover{background:#059669}
.nav-pill.level.active,.nav-pill.level:hover{background:#1e293b}

.opd-block{background:white;border-radius:20px;overflow:hidden;box-shadow:var(--shadow-md);margin-bottom:28px;border:1px solid #e8e0d4;transition:box-shadow .3s}
.opd-block:hover{box-shadow:0 8px 32px rgba(0,0,0,.15)}
.opd-block-header{background:linear-gradient(135deg,var(--coklat-tua),color-mix(in srgb,var(--coklat-tua) 70%,white));padding:14px 22px;display:flex;align-items:center;gap:12px}
.opd-block-header .opd-nama{color:white;font-weight:800;font-size:15px}
.opd-block-header .opd-link{color:rgba(255,255,255,.65);font-size:12px;text-decoration:none;transition:.2s;margin-left:auto}
.opd-block-header .opd-link:hover{color:white}

.rekap-tbl{width:100%;border-collapse:collapse}
.rekap-tbl thead tr{background:linear-gradient(135deg,var(--coklat-tua),color-mix(in srgb,var(--coklat-tua) 75%,black))}
.rekap-tbl thead th{color:white;padding:13px 14px;font-size:12px;font-weight:700;letter-spacing:.3px;border:1px solid rgba(255,255,255,.15);text-align:center}
.rekap-tbl thead th.left{text-align:left}
.rekap-tbl tbody td{padding:11px 14px;border:1px solid #e8e0d4;font-size:13px;vertical-align:middle}
.rekap-tbl tbody tr:nth-child(even) td{background:#fdf9f4}
.rekap-tbl tbody tr:hover td{background:#fef3e2;transition:background .2s}
.cell-nilai{text-align:center;font-weight:700;font-size:14px;color:#1e3a5f}
.cell-nilai.belum{color:#9ca3af;font-weight:400;font-style:italic;font-size:12px}
.cell-no{text-align:center;font-weight:700;color:#6b7280;width:50px}

.row-total td{background:linear-gradient(135deg,var(--coklat-tua),color-mix(in srgb,var(--coklat-tua) 75%,black))!important;color:white!important;font-weight:800;font-size:13px;padding:14px;border:1px solid rgba(255,255,255,.1)}
.row-total .total-nilai{text-align:center;font-size:20px;font-weight:900;letter-spacing:.5px}

.predikat-badge{display:inline-block;padding:5px 18px;border-radius:40px;font-weight:900;font-size:14px;letter-spacing:.5px;color:white}
.delta-chip{display:inline-block;font-size:10px;padding:2px 7px;border-radius:20px;margin-left:6px;font-weight:700;vertical-align:middle}
.delta-up{background:#dcfce7;color:#15803d}
.delta-down{background:#fee2e2;color:#dc2626}
.delta-flat{background:#f1f5f9;color:#64748b}
.empty-state{text-align:center;padding:56px;background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-radius:20px}

.kop-surat{display:none}
@media print{
    header,nav,.sidebar,.topbar,.topbar-actions,.filter-wrap,.no-print,button,a[href]{display:none!important}
    body,.content,.main-content{margin:0!important;padding:0!important;background:white!important}
    .opd-block{box-shadow:none!important;border:1px solid #ccc!important;page-break-inside:avoid}
    .rekap-tbl thead tr,.row-total td{-webkit-print-color-adjust:exact;print-color-adjust:exact}
    .kop-surat{display:block!important}
}
</style>

{{-- ═══ FILTER ═══ --}}
<div class="filter-wrap fade-up no-print">
    <form method="GET" action="{{ route('klaster.rekap') }}">
        <div style="display:flex;gap:14px;align-items:flex-end;flex-wrap:wrap;">

            @if(session('user.role') === 'admin')
            <div>
                <label class="filter-label">Perangkat Daerah</label>
                <select name="opd_id" class="filter-select" style="min-width:220px">
                    <option value="">— Semua OPD —</option>
                    @foreach($listOpd as $o)
                        <option value="{{ $o->id }}" {{ ($opdId ?? '') == $o->id ? 'selected' : '' }}>{{ $o->nama }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="filter-label">Tipe Klaster</label>
                <select name="type" class="filter-select">
                    @foreach($listType as $t)
                        <option value="{{ $t }}" {{ $type == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="filter-label">Level</label>
                <select name="level" class="filter-select">
                    @foreach($listLevel as $l)
                        <option value="{{ $l }}" {{ $level == $l ? 'selected' : '' }}>Level {{ $l }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="filter-label">Tahun 1 (Pembanding)</label>
                <select name="tahun1" class="filter-select">
                    @foreach($listTahun as $t)
                        <option value="{{ $t }}" {{ $tahun1 == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="filter-label">Tahun 2 (Terkini)</label>
                <select name="tahun2" class="filter-select">
                    @foreach($listTahun as $t)
                        <option value="{{ $t }}" {{ $tahun2 == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn-tampilkan">🔍 Tampilkan</button>
        </div>

        {{-- Tab navigasi cepat --}}
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid #e5e7eb;display:flex;gap:16px;align-items:center;flex-wrap:wrap">
            <div style="display:flex;gap:6px">
                @foreach($listType as $t)
                    <a href="{{ route('klaster.rekap') }}?type={{ $t }}&level={{ $level }}&tahun1={{ $tahun1 }}&tahun2={{ $tahun2 }}"
                       class="nav-pill {{ $t }} {{ $type == $t ? 'active' : '' }}">
                        {{ ucfirst($t) }}
                    </a>
                @endforeach
            </div>
            <div style="display:flex;gap:6px">
                @foreach($listLevel as $l)
                    <a href="{{ route('klaster.rekap') }}?type={{ $type }}&level={{ $l }}&tahun1={{ $tahun1 }}&tahun2={{ $tahun2 }}"
                       class="nav-pill level {{ $level == $l ? 'active' : '' }}">
                        Level {{ $l }}
                    </a>
                @endforeach
            </div>
        </div>
    </form>
</div>

{{-- ═══ KOP SURAT PRINT ═══ --}}
<div class="kop-surat" style="text-align:center;margin-bottom:20px;border-bottom:2px solid #000;padding-bottom:10px;">
    <h2 style="margin:0">PEMERINTAH DAERAH</h2>
    <h3 style="margin:4px 0">REKAP NILAI KLASTER {{ strtoupper($type) }} LEVEL {{ $level }}</h3>
    <p style="font-size:12px;margin:4px 0">Perbandingan Tahun {{ $tahun1 }} vs {{ $tahun2 }}</p>
    <p style="font-size:12px;margin:4px 0">Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
</div>

{{-- ═══ BLOK PER OPD ═══ --}}
@forelse($rekapData as $rd)
@php
    $nilaiT1 = $rd['nilai_tahun1'];
    $nilaiT2 = $rd['nilai_tahun2'];
    $delta   = $nilaiT2 - $nilaiT1;
    $kodT1   = $nilaiT1 > 0 ? klasterPredikatKode($nilaiT1) : null;
    $kodT2   = $nilaiT2 > 0 ? klasterPredikatKode($nilaiT2) : null;
@endphp

<div class="opd-block fade-up" style="animation-delay:{{ $loop->index * 0.05 }}s">

    <div class="opd-block-header">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.7)" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <span class="opd-nama">{{ $rd['opd'] }}</span>
        <a href="{{ route('klaster.utama.1') }}?opd_id={{ $rd['opd_id'] }}&tahun={{ $tahun2 }}&type={{ $type }}&level={{ $level }}"
           class="opd-link">→ Buka Evaluasi</a>
    </div>

    <div style="overflow-x:auto">
        <table class="rekap-tbl">
            <thead>
                <tr>
                    <th rowspan="2" style="width:4%;vertical-align:middle">No</th>
                    <th rowspan="2" class="left" style="width:38%;vertical-align:middle">Komponen / Sub Komponen</th>
                    <th rowspan="2" style="width:8%;vertical-align:middle">Bobot</th>
                    <th colspan="2" style="text-align:center;border-bottom:1px solid rgba(255,255,255,.25)">
                        Nilai Akuntabilitas Kinerja
                    </th>
                    <th rowspan="2" style="width:14%;vertical-align:middle;text-align:center">Perbandingan</th>
                </tr>
                <tr>
                    <th style="width:14%;font-size:13px;background:rgba(0,0,0,.15)">{{ $tahun1 }}</th>
                    <th style="width:14%;font-size:13px;background:rgba(0,0,0,.15)">{{ $tahun2 }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rd['komponen'] as $idx => $kom)
                @php $deltaKom = $kom['nilai_t2'] - $kom['nilai_t1']; @endphp
                <tr>
                    <td class="cell-no">{{ $idx + 1 }}</td>
                    <td style="font-weight:600;color:#1e293b">{{ $kom['nama'] }}</td>
                    <td style="text-align:center;font-weight:700;color:#3b2f20">{{ number_format($kom['bobot'], 2) }}</td>
                    <td class="{{ $kom['nilai_t1'] > 0 ? 'cell-nilai' : 'cell-nilai belum' }}">
                        {{ $kom['nilai_t1'] > 0 ? number_format($kom['nilai_t1'], 2) : 'Belum Input' }}
                    </td>
                    <td class="{{ $kom['nilai_t2'] > 0 ? 'cell-nilai' : 'cell-nilai belum' }}">
                        {{ $kom['nilai_t2'] > 0 ? number_format($kom['nilai_t2'], 2) : 'Belum Input' }}
                    </td>
                    <td style="text-align:center;vertical-align:middle">
                        @if($kom['nilai_t1'] > 0 && $kom['nilai_t2'] > 0)
                            @if($deltaKom > 0)
                                <span style="background:#dcfce7;color:#15803d;padding:4px 10px;border-radius:20px;font-weight:700;font-size:12px;display:inline-block">▲ {{ number_format(abs($deltaKom),2) }}</span>
                            @elseif($deltaKom < 0)
                                <span style="background:#fee2e2;color:#dc2626;padding:4px 10px;border-radius:20px;font-weight:700;font-size:12px;display:inline-block">▼ {{ number_format(abs($deltaKom),2) }}</span>
                            @else
                                <span style="background:#f1f5f9;color:#64748b;padding:4px 10px;border-radius:20px;font-weight:700;font-size:12px;display:inline-block">= 0</span>
                            @endif
                        @else
                            <span style="color:#94a3b8;font-size:12px">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach

                {{-- Baris TOTAL --}}
                <tr class="row-total">
                    <td colspan="2" style="text-align:right;font-size:14px">⊕ &nbsp;Nilai Akuntabilitas Kinerja</td>
                    <td style="text-align:center;font-size:12px;color:rgba(255,255,255,.6)">—</td>

                    <td class="total-nilai">
                        @if($nilaiT1 > 0)
                            {{ number_format($nilaiT1, 2) }}<br>
                            <span class="predikat-badge" style="background:{{ $warnaPredikat[$kodT1] ?? '#6b7280' }};font-size:12px;padding:3px 12px;margin-top:4px;display:inline-block">
                                {{ $kodT1 }}
                            </span>
                        @else
                            <span style="font-size:12px;font-weight:400;opacity:.6;font-style:italic">Belum Input</span>
                        @endif
                    </td>

                    <td class="total-nilai">
                        @if($nilaiT2 > 0)
                            {{ number_format($nilaiT2, 2) }}<br>
                            <span class="predikat-badge" style="background:{{ $warnaPredikat[$kodT2] ?? '#6b7280' }};font-size:12px;padding:3px 12px;margin-top:4px;display:inline-block">
                                {{ $kodT2 }}
                            </span>
                        @else
                            <span style="font-size:12px;font-weight:400;opacity:.6;font-style:italic">Belum Input</span>
                        @endif
                    </td>

                    {{-- Kolom Perbandingan Total --}}
                    <td class="total-nilai">
                        @if($nilaiT1 > 0 && $nilaiT2 > 0)
                            @if($delta > 0)
                                <span style="background:rgba(255,255,255,.15);color:#bbf7d0;padding:6px 14px;border-radius:30px;font-weight:800;font-size:15px;display:inline-block">▲ {{ number_format(abs($delta),2) }}</span>
                                <div style="font-size:10px;color:rgba(255,255,255,.5);margin-top:4px">Meningkat</div>
                            @elseif($delta < 0)
                                <span style="background:rgba(255,255,255,.15);color:#fca5a5;padding:6px 14px;border-radius:30px;font-weight:800;font-size:15px;display:inline-block">▼ {{ number_format(abs($delta),2) }}</span>
                                <div style="font-size:10px;color:rgba(255,255,255,.5);margin-top:4px">Menurun</div>
                            @else
                                <span style="background:rgba(255,255,255,.15);color:rgba(255,255,255,.7);padding:6px 14px;border-radius:30px;font-weight:800;font-size:15px;display:inline-block">= Tetap</span>
                            @endif
                        @else
                            <span style="opacity:.5;font-size:12px">—</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@empty
<div class="empty-state fade-up">
    <div style="font-size:52px;margin-bottom:16px">📊</div>
    <h3 style="font-weight:700;margin-bottom:8px">Belum Ada Data Penilaian</h3>
    <p style="color:#6b7280">Belum ada data Klaster {{ ucfirst($type) }} Level {{ $level }} untuk periode yang dipilih.</p>
</div>
@endforelse

@if(count($rekapData) > 0)
<div class="no-print" style="margin-top:8px;text-align:center;font-size:12px;color:#9ca3af;">
    Total OPD: <strong>{{ count($rekapData) }}</strong> &nbsp;|&nbsp;
    Klaster: <strong>{{ ucfirst($type) }}</strong> Level <strong>{{ $level }}</strong> &nbsp;|&nbsp;
    Periode: <strong>{{ $tahun1 }}</strong> vs <strong>{{ $tahun2 }}</strong>
</div>
@endif

@endsection
