@extends('layouts.app')
@section('title', 'Rekap Nilai LKE AKIP')
@section('page-title', 'LKE AKIP')
@section('page-sub', 'Rekap Nilai Perbandingan')

@section('topbar-actions')
<div style="display:flex;gap:8px;align-items:center;">
    <button onclick="window.print()" class="btn-act btn-print">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 6 3 18 3 18 9"/>
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
            <rect x="6" y="14" width="12" height="8"/>
        </svg>
        Cetak PDF
    </button>
    @if(session('user.role') === 'admin')
    <a href="{{ route('evaluasi.lke') }}" class="btn-act btn-back">
        ← Kembali
    </a>
    @endif
</div>
@endsection

@section('content')

@php
    if(session('user.role') === 'operator') {
        $opdIdUser = session('user.daerah_id');
        $rekapData = array_filter($rekapData, fn($item) => $item['opd_id'] == $opdIdUser);
        $rekapData = array_values($rekapData);
    }

    $warnaPredikat = [
        'AA'=>'#10b981','A'=>'#3b82f6','BB'=>'#8b5cf6',
        'B' =>'#f59e0b','CC'=>'#f97316','C'=>'#ef4444',
        'D' =>'#dc2626','E'=>'#6b7280',
    ];

    function getPredikatKode(float $n): string {
        if ($n >= 90) return 'AA';
        if ($n >= 80) return 'A';
        if ($n >= 70) return 'BB';
        if ($n >= 60) return 'B';
        if ($n >= 50) return 'CC';
        if ($n >= 30) return 'C';
        if ($n >  0)  return 'D';
        return 'E';
    }

    function getPredikatLabel(string $k): string {
        return match($k) {
            'AA' => 'Sangat Memuaskan', 'A'  => 'Memuaskan',
            'BB' => 'Sangat Baik',      'B'  => 'Baik',
            'CC' => 'Cukup Baik',       'C'  => 'Kurang',
            'D'  => 'Sangat Kurang',    default => 'Tidak Ada Upaya'
        };
    }
@endphp

<style>
:root {
    --bg:       #0c0c14;
    --surface:  #13131e;
    --surface2: #1a1a28;
    --border:   rgba(255,255,255,.07);
    --border2:  rgba(255,255,255,.12);
    --text:     #f0f0f8;
    --text-sub: #8888a8;
    --text-dim: #4a4a6a;
    --gold:     #d4982e;
    --gold-soft:rgba(212,152,46,.12);
    --accent:   #6366f1;
}

/* ── Buttons ── */
.btn-act {
    display:inline-flex;align-items:center;gap:6px;
    padding:7px 16px;border-radius:8px;font-size:12px;
    font-weight:600;cursor:pointer;border:none;transition:all .2s;
}
.btn-print {
    background:linear-gradient(135deg,var(--gold),#a06518);
    color:#fff;box-shadow:0 4px 14px rgba(212,152,46,.25);
}
.btn-print:hover { transform:translateY(-1px);box-shadow:0 6px 18px rgba(212,152,46,.35); }
.btn-back {
    background:rgba(255,255,255,.06);border:1px solid var(--border2);
    color:var(--text-sub);text-decoration:none;
}
.btn-back:hover { background:rgba(255,255,255,.1);color:var(--text); }

/* ── Filter card ── */
.filter-card {
    background:var(--surface);border:1px solid var(--border);
    border-radius:14px;padding:16px 20px;margin-bottom:20px;
    display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;
}
.filter-group { display:flex;flex-direction:column;gap:5px; }
.filter-label {
    font-size:9px;font-weight:700;letter-spacing:1.5px;
    text-transform:uppercase;color:var(--text-dim);
}
.filter-select {
    background:rgba(255,255,255,.04);border:1px solid var(--border2);
    border-radius:8px;padding:7px 12px;font-size:12px;
    color:var(--text);outline:none;cursor:pointer;
}
.filter-select:focus { border-color:var(--accent); }
.btn-filter {
    background:var(--accent);color:#fff;border:none;
    padding:8px 18px;border-radius:8px;font-size:12px;
    font-weight:600;cursor:pointer;
}

/* ── OPD Block ── */
.opd-block {
    background:var(--surface);border:1px solid var(--border);
    border-radius:16px;margin-bottom:16px;overflow:hidden;
}

/* ── OPD Header (compact) ── */
.opd-hd {
    display:flex;align-items:center;justify-content:space-between;
    padding:12px 18px;
    background:linear-gradient(90deg,rgba(212,152,46,.1),rgba(212,152,46,.03));
    border-bottom:1px solid var(--border);cursor:pointer;
    user-select:none;
}
.opd-hd-left { display:flex;align-items:center;gap:10px; }
.opd-icon {
    width:32px;height:32px;border-radius:8px;
    background:rgba(212,152,46,.15);border:1px solid rgba(212,152,46,.3);
    display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.opd-nama { font-size:13px;font-weight:700;color:var(--text); }
.opd-hd-right { display:flex;align-items:center;gap:10px; }

/* ── Score pills ── */
.score-pill {
    display:inline-flex;align-items:center;gap:5px;
    padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;
}
.score-pill .year { font-size:9px;opacity:.7;font-weight:400; }

/* ── Delta badge ── */
.delta {
    display:inline-flex;align-items:center;gap:3px;
    padding:3px 8px;border-radius:6px;font-size:11px;font-weight:700;
}
.delta-up   { background:rgba(16,185,129,.15);color:#10b981; }
.delta-down { background:rgba(239,68,68,.15);color:#ef4444; }
.delta-flat { background:rgba(107,114,128,.15);color:#6b7280; }

/* ── Collapse chevron ── */
.chev {
    width:18px;height:18px;color:var(--text-dim);
    transition:transform .25s;flex-shrink:0;
}
.opd-block.open .chev { transform:rotate(180deg); }

/* ── Detail panel ── */
.opd-detail { display:none;overflow:hidden; }
.opd-block.open .opd-detail { display:block; }

/* ── Compact table ── */
.tbl { width:100%;border-collapse:collapse;font-size:12px; }
.tbl thead tr { background:rgba(212,152,46,.06); }
.tbl thead th {
    padding:9px 12px;text-align:center;font-size:10px;
    font-weight:700;letter-spacing:.5px;text-transform:uppercase;
    color:var(--text-dim);border-bottom:1px solid var(--border);
}
.tbl thead th.left { text-align:left; }
.tbl tbody td {
    padding:8px 12px;border-bottom:1px solid rgba(255,255,255,.03);
    color:var(--text-sub);vertical-align:middle;text-align:center;
}
.tbl tbody td.left { text-align:left;color:var(--text);font-weight:500; }
.tbl tbody tr:last-child td { border-bottom:none; }
.tbl tbody tr:hover td { background:rgba(255,255,255,.02); }

/* ── Value cells ── */
.val { font-weight:700;color:var(--gold); }
.val-empty { color:var(--text-dim);font-style:italic;font-size:11px; }

/* ── Total row ── */
.tbl tfoot tr td {
    padding:10px 12px;background:rgba(212,152,46,.07);
    border-top:1px solid rgba(212,152,46,.2);
    font-weight:800;font-size:13px;
}

/* ── Predikat chip ── */
.pred {
    display:inline-block;padding:2px 10px;border-radius:20px;
    font-size:11px;font-weight:800;color:#fff;
}

/* ── Catatan accordion ── */
.cat-wrap {
    border-top:1px solid var(--border);
    background:rgba(0,0,0,.15);
}
.cat-toggle {
    width:100%;padding:10px 18px;background:none;border:none;
    display:flex;align-items:center;gap:8px;cursor:pointer;
    font-size:11px;font-weight:600;color:var(--text-dim);
    text-align:left;
}
.cat-toggle:hover { color:var(--gold); }
.cat-body { padding:0 18px 14px; }
.cat-item {
    margin-bottom:8px;padding:8px 12px;
    background:rgba(212,152,46,.05);border-left:2px solid var(--gold);
    border-radius:0 8px 8px 0;
}
.cat-item-title { font-size:10px;font-weight:700;color:var(--gold);margin-bottom:3px; }
.cat-item-text  { font-size:11px;color:var(--text-sub);line-height:1.5; }

/* ── Empty state ── */
.empty {
    text-align:center;padding:60px 20px;
    background:var(--surface);border-radius:16px;
    border:1px solid var(--border);
}

/* ── Sticky summary bar ── */
.summary-bar {
    position:sticky;bottom:16px;
    background:rgba(19,19,30,.92);
    backdrop-filter:blur(14px);
    border:1px solid var(--border2);
    border-radius:12px;padding:10px 18px;
    display:flex;align-items:center;justify-content:space-between;
    font-size:11px;color:var(--text-sub);
    margin-top:16px;z-index:10;
}
.summary-bar strong { color:var(--text); }

/* ── PRINT ── */
@media print {
    .no-print,.filter-card,.cat-wrap,.summary-bar,
    .chev,a.btn-back,button { display:none!important; }
    .opd-detail { display:block!important; }
    body,.main-content,div { background:white!important;color:#111!important; }
    .opd-block { border:1px solid #ddd!important;page-break-inside:avoid;margin-bottom:12px!important; }
    .opd-hd { background:#f5f0e8!important; }
    .opd-nama { color:#111!important; }
    .tbl thead tr { background:#f5f0e8!important; }
    .tbl thead th,.tbl tbody td,.tbl tfoot td { color:#111!important;border-color:#ddd!important; }
    .val { color:#6b4c10!important; }
    .pred { -webkit-print-color-adjust:exact;print-color-adjust:exact; }
    .delta { -webkit-print-color-adjust:exact;print-color-adjust:exact; }
    .score-pill { -webkit-print-color-adjust:exact;print-color-adjust:exact; }
}
</style>

{{-- ── FILTER ── --}}
<div class="filter-card no-print">
    <form method="GET" action="{{ route('evaluasi.lke.rekap') }}"
          style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;width:100%;">

        @if(session('user.role') === 'admin')
        <div class="filter-group" style="flex:1;min-width:200px;">
            <label class="filter-label">Perangkat Daerah</label>
            <select name="opd_id" class="filter-select">
                <option value="">— Semua OPD —</option>
                @foreach($listOpd as $o)
                    <option value="{{ $o->id }}" {{ ($opdId ?? '') == $o->id ? 'selected' : '' }}>
                        {{ $o->nama }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="filter-group">
            <label class="filter-label">Tahun Pembanding</label>
            <select name="tahun1" class="filter-select">
                @foreach($listTahun as $t)
                    <option value="{{ $t }}" {{ $tahun1 == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label">Tahun Terkini</label>
            <select name="tahun2" class="filter-select">
                @foreach($listTahun as $t)
                    <option value="{{ $t }}" {{ $tahun2 == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn-filter">Tampilkan</button>
    </form>
</div>

{{-- ── OPD BLOCKS ── --}}
@forelse($rekapData as $i => $rd)
@php
    $nilaiT1  = $rd['nilai_tahun1'];
    $nilaiT2  = $rd['nilai_tahun2'];
    $delta    = $nilaiT2 - $nilaiT1;
    $kodT1    = $nilaiT1 > 0 ? getPredikatKode($nilaiT1) : null;
    $kodT2    = $nilaiT2 > 0 ? getPredikatKode($nilaiT2) : null;
    $wT1      = $warnaPredikat[$kodT1] ?? '#6b7280';
    $wT2      = $warnaPredikat[$kodT2] ?? '#6b7280';

    // Catatan kriteria
    $catatanList = DB::table('lke_penilaian_kriteria as lpc')
        ->join('lke_kriteria as lk', 'lpc.kriteria_id', '=', 'lk.id')
        ->join('lke_komponen as kp', 'lk.komponen_id', '=', 'kp.id')
        ->where('lpc.perangkat_daerah_id', $rd['opd_id'])
        ->where('lpc.tahun', $tahun2)
        ->whereNotNull('lpc.komentar_admin')
        ->where('lpc.komentar_admin', '!=', '')
        ->select('lpc.komentar_admin','lk.nomor','lk.uraian','kp.nama as nama_komponen')
        ->get();
@endphp

<div class="opd-block {{ $i === 0 ? 'open' : '' }}" id="opd-{{ $i }}">

    {{-- Header (klik untuk expand/collapse) --}}
    <div class="opd-hd" onclick="toggleBlock('opd-{{ $i }}')">
        <div class="opd-hd-left">
            <div class="opd-icon">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                     stroke="#d4982e" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </div>
            <div>
                <div class="opd-nama">{{ $rd['opd'] }}</div>
                @if(session('user.role') === 'admin')
                <a href="{{ route('evaluasi.lke') }}?opd_id={{ $rd['opd_id'] }}&tahun={{ $tahun2 }}"
                   onclick="event.stopPropagation()"
                   style="font-size:10px;color:var(--text-dim);text-decoration:none;"
                   onmouseover="this.style.color='var(--gold)'"
                   onmouseout="this.style.color='var(--text-dim)'">
                    Buka Evaluasi →
                </a>
                @endif
            </div>
        </div>

        <div class="opd-hd-right">
            {{-- Score T1 --}}
            @if($nilaiT1 > 0)
            <span class="score-pill"
                  style="background:{{ $wT1 }}18;color:{{ $wT1 }};border:1px solid {{ $wT1 }}35;">
                <span>{{ number_format($nilaiT1,1) }}</span>
                <span class="pred" style="background:{{ $wT1 }};">{{ $kodT1 }}</span>
                <span class="year">{{ $tahun1 }}</span>
            </span>
            @endif

            {{-- Arrow --}}
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                 stroke="var(--text-dim)" stroke-width="2">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>

            {{-- Score T2 --}}
            @if($nilaiT2 > 0)
            <span class="score-pill"
                  style="background:{{ $wT2 }}18;color:{{ $wT2 }};border:1px solid {{ $wT2 }}35;">
                <span>{{ number_format($nilaiT2,1) }}</span>
                <span class="pred" style="background:{{ $wT2 }};">{{ $kodT2 }}</span>
                <span class="year">{{ $tahun2 }}</span>
            </span>
            @endif

            {{-- Delta --}}
            @if($nilaiT1 > 0 && $nilaiT2 > 0)
            <span class="delta {{ $delta > 0 ? 'delta-up' : ($delta < 0 ? 'delta-down' : 'delta-flat') }}">
                {{ $delta > 0 ? '▲' : ($delta < 0 ? '▼' : '=') }}
                {{ $delta != 0 ? number_format(abs($delta),2) : 'Tetap' }}
            </span>
            @endif

            <svg class="chev" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </div>
    </div>

    {{-- Detail panel --}}
    <div class="opd-detail">

        {{-- Tabel komponen --}}
        <div style="overflow-x:auto;padding:0;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th style="width:4%">#</th>
                        <th class="left" style="width:40%">Komponen</th>
                        <th style="width:9%">Bobot</th>
                        <th style="width:13%">{{ $tahun1 }}</th>
                        <th style="width:13%">{{ $tahun2 }}</th>
                        <th style="width:14%">Δ Perubahan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rd['komponen'] as $idx => $kom)
                    @php $dK = $kom['nilai_t2'] - $kom['nilai_t1']; @endphp
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td class="left">{{ $kom['nama'] }}</td>
                        <td>{{ number_format($kom['bobot'],2) }}</td>
                        <td>
                            @if($kom['nilai_t1'] > 0)
                                <span class="val">{{ number_format($kom['nilai_t1'],2) }}</span>
                            @else
                                <span class="val-empty">—</span>
                            @endif
                        </td>
                        <td>
                            @if($kom['nilai_t2'] > 0)
                                <span class="val">{{ number_format($kom['nilai_t2'],2) }}</span>
                            @else
                                <span class="val-empty">—</span>
                            @endif
                        </td>
                        <td>
                            @if($kom['nilai_t1'] > 0 && $kom['nilai_t2'] > 0)
                                <span class="delta {{ $dK > 0 ? 'delta-up' : ($dK < 0 ? 'delta-down' : 'delta-flat') }}">
                                    {{ $dK > 0 ? '▲' : ($dK < 0 ? '▼' : '=') }}
                                    {{ $dK != 0 ? number_format(abs($dK),2) : 'Tetap' }}
                                </span>
                            @else
                                <span class="val-empty">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" style="text-align:right;color:var(--gold);padding-right:8px;">
                            Total Nilai Akuntabilitas
                        </td>
                        <td style="text-align:center;color:var(--text-dim);">—</td>
                        <td style="text-align:center;">
                            @if($nilaiT1 > 0)
                                <div style="color:var(--gold);font-size:15px;font-weight:900;">
                                    {{ number_format($nilaiT1,2) }}
                                </div>
                                <span class="pred" style="background:{{ $wT1 }};font-size:10px;margin-top:3px;display:inline-block;">
                                    {{ $kodT1 }} — {{ getPredikatLabel($kodT1) }}
                                </span>
                            @else
                                <span class="val-empty">Belum Input</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            @if($nilaiT2 > 0)
                                <div style="color:var(--gold);font-size:15px;font-weight:900;">
                                    {{ number_format($nilaiT2,2) }}
                                </div>
                                <span class="pred" style="background:{{ $wT2 }};font-size:10px;margin-top:3px;display:inline-block;">
                                    {{ $kodT2 }} — {{ getPredikatLabel($kodT2) }}
                                </span>
                            @else
                                <span class="val-empty">Belum Input</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            @if($nilaiT1 > 0 && $nilaiT2 > 0)
                                <span class="delta {{ $delta > 0 ? 'delta-up' : ($delta < 0 ? 'delta-down' : 'delta-flat') }}"
                                      style="font-size:13px;padding:4px 12px;">
                                    {{ $delta > 0 ? '▲' : ($delta < 0 ? '▼' : '=') }}
                                    {{ $delta != 0 ? number_format(abs($delta),2) : 'Tetap' }}
                                </span>
                                <div style="font-size:10px;color:var(--text-dim);margin-top:3px;">
                                    {{ $delta > 0 ? 'Meningkat' : ($delta < 0 ? 'Menurun' : 'Tidak berubah') }}
                                </div>
                            @else
                                <span class="val-empty">—</span>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Catatan admin (no-print) --}}
        @if($catatanList->count() > 0)
        <div class="cat-wrap no-print">
            <button class="cat-toggle" onclick="toggleCat(this)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                {{ $catatanList->count() }} Komentar Admin
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" class="cat-chev"
                     style="margin-left:auto;transition:transform .2s;">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>
            <div class="cat-body" style="display:none;">
                @foreach($catatanList as $cat)
                <div class="cat-item">
                    <div class="cat-item-title">
                        {{ $cat->nama_komponen }} → Kriteria {{ $cat->nomor }}
                    </div>
                    <div style="font-size:10px;color:var(--text-dim);margin:2px 0 4px;">
                        {{ Str::limit($cat->uraian, 80) }}
                    </div>
                    <div class="cat-item-text">{{ $cat->komentar_admin }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>{{-- end opd-detail --}}
</div>{{-- end opd-block --}}

@empty
<div class="empty">
    <div style="font-size:44px;margin-bottom:14px;opacity:.4;">📊</div>
    <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:6px;">
        Belum Ada Data
    </div>
    <div style="font-size:12px;color:var(--text-sub);">
        Belum ada data LKE AKIP untuk periode yang dipilih.
    </div>
</div>
@endforelse

{{-- ── Summary bar ── --}}
@if(count($rekapData) > 0)
<div class="summary-bar no-print">
    <span>
        <strong>{{ count($rekapData) }}</strong> OPD ditampilkan
        &nbsp;·&nbsp; Periode
        <strong>{{ $tahun1 }}</strong> vs <strong>{{ $tahun2 }}</strong>
    </span>
    <span style="font-size:10px;">
        Klik header OPD untuk expand/collapse detail
    </span>
</div>
@endif

@endsection

@section('scripts')
<script>
function toggleBlock(id) {
    var el = document.getElementById(id);
    el.classList.toggle('open');
}

function toggleCat(btn) {
    var body = btn.nextElementSibling;
    var chev = btn.querySelector('.cat-chev');
    var isOpen = body.style.display !== 'none';
    body.style.display = isOpen ? 'none' : 'block';
    if (chev) chev.style.transform = isOpen ? '' : 'rotate(180deg)';
}

// Print: expand semua sebelum print
window.onbeforeprint = function() {
    document.querySelectorAll('.opd-block').forEach(function(b) {
        b.classList.add('open');
    });
};
</script>
@endsection