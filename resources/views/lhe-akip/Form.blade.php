@extends('layouts.app')

@section('title', $mode === 'edit' ? 'Edit LHE AKIP' : 'Buat LHE AKIP')
@section('page-title', $mode === 'edit' ? 'Edit LHE AKIP' : 'Buat LHE AKIP Baru')
@section('page-sub', 'Laporan Hasil Evaluasi Akuntabilitas Kinerja')

@section('content')

<form method="POST" action="{{ $mode === 'edit' ? route('lhe-akip.update', $lhe->id) : route('lhe-akip.store') }}" id="formLhe">
  @csrf
  @if($mode === 'edit') @method('PUT') @endif

  {{-- ══════════════════════════════════════════════════
       1. HEADER SURAT + PILIH OPD/TAHUN
  ══════════════════════════════════════════════════ --}}
  <div class="card">
    <div class="card-header">
      <div>
        <div class="card-title">📄 Header Surat</div>
        <div class="card-subtitle">Pilih OPD & tahun untuk memuat nilai hasil evaluasi secara otomatis</div>
      </div>
    </div>
    <div class="card-body">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

        <div class="form-group">
          <label class="form-label">OPD yang Dievaluasi *</label>
          <select name="perangkat_daerah_id" id="perangkat_daerah_id" class="form-control" required
                  {{ $mode === 'edit' ? 'disabled' : '' }}>
            <option value="">-- Pilih OPD --</option>
            @foreach($daftarOpd as $opd)
              <option value="{{ $opd->id }}" {{ old('perangkat_daerah_id', $lhe->perangkat_daerah_id) == $opd->id ? 'selected' : '' }}>
                {{ $opd->nama }}
              </option>
            @endforeach
          </select>
          @if($mode === 'edit')
            {{-- select disabled tidak ikut ter-submit, jadi kirim ulang via hidden --}}
            <input type="hidden" name="perangkat_daerah_id" value="{{ $lhe->perangkat_daerah_id }}">
            <div style="font-size:11px;color:var(--text-light);margin-top:4px;">OPD tidak bisa diubah setelah dibuat.</div>
          @endif
        </div>

        <div class="form-group">
          <label class="form-label">Tahun Evaluasi *</label>
          <input type="number" name="tahun_evaluasi" id="tahun_evaluasi" class="form-control" min="2020" max="2100"
                 value="{{ old('tahun_evaluasi', $lhe->tahun_evaluasi ?? now()->year) }}"
                 {{ $mode === 'edit' ? 'readonly' : '' }} required>
        </div>

        <div class="form-group">
          <label class="form-label">Nomor Surat *</label>
          <input type="text" name="nomor_surat" class="form-control" placeholder="000.8.6.3/ 17 /BO3.1"
                 value="{{ old('nomor_surat', $lhe->nomor_surat) }}" required>
        </div>

        <div class="form-group">
          <label class="form-label">Tanggal Surat *</label>
          <input type="date" name="tanggal_surat" class="form-control"
                 value="{{ old('tanggal_surat', optional($lhe->tanggal_surat)->format('Y-m-d')) }}" required>
        </div>

        <div class="form-group">
          <label class="form-label">Nomor SK Tim Evaluasi</label>
          <input type="text" name="nomor_sk_tim" class="form-control" placeholder="284/KEP/HK/2025"
                 value="{{ old('nomor_sk_tim', $lhe->nomor_sk_tim) }}">
        </div>

        <div class="form-group">
          <label class="form-label">Status Dokumen *</label>
          <select name="status" class="form-control" required>
            <option value="draft" {{ old('status', $lhe->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="final" {{ old('status', $lhe->status ?? '') === 'final' ? 'selected' : '' }}>Final</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Periode Pelaksanaan — Mulai</label>
          <input type="date" name="periode_mulai" class="form-control"
                 value="{{ old('periode_mulai', optional($lhe->periode_mulai)->format('Y-m-d')) }}">
        </div>

        <div class="form-group">
          <label class="form-label">Periode Pelaksanaan — Selesai</label>
          <input type="date" name="periode_selesai" class="form-control"
                 value="{{ old('periode_selesai', optional($lhe->periode_selesai)->format('Y-m-d')) }}">
        </div>

      </div>

      @if($mode === 'create')
        <button type="button" id="btnMuatNilai" class="btn btn-primary btn-sm" style="margin-top:8px;">
          🔄 Muat Nilai dari Hasil Evaluasi
        </button>
      @endif
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════
       2. NILAI PER KOMPONEN — READ ONLY, dari lke_penilaian
  ══════════════════════════════════════════════════ --}}
  <div class="card" id="cardNilai" style="{{ $nilaiAwal ? '' : 'display:none;' }}">
    <div class="card-header">
      <div>
        <div class="card-title">📊 Nilai Akuntabilitas Kinerja</div>
        <div class="card-subtitle">Diambil otomatis dari menu Evaluasi Kinerja → LKE AKIP (tidak bisa diedit di sini)</div>
      </div>
    </div>
    <div class="card-body">

      <div id="alertBelumAdaData" class="alert alert-warning" style="display:none;">
        ⚠️ OPD & tahun ini belum punya nilai di menu Evaluasi Kinerja. Nilai akan tampil 0 sampai penilaian LKE diisi.
      </div>

      <div class="table-wrap" style="margin-bottom:16px;">
        <table>
          <thead>
            <tr><th>Komponen</th><th>Bobot</th><th>Nilai (live)</th></tr>
          </thead>
          <tbody id="tbodyNilai">
            {{-- diisi via JS --}}
          </tbody>
        </table>
      </div>

      <div class="stats-grid" style="grid-template-columns:1fr 1fr;">
        <div class="stat-card">
          <span class="stat-icon">🎯</span>
          <div class="stat-value" id="preview-total">0.00</div>
          <div class="stat-label">Total Nilai Akuntabilitas Kinerja</div>
        </div>
        <div class="stat-card">
          <span class="stat-icon">🏆</span>
          <div class="stat-value" id="preview-kategori">-</div>
          <div class="stat-label">Kategori</div>
        </div>
      </div>

    </div>
  </div>

  {{-- ══════════════════════════════════════════════════
       3. URAIAN & CATATAN PER KOMPONEN
  ══════════════════════════════════════════════════ --}}
  @php
    $komponenList = [
      ['key' => 'perencanaan',       'label' => 'Perencanaan Kinerja'],
      ['key' => 'pengukuran',        'label' => 'Pengukuran Kinerja'],
      ['key' => 'pelaporan',         'label' => 'Pelaporan Kinerja'],
      ['key' => 'evaluasi_internal', 'label' => 'Evaluasi Akuntabilitas Kinerja Internal'],
    ];
  @endphp

  @foreach($komponenList as $idx => $item)
    @php [$key, $label] = [$item['key'], $item['label']]; @endphp
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title">📝 {{ $label }}</div>
          <div class="card-subtitle">Uraian naratif manual + catatan perbaikan otomatis dari komentar Evaluator</div>
        </div>
      </div>
      <div class="card-body">

        <div class="form-group">
          <label class="form-label">Uraian Naratif</label>
          <textarea name="uraian_{{ $key }}" class="form-control" rows="4"
                    placeholder="Jelaskan capaian dan kondisi komponen ini...">{{ old('uraian_' . $key, $lhe->{'uraian_' . $key}) }}</textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Poin Catatan Perbaikan <span style="color:var(--text-light);font-weight:400;">(otomatis dari komentar Evaluator — centang yang ingin ditampilkan saat cetak)</span></label>
          <div class="form-control" style="min-height:60px;background:var(--bg-hover);" id="catatan_preview_{{ $idx }}">
            <span style="color:var(--text-light);font-size:12px;">Klik "Muat Nilai dari Hasil Evaluasi" di atas untuk memuat catatan.</span>
          </div>
        </div>

        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label">Poin Rekomendasi (satu poin per baris)</label>
          @php
            $rekomendasiLama = old('rekomendasi_' . $key);
            if (!$rekomendasiLama && is_array($lhe->rekomendasi ?? null)) {
                $found = collect($lhe->rekomendasi)->firstWhere('komponen', $label);
                $rekomendasiLama = $found ? implode("\n", $found['poin']) : '';
            }
          @endphp
          <textarea name="rekomendasi_{{ $key }}" class="form-control" rows="3"
                    placeholder="Contoh:&#10;Membuat cascading kinerja berdasarkan Permenpan RB Nomor 89 Tahun 2021...">{{ $rekomendasiLama }}</textarea>
        </div>

      </div>
    </div>
  @endforeach

  {{-- ══════════════════════════════════════════════════
       4. PENUTUP & PENANDATANGAN
  ══════════════════════════════════════════════════ --}}
  <div class="card">
    <div class="card-header"><div class="card-title">✍️ Penutup & Penandatangan</div></div>
    <div class="card-body">

      <div class="form-group">
        <label class="form-label">Kalimat Penutup</label>
        <textarea name="penutup" class="form-control" rows="3">{{ old('penutup', $lhe->penutup) }}</textarea>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div class="form-group">
          <label class="form-label">Nama Penandatangan</label>
          <input type="text" name="nama_penandatangan" class="form-control"
                 value="{{ old('nama_penandatangan', $lhe->nama_penandatangan) }}">
        </div>
        <div class="form-group">
          <label class="form-label">Jabatan</label>
          <input type="text" name="jabatan_penandatangan" class="form-control"
                 placeholder="Asisten Administrasi Umum"
                 value="{{ old('jabatan_penandatangan', $lhe->jabatan_penandatangan) }}">
        </div>
        <div class="form-group">
          <label class="form-label">Pangkat / Golongan</label>
          <input type="text" name="pangkat_penandatangan" class="form-control"
                 placeholder="Pembina Utama Muda (IV/c)"
                 value="{{ old('pangkat_penandatangan', $lhe->pangkat_penandatangan) }}">
        </div>
        <div class="form-group">
          <label class="form-label">NIP</label>
          <input type="text" name="nip_penandatangan" class="form-control"
                 value="{{ old('nip_penandatangan', $lhe->nip_penandatangan) }}">
        </div>
      </div>

    </div>
  </div>

  <div class="card">
    <div class="card-body" style="display:flex;justify-content:flex-end;gap:8px;">
      <a href="{{ route('lhe-akip.index') }}" class="btn btn-outline">Batal</a>
      <button type="submit" class="btn btn-primary">
        💾 {{ $mode === 'edit' ? 'Perbarui LHE AKIP' : 'Simpan LHE AKIP' }}
      </button>
    </div>
  </div>

</form>

@endsection

@section('scripts')
<script>
const NILAI_PREVIEW_URL = '{{ route("lhe-akip.nilai-preview") }}';
const CATATAN_TERPILIH_LAMA = @json($lhe->catatan_terpilih ?? null); // ⭐ BARU

function escapeHtml(str) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

function tampilkanNilai(data) {
  var tbody = document.getElementById('tbodyNilai');
  tbody.innerHTML = '';
  data.rincian.forEach(function(k) {
    tbody.innerHTML += '<tr><td>' + k.nama + '</td><td>' + k.bobot.toFixed(2) + '</td><td><strong>' + k.nilai.toFixed(2) + '</strong></td></tr>';
  });
  document.getElementById('preview-total').textContent = data.total.toFixed(2);
  document.getElementById('preview-kategori').textContent = data.kategori;
  document.getElementById('cardNilai').style.display = '';
  document.getElementById('alertBelumAdaData').style.display = data.ada_data ? 'none' : 'block';

  // ⭐ DIUBAH — render sebagai checkbox, bukan list statis
  (data.catatan || []).forEach(function(poinList, idx) {
    var box = document.getElementById('catatan_preview_' + idx);
    if (!box) return;

    if (!poinList.length) {
      box.innerHTML = '<span style="color:var(--text-light);font-size:12px;">Belum ada komentar dari Evaluator untuk komponen ini.</span>';
      return;
    }

    // idx belum pernah diatur sebelumnya → default semua tercentang
    var idsTerpilih = (CATATAN_TERPILIH_LAMA && CATATAN_TERPILIH_LAMA[idx])
      ? CATATAN_TERPILIH_LAMA[idx].map(String)
      : null;

    box.innerHTML = poinList.map(function(p) {
      var checked = idsTerpilih === null ? true : idsTerpilih.indexOf(String(p.id)) !== -1;
      return '<label style="display:flex;align-items:flex-start;gap:8px;font-size:12.5px;margin-bottom:6px;cursor:pointer;">' +
             '<input type="checkbox" name="catatan_pilih[' + idx + '][]" value="' + p.id + '"' + (checked ? ' checked' : '') + ' style="margin-top:2px;flex-shrink:0;">' +
             '<span>' + escapeHtml(p.text) + '</span>' +
             '</label>';
    }).join('');
  });
}

function muatNilai() {
  var opdId = document.getElementById('perangkat_daerah_id').value;
  var tahun = document.getElementById('tahun_evaluasi').value;
  if (!opdId || !tahun) {
    alert('Pilih OPD dan tahun terlebih dahulu.');
    return;
  }
  fetch(NILAI_PREVIEW_URL + '?perangkat_daerah_id=' + opdId + '&tahun=' + tahun)
    .then(function(r) { return r.json(); })
    .then(tampilkanNilai)
    .catch(function(err) { console.error('Gagal muat nilai:', err); alert('Gagal memuat nilai.'); });
}

var btnMuat = document.getElementById('btnMuatNilai');
if (btnMuat) {
  btnMuat.addEventListener('click', muatNilai);
}

@if($nilaiAwal)
  // Mode edit: langsung tampilkan nilai yang sudah dihitung server
  document.addEventListener('DOMContentLoaded', function() {
    tampilkanNilai(@json($nilaiAwal));
  });
@endif
</script>
@endsection