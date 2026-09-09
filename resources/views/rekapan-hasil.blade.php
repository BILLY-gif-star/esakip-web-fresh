@extends('layouts.app')
@section('title', 'Rekapan Hasil LKE AKIP')
@section('page-title', 'Rekapan Hasil LKE AKIP')
@section('page-sub', 'Ringkasan nilai evaluasi per Perangkat Daerah')

@section('topbar-actions')
<div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">

    {{-- Tombol Upload Dokumen ke OPD --}}
    <button onclick="document.getElementById('modalUploadDokumen').style.display='flex'"
            style="display:inline-flex;align-items:center;gap:7px;
                   background:linear-gradient(135deg,#6366f1,#4f46e5);
                   padding:9px 20px;border-radius:40px;
                   color:#fff;font-weight:600;font-size:12px;border:none;
                   cursor:pointer;box-shadow:0 4px 14px rgba(99,102,241,.35);"
            onmouseover="this.style.transform='translateY(-2px)'"
            onmouseout="this.style.transform='translateY(0)'">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.2"
             stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Upload ke OPD
    </button>

    {{-- Filter Tahun --}}
    <form method="GET" action="{{ route('rekapan.hasil') }}"
          style="display:flex;gap:8px;align-items:center;">
        <select name="tahun"
                onchange="this.form.submit()"
                style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);
                       border-radius:10px;padding:8px 14px;color:#fff;font-size:12px;
                       font-weight:600;outline:none;cursor:pointer;">
            @foreach($listTahun as $t)
                <option value="{{ $t }}"
                        {{ $tahun == $t ? 'selected' : '' }}
                        style="background:#1a1a24;color:#fff;">
                    {{ $t }}
                </option>
            @endforeach
        </select>
    </form>

    {{-- Tombol Unduh Excel --}}
    <a href="{{ route('rekapan.hasil.excel', ['tahun' => $tahun]) }}"
       style="display:inline-flex;align-items:center;gap:7px;
              background:linear-gradient(135deg,#059669,#047857);
              padding:9px 20px;border-radius:40px;
              color:#fff;font-weight:600;font-size:12px;
              text-decoration:none;
              box-shadow:0 4px 14px rgba(5,150,105,.35);
              transition:all .2s;"
       onmouseover="this.style.transform='translateY(-2px)'"
       onmouseout="this.style.transform='translateY(0)'">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.2"
             stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Unduh Excel
    </a>

   
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
        font-size: 11px;
        font-weight: 700;
        text-align: center;
        border-right: 1px solid rgba(255,255,255,.1);
        letter-spacing: .3px;
        text-transform: uppercase;
    }
    .rekapan-table td {
        padding: 11px 12px;
        font-size: 12.5px;
        text-align: center;
        border-bottom: 1px solid rgba(255,255,255,.05);
        color: #e4e4e7;
        vertical-align: middle;
    }
    .rekapan-table tbody tr:hover td {
        background: rgba(255,255,255,.03);
    }
    .text-left { text-align: left !important; }
    .nilai-cell { font-weight: 700; }
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

    .rank-wrap {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 13px;
    }
    .rank-1 { background: rgba(251,191,36,.25); color: #fbbf24; }
    .rank-2 { background: rgba(156,163,175,.2); color: #9ca3af; }
    .rank-3 { background: rgba(180,83,9,.2);    color: #fb923c; }
    .rank-n { background: rgba(255,255,255,.06); color: #71717a; }

    .table-wrapper {
        overflow-x: auto;
        border-radius: 16px;
    }

    .footer-info {
        text-align: center;
        padding: 16px 20px;
        color: #52525b;
        font-size: 11px;
        border-top: 1px solid rgba(255,255,255,.05);
        margin-top: 20px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #1e1e2e, #181825);
        border-radius: 16px;
        color: #6b7280;
        border: 1px solid rgba(255,255,255,.06);
    }
    .empty-state .icon { font-size: 64px; margin-bottom: 16px; display: block; }
</style>

{{-- Flash success/error --}}
@if(session('success'))
<div style="background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.3);
            border-radius:12px;padding:12px 18px;margin-bottom:18px;
            display:flex;align-items:center;gap:10px;color:#34d399;font-size:13px;font-weight:600;">
    ✅ {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);
            border-radius:12px;padding:12px 18px;margin-bottom:18px;
            display:flex;align-items:center;gap:10px;color:#f87171;font-size:13px;font-weight:600;">
    ❌ {{ session('error') }}
</div>
@endif

@if(count($dataRekapan) > 0)

<div class="table-wrapper">
    <table class="rekapan-table">
        <thead>
            <tr>
                <th style="width:5%;">No</th>
                <th style="width:28%;text-align:left;">Nama Perangkat Daerah</th>
                <th style="width:11%;">Perencanaan<br>Kinerja</th>
                <th style="width:11%;">Pengukuran<br>Kinerja</th>
                <th style="width:11%;">Pelaporan<br>Kinerja</th>
                <th style="width:12%;">Evaluasi<br>AKIP Internal</th>
                <th style="width:8%;">Jumlah</th>
                <th style="width:10%;">Kualitas</th>
                <th style="width:8%;">Predikat</th>
                <th style="width:6%;">Peringkat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataRekapan as $item)
            @php
                $total    = $item['total'];
                $predikat = $item['predikat'];

                $kelasTotal = $total >= 70 ? 'nilai-tinggi' : ($total >= 50 ? 'nilai-sedang' : 'nilai-rendah');
                $kelas1     = $item['nilai1'] >= 21   ? 'nilai-tinggi' : ($item['nilai1'] >= 15   ? 'nilai-sedang' : 'nilai-rendah');
                $kelas2     = $item['nilai2'] >= 21   ? 'nilai-tinggi' : ($item['nilai2'] >= 15   ? 'nilai-sedang' : 'nilai-rendah');
                $kelas3     = $item['nilai3'] >= 10.5 ? 'nilai-tinggi' : ($item['nilai3'] >= 7.5  ? 'nilai-sedang' : 'nilai-rendah');
                $kelas4     = $item['nilai4'] >= 17.5 ? 'nilai-tinggi' : ($item['nilai4'] >= 12.5 ? 'nilai-sedang' : 'nilai-rendah');

                $rankClass = match($item['peringkat']) {
                    1       => 'rank-1',
                    2       => 'rank-2',
                    3       => 'rank-3',
                    default => 'rank-n',
                };
            @endphp
            <tr>
                {{-- No --}}
                <td>
                    <span class="rank-wrap {{ $rankClass }}">
                        {{ $item['peringkat'] }}
                    </span>
                </td>

                {{-- Nama OPD --}}
                <td class="text-left" style="font-weight:500;color:#f4f4f5;">
                    {{ $item['nama'] }}
                </td>

                {{-- Nilai per komponen --}}
                <td class="nilai-cell {{ $kelas1 }}">{{ number_format($item['nilai1'], 2) }}</td>
                <td class="nilai-cell {{ $kelas2 }}">{{ number_format($item['nilai2'], 2) }}</td>
                <td class="nilai-cell {{ $kelas3 }}">{{ number_format($item['nilai3'], 2) }}</td>
                <td class="nilai-cell {{ $kelas4 }}">{{ number_format($item['nilai4'], 2) }}</td>

                {{-- Jumlah --}}
                <td class="nilai-cell {{ $kelasTotal }}" style="font-size:14px;font-weight:800;">
                    {{ number_format($total, 2) }}
                </td>

                {{-- Kualitas (label predikat) --}}
                <td>
                    <span style="font-size:11px;color:{{ $predikat['color'] }};font-weight:700;">
                        {{ $predikat['label'] }}
                    </span>
                </td>

                {{-- Predikat (kode) --}}
                <td>
                    <span class="predikat-badge"
                          style="background:{{ $predikat['color'] }}25;
                                 color:{{ $predikat['color'] }};
                                 border:1px solid {{ $predikat['color'] }}45;">
                        {{ $predikat['kode'] }}
                    </span>
                </td>

                {{-- Peringkat --}}
                <td style="font-weight:800;font-size:14px;color:#a1a1aa;">
                    {{ $item['peringkat'] }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="footer-info">
    📊 Rekapan LKE AKIP Provinsi NTT — Tahun <strong style="color:#a1a1aa;">{{ $tahun }}</strong>
    &nbsp;|&nbsp; Menampilkan <strong style="color:#a1a1aa;">{{ count($dataRekapan) }}</strong> OPD
</div>

@else

<div class="empty-state">
    <span class="icon">📭</span>
    <div style="font-size:16px;font-weight:600;margin-bottom:8px;color:#e4e4e7;">
        Belum Ada Data
    </div>
    <div style="font-size:13px;color:#71717a;">
        Belum ada data penilaian LKE AKIP untuk tahun {{ $tahun }}
    </div>
    <div style="margin-top:12px;font-size:12px;color:#52525b;">
        Silakan lakukan penilaian melalui menu Evaluasi Kinerja → LKE AKIP
    </div>
</div>

@endif


{{-- ════════════════════════════════════════════
     MODAL UPLOAD DOKUMEN KE OPD
     ════════════════════════════════════════════ --}}
<div id="modalUploadDokumen"
     style="display:none;position:fixed;inset:0;z-index:9999;
            background:rgba(0,0,0,.65);backdrop-filter:blur(4px);
            align-items:center;justify-content:center;padding:20px;">

    <div style="background:#1a1a24;border:1px solid rgba(255,255,255,.1);
                border-radius:20px;width:100%;max-width:520px;
                box-shadow:0 20px 60px rgba(0,0,0,.5);overflow:hidden;
                max-height:90vh;overflow-y:auto;">

        {{-- Header --}}
        <div style="padding:20px 24px 16px;border-bottom:1px solid rgba(255,255,255,.08);
                    display:flex;align-items:center;justify-content:space-between;
                    position:sticky;top:0;background:#1a1a24;z-index:1;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;border-radius:10px;
                            background:rgba(99,102,241,.2);border:1px solid rgba(99,102,241,.3);
                            display:flex;align-items:center;justify-content:center;font-size:18px;">
                    📄
                </div>
                <div>
                    <div style="font-size:14px;font-weight:700;color:#fff;">Upload Dokumen ke OPD</div>
                    <div style="font-size:11px;color:#71717a;">Kirim dokumen hasil penilaian ke OPD tujuan</div>
                </div>
            </div>
            <button type="button"
                    onclick="tutupModalUpload()"
                    style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
                           border-radius:8px;width:30px;height:30px;color:#a1a1aa;
                           font-size:16px;cursor:pointer;display:flex;align-items:center;
                           justify-content:center;flex-shrink:0;">
                ✕
            </button>
        </div>

        {{-- Form --}}
        <form id="formUploadDokumen"
              action="{{ route('dokumen.hasil.upload') }}"
              method="POST"
              enctype="multipart/form-data"
              style="padding:24px;display:flex;flex-direction:column;gap:16px;">
            @csrf

            {{-- Pilih OPD --}}
            <div>
                <label style="font-size:11px;font-weight:600;color:#a1a1aa;
                              text-transform:uppercase;letter-spacing:.5px;
                              display:block;margin-bottom:6px;">
                    Perangkat Daerah Tujuan <span style="color:#f87171;">*</span>
                </label>
                <select name="perangkat_daerah_id" required
                        style="width:100%;background:rgba(255,255,255,.05);
                               border:1px solid rgba(255,255,255,.12);border-radius:10px;
                               padding:10px 14px;color:#fff;font-size:13px;outline:none;
                               cursor:pointer;box-sizing:border-box;">
                    <option value="" style="background:#1a1a24;">-- Pilih OPD --</option>
                    @foreach($listOpd as $opd)
                        <option value="{{ $opd->id }}" style="background:#1a1a24;">
                            {{ $opd->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Judul --}}
            <div>
                <label style="font-size:11px;font-weight:600;color:#a1a1aa;
                              text-transform:uppercase;letter-spacing:.5px;
                              display:block;margin-bottom:6px;">
                    Judul Dokumen <span style="color:#f87171;">*</span>
                </label>
                <input type="text" name="judul" required maxlength="200"
                       placeholder="cth: Hasil LKE AKIP 2024"
                       style="width:100%;background:rgba(255,255,255,.05);
                              border:1px solid rgba(255,255,255,.12);border-radius:10px;
                              padding:10px 14px;color:#fff;font-size:13px;
                              outline:none;box-sizing:border-box;"
                       onfocus="this.style.borderColor='rgba(99,102,241,.5)'"
                       onblur="this.style.borderColor='rgba(255,255,255,.12)'">
            </div>

            {{-- Deskripsi --}}
            <div>
                <label style="font-size:11px;font-weight:600;color:#a1a1aa;
                              text-transform:uppercase;letter-spacing:.5px;
                              display:block;margin-bottom:6px;">
                    Deskripsi <span style="color:#52525b;font-weight:400;">(opsional)</span>
                </label>
                <textarea name="deskripsi" maxlength="500" rows="2"
                          placeholder="Keterangan singkat tentang dokumen..."
                          style="width:100%;background:rgba(255,255,255,.05);
                                 border:1px solid rgba(255,255,255,.12);border-radius:10px;
                                 padding:10px 14px;color:#fff;font-size:13px;
                                 outline:none;resize:vertical;box-sizing:border-box;"
                          onfocus="this.style.borderColor='rgba(99,102,241,.5)'"
                          onblur="this.style.borderColor='rgba(255,255,255,.12)'"></textarea>
            </div>

            {{-- Tahun --}}
            <div>
                <label style="font-size:11px;font-weight:600;color:#a1a1aa;
                              text-transform:uppercase;letter-spacing:.5px;
                              display:block;margin-bottom:6px;">
                    Tahun <span style="color:#f87171;">*</span>
                </label>
                <select name="tahun" required
                        style="width:100%;background:rgba(255,255,255,.05);
                               border:1px solid rgba(255,255,255,.12);border-radius:10px;
                               padding:10px 14px;color:#fff;font-size:13px;
                               outline:none;cursor:pointer;box-sizing:border-box;">
                    @foreach($listTahun as $t)
                        <option value="{{ $t }}"
                                {{ $tahun == $t ? 'selected' : '' }}
                                style="background:#1a1a24;">
                            {{ $t }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- File PDF --}}
            <div>
                <label style="font-size:11px;font-weight:600;color:#a1a1aa;
                              text-transform:uppercase;letter-spacing:.5px;
                              display:block;margin-bottom:6px;">
                    File PDF <span style="color:#f87171;">*</span>
                    <span style="color:#52525b;font-weight:400;">(maks. 5MB)</span>
                </label>
                <input type="file" name="file" accept=".pdf" required
                       id="inputFileDokumen"
                       style="display:none;"
                       onchange="updateLabelFile(this)">
                <div onclick="document.getElementById('inputFileDokumen').click()"
                     id="dropzoneFile"
                     style="width:100%;background:rgba(255,255,255,.05);
                            border:2px dashed rgba(255,255,255,.15);border-radius:10px;
                            padding:20px;text-align:center;cursor:pointer;
                            transition:border-color .2s;box-sizing:border-box;">
                    <div style="font-size:24px;margin-bottom:6px;">📎</div>
                    <div id="labelFileDokumen"
                         style="font-size:12px;color:#a1a1aa;">
                        Klik untuk pilih file PDF...
                    </div>
                    <div style="font-size:10px;color:#52525b;margin-top:4px;">
                        Format: PDF | Maksimal: 5MB
                    </div>
                </div>
            </div>

            {{-- Progress bar (hidden by default) --}}
            <div id="uploadProgress" style="display:none;">
                <div style="font-size:11px;color:#a1a1aa;margin-bottom:6px;">Mengupload...</div>
                <div style="background:rgba(255,255,255,.08);border-radius:999px;height:6px;overflow:hidden;">
                    <div id="progressBar"
                         style="height:100%;width:0%;
                                background:linear-gradient(90deg,#6366f1,#8b5cf6);
                                border-radius:999px;transition:width .3s;"></div>
                </div>
            </div>

            {{-- Tombol --}}
            <div style="display:flex;gap:10px;margin-top:4px;">
                <button type="button"
                        onclick="tutupModalUpload()"
                        style="flex:1;padding:11px;border-radius:10px;
                               background:rgba(255,255,255,.06);
                               border:1px solid rgba(255,255,255,.1);
                               color:#a1a1aa;font-weight:600;font-size:13px;cursor:pointer;">
                    Batal
                </button>
                <button type="submit"
                        id="btnSubmitUpload"
                        style="flex:2;padding:11px;border-radius:10px;
                               background:linear-gradient(135deg,#6366f1,#4f46e5);
                               border:none;color:#fff;font-weight:700;
                               font-size:13px;cursor:pointer;
                               box-shadow:0 4px 14px rgba(99,102,241,.35);"
                        onmouseover="this.style.opacity='.9'"
                        onmouseout="this.style.opacity='1'">
                    🚀 Upload Dokumen
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Tutup modal
    function tutupModalUpload() {
        document.getElementById('modalUploadDokumen').style.display = 'none';
        // Reset form
        document.getElementById('formUploadDokumen').reset();
        document.getElementById('labelFileDokumen').textContent = 'Klik untuk pilih file PDF...';
        document.getElementById('uploadProgress').style.display = 'none';
        document.getElementById('progressBar').style.width = '0%';
        document.getElementById('btnSubmitUpload').disabled = false;
        document.getElementById('btnSubmitUpload').textContent = '🚀 Upload Dokumen';
    }

    // Tutup modal saat klik backdrop
    document.getElementById('modalUploadDokumen').addEventListener('click', function(e) {
        if (e.target === this) tutupModalUpload();
    });

    // Update label nama file
    function updateLabelFile(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const sizeMB = (file.size / 1024 / 1024).toFixed(2);
            document.getElementById('labelFileDokumen').textContent = `📄 ${file.name} (${sizeMB} MB)`;
            document.getElementById('labelFileDokumen').style.color = '#34d399';
        }
    }

    // Submit dengan feedback loading
    document.getElementById('formUploadDokumen').addEventListener('submit', function(e) {
        const btn = document.getElementById('btnSubmitUpload');
        btn.disabled = true;
        btn.textContent = '⏳ Mengupload...';
        document.getElementById('uploadProgress').style.display = 'block';

        // Simulasi progress (karena tidak pakai AJAX)
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            document.getElementById('progressBar').style.width = progress + '%';
        }, 300);
    });
</script>

@endsection