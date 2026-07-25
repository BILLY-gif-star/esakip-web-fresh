@extends('layouts.app')

@section('title', 'Penjelasan Penilaian LKE AKIP')
@section('page-title', 'Penjelasan Penilaian')
@section('page-sub', 'Pedoman Penilaian Lembar Kerja Evaluasi AKIP')

@section('content')

<style>
    .penjelasan-card {
        background: linear-gradient(135deg, #1e1e2e, #181825);
        border-radius: 20px;
        border: 1px solid rgba(255,255,255,.08);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .penjelasan-header {
        background: linear-gradient(135deg, #1e3a5f, #2563eb);
        padding: 16px 24px;
        color: #fff;
        font-weight: 700;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .penjelasan-body {
        padding: 24px;
    }
    .penjelasan-table {
        width: 100%;
        border-collapse: collapse;
    }
    .penjelasan-table th {
        background: rgba(99,102,241,.15);
        color: #a5b4fc;
        padding: 12px;
        font-size: 12px;
        font-weight: 700;
        text-align: center;
        border: 1px solid rgba(255,255,255,.08);
    }
    .penjelasan-table td {
        padding: 10px 12px;
        font-size: 13px;
        color: #e4e4e7;
        border: 1px solid rgba(255,255,255,.06);
        vertical-align: top;
    }
    .predikat-AA { color: #059669; font-weight: bold; }
    .predikat-A { color: #2563eb; font-weight: bold; }
    .predikat-BB { color: #7c3aed; font-weight: bold; }
    .predikat-B { color: #d4982e; font-weight: bold; }
    .predikat-CC { color: #f59e0b; font-weight: bold; }
    .predikat-C { color: #dc2626; font-weight: bold; }
    .predikat-D { color: #991b1b; font-weight: bold; }
    .predikat-E { color: #6b7280; font-weight: bold; }
    .bobot-badge {
        display: inline-block;
        background: rgba(99,102,241,.2);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        color: #a5b4fc;
    }
</style>

<div class="penjelasan-card">
    <div class="penjelasan-header">
        <span>📖</span> Pedoman Penilaian LKE AKIP
    </div>
    <div class="penjelasan-body">
        <p style="color: #a1a1aa; margin-bottom: 20px;">
            Penilaian dilakukan pada setiap sub-komponen berdasarkan pemenuhan kualitas dari kriteria (sebagai probing),
            dengan pilihan jawaban <strong>AA / A / BB / B / CC / C / D / E</strong> sesuai dengan gradasi nilai berikut:
        </p>
    </div>
</div>

{{-- TABEL KEBERADAAN --}}
<div class="penjelasan-card">
    <div class="penjelasan-header">
        <span>📋</span> Dimensi 1: Keberadaan
       
    </div>
    <div class="penjelasan-body">
        <table class="penjelasan-table">
            <thead>
                <tr>
                    <th>Pilihan Jawaban</th>
                    <th>Nilai</th>
                    <th>Penjelasan</th>
                </tr>
            </thead>
            <tbody>
                <tr><td class="predikat-AA">AA</td><td>100</td><td>Jika seluruh kriteria telah terpenuhi (100%) dan telah dipertahankan dalam setidaknya 5 tahun terakhir.</td></tr>
                <tr><td class="predikat-A">A</td><td>90</td><td>Jika seluruh kriteria telah terpenuhi (100%) dan telah dipertahankan dalam setidaknya 1 tahun terakhir.</td></tr>
                <tr><td class="predikat-BB">BB</td><td>80</td><td>Jika kualitas seluruh kriteria telah terpenuhi (100%) sesuai dengan mandat kebijakan nasional.</td></tr>
                <tr><td class="predikat-B">B</td><td>70</td><td>Jika kualitas sebagian besar kriteria telah terpenuhi (>75% - 100%).</td></tr>
                <tr><td class="predikat-CC">CC</td><td>60</td><td>Jika kualitas sebagian besar kriteria telah terpenuhi (>50% - 75%).</td></tr>
                <tr><td class="predikat-C">C</td><td>50</td><td>Jika kualitas sebagian kecil kriteria telah terpenuhi (>25% - 50%).</td></tr>
                <tr><td class="predikat-D">D</td><td>30</td><td>Jika kriteria penilaian akuntabilitas kinerja telah mulai dipenuhi (>0% - 25%).</td></tr>
                <tr><td class="predikat-E">E</td><td>0</td><td>Jika sama sekali tidak ada upaya dalam pemenuhan kriteria penilaian akuntabilitas kinerja.</td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- TABEL KUALITAS --}}
<div class="penjelasan-card">
    <div class="penjelasan-header">
        <span>⭐</span> Dimensi 2: Kualitas
    
    </div>
    <div class="penjelasan-body">
        <table class="penjelasan-table">
            <thead>
                <tr><th>Pilihan Jawaban</th><th>Nilai</th><th>Penjelasan</th></tr>
            </thead>
            <tbody>
                <tr><td class="predikat-AA">AA</td><td>100</td><td>Jika kualitas seluruh kriteria telah terpenuhi (100%) dan terdapat upaya inovatif serta layak menjadi percontohan secara nasional</td></tr>
                <tr><td class="predikat-A">A</td><td>90</td><td>Jika kualitas seluruh kriteria telah terpenuhi (100%) dan terdapat beberapa upaya yang bisa dihargai dari pemenuhan kriteria tersebut.</td></tr>
                <tr><td class="predikat-BB">BB</td><td>80</td><td>Jika kualitas seluruh kriteria telah terpenuhi (100%) sesuai dengan mandat kebijakan nasional.</td></tr>
                <tr><td class="predikat-B">B</td><td>70</td><td>Jika kualitas sebagian besar kriteria telah terpenuhi (>75% - 100%).</td></tr>
                <tr><td class="predikat-CC">CC</td><td>60</td><td>Jika kualitas sebagian besar kriteria telah terpenuhi (>50% - 75%).</td></tr>
                <tr><td class="predikat-C">C</td><td>50</td><td>Jika kualitas sebagian kecil kriteria telah terpenuhi (>25% - 50%).</td></tr>
                <tr><td class="predikat-D">D</td><td>30</td><td>Jika kriteria penilaian akuntabilitas kinerja telah mulai dipenuhi (>0% - 25%).</td></tr>
                <tr><td class="predikat-E">E</td><td>0</td><td>Jika sama sekali tidak ada upaya dalam pemenuhan kriteria penilaian akuntabilitas kinerja.</td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- TABEL PEMANFAATAN --}}
<div class="penjelasan-card">
    <div class="penjelasan-header">
        <span>🚀</span> Dimensi 3: Pemanfaatan
       
    </div>
    <div class="penjelasan-body">
        <table class="penjelasan-table">
            <thead>
                <tr><th>Pilihan Jawaban</th><th>Nilai</th><th>Penjelasan</th></tr>
            </thead>
            <tbody>
                <tr><td class="predikat-AA">AA</td><td>100</td><td>Jika kualitas seluruh kriteria telah terpenuhi (100%) dan terdapat upaya inovatif serta layak menjadi percontohan secara nasional</td></tr>
                <tr><td class="predikat-A">A</td><td>90</td><td>Jika kualitas seluruh kriteria telah terpenuhi (100%) dan terdapat beberapa upaya yang bisa dihargai dari pemenuhan kriteria tersebut.</td></tr>
                <tr><td class="predikat-BB">BB</td><td>80</td><td>Jika kualitas seluruh kriteria telah terpenuhi (100%) sesuai dengan mandat kebijakan nasional.</td></tr>
                <tr><td class="predikat-B">B</td><td>70</td><td>Jika kualitas sebagian besar kriteria telah terpenuhi (>75% - 100%).</td></tr>
                <tr><td class="predikat-CC">CC</td><td>60</td><td>Jika kualitas sebagian besar kriteria telah terpenuhi (>50% - 75%).</td></tr>
                <tr><td class="predikat-C">C</td><td>50</td><td>Jika kualitas sebagian kecil kriteria telah terpenuhi (>25% - 50%).</td></tr>
                <tr><td class="predikat-D">D</td><td>30</td><td>Jika kriteria penilaian akuntabilitas kinerja telah mulai dipenuhi (>0% - 25%).</td></tr>
                <tr><td class="predikat-E">E</td><td>0</td><td>Jika sama sekali tidak ada upaya dalam pemenuhan kriteria penilaian akuntabilitas kinerja.</td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection