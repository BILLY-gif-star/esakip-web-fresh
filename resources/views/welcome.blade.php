<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>e-SAKIPKU — Sistem Akuntabilitas Kinerja Instansi Pemerintah Provinsi NTT</title>
  <meta name="description" content="e-SAKIPKU menghimpun perjanjian kinerja, evaluasi LKE, dan laporan LKIP seluruh Perangkat Daerah Provinsi Nusa Tenggara Timur dalam satu sistem.">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,600&family=IBM+Plex+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📊</text></svg>">

  <style>
    :root {
      --primary: #0F3D5E;
      --primary-deep: #0A2C43;
      --primary-light: #1E5679;
      --secondary: #4A90B8;
      --secondary-light: #8FC1DB;
      --secondary-dark: #2C6E93;
      --accent: #D9A441;
      --accent-light: #E8C476;
      --accent-dark: #B8862F;
      --bg: #F7F8FA;
      --bg-2: #EDEFF2;
      --text: #1F2937;
      --text-soft: #5B6472;
      --line: rgba(15,61,94,.12);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }

    body {
      margin: 0;
      background: var(--bg);
      color: var(--text);
      font-family: 'Plus Jakarta Sans', sans-serif;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      overflow-x: hidden;
      position: relative;
    }

    /* Background foto tim (fixed) */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      z-index: -2;
      background-color: #12467E;
      background-image: url('{{ $img['staff_photo'] }}');
      background-blend-mode: screen;
      background-size: cover;
      background-position: center 12%;
      background-repeat: no-repeat;
    }
    body::after {
      content: '';
      position: fixed;
      inset: 0;
      z-index: -1;
      background: linear-gradient(180deg,
        rgba(9,35,58,.38) 0%,
        rgba(9,35,58,.52) 50%,
        rgba(9,35,58,.66) 100%);
    }

    a { color: inherit; text-decoration: none; }

    /* Motif Garis Tenun Ikat */
    .ikat-strip {
      height: 8px;
      background-image:
        linear-gradient(45deg, var(--accent) 23%, transparent 23%),
        linear-gradient(-45deg, var(--secondary) 23%, transparent 23%),
        linear-gradient(45deg, var(--primary) 23%, transparent 23%);
      background-size: 16px 16px;
      opacity: .9;
    }
    .ikat-strip.dim { opacity: .4; }

    /* Header */
    .letterhead {
      background: linear-gradient(135deg, var(--primary-deep), var(--primary));
      position: relative;
      z-index: 10;
      border-bottom: 3px solid var(--accent);
    }
    .letterhead-inner {
      max-width: 1180px;
      margin: 0 auto;
      padding: 18px 28px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
    }
    .brand { display: flex; align-items: center; gap: 14px; }
    .seal {
      width: 46px; height: 46px;
      flex-shrink: 0;
      object-fit: contain;
      background: rgba(255,255,255,.08);
      border-radius: 50%;
      padding: 3px;
      border: 1px solid rgba(217,164,65,.35);
    }
    .brand-text strong {
      display: block;
      font-family: 'Fraunces', serif;
      font-size: 20px;
      font-weight: 700;
      color: #FFFFFF;
    }
    .brand-text span {
      display: block;
      font-family: 'IBM Plex Mono', monospace;
      font-size: 9.5px;
      letter-spacing: 1.2px;
      color: var(--secondary-light);
      text-transform: uppercase;
      margin-top: 3px;
    }

    .nav-links { display: flex; align-items: center; gap: 28px; list-style: none; }
    .nav-links a { font-size: 14px; color: rgba(255,255,255,.85); font-weight: 500; transition: all .2s; }
    .nav-links a:hover { color: var(--accent-light); }
    .nav-links a:focus-visible { outline: 2px solid var(--accent-light); outline-offset: 3px; border-radius: 2px; }

    .topbar-login {
      font-family: 'IBM Plex Mono', monospace;
      font-size: 11px;
      letter-spacing: .8px;
      text-transform: uppercase;
      color: #FFF;
      border: 1px solid var(--accent);
      border-radius: 6px;
      padding: 10px 22px;
      transition: all .3s ease;
      background: rgba(217,164,65,.15);
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    .topbar-login:hover {
      background: var(--accent);
      color: var(--primary-deep);
      border-color: transparent;
      transform: translateY(-2px);
      font-weight: 600;
    }
    .topbar-login:focus-visible {
      outline: 2px solid var(--accent-light);
      outline-offset: 3px;
    }

    .ic { width: 15px; height: 15px; flex-shrink: 0; vertical-align: -3px; }
    .ic-lg { width: 24px; height: 24px; vertical-align: middle; }

    /* Section */
    section { max-width: 1180px; margin: 0 auto; padding: 72px 28px; }
    .section-header { text-align: center; max-width: 680px; margin: 0 auto 52px; }
    .section-eyebrow {
      font-family: 'IBM Plex Mono', monospace;
      font-size: 11.5px;
      letter-spacing: 1.8px;
      text-transform: uppercase;
      color: #FFFFFF;
      margin-bottom: 10px;
      font-weight: 600;
      background: rgba(255,255,255,.14);
      display: inline-block;
      padding: 4px 14px;
      border-radius: 20px;
      border: 1px solid rgba(255,255,255,.28);
    }
    .section-title { font-family: 'Fraunces', serif; font-size: 30px; color: #FFFFFF; margin-bottom: 10px; font-weight: 600; line-height: 1.2; }
    .section-desc { font-size: 15px; color: rgba(255,255,255,.78); line-height: 1.65; }

    #alur .section-eyebrow { color: var(--accent-light); background: rgba(217,164,65,.18); border-color: rgba(217,164,65,.4); }

    /* Hero */
    .hero-wrap {
      padding-top: 64px;
      padding-bottom: 40px;
      display: grid;
      grid-template-columns: 1.35fr .9fr;
      gap: 56px;
      align-items: start;
    }
    .eyebrow {
      font-family: 'IBM Plex Mono', monospace;
      font-size: 11px;
      letter-spacing: 1.6px;
      text-transform: uppercase;
      color: var(--accent-light);
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 24px;
      font-weight: 600;
    }
    .eyebrow::before {
      content: ''; width: 28px; height: 3px;
      background: var(--accent);
      display: inline-block; border-radius: 3px;
    }
    h1 {
      font-family: 'Fraunces', serif;
      font-weight: 600;
      font-size: clamp(38px, 4.5vw, 58px);
      line-height: 1.1;
      letter-spacing: -.015em;
      color: #FFFFFF;
      margin: 0 0 24px;
      text-shadow: 0 2px 18px rgba(0,0,0,.25);
    }
    h1 em { font-style: italic; font-weight: 600; color: var(--accent-light); }
    .lede { font-size: 16px; line-height: 1.75; color: rgba(255,255,255,.85); margin: 0 0 36px; }
    .lede strong { color: #FFFFFF; font-weight: 600; }

    .hero-actions { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; margin-bottom: 16px; }
    .btn-primary {
      display: inline-flex; align-items: center; gap: 10px;
      background: var(--primary);
      color: #FFF; font-weight: 600; font-size: 15px;
      padding: 16px 32px; border-radius: 8px; border: 1px solid var(--accent);
      box-shadow: 0 12px 24px -6px rgba(15,61,94,.35);
      transition: all .3s cubic-bezier(.2,.7,.3,1);
    }
    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 18px 32px -8px rgba(15,61,94,.45);
      background: var(--primary-deep);
    }
    .btn-primary:focus-visible { outline: 2px solid var(--secondary-light); outline-offset: 3px; }

    .hero-note { font-size: 12.5px; color: rgba(255,255,255,.75); display: flex; align-items: center; gap: 6px; }
    .hero-note strong {
      color: #FFFFFF; font-weight: 600;
      background: rgba(255,255,255,.14); padding: 3px 10px;
      border-radius: 12px; border: 1px solid rgba(255,255,255,.28);
    }

    /* Dossier */
    .dossier {
      background: #FFFFFF;
      border: 1px solid var(--line); border-radius: 12px;
      padding: 28px; position: relative;
      box-shadow: 0 20px 40px -18px rgba(15,61,94,.2);
    }
    .dossier::after {
      content: ''; position: absolute; top: -1px; left: -1px; right: -1px; height: 4px;
      background: linear-gradient(90deg, var(--primary), var(--secondary), var(--accent));
      border-radius: 12px 12px 0 0;
    }
    .dossier-label {
      font-family: 'IBM Plex Mono', monospace; font-size: 10.5px;
      letter-spacing: 1.5px; text-transform: uppercase; color: var(--primary);
      margin-bottom: 18px; font-weight: 600; display: flex; align-items: center; gap: 10px;
    }
    .dossier-label::after { content: ''; flex: 1; height: 1px; background: linear-gradient(90deg, var(--accent), transparent); }
    .dossier-list { list-style: none; margin: 0 0 20px; padding: 0; }
    .dossier-list li {
      display: flex; align-items: center; gap: 12px; padding: 12px 0;
      border-bottom: 1px solid var(--line); font-size: 14px; color: var(--primary); font-weight: 500;
    }
    .dossier-list li:last-child { border-bottom: none; }
    .dossier-num {
      font-family: 'IBM Plex Mono', monospace; font-size: 11px; color: #FFF;
      width: 26px; height: 26px; font-weight: 600;
      border-radius: 6px; display: flex; align-items: center; justify-content: center;
    }
    .dossier-list li:nth-child(1) .dossier-num { background: var(--primary); }
    .dossier-list li:nth-child(2) .dossier-num { background: var(--secondary); }
    .dossier-list li:nth-child(3) .dossier-num { background: var(--accent-dark); }
    .dossier-list li:nth-child(4) .dossier-num { background: var(--secondary-dark); }
    .dossier-stat { border-top: 2px solid var(--bg-2); padding-top: 18px; display: flex; align-items: center; gap: 16px; }
    .dossier-stat-num {
      font-family: 'Fraunces', serif; font-size: 44px; font-weight: 700;
      color: var(--primary);
      line-height: 1;
    }
    .dossier-stat-label { font-size: 13px; color: var(--text-soft); line-height: 1.4; }
    .dossier-stat-label strong { color: var(--primary); }

    /* Features */
    .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; }
    .feature-card {
      background: #FFFFFF;
      border: 1px solid var(--line);
      border-radius: 10px;
      padding: 26px;
      transition: all .3s ease;
      position: relative;
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(15,61,94,.04);
    }
    .feature-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; }
    .feature-card:nth-child(1)::before { background: var(--primary); }
    .feature-card:nth-child(2)::before { background: var(--secondary); }
    .feature-card:nth-child(3)::before { background: var(--accent); }
    .feature-card:nth-child(4)::before { background: var(--secondary-dark); }

    .feature-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 20px 36px -16px rgba(15,61,94,.25);
      border-color: rgba(15,61,94,.15);
    }
    .feature-icon-wrapper {
      width: 52px; height: 52px;
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      margin-bottom: 18px;
    }
    .feature-card:nth-child(1) .feature-icon-wrapper { background: rgba(15,61,94,.1); color: var(--primary); }
    .feature-card:nth-child(2) .feature-icon-wrapper { background: rgba(74,144,184,.14); color: var(--secondary-dark); }
    .feature-card:nth-child(3) .feature-icon-wrapper { background: rgba(217,164,65,.16); color: var(--accent-dark); }
    .feature-card:nth-child(4) .feature-icon-wrapper { background: rgba(44,110,147,.14); color: var(--secondary-dark); }

    .feature-card h3 { font-size: 17px; color: var(--primary); margin-bottom: 9px; font-family: 'Fraunces', serif; font-weight: 600; line-height: 1.3; }
    .feature-card p { font-size: 14px; color: var(--text-soft); line-height: 1.6; }

    /* Workflow */
    .workflow-steps { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px; }
    .step-card {
      background: #FFFFFF;
      border: 1px solid var(--line);
      padding: 26px;
      border-radius: 10px;
      position: relative;
      overflow: hidden;
      box-shadow: 0 10px 20px -10px rgba(15,61,94,.08);
      transition: all .3s ease;
    }
    .step-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; }
    .step-card:nth-child(1)::before { background: var(--accent); }
    .step-card:nth-child(2)::before { background: var(--secondary); }
    .step-card:nth-child(3)::before { background: var(--primary); }
    .step-card:nth-child(4)::before { background: var(--secondary-dark); }
    .step-card:hover { transform: translateY(-4px); border-color: var(--secondary); }
    .step-badge {
      font-family: 'IBM Plex Mono', monospace;
      font-size: 11px;
      font-weight: 600;
      color: #FFF;
      padding: 5px 12px;
      border-radius: 6px;
      display: inline-block;
      margin-bottom: 14px;
    }
    .step-card:nth-child(1) .step-badge { background: var(--accent-dark); }
    .step-card:nth-child(2) .step-badge { background: var(--secondary); }
    .step-card:nth-child(3) .step-badge { background: var(--primary); }
    .step-card:nth-child(4) .step-badge { background: var(--secondary-dark); }
    .step-card h4 { font-size: 17px; color: var(--primary); margin-bottom: 9px; font-weight: 600; line-height: 1.3; }
    .step-card p { font-size: 14px; color: var(--text-soft); line-height: 1.6; }

    /* Slideshow */
    .slideshow {
      position: relative;
      max-width: 1180px;
      height: 340px;
      margin: 0 auto;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 24px 48px -18px rgba(15,61,94,.3);
      border: 1px solid var(--line);
      background: var(--primary-deep);
    }
    .slide {
      position: absolute; inset: 0;
      background-size: cover;
      background-position: center;
      background-color: var(--primary-deep);
      opacity: 0;
      transition: opacity .8s ease;
      display: flex;
      align-items: flex-end;
    }
    .slide.active { opacity: 1; }
    .slide-caption {
      padding: 32px;
      color: #FFF;
      width: 100%;
      background: linear-gradient(to top, rgba(10,44,67,.9) 0%, rgba(10,44,67,.5) 55%, transparent 100%);
    }
    .slide-tag {
      font-family: 'IBM Plex Mono', monospace;
      font-size: 11px;
      letter-spacing: 1.4px;
      text-transform: uppercase;
      background: var(--accent);
      color: var(--primary-deep);
      padding: 4px 12px;
      border-radius: 20px;
      display: inline-block;
      margin-bottom: 10px;
      font-weight: 600;
    }
    .slide-caption h3 {
      font-family: 'Fraunces', serif;
      font-size: 24px;
      font-weight: 600;
      max-width: 560px;
      line-height: 1.3;
      text-shadow: 0 2px 10px rgba(0,0,0,.35);
    }
    .slide-nav {
      position: absolute; top: 50%; transform: translateY(-50%);
      width: 42px; height: 42px; border-radius: 50%;
      background: rgba(10,44,67,.5);
      color: #FFF; border: 1px solid rgba(255,255,255,.3);
      cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      transition: all .2s ease; z-index: 2;
    }
    .slide-nav svg { width: 18px; height: 18px; }
    .slide-nav:hover { background: var(--accent); color: var(--primary-deep); border-color: transparent; }
    .slide-nav:focus-visible { outline: 2px solid var(--accent-light); outline-offset: 2px; }
    .slide-nav.prev { left: 18px; }
    .slide-nav.next { right: 18px; }
    .slide-dots {
      position: absolute; bottom: 16px; left: 50%; transform: translateX(-50%);
      display: flex; gap: 8px; z-index: 2;
    }
    .dot {
      width: 9px; height: 9px; border-radius: 50%;
      background: rgba(255,255,255,.5);
      border: none; padding: 0; cursor: pointer; transition: all .2s ease;
    }
    .dot.active { background: var(--accent); width: 22px; border-radius: 5px; }
    .dot:focus-visible { outline: 2px solid var(--accent-light); outline-offset: 2px; }
    @media (max-width: 640px) {
      .slideshow { height: 260px; }
      .slide-caption h3 { font-size: 18px; }
    }

    /* FAQ */
    .faq-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 22px; }
    .faq-item {
      background: #FFFFFF;
      border: 1px solid var(--line);
      border-left: 4px solid var(--secondary);
      padding: 22px 26px;
      border-radius: 8px;
      box-shadow: 0 6px 16px rgba(15,61,94,.03);
      transition: transform .2s ease;
    }
    .faq-item:nth-child(1) { border-left-color: var(--primary); }
    .faq-item:nth-child(2) { border-left-color: var(--secondary); }
    .faq-item:nth-child(3) { border-left-color: var(--accent); }
    .faq-item:nth-child(4) { border-left-color: var(--secondary-dark); }
    .faq-item:hover { transform: translateX(4px); }
    .faq-item h4 { font-size: 15px; color: var(--primary); margin-bottom: 9px; display: flex; align-items: flex-start; gap: 10px; font-weight: 600; line-height: 1.4; }
    .faq-item h4 svg { margin-top: 3px; flex-shrink: 0; }
    .faq-item p { font-size: 14px; color: var(--text-soft); line-height: 1.6; padding-left: 25px; }

    /* CTA */
    .cta-banner {
      background: linear-gradient(135deg, var(--primary-deep) 0%, var(--primary) 100%);
      border: 1px solid var(--accent);
      border-radius: 16px;
      padding: 56px 36px;
      text-align: center;
      color: #FFF;
      position: relative;
      overflow: hidden;
      box-shadow: 0 28px 56px -22px rgba(10,44,67,.5);
    }
    .cta-banner h2 {
      font-family: 'Fraunces', serif;
      font-size: 32px;
      margin-bottom: 14px;
      color: #FFFFFF;
      font-weight: 600;
    }
    .cta-banner p { font-size: 15.5px; color: rgba(255,255,255,.85); max-width: 580px; margin: 0 auto 32px; line-height: 1.65; }
    .cta-banner .btn-primary { background: var(--accent); color: var(--primary-deep); border-color: transparent; }
    .cta-banner .btn-primary:hover { background: var(--accent-light); }

    /* Footer */
    footer {
      max-width: 1180px;
      margin: 0 auto;
      padding: 32px 28px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 14px;
      font-size: 12.5px;
      color: rgba(255,255,255,.7);
      border-top: 1px solid rgba(255,255,255,.16);
    }
    footer .foot-mono { font-family: 'IBM Plex Mono', monospace; letter-spacing: .5px; font-size: 11px; text-transform: uppercase; }
    footer .foot-mono:first-child { color: #FFFFFF; font-weight: 600; }
    footer .foot-mono:last-child { color: var(--accent-light); font-weight: 700; }

    @media (max-width: 960px) {
      .hero-wrap { grid-template-columns: 1fr; gap: 40px; }
      .nav-links { display: none; }
      .faq-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
      section { padding: 48px 18px; }
      .letterhead-inner { padding: 14px 18px; }
      .brand-text span { font-size: 8.5px; }
      .cta-banner { padding: 36px 20px; }
      .cta-banner h2 { font-size: 26px; }
      footer { flex-direction: column; align-items: flex-start; }
    }
  </style>
</head>
<body>

  <div class="ikat-strip"></div>

  <header class="letterhead">
    <div class="letterhead-inner">
      <div class="brand">
        <img src="{{ $img['logo'] }}"
             alt="Lambang Provinsi Nusa Tenggara Timur"
             class="seal"
             onerror="this.onerror=null; this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%23D9A441%22 stroke-width=%221.6%22><path d=%22M3 21h18%22/><path d=%22M5 21V9l7-5 7 5v12%22/><path d=%22M9 21v-6h6v6%22/><path d=%22M9 9h.01M12 9h.01M15 9h.01%22/></svg>'">
        <div class="brand-text">
          <strong>e-SAKIPKU</strong>
          <span>Pemerintah Provinsi Nusa Tenggara Timur</span>
        </div>
      </div>

      <nav>
        <ul class="nav-links">
          <li><a href="#keunggulan">Keunggulan</a></li>
          <li><a href="#alur">Alur Kerja</a></li>
          <li><a href="#faq">FAQ</a></li>
        </ul>
      </nav>

      <a href="{{ Route::has('login') ? route('login') : '#' }}" class="topbar-login">
        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="15" r="4"/><path d="M10.5 12.5L20 3M20 3h-4M20 3v4"/></svg>
        Masuk Portal
      </a>
    </div>
  </header>

  <div class="ikat-strip dim"></div>

  <main>
    <div class="hero-wrap">
      <div>
        <div class="eyebrow">Akuntabilitas Kinerja Instansi Pemerintah</div>

        <h1>Kinerja Perangkat Daerah, tercatat dari akar hingga <em>hasil</em>.</h1>

        <p class="lede">
          e-SAKIPKU menghimpun <strong>perjanjian kinerja</strong>, <strong>evaluasi LKE</strong>, dan <strong>laporan LKIP</strong>
          setiap Perangkat Daerah se-Nusa Tenggara Timur dalam satu sistem —
          dari <em>cascading</em> pohon kinerja tingkat OPD hingga rekapitulasi
          capaian tingkat provinsi.
        </p>

        <div class="hero-actions">
          <a href="{{ Route::has('login') ? route('login') : '#' }}" class="btn-primary">
            Masuk ke Sistem
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M5 12h14"/><path d="M13 6l6 6-6 6"/>
            </svg>
          </a>
        </div>

        <div class="hero-note" style="margin-top: 16px;">
          <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          Khusus <strong>Admin Biro Organisasi</strong> &amp; <strong>Operator OPD</strong> terdaftar.
        </div>
      </div>

      <aside class="dossier">
        <div class="dossier-label">
          <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3h6l1 2h4v2H4V5h4l1-2z"/><path d="M5 7l1 13a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-13"/><path d="M10 11v6M14 11v6"/></svg>
          Modul Utama Sistem
        </div>
        <ul class="dossier-list">
          <li><span class="dossier-num">1</span> Perjanjian Kinerja &amp; RENSTRA</li>
          <li><span class="dossier-num">2</span> Evaluasi LKE &amp; Klaster</li>
          <li><span class="dossier-num">3</span> Cascading Pohon Kinerja</li>
          <li><span class="dossier-num">4</span> Pelaporan LKIP Online</li>
        </ul>
        <div class="dossier-stat">
          <div class="dossier-stat-num">42</div>
          <div class="dossier-stat-label"><strong>Perangkat Daerah</strong><br><span style="font-size:12px;">terkoneksi dalam portal</span></div>
        </div>
      </aside>
    </div>

    <section id="keunggulan">
      <div class="section-header">
        <div class="section-eyebrow">Fitur &amp; Keunggulan</div>
        <h2 class="section-title">Inovasi Digitalisasi Kinerja Daerah</h2>
        <p class="section-desc">Mewujudkan tata kelola pemerintahan Provinsi NTT yang akuntabel, transparan, dan berorientasi pada hasil nyata.</p>
      </div>

      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon-wrapper">
            <svg class="ic-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 3v6"/><path d="M12 9c-4 0-6.5 2.5-6.5 6.5"/><path d="M12 9c4 0 6.5 2.5 6.5 6.5"/>
              <circle cx="12" cy="3" r="1.3" fill="currentColor" stroke="none"/>
              <circle cx="5.5" cy="15.5" r="1.2" fill="currentColor" stroke="none"/>
              <circle cx="18.5" cy="15.5" r="1.2" fill="currentColor" stroke="none"/>
            </svg>
          </div>
          <h3>Cascading Pohon Kinerja</h3>
          <p>Memetakan sasaran strategis Pemprov NTT hingga indikator kinerja individu secara terstruktur dan transparan.</p>
        </div>

        <div class="feature-card">
          <div class="feature-icon-wrapper">
            <svg class="ic-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 12a9 9 0 1 1-3-6.7"/><path d="M21 3v6h-6"/>
            </svg>
          </div>
          <h3>Revisi &amp; Pengajuan Online</h3>
          <p>Proses pembaruan indikator dan revisi Perjanjian Kinerja (PK) dapat dilakukan online tanpa pertemuan tatap muka.</p>
        </div>

        <div class="feature-card">
          <div class="feature-icon-wrapper">
            <svg class="ic-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 20V10"/><path d="M11 20V4"/><path d="M18 20v-7"/>
            </svg>
          </div>
          <h3>Evaluasi LKE Real-time</h3>
          <p>Penilaian Lembar Kerja Evaluasi SAKIP otomatis terintegrasi dengan klaster predikat OPD secara presisi.</p>
        </div>

        <div class="feature-card">
          <div class="feature-icon-wrapper">
            <svg class="ic-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="4" width="18" height="5" rx="1"/><path d="M5 9v9a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V9"/><path d="M10 13h4"/>
            </svg>
          </div>
          <h3>Arsip LKIP Terpusat</h3>
          <p>Seluruh dokumen Laporan Kinerja Instansi Pemerintah tersimpan rapi dan dapat diakses dengan aman kapan saja.</p>
        </div>
      </div>
    </section>

    <section id="alur">
      <div class="section-header">
        <div class="section-eyebrow">Tahapan Kerja</div>
        <h2 class="section-title">Alur Pengelolaan SAKIP</h2>
        <p class="section-desc">Langkah mudah pengelolaan akuntabilitas kinerja bagi setiap Operator Perangkat Daerah.</p>
      </div>

      <div class="workflow-steps">
        <div class="step-card">
          <span class="step-badge">Langkah 01</span>
          <h4>Input Renstra &amp; PK</h4>
          <p>Operator OPD mengunggah dokumen Perjanjian Kinerja dan Indikator Kinerja Utama (IKU).</p>
        </div>

        <div class="step-card">
          <span class="step-badge">Langkah 02</span>
          <h4>Verifikasi Biro</h4>
          <p>Biro Organisasi memverifikasi keterkaitan (cascading) indikator kinerja OPD dengan Pemprov.</p>
        </div>

        <div class="step-card">
          <span class="step-badge">Langkah 03</span>
          <h4>Evaluasi &amp; LKE</h4>
          <p>Tim Evaluator melakukan penilaian mandiri (LKE) dan memberikan catatan rekomendasi.</p>
        </div>

        <div class="step-card">
          <span class="step-badge">Langkah 04</span>
          <h4>Pelaporan LKIP</h4>
          <p>Penerbitan rekapitulasi nilai akhir SAKIP dan pelaporan LKIP Provinsi NTT secara akuntabel.</p>
        </div>
      </div>
    </section>

    <section id="galeri" style="padding-top: 32px; padding-bottom: 32px;">
      <div class="slideshow" id="slideshow">
        <div class="slide active" style="background-image: url('{{ $img['gallery_1'] }}');">
          <div class="slide-caption">
            <span class="slide-tag">Rapat Koordinasi</span>
            <h3>Evaluasi Kinerja Perangkat Daerah se-NTT</h3>
          </div>
        </div>
        <div class="slide" style="background-image: url('{{ $img['gallery_2'] }}');">
          <div class="slide-caption">
            <span class="slide-tag">Cascading Kinerja</span>
            <h3>Sinkronisasi Indikator OPD dengan Sasaran Provinsi</h3>
          </div>
        </div>
        <div class="slide" style="background-image: url('{{ $img['gallery_3'] }}');">
          <div class="slide-caption">
            <span class="slide-tag">Pelaporan LKIP</span>
            <h3>Dokumentasi Akuntabilitas Kinerja Terpadu</h3>
          </div>
        </div>

        <button class="slide-nav prev" onclick="changeSlide(-1)" aria-label="Sebelumnya">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
        </button>
        <button class="slide-nav next" onclick="changeSlide(1)" aria-label="Berikutnya">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
        </button>

        <div class="slide-dots">
          <button class="dot active" onclick="goToSlide(0)" aria-label="Slide 1"></button>
          <button class="dot" onclick="goToSlide(1)" aria-label="Slide 2"></button>
          <button class="dot" onclick="goToSlide(2)" aria-label="Slide 3"></button>
        </div>
      </div>
    </section>

    <script>
      let currentSlide = 0;
      const slides = document.querySelectorAll('#slideshow .slide');
      const dots = document.querySelectorAll('#slideshow .dot');
      let slideTimer;
      const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

      function showSlide(index) {
        slides.forEach(s => s.classList.remove('active'));
        dots.forEach(d => d.classList.remove('active'));
        currentSlide = (index + slides.length) % slides.length;
        slides[currentSlide].classList.add('active');
        dots[currentSlide].classList.add('active');
      }
      function changeSlide(dir) { showSlide(currentSlide + dir); resetTimer(); }
      function goToSlide(i) { showSlide(i); resetTimer(); }
      function resetTimer() {
        clearInterval(slideTimer);
        if (!reduceMotion) slideTimer = setInterval(() => showSlide(currentSlide + 1), 5000);
      }
      if (slides.length) resetTimer();
    </script>

    <section id="faq">
      <div class="section-header">
        <div class="section-eyebrow">Pertanyaan Umum</div>
        <h2 class="section-title">Bantuan &amp; Informasi</h2>
        <p class="section-desc">Hal yang sering ditanyakan mengenai akses dan operasional e-SAKIPKU.</p>
      </div>

      <div class="faq-grid">
        <div class="faq-item">
          <h4><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 0 1 5 0c0 1.5-2.5 2-2.5 3.5"/><circle cx="12" cy="16.5" r=".3" fill="currentColor"/></svg> Siapa saja yang dapat mengakses sistem ini?</h4>
          <p>Akses akun diberikan resmi kepada Admin Biro Organisasi Sekretariat Daerah Provinsi NTT serta Operator SAKIP terdaftar di masing-masing Perangkat Daerah.</p>
        </div>

        <div class="faq-item">
          <h4><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 0 1 5 0c0 1.5-2.5 2-2.5 3.5"/><circle cx="12" cy="16.5" r=".3" fill="currentColor"/></svg> Bagaimana jika OPD lupa kredensial akun?</h4>
          <p>Silakan hubungi Administrator Biro Organisasi Setda Prov. NTT melalui layanan bantuan resmi dengan melampirkan Surat Pengantar Instansi.</p>
        </div>

        <div class="faq-item">
          <h4><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 0 1 5 0c0 1.5-2.5 2-2.5 3.5"/><circle cx="12" cy="16.5" r=".3" fill="currentColor"/></svg> Kapan batas waktu pengunggahan LKIP Tahunan?</h4>
          <p>Pengunggahan dokumen LKIP dilakukan sesuai jadwal reguler yang ditetapkan dalam Surat Edaran Gubernur Nusa Tenggara Timur.</p>
        </div>

        <div class="faq-item">
          <h4><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 0 1 5 0c0 1.5-2.5 2-2.5 3.5"/><circle cx="12" cy="16.5" r=".3" fill="currentColor"/></svg> Apakah sistem ini mendukung revisi indikator kinerja?</h4>
          <p>Ya, revisi indikator dapat diajukan melalui modul Perjanjian Kinerja untuk kemudian diverifikasi online oleh Biro Organisasi.</p>
        </div>
      </div>
    </section>

    <section>
      <div class="cta-banner">
        <h2>Siap Mengelola Akuntabilitas Kinerja?</h2>
        <p>Akses akun Anda untuk mulai mengelola dokumen Perjanjian Kinerja, LKE, dan Laporan Kinerja Perangkat Daerah secara terpadu.</p>
        <a href="{{ Route::has('login') ? route('login') : '#' }}" class="btn-primary" style="position: relative; z-index: 2;">
          Masuk ke Portal e-SAKIPKU
        </a>
      </div>
    </section>
  </main>

  <div class="ikat-strip dim"></div>

  <footer>
    <div class="foot-mono">© {{ date('Y') }} Pemerintah Provinsi Nusa Tenggara Timur · Biro Organisasi Setda</div>
    <div class="foot-mono">e-SAKIPKU · Akuntabilitas Kinerja Terintegrasi</div>
  </footer>

</body>
</html>