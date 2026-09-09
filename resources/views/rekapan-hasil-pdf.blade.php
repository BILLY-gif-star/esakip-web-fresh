@extends('layouts.app')
@section('title', 'Rekapan Hasil LKE AKIP')
@section('page-title', 'Rekapan Hasil LKE AKIP')
@section('page-sub', 'Ringkasan nilai evaluasi per Perangkat Daerah')

@section('topbar-actions')
<div style="display:flex;gap:10px;align-items:center;">
    {{-- Filter tahun --}}
    <form method="GET" action="{{ route('rekapan.hasil') }}" style="display:flex;gap:8px;">
        <select name="tahun" class="btn-outline-glass" style="padding:8px 16px;">
            @foreach($listTahun as $t)
                <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary-glass">🔍 Tampilkan</button>
    </form>

    {{-- Tombol unduh --}}
    @if(count($dataRekapan) > 0)
    <a href="{{ route('rekapan.hasil.excel', ['tahun' => $tahun]) }}"
       style="background:linear-gradient(135deg,#059669,#047857);border:none;padding:8px 18px;
              border-radius:40px;color:white;font-weight:600;font-size:12px;
              text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
        📥 Excel
    </a>

    @endif
</div>
@endsection

@section('content')

<style>
    .rekapan-table {
        width: 100%;
        border-collapse: collapse;
        background: linear-gradient(135deg, #1e1e2e, #181825);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    .rekapan-table th {
        background: linear-gradient(135deg, #1e3a5f, #2563eb);
        color: white;
        padding: 14px 12px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        border-right: 1px solid rgba(255,255,255,.1);
    }
    .rekapan-table td {
        padding: 12px;
        font-size: 13px;
        text-align: center;
        border-bottom: 1px solid rgba(255,255,255,.05);
        color: #e4e4e7;
    }
    .rekapan-table tr:hover td {
        background: rgba(255,255,255,.03);
    }
    .text-left {
        text-align: left !important;
    }
    .nilai-cell {
        font-weight: 700;
    }
    .nilai-tinggi { color: #34d399; }
    .nilai-sedang { color: #fbbf24; }
    .nilai-rendah { color: #f87171; }
    .predikat-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }
    .peringkat-cell {
        font-weight: 800;
        font-size: 16px;
    }
    .btn-primary-glass {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        border: none;
        padding: 8px 20px;
        border-radius: 40px;
        color: white;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-outline-glass {
        background: transparent;
        border: 1px solid #e2e8f0;
        padding: 8px 20px;
        border-radius: 40px;
        color: #a1a1aa;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-outline-glass:hover {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border-color: transparent;
        color: white;
    }
    .table-wrapper {
        overflow-x: auto;
        border-radius: 16px;
    }
    .footer-info {
        text-align: center;
        padding: 20px;
        color: #6b7280;
        font-size: 12px;
        border-top: 1px solid rgba(255,255,255,.05);
        margin-top: 20px;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #1e1e2e, #181825);
        border-radius: 16px;
        color: #6b7280;
    }
    .empty-state .icon {
        font-size: 64px;
        margin-bottom: 16px;
        display: block;
    }
    .rank-1 { background: #fbbf24; color: #78350f; font-weight: 800; padding: 4px 8px; border-radius: 20px; display: inline-block; }
    .rank-2 { background: #9ca3af; color: white; font-weight: 800; padding: 4px 8px; border-radius: 20px; display: inline-block; }
    .rank-3 { background: #b45309; color: white; font-weight: 800; padding: 4px 8px; border-radius: 20px; display: inline-block; }
</style>

@if(count($dataRekapan) > 0)
<div class="table-wrapper">
    <table class="rekapan-table">
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 30%; text-align: left;">NAMA PERANGKAT DAERAH</th>
                <th style="width: 12%;">PERENCANAAN<br>KINERJA</th>
                <th style="width: 12%;">PENGUKURAN<br>KINERJA</th>
                <th style="width: 12%;">PELAPORAN<br>KINERJA</th>
                <th style="width: 12%;">EVALUASI AKUNTABILITAS<br>KINERJA INTERNAL</th>
                <th style="width: 8%;">JUMLAH</th>
                <th style="width: 8%;">KUALITAS</th>
                <th style="width: 10%;">PREDIKAT</th>
                <th style="width: 6%;">PERINGKAT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataRekapan as $item)
            @php
                $total = $item['total'];
                if ($total >= 70) $kelasTotal = 'nilai-tinggi';
                elseif ($total >= 50) $kelasTotal = 'nilai-sedang';
                else $kelasTotal = 'nilai-rendah';
                
                $nilai1 = $item['nilai1'];
                $kelas1 = $nilai1 >= 21 ? 'nilai-tinggi' : ($nilai1 >= 15 ? 'nilai-sedang' : 'nilai-rendah');
                
                $nilai2 = $item['nilai2'];
                $kelas2 = $nilai2 >= 21 ? 'nilai-tinggi' : ($nilai2 >= 15 ? 'nilai-sedang' : 'nilai-rendah');
                
                $nilai3 = $item['nilai3'];
                $kelas3 = $nilai3 >= 10.5 ? 'nilai-tinggi' : ($nilai3 >= 7.5 ? 'nilai-sedang' : 'nilai-rendah');
                
                $nilai4 = $item['nilai4'];
                $kelas4 = $nilai4 >= 17.5 ? 'nilai-tinggi' : ($nilai4 >= 12.5 ? 'nilai-sedang' : 'nilai-rendah');
                
                $predikat = $item['predikat'];
                
                $rankClass = '';
                if ($item['peringkat'] == 1) $rankClass = 'rank-1';
                elseif ($item['peringkat'] == 2) $rankClass = 'rank-2';
                elseif ($item['peringkat'] == 3) $rankClass = 'rank-3';
            @endphp
            <tr>
                <td class="peringkat-cell">
                    <span class="{{ $rankClass }}">{{ $item['peringkat'] }}</span>
                </td>
                <td class="text-left" style="font-weight: 500;">{{ $item['nama'] }}</td>
                <td class="nilai-cell {{ $kelas1 }}">{{ number_format($nilai1, 2) }}</td>
                <td class="nilai-cell {{ $kelas2 }}">{{ number_format($nilai2, 2) }}</td>
                <td class="nilai-cell {{ $kelas3 }}">{{ number_format($nilai3, 2) }}</td>
                <td class="nilai-cell {{ $kelas4 }}">{{ number_format($nilai4, 2) }}</td>
                <td class="nilai-cell {{ $kelasTotal }}" style="font-weight: 800;">{{ number_format($total, 2) }}</td>
                <td>{{ number_format($item['kualitas'], 2) }}%</td>
                <td>
                    <span class="predikat-badge" style="background: {{ $predikat['color'] }}20; color: {{ $predikat['color'] }}; border: 1px solid {{ $predikat['color'] }}40;">
                        {{ $predikat['kode'] }} - {{ $predikat['label'] }}
                    </span>
                </td>
                <td class="peringkat-cell">{{ $item['peringkat'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="footer-info">
    📊 Rekapan hasil LKE AKIP Provinsi NTT - Tahun {{ $tahun }} (Menampilkan {{ count($dataRekapan) }} OPD)
</div>
@else
<div class="empty-state">
    <span class="icon">📭</span>
    <div style="font-size: 16px; font-weight: 600; margin-bottom: 8px;">Belum Ada Data</div>
    <div style="font-size: 13px;">Belum ada data penilaian LKE AKIP untuk tahun {{ $tahun }}</div>
    <div style="margin-top: 16px; font-size: 12px;">Silakan lakukan penilaian terlebih dahulu melalui menu Evaluasi Kinerja → LKE AKIP</div>
</div>
@endif

@endsection