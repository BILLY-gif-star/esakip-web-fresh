@extends('layouts.app')
@section('title','Dasbor E-SAKIPKU')
@section('page-title','DASBOR')

@section('content')
<style>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400;1,600&family=Cinzel:wght@400;600&family=Inter:wght@300;400;500;600;700;800&display=swap');

:root {
    --dark-card:   #1a1a24;
    --dark-border: rgba(255,255,255,.08);
    --accent-primary:  #6366f1;
    --accent-success:  #10b981;
    --accent-warning:  #f59e0b;
    --accent-danger:   #ef4444;
    --text-primary:    #ffffff;
    --text-secondary:  #a1a1aa;
    --text-muted:      #71717a;
}

body { background: linear-gradient(135deg,#0a0a0f 0%,#0f0f14 100%); font-family:'Inter',sans-serif; }

/* ── KPI Grid ── */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px,1fr));
    gap: 18px;
    margin-bottom: 28px;
}
.kpi-card {
    background: linear-gradient(145deg,#1e1e2e,#16161f);
    border-radius: 22px;
    padding: 22px 22px 20px;
    border: 1px solid rgba(255,255,255,.07);
    position: relative;
    overflow: hidden;
    transition: transform .25s, box-shadow .25s, border-color .25s;
    cursor: default;
}
.kpi-card::before {
    content:'';
    position:absolute; top:0; left:0; right:0; height:3px;
    background: var(--kc);
    box-shadow: 0 0 16px 2px var(--kg);
    border-radius: 22px 22px 0 0;
}
.kpi-card::after {
    content:'';
    position:absolute; bottom:-35px; right:-35px;
    width:120px; height:120px;
    background: radial-gradient(circle, var(--kg), transparent 70%);
    pointer-events:none;
}
.kpi-card:hover { transform:translateY(-4px); border-color:var(--kb); box-shadow:0 14px 36px rgba(0,0,0,.45); }
.kpi-icon-wrap {
    width:46px; height:46px; border-radius:14px;
    background:var(--ka); border:1px solid var(--kb);
    display:flex; align-items:center; justify-content:center;
    margin-bottom:18px; position:relative; z-index:1;
}
.kpi-icon-wrap svg { width:22px; height:22px; }
.kpi-value { font-size:38px; font-weight:800; color:var(--text-primary); line-height:1; margin-bottom:6px; letter-spacing:-1px; position:relative; z-index:1; }
.kpi-label { font-size:12px; font-weight:500; color:var(--text-secondary); letter-spacing:.3px; position:relative; z-index:1; }
.kpi-desc { font-size:10px; color:var(--text-muted); margin-top:4px; position:relative; z-index:1; }

/* ── Juknis Link ── */
.juknis-link {
    text-decoration: none;
    display: block;
    cursor: pointer;
}
.juknis-link:hover .kpi-value {
    color: #c4b5fd;
}
.juknis-badge {
    display: inline-block;
    background: rgba(139,92,246,.15);
    border-radius: 20px;
    padding: 2px 10px;
    font-size: 10px;
    color: #c4b5fd;
    margin-top: 8px;
}

/* ── Ringkasan Klaster (BARU) ── */
.klaster-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
    margin-bottom: 28px;
}
.klaster-card {
    background: linear-gradient(145deg,#1e1e2e,#16161f);
    border-radius: 20px;
    padding: 20px 22px;
    border: 1px solid rgba(255,255,255,.07);
    position: relative;
    overflow: hidden;
    transition: transform .25s, box-shadow .25s;
}
.klaster-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(0,0,0,.3);
}
.klaster-header {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
}
.klaster-icon {
    font-size: 28px;
    margin-bottom: 12px;
}
.klaster-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
}
.klaster-jumlah {
    font-size: 42px;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 6px;
}
.klaster-sub {
    font-size: 12px;
    color: var(--text-muted);
}

/* ── Welcome ── */
.welcome-section {
    background: linear-gradient(135deg,rgba(99,102,241,.12),rgba(139,92,246,.06));
    border-radius:24px; padding:32px 36px; margin-bottom:28px;
    border:1.5px solid rgba(99,102,241,.45);
    position:relative; overflow:hidden;
}
.welcome-section::before {
    content:''; position:absolute; top:-70px; left:-60px;
    width:240px; height:240px;
    background:radial-gradient(circle,rgba(99,102,241,.2),transparent 70%);
    pointer-events:none;
}
.welcome-section::after {
    content:''; position:absolute; bottom:-60px; right:-50px;
    width:220px; height:220px;
    background:radial-gradient(circle,rgba(139,92,246,.15),transparent 70%);
    pointer-events:none;
}
.ws-badge {
    display:inline-flex; align-items:center; gap:8px;
    background:rgba(99,102,241,.15); border:1px solid rgba(99,102,241,.4);
    border-radius:999px; padding:4px 16px;
    font-size:10.5px; font-weight:600; color:#a5b4fc;
    letter-spacing:.6px; text-transform:uppercase;
    margin-bottom:20px; position:relative; z-index:1; width:fit-content;
}
.ws-badge-dot {
    width:7px; height:7px; border-radius:50%; background:#6366f1;
    animation:blink-dot 1.6s ease-in-out infinite; flex-shrink:0;
}
@keyframes blink-dot {
    0%,100% { box-shadow:0 0 0 0 rgba(99,102,241,.6); opacity:1; }
    50%      { box-shadow:0 0 0 5px rgba(99,102,241,0); opacity:.6; }
}
.welcome-greeting {
    font-family:'Cormorant Garamond','Georgia',serif;
    font-size:15px; font-weight:400; font-style:italic;
    color:rgba(165,180,252,.7); letter-spacing:2.5px;
    text-transform:uppercase; margin-bottom:4px; position:relative; z-index:1;
}
.welcome-name {
    font-family:'Cinzel','Georgia',serif;
    font-size:34px; font-weight:600; line-height:1.15; margin-bottom:12px;
    position:relative; z-index:1; letter-spacing:2px;
    background:linear-gradient(100deg,#ffffff 0%,#e0e7ff 30%,#a5b4fc 60%,#c4b5fd 85%,#f0abfc 100%);
    -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
}
.welcome-subtitle {
    font-size:13px; font-weight:400; color:rgba(161,161,170,.75);
    letter-spacing:.5px; margin-bottom:18px;
    position:relative; z-index:1; display:flex; align-items:center; gap:10px;
}
.welcome-subtitle::before {
    content:''; display:inline-block; width:24px; height:1px;
    background:rgba(99,102,241,.6); flex-shrink:0;
}
.welcome-text { color:var(--text-secondary); font-size:13px; line-height:1.6; max-width:480px; position:relative; z-index:1; }

/* ── Quick Links ── */
.quick-links-grid {
    display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:16px; margin-top:26px; position:relative; z-index:1;
}
.quick-link-card {
    background:rgba(26,26,36,.9); backdrop-filter:blur(10px);
    border-radius:18px; padding:20px 20px 16px;
    text-decoration:none; border:1px solid rgba(255,255,255,.07);
    transition:transform .3s cubic-bezier(.4,0,.2,1), border-color .3s, background .3s, box-shadow .3s;
    display:flex; flex-direction:column; position:relative; overflow:hidden;
}
.quick-link-card:hover {
    transform:translateY(-6px);
    border-color:var(--ql-accent,rgba(99,102,241,.5));
    background:rgba(99,102,241,.08);
    box-shadow:0 14px 34px rgba(0,0,0,.4);
    text-decoration:none;
}
.quick-icon {
    width:48px; height:48px; border-radius:15px;
    background:rgba(255,255,255,.06);
    display:flex; align-items:center; justify-content:center;
    font-size:22px; margin-bottom:14px;
    transition:transform .35s cubic-bezier(.34,1.56,.64,1), background .3s;
    position:relative; z-index:1;
}
.quick-link-card:hover .quick-icon { transform:rotate(-6deg) scale(1.12); background:rgba(255,255,255,.1); }
.quick-content  { flex:1; position:relative; z-index:1; }
.quick-title    { font-size:14px; font-weight:700; color:var(--text-primary); margin-bottom:6px; line-height:1.3; }
.quick-desc     { font-size:11.5px; color:var(--text-muted); line-height:1.5; }
.quick-divider  { height:1px; background:rgba(255,255,255,.07); margin:14px 0 12px; }
.quick-footer   { display:flex; align-items:center; justify-content:space-between; position:relative; z-index:1; }
.quick-footer-label { font-size:10.5px; font-weight:600; color:var(--ql-accent-text,#a5b4fc); letter-spacing:.4px; text-transform:uppercase; }
.quick-arrow { font-size:15px; color:var(--ql-accent-text,#a5b4fc); opacity:0; transform:translateX(-6px); transition:opacity .3s, transform .35s cubic-bezier(.4,0,.2,1); }
.quick-link-card:hover .quick-arrow { opacity:1; transform:translateX(0); }

/* ── Analytics Row ── */
.analytics-grid {
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 20px;
    margin-top: 20px;
    margin-bottom: 28px;
}
.analytics-card {
    background: var(--dark-card);
    border-radius: 20px;
    padding: 20px;
    border: 1px solid var(--dark-border);
}
.analytics-label {
    font-size: 12px;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 16px;
}
.analytics-value {
    font-size: 36px;
    font-weight: 800;
    margin-bottom: 8px;
}
.analytics-sub {
    font-size: 12px;
    color: var(--text-muted);
}

/* ── Timestamp ── */
.timestamp {
    text-align: right;
    margin-top: 24px;
    padding-top: 16px;
    border-top: 1px solid var(--dark-border);
}
.timestamp-text {
    font-size: 11px;
    color: var(--text-muted);
}

@media(max-width:768px) {
    .kpi-grid { grid-template-columns:repeat(2,1fr); }
    .klaster-grid { grid-template-columns:repeat(2,1fr); }
    .analytics-grid { grid-template-columns:1fr; }
    .welcome-section { padding:22px 20px; }
    .welcome-name { font-size:26px; }
}
@media(max-width:480px) {
    .kpi-grid { grid-template-columns:1fr; }
    .klaster-grid { grid-template-columns:1fr; }
    .quick-links-grid { grid-template-columns:1fr; }
}
</style>

{{-- ══ KPI Cards ══ --}}
<div class="kpi-grid">

    <div class="kpi-card" style="--kc:linear-gradient(90deg,#6366f1,#818cf8);--kg:rgba(99,102,241,.35);--kb:rgba(99,102,241,.28);--ka:rgba(99,102,241,.12);">
        <div class="kpi-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="#818cf8" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"/>
            </svg>
        </div>
        <div class="kpi-value">{{ $stats['opd'] ?? 0 }}</div>
        <div class="kpi-label">Perangkat Daerah</div>
    </div>

    <div class="kpi-card" style="--kc:linear-gradient(90deg,#10b981,#34d399);--kg:rgba(16,185,129,.3);--kb:rgba(16,185,129,.28);--ka:rgba(16,185,129,.1);">
        <div class="kpi-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="8" r="4"/>
                <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                <path d="M21 20c0-3-2-5.3-5-6.3"/>
            </svg>
        </div>
        <div class="kpi-value">{{ $stats['operator'] ?? 0 }}</div>
        <div class="kpi-label">Operator Terdaftar</div>
    </div>

    <div class="kpi-card" style="--kc:linear-gradient(90deg,#f59e0b,#fbbf24);--kg:rgba(245,158,11,.3);--kb:rgba(245,158,11,.28);--ka:rgba(245,158,11,.1);">
        <div class="kpi-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <line x1="10" y1="9"  x2="8" y2="9"/>
            </svg>
        </div>
        <div class="kpi-value">{{ $stats['template'] ?? 0 }}</div>
        <div class="kpi-label">Template Tersedia</div>
    </div>

    @php $adaMenunggu = ($stats['menunggu'] ?? 0) > 0; @endphp
    <div class="kpi-card" style="
        --kc:{{ $adaMenunggu ? 'linear-gradient(90deg,#ef4444,#f87171)' : 'linear-gradient(90deg,#10b981,#34d399)' }};
        --kg:{{ $adaMenunggu ? 'rgba(239,68,68,.3)'   : 'rgba(16,185,129,.3)' }};
        --kb:{{ $adaMenunggu ? 'rgba(239,68,68,.28)'  : 'rgba(16,185,129,.28)' }};
        --ka:{{ $adaMenunggu ? 'rgba(239,68,68,.1)'   : 'rgba(16,185,129,.1)' }};">
        <div class="kpi-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="{{ $adaMenunggu ? '#f87171' : '#34d399' }}" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <div class="kpi-value">{{ $stats['menunggu'] ?? 0 }}</div>
        <div class="kpi-label">Menunggu Persetujuan</div>
    </div>

</div>

{{-- ══ RINGKASAN KLASTER (BARU) ══ --}}
@if(isset($rekapKlaster) && $rekapKlaster->count() > 0)
<div class="klaster-grid">
    @php
        $klasterConfig = [
            'utama' => ['icon' => '🏆', 'label' => 'Klaster Utama', 'color' => '#6366f1', 'glow' => 'rgba(99,102,241,.3)'],
            'pendukung' => ['icon' => '🛡️', 'label' => 'Klaster Pendukung', 'color' => '#10b981', 'glow' => 'rgba(16,185,129,.3)'],
            'tambahan' => ['icon' => '➕', 'label' => 'Klaster Tambahan', 'color' => '#f59e0b', 'glow' => 'rgba(245,158,11,.3)'],
        ];
    @endphp

    @foreach(['utama', 'pendukung', 'tambahan'] as $key)
    @php
        $cfg = $klasterConfig[$key];
        $jumlah = $rekapKlaster[$key]->jumlah ?? 0;
    @endphp
    <div class="klaster-card">
        <div class="klaster-header" style="background: {{ $cfg['color'] }}; box-shadow: 0 0 12px {{ $cfg['glow'] }};"></div>
        <div class="klaster-icon">{{ $cfg['icon'] }}</div>
        <div class="klaster-label" style="color: {{ $cfg['color'] }};">{{ $cfg['label'] }}</div>
        <div class="klaster-jumlah" style="color: {{ $cfg['color'] }};">{{ $jumlah }}</div>
        <div class="klaster-sub">OPD terdaftar</div>
    </div>
    @endforeach
</div>
@endif

{{-- ══ 3 KARTU JUKNIS (Petunjuk Teknis) ══ --}}
@php
    use App\Models\Juknis;
    $juknisList = Juknis::where('is_active', true)
        ->orderBy('urutan', 'asc')
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();
    
    $cards = [1 => null, 2 => null, 3 => null];
    foreach($juknisList as $j) {
        if($j->urutan >= 1 && $j->urutan <= 3 && !$cards[$j->urutan]) {
            $cards[$j->urutan] = $j;
        }
    }
    $noUrutan = $juknisList->where('urutan', 0)->values();
    $nextIdx = 0;
    for($i = 1; $i <= 3; $i++) {
        if(!$cards[$i] && $nextIdx < $noUrutan->count()) {
            $cards[$i] = $noUrutan[$nextIdx];
            $nextIdx++;
        }
    }
@endphp

<div class="kpi-grid" style="margin-bottom: 28px;">
    @for($i = 1; $i <= 3; $i++)
    @php $juknis = $cards[$i] ?? null; @endphp
    <div class="kpi-card" style="
        --kc: linear-gradient(90deg,#8b5cf6,#a78bfa);
        --kg: rgba(139,92,246,.3);
        --kb: rgba(139,92,246,.28);
        --ka: rgba(139,92,246,.1);">
        <div class="kpi-icon-wrap">
            @if($juknis)
                @php
                    $fileExt = strtolower(pathinfo($juknis->file_name, PATHINFO_EXTENSION));
                    $fileIcon = $fileExt === 'pdf' ? '📄' : ($fileExt === 'docx' ? '📝' : ($fileExt === 'xlsx' ? '📊' : '📁'));
                @endphp
                <span style="font-size:24px;">{{ $fileIcon }}</span>
            @else
                <svg viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
            @endif
        </div>
        @if($juknis)
            <a href="{{ route('juknis.download', $juknis->id) }}" class="juknis-link" download>
                <div class="kpi-value" style="font-size:18px; font-weight:600; color:#c4b5fd; margin-bottom:4px;">{{ Str::limit($juknis->judul, 25) }}</div>
                <div class="kpi-label" style="margin-bottom:4px;">{{ $juknis->file_name }}</div>
                <div class="kpi-desc">
                    {{ \Carbon\Carbon::parse($juknis->created_at)->format('d M Y') }}
                </div>
                <div class="juknis-badge">⬇️ Download Juknis</div>
            </a>
        @else
            <div class="kpi-value" style="font-size:18px; font-weight:600; color:#6b7280;">📭</div>
            <div class="kpi-label">Juknis {{ $i }}</div>
            <div class="kpi-desc">Belum tersedia</div>
            <div class="juknis-badge" style="background:rgba(139,92,246,.08); color:#6b7280;">Admin akan upload</div>
        @endif
    </div>
    @endfor
</div>

{{-- ══ Welcome ══ --}}
<div class="welcome-section">
    <div class="ws-badge"><span class="ws-badge-dot"></span> E-SAKIPKU Provinsi NTT</div>
    <div class="welcome-greeting">Selamat Datang,</div>
    <div class="welcome-name">{{ $user['nama'] }}</div>
    <div class="welcome-subtitle">Admin </div>
    <div class="welcome-text">Gunakan menu di sebelah kiri untuk mengakses fitur E-SAKIPKU Provinsi NTT.</div>

    <div class="quick-links-grid">
        <a href="{{ route('perjanjian.index','perjanjian-kinerja') }}" class="quick-link-card" style="--ql-accent:rgba(99,102,241,.55);--ql-accent-text:#a5b4fc;">
            <div class="quick-icon">🤝</div>
            <div class="quick-content">
                <div class="quick-title">Perencanaan Kinerja</div>
                <div class="quick-desc">Unduh &amp; kelola template dokumen perjanjian kinerja OPD</div>
            </div>
            <div class="quick-divider"></div>
            <div class="quick-footer"><span class="quick-footer-label">Template dokumen</span><span class="quick-arrow">→</span></div>
        </a>

        <a href="{{ route('evaluasi.lke') }}" class="quick-link-card" style="--ql-accent:rgba(16,185,129,.5);--ql-accent-text:#6ee7b7;">
            <div class="quick-icon">📊</div>
            <div class="quick-content">
                <div class="quick-title">LKE AKIP</div>
                <div class="quick-desc">Isi lembar kerja evaluasi akuntabilitas kinerja instansi</div>
            </div>
            <div class="quick-divider"></div>
            <div class="quick-footer"><span class="quick-footer-label">Evaluasi kinerja</span><span class="quick-arrow">→</span></div>
        </a>

        <a href="{{ route('pengukuran.periodik') }}" class="quick-link-card" style="--ql-accent:rgba(245,158,11,.5);--ql-accent-text:#fcd34d;">
            <div class="quick-icon">📈</div>
            <div class="quick-content">
                <div class="quick-title">Pengukuran Kinerja</div>
                <div class="quick-desc">Input capaian indikator kinerja secara periodik</div>
            </div>
            <div class="quick-divider"></div>
            <div class="quick-footer"><span class="quick-footer-label">Input periodik</span><span class="quick-arrow">→</span></div>
        </a>
    </div>
</div>

{{-- ══ Analytics Row ══ --}}
<div class="analytics-grid">
    <div class="analytics-card">
        <div class="analytics-label">DOKUMEN TERUPLOAD</div>
        <div class="analytics-value" style="color: var(--accent-success);">{{ $stats['dokumen_uploaded'] ?? 0 }}</div>
        <div class="analytics-sub">Perjanjian Kinerja Tahun {{ $tahun }}</div>
    </div>
    <div class="analytics-card">
        <div class="analytics-label">TOTAL OPD</div>
        <div class="analytics-value" style="color: var(--accent-primary);">{{ $stats['opd'] ?? 0 }}</div>
        <div class="analytics-sub">Perangkat Daerah terdaftar</div>
    </div>
    <div class="analytics-card">
        <div class="analytics-label">MENUNGGU PERSETUJUAN</div>
        <div class="analytics-value" style="color: {{ $adaMenunggu ? '#f59e0b' : '#10b981' }};">{{ $stats['menunggu'] ?? 0 }}</div>
        <div class="analytics-sub">Akun operator belum disetujui</div>
    </div>
</div>

{{-- ── Timestamp ── --}}
<div class="timestamp">
    <span class="timestamp-text">
        🕐 TERAKHIR DIPERBARUI: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB
    </span>
</div>

@endsection