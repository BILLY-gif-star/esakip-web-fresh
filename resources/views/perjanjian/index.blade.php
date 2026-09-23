@extends('layouts.app')
@section('title', $cfg['label'])
@section('page-title', 'Perjanjian Kinerja')
@section('page-sub', $cfg['label'])

@section('topbar-actions')
  @if(session('user.role') === 'admin')
    <button onclick="document.getElementById('modalUpload').classList.add('open')"
            style="display:inline-flex;align-items:center;gap:8px;
                   background:linear-gradient(135deg,#f59e0b,#d97706);
                   padding:9px 20px;border-radius:40px;border:none;
                   color:#fff;font-weight:600;font-size:12px;cursor:pointer;
                   box-shadow:0 4px 14px rgba(245,158,11,.35);transition:all .2s;"
            onmouseover="this.style.transform='translateY(-2px)'"
            onmouseout="this.style.transform='translateY(0)'">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
        <polyline points="7 10 12 15 17 10"/>
        <line x1="12" y1="15" x2="12" y2="3"/>
      </svg>
      Upload Template
    </button>
  @endif
@endsection

@section('content')
<style>
  /* ═══════════════════════════════════════════
     DARK THEME — konsisten dengan layout app
  ═══════════════════════════════════════════ */
  :root {
    --dk-card:    #1a1a24;
    --dk-card2:   #16161f;
    --dk-border:  rgba(255,255,255,.08);
    --dk-border2: rgba(255,255,255,.12);
    --txt-1: #f4f4f5;
    --txt-2: #a1a1aa;
    --txt-3: #71717a;
    --accent: #6366f1;
    --accent2: #4f46e5;
    --ok:    #10b981;
    --ok2:   #34d399;
    --warn:  #f59e0b;
    --warn2: #fbbf24;
    --err:   #ef4444;
    --err2:  #f87171;
  }

  @keyframes fadeUp {
    from { opacity:0; transform:translateY(16px); }
    to   { opacity:1; transform:translateY(0); }
  }

  .pk-card {
    background: var(--dk-card);
    border: 1px solid var(--dk-border);
    border-radius: 20px;
    overflow: hidden;
    margin-bottom: 24px;
    animation: fadeUp .35s ease both;
  }

  .pk-card-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--dk-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
  }

  .pk-card-body { padding: 22px 24px; }

  /* ── Badge ───────────────────────────────── */
  .badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    flex-shrink: 0;
  }
  .badge-ok      { background:rgba(16,185,129,.15); border:1px solid rgba(16,185,129,.3); color:#34d399; }
  .badge-warn    { background:rgba(245,158,11,.15);  border:1px solid rgba(245,158,11,.3);  color:#fbbf24; }
  .badge-err     { background:rgba(239,68,68,.15);   border:1px solid rgba(239,68,68,.3);   color:#f87171; }
  .badge-neutral { background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); color:#a1a1aa; }

  /* ── Info alert ──────────────────────────── */
  .pk-info {
    background: rgba(99,102,241,.08);
    border: 1px solid rgba(99,102,241,.2);
    border-radius: 14px;
    padding: 14px 18px;
    margin-bottom: 22px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-size: 13px;
    color: #a1a1aa;
    animation: fadeUp .3s ease;
  }

  .pk-alert {
    border-radius: 14px;
    padding: 13px 18px;
    margin-bottom: 18px;
    font-size: 13px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
  }
  .pk-alert.ok   { background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.25); color:#34d399; }
  .pk-alert.warn { background:rgba(245,158,11,.1);  border:1px solid rgba(245,158,11,.25);  color:#fbbf24; }
  .pk-alert.err  { background:rgba(239,68,68,.1);   border:1px solid rgba(239,68,68,.25);   color:#f87171; }

  /* ── Buttons ─────────────────────────────── */
  .btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    border: none;
    transition: all .2s;
    white-space: nowrap;
  }
  .btn:hover { text-decoration: none; }

  .btn-accent  { background:linear-gradient(135deg,var(--accent),var(--accent2)); color:#fff; }
  .btn-accent:hover { opacity:.85; color:#fff; }

  .btn-ok      { background:rgba(16,185,129,.15); border:1px solid rgba(16,185,129,.3); color:#34d399; }
  .btn-ok:hover{ background:rgba(16,185,129,.25); }

  .btn-warn    { background:rgba(245,158,11,.15); border:1px solid rgba(245,158,11,.3); color:#fbbf24; }
  .btn-warn:hover { background:rgba(245,158,11,.25); }

  .btn-err     { background:rgba(239,68,68,.12); border:1px solid rgba(239,68,68,.25); color:#f87171; }
  .btn-err:hover { background:rgba(239,68,68,.2); }

  .btn-ghost   { background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); color:#a1a1aa; }
  .btn-ghost:hover { background:rgba(255,255,255,.1); color:#fff; }

  .btn-disabled { opacity:.4; cursor:not-allowed; pointer-events:none; }

  /* ── Stats grid ──────────────────────────── */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 16px;
    margin-bottom: 22px;
  }

  .stat-card {
    background: var(--dk-card);
    border: 1px solid var(--dk-border);
    border-radius: 16px;
    padding: 18px;
    animation: fadeUp .35s ease both;
  }

  .stat-card:nth-child(1) { animation-delay:.05s }
  .stat-card:nth-child(2) { animation-delay:.1s }
  .stat-card:nth-child(3) { animation-delay:.15s }
  .stat-card:nth-child(4) { animation-delay:.2s }

  /* ── Table ───────────────────────────────── */
  .pk-table-wrap {
    overflow-x: auto;
    border-radius: 20px;
    border: 1px solid var(--dk-border);
    animation: fadeUp .4s ease;
  }

  .pk-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 820px;
  }

  .pk-table thead th {
    background: #141420;
    padding: 13px 16px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--txt-3);
    border-bottom: 1px solid var(--dk-border);
    text-align: left;
    white-space: nowrap;
  }

  .pk-table tbody td {
    padding: 13px 16px;
    font-size: 13px;
    color: var(--txt-2);
    border-bottom: 1px solid rgba(255,255,255,.04);
    vertical-align: middle;
  }

  .pk-table tbody tr:hover td { background: rgba(255,255,255,.025); }
  .pk-table tbody tr:last-child td { border-bottom: none; }

  /* ── Upload area ─────────────────────────── */
  .upload-area {
    background: rgba(255,255,255,.03);
    border: 2px dashed rgba(255,255,255,.1);
    border-radius: 14px;
    padding: 22px;
    transition: border-color .2s;
  }
  .upload-area:hover { border-color: rgba(99,102,241,.4); }

  /* ── Form controls ───────────────────────── */
  .pk-input, .pk-select, .pk-textarea {
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
  .pk-input:focus, .pk-select:focus, .pk-textarea:focus {
    border-color: rgba(99,102,241,.5);
  }
  .pk-input::placeholder, .pk-textarea::placeholder { color: #52525b; }
  .pk-select option { background: #1a1a24; }
  .pk-label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: var(--txt-3);
    text-transform: uppercase;
    letter-spacing: .4px;
    margin-bottom: 7px;
  }
  .pk-field { margin-bottom: 16px; }

  /* ── Modal ───────────────────────────────── */
  .pk-modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.65);
    backdrop-filter: blur(5px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }
  .pk-modal.open { display: flex; }

  .pk-modal-box {
    background: #16161f;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 22px;
    width: 100%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 24px 64px rgba(0,0,0,.6);
    animation: fadeUp .25s ease;
  }

  .pk-modal-head {
    padding: 20px 24px 18px;
    border-bottom: 1px solid rgba(255,255,255,.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    background: #16161f;
    z-index: 1;
  }

  .pk-modal-body { padding: 24px; }
  .pk-modal-foot {
    padding: 16px 24px;
    border-top: 1px solid rgba(255,255,255,.08);
    display: flex;
    justify-content: flex-end;
    gap: 10px;
  }

  /* ── File icon ───────────────────────────── */
  .file-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(99,102,241,.15);
    border: 1px solid rgba(99,102,241,.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
  }

  .empty-state {
    text-align: center;
    padding: 56px 20px;
  }

  /* ── Flash ───────────────────────────────── */
  .pk-flash {
    border-radius: 14px;
    padding: 13px 18px;
    margin-bottom: 20px;
    font-size: 13px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: fadeUp .3s ease;
  }
  .pk-flash.ok  { background:rgba(16,185,129,.12); border:1px solid rgba(16,185,129,.3); color:#34d399; }
  .pk-flash.err { background:rgba(239,68,68,.12);  border:1px solid rgba(239,68,68,.3);  color:#f87171; }
</style>


{{-- Info bar --}}
<div class="pk-info">
  <span style="font-size:18px;flex-shrink:0;">ℹ️</span>
  <span>
    @if(session('user.role') === 'admin')
      Upload template <strong style="color:#fff;">{{ $cfg['label'] }}</strong> agar dapat diunduh Operator.
      Pantau dan review dokumen yang dikembalikan oleh masing-masing OPD.
    @else
      Download template dari Admin, isi dan lengkapi, lalu upload kembali untuk direview.
      Status dokumen Anda akan diperbarui setelah Admin melakukan review.
    @endif
  </span>
</div>

{{-- Filter Tahun --}}
<div class="pk-card" style="margin-bottom:20px;">
    <div class="pk-card-header">
        <div class="pk-card-title">📅 Pilih Tahun</div>
    </div>
    <div class="pk-card-body">
        <form method="GET" action="{{ url()->current() }}" class="filter-form">
            <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                <div style="min-width:150px;">
                    <label class="pk-label">Tahun</label>
                    <select name="tahun" class="pk-select" onchange="this.form.submit()">
                        @for($i = date('Y'); $i >= date('Y')-5; $i--)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                Tahun {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div style="margin-top:18px;">
                    <span class="badge badge-neutral">
                        📂 Menampilkan data tahun {{ $tahun }}
                    </span>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     SECTION: TEMPLATE DARI ADMIN
══════════════════════════════════════════════ --}}
<div class="pk-card">
  <div class="pk-card-header">
    <div>
      <div style="font-size:15px;font-weight:700;color:var(--txt-1);">
        📄 Template {{ $cfg['label'] }}
      </div>
      <div style="font-size:12px;color:var(--txt-3);margin-top:3px;">Tahun {{ $tahun }}</div>
    </div>
    @if($template)
      <span class="badge badge-ok">✓ Tersedia</span>
    @else
      <span class="badge badge-neutral">✗ Belum ada</span>
    @endif
  </div>

  <div class="pk-card-body">
    @if($template)
      @php
        $tplPath   = storage_path('app/templates/' . $template->nama_file);
        $tplExists = file_exists($tplPath);
        $tplExt    = strtolower(pathinfo($template->nama_file, PATHINFO_EXTENSION));
        $tplIcon   = match($tplExt) { 'pdf' => '📄', 'docx','doc' => '📝', 'xlsx','xls' => '📊', default => '📁' };
      @endphp

      @if(!$tplExists)
        <div class="pk-alert err" style="margin-bottom:18px;">
          <span>⚠️</span>
          <span>File <strong>{{ $template->nama_file }}</strong> tidak ditemukan di server. Silakan upload ulang template.</span>
        </div>
      @endif

      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
        <div style="display:flex;align-items:center;gap:14px;">
          <div class="file-icon">{{ $tplIcon }}</div>
          <div>
            <div style="font-size:14px;font-weight:600;color:var(--txt-1);">{{ $template->nama_file }}</div>
            <div style="font-size:11px;color:var(--txt-3);margin-top:3px;">
              Diupload: {{ \Carbon\Carbon::parse($template->updated_at)->format('d M Y H:i') }}
            </div>
          </div>
        </div>

        <div style="display:flex;gap:8px;flex-wrap:wrap;">
          @if($tplExists)
            @if($tplExt === 'pdf')
              <a href="{{ route('perjanjian.preview.template', $template->id) }}" target="_blank" class="btn btn-accent">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
                Lihat
              </a>
            @else
              <a href="{{ route('perjanjian.download.template', $template->id) }}" class="btn btn-accent" download>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Download
              </a>
            @endif
          @endif

          @if(session('user.role') === 'admin')
            <button class="btn btn-ghost" onclick="document.getElementById('modalUpload').classList.add('open')">
              🔄 Ganti
            </button>
            @if($tplExists)
              <form method="POST" action="{{ route('perjanjian.hapus.template', $template->id) }}"
                    onsubmit="return confirm('Hapus template {{ addslashes($template->nama_file) }}?')"
                    style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-err">🗑 Hapus</button>
              </form>
            @endif
          @endif
        </div>
      </div>

    @else
      <div class="empty-state">
        <div style="font-size:44px;margin-bottom:14px;opacity:.4;">📋</div>
        <div style="font-size:15px;font-weight:600;color:var(--txt-1);margin-bottom:8px;">Template belum tersedia</div>
        <div style="font-size:12px;color:var(--txt-3);margin-bottom:20px;">
          @if(session('user.role') === 'admin')
            Klik Upload Template untuk menambahkan.
          @else
            Silakan hubungi Admin untuk upload template.
          @endif
        </div>
        @if(session('user.role') === 'admin')
          <button class="btn btn-warn" onclick="document.getElementById('modalUpload').classList.add('open')">
            📤 Upload Template
          </button>
        @endif
      </div>
    @endif
  </div>
</div>

{{-- ══════════════════════════════════════════════
     SECTION: UPLOAD DOKUMEN (OPERATOR)
══════════════════════════════════════════════ --}}
@if(session('user.role') === 'operator')
@if($isDual)
  {{-- ⭐ DUAL UPLOAD — khusus Perjanjian Kinerja (Murni + Revisi) --}}
  <div class="pk-card" style="animation-delay:.1s;">
    <div class="pk-card-header">
      <div>
        <div style="font-size:15px;font-weight:700;color:var(--txt-1);">📤 Upload Dokumen Anda</div>
        <div style="font-size:12px;color:var(--txt-3);margin-top:3px;">
          {{ session('user.nama_daerah') ?? 'OPD Anda' }} — Tahun {{ $tahun }}
        </div>
      </div>
    </div>
    <div class="pk-card-body">

      {{-- Status ringkas dua dokumen --}}
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
        <div>
          <div style="font-size:12px;font-weight:700;color:var(--txt-1);margin-bottom:8px;">📄 Perjanjian Kinerja Murni</div>
          @if($dokMurni)
            <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);border-radius:12px;padding:12px 14px;">
              <div style="font-size:12px;font-weight:600;color:var(--txt-1);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $dokMurni->nama_file }}</div>
              <div style="font-size:10px;color:var(--txt-3);margin-top:4px;">
                Upload: {{ \Carbon\Carbon::parse($dokMurni->updated_at)->format('d M Y H:i') }}
                @if(!$dokMurni->file_exists) &nbsp;·&nbsp;<span style="color:#f87171;">⚠️ File tidak ditemukan</span> @endif
              </div>
              <div style="margin-top:8px;display:flex;align-items:center;gap:8px;">
                @if($dokMurni->status === 'disetujui') <span class="badge badge-ok">✓ Disetujui</span>
                @elseif($dokMurni->status === 'ditolak') <span class="badge badge-err">✗ Revisi</span>
                @else <span class="badge badge-warn">⏳ Menunggu</span> @endif
                @if($dokMurni->file_exists)
                  <a href="{{ route('perjanjian.lihat', $dokMurni->id) }}" target="_blank" class="btn btn-ghost" style="padding:4px 10px;">👁 Lihat</a>
                @endif
              </div>
              @if($dokMurni->status === 'ditolak' && $dokMurni->catatan_admin)
                <div style="margin-top:8px;font-size:11px;color:#f87171;">Catatan: <em>{{ $dokMurni->catatan_admin }}</em></div>
              @endif
            </div>
          @else
            <div style="padding:14px;text-align:center;background:rgba(255,255,255,.02);border:1px dashed rgba(255,255,255,.1);border-radius:12px;color:var(--txt-3);font-size:12px;">Belum diupload</div>
          @endif
        </div>

        <div>
          <div style="font-size:12px;font-weight:700;color:var(--txt-1);margin-bottom:8px;">📝 Perjanjian Kinerja Revisi</div>
          @if($dokRevisi)
            <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);border-radius:12px;padding:12px 14px;">
              <div style="font-size:12px;font-weight:600;color:var(--txt-1);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $dokRevisi->nama_file }}</div>
              <div style="font-size:10px;color:var(--txt-3);margin-top:4px;">
                Upload: {{ \Carbon\Carbon::parse($dokRevisi->updated_at)->format('d M Y H:i') }}
                @if(!$dokRevisi->file_exists) &nbsp;·&nbsp;<span style="color:#f87171;">⚠️ File tidak ditemukan</span> @endif
              </div>
              <div style="margin-top:8px;display:flex;align-items:center;gap:8px;">
                @if($dokRevisi->status === 'disetujui') <span class="badge badge-ok">✓ Disetujui</span>
                @elseif($dokRevisi->status === 'ditolak') <span class="badge badge-err">✗ Revisi</span>
                @else <span class="badge badge-warn">⏳ Menunggu</span> @endif
                @if($dokRevisi->file_exists)
                  <a href="{{ route('perjanjian.lihat', $dokRevisi->id) }}" target="_blank" class="btn btn-ghost" style="padding:4px 10px;">👁 Lihat</a>
                @endif
              </div>
              @if($dokRevisi->status === 'ditolak' && $dokRevisi->catatan_admin)
                <div style="margin-top:8px;font-size:11px;color:#f87171;">Catatan: <em>{{ $dokRevisi->catatan_admin }}</em></div>
              @endif
            </div>
          @else
            <div style="padding:14px;text-align:center;background:rgba(255,255,255,.02);border:1px dashed rgba(255,255,255,.1);border-radius:12px;color:var(--txt-3);font-size:12px;">Belum diupload</div>
          @endif
        </div>
      </div>

      {{-- Form upload dual --}}
      <div class="upload-area">
        <form method="POST" action="{{ route('perjanjian.upload.dual') }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="tahun" value="{{ $tahun }}">

          <div class="pk-field">
            <label class="pk-label">📄 File Perjanjian Kinerja Murni</label>
            <input type="file" name="file_murni" class="pk-input" accept=".pdf,.docx,.xlsx,.doc,.xls">
            <div style="font-size:10px;color:var(--txt-3);margin-top:6px;">Kosongkan jika tidak ingin mengganti file ini. Maks 5MB.</div>
          </div>

          <div class="pk-field">
            <label class="pk-label">📝 File Perjanjian Kinerja Revisi</label>
            <input type="file" name="file_revisi" class="pk-input" accept=".pdf,.docx,.xlsx,.doc,.xls">
            <div style="font-size:10px;color:var(--txt-3);margin-top:6px;">Kosongkan jika tidak ingin mengganti file ini. Maks 5MB.</div>
          </div>

          <div class="pk-field" style="margin-bottom:20px;">
            <label class="pk-label">📝 Keterangan <span style="color:var(--txt-3);font-weight:400;">(opsional)</span></label>
            <input type="text" name="keterangan" class="pk-input" placeholder="Contoh: Revisi setelah rapat pembahasan...">
          </div>

          <button type="submit"
                  style="width:100%;padding:12px;border-radius:12px;border:none;
                         background:linear-gradient(135deg,#6366f1,#4f46e5);
                         color:#fff;font-weight:700;font-size:14px;cursor:pointer;
                         box-shadow:0 4px 14px rgba(99,102,241,.35);transition:opacity .2s;"
                  onmouseover="this.style.opacity='.85'"
                  onmouseout="this.style.opacity='1'">
            📤 Upload Dokumen
          </button>
        </form>
      </div>

    </div>
  </div>
@else
  {{-- Upload single (jenis lain, tidak berubah) --}}
  <div class="pk-card" style="animation-delay:.1s;">
    <div class="pk-card-header">
      <div>
        <div style="font-size:15px;font-weight:700;color:var(--txt-1);">📤 Upload Dokumen Anda</div>
        <div style="font-size:12px;color:var(--txt-3);margin-top:3px;">
          {{ session('user.nama_daerah') ?? 'OPD Anda' }} — Tahun {{ $tahun }}
        </div>
      </div>
      @if($dokOpd)
        @if($dokOpd->status === 'disetujui')
          <span class="badge badge-ok">✓ Disetujui</span>
        @elseif($dokOpd->status === 'ditolak')
          <span class="badge badge-err">✗ Perlu Revisi</span>
        @else
          <span class="badge badge-warn">⏳ Menunggu</span>
        @endif
      @else
        <span class="badge badge-neutral">Belum diupload</span>
      @endif
    </div>

    <div class="pk-card-body">
      @php
        $opFileExists = false;
        if ($dokOpd && $dokOpd->nama_file) {
          $opFileExists = file_exists(storage_path('app/dokumen_opd/' . $dokOpd->nama_file));
        }
      @endphp

      @if($dokOpd && $dokOpd->status === 'ditolak')
        <div class="pk-alert err">
          <span>❌</span>
          <span>
            <strong>Dokumen perlu direvisi.</strong><br>
            @if($dokOpd->catatan_admin)
              <span style="font-size:12px;">Catatan Admin: <em>{{ $dokOpd->catatan_admin }}</em></span>
            @endif
          </span>
        </div>
      @elseif($dokOpd && $dokOpd->status === 'disetujui')
        <div class="pk-alert ok">
          <span>✅</span>
          <span><strong>Dokumen telah disetujui!</strong> Tidak perlu mengupload ulang.</span>
        </div>
      @elseif($dokOpd && $dokOpd->status === 'menunggu')
        <div class="pk-alert warn">
          <span>⏳</span>
          <span>Dokumen sudah diupload dan sedang menunggu review dari Admin.</span>
        </div>
      @endif

      @if($dokOpd && $dokOpd->status !== 'disetujui')
        <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);
                    border-radius:12px;padding:14px 16px;margin-bottom:18px;
                    display:flex;align-items:center;gap:12px;">
          <span style="font-size:22px;">📄</span>
          <div style="flex:1;min-width:0;">
            <div style="font-size:13px;font-weight:600;color:var(--txt-1);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
              {{ $dokOpd->nama_file }}
            </div>
            <div style="font-size:11px;color:var(--txt-3);margin-top:2px;">
              Upload: {{ \Carbon\Carbon::parse($dokOpd->updated_at)->format('d M Y H:i') }}
              @if(!$opFileExists)
                &nbsp;·&nbsp;<span style="color:#f87171;">⚠️ File tidak ditemukan</span>
              @endif
            </div>
          </div>
          @if($opFileExists)
            <a href="{{ route('perjanjian.lihat', $dokOpd->id) }}" target="_blank" class="btn btn-ghost">
              👁 Lihat
            </a>
          @endif
        </div>
      @endif

      @if(!$dokOpd || $dokOpd->status !== 'disetujui')
        <div class="upload-area">
          <form method="POST" action="{{ route('perjanjian.upload.dokumen') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="jenis" value="{{ $cfg['opd'] }}">
            <input type="hidden" name="tahun" value="{{ $tahun }}">

            <div class="pk-field">
              <label class="pk-label">📁 Pilih File Dokumen <span style="color:#f87171;">*</span></label>
              <input type="file" name="file" class="pk-input" accept=".pdf,.docx,.xlsx,.doc,.xls" required>
              <div style="font-size:10px;color:var(--txt-3);margin-top:6px;">Format: PDF, DOC, DOCX, XLS, XLSX — Maksimal 5MB</div>
            </div>

            <div class="pk-field" style="margin-bottom:20px;">
              <label class="pk-label">📝 Keterangan <span style="color:var(--txt-3);font-weight:400;">(opsional)</span></label>
              <input type="text" name="keterangan" class="pk-input"
                     placeholder="Contoh: Dokumen sudah ditandatangani..."
                     value="{{ $dokOpd->keterangan ?? '' }}">
            </div>

            <button type="submit"
                    style="width:100%;padding:12px;border-radius:12px;border:none;
                           background:linear-gradient(135deg,#6366f1,#4f46e5);
                           color:#fff;font-weight:700;font-size:14px;cursor:pointer;
                           box-shadow:0 4px 14px rgba(99,102,241,.35);transition:opacity .2s;"
                    onmouseover="this.style.opacity='.85'"
                    onmouseout="this.style.opacity='1'">
              📤 {{ $dokOpd ? 'Upload Ulang / Revisi' : 'Upload Dokumen' }}
            </button>
          </form>
        </div>
      @endif
    </div>
  </div>
@endif
@endif

{{-- ══════════════════════════════════════════════
     SECTION: TABEL SEMUA OPD (ADMIN) — jenis non-dual
══════════════════════════════════════════════ --}}
@if(session('user.role') === 'admin' && !$isDual)

  {{-- Stats --}}
  @if($listOpd->count() > 0)
  <div class="stats-grid">
    @php
      $cntMenunggu  = $listOpd->where('status','menunggu')->count();
      $cntDisetujui = $listOpd->where('status','disetujui')->count();
      $cntDitolak   = $listOpd->where('status','ditolak')->count();
    @endphp
    <div class="stat-card">
      <div style="font-size:26px;margin-bottom:8px;">⏳</div>
      <div style="font-size:28px;font-weight:800;color:#fbbf24;">{{ $cntMenunggu }}</div>
      <div style="font-size:12px;color:var(--txt-3);margin-top:3px;">Menunggu Review</div>
    </div>
    <div class="stat-card">
      <div style="font-size:26px;margin-bottom:8px;">✅</div>
      <div style="font-size:28px;font-weight:800;color:#34d399;">{{ $cntDisetujui }}</div>
      <div style="font-size:12px;color:var(--txt-3);margin-top:3px;">Disetujui</div>
    </div>
    <div class="stat-card">
      <div style="font-size:26px;margin-bottom:8px;">❌</div>
      <div style="font-size:28px;font-weight:800;color:#f87171;">{{ $cntDitolak }}</div>
      <div style="font-size:12px;color:var(--txt-3);margin-top:3px;">Perlu Revisi</div>
    </div>
    <div class="stat-card">
      <div style="font-size:26px;margin-bottom:8px;">📂</div>
      <div style="font-size:28px;font-weight:800;color:#a1a1aa;">{{ $listOpd->count() }}</div>
      <div style="font-size:12px;color:var(--txt-3);margin-top:3px;">Total Upload</div>
    </div>
  </div>
  @endif

  {{-- Tabel --}}
  <div class="pk-table-wrap">
    <table class="pk-table">
      <thead>
        <tr>
          <th>OPD / Operator</th>
          <th>File</th>
          <th>Keterangan</th>
          <th>Tgl Upload</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($listOpd as $d)
        @php
          $fp      = storage_path('app/dokumen_opd/' . $d->nama_file);
          $fExists = file_exists($fp);
          $fExt    = strtolower(pathinfo($d->nama_file, PATHINFO_EXTENSION));
          $fIcon   = match($fExt) { 'pdf' => '📄', 'docx','doc' => '📝', 'xlsx','xls' => '📊', default => '📁' };
        @endphp
        <tr>
          {{-- OPD --}}
          <td>
            <div style="font-weight:600;color:var(--txt-1);">{{ $d->nama_opd ?? '—' }}</div>
            <div style="font-size:11px;color:var(--txt-3);">{{ $d->nama_user ?? '—' }}</div>
          </td>

          {{-- File --}}
          <td>
            <div style="display:flex;align-items:center;gap:8px;">
              <span style="font-size:18px;flex-shrink:0;">{{ $fIcon }}</span>
              <div style="min-width:0;">
                <div style="font-size:12px;font-weight:500;color:var(--txt-1);
                            overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px;">
                  {{ $d->nama_file }}
                </div>
                <div style="font-size:10px;color:var(--txt-3);">{{ strtoupper($fExt) }}</div>
              </div>
            </div>
            @if(!$fExists)
              <span style="display:inline-block;margin-top:4px;background:rgba(239,68,68,.15);
                           color:#f87171;font-size:10px;padding:2px 8px;border-radius:8px;">
                ⚠️ File tidak ada
              </span>
            @endif
          </td>

          {{-- Keterangan --}}
          <td style="max-width:160px;">
            <span style="font-size:12px;color:var(--txt-3);display:block;
                         overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
              {{ $d->keterangan ?: '—' }}
            </span>
          </td>

          {{-- Tgl Upload --}}
          <td style="white-space:nowrap;">
            <div style="font-size:12px;">{{ \Carbon\Carbon::parse($d->updated_at)->format('d/m/Y') }}</div>
            <div style="font-size:10px;color:var(--txt-3);">{{ \Carbon\Carbon::parse($d->updated_at)->format('H:i') }}</div>
          </td>

          {{-- Status --}}
          <td>
            @if($d->status === 'disetujui')
              <span class="badge badge-ok">✓ Disetujui</span>
            @elseif($d->status === 'ditolak')
              <span class="badge badge-err">✗ Revisi</span>
            @else
              <span class="badge badge-warn">⏳ Menunggu</span>
            @endif
            @if($d->catatan_admin)
              <div style="font-size:10px;color:var(--txt-3);margin-top:4px;max-width:120px;
                          overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                   title="{{ $d->catatan_admin }}">
                💬 {{ $d->catatan_admin }}
              </div>
            @endif
          </td>

          {{-- Aksi --}}
          <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">

              {{-- Preview / Download --}}
              @if($fExists)
                @if(in_array($fExt, ['doc','docx']))
                  <a href="{{ route('perjanjian.preview', $d->id) }}" class="btn btn-accent" download>
                    ⬇ DL
                  </a>
                @else
                  <a href="{{ route('perjanjian.preview', $d->id) }}" target="_blank" class="btn btn-accent">
                    👁 Lihat
                  </a>
                @endif
              @else
                <span class="btn btn-ghost btn-disabled">👁 Lihat</span>
              @endif

              {{-- Edit --}}
              <button class="btn btn-ghost"
                      onclick="openEditModal(
                        {{ $d->id }},
                        '{{ addslashes($d->nama_opd ?? '') }}',
                        '{{ addslashes($d->keterangan ?? '') }}',
                        '{{ $d->status }}',
                        '{{ addslashes($d->catatan_admin ?? '') }}'
                      )">
                ✏️ Edit
              </button>

              {{-- Setujui --}}
              @if($d->status !== 'disetujui')
                <form method="POST" action="{{ route('perjanjian.review', $d->id) }}" style="display:inline;"
                      onsubmit="return confirm('Setujui dokumen {{ addslashes($d->nama_opd ?? '') }}?')">
                  @csrf
                  <input type="hidden" name="status" value="disetujui">
                  <input type="hidden" name="catatan" value="">
                  <button type="submit" class="btn btn-ok">✓ Setujui</button>
                </form>
              @endif

              {{-- Tolak --}}
              @if($d->status !== 'ditolak')
                <form method="POST" action="{{ route('perjanjian.review', $d->id) }}" style="display:inline;"
                      onsubmit="return confirm('Tolak dokumen {{ addslashes($d->nama_opd ?? '') }}?')">
                  @csrf
                  <input type="hidden" name="status" value="ditolak">
                  <input type="hidden" name="catatan" value="">
                  <button type="submit" class="btn btn-err">✗ Tolak</button>
                </form>
              @endif

              {{-- Hapus --}}
              <form method="POST" action="{{ route('perjanjian.hapus', $d->id) }}" style="display:inline;"
                    onsubmit="return confirm('Yakin hapus dokumen {{ addslashes($d->nama_opd ?? '') }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-err">🗑</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6">
            <div class="empty-state">
              <div style="font-size:44px;margin-bottom:14px;opacity:.3;">📂</div>
              <div style="font-size:15px;font-weight:600;color:var(--txt-1);margin-bottom:6px;">Belum ada dokumen</div>
              <div style="font-size:12px;color:var(--txt-3);">Operator belum mengupload dokumen untuk tahun {{ $tahun }}</div>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endif

{{-- ══════════════════════════════════════════════
     SECTION: TABEL SEMUA OPD (ADMIN) — Dual (Murni & Revisi)
══════════════════════════════════════════════ --}}
@if(session('user.role') === 'admin' && $isDual)
<div class="pk-table-wrap">
  <table class="pk-table">
    <thead>
      <tr>
        <th>OPD / Operator</th>
        <th>Perjanjian Kinerja Murni</th>
        <th>Status Murni</th>
        <th>Perjanjian Kinerja Revisi</th>
        <th>Status Revisi</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($listOpdDual as $opdIdKey => $files)
        @php
          $murni  = $files->first(fn($f) => $f->sub_jenis === 'murni' || is_null($f->sub_jenis));
          $revisi = $files->first(fn($f) => $f->sub_jenis === 'revisi');
          $namaOpdRow = $murni->nama_opd ?? $revisi->nama_opd ?? '—';
        @endphp
        <tr>
          <td>
            <div style="font-weight:600;color:var(--txt-1);">{{ $namaOpdRow }}</div>
            <div style="font-size:11px;color:var(--txt-3);">{{ $murni->nama_user ?? $revisi->nama_user ?? '—' }}</div>
          </td>

          <td>
            @if($murni)
              <div style="font-size:12px;color:var(--txt-1);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px;">{{ $murni->nama_file }}</div>
              <div style="font-size:10px;color:var(--txt-3);">{{ \Carbon\Carbon::parse($murni->updated_at)->format('d/m/Y H:i') }}</div>
              @if(!$murni->file_exists)<span style="color:#f87171;font-size:10px;">⚠️ File tidak ada</span>@endif
            @else
              <span style="color:var(--txt-3);">Belum upload</span>
            @endif
          </td>
          <td>
            @if($murni)
              @if($murni->status === 'disetujui') <span class="badge badge-ok">✓ Disetujui</span>
              @elseif($murni->status === 'ditolak') <span class="badge badge-err">✗ Revisi</span>
              @else <span class="badge badge-warn">⏳ Menunggu</span> @endif
            @else <span class="badge badge-neutral">—</span> @endif
          </td>

          <td>
            @if($revisi)
              <div style="font-size:12px;color:var(--txt-1);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px;">{{ $revisi->nama_file }}</div>
              <div style="font-size:10px;color:var(--txt-3);">{{ \Carbon\Carbon::parse($revisi->updated_at)->format('d/m/Y H:i') }}</div>
              @if(!$revisi->file_exists)<span style="color:#f87171;font-size:10px;">⚠️ File tidak ada</span>@endif
            @else
              <span style="color:var(--txt-3);">Belum upload</span>
            @endif
          </td>
          <td>
            @if($revisi)
              @if($revisi->status === 'disetujui') <span class="badge badge-ok">✓ Disetujui</span>
              @elseif($revisi->status === 'ditolak') <span class="badge badge-err">✗ Revisi</span>
              @else <span class="badge badge-warn">⏳ Menunggu</span> @endif
            @else <span class="badge badge-neutral">—</span> @endif
          </td>

          <td>
            <div style="display:flex;flex-direction:column;gap:6px;">
              @if($murni)
                <div style="display:flex;gap:6px;align-items:center;">
                  <span style="font-size:9px;color:var(--txt-3);">Murni:</span>
                  @if($murni->file_exists)
                    <a href="{{ route('perjanjian.preview', $murni->id) }}" target="_blank" class="btn btn-ghost" style="padding:3px 8px;font-size:10px;">👁</a>
                  @endif
                  @if($murni->status !== 'disetujui')
                    <form method="POST" action="{{ route('perjanjian.review', $murni->id) }}" style="display:inline;">
                      @csrf<input type="hidden" name="status" value="disetujui"><input type="hidden" name="catatan" value="">
                      <button type="submit" class="btn btn-ok" style="padding:3px 8px;font-size:10px;">✓</button>
                    </form>
                  @endif
                  @if($murni->status !== 'ditolak')
                    <form method="POST" action="{{ route('perjanjian.review', $murni->id) }}" style="display:inline;">
                      @csrf<input type="hidden" name="status" value="ditolak"><input type="hidden" name="catatan" value="">
                      <button type="submit" class="btn btn-err" style="padding:3px 8px;font-size:10px;">✗</button>
                    </form>
                  @endif
                </div>
              @endif
              @if($revisi)
                <div style="display:flex;gap:6px;align-items:center;">
                  <span style="font-size:9px;color:var(--txt-3);">Revisi:</span>
                  @if($revisi->file_exists)
                    <a href="{{ route('perjanjian.preview', $revisi->id) }}" target="_blank" class="btn btn-ghost" style="padding:3px 8px;font-size:10px;">👁</a>
                  @endif
                  @if($revisi->status !== 'disetujui')
                    <form method="POST" action="{{ route('perjanjian.review', $revisi->id) }}" style="display:inline;">
                      @csrf<input type="hidden" name="status" value="disetujui"><input type="hidden" name="catatan" value="">
                      <button type="submit" class="btn btn-ok" style="padding:3px 8px;font-size:10px;">✓</button>
                    </form>
                  @endif
                  @if($revisi->status !== 'ditolak')
                    <form method="POST" action="{{ route('perjanjian.review', $revisi->id) }}" style="display:inline;">
                      @csrf<input type="hidden" name="status" value="ditolak"><input type="hidden" name="catatan" value="">
                      <button type="submit" class="btn btn-err" style="padding:3px 8px;font-size:10px;">✗</button>
                    </form>
                  @endif
                </div>
              @endif
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6">
            <div class="empty-state">
              <div style="font-size:44px;margin-bottom:14px;opacity:.3;">📂</div>
              <div style="font-size:15px;font-weight:600;color:var(--txt-1);margin-bottom:6px;">Belum ada dokumen</div>
              <div style="font-size:12px;color:var(--txt-3);">Operator belum mengupload dokumen untuk tahun {{ $tahun }}</div>
            </div>
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endif


{{-- ══════════════════════════════════════════════
     MODAL: UPLOAD TEMPLATE (admin)
══════════════════════════════════════════════ --}}
@if(session('user.role') === 'admin')
<div class="pk-modal" id="modalUpload">
  <div class="pk-modal-box">
    <div class="pk-modal-head">
      <div>
        <div style="font-size:15px;font-weight:700;color:var(--txt-1);">📤 Upload Template</div>
        <div style="font-size:11px;color:var(--txt-3);margin-top:2px;">{{ $cfg['label'] }} — Tahun {{ $tahun }}</div>
      </div>
      <button class="btn btn-ghost" style="padding:6px 10px;"
              onclick="document.getElementById('modalUpload').classList.remove('open')">✕</button>
    </div>
    <form method="POST" action="{{ route('perjanjian.upload.template') }}" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="jenis" value="{{ $cfg['key'] }}">
      <input type="hidden" name="tahun" value="{{ $tahun }}">
      <div class="pk-modal-body">
        <div class="upload-area" onclick="document.getElementById('tplFileInput').click()" style="cursor:pointer;">
          <div style="text-align:center;">
            <div style="font-size:32px;margin-bottom:10px;">📁</div>
            <div style="font-size:13px;color:var(--txt-2);margin-bottom:6px;" id="tplFileLabel">
              Klik untuk pilih file template
            </div>
            <div style="font-size:11px;color:var(--txt-3);">PDF, DOC, DOCX, XLS, XLSX — Maks 5MB</div>
          </div>
          <input type="file" id="tplFileInput" name="file"
                 accept=".pdf,.docx,.xlsx,.doc,.xls" required
                 style="display:none;"
                 onchange="document.getElementById('tplFileLabel').textContent = this.files[0]?.name ?? 'Pilih file template'">
        </div>
      </div>
      <div class="pk-modal-foot">
        <button type="button" class="btn btn-ghost"
                onclick="document.getElementById('modalUpload').classList.remove('open')">Batal</button>
        <button type="submit" class="btn btn-warn">📂 Upload Template</button>
      </div>
    </form>
  </div>
</div>
@endif


{{-- ══════════════════════════════════════════════
     MODAL: EDIT DOKUMEN (admin)
══════════════════════════════════════════════ --}}
<div class="pk-modal" id="modalEdit">
  <div class="pk-modal-box">
    <div class="pk-modal-head">
      <div>
        <div style="font-size:15px;font-weight:700;color:var(--txt-1);">✏️ Edit Dokumen</div>
        <div style="font-size:11px;color:var(--txt-3);margin-top:2px;" id="editInfo">—</div>
      </div>
      <button class="btn btn-ghost" style="padding:6px 10px;" onclick="closeEditModal()">✕</button>
    </div>
    <form method="POST" id="formEdit" enctype="multipart/form-data">
      @csrf @method('PUT')
      <div class="pk-modal-body">

        <div class="pk-field">
          <label class="pk-label">📝 Keterangan</label>
          <input type="text" name="keterangan" id="editKeterangan" class="pk-input"
                 placeholder="Keterangan dokumen...">
        </div>

        <div class="pk-field">
          <label class="pk-label">📊 Status</label>
          <select name="status" id="editStatus" class="pk-select">
            <option value="menunggu">⏳ Menunggu Review</option>
            <option value="disetujui">✅ Disetujui</option>
            <option value="ditolak">❌ Ditolak (Perlu Revisi)</option>
          </select>
        </div>

        <div class="pk-field">
          <label class="pk-label">💬 Catatan Admin</label>
          <textarea name="catatan" id="editCatatan" rows="2" class="pk-textarea"
                    placeholder="Catatan untuk operator..."></textarea>
        </div>

        <div class="pk-field" style="margin-bottom:0;">
          <label class="pk-label">📁 Ganti File <span style="color:var(--txt-3);font-weight:400;">(opsional)</span></label>
          <input type="file" name="file" class="pk-input" accept=".pdf,.docx,.xlsx,.doc,.xls">
          <div style="font-size:10px;color:var(--txt-3);margin-top:5px;">Kosongkan jika tidak ingin mengganti file</div>
        </div>
      </div>
      <div class="pk-modal-foot">
        <button type="button" class="btn btn-ghost" onclick="closeEditModal()">Batal</button>
        <button type="submit" class="btn btn-accent">💾 Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditModal(id, namaOpd, keterangan, status, catatan) {
  document.getElementById('formEdit').action = '/perjanjian/edit/' + id;
  document.getElementById('editInfo').textContent = 'OPD: ' + namaOpd;
  document.getElementById('editKeterangan').value = keterangan || '';
  document.getElementById('editStatus').value = status || 'menunggu';
  document.getElementById('editCatatan').value = catatan || '';
  document.getElementById('modalEdit').classList.add('open');
}

function closeEditModal() {
  document.getElementById('modalEdit').classList.remove('open');
}

// Tutup modal saat klik backdrop
document.querySelectorAll('.pk-modal').forEach(function(modal) {
  modal.addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
  });
});
</script>
@endsection