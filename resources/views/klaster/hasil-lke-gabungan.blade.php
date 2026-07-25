@extends('layouts.app')
@section('title', 'Hasil LKE Gabungan')
@section('page-title', 'Evaluasi Kinerja')
@section('page-sub', 'Hasil LKE Gabungan')

@section('topbar-actions')
    @if(session('user.role') === 'admin')
    <div style="display: flex; gap: 10px;">
        <button onclick="window.print();" class="btn-print-glass">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9V3h12v6M6 21H4a2 2 0 0 1-2-2v-6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-2"/>
                <path d="M6 15h12v6H6z"/>
            </svg>
            Cetak / PDF
        </button>
    </div>
    @endif
@endsection

@section('content')
<style>
    .filter-box {
        background: white;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }

    .table-container {
        background: white;
        border-radius: 16px;
        overflow-x: auto;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }

    .gabungan-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        min-width: 1100px;
    }

    .gabungan-table th {
        background: linear-gradient(135deg, #1e293b, #0f172a);
        color: white;
        padding: 12px 8px;
        font-weight: 600;
        text-align: center;
        border-right: 1px solid rgba(255,255,255,0.1);
        vertical-align: middle;
    }

    .gabungan-table td {
        padding: 10px 8px;
        border-bottom: 1px solid #e2e8f0;
        text-align: center;
        vertical-align: middle;
    }

    .gabungan-table tbody tr:hover td {
        background: #f8fafc;
    }

    /* Baris komponen utama */
    .row-komponen td {
        background: #1e293b;
        color: #f1f5f9;
        font-weight: 700;
        font-size: 12px;
    }

    /* Baris sub komponen */
    .row-sub td {
        background: #f8fafc;
        font-weight: 500;
        border-left: 3px solid #2563eb;
    }

    .nilai-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 11px;
    }
    .nilai-sudah {
        background: #d1fae5;
        color: #065f46;
    }
    .nilai-belum {
        background: #fee2e2;
        color: #991b1b;
    }

    .total-box {
        background: #f1f5f9;
        border-radius: 16px;
        padding: 24px;
        margin-top: 24px;
        text-align: center;
    }
    .total-nilai {
        font-size: 36px;
        font-weight: 800;
        color: #1e293b;
    }
    .predikat-badge {
        display: inline-block;
        padding: 8px 28px;
        border-radius: 30px;
        font-weight: 700;
        font-size: 16px;
        margin-top: 10px;
    }

    .btn-print-glass {
        background: transparent;
        border: 1px solid #cbd5e1;
        padding: 8px 20px;
        border-radius: 30px;
        color: #475569;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-print-glass:hover { background: #f1f5f9; }

    .type-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
    }
    .type-utama     { background: #dbeafe; color: #1e40af; }
    .type-pendukung { background: #fef3c7; color: #92400e; }
    .type-tambahan  { background: #e0e7ff; color: #3730a3; }

    @media print {
        .filter-box, .topbar-actions, .btn-print-glass, .no-print { display: none !important; }
        .gabungan-table th { background: #e2e8f0 !important; color: black !important; }
        .row-komponen td   { background: #e2e8f0 !important; color: black !important; }
    }
</style>

{{-- FILTER --}}
<div class="filter-box no-print">
    <form method="GET" action="{{ route('klaster.hasil.lke.gabungan') }}">
        <div style="display: flex; gap: 15px; align-items: end; flex-wrap: wrap;">
            @if(session('user.role') === 'admin')
            <div>
                <label style="font-size: 12px; font-weight: bold; display:block; margin-bottom:4px;">Perangkat Daerah</label>
                <select name="opd_id" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; min-width: 220px;">
                    <option value="">-- Pilih OPD --</option>
                    @foreach($listOpd as $o)
                        <option value="{{ $o->id }}" {{ $opdId == $o->id ? 'selected' : '' }}>{{ $o->nama }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div>
                <label style="font-size: 12px; font-weight: bold; display:block; margin-bottom:4px;">Tahun</label>
                <select name="tahun" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
                    @foreach($listTahun as $t)
                        <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" style="background: #2563eb; color: white; border: none; padding: 9px 24px; border-radius: 8px; cursor: pointer; font-weight: 600;">
                Tampilkan
            </button>
        </div>
    </form>
</div>

@if(!$opdId)
<div style="text-align: center; padding: 80px 20px;">
    <div style="font-size: 56px; margin-bottom: 16px;">📊</div>
    <h3 style="color: #1e293b; margin-bottom: 8px;">Pilih OPD terlebih dahulu</h3>
    <p style="color: #64748b;">Gunakan filter di atas untuk memilih OPD yang akan dievaluasi.</p>
</div>
@else

@php
    function getWarnaPredikat($nilai) {
        if ($nilai >= 90) return '#059669';
        if ($nilai >= 80) return '#2563eb';
        if ($nilai >= 70) return '#7c3aed';
        if ($nilai >= 60) return '#d4982e';
        if ($nilai >= 50) return '#f59e0b';
        if ($nilai >= 30) return '#dc2626';
        if ($nilai >  0)  return '#991b1b';
        return '#6b7280';
    }

    function getLabelPredikat($nilai) {
        if ($nilai >= 90) return 'AA — Sangat Memuaskan';
        if ($nilai >= 80) return 'A — Memuaskan';
        if ($nilai >= 70) return 'BB — Sangat Baik';
        if ($nilai >= 60) return 'B — Baik';
        if ($nilai >= 50) return 'CC — Cukup Baik';
        if ($nilai >= 30) return 'C — Kurang';
        if ($nilai >  0)  return 'D — Sangat Kurang';
        return 'E — Tidak Ada Upaya';
    }

    function getTypeLabel($type) {
        return ['utama' => 'Utama', 'pendukung' => 'Pendukung', 'tambahan' => 'Tambahan'][$type] ?? ucfirst($type);
    }
@endphp

<div class="table-container">
    <table class="gabungan-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 4%;">NO</th>
                <th rowspan="2" style="text-align: left; width: 30%;">Komponen / Sub Komponen</th>
                <th rowspan="2" style="width: 7%;">TIPE</th>
                <th rowspan="2" style="width: 6%;">LEVEL</th>
                <th rowspan="2" style="width: 7%;">BOBOT</th>
                <th rowspan="2" style="width: 10%;">NILAI INSTANSI</th>
                <th colspan="3">NILAI PER TIPE</th>
                <th rowspan="2" style="width: 9%;">NILAI UNIT</th>
                <th rowspan="2" style="width: 10%;">NILAI AKUNTABILITAS</th>
            </tr>
            <tr>
                <th style="color: #fcd34d; width: 6%;">UTAMA</th>
                <th style="color: #fcd34d; width: 6%;">PENDUKUNG</th>
                <th style="color: #fcd34d; width: 6%;">TAMBAHAN</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp

            @foreach($allData as $item)

            {{-- ── BARIS KOMPONEN UTAMA ── --}}
            <tr class="row-komponen">
                <td>{{ $no++ }}</td>
                <td style="text-align: left; padding-left: 12px; font-weight: 700;">
                    {{ $item['komponen_nama'] }}
                </td>
                <td>
                    <span class="type-badge type-{{ $item['type'] }}">{{ getTypeLabel($item['type']) }}</span>
                </td>
                <td>Level {{ $item['level'] }}</td>
                <td style="font-weight: 700;">{{ number_format($item['bobot_komponen'], 2) }}</td>
                <td style="font-weight: 700;">{{ number_format($item['nilai_komponen'], 2) }}</td>
                <td>—</td>
                <td>—</td>
                <td>—</td>
                <td style="font-weight: 700;">{{ number_format($item['nilai_komponen'], 2) }}</td>
                <td style="font-weight: 700;">{{ number_format($item['nilai_komponen'], 2) }}</td>
            </tr>

            {{-- ── BARIS SUB KOMPONEN ── --}}
            @foreach($item['sub_komponen'] as $sub)
            <tr class="row-sub">
                <td style="color: #94a3b8; font-size: 11px;">↳</td>
                <td style="text-align: left; padding-left: 28px;">
                    {{ $sub['nama'] }}
                </td>
                <td>
                    <span class="type-badge type-{{ $sub['type'] }}">{{ getTypeLabel($sub['type']) }}</span>
                </td>
                <td>Level {{ $sub['level'] }}</td>
                <td>{{ number_format($sub['bobot'], 2) }}</td>
                <td>
                    <span class="nilai-badge {{ $sub['nilai_instansi'] > 0 ? 'nilai-sudah' : 'nilai-belum' }}">
                        {{ number_format($sub['nilai_instansi'], 2) }}
                    </span>
                </td>
                <td style="{{ $sub['nilai_utama'] > 0 ? 'font-weight:700; color:#059669;' : 'color:#cbd5e1;' }}">
                    {{ number_format($sub['nilai_utama'], 2) }}
                </td>
                <td style="{{ $sub['nilai_pendukung'] > 0 ? 'font-weight:700; color:#059669;' : 'color:#cbd5e1;' }}">
                    {{ number_format($sub['nilai_pendukung'], 2) }}
                </td>
                <td style="{{ $sub['nilai_tambahan'] > 0 ? 'font-weight:700; color:#059669;' : 'color:#cbd5e1;' }}">
                    {{ number_format($sub['nilai_tambahan'], 2) }}
                </td>
                <td style="font-weight: 600; background: #fef9c3;">
                    {{ number_format($sub['nilai_unit'], 2) }}
                </td>
                <td style="font-weight: 600; background: #dbeafe;">
                    {{ number_format($sub['nilai_akuntabilitas'], 2) }}
                </td>
            </tr>
            @endforeach

            @endforeach
        </tbody>

        {{-- TOTAL --}}
        <tfoot>
            <tr style="background: #0f172a;">
                <td colspan="5" style="text-align: right; color: white; font-weight: 700; padding: 12px 8px;">
                    TOTAL NILAI AKUNTABILITAS KINERJA
                </td>
                <td colspan="6" style="text-align: center; font-weight: 800; font-size: 16px; color: #fcd34d; padding: 12px 8px;">
                    {{ number_format($nilaiAkhir, 2) }}
                    &nbsp;
                    <span style="font-size: 12px; font-weight: 600; color: #94a3b8;">
                        ({{ $predikat['kode'] }} — {{ $predikat['label'] }})
                    </span>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

{{-- SUMMARY CARD --}}
<div class="total-box" style="margin-top: 24px;">
    <div style="font-size: 13px; color: #64748b; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 1px;">
        Nilai Akhir Akuntabilitas Kinerja — Tahun {{ $tahun }}
    </div>
    <div class="total-nilai">{{ number_format($nilaiAkhir, 2) }}</div>
    <div>
        <span class="predikat-badge" style="
            background: {{ $predikat['color'] }}20;
            color: {{ $predikat['color'] }};
            border: 1px solid {{ $predikat['color'] }}60;
        ">
            {{ $predikat['kode'] }} — {{ $predikat['label'] }}
        </span>
    </div>
</div>

@endif
@endsection

@section('scripts')
<script>
    window.onbeforeprint = function() { document.body.classList.add('printing'); };
    window.onafterprint  = function() { document.body.classList.remove('printing'); };
</script>
@endsection