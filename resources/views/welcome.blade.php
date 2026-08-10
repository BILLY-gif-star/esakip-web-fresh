<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>e-SAKIPKU — Sistem Akuntabilitas Kinerja Instansi Pemerintah Provinsi NTT</title>
  <meta name="description" content="e-SAKIPKU menghimpun perjanjian kinerja, evaluasi LKE, dan laporan LKIP seluruh Perangkat Daerah Provinsi Nusa Tenggara Timur dalam satu sistem.">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=IBM+Plex+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📊</text></svg>">

  <style>
    :root {
      --indigo: #16213F;
      --indigo-deep: #0B1220;
      --gold: #C99A3E;
      --gold-bright: #F5C259;
      --gold-light: #F8E8B0;
      --teal: #1F8275;
      --teal-light: #31B0A1;
      --teal-bright: #5FC4B8;
      --paper: #F1EAE0;
      --paper-2: #E8DFCE;
      --ink: #1E1C18;
      --ink-soft: #4F4A40;
      --line: rgba(22,33,63,.12);
      --coral: #E05A47;
      --coral-light: #F5B89A;
      --coral-bright: #FF8A75;
      --purple: #6C5CE7;
      --purple-light: #A88BD4;
      --blue-accent: #007AFF;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }

    body {
      margin: 0;
      background:
        radial-gradient(ellipse 1200px 900px at 8% -5%, rgba(224,90,71,.4), transparent 60%),
        radial-gradient(ellipse 1200px 900px at 95% 8%, rgba(201,154,62,.44), transparent 60%),
        radial-gradient(ellipse 1100px 1000px at 88% 50%, rgba(31,130,117,.4), transparent 60%),
        radial-gradient(ellipse 1000px 1000px at 3% 55%, rgba(108,92,231,.38), transparent 60%),
        radial-gradient(ellipse 1200px 800px at 50% 100%, rgba(31,176,161,.36), transparent 62%),
        radial-gradient(ellipse 900px 800px at 45% 35%, rgba(245,194,89,.24), transparent 65%),
        radial-gradient(ellipse 700px 700px at 70% 85%, rgba(255,138,117,.22), transparent 60%),
        var(--paper);
      background-size: 100% 320%;
      animation: bgDrift 30s ease-in-out infinite alternate;
    }
    @keyframes bgDrift {
      0% { background-position: 0 0%; }
      100% { background-position: 0 100%; }
      color: var(--ink);
      font-family: 'Plus Jakarta Sans', sans-serif;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      overflow-x: hidden;
      position: relative;
    }

    body::before {
      content: '';
      position: fixed;
      inset: 0;
      pointer-events: none;
      z-index: 50;
      opacity: .03;
      mix-blend-mode: multiply;
      background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='120' height='120'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2' stitchTiles='stitch'/></filter><rect width='100%25' height='100%25' filter='url(%23n)'/></svg>");
    }

    a { color: inherit; text-decoration: none; }

    .ikat-strip {
      height: 10px;
      background-image:
        linear-gradient(45deg, var(--gold) 23%, transparent 23%),
        linear-gradient(-45deg, var(--coral) 23%, transparent 23%),
        linear-gradient(45deg, var(--teal) 23%, transparent 23%),
        linear-gradient(-45deg, var(--purple) 23%, transparent 23%);
      background-size: 16px 16px;
      opacity: .8;
    }
    .ikat-strip.dim { opacity: .35; }

    .letterhead {
      background: linear-gradient(135deg, var(--indigo-deep), #1A2B4C, #132238);
      position: relative;
      z-index: 10;
      border-bottom: 3px solid var(--gold);
    }
    .letterhead::after {
      content: '';
      position: absolute;
      bottom: -3px; left: 0; right: 0;
      height: 3px;
      background: linear-gradient(90deg, var(--gold), var(--coral), var(--teal-light), var(--purple-light), var(--gold-bright));
      background-size: 200% 100%;
      animation: gradientMove 4s ease-in-out infinite;
    }

    @keyframes gradientMove {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
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
      filter: drop-shadow(0 0 10px rgba(245,194,89,.4));
      background: rgba(255,255,255,.08);
      border-radius: 50%;
      padding: 3px;
      border: 1px solid rgba(245,194,89,.3);
    }
    .brand-text strong {
      display: block;
      font-family: 'Fraunces', serif;
      font-size: 20px;
      font-weight: 700;
      background: linear-gradient(135deg, #FFFFFF, var(--gold-bright));
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }
    .brand-text span {
      display: block;
      font-family: 'IBM Plex Mono', monospace;
      font-size: 9.5px;
      letter-spacing: 1.2px;
      color: var(--teal-bright);
      text-transform: uppercase;
      margin-top: 3px;
    }

    .nav-links { display: flex; align-items: center; gap: 28px; list-style: none; }
    .nav-links a { font-size: 14px; color: rgba(249,246,239,.85); font-weight: 500; transition: all .2s; }
    .nav-links a:hover { color: var(--gold-bright); text-shadow: 0 0 12px rgba(245,194,89,.4); }

    .topbar-login {
      font-family: 'IBM Plex Mono', monospace;
      font-size: 11px;
      letter-spacing: .8px;
      text-transform: uppercase;
      color: #FFF;
      border: 1px solid var(--gold-bright);
      border-radius: 6px;
      padding: 10px 22px;
      transition: all .3s ease;
      background: linear-gradient(135deg, rgba(201,154,62,.2), rgba(224,90,71,.2));
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,.15);
    }
    .topbar-login:hover {
      background: linear-gradient(135deg, var(--gold-bright), var(--coral));
      color: var(--indigo-deep);
      border-color: transparent;
      box-shadow: 0 0 25px rgba(245,194,89,.5);
      transform: translateY(-2px);
      font-weight: 600;
    }

    section { max-width: 1180px; margin: 0 auto; padding: 72px 28px; }
    .section-header { text-align: center; max-width: 680px; margin: 0 auto 52px; }
    .section-eyebrow {
      font-family: 'IBM Plex Mono', monospace;
      font-size: 11.5px;
      letter-spacing: 1.8px;
      text-transform: uppercase;
      color: var(--teal);
      margin-bottom: 10px;
      font-weight: 600;
      background: rgba(31,130,117,.1);
      display: inline-block;
      padding: 4px 14px;
      border-radius: 20px;
      border: 1px solid rgba(31,130,117,.2);
    }
    .section-title { font-family: 'Fraunces', serif; font-size: 34px; color: var(--indigo-deep); margin-bottom: 12px; font-weight: 600; }
    .section-desc { font-size: 15.5px; color: var(--ink-soft); line-height: 1.6; }

    #alur .section-eyebrow { color: var(--coral); background: rgba(224,90,71,.1); border-color: rgba(224,90,71,.2); }
    #faq .section-eyebrow { color: var(--purple); background: rgba(108,92,231,.1); border-color: rgba(108,92,231,.2); }

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
      background: linear-gradient(110deg, var(--gold), var(--coral) 40%, var(--teal-light) 70%, var(--purple));
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 24px;
      font-weight: 600;
    }
    .eyebrow::before {
      content: ''; width: 28px; height: 3px;
      background: linear-gradient(90deg, var(--gold), var(--coral), var(--teal-light));
      display: inline-block; border-radius: 3px;
    }
    h1 {
      font-family: 'Fraunces', serif;
      font-weight: 600;
      font-size: clamp(38px, 4.5vw, 58px);
      line-height: 1.1;
      letter-spacing: -.015em;
      color: var(--indigo-deep);
      margin: 0 0 24px;
    }
    h1 em {
      font-style: italic; font-weight: 600;
      background: linear-gradient(120deg, #16213F, var(--coral) 45%, var(--purple));
      -webkit-background-clip: text;
      background-clip: text; color: transparent;
    }
    .lede { font-size: 16px; line-height: 1.75; color: var(--ink-soft); margin: 0 0 36px; }
    .lede strong { color: var(--teal); font-weight: 600; }

    .hero-actions { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; margin-bottom: 16px; }
    .btn-primary {
      display: inline-flex; align-items: center; gap: 10px;
      background: linear-gradient(135deg, var(--indigo-deep), var(--indigo), #2A4A7A);
      color: #FFF; font-weight: 600; font-size: 15px;
      padding: 16px 32px; border-radius: 8px; border: 1px solid var(--gold-bright);
      box-shadow: 0 12px 24px -6px rgba(22,33,63,.35);
      transition: all .3s cubic-bezier(.2,.7,.3,1);
    }
    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 18px 32px -8px rgba(22,33,63,.5);
      border-color: #FFF;
      background: linear-gradient(135deg, #1E3A5F, var(--indigo-deep));
    }

    .hero-note { font-size: 12.5px; color: var(--ink-soft); }
    .hero-note strong {
      color: var(--teal); font-weight: 600;
      background: rgba(31,130,117,.12); padding: 3px 10px;
      border-radius: 12px; border: 1px solid rgba(31,130,117,.25);
    }

    .dossier {
      background: #FFFCF6;
      border: 1px solid rgba(201,154,62,.4); border-radius: 12px;
      padding: 28px; position: relative;
      box-shadow: 0 20px 40px -15px rgba(22,33,63,.15);
    }
    .dossier::after {
      content: ''; position: absolute; top: -1px; left: -1px; right: -1px; height: 5px;
      background: linear-gradient(90deg, var(--gold), var(--coral), var(--teal-light), var(--purple));
      border-radius: 12px 12px 0 0;
    }
    .dossier-label {
      font-family: 'IBM Plex Mono', monospace; font-size: 10.5px;
      letter-spacing: 1.5px; text-transform: uppercase; color: var(--indigo);
      margin-bottom: 18px; font-weight: 600; display: flex; align-items: center; gap: 10px;
    }
    .dossier-label::after { content: ''; flex: 1; height: 1px; background: linear-gradient(90deg, var(--gold), transparent); }
    .dossier-list { list-style: none; margin: 0 0 20px; padding: 0; }
    .dossier-list li {
      display: flex; align-items: center; gap: 12px; padding: 12px 0;
      border-bottom: 1px solid rgba(22,33,63,.08); font-size: 14px; color: var(--indigo); font-weight: 500;
    }
    .dossier-num {
      font-family: 'IBM Plex Mono', monospace; font-size: 11px; color: #FFF;
      width: 26px; height: 26px; font-weight: 600; background: linear-gradient(135deg, var(--coral), var(--gold));
      border-radius: 6px; display: flex; align-items: center; justify-content: center;
      box-shadow: 0 4px 8px rgba(224,90,71,.3);
    }
    .dossier-list li:nth-child(1) .dossier-num { background: linear-gradient(135deg, var(--teal), var(--teal-light)); box-shadow: 0 4px 8px rgba(31,130,117,.3); }
    .dossier-list li:nth-child(2) .dossier-num { background: linear-gradient(135deg, var(--coral), var(--coral-bright)); box-shadow: 0 4px 8px rgba(224,90,71,.3); }
    .dossier-list li:nth-child(3) .dossier-num { background: linear-gradient(135deg, var(--gold), var(--gold-bright)); box-shadow: 0 4px 8px rgba(201,154,62,.3); }
    .dossier-list li:nth-child(4) .dossier-num { background: linear-gradient(135deg, var(--purple), var(--purple-light)); box-shadow: 0 4px 8px rgba(108,92,231,.3); }
    .dossier-stat { border-top: 2px solid var(--gold-light); padding-top: 18px; display: flex; align-items: center; gap: 16px; }
    .dossier-stat-num {
      font-family: 'Fraunces', serif; font-size: 44px; font-weight: 700;
      background: linear-gradient(120deg, var(--teal), var(--indigo), var(--coral));
      -webkit-background-clip: text; background-clip: text; color: transparent;
      line-height: 1;
    }

    .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; }
    .feature-card {
      background: #FFFCF6;
      border: 1px solid var(--line);
      border-radius: 10px;
      padding: 26px;
      transition: all .3s ease;
      position: relative;
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(0,0,0,.03);
    }
    .feature-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; }
    .feature-card:nth-child(1)::before { background: linear-gradient(90deg, var(--teal), var(--teal-light)); }
    .feature-card:nth-child(2)::before { background: linear-gradient(90deg, var(--coral), var(--coral-bright)); }
    .feature-card:nth-child(3)::before { background: linear-gradient(90deg, var(--gold), var(--gold-bright)); }
    .feature-card:nth-child(4)::before { background: linear-gradient(90deg, var(--purple), var(--purple-light)); }

    .feature-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 24px 44px -14px rgba(22,33,63,.28);
      border-color: rgba(0,0,0,.08);
    }
    .feature-icon-wrapper {
      width: 52px; height: 52px;
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 26px; margin-bottom: 18px;
    }
    .feature-card:nth-child(1) .feature-icon-wrapper { background: linear-gradient(135deg, rgba(31,130,117,.22), rgba(95,196,184,.22)); box-shadow: inset 0 0 0 1px rgba(31,130,117,.25); }
    .feature-card:nth-child(2) .feature-icon-wrapper { background: linear-gradient(135deg, rgba(224,90,71,.22), rgba(255,138,117,.22)); box-shadow: inset 0 0 0 1px rgba(224,90,71,.25); }
    .feature-card:nth-child(3) .feature-icon-wrapper { background: linear-gradient(135deg, rgba(201,154,62,.28), rgba(245,194,89,.28)); box-shadow: inset 0 0 0 1px rgba(201,154,62,.3); }
    .feature-card:nth-child(4) .feature-icon-wrapper { background: linear-gradient(135deg, rgba(108,92,231,.22), rgba(168,139,212,.22)); box-shadow: inset 0 0 0 1px rgba(108,92,231,.25); }
    .feature-card:nth-child(1) { border-color: rgba(31,130,117,.18); }
    .feature-card:nth-child(2) { border-color: rgba(224,90,71,.18); }
    .feature-card:nth-child(3) { border-color: rgba(201,154,62,.22); }
    .feature-card:nth-child(4) { border-color: rgba(108,92,231,.18); }

    .feature-card h3 { font-size: 18px; color: var(--indigo-deep); margin-bottom: 10px; font-family: 'Fraunces', serif; font-weight: 600; }
    .feature-card p { font-size: 13.5px; color: var(--ink-soft); line-height: 1.65; }

    .workflow-steps { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px; }
    .step-card {
      background: linear-gradient(145deg, #FFFFFF, var(--paper-2));
      border: 1px solid rgba(201,154,62,.3);
      padding: 26px;
      border-radius: 10px;
      position: relative;
      overflow: hidden;
      box-shadow: 0 10px 20px -8px rgba(0,0,0,.05);
      transition: all .3s ease;
    }
    .step-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; }
    .step-card:nth-child(1)::before { background: linear-gradient(90deg, var(--gold), var(--gold-bright)); }
    .step-card:nth-child(2)::before { background: linear-gradient(90deg, var(--teal), var(--teal-light)); }
    .step-card:nth-child(3)::before { background: linear-gradient(90deg, var(--coral), var(--coral-bright)); }
    .step-card:nth-child(4)::before { background: linear-gradient(90deg, var(--purple), var(--purple-light)); }
    .step-card:hover { transform: translateY(-4px); border-color: var(--teal); }
    .step-badge {
      font-family: 'IBM Plex Mono', monospace;
      font-size: 11px;
      font-weight: 600;
      color: #FFF;
      padding: 5px 12px;
      border-radius: 6px;
      display: inline-block;
      margin-bottom: 14px;
      box-shadow: 0 4px 10px rgba(22,33,63,.2);
    }
    .step-card:nth-child(1) .step-badge { background: linear-gradient(135deg, var(--gold), #B8842E); }
    .step-card:nth-child(2) .step-badge { background: linear-gradient(135deg, var(--teal), var(--teal-light)); }
    .step-card:nth-child(3) .step-badge { background: linear-gradient(135deg, var(--coral), var(--coral-bright)); }
    .step-card:nth-child(4) .step-badge { background: linear-gradient(135deg, var(--purple), var(--purple-light)); }
    .step-card h4 { font-size: 17px; color: var(--indigo-deep); margin-bottom: 10px; font-weight: 600; }
    .step-card p { font-size: 13.5px; color: var(--ink-soft); line-height: 1.6; }

    .faq-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 22px; }
    .faq-item {
      background: #FFFCF6;
      border: 1px solid var(--line);
      border-left: 4px solid var(--teal);
      padding: 22px 26px;
      border-radius: 8px;
      box-shadow: 0 6px 16px rgba(0,0,0,.02);
      transition: transform .2s ease, border-left-color .2s ease;
    }
    .faq-item:nth-child(1) { border-left-color: var(--teal); }
    .faq-item:nth-child(2) { border-left-color: var(--coral); }
    .faq-item:nth-child(3) { border-left-color: var(--gold); }
    .faq-item:nth-child(4) { border-left-color: var(--purple); }
    .faq-item:hover { transform: translateX(4px); }
    .faq-item h4 { font-size: 15.5px; color: var(--indigo-deep); margin-bottom: 10px; display: flex; align-items: center; gap: 10px; font-weight: 600; }
    .faq-item p { font-size: 13.8px; color: var(--ink-soft); line-height: 1.65; }

    .cta-banner {
      background: linear-gradient(135deg, var(--indigo-deep) 0%, #1A2E4E 50%, var(--indigo) 100%);
      border: 2px solid var(--gold-bright);
      border-radius: 16px;
      padding: 56px 36px;
      text-align: center;
      color: #FFF;
      position: relative;
      overflow: hidden;
      box-shadow: 0 28px 56px -20px rgba(11,18,32,.6);
    }
    .cta-banner::before {
      content: ''; position: absolute; inset: 0;
      background: radial-gradient(circle at top right, rgba(245,194,89,.2), transparent 60%),
                  radial-gradient(circle at bottom left, rgba(224,90,71,.2), transparent 60%);
      pointer-events: none;
    }
    .cta-banner h2 {
      font-family: 'Fraunces', serif;
      font-size: 32px;
      margin-bottom: 14px;
      background: linear-gradient(120deg, #FFFFFF, var(--gold-bright));
      -webkit-background-clip: text; background-clip: text; color: transparent;
      font-weight: 600;
    }
    .cta-banner p { font-size: 15.5px; color: rgba(249,246,239,.85); max-width: 580px; margin: 0 auto 32px; line-height: 1.65; }

    .slideshow {
      position: relative;
      max-width: 1180px;
      height: 340px;
      margin: 0 auto;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 24px 48px -18px rgba(11,18,32,.35);
      border: 1px solid rgba(201,154,62,.35);
    }
    .slide {
      position: absolute; inset: 0;
      background-size: cover;
      background-position: center;
      opacity: 0;
      transition: opacity .8s ease;
      display: flex;
      align-items: flex-end;
    }
    .slide.active { opacity: 1; }
    .slide-caption { padding: 32px; color: #FFF; }
    .slide-tag {
      font-family: 'IBM Plex Mono', monospace;
      font-size: 11px;
      letter-spacing: 1.4px;
      text-transform: uppercase;
      background: linear-gradient(135deg, var(--gold), var(--coral));
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
      text-shadow: 0 2px 10px rgba(0,0,0,.3);
    }
    .slide-nav {
      position: absolute; top: 50%; transform: translateY(-50%);
      width: 42px; height: 42px; border-radius: 50%;
      background: rgba(11,18,32,.45);
      color: #FFF; border: 1px solid rgba(255,255,255,.3);
      font-size: 22px; line-height: 1; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      transition: all .2s ease; z-index: 2;
    }
    .slide-nav:hover { background: var(--gold-bright); color: var(--indigo-deep); border-color: transparent; }
    .slide-nav.prev { left: 18px; }
    .slide-nav.next { right: 18px; }
    .slide-dots {
      position: absolute; bottom: 16px; left: 50%; transform: translateX(-50%);
      display: flex; gap: 8px; z-index: 2;
    }
    .dot {
      width: 9px; height: 9px; border-radius: 50%;
      background: rgba(255,255,255,.45);
      cursor: pointer; transition: all .2s ease;
    }
    .dot.active { background: var(--gold-bright); width: 22px; border-radius: 5px; }
    @media (max-width: 640px) {
      .slideshow { height: 260px; }
      .slide-caption h3 { font-size: 18px; }
    }

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
      color: var(--ink-soft);
      border-top: 2px solid transparent;
      border-image: linear-gradient(90deg, var(--gold), var(--coral), var(--teal-light), var(--purple)) 1;
    }
    footer .foot-mono { font-family: 'IBM Plex Mono', monospace; letter-spacing: .5px; font-size: 11px; text-transform: uppercase; }
    footer .foot-mono:first-child { color: var(--teal); font-weight: 600; }
    footer .foot-mono:last-child {
      background: linear-gradient(90deg, var(--coral), var(--purple));
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent; font-weight: 700;
    }

    @media (max-width: 960px) {
      .hero-wrap { grid-template-columns: 1fr; gap: 40px; }
      .nav-links { display: none; }
      .faq-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
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
        <img src="{{ asset('assets/logo_ntt.png') }}"
             alt="Lambang Provinsi Nusa Tenggara Timur"
             class="seal"
             onerror="this.onerror=null; this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🏛️</text></svg>'">
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
        🔑 Masuk Portal
      </a>
    </div>
  </header>

  <div class="ikat-strip dim"></div>

  <main class="hero-wrap" style="position: relative;">
    <div style="position:absolute; top:-40px; right:8%; width:220px; height:220px; background:radial-gradient(circle, rgba(224,90,71,.18), transparent 70%); filter:blur(6px); pointer-events:none; z-index:-1;"></div>
    <div style="position:absolute; bottom:-30px; left:35%; width:180px; height:180px; background:radial-gradient(circle, rgba(108,92,231,.16), transparent 70%); filter:blur(6px); pointer-events:none; z-index:-1;"></div>
    <div>
      <div class="eyebrow">✨ Akuntabilitas Kinerja Instansi Pemerintah</div>

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
        🛡️ Khusus <strong>Admin Biro Organisasi</strong> &amp; <strong>Operator OPD</strong> terdaftar.
      </div>
    </div>

    <aside class="dossier">
      <div class="dossier-label">📑 Modul Utama Sistem</div>
      <ul class="dossier-list">
        <li><span class="dossier-num">1</span> Perjanjian Kinerja &amp; RENSTRA</li>
        <li><span class="dossier-num">2</span> Evaluasi LKE &amp; Klaster</li>
        <li><span class="dossier-num">3</span> Cascading Pohon Kinerja</li>
        <li><span class="dossier-num">4</span> Pelaporan LKIP Online</li>
      </ul>
      <div class="dossier-stat">
        <div class="dossier-stat-num">42</div>
        <div class="dossier-stat-label"><strong>Perangkat Daerah</strong><br><span style="font-size:12px; color:var(--ink-soft);">terkoneksi dalam portal</span></div>
      </div>
    </aside>
  </main>

  <section id="keunggulan" style="position: relative;">
    <div style="position:absolute; top:10%; left:-6%; width:260px; height:260px; background:radial-gradient(circle, rgba(31,130,117,.28), transparent 70%); filter:blur(10px); pointer-events:none; z-index:-1;"></div>
    <div style="position:absolute; bottom:0%; right:-6%; width:300px; height:300px; background:radial-gradient(circle, rgba(224,90,71,.25), transparent 70%); filter:blur(10px); pointer-events:none; z-index:-1;"></div>
    <div class="section-header">
      <div class="section-eyebrow">Fitur & Keunggulan</div>
      <h2 class="section-title">Inovasi Digitalisasi Kinerja Daerah</h2>
      <p class="section-desc">Mewujudkan tata kelola pemerintahan Provinsi NTT yang akuntabel, transparan, dan berorientasi pada hasil nyata.</p>
    </div>

    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon-wrapper">🌱</div>
        <h3>Cascading Pohon Kinerja</h3>
        <p>Memetakan sasaran strategis Pemprov NTT hingga indikator kinerja individu secara terstruktur dan transparan.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon-wrapper">⚡</div>
        <h3>Revisi & Pengajuan Online</h3>
        <p>Proses pembaruan indikator dan revisi Perjanjian Kinerja (PK) dapat dilakukan online tanpa pertemuan tatap muka.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon-wrapper">📊</div>
        <h3>Evaluasi LKE Real-time</h3>
        <p>Penilaian Lembar Kerja Evaluasi SAKIP otomatis terintegrasi dengan klaster predikat OPD secara presisi.</p>
      </div>

      <div class="feature-card">
        <div class="feature-icon-wrapper">🛡️</div>
        <h3>Arsip LKIP Terpusat</h3>
        <p>Seluruh dokumen Laporan Kinerja Instansi Pemerintah tersimpan rapi dan dapat diakses dengan aman kapan saja.</p>
      </div>
    </div>
  </section>

  <section id="alur" style="position: relative;">
    <div style="position:absolute; top:-4%; right:8%; width:280px; height:280px; background:radial-gradient(circle, rgba(108,92,231,.26), transparent 70%); filter:blur(10px); pointer-events:none; z-index:-1;"></div>
    <div style="position:absolute; bottom:5%; left:-4%; width:260px; height:260px; background:radial-gradient(circle, rgba(201,154,62,.28), transparent 70%); filter:blur(10px); pointer-events:none; z-index:-1;"></div>
    <div class="section-header">
      <div class="section-eyebrow">Tahapan Kerja</div>
      <h2 class="section-title">Alur Pengelolaan SAKIP</h2>
      <p class="section-desc">Langkah mudah pengelolaan akuntabilitas kinerja bagi setiap Operator Perangkat Daerah.</p>
    </div>

    <div class="workflow-steps">
      <div class="step-card">
        <span class="step-badge">Langkah 01</span>
        <h4>Input Renstra & PK</h4>
        <p>Operator OPD mengunggah dokumen Perjanjian Kinerja dan Indikator Kinerja Utama (IKU).</p>
      </div>

      <div class="step-card">
        <span class="step-badge">Langkah 02</span>
        <h4>Verifikasi Biro</h4>
        <p>Biro Organisasi memverifikasi keterkaitan (cascading) indikator kinerja OPD dengan Pemprov.</p>
      </div>

      <div class="step-card">
        <span class="step-badge">Langkah 03</span>
        <h4>Evaluasi & LKE</h4>
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
      <div class="slide active" style="background-image: linear-gradient(135deg, var(--indigo-deep) 0%, #1F3A63 40%, var(--teal) 100%);">
        <div class="slide-caption">
          <span class="slide-tag">Rapat Koordinasi</span>
          <h3>Evaluasi Kinerja Perangkat Daerah se-NTT</h3>
        </div>
      </div>
      <div class="slide" style="background-image: linear-gradient(135deg, #3A2A5C 0%, var(--purple) 45%, var(--coral) 100%);">
        <div class="slide-caption">
          <span class="slide-tag">Cascading Kinerja</span>
          <h3>Sinkronisasi Indikator OPD dengan Sasaran Provinsi</h3>
        </div>
      </div>
      <div class="slide" style="background-image: linear-gradient(135deg, #5C3A1E 0%, var(--gold) 45%, var(--teal-light) 100%);">
        <div class="slide-caption">
          <span class="slide-tag">Pelaporan LKIP</span>
          <h3>Dokumentasi Akuntabilitas Kinerja Terpadu</h3>
        </div>
      </div>

      <button class="slide-nav prev" onclick="changeSlide(-1)" aria-label="Sebelumnya">‹</button>
      <button class="slide-nav next" onclick="changeSlide(1)" aria-label="Berikutnya">›</button>

      <div class="slide-dots">
        <span class="dot active" onclick="goToSlide(0)"></span>
        <span class="dot" onclick="goToSlide(1)"></span>
        <span class="dot" onclick="goToSlide(2)"></span>
      </div>
    </div>
  </section>

  <script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('#slideshow .slide');
    const dots = document.querySelectorAll('#slideshow .dot');
    let slideTimer;

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
      slideTimer = setInterval(() => showSlide(currentSlide + 1), 5000);
    }
    resetTimer();
  </script>

  <section id="faq" style="position: relative;">
    <div style="position:absolute; top:0%; left:10%; width:250px; height:250px; background:radial-gradient(circle, rgba(31,176,161,.26), transparent 70%); filter:blur(10px); pointer-events:none; z-index:-1;"></div>
    <div style="position:absolute; bottom:-4%; right:6%; width:280px; height:280px; background:radial-gradient(circle, rgba(255,138,117,.24), transparent 70%); filter:blur(10px); pointer-events:none; z-index:-1;"></div>
    <div class="section-header">
      <div class="section-eyebrow">Pertanyaan Umum</div>
      <h2 class="section-title">Bantuan & Informasi</h2>
      <p class="section-desc">Hal yang sering ditanyakan mengenai akses dan operasional e-SAKIPKU.</p>
    </div>

    <div class="faq-grid">
      <div class="faq-item">
        <h4>❓ Siapa saja yang dapat mengakses sistem ini?</h4>
        <p>Akses akun diberikan resmi kepada Admin Biro Organisasi Sekretariat Daerah Provinsi NTT serta Operator SAKIP terdaftar di masing-masing Perangkat Daerah.</p>
      </div>

      <div class="faq-item">
        <h4>❓ Bagaimana jika OPD lupa kredensial akun?</h4>
        <p>Silakan hubungi Administrator Biro Organisasi Setda Prov. NTT melalui layanan bantuan resmi dengan melampirkan Surat Pengantar Instansi.</p>
      </div>

      <div class="faq-item">
        <h4>❓ Kapan batas waktu pengunggahan LKIP Tahunan?</h4>
        <p>Pengunggahan dokumen LKIP dilakukan sesuai jadwal reguler yang ditetapkan dalam Surat Edaran Gubernur Nusa Tenggara Timur.</p>
      </div>

      <div class="faq-item">
        <h4>❓ Apakah sistem ini mendukung revisi indikator kinerja?</h4>
        <p>Ya, revisi indikator dapat diajukan melalui modul Perjanjian Kinerja untuk kemudian diverifikasi online oleh Biro Organisasi.</p>
      </div>
    </div>
  </section>

  <section>
    <div class="cta-banner">
      <h2>Siap Mengelola Akuntabilitas Kinerja?</h2>
      <p>Akses akun Anda untuk mulai mengelola dokumen Perjanjian Kinerja, LKE, dan Laporan Kinerja Perangkat Daerah secara terpadu.</p>
      <a href="{{ Route::has('login') ? route('login') : '#' }}" class="btn-primary" style="border-color: var(--gold-bright); position: relative; z-index: 2;">
        Masuk ke Portal e-SAKIPKU
      </a>
    </div>
  </section>

  <div class="ikat-strip dim"></div>

  <footer>
    <div class="foot-mono">© {{ date('Y') }} Pemerintah Provinsi Nusa Tenggara Timur · Biro Organisasi Setda</div>
    <div class="foot-mono">e-SAKIPKU · Akuntabilitas Kinerja Terintegrasi</div>
  </footer>

</body>
</html>