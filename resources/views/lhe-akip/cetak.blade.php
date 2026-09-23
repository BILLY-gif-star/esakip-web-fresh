@extends('layouts.app')

@section('title', 'Cetak LHE AKIP')
@section('page-title', 'Preview / Cetak LHE AKIP')
@section('page-sub', $opd->nama ?? '')

@section('topbar-actions')
  <a href="{{ route('lhe-akip.index', ['tahun' => $lhe->tahun_evaluasi]) }}" class="btn btn-outline btn-sm no-print">← Kembali</a>
  <button onclick="window.print()" class="btn btn-gold btn-sm no-print">🖨️ Cetak / Simpan PDF</button>
@endsection

@section('content')

<style>
  .kertas-wrap { display:flex; justify-content:center; }
  .kertas {
    background:#ffffff; color:#1a1a1a; width:210mm; min-height:297mm;
    padding:20mm 22mm; font-family:'Times New Roman', Times, serif;
    font-size:12pt; line-height:1.5; box-shadow:0 4px 24px rgba(0,0,0,.35); margin-bottom:24px;
    position:relative;
  }
  .kertas p { text-align:justify; margin-bottom:10px; }

  .dok-logo { text-align:center; margin-bottom:18px; }
  .dok-logo img { height:110px; }

  .dok-kop { text-align:center; margin-bottom:6px; }
  .dok-kop .judul1 { font-size:13pt; font-weight:bold; letter-spacing:.5px; }
  .dok-kop .judul2 { font-size:13pt; font-weight:bold; margin-top:2px; }
  .dok-kop .opd    { font-size:12pt; font-weight:bold; margin-top:10px; text-transform:uppercase; }

  .dok-sampul {
    display: flex;
    flex-direction: column;
    min-height: 245mm; /* tinggi A4 (297mm) dikurangi padding atas+bawah .kertas (20mm x 2 ≈ 40mm, + sedikit buffer) */
  }

  .dok-nomor { margin-top:110px; }
  .dok-nomor table td { padding:2px 0; vertical-align:top; font-size:12pt; }
  .dok-nomor table td.label { width:90px; }

  .dok-pemerintah {
    text-align:center;
    margin-top: auto;   /* dorong sampai mentok bawah, mengisi sisa ruang kosong */
  }
  .dok-pemerintah .p1 { font-weight:bold; font-size:13pt; }
  .dok-pemerintah .p2 { font-weight:bold; font-size:13pt; margin-top:4px; }

  .page-break { page-break-before:always; }
  .halaman-nomor { text-align:center; font-size:10pt; color:#666; margin-top:30px; }

  .bab-title { font-weight:bold; text-decoration:underline; margin:18px 0 10px; }
  .sub-title { font-weight:bold; margin:14px 0 6px; }
  .caption-tabel { font-weight:bold; text-align:center; margin:14px 0 8px; }

  /* Daftar bernomor 1,2,3... — sesuai gaya penomoran dokumen asli */
  .poin-list-num { margin:0 0 10px 26px; padding:0; list-style:decimal; }
  .poin-list-num > li { margin-bottom:6px; text-align:justify; padding-left:4px; }

  /* Sub-daftar berlabel a,b,c di dalam Rekomendasi */
  .poin-sub-alpha { margin:6px 0 6px 22px; padding:0; list-style:lower-alpha; }
  .poin-sub-alpha > li { margin-bottom:4px; text-align:justify; padding-left:4px; }

  .tabel-nilai { width:100%; border-collapse:collapse; margin:10px 0 18px; font-size:11.5pt; }
  .tabel-nilai th, .tabel-nilai td { border:1px solid #333; padding:6px 8px; }
  .tabel-nilai th { text-align:center; font-weight:bold; background:#6b1d1d; color:#fff; }
  .tabel-nilai td.center { text-align:center; }
  .tabel-nilai tr.total td { font-weight:bold; background:#f2e9e9; }
  .tabel-nilai tr.total-utama td { font-weight:bold; background:#6b1d1d; color:#fff; }
  .tabel-nilai tr.baris-sub td { font-size:10.5pt; color:#444; background:#fbfbfb; }

  .ttd-block { margin-top:50px; }
  .ttd-table { width:100%; border-collapse:collapse; }
  .ttd-table td { border:1px solid #333; padding:14px; vertical-align:top; text-align:center; }
  .ttd-nama { margin-top:60px; font-weight:bold; text-decoration:underline; }

  .paraf-table { width:60%; margin-top:24px; border-collapse:collapse; font-size:11pt; }
  .paraf-table td, .paraf-table th { border:1px solid #333; padding:6px 10px; }
  .paraf-table th { background:#f2f2f2; text-align:left; }
  .paraf-table td.kotak { width:60px; }

  .kertas, .kertas td, .kertas th, .kertas div, .kertas span, .kertas li {
    color: #1a1a1a;
  }

  /* header tabel nilai tetap putih di atas background merah tua */
  .tabel-nilai th {
    color: #fff !important;
  }

  /* header tabel paraf: background abu muda, teks tetap gelap */
  .paraf-table th {
    color: #1a1a1a !important;
  }

  @media print {
    .no-print { display:none !important; }
    .kertas { box-shadow:none; margin:0; width:auto; min-height:auto; }
    .kertas-wrap { display:block; }
  }
</style>

<div class="kertas-wrap">
<div class="kertas">

  {{-- ══════ HALAMAN SAMPUL ══════ --}}
  <div class="dok-sampul">

    <div class="dok-logo">
      <img src="{{ asset('assets/logo_ntt.png') }}" alt="Logo Provinsi NTT" onerror="this.style.display='none'">
    </div>

    <div class="dok-kop">
      <div class="judul1">LAPORAN HASIL EVALUASI</div>
      <div class="judul2">AKUNTABILITAS KINERJA INSTANSI PEMERINTAH (AKIP)</div>
      <div class="opd">{{ $opd->nama ?? '[Nama OPD]' }}<br>Sekretariat Daerah<br>Provinsi Nusa Tenggara Timur</div>
    </div>

    <div class="dok-nomor">
      <table>
        <tr><td class="label">NOMOR</td><td>: {{ $lhe->nomor_surat }}</td></tr>
        <tr><td class="label">TANGGAL</td><td>: {{ optional($lhe->tanggal_surat)->translatedFormat('d F Y') }}</td></tr>
      </table>
    </div>

    <div class="dok-pemerintah">
      <div class="p1">PEMERINTAH PROVINSI NUSA TENGGARA TIMUR</div>
      <div class="p2">{{ $lhe->tahun_evaluasi }}</div>
    </div>

  </div>

  {{-- ══════ I. PENDAHULUAN ══════ --}}
  <div class="page-break"></div>

  <div class="bab-title">I. PENDAHULUAN</div>

  <div class="sub-title">A. Dasar Hukum Evaluasi</div>
  <ol class="poin-list-num">
    <li>Peraturan Pemerintah Nomor 8 Tahun 2006 tentang Pelaporan Keuangan dan Kinerja Instansi Pemerintah;</li>
    <li>Peraturan Presiden Nomor 29 Tahun 2014 tentang Sistem Akuntabilitas Kinerja Instansi Pemerintah;</li>
    <li>Peraturan Menteri Pendayagunaan Aparatur Negara dan Reformasi Birokrasi Republik Indonesia Nomor 88 Tahun 2021 tentang Evaluasi Akuntabilitas Kinerja Instansi Pemerintah;</li>
    @if($lhe->nomor_sk_tim)
    <li>Keputusan Gubernur Nusa Tenggara Timur Nomor {{ $lhe->nomor_sk_tim }} tentang Tim Kerja Evaluasi Akuntabilitas Kinerja Instansi Pemerintah Provinsi Nusa Tenggara Timur Tahun {{ $lhe->tahun_evaluasi }}.</li>
    @endif
  </ol>

  <div class="sub-title">B. Latar Belakang</div>
  <p>Evaluasi AKIP adalah aktivitas analisis yang sistematis, pemberian nilai, atribut, apresiasi, dan pengenalan permasalahan, serta pemberian solusi atas masalah yang ditemukan guna peningkatan akuntabilitas dan peningkatan kinerja instansi pemerintah.</p>
  <p>Penguatan akuntabilitas kinerja merupakan salah satu strategi yang dilaksanakan dalam rangka mempercepat pelaksanaan reformasi birokrasi untuk mewujudkan pemerintahan yang bersih, akuntabel dan kapabel, meningkatnya kualitas pelayanan publik kepada masyarakat dan meningkatnya kapasitas dan akuntabilitas kinerja birokrasi.</p>

  <div class="sub-title">C. Tujuan Evaluasi</div>
  <p>Secara umum, tujuan evaluasi atas implementasi SAKIP adalah untuk:</p>
  <ol class="poin-list-num">
    <li>Memperoleh informasi tentang implementasi SAKIP;</li>
    <li>Menilai tingkat implementasi SAKIP;</li>
    <li>Memberikan saran perbaikan untuk peningkatan implementasi SAKIP;</li>
    <li>Memonitor tindak lanjut rekomendasi hasil evaluasi periode sebelumnya.</li>
  </ol>

  <div class="sub-title">D. Ruang Lingkup Evaluasi</div>
  <p>Adapun ruang lingkup evaluasi atas implementasi AKIP mencakup:</p>
  <ol class="poin-list-num">
    <li>Perencanaan Kinerja;</li>
    <li>Pengukuran Kinerja;</li>
    <li>Pelaporan Kinerja;</li>
    <li>Evaluasi Akuntabilitas Kinerja Internal.</li>
  </ol>

  <div class="sub-title">E. Metodologi Evaluasi</div>
  <p>Metodologi yang digunakan dalam evaluasi atas implementasi SAKIP adalah kombinasi metodologi kualitatif dan kuantitatif dengan mempertimbangkan kepraktisan dan kemanfaatan yang disesuaikan dengan tujuan evaluasi serta mempertimbangkan kendala yang ada. Dalam hal ini, evaluator perlu menjelaskan kelemahan dan kelebihan metodologi yang digunakan kepada pihak yang dievaluasi. Langkah praktis ini diambil agar dapat lebih cepat menghasilkan rekomendasi hasil evaluasi yang memberikan petunjuk untuk perbaikan implementasi SAKIP dan peningkatan akuntabilitas kinerja instansi pemerintah.</p>

  <div class="sub-title">F. Waktu Pelaksanaan Evaluasi</div>
  <p>
    Evaluasi AKIP pada {{ $opd->nama ?? '[Nama OPD]' }} Sekretariat Daerah Provinsi NTT dilaksanakan oleh Tim Evaluasi AKIP Pemerintah Provinsi NTT
    @if($lhe->periode_mulai && $lhe->periode_selesai)
      pada {{ $lhe->periode_mulai->translatedFormat('d F Y') }} s.d {{ $lhe->periode_selesai->translatedFormat('d F Y') }}.
    @else
      pada tahun {{ $lhe->tahun_evaluasi }}.
    @endif
  </p>

  {{-- ══════ II. HASIL EVALUASI ══════ --}}
  <div class="bab-title">II. HASIL EVALUASI</div>
  <p>
    Hasil evaluasi yang dituangkan dalam bentuk nilai dengan kisaran mulai dari 0 s.d. 100,
    {{ $opd->nama ?? '[Nama OPD]' }} Sekretariat Daerah Provinsi Nusa Tenggara Timur
    memperoleh nilai sebesar <strong>{{ number_format($nilai['total'], 2, ',', '.') }}</strong>
    dengan kategori <strong>{{ $nilai['kategori'] }} ({{ \App\Models\LheAkip::deskripsiKategori($nilai['kategori']) }})</strong>.
  </p>

  <div class="caption-tabel">
    Rincian Hasil Evaluasi AKIP {{ $opd->nama ?? '[Nama OPD]' }} Sekretariat Daerah<br>
    Provinsi Nusa Tenggara Timur Tahun {{ $lhe->tahun_evaluasi }}
  </div>

  <table class="tabel-nilai">
    <thead>
      <tr>
        <th style="width:5%;">No</th>
        <th>Komponen / Sub Komponen / Kriteria</th>
        <th style="width:12%;">Bobot</th>
        <th style="width:15%;">Nilai {{ $lhe->tahun_evaluasi }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach($nilai['rincian'] as $i => $k)
        <tr class="baris-komponen">
          <td class="center">{{ $i + 1 }}</td>
          <td><strong>{{ $k['nama'] }}</strong></td>
          <td class="center"><strong>{{ number_format($k['bobot'], 2, ',', '.') }}</strong></td>
          <td class="center"><strong>{{ number_format($k['nilai'], 2, ',', '.') }}</strong></td>
        </tr>

      @endforeach
      <tr class="total-utama">
        <td colspan="3">Nilai Akuntabilitas Kinerja</td>
        <td class="center">{{ number_format($nilai['total'], 2, ',', '.') }}</td>
      </tr>
      <tr class="total">
        <td colspan="3">Kategori</td>
        <td class="center">{{ $nilai['kategori'] }}</td>
      </tr>
    </tbody>
  </table>

  <p>Nilai tersebut merupakan akumulasi penilaian evaluasi terhadap komponen manajemen kinerja dengan rincian sebagai berikut:</p>

  @php
    // Urutan $nilai['rincian'] mengikuti urutan id lke_komponen induk (1,2,3,4)
    // = Perencanaan, Pengukuran, Pelaporan, Evaluasi Internal
    $keyMap    = ['perencanaan', 'pengukuran', 'pelaporan', 'evaluasi_internal'];
    $hurufMap  = ['A', 'B', 'C', 'D']; // lanjutan huruf sub-bab di Bab II

    $formatBobotPersen = function ($b) {
        $b = (float) $b;
        return $b == floor($b)
            ? number_format($b, 0, ',', '.')
            : rtrim(rtrim(number_format($b, 2, ',', '.'), '0'), ',');
    };
  @endphp

  @foreach($nilai['rincian'] as $idx => $k)
    @php $key = $keyMap[$idx] ?? null; @endphp
    <div class="sub-title">{{ $hurufMap[$idx] ?? '' }}. Evaluasi atas {{ $k['nama'] }} (Bobot Nilai {{ $formatBobotPersen($k['bobot']) }}%)</div>

    @if($key && $lhe->{'uraian_' . $key})
      <p>{{ $lhe->{'uraian_' . $key} }}</p>
    @endif

    @php $catatan = $nilai['catatan'][$idx] ?? []; @endphp
    @if(count($catatan))
      <p>Namun demikian, masih terdapat hal-hal yang perlu adanya penyempurnaan yakni:</p>
      <ol class="poin-list-num">
        @foreach($catatan as $poin)
          <li>{{ $poin }}</li>
        @endforeach
      </ol>
    @endif
  @endforeach

  {{-- REKOMENDASI — poin lanjutan huruf E, BUKAN bab terpisah --}}
  @if(is_array($lhe->rekomendasi) && count(array_filter($lhe->rekomendasi, fn($g) => count($g['poin'] ?? []))))
    <div class="sub-title">E. Rekomendasi</div>
    <p>
      Terhadap catatan evaluasi yang telah dikemukakan di atas, kami merekomendasikan kepada
      Kepala {{ $opd->nama ?? '[Nama OPD]' }} Sekretariat Daerah Provinsi NTT agar
      mempertahankan kinerja yang sudah optimal. Meski demikian ada beberapa hal yang masih
      harus terus ditingkatkan demi perbaikan dan penyempurnaan SAKIP ke depannya yakni:
    </p>

    <ol class="poin-list-num">
      @foreach($lhe->rekomendasi as $grup)
        @if(count($grup['poin'] ?? []))
          <li>
            Melakukan penyempurnaan {{ $grup['komponen'] }} melalui upaya:
            <ol class="poin-sub-alpha">
              @foreach($grup['poin'] as $poin)
                <li>{{ $poin }}</li>
              @endforeach
            </ol>
          </li>
        @endif
      @endforeach
    </ol>
  @endif

  {{-- ══════ III. PENUTUP ══════ --}}
  <div class="bab-title">III. PENUTUP</div>
  <p>
    @if($lhe->penutup)
      {{ $lhe->penutup }}
    @else
      Demikian hasil evaluasi atas implementasi Sistem Akuntabilitas Kinerja Instansi Pemerintah (SAKIP) ini disampaikan.
      Kami menghargai upaya yang telah dilakukan dalam mengimplementasikan SAKIP pada lingkungan
      {{ $opd->nama ?? '[Nama OPD]' }} Sekretariat Daerah Provinsi NTT. Atas perhatian dan kerja sama disampaikan terima kasih.
    @endif
  </p>

  {{-- TANDA TANGAN --}}
  @if($lhe->nama_penandatangan)
  <div class="ttd-block">
    <table class="ttd-table">
      <tr>
        <td>
          a.n. Gubernur Nusa Tenggara Timur<br>
          Sekretaris Daerah<br>
          u.b.<br>
          {{ $lhe->jabatan_penandatangan }},
          <div class="ttd-nama">{{ $lhe->nama_penandatangan }}</div>
          {{ $lhe->pangkat_penandatangan }}<br>
          @if($lhe->nip_penandatangan) NIP {{ $lhe->nip_penandatangan }} @endif
        </td>
      </tr>
    </table>

    <table class="paraf-table">
      <thead><tr><th>Paraf Hierarki</th><th class="kotak"></th></tr></thead>
      <tbody>
        <tr><td>Plt. Kepala Biro Organisasi</td><td class="kotak"></td></tr>
        <tr><td>Plh. Kabag Reformasi Birokrasi dan Akuntabilitas Kinerja</td><td class="kotak"></td></tr>
        <tr><td>Analis Akuntabilitas Kinerja</td><td class="kotak"></td></tr>
      </tbody>
    </table>
  </div>
  @endif

</div>
</div>

@endsection