<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Login — e-SAKIPKU NTT</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap"
      media="print" onload="this.media='all'">
<noscript>
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap">
</noscript>

<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}

:root{
  --gold:#c8922a;
  --gold-2:#e8b84b;
  --gold-3:#fdd87a;
  --navy:#07101f;
  --navy-2:#0d1b30;
  --font-serif:'Cormorant Garamond',Georgia,'Times New Roman',serif;
  --font-sans:'DM Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
}

html,body{
  font-family:var(--font-sans);
  min-height:100vh;
  background:var(--navy);
  -webkit-font-smoothing:antialiased;
}

body{display:flex;}

body::before{
  content:'';
  position:fixed;inset:0;
  background-image:radial-gradient(rgba(200,146,42,.07) 1px,transparent 1px);
  background-size:32px 32px;
  pointer-events:none;z-index:0;
}

.orb{position:fixed;border-radius:50%;filter:blur(80px);pointer-events:none;z-index:0;}
.orb-1{width:600px;height:600px;top:-200px;left:-100px;background:radial-gradient(circle,rgba(200,146,42,.08) 0%,transparent 70%);}
.orb-2{width:400px;height:400px;bottom:-100px;right:320px;background:radial-gradient(circle,rgba(16,60,120,.35) 0%,transparent 70%);}

/* ── PANEL KIRI ── */
.panel-left{flex:1.4;position:relative;overflow:hidden;}

.bg-img{
  position:absolute;inset:0;
  background:url('{{ asset("assets/gubernur.jpg") }}') center/cover no-repeat;
  animation:slowZoom 18s ease both;
  transform-origin:center bottom;
}
@keyframes slowZoom{from{transform:scale(1.1)}to{transform:scale(1)}}

.panel-left::before{
  content:'';position:absolute;inset:0;
  background:
    linear-gradient(to right,rgba(7,16,31,.1) 0%,rgba(7,16,31,.95) 100%),
    linear-gradient(to bottom,rgba(0,0,0,.2) 0%,transparent 30%,rgba(0,0,0,.7) 75%,rgba(7,16,31,.98) 100%);
  z-index:1;
}

.scan-line{
  position:absolute;left:0;right:0;height:1px;
  background:linear-gradient(to right,transparent,rgba(200,146,42,.3),transparent);
  z-index:3;animation:scanDown 8s ease-in-out infinite;opacity:0;
}
@keyframes scanDown{0%{top:0%;opacity:0;}10%{opacity:1;}90%{opacity:1;}100%{top:100%;opacity:0;}}

.badge-top{
  position:absolute;top:28px;left:32px;z-index:5;
  display:flex;align-items:center;gap:10px;
  background:rgba(255,255,255,.07);backdrop-filter:blur(20px);
  border:1px solid rgba(255,255,255,.12);border-radius:100px;
  padding:7px 20px 7px 7px;
  animation:fadeDown .9s cubic-bezier(.22,1,.36,1) both;
}
@keyframes fadeDown{from{opacity:0;transform:translateY(-12px)}to{opacity:1;transform:translateY(0)}}

.badge-logo{
  width:34px;height:34px;border-radius:50%;
  background:linear-gradient(135deg,var(--gold),#7a4a08);
  display:flex;align-items:center;justify-content:center;
  box-shadow:0 4px 12px rgba(200,146,42,.3);
}
.badge-logo img{width:20px;height:20px;object-fit:contain;filter:brightness(10);}
.badge-top-text{font-size:10px;font-weight:700;color:#fff;letter-spacing:3px;text-transform:uppercase;}
.badge-year{font-size:9px;color:rgba(255,255,255,.35);letter-spacing:1.5px;padding-left:10px;border-left:1px solid rgba(255,255,255,.15);}

.overlay-bottom{
  position:absolute;bottom:48px;left:44px;right:90px;z-index:5;
  animation:fadeUp 1s .15s cubic-bezier(.22,1,.36,1) both;
}
@keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}

.tag-label{
  display:inline-flex;align-items:center;gap:7px;
  background:rgba(200,146,42,.1);border:1px solid rgba(200,146,42,.3);
  border-radius:100px;padding:5px 16px;
  font-size:9px;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;
  color:var(--gold-2);margin-bottom:20px;
}
.tag-label::before{content:'';width:5px;height:5px;border-radius:50%;background:var(--gold-2);animation:blink 2.5s ease-in-out infinite;}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}

.overlay-title{
  font-family:var(--font-serif);font-size:46px;font-weight:300;
  color:#fff;line-height:1.12;margin-bottom:16px;letter-spacing:-.5px;
  text-shadow:0 2px 32px rgba(0,0,0,.5);
}
.overlay-title em{font-style:italic;font-weight:400;color:var(--gold-3);text-shadow:0 0 40px rgba(253,216,122,.25);}

.overlay-meta{display:flex;align-items:center;gap:16px;}
.overlay-sub{font-size:10px;color:rgba(255,255,255,.38);letter-spacing:2.5px;text-transform:uppercase;}
.overlay-divider{width:1px;height:12px;background:rgba(255,255,255,.2);}

.stats-row{display:flex;gap:28px;margin-top:24px;padding-top:22px;border-top:1px solid rgba(255,255,255,.07);}
.stat-num{font-family:var(--font-serif);font-size:22px;font-weight:500;color:#fff;line-height:1;margin-bottom:3px;}
.stat-num span{color:var(--gold-2);}
.stat-label{font-size:9px;color:rgba(255,255,255,.3);letter-spacing:1.5px;text-transform:uppercase;}

.deco-corner{position:absolute;bottom:0;left:44px;right:90px;height:2px;background:linear-gradient(to right,var(--gold),rgba(200,146,42,.1),transparent);z-index:5;}

/* ── PANEL KANAN ── */
.panel-right{
  width:430px;flex-shrink:0;
  display:flex;flex-direction:column;justify-content:center;
  padding:44px 50px;background:var(--navy);
  position:relative;overflow-y:auto;z-index:1;min-height:100vh;
}

.form-wrap{position:relative;z-index:1;animation:slideIn .7s cubic-bezier(.22,1,.36,1) both;}
@keyframes slideIn{from{opacity:0;transform:translateX(28px)}to{opacity:1;transform:translateX(0)}}

.form-eyebrow{display:flex;align-items:center;gap:10px;margin-bottom:16px;}
.eyebrow-line{width:22px;height:1.5px;background:linear-gradient(to right,var(--gold),var(--gold-2));}
.eyebrow-text{font-size:9.5px;font-weight:700;letter-spacing:3.5px;text-transform:uppercase;color:var(--gold);}

.form-title{font-family:var(--font-serif);font-size:48px;font-weight:300;color:#fff;line-height:.95;letter-spacing:-1px;margin-bottom:8px;}
.form-title span{font-style:italic;font-weight:400;color:var(--gold-2);}

.form-desc{font-size:12.5px;color:rgba(255,255,255,.3);margin-bottom:30px;line-height:1.6;font-weight:300;}

.msg-icon{flex-shrink:0;display:flex;align-items:center;}

.msg-error,.msg-success{
  display:flex;align-items:flex-start;gap:10px;
  border-radius:10px;padding:12px 14px;
  font-size:12.5px;margin-bottom:22px;line-height:1.45;
}
.msg-error{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-left:3px solid #ef4444;color:#fca5a5;animation:shake .4s ease;}
.msg-success{background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2);border-left:3px solid #10b981;color:#6ee7b7;align-items:center;}
@keyframes shake{0%,100%{transform:translateX(0)}20%,60%{transform:translateX(-5px)}40%,80%{transform:translateX(5px)}}

.field{margin-bottom:16px;}
.field-label{font-size:9.5px;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;color:rgba(255,255,255,.3);margin-bottom:8px;display:block;}
.field-input-wrap{position:relative;}

.field-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:rgba(255,255,255,.2);pointer-events:none;display:flex;align-items:center;justify-content:center;}
.field-icon svg{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round;}

.field input{
  width:100%;padding:13px 44px;
  background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);
  border-radius:10px;font-family:var(--font-sans);
  font-size:13.5px;font-weight:400;color:#fff;outline:none;
  transition:all .25s;letter-spacing:.2px;
}
.field input::placeholder{color:rgba(255,255,255,.18);font-weight:300;}
.field input:focus{border-color:rgba(200,146,42,.5);background:rgba(255,255,255,.06);box-shadow:0 0 0 4px rgba(200,146,42,.08);}

.toggle-pass{
  position:absolute;right:13px;top:50%;transform:translateY(-50%);
  background:none;border:none;color:rgba(255,255,255,.2);
  cursor:pointer;padding:4px;display:flex;align-items:center;transition:color .2s;
}
.toggle-pass:hover{color:rgba(255,255,255,.5);}
.toggle-pass svg{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round;}

.strength-bar{height:2px;background:rgba(255,255,255,.06);border-radius:2px;margin-top:7px;overflow:hidden;}
.strength-fill{height:100%;width:0;border-radius:2px;transition:width .4s ease,background .4s ease;}

.or-divider{display:flex;align-items:center;gap:14px;margin:20px 0;}
.or-divider::before,.or-divider::after{content:'';flex:1;height:1px;background:rgba(255,255,255,.06);}
.or-divider span{font-size:10px;color:rgba(255,255,255,.2);letter-spacing:1.5px;text-transform:uppercase;}

.btn-submit{
  width:100%;height:52px;padding:0 20px;margin-top:6px;
  background:linear-gradient(135deg,#c8922a 0%,#8a5510 50%,#c8922a 100%);
  background-size:200% 100%;border:none;border-radius:10px;color:#fff;
  font-family:var(--font-sans);font-size:11px;font-weight:700;
  letter-spacing:3px;text-transform:uppercase;cursor:pointer;transition:all .35s;
  display:flex;align-items:center;justify-content:space-between;
  box-shadow:0 8px 28px rgba(200,146,42,.2);position:relative;overflow:hidden;
}
.btn-submit::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,.1) 0%,transparent 60%);}
.btn-submit:hover{background-position:100% 0;box-shadow:0 12px 36px rgba(200,146,42,.35);transform:translateY(-2px);}
.btn-submit:active{transform:translateY(0);}
.btn-submit:disabled{opacity:.7;cursor:not-allowed;transform:none;}
.btn-submit.loading .btn-text{opacity:.5;}
.btn-submit.loading .btn-arrow{animation:spin .7s linear infinite;}
@keyframes spin{to{transform:rotate(360deg);}}

.btn-arrow{width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:transform .2s;}
.btn-arrow svg{width:14px;height:14px;fill:none;stroke:#fff;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}
.btn-submit:hover .btn-arrow{transform:translateX(3px);}

.daftar-wrap{text-align:center;font-size:12.5px;color:rgba(255,255,255,.28);}
.daftar-btn{background:none;border:none;color:var(--gold-2);font-size:12.5px;font-weight:600;cursor:pointer;margin-left:6px;font-family:var(--font-sans);transition:color .2s;letter-spacing:.3px;}
.daftar-btn:hover{color:var(--gold-3);}

.form-footer{margin-top:30px;padding-top:20px;border-top:1px solid rgba(255,255,255,.06);display:flex;align-items:center;justify-content:center;gap:8px;}
.form-footer-dot{width:2px;height:2px;border-radius:50%;background:rgba(255,255,255,.2);}
.form-footer span{font-size:9.5px;color:rgba(255,255,255,.16);letter-spacing:2px;text-transform:uppercase;}

/* ── MODAL DAFTAR ── */
.modal-bg{display:none;position:fixed;inset:0;background:rgba(4,9,18,.8);backdrop-filter:blur(8px);z-index:100;align-items:center;justify-content:center;padding:16px;}
.modal-bg.open{display:flex;}

.modal-box{
  background:var(--navy-2);border-radius:18px;
  width:480px;max-width:100%;max-height:90vh;overflow-y:auto;
  border:1px solid rgba(255,255,255,.08);
  box-shadow:0 40px 100px rgba(0,0,0,.8),0 0 0 1px rgba(200,146,42,.1);
  animation:mIn .3s cubic-bezier(.34,1.56,.64,1);
}
@keyframes mIn{from{opacity:0;transform:scale(.93) translateY(-12px)}to{opacity:1;transform:scale(1) translateY(0)}}

.m-hd{padding:22px 26px;border-bottom:1px solid rgba(255,255,255,.06);display:flex;align-items:center;justify-content:space-between;background:linear-gradient(to bottom,rgba(200,146,42,.04),transparent);}
.m-title-wrap{display:flex;align-items:center;gap:10px;}
.m-title-icon{width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,rgba(200,146,42,.2),rgba(200,146,42,.05));border:1px solid rgba(200,146,42,.25);display:flex;align-items:center;justify-content:center;}
.m-title-icon svg{width:14px;height:14px;fill:none;stroke:var(--gold-2);stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round;}
.m-title{font-family:var(--font-serif);font-size:18px;font-weight:400;color:#fff;letter-spacing:-.3px;}
.m-title-sub{font-size:10px;color:rgba(255,255,255,.28);letter-spacing:1px;margin-top:1px;}
.m-close{width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.45);cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;transition:all .2s;line-height:1;}
.m-close:hover{background:rgba(255,255,255,.12);color:#fff;}

.m-bd{padding:24px 26px;}
.m-field{margin-bottom:15px;}
.m-label{display:block;font-size:9.5px;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;color:rgba(255,255,255,.28);margin-bottom:7px;}
.m-input{width:100%;padding:11px 14px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:9px;font-family:var(--font-sans);font-size:13px;color:#fff;outline:none;transition:all .2s;}
.m-input:focus{border-color:rgba(200,146,42,.4);background:rgba(255,255,255,.06);box-shadow:0 0 0 3px rgba(200,146,42,.07);}
.m-input option{background:var(--navy-2);}
.m-input::placeholder{color:rgba(255,255,255,.18);}

/* Input error state */
.m-input.is-error{border-color:rgba(239,68,68,.5);background:rgba(239,68,68,.04);}
.m-input.is-error:focus{border-color:rgba(239,68,68,.6);box-shadow:0 0 0 3px rgba(239,68,68,.08);}

.m-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}

.m-info{display:flex;align-items:flex-start;gap:10px;background:rgba(200,146,42,.07);border:1px solid rgba(200,146,42,.18);border-radius:10px;padding:12px 14px;font-size:12px;color:rgba(255,255,255,.42);margin-top:4px;line-height:1.5;}
.m-info strong{color:var(--gold-2);}
.m-info-icon{font-size:14px;margin-top:1px;flex-shrink:0;}

.m-ft{padding:18px 26px;border-top:1px solid rgba(255,255,255,.06);display:flex;justify-content:flex-end;gap:10px;background:rgba(0,0,0,.12);}

/* Error box di dalam modal */
.msg-error-modal{
  display:flex;align-items:flex-start;gap:9px;
  background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);
  border-left:3px solid #ef4444;border-radius:9px;
  padding:10px 13px;font-size:12px;color:#fca5a5;
  margin-bottom:18px;animation:shake .4s ease;line-height:1.5;
}
.msg-error-modal svg{flex-shrink:0;margin-top:1px;}

/* Field hint inline */
.field-hint{font-size:10.5px;margin-top:5px;padding-left:2px;}
.field-hint.error{color:#f87171;}
.field-hint.success{color:#6ee7b7;}

.btn-batal{padding:10px 22px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:9px;color:rgba(255,255,255,.5);font-size:13px;font-weight:500;cursor:pointer;font-family:var(--font-sans);transition:all .2s;}
.btn-batal:hover{background:rgba(255,255,255,.09);color:#fff;}

.btn-daftar-submit{padding:10px 24px;background:linear-gradient(135deg,var(--gold),#8a5510);border:none;border-radius:9px;color:#fff;font-size:13px;font-weight:700;cursor:pointer;font-family:var(--font-sans);box-shadow:0 6px 20px rgba(200,146,42,.25);transition:all .25s;letter-spacing:.5px;}
.btn-daftar-submit:hover{transform:translateY(-1px);box-shadow:0 10px 28px rgba(200,146,42,.4);}

/* ── RESPONSIVE ── */
@media (max-width:768px){
  body{flex-direction:column;}
  .panel-left{display:none;}
  .panel-right{width:100%;min-height:100vh;padding:60px 28px 40px;}
  .form-title{font-size:38px;}
  .m-row{grid-template-columns:1fr;gap:0;}
}
@media (max-width:480px){
  .panel-right{padding:50px 20px 40px;}
  .form-title{font-size:32px;}
  .modal-box{border-radius:14px;}
  .m-bd{padding:20px 18px;}
  .m-ft{padding:14px 18px;}
}
</style>
</head>
<body>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

{{-- ── PANEL KIRI ── --}}
<div class="panel-left">
  <div class="bg-img"></div>
  <div class="scan-line"></div>

  <div class="badge-top">
    <div class="badge-logo">
      <img src="{{ asset('assets/logo_ntt.png') }}" onerror="this.style.display='none'" alt="Logo NTT">
    </div>
    <span class="badge-top-text">e-SAKIPKU</span>
    <span class="badge-year">NTT</span>
  </div>

  <div class="overlay-bottom">
    <div class="tag-label">Provinsi Nusa Tenggara Timur</div>
    <h2 class="overlay-title">
      Sistem Akuntabilitas<br>
      Kinerja <em>Instansi</em><br>
      Pemerintah NTT
    </h2>
    <div class="overlay-meta">
      <span class="overlay-sub">Biro Organisasi</span>
      <div class="overlay-divider"></div>
      <span class="overlay-sub">Setda Provinsi NTT</span>
    </div>
    <div class="stats-row">
      <div class="stat-item">
        <div class="stat-num">42</div>
        <div class="stat-label">OPD Terdaftar</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">{{ date('Y') }}</div>
        <div class="stat-label">Tahun Aktif</div>
      </div>
    </div>
  </div>
  <div class="deco-corner"></div>
</div>

{{-- ── PANEL KANAN ── --}}
<div class="panel-right">
  <div class="form-wrap">

    <div class="form-eyebrow">
      <div class="eyebrow-line"></div>
      <span class="eyebrow-text">Portal Masuk</span>
    </div>

    <h1 class="form-title">Masuk ke<br><span>Sistem</span></h1>
    <p class="form-desc">Masukkan kredensial Anda untuk mengakses e-SAKIPKU Provinsi NTT.</p>

    @if(session('daftar_sukses'))
      <div class="msg-success">
        <span class="msg-icon">
          <svg width="15" height="15" viewBox="0 0 16 16" fill="none">
            <circle cx="8" cy="8" r="7" stroke="#10b981" stroke-width="1.5"/>
            <path d="M5 8l2.5 2.5L11 5.5" stroke="#10b981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </span>
        <span>{{ session('daftar_sukses') }}</span>
      </div>
    @endif

    @if($errors->has('username') || $errors->has('password'))
      <div class="msg-error">
        <span class="msg-icon">
          <svg width="15" height="15" viewBox="0 0 16 16" fill="none">
            <circle cx="8" cy="8" r="7" stroke="#ef4444" stroke-width="1.5"/>
            <path d="M8 5v4M8 11v.5" stroke="#ef4444" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </span>
        <span>{{ $errors->first('username') ?: $errors->first('password') }}</span>
      </div>
    @endif

    @if(session('error'))
      <div class="msg-error">
        <span class="msg-icon">
          <svg width="15" height="15" viewBox="0 0 16 16" fill="none">
            <circle cx="8" cy="8" r="7" stroke="#ef4444" stroke-width="1.5"/>
            <path d="M8 5v4M8 11v.5" stroke="#ef4444" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </span>
        <span>{{ session('error') }}</span>
      </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" id="loginForm">
      @csrf

      <div class="field">
        <label class="field-label" for="inp_username">Username/NIP</label>
        <div class="field-input-wrap">
          <div class="field-icon">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
          </div>
          <input type="text" name="username" id="inp_username"
                 placeholder="Masukkan username/nip..."
                 value="{{ old('username') }}"
                 required autocomplete="username">
        </div>
      </div>

      <div class="field">
        <label class="field-label" for="inp_password">Password</label>
        <div class="field-input-wrap">
          <div class="field-icon">
            <svg viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
          </div>
          <input type="password" name="password" id="inp_password"
                 placeholder="Masukkan password..."
                 required autocomplete="current-password">
          <button type="button" class="toggle-pass" id="togglePass"
                  title="Tampilkan password" aria-label="Tampilkan password">
            <svg viewBox="0 0 24 24">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-submit" id="submitBtn">
        <span class="btn-text">Masuk ke Sistem</span>
        <span class="btn-arrow">
          <svg viewBox="0 0 16 16"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
        </span>
      </button>
    </form>

    <div class="or-divider"><span>atau</span></div>

    <div class="daftar-wrap">
      Belum punya akun?
      <button class="daftar-btn" onclick="showDaftar()">Daftar Sekarang →</button>
    </div>

    <div class="form-footer">
      <span>Biro Organisasi</span>
      <div class="form-footer-dot"></div>
      <span>Provinsi NTT</span>
      <div class="form-footer-dot"></div>
      <span>{{ date('Y') }}</span>
    </div>

  </div>
</div>

{{-- ── MODAL DAFTAR ── --}}
<div class="modal-bg" id="modalDaftar" role="dialog" aria-modal="true" aria-label="Form Daftar Akun">
  <div class="modal-box">
    <div class="m-hd">
      <div class="m-title-wrap">
        <div class="m-title-icon">
          <svg viewBox="0 0 24 24">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
        <div>
          <div class="m-title">Daftar Akun Operator</div>
          <div class="m-title-sub">Pendaftaran akun baru</div>
        </div>
      </div>
      <button class="m-close" onclick="closeDaftar()" aria-label="Tutup">✕</button>
    </div>

    <form method="POST" action="{{ route('daftar') }}" id="daftarForm">
      @csrf
      <div class="m-bd">

        {{-- Error dari server (semua error form daftar) --}}
        @php
          $daftarErrorKeys = ['daftar_nama','daftar_username','daftar_password','daftar_konfirmasi','daftar_daerah'];
          $daftarErrors = collect($daftarErrorKeys)->map(fn($k) => $errors->first($k))->filter();
        @endphp

        @if($daftarErrors->isNotEmpty())
          <div class="msg-error-modal">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
              <circle cx="8" cy="8" r="7" stroke="#ef4444" stroke-width="1.5"/>
              <path d="M8 5v4M8 11v.5" stroke="#ef4444" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <div>
              @foreach($daftarErrors as $errMsg)
                <div>{{ $errMsg }}</div>
              @endforeach
            </div>
          </div>
        @endif

        <div class="m-field">
          <label class="m-label" for="daftar_nama">Nama Lengkap</label>
          <input class="m-input {{ $errors->has('daftar_nama') ? 'is-error' : '' }}"
                 type="text" name="nama" id="daftar_nama"
                 placeholder="Nama lengkap Anda..."
                 required value="{{ old('nama') }}">
        </div>

        <div class="m-field">
          <label class="m-label" for="daftar_daerah">Perangkat Daerah / OPD</label>
          <select class="m-input {{ $errors->has('daftar_daerah') ? 'is-error' : '' }}"
                  name="daerah_id" id="daftar_daerah" required>
            <option value="">-- Pilih OPD --</option>
            @foreach($listOpd as $opd)
              <option value="{{ $opd->id }}" {{ old('daerah_id') == $opd->id ? 'selected' : '' }}>
                {{ $opd->nama }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="m-row">
          <div class="m-field">
            <label class="m-label" for="daftar_username">Username/Nip</label>
            <input class="m-input {{ $errors->has('daftar_username') ? 'is-error' : '' }}"
                   type="text" name="username" id="daftar_username"
                  
                   required value="{{ old('username') }}"
                   autocomplete="off">
          </div>
          <div class="m-field">
            <label class="m-label" for="daftar_password">Password</label>
            <input class="m-input {{ $errors->has('daftar_password') ? 'is-error' : '' }}"
                   type="password" name="password" id="daftar_password"
                   placeholder="Min. 6 karakter..."
                   required autocomplete="new-password">
          </div>
        </div>

        {{-- Strength bar password daftar --}}
        <div class="strength-bar" style="margin-bottom:8px;">
          <div class="strength-fill" id="strengthFillDaftar"></div>
        </div>
        <div id="strengthHintDaftar" class="field-hint" style="margin-bottom:12px;"></div>

        <div class="m-field">
          <label class="m-label" for="daftar_konfirmasi">Konfirmasi Password</label>
          <input class="m-input {{ $errors->has('daftar_konfirmasi') ? 'is-error' : '' }}"
                 type="password" name="konfirmasi" id="daftar_konfirmasi"
                 placeholder="Ulangi password..."
                 required autocomplete="new-password">
          <div id="konfirmasiHint" class="field-hint"></div>
        </div>

        <div class="m-info">
          <span class="m-info-icon">⚠</span>
          <span>
            Akun Anda akan aktif setelah mendapat persetujuan dari
            <strong>Admin Biro Organisasi</strong>.
            Proses verifikasi biasanya membutuhkan 1–2 hari kerja.
          </span>
        </div>

      </div>
      <div class="m-ft">
        <button type="button" class="btn-batal" onclick="closeDaftar()">Batal</button>
        <button type="submit" class="btn-daftar-submit" id="btnDaftar">Daftar Sekarang</button>
      </div>
    </form>
  </div>
</div>

<script>
// ── MODAL ─────────────────────────────────────────────────

function showDaftar() {
    document.getElementById('modalDaftar').classList.add('open');
    setTimeout(() => document.getElementById('daftar_nama').focus(), 100);
}
function closeDaftar() {
    document.getElementById('modalDaftar').classList.remove('open');
}

document.getElementById('modalDaftar').addEventListener('click', function(e) {
    if (e.target === this) closeDaftar();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDaftar();
});

// Auto-buka modal jika ada error dari server pada form daftar
@if($errors->hasAny(['daftar_nama','daftar_username','daftar_password','daftar_konfirmasi','daftar_daerah']))
    document.addEventListener('DOMContentLoaded', function() { showDaftar(); });
@endif

// ── TOGGLE PASSWORD LOGIN ─────────────────────────────────

var eyeVisible = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
var eyeHidden  = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
var passVisible = false;

document.getElementById('togglePass').addEventListener('click', function() {
    var inp = document.getElementById('inp_password');
    passVisible = !passVisible;
    inp.type         = passVisible ? 'text' : 'password';
    this.innerHTML   = passVisible ? eyeHidden : eyeVisible;
    this.style.color = passVisible ? 'rgba(200,146,42,.7)' : 'rgba(255,255,255,.2)';
    this.title       = passVisible ? 'Sembunyikan password' : 'Tampilkan password';
    this.setAttribute('aria-label', this.title);
});

// ── VALIDASI REAL-TIME FORM DAFTAR ───────────────────────

var inpPasswordDaftar    = document.getElementById('daftar_password');
var inpKonfirmasiDaftar  = document.getElementById('daftar_konfirmasi');
var strengthFillDaftar   = document.getElementById('strengthFillDaftar');
var strengthHintDaftar   = document.getElementById('strengthHintDaftar');
var konfirmasiHintDaftar = document.getElementById('konfirmasiHint');

// Strength bar + validasi panjang password
if (inpPasswordDaftar) {
    inpPasswordDaftar.addEventListener('input', function() {
        var v = this.value;
        var s = 0;
        var hint = '';

        if (v.length === 0) {
            hint = '';
        } else if (v.length < 6) {
            s = 15;
            hint = '⚠ Password minimal 6 karakter';
            strengthHintDaftar.className = 'field-hint error';
        } else if (v.length < 8 || !/[A-Z]/.test(v)) {
            s = 40;
            hint = '● Password lemah — coba tambah huruf besar';
            strengthHintDaftar.className = 'field-hint error';
        } else if (v.length < 10 || !/[^A-Za-z0-9]/.test(v)) {
            s = 65;
            hint = '◑ Password cukup kuat';
            strengthHintDaftar.className = 'field-hint';
            strengthHintDaftar.style.color = '#f59e0b';
        } else {
            s = 100;
            hint = '✓ Password kuat';
            strengthHintDaftar.className = 'field-hint success';
        }

        strengthFillDaftar.style.width = s + '%';
        strengthFillDaftar.style.background = s <= 15 ? '#ef4444' : s <= 40 ? '#ef4444' : s <= 65 ? '#f59e0b' : '#10b981';
        strengthHintDaftar.textContent = hint;

        // Re-validasi konfirmasi jika sudah diisi
        if (inpKonfirmasiDaftar.value.length > 0) {
            validateKonfirmasiDaftar();
        }
    });
}

// Validasi konfirmasi password real-time
function validateKonfirmasiDaftar() {
    var pass   = inpPasswordDaftar.value;
    var konfirm = inpKonfirmasiDaftar.value;

    if (konfirm.length === 0) {
        konfirmasiHintDaftar.textContent = '';
        inpKonfirmasiDaftar.classList.remove('is-error');
        return true;
    }

    if (pass !== konfirm) {
        konfirmasiHintDaftar.textContent  = '✗ Password tidak sama';
        konfirmasiHintDaftar.className    = 'field-hint error';
        inpKonfirmasiDaftar.classList.add('is-error');
        return false;
    } else {
        konfirmasiHintDaftar.textContent  = '✓ Password sama';
        konfirmasiHintDaftar.className    = 'field-hint success';
        inpKonfirmasiDaftar.classList.remove('is-error');
        return true;
    }
}

if (inpKonfirmasiDaftar) {
    inpKonfirmasiDaftar.addEventListener('input', validateKonfirmasiDaftar);
}

// Blokir submit jika konfirmasi tidak cocok
document.getElementById('daftarForm').addEventListener('submit', function(e) {
    var pass    = inpPasswordDaftar.value;
    var konfirm = inpKonfirmasiDaftar.value;

    if (pass.length < 6) {
        e.preventDefault();
        inpPasswordDaftar.focus();
        strengthHintDaftar.textContent  = '⚠ Password minimal 6 karakter';
        strengthHintDaftar.className    = 'field-hint error';
        inpPasswordDaftar.classList.add('is-error');
        return;
    }

    if (pass !== konfirm) {
        e.preventDefault();
        inpKonfirmasiDaftar.focus();
        konfirmasiHintDaftar.textContent = '✗ Konfirmasi password tidak sama';
        konfirmasiHintDaftar.className   = 'field-hint error';
        inpKonfirmasiDaftar.classList.add('is-error');
        return;
    }
});

// ── LOADING STATE LOGIN ───────────────────────────────────

document.getElementById('loginForm').addEventListener('submit', function() {
    var btn = document.getElementById('submitBtn');
    btn.classList.add('loading');
    btn.querySelector('.btn-text').textContent = 'Memproses...';
    btn.disabled = true;
    // Tidak perlu setTimeout, jika ada error validasi server, halaman akan reload otomatis
});
</script>
</body>
</html>