<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>e-SAKIPKU — Sistem Akuntabilitas Kinerja Instansi Pemerintah Provinsi NTT</title>
  <meta name="description" content="e-SAKIPKU menghimpun perjanjian kinerja, evaluasi LKE, dan laporan LKIP seluruh Perangkat Daerah Provinsi Nusa Tenggara Timur dalam satu sistem.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500&display=swap" rel="stylesheet">
  <style>
    :root{
      --indigo: #16213F;
      --indigo-deep: #0B1220;
      --gold: #B98A32;
      --gold-bright: #EAC784;
      --teal: #2B6660;
      --paper: #F5F1E6;
      --paper-2: #ECE3CE;
      --ink: #1E1C18;
      --ink-soft: #5B564C;
      --line: rgba(22,33,63,.16);
    }

    *{ box-sizing:border-box; }
    html{ scroll-behavior:smooth; }

    body{
      margin:0;
      background:
        radial-gradient(ellipse 900px 500px at 12% -8%, rgba(185,138,50,.10), transparent 55%),
        radial-gradient(ellipse 700px 500px at 100% 10%, rgba(43,102,96,.08), transparent 50%),
        var(--paper);
      color:var(--ink);
      font-family:'Plus Jakarta Sans', sans-serif;
      -webkit-font-smoothing:antialiased;
      overflow-x:hidden;
      position:relative;
    }

    /* Tekstur kertas halus (SVG noise), sangat tipis */
    body::before{
      content:'';
      position:fixed;
      inset:0;
      pointer-events:none;
      z-index:50;
      opacity:.035;
      mix-blend-mode:multiply;
      background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='120' height='120'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/></filter><rect width='100%25' height='100%25' filter='url(%23n)'/></svg>");
    }

    a{ color:inherit; }

    /* ── Motif garis ikat (strip dekoratif) ──────── */
    .ikat-strip{
      height:9px;
      background-image:
        linear-gradient(45deg, var(--gold) 23%, transparent 23%),
        linear-gradient(-45deg, var(--gold) 23%, transparent 23%);
      background-size:13px 13px;
      opacity:.55;
    }
    .ikat-strip.dim{ opacity:.22; }

    /* ── Letterhead ───────────────────────────────── */
    .letterhead{
      background:linear-gradient(155deg, var(--indigo), var(--indigo-deep));
      position:relative;
      z-index:3;
    }
    .letterhead-inner{
      max-width:1180px;
      margin:0 auto;
      padding:20px 28px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:16px;
    }
    .brand{
      display:flex;
      align-items:center;
      gap:14px;
    }
    .seal{
      width:46px; height:46px;
      flex-shrink:0;
      object-fit:contain;
    }
    .brand-text{ line-height:1.2; }
    .brand-text strong{
      display:block;
      font-family:'Fraunces', serif;
      font-size:17px;
      font-weight:600;
      letter-spacing:.2px;
      color:var(--gold-bright);
    }
    .brand-text span{
      display:block;
      font-family:'IBM Plex Mono', monospace;
      font-size:9.5px;
      letter-spacing:1.1px;
      color:rgba(245,241,230,.6);
      text-transform:uppercase;
      margin-top:3px;
    }
    .topbar-login{
      font-family:'IBM Plex Mono', monospace;
      font-size:10.5px;
      letter-spacing:.8px;
      text-transform:uppercase;
      text-decoration:none;
      color:var(--paper);
      border:1px solid rgba(234,199,132,.4);
      border-radius:3px;
      padding:10px 20px;
      transition:background .2s, border-color .2s;
      white-space:nowrap;
    }
    .topbar-login:hover{
      background:rgba(234,199,132,.12);
      border-color:var(--gold-bright);
    }
    .topbar-login:focus-visible{
      outline:2px solid var(--gold-bright);
      outline-offset:3px;
    }

    /* ── Hero ───────────────────────────────────── */
    .hero-wrap{
      max-width:1180px;
      margin:0 auto;
      padding:88px 28px 0;
      position:relative;
      z-index:2;
      display:grid;
      grid-template-columns:1.35fr .9fr;
      gap:64px;
      align-items:start;
    }

    .eyebrow{
      font-family:'IBM Plex Mono', monospace;
      font-size:11px;
      letter-spacing:1.6px;
      text-transform:uppercase;
      background:linear-gradient(100deg, var(--gold), var(--gold-bright) 60%, var(--gold));
      -webkit-background-clip:text;
      background-clip:text;
      color:transparent;
      display:flex;
      align-items:center;
      gap:10px;
      margin-bottom:30px;
    }
    .eyebrow::before{
      content:'';
      width:26px; height:1px;
      background:var(--gold);
      display:inline-block;
    }

    h1{
      font-family:'Fraunces', serif;
      font-optical-sizing:auto;
      font-weight:600;
      font-size:clamp(36px, 4.6vw, 62px);
      line-height:1.06;
      letter-spacing:-.015em;
      color:var(--indigo-deep);
      max-width:14.5ch;
      margin:0 0 28px;
    }
    h1 em{
      font-style:italic;
      font-weight:500;
      background:linear-gradient(100deg, var(--gold), var(--gold-bright));
      -webkit-background-clip:text;
      background-clip:text;
      color:transparent;
    }

    .lede{
      font-size:16.5px;
      line-height:1.75;
      color:var(--ink-soft);
      max-width:50ch;
      margin:0 0 40px;
    }

    .hero-actions{
      display:flex;
      align-items:center;
      gap:24px;
      flex-wrap:wrap;
      margin-bottom:8px;
    }

    .btn-primary{
      display:inline-flex;
      align-items:center;
      gap:11px;
      background:var(--indigo);
      color:var(--paper);
      text-decoration:none;
      font-weight:600;
      font-size:14.5px;
      letter-spacing:.2px;
      padding:16px 30px;
      border-radius:4px;
      border:1px solid var(--indigo);
      box-shadow:0 14px 28px -10px rgba(11,18,32,.55);
      transition:transform .2s, box-shadow .2s, background .2s;
      position:relative;
    }
    .btn-primary::after{
      content:'';
      position:absolute;
      inset:3px;
      border:1px solid rgba(234,199,132,.5);
      border-radius:2px;
      pointer-events:none;
    }
    .btn-primary:hover{
      transform:translateY(-2px);
      background:var(--indigo-deep);
      box-shadow:0 18px 32px -10px rgba(11,18,32,.6);
    }
    .btn-primary:focus-visible{
      outline:2px solid var(--teal);
      outline-offset:3px;
    }
    .btn-primary svg{
      width:14px; height:14px;
      transition:transform .2s;
    }
    .btn-primary:hover svg{ transform:translateX(3px); }

    .hero-note{
      font-size:12.5px;
      color:var(--ink-soft);
    }
    .hero-note strong{ color:var(--indigo); font-weight:600; }

    /* ── Dossier card (kartu modul) ───────────────── */
    .dossier{
      background:var(--paper-2);
      border:1px solid var(--line);
      border-radius:4px;
      padding:30px 30px 26px;
      box-shadow:0 24px 48px -24px rgba(22,33,63,.28);
      position:relative;
    }
    .dossier::before{
      content:'';
      position:absolute;
      inset:6px;
      border:1px solid rgba(185,138,50,.28);
      border-radius:2px;
      pointer-events:none;
    }
    .dossier-label{
      font-family:'IBM Plex Mono', monospace;
      font-size:10px;
      letter-spacing:1.4px;
      text-transform:uppercase;
      color:var(--teal);
      margin-bottom:18px;
    }
    .dossier-list{
      list-style:none;
      margin:0 0 22px;
      padding:0;
    }
    .dossier-list li{
      display:flex;
      align-items:baseline;
      gap:14px;
      padding:11px 0;
      border-bottom:1px solid rgba(22,33,63,.09);
      font-size:13.5px;
      color:var(--ink);
    }
    .dossier-list li:last-child{ border-bottom:none; }
    .dossier-num{
      font-family:'IBM Plex Mono', monospace;
      font-size:11px;
      color:var(--gold);
      flex-shrink:0;
      width:20px;
    }
    .dossier-stat{
      border-top:1px solid rgba(22,33,63,.14);
      padding-top:20px;
      display:flex;
      align-items:baseline;
      gap:12px;
    }
    .dossier-stat-num{
      font-family:'Fraunces', serif;
      font-size:38px;
      font-weight:600;
      color:var(--indigo-deep);
      line-height:1;
    }
    .dossier-stat-label{
      font-size:12px;
      color:var(--ink-soft);
      line-height:1.4;
    }

    /* ── Signature: pohon kinerja di atas siluet bukit ── */
    .signature{
      position:relative;
      max-width:1180px;
      margin:96px auto 0;
      padding:0 28px;
      z-index:1;
    }
    .signature-frame{
      position:relative;
      height:240px;
      overflow:hidden;
    }
    .signature-frame svg{
      position:absolute;
      left:0; top:0;
      width:100%;
      height:100%;
    }

    @media (prefers-reduced-motion: no-preference){
      .grow-line{
        stroke-dasharray:600;
        stroke-dashoffset:600;
        animation:drawline 1.9s ease-out forwards;
      }
      .fade-node{ opacity:0; animation:fadenode .5s ease-out forwards; }
      .rise{
        opacity:0;
        transform:translateY(16px);
        animation:riseup .65s cubic-bezier(.2,.7,.3,1) forwards;
      }
      .rise:nth-child(1){ animation-delay:.05s; }
      .rise:nth-child(2){ animation-delay:.16s; }
      .rise:nth-child(3){ animation-delay:.27s; }
      .rise:nth-child(4){ animation-delay:.38s; }
      .rise:nth-child(5){ animation-delay:.49s; }
      .dossier{ opacity:0; animation:riseup .7s cubic-bezier(.2,.7,.3,1) forwards; animation-delay:.35s; }
    }
    @keyframes drawline{ to{ stroke-dashoffset:0; } }
    @keyframes fadenode{ to{ opacity:1; } }
    @keyframes riseup{ to{ opacity:1; transform:translateY(0); } }

    /* ── Footer ─────────────────────────────────── */
    footer{
      max-width:1180px;
      margin:0 auto;
      padding:30px 28px 44px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      flex-wrap:wrap;
      gap:12px;
      font-size:12px;
      color:var(--ink-soft);
    }
    footer .foot-mono{
      font-family:'IBM Plex Mono', monospace;
      letter-spacing:.5px;
      font-size:10.5px;
      text-transform:uppercase;
    }

    @media (max-width: 900px){
      .hero-wrap{ grid-template-columns:1fr; gap:48px; }
    }
    @media (max-width: 640px){
      .letterhead-inner{ padding:18px 20px; }
      .hero-wrap{ padding:56px 20px 0; }
      .signature{ margin-top:60px; padding:0 20px; }
      .signature-frame{ height:170px; }
      footer{ padding:24px 20px 36px; flex-direction:column; align-items:flex-start; }
      .dossier{ padding:24px 22px 22px; }
    }
  </style>
</head>
<body>

  <div class="ikat-strip"></div>
  <div class="letterhead">
    <div class="letterhead-inner">
      <div class="brand">
        {{-- Lambang resmi Pemprov NTT. Taruh file logo di public/images/logo-ntt.png --}}
        <img src="{{ asset('images/logo-ntt.png') }}" alt="Lambang Provinsi Nusa Tenggara Timur" class="seal">
        <div class="brand-text">
          <strong>e-SAKIPKU</strong>
          <span>Pemerintah Provinsi Nusa Tenggara Timur</span>
        </div>
      </div>
      <a href="{{ route('login') }}" class="topbar-login">Masuk ke Sistem</a>
    </div>
  </div>
  <div class="ikat-strip dim"></div>

  <div class="hero-wrap">
    <div>
      <div class="eyebrow rise">Sistem Akuntabilitas Kinerja Instansi Pemerintah</div>

      <h1 class="rise">Kinerja Perangkat Daerah, tercatat dari akar hingga <em>hasil</em>.</h1>

      <p class="lede rise">
        e-SAKIPKU menghimpun perjanjian kinerja, evaluasi LKE, dan laporan LKIP
        setiap Perangkat Daerah se-Nusa Tenggara Timur dalam satu sistem —
        dari cascading pohon kinerja di tingkat OPD hingga rekapitulasi
        capaian di tingkat provinsi.
      </p>

      <div class="hero-actions rise">
        <a href="{{ route('login') }}" class="btn-primary">
          Masuk ke Sistem
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14"/><path d="M13 6l6 6-6 6"/>
          </svg>
        </a>
      </div>
      <div class="hero-note rise">Khusus untuk <strong>Admin</strong> dan <strong>Operator OPD</strong> terdaftar.</div>
    </div>

    <aside class="dossier">
      <div class="dossier-label">Modul Sistem</div>
      <ul class="dossier-list">
        <li><span class="dossier-num">01</span> Perjanjian Kinerja &amp; RENSTRA</li>
        <li><span class="dossier-num">02</span> Evaluasi LKE &amp; Klaster</li>
        <li><span class="dossier-num">03</span> Cascading Pohon Kinerja</li>
        <li><span class="dossier-num">04</span> Laporan LKIP</li>
      </ul>
      <div class="dossier-stat">
        <div class="dossier-stat-num">42</div>
        <div class="dossier-stat-label">Perangkat Daerah<br>terhubung dalam sistem</div>
      </div>
    </aside>
  </div>

  <div class="signature">
    <div class="signature-frame">
      <svg viewBox="0 0 1180 240" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Matahari, sebagian tertutup bukit -->
        <circle cx="590" cy="118" r="52" fill="var(--gold)" opacity="0.22"/>
        <circle cx="590" cy="118" r="52" stroke="var(--gold)" stroke-width="1" fill="none" opacity="0.4"/>

        <path d="M0,198 C160,158 260,183 400,163 C560,140 680,176 820,156 C960,138 1060,166 1180,148 L1180,240 L0,240 Z"
              fill="var(--paper-2)" opacity="0.9"/>
        <path d="M0,222 C140,202 300,217 460,199 C620,181 760,211 940,195 C1040,185 1110,203 1180,197 L1180,240 L0,240 Z"
              fill="var(--gold)" opacity="0.16"/>

        <!-- Siluet Komodo berjalan di punggung bukit -->
        <g transform="translate(878,178)" fill="var(--indigo-deep)" opacity="0.88">
          <path d="M0,18 Q-20,10 -31,-1 Q-22,6 -3,13 Z"/>
          <ellipse cx="19" cy="13" rx="21" ry="7.5"/>
          <rect x="6" y="18" width="2.6" height="8" rx="1"/>
          <rect x="15" y="19" width="2.6" height="8" rx="1"/>
          <rect x="26" y="19" width="2.6" height="8" rx="1"/>
          <rect x="34" y="18" width="2.6" height="8" rx="1"/>
          <path d="M38,9 Q45,5 51,7 Q47,9 43,11 Q49,11 52,14 Q46,15 41,13 Z"/>
        </g>

        <!-- Laut dan perahu layar -->
        <g transform="translate(118,196)" opacity="0.92">
          <path d="M-16,20 L20,20 L13,25 L-9,25 Z" fill="var(--indigo)"/>
          <line x1="1" y1="20" x2="1" y2="-6" stroke="var(--indigo-deep)" stroke-width="1"/>
          <path d="M1,19 L1,-4 L15,19 Z" fill="var(--teal)" opacity="0.9"/>
        </g>
        <path d="M0,226 q30,-7 60,0 t60,0 t60,0 t60,0 t60,0 t60,0 t60,0 t60,0 t60,0 t60,0 t60,0 t60,0 t60,0 t60,0 t60,0 t60,0 t60,0 t60,0 t60,0"
              stroke="var(--teal)" stroke-width="1" fill="none" opacity="0.35"/>

        <g stroke="var(--teal)" stroke-width="1.4" fill="none" opacity="0.8">
          <path class="grow-line" d="M590,30 L590,68" />
          <path class="grow-line" d="M590,68 L470,110" />
          <path class="grow-line" d="M590,68 L590,110" />
          <path class="grow-line" d="M590,68 L710,110" />
          <path class="grow-line" d="M470,110 L410,152" />
          <path class="grow-line" d="M470,110 L500,152" />
          <path class="grow-line" d="M590,110 L580,152" />
          <path class="grow-line" d="M590,110 L640,152" />
          <path class="grow-line" d="M710,110 L690,152" />
          <path class="grow-line" d="M710,110 L770,152" />
        </g>
        <g fill="var(--indigo)">
          <circle class="fade-node" style="animation-delay:.2s" cx="590" cy="30" r="4.5"/>
          <circle class="fade-node" style="animation-delay:.5s" cx="470" cy="110" r="3.6"/>
          <circle class="fade-node" style="animation-delay:.55s" cx="590" cy="110" r="3.6"/>
          <circle class="fade-node" style="animation-delay:.6s" cx="710" cy="110" r="3.6"/>
        </g>
        <g fill="var(--gold)">
          <circle class="fade-node" style="animation-delay:.85s" cx="410" cy="152" r="3"/>
          <circle class="fade-node" style="animation-delay:.9s" cx="500" cy="152" r="3"/>
          <circle class="fade-node" style="animation-delay:.95s" cx="580" cy="152" r="3"/>
          <circle class="fade-node" style="animation-delay:1s" cx="640" cy="152" r="3"/>
          <circle class="fade-node" style="animation-delay:1.05s" cx="690" cy="152" r="3"/>
          <circle class="fade-node" style="animation-delay:1.1s" cx="770" cy="152" r="3"/>
        </g>
      </svg>
    </div>
  </div>

  <div class="ikat-strip dim"></div>
  <footer>
    <div class="foot-mono">© {{ date('Y') }} Pemerintah Provinsi Nusa Tenggara Timur</div>
    <div class="foot-mono">e-SAKIPKU · Sistem Akuntabilitas Kinerja</div>
  </footer>

</body>
</html>