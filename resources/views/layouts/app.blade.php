
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>e-SAKIPKU — @yield('title', 'Sistem Akuntabilitas Kinerja')</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ══════════════════════════════════════════
   VARIABLES - DARK MODE (DEFAULT)
══════════════════════════════════════════ */
:root {
  /* ── Dark Mode (default) ── */
  --bg-body: #070d1a;
  --bg-sidebar: #0a1628;
  --bg-card: linear-gradient(135deg, #1a1f2e, #141824);
  --bg-card-solid: #1a1f2e;
  --bg-input: rgba(255,255,255,.07);
  --bg-hover: rgba(255,255,255,.04);
  --bg-table-header: rgba(255,255,255,.04);
  --bg-table-stripe: rgba(255,255,255,.025);
  --bg-modal: #1a1f2e;
  --bg-topbar: rgba(10,22,40,.7);
  --bg-filter: rgba(30,30,46,.8);
  
  --text-primary: #ffffff;
  --text-secondary: rgba(255,255,255,.85);
  --text-muted: rgba(255,255,255,.55);
  --text-light: rgba(255,255,255,.35);
  --text-gray: rgba(255,255,255,.5);
  
  --border-color: rgba(255,255,255,.07);
  --border-light: rgba(255,255,255,.06);
  --border-input: rgba(255,255,255,.12);
  
  --shadow-card: 0 4px 20px rgba(0,0,0,0.2);
  --shadow-card-hover: 0 8px 32px rgba(0,0,0,0.35);
  --shadow-modal: 0 20px 50px rgba(0,0,0,0.5);
  
  --scrollbar-track: rgba(255,255,255,.05);
  --scrollbar-thumb: rgba(255,255,255,.15);
  
  --navy: #0a1628;
  --navy-2: #0f1f3d;
  --navy-3: #162442;
  --blue: #2563eb;
  --blue-2: #1d4ed8;
  --gold: #d4982e;
  --gold-2: #f0b84a;
  --gold-3: #fcd34d;
  --green: #059669;
  --red: #dc2626;
  
  --sidebar-w: 260px;
  --topbar-h: 58px;
  --radius: 12px;
  --radius-sm: 8px;
  --radius-lg: 16px;
}

/* ══════════════════════════════════════════
   LIGHT MODE
══════════════════════════════════════════ */
[data-theme="light"] {
  --bg-body: #f1f5f9;
  --bg-sidebar: #ffffff;
  --bg-card: linear-gradient(135deg, #ffffff, #f8fafc);
  --bg-card-solid: #ffffff;
  --bg-input: rgba(0,0,0,.04);
  --bg-hover: rgba(0,0,0,.03);
  --bg-table-header: #f1f5f9;
  --bg-table-stripe: rgba(0,0,0,.02);
  --bg-modal: #ffffff;
  --bg-topbar: rgba(255,255,255,.95);
  --bg-filter: rgba(255,255,255,.95);
  
  --text-primary: #0f172a;
  --text-secondary: #1e293b;
  --text-muted: #475569;
  --text-light: #94a3b8;
  --text-gray: #64748b;
  
  --border-color: #e2e8f0;
  --border-light: #e2e8f0;
  --border-input: #cbd5e1;
  
  --shadow-card: 0 1px 3px rgba(0,0,0,0.06);
  --shadow-card-hover: 0 4px 12px rgba(0,0,0,0.1);
  --shadow-modal: 0 20px 50px rgba(0,0,0,0.2);
  
  --scrollbar-track: #e2e8f0;
  --scrollbar-thumb: #94a3b8;
  
  --navy: #1e293b;
  --navy-2: #334155;
  --navy-3: #475569;
}

/* ══════════════════════════════════════════
   JARING PENGAMAN MODE TERANG
   Banyak halaman (Pengukuran Periodik, Juknis, dll)
   & komponen topbar (notifikasi) masih tulis warna
   dark-glass langsung (hex/rgba putih), bukan lewat
   variable tema — jadi nggak otomatis berubah pas
   Mode Terang aktif. Blok ini menutup celah itu.
══════════════════════════════════════════ */

/* ── 1. Area konten halaman (.page-content) ── */
[data-theme="light"] .page-content {
  color: #1e293b;
}

/* teks putih/abu-terang yang ditulis lewat inline style */
[data-theme="light"] .page-content [style*="color:#fff"],
[data-theme="light"] .page-content [style*="color: #fff"],
[data-theme="light"] .page-content [style*="color:#ffffff"],
[data-theme="light"] .page-content [style*="color: #ffffff"],
[data-theme="light"] .page-content [style*="color:rgba(255,255,255"],
[data-theme="light"] .page-content [style*="color: rgba(255,255,255"] {
  color: #1e293b !important;
}

/* komponen dark-glass generik yang berulang di banyak halaman */
[data-theme="light"] .page-content .xls-td,
[data-theme="light"] .page-content .xls-th,
[data-theme="light"] .page-content .xls-input,
[data-theme="light"] .page-content .row-num,
[data-theme="light"] .page-content .sheet-title-bar,
[data-theme="light"] .page-content .sasaran-cell-text,
[data-theme="light"] .page-content .xls-statusbar,
[data-theme="light"] .page-content .form-lbl,
[data-theme="light"] .page-content .form-inp,
[data-theme="light"] .page-content .form-ta,
[data-theme="light"] .page-content .form-control-friendly,
[data-theme="light"] .page-content .card-header-glass h3,
[data-theme="light"] .page-content .table-glass td,
[data-theme="light"] .page-content .table-glass th,
[data-theme="light"] .page-content .modal-title-friendly,
[data-theme="light"] .page-content .modal-head-title,
[data-theme="light"] .page-content .indikator-block-title,
[data-theme="light"] .page-content .ribbon-step,
[data-theme="light"] .page-content .small.font-weight-bold,
[data-theme="light"] .page-content .ctx-item {
  color: #1e293b !important;
}

[data-theme="light"] .page-content .text-muted,
[data-theme="light"] .page-content .xls-toolbar-label,
[data-theme="light"] .page-content .tw-card-head,
[data-theme="light"] .page-content .tw-section-label,
[data-theme="light"] .page-content .ctx-label {
  color: #64748b !important;
}

/* background dark-glass rgba(255,255,255,.0x) nyaris tak
   kontras di atas body terang — kasih background solid tipis */
[data-theme="light"] .page-content .sheet-wrap,
[data-theme="light"] .page-content .xls-toolbar,
[data-theme="light"] .page-content .form-panel,
[data-theme="light"] .page-content .indikator-block,
[data-theme="light"] .page-content .tw-block,
[data-theme="light"] .page-content .tw-card,
[data-theme="light"] .page-content .card-glass,
[data-theme="light"] .page-content .modal-content-friendly,
[data-theme="light"] .page-content .modal-box,
[data-theme="light"] .page-content .ctx-menu {
  background: #ffffff !important;
  border-color: #e2e8f0 !important;
}

[data-theme="light"] .page-content input,
[data-theme="light"] .page-content textarea,
[data-theme="light"] .page-content select {
  color: #1e293b !important;
}

/* ── 2. Notifikasi dropdown — DI LUAR .page-content,
       diisi via JS pakai warna hex hardcoded, jadi
       perlu safety net sendiri ── */
[data-theme="light"] .notif-dropdown {
  color: #1e293b;
}
[data-theme="light"] .notif-dropdown [style*="color:#fff"],
[data-theme="light"] .notif-dropdown [style*="color: #fff"],
[data-theme="light"] .notif-dropdown [style*="color:#e4e4e7"] {
  color: #1e293b !important;
}
[data-theme="light"] .notif-dropdown [style*="color:#a1a1aa"],
[data-theme="light"] .notif-dropdown [style*="color:#52525b"] {
  color: #64748b !important;
}
[data-theme="light"] .notif-list > div[style*="color:#52525b"] {
  color: #94a3b8 !important;
}

/* ══════════════════════════════════════════
   THEME TOGGLE BUTTON
══════════════════════════════════════════ */
.theme-toggle-btn {
  width: 100%;
  padding: 10px 12px;
  background: rgba(255,255,255,.06);
  border: 1px solid rgba(255,255,255,.1);
  border-radius: 10px;
  color: var(--text-muted);
  cursor: pointer;
  font-family: inherit;
  font-size: 13px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: all .2s;
}

.theme-toggle-btn:hover {
  background: rgba(255,255,255,.12);
}

[data-theme="light"] .theme-toggle-btn {
  background: rgba(0,0,0,.04);
  border-color: rgba(0,0,0,.08);
}

[data-theme="light"] .theme-toggle-btn:hover {
  background: rgba(0,0,0,.08);
}

/* ══════════════════════════════════════════
   RESET & GLOBAL
══════════════════════════════════════════ */
*,*::before,*::after { 
  margin:0; 
  padding:0; 
  box-sizing:border-box; 
}

html { 
  scroll-behavior:smooth; 
}

body {
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  background: var(--bg-body);
  color: var(--text-primary);
  transition: background 0.3s ease, color 0.3s ease;
  min-height: 100vh;
  overflow-x: hidden;
}

/* ══════════════════════════════════════════
   LAYOUT WRAPPER
══════════════════════════════════════════ */
.app-layout {
  display: flex;
  min-height: 100vh;
}

/* ══════════════════════════════════════════
   SIDEBAR
══════════════════════════════════════════ */
.sidebar {
  width: var(--sidebar-w);
  background: var(--bg-sidebar);
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
  position: fixed;
  top: 0; left: 0;
  height: 100vh;
  overflow-y: auto;
  overflow-x: hidden;
  z-index: 200;
  border-right: 1px solid var(--border-color);
  transition: background 0.3s ease, border-color 0.3s ease;
}

.sidebar::-webkit-scrollbar { 
  width: 3px; 
}

.sidebar::-webkit-scrollbar-thumb { 
  background: rgba(255,255,255,.1); 
  border-radius: 99px; 
}

/* ── Brand ── */
.sidebar-brand {
  padding: 18px 16px 14px;
  display: flex;
  align-items: center;
  gap: 12px;
  border-bottom: 1px solid var(--border-color);
  flex-shrink: 0;
}

.sidebar-brand img {
  width: 38px; height: 38px;
  object-fit: contain;
  filter: drop-shadow(0 2px 8px rgba(0,0,0,.4));
  flex-shrink: 0;
}

.brand-text h2 {
  font-family: 'Outfit', sans-serif;
  font-size: 16px;
  font-weight: 800;
  color: var(--text-primary);
  letter-spacing: .2px;
  line-height: 1.2;
}

.brand-text span {
  font-size: 9px;
  color: var(--text-light);
  letter-spacing: 1.5px;
  text-transform: uppercase;
}

/* ── Tombol Close Sidebar (hanya mobile) ── */
.btn-close-sidebar {
  display: none;
  margin-left: auto;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.12);
  color: var(--text-muted);
  cursor: pointer;
  font-size: 16px;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all .2s;
}

.btn-close-sidebar:hover {
  background: rgba(255,255,255,.15);
  color: var(--text-primary);
}

/* ── Nav label ── */
.sidebar-label {
  padding: 14px 18px 4px;
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 1.8px;
  text-transform: uppercase;
  color: var(--text-muted);
  user-select: none;
}

/* ── Nav items ── */
.nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 10px 9px 14px;
  font-size: 13px;
  font-weight: 500;
  color: var(--text-muted);
  cursor: pointer;
  transition: all .15s;
  text-decoration: none;
  border-left: 2px solid transparent;
  margin: 1px 10px;
  border-radius: 9px;
  line-height: 1.4;
}

.nav-item:hover {
  background: var(--bg-hover);
  color: var(--text-primary);
  text-decoration: none;
}

.nav-item.active {
  background: linear-gradient(135deg, rgba(212,152,46,.16), rgba(37,99,235,.1));
  color: var(--text-primary);
  border-left-color: var(--gold);
  box-shadow: 0 2px 10px rgba(0,0,0,.2);
}

.nav-icon {
  font-size: 15px;
  width: 28px; height: 28px;
  display: flex; align-items: center; justify-content: center;
  border-radius: 7px;
  background: rgba(255,255,255,.06);
  flex-shrink: 0;
  transition: all .15s;
}

.nav-item.active .nav-icon { 
  background: rgba(212,152,46,.18); 
}

.nav-item:hover .nav-icon { 
  background: rgba(255,255,255,.1); 
}

.nav-label { 
  flex: 1; 
  min-width: 0; 
}

.nav-badge {
  margin-left: auto;
  background: var(--gold);
  color: #fff;
  font-size: 9px;
  font-weight: 800;
  padding: 2px 7px;
  border-radius: 20px;
  box-shadow: 0 2px 6px rgba(212,152,46,.4);
  flex-shrink: 0;
}

.nav-arrow {
  font-size: 10px;
  transition: transform .2s;
  color: rgba(255,255,255,.25);
  flex-shrink: 0;
}

/* ── Nav group ── */
.nav-group-body { 
  display: none; 
}

.nav-group-body.open { 
  display: block; 
}

.nav-sub {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 7px 14px 7px 50px;
  font-size: 12px;
  font-weight: 400;
  color: var(--text-light);
  cursor: pointer;
  text-decoration: none;
  transition: all .15s;
  margin: 1px 10px;
  border-radius: 7px;
  position: relative;
  line-height: 1.4;
  border-left: 2px solid transparent;
}

.nav-sub::before {
  content: '';
  position: absolute;
  left: 26px; top: 50%;
  width: 5px; height: 5px;
  border-radius: 50%;
  background: rgba(255,255,255,.15);
  transform: translateY(-50%);
  transition: all .15s;
}

.nav-sub:hover {
  color: var(--text-primary);
  background: var(--bg-hover);
  text-decoration: none;
}

.nav-sub:hover::before { 
  background: var(--gold-2); 
}

.nav-sub.active {
  color: var(--gold-2);
  font-weight: 600;
  background: rgba(212,152,46,.06);
}

.nav-sub.active::before { 
  background: var(--gold-2); 
}

/* ── Divider ── */
.nav-divider {
  height: 1px;
  background: var(--border-color);
  margin: 8px 14px;
}

/* ── Footer ── */
.sidebar-footer {
  margin-top: auto;
  padding: 12px;
  border-top: 1px solid var(--border-color);
  flex-shrink: 0;
}

.user-card {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  background: var(--bg-hover);
  border: 1px solid var(--border-color);
  border-radius: 10px;
  margin-bottom: 8px;
}

.user-avatar {
  width: 34px; height: 34px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold-2));
  display: flex; align-items: center; justify-content: center;
  font-size: 13px; font-weight: 800;
  color: #fff; flex-shrink: 0;
  box-shadow: 0 2px 10px rgba(212,152,46,.35);
}

.user-info { 
  min-width: 0; 
}

.user-name {
  font-size: 12px; 
  font-weight: 700; 
  color: var(--text-primary);
  white-space: nowrap; 
  overflow: hidden; 
  text-overflow: ellipsis;
}

.user-role { 
  font-size: 10px; 
  color: var(--text-muted);
  margin-top: 2px; 
}

.klaster-badge {
  display: inline-flex; 
  align-items: center; 
  gap: 4px;
  padding: 2px 8px;
  border-radius: 20px;
  font-size: 9px; 
  font-weight: 700;
  margin-top: 3px;
}

.klaster-utama { 
  background: rgba(99,102,241,.2); 
  color: #a5b4fc; 
  border: 1px solid rgba(99,102,241,.3); 
}

.klaster-pendukung { 
  background: rgba(16,185,129,.2); 
  color: #6ee7b7; 
  border: 1px solid rgba(16,185,129,.3); 
}

.klaster-tambahan { 
  background: rgba(245,158,11,.2); 
  color: #fcd34d; 
  border: 1px solid rgba(245,158,11,.3); 
}

.btn-logout {
  width: 100%; 
  padding: 8px 12px;
  background: rgba(220,38,38,.1);
  border: 1px solid rgba(220,38,38,.2);
  border-radius: 9px;
  color: #fca5a5;
  font-size: 12px; 
  font-weight: 600;
  cursor: pointer; 
  transition: all .15s;
  font-family: inherit;
  display: flex; 
  align-items: center; 
  justify-content: center; 
  gap: 6px;
}

.btn-logout:hover { 
  background: rgba(220,38,38,.2); 
  border-color: rgba(220,38,38,.35); 
}

/* ══════════════════════════════════════════
   MAIN CONTENT
══════════════════════════════════════════ */
.main-content {
  margin-left: var(--sidebar-w);
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
}

/* ══════════════════════════════════════════
   TOPBAR
══════════════════════════════════════════ */
.topbar {
  height: var(--topbar-h);
  background: var(--bg-topbar);
  backdrop-filter: blur(16px);
  border-bottom: 1px solid var(--border-color);
  transition: background 0.3s ease, border-color 0.3s ease;
  display: flex;
  align-items: center;
  padding: 0 24px;
  gap: 10px;
  position: sticky;
  top: 0; 
  z-index: 50;
  flex-shrink: 0;
}

.btn-hamburger {
  display: none;
  width: 36px; height: 36px;
  border-radius: 9px;
  background: var(--bg-input);
  border: 1px solid var(--border-input);
  color: var(--text-muted);
  cursor: pointer;
  font-size: 16px;
  align-items: center; 
  justify-content: center;
  flex-shrink: 0;
  transition: all .2s;
}

.btn-hamburger:hover { 
  background: rgba(255,255,255,.12); 
  color: #fff; 
}

.topbar-title {
  font-family: 'Outfit', sans-serif;
  font-size: 16px; 
  font-weight: 700;
  color: var(--text-primary);
  letter-spacing: -.2px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.topbar-sub {
  font-size: 11px;
  color: var(--text-muted);
  padding: 2px 10px;
  background: var(--bg-input);
  border-radius: 20px;
  border: 1px solid var(--border-input);
  white-space: nowrap;
  flex-shrink: 0;
}

.topbar-actions-wrapper {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-left: 12px;
}

.topbar-icon-btn {
  width: 36px; height: 36px;
  border-radius: 50%;
  background: rgba(255,255,255,.07);
  border: 1px solid rgba(255,255,255,.1);
  cursor: pointer;
  display: flex; 
  align-items: center; 
  justify-content: center;
  font-size: 17px;
  transition: all .2s;
  position: relative;
  text-decoration: none;
  flex-shrink: 0;
}

.topbar-icon-btn:hover {
  background: rgba(255,255,255,.13);
  border-color: rgba(255,255,255,.2);
}

.icon-badge {
  display: none;
  position: absolute;
  top: -2px; right: -2px;
  min-width: 17px; height: 17px;
  border-radius: 99px;
  font-size: 9px; 
  font-weight: 800;
  align-items: center; 
  justify-content: center;
  padding: 0 4px;
  border: 2px solid #070d1a;
}

.notif-badge-bg { 
  background: #ef4444; 
  color: #fff; 
}

.chat-badge-bg { 
  background: #10b981; 
  color: #fff; 
}

/* ── Notif dropdown ── */
.notif-dropdown {
  display: none;
  position: absolute;
  top: calc(100% + 8px); 
  right: 0;
  width: 340px;
  max-height: 480px;
  background: var(--bg-modal);
  border: 1px solid var(--border-color);
  border-radius: 16px;
  box-shadow: var(--shadow-modal);
  overflow: hidden;
  z-index: 999;
}

.notif-header {
  padding: 14px 16px;
  border-bottom: 1px solid var(--border-color);
  display: flex; 
  align-items: center; 
  justify-content: space-between;
}

.notif-list { 
  max-height: 380px; 
  overflow-y: auto; 
}

.notif-list::-webkit-scrollbar { 
  width: 3px; 
}

.notif-list::-webkit-scrollbar-thumb { 
  background: rgba(255,255,255,.1); 
  border-radius: 99px; 
}

/* ══════════════════════════════════════════
   PAGE CONTENT
══════════════════════════════════════════ */
.page-content {
  padding: 24px;
  flex: 1;
}

/* ══════════════════════════════════════════
   GLOBAL COMPONENTS
══════════════════════════════════════════ */
/* Cards */
.card { 
  background: var(--bg-card); 
  border-radius: var(--radius); 
  border: 1px solid var(--border-color);
  transition: background 0.3s ease, border-color 0.3s ease;
  overflow: hidden; 
  margin-bottom: 20px; 
}

.card:hover { 
  box-shadow: var(--shadow-card-hover); 
}

.card-header { 
  padding: 16px 20px; 
  border-bottom: 1px solid var(--border-color);
  display: flex; 
  align-items: center; 
  justify-content: space-between; 
  background: var(--bg-hover);
}

.card-title { 
  font-family: 'Outfit', sans-serif; 
  font-size: 14px; 
  font-weight: 700; 
  color: var(--text-primary); 
}

.card-subtitle { 
  font-size: 12px;
  color: var(--text-muted); 
  margin-top: 2px; 
}

.card-body { 
  padding: 20px; 
}

/* Buttons */
.btn { 
  display: inline-flex; 
  align-items: center; 
  gap: 6px; 
  padding: 8px 18px; 
  border-radius: var(--radius-sm); 
  font-family: 'DM Sans', sans-serif; 
  font-size: 13px; 
  font-weight: 600; 
  cursor: pointer; 
  border: none; 
  transition: all .18s; 
  white-space: nowrap; 
  text-decoration: none; 
}

.btn:active { 
  transform: translateY(1px); 
}

.btn-primary { 
  background: linear-gradient(135deg, var(--blue), var(--blue-2)); 
  color: #fff; 
  box-shadow: 0 2px 8px rgba(37,99,235,.3); 
}

.btn-primary:hover { 
  box-shadow: 0 4px 16px rgba(37,99,235,.4); 
  color: #fff; 
}

.btn-gold { 
  background: linear-gradient(135deg, var(--gold), #b8841e); 
  color: #fff; 
  box-shadow: 0 2px 8px rgba(212,152,46,.3); 
}

.btn-gold:hover { 
  box-shadow: 0 4px 16px rgba(212,152,46,.4); 
  color: #fff; 
}

.btn-success { 
  background: linear-gradient(135deg, var(--green), #047857); 
  color: #fff; 
  box-shadow: 0 2px 6px rgba(5,150,105,.25); 
}

.btn-success:hover { 
  box-shadow: 0 4px 12px rgba(5,150,105,.35); 
  color: #fff; 
}

.btn-danger { 
  background: linear-gradient(135deg, var(--red), #b91c1c); 
  color: #fff; 
  box-shadow: 0 2px 6px rgba(220,38,38,.25); 
}

.btn-danger:hover { 
  box-shadow: 0 4px 12px rgba(220,38,38,.35); 
  color: #fff; 
}

.btn-outline { 
  background: transparent; 
  color: var(--text-muted); 
  border: 1.5px solid var(--border-input); 
}

.btn-outline:hover { 
  background: var(--bg-hover); 
  border-color: var(--text-light); 
  color: var(--text-primary); 
}

.btn-sm { 
  padding: 5px 12px; 
  font-size: 12px; 
  border-radius: 6px; 
}

/* Forms */
.form-group { 
  margin-bottom: 16px; 
}

.form-label { 
  display: block; 
  font-size: 12px; 
  font-weight: 600; 
  color: var(--text-muted); 
  margin-bottom: 6px; 
  letter-spacing: .2px; 
}

.form-control { 
  width: 100%; 
  padding: 9px 13px; 
  border-radius: var(--radius-sm); 
  font-family: 'DM Sans', sans-serif; 
  font-size: 13px;  
  outline: none; 
  border: 1.5px solid var(--border-input);
  color: var(--text-primary);
  background: var(--bg-input);
  transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;
}

.form-control:focus { 
  border-color: var(--blue); 
  box-shadow: 0 0 0 3px rgba(37,99,235,.18); 
}

.form-control::placeholder { 
  color: var(--text-light); 
}

/* Table */
.table-wrap { 
  overflow-x: auto; 
}

table { 
  width: 100%; 
  border-collapse: collapse; 
}

th { 
  padding: 11px 16px; 
  font-size: 11px; 
  font-weight: 700; 
  text-align: left; 
  color: var(--text-muted);
  background: var(--bg-table-header);
  border-bottom: 1px solid var(--border-color);
  letter-spacing: .8px; 
  text-transform: uppercase; 
}

td { 
  padding: 12px 16px; 
  font-size: 13px; 
  color: var(--text-secondary);
  border-bottom: 1px solid var(--border-light); 
}

tr:last-child td { 
  border-bottom: none; 
}

tbody tr:hover td { 
  background: var(--bg-table-stripe); 
}

/* Badges */
.badge { 
  display: inline-flex; 
  align-items: center; 
  gap: 4px; 
  padding: 3px 10px; 
  border-radius: 20px; 
  font-size: 11px; 
  font-weight: 700; 
}

.badge-success { 
  background: rgba(16,185,129,.18); 
  color: #34d399; 
  border: 1px solid rgba(16,185,129,.28); 
}

.badge-warning { 
  background: rgba(245,158,11,.18); 
  color: #fbbf24; 
  border: 1px solid rgba(245,158,11,.28); 
}

.badge-danger { 
  background: rgba(239,68,68,.18);  
  color: #f87171; 
  border: 1px solid rgba(239,68,68,.28); 
}

.badge-info { 
  background: rgba(59,130,246,.18);  
  color: #60a5fa; 
  border: 1px solid rgba(59,130,246,.28); 
}

/* Alerts */
.alert { 
  padding: 12px 16px; 
  border-radius: var(--radius-sm); 
  margin-bottom: 16px; 
  font-size: 13px; 
  border-left: 3px solid; 
}

.alert-success { 
  background: rgba(16,185,129,.12); 
  border-color: #10b981; 
  color: #34d399; 
}

.alert-danger { 
  background: rgba(239,68,68,.12);  
  border-color: #ef4444; 
  color: #f87171; 
}

.alert-warning { 
  background: rgba(245,158,11,.12); 
  border-color: #f59e0b; 
  color: #fbbf24; 
}

.alert-info { 
  background: rgba(59,130,246,.12);  
  border-color: #3b82f6; 
  color: #60a5fa; 
}

/* Modal */
.modal-wrap { 
  display: none; 
  position: fixed; 
  inset: 0; 
  background: rgba(0,0,0,.7); 
  backdrop-filter: blur(8px); 
  z-index: 1000; 
  align-items: center; 
  justify-content: center; 
}

.modal-wrap.open { 
  display: flex; 
}

.modal-box { 
  background: var(--bg-modal);
  border: 1px solid var(--border-color);
  border-radius: 16px; 
  width: 90%; 
  max-width: 560px; 
  max-height: 90vh; 
  overflow-y: auto; 
  box-shadow: var(--shadow-modal);
  transition: background 0.3s ease, border-color 0.3s ease;
  animation: modalIn .22s cubic-bezier(.34,1.56,.64,1); 
}

@keyframes modalIn { 
  from { opacity: 0; transform: scale(.93) translateY(-10px); } 
  to { opacity: 1; transform: scale(1) translateY(0); } 
}

.modal-hd { 
  padding: 18px 22px; 
  border-bottom: 1px solid var(--border-color); 
  display: flex; 
  align-items: center; 
  justify-content: space-between; 
}

.modal-title { 
  font-family: 'Outfit', sans-serif; 
  font-size: 15px; 
  font-weight: 700;  
  color: var(--text-primary); 
}

.modal-close { 
  width: 30px; 
  height: 30px; 
  border-radius: 50%; 
  background: rgba(255,255,255,.08); 
  border: 1.5px solid rgba(255,255,255,.15); 
  cursor: pointer; 
  font-size: 13px; 
  color: var(--text-muted); 
  display: flex; 
  align-items: center; 
  justify-content: center; 
  transition: all .15s; 
}

.modal-close:hover { 
  background: rgba(255,255,255,.15); 
}

.modal-bd { 
  padding: 22px; 
}

.modal-ft { 
  padding: 16px 22px; 
  border-top: 1px solid var(--border-color); 
  display: flex; 
  justify-content: flex-end; 
  gap: 8px; 
  background: rgba(0,0,0,.15); 
}

/* Stats */
.stats-grid { 
  display: grid; 
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); 
  gap: 16px; 
  margin-bottom: 20px; 
}

.stat-card { 
  background: var(--bg-card); 
  border-radius: var(--radius); 
  padding: 20px; 
  border: 1px solid var(--border-color); 
  transition: all .2s; 
  position: relative; 
  overflow: hidden; 
}

.stat-card:hover { 
  transform: translateY(-2px); 
  box-shadow: var(--shadow-card-hover); 
}

.stat-icon { 
  font-size: 22px; 
  margin-bottom: 10px; 
  display: block; 
}

.stat-value { 
  font-family: 'Outfit', sans-serif; 
  font-size: 28px; 
  font-weight: 800; 
  color: var(--text-primary); 
  line-height: 1; 
  letter-spacing: -1px; 
}

.stat-label { 
  font-size: 12px; 
  color: var(--text-muted); 
  margin-top: 4px; 
  font-weight: 500; 
}

/* Empty state */
.empty-state { 
  text-align: center; 
  padding: 52px 20px; 
  color: var(--text-muted); 
}

.empty-state .icon { 
  font-size: 42px; 
  margin-bottom: 14px; 
  display: block; 
}

.empty-state h3 { 
  font-size: 15px; 
  font-weight: 700; 
  color: var(--text-secondary); 
  margin-bottom: 6px; 
  font-family: 'Outfit', sans-serif; 
}

/* Scrollbar global */
::-webkit-scrollbar { 
  width: 5px; 
  height: 5px; 
}

::-webkit-scrollbar-thumb { 
  background: var(--scrollbar-thumb); 
  border-radius: 99px; 
}

::-webkit-scrollbar-thumb:hover { 
  background: rgba(255,255,255,.3); 
}

/* Animations */
@keyframes fadeUp { 
  from { opacity: 0; transform: translateY(10px); } 
  to { opacity: 1; transform: translateY(0); } 
}

.page-content > * { 
  animation: fadeUp .28s ease both; 
}

.page-content > *:nth-child(2) { 
  animation-delay: .05s; 
}

.page-content > *:nth-child(3) { 
  animation-delay: .1s; 
}

/* File error popup */
@keyframes popIn { 
  from { opacity: 0; transform: scale(.75) translateY(20px); } 
  to { opacity: 1; transform: scale(1) translateY(0); } 
}

.file-error-overlay { 
  position: fixed; 
  inset: 0; 
  background: rgba(0,0,0,.65); 
  backdrop-filter: blur(8px); 
  z-index: 99999; 
  display: flex; 
  align-items: center; 
  justify-content: center; 
}

.file-error-box { 
  background: var(--bg-modal); 
  border: 1px solid rgba(239,68,68,.3); 
  border-radius: 24px; 
  padding: 44px 48px; 
  max-width: 500px; 
  width: 90%; 
  text-align: center; 
  box-shadow: var(--shadow-modal); 
  animation: popIn .35s cubic-bezier(.34,1.56,.64,1); 
}

.file-error-icon { 
  width: 72px; 
  height: 72px; 
  background: rgba(239,68,68,.12); 
  border: 2px solid rgba(239,68,68,.25); 
  border-radius: 50%; 
  display: flex; 
  align-items: center; 
  justify-content: center; 
  font-size: 34px; 
  margin: 0 auto 20px; 
}

.file-error-title { 
  font-family: 'Outfit', sans-serif; 
  font-size: 22px; 
  font-weight: 800; 
  color: #f87171; 
  margin-bottom: 12px; 
}

.file-error-message { 
  font-size: 14px; 
  color: var(--text-muted); 
  margin-bottom: 14px; 
  line-height: 1.6; 
}

.file-error-badge { 
  display: inline-flex; 
  align-items: center; 
  gap: 6px; 
  background: rgba(239,68,68,.08); 
  border: 1px solid rgba(239,68,68,.22); 
  border-radius: 20px; 
  padding: 5px 14px; 
  font-size: 12px; 
  font-weight: 700; 
  color: #fca5a5; 
  margin-bottom: 24px; 
}

.file-error-btn { 
  background: linear-gradient(135deg,#ef4444,#dc2626); 
  color: #fff; 
  border: none; 
  padding: 12px 44px; 
  border-radius: 40px; 
  font-size: 14px; 
  font-weight: 700; 
  cursor: pointer; 
  box-shadow: 0 4px 20px rgba(239,68,68,.35); 
  transition: all .2s; 
  font-family: 'DM Sans', sans-serif; 
}

.file-error-btn:hover { 
  transform: translateY(-2px); 
  box-shadow: 0 8px 28px rgba(239,68,68,.45); 
}

/* ══════════════════════════════════════════
   RESPONSIVE — MOBILE
══════════════════════════════════════════ */
@media (max-width: 768px) {
  
  :root { --sidebar-w: 260px; }

  .sidebar {
    transform: translateX(-100%);
    box-shadow: none;
  }
  
  .sidebar.open {
    transform: translateX(0);
    box-shadow: 4px 0 40px rgba(0,0,0,.5);
  }

  /* ── Tampilkan tombol close di mobile ── */
  .btn-close-sidebar {
    display: flex !important;
  }

  .btn-hamburger { 
    display: flex; 
  }

  .main-content { 
    margin-left: 0; 
  }

  .page-content { 
    padding: 16px; 
  }

  .topbar { 
    padding: 0 16px; 
  }
  
  .topbar-title { 
    font-size: 14px; 
  }
  
  .topbar-sub { 
    display: none; 
  }
  
  .topbar-actions-wrapper { 
    display: none; 
  }

  .stats-grid { 
    grid-template-columns: repeat(2, 1fr); 
    gap: 12px; 
  }

  .modal-box { 
    width: 95%; 
    max-width: 95%; 
  }

  .file-error-box { 
    padding: 32px 24px; 
  }
}

/* Tambahan overlay untuk mobile */
.sidebar-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.5);
  z-index: 150;
}

.sidebar-overlay.active {
  display: block;
}

@media (max-width: 480px) {
  .stats-grid { 
    grid-template-columns: 1fr; 
  }
  
  .page-content { 
    padding: 12px; 
  }
}

/* ══════════════════════════════════════════
   PRINT
══════════════════════════════════════════ */
@media print {
  .sidebar-overlay,
  .sidebar,
  .topbar,
  .file-error-overlay,
  .alert { 
    display: none !important; 
  }

  .main-content { 
    margin-left: 0 !important; 
  }
  
  .page-content { 
    padding: 0 !important; 
  }

  body { 
    background: #fff !important; 
  }
}
</style>
</head>
<body>

{{-- OVERLAY untuk mobile --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="app-layout">

  {{-- ══════════════════════════════════════
       SIDEBAR
  ══════════════════════════════════════ --}}
  <aside class="sidebar" id="sidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">
      <img src="{{ asset('assets/logo_ntt.png') }}" onerror="this.style.display='none'" alt="Logo NTT">
      <div class="brand-text">
        <h2>e-SAKIPKU</h2>
        <span>Prov. NTT</span>
      </div>
      <button class="btn-close-sidebar" onclick="closeSidebar()">✕</button>
    </div>

    @php $userRole = session('user.role', 'guest'); @endphp

    {{-- ════ MENU UTAMA ════ --}}
    <div class="sidebar-label">Utama</div>
    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <span class="nav-icon">🏠</span>
      <span class="nav-label">Dashboard</span>
    </a>

    {{-- ════ ADMIN ════ --}}
    @if($userRole === 'admin')

      <div class="sidebar-label">Dokumen</div>

      <a class="nav-item nav-group-toggle" onclick="toggleNav('grpPerjanjianAdmin', this)">
        <span class="nav-icon">🤝</span>
        <span class="nav-label">Perencanaan Kinerja</span>
        <span class="nav-arrow">▾</span>
      </a>
      <div class="nav-group-body {{ request()->is('perjanjian/*') ? 'open' : '' }}" id="grpPerjanjianAdmin">
        <a href="{{ route('perjanjian.index','renstra-iku') }}"          class="nav-sub {{ request()->is('perjanjian/renstra-iku') ? 'active' : '' }}">RENSTRA / IKU</a>
        <a href="{{ route('perjanjian.index','perjanjian-kinerja') }}"   class="nav-sub {{ request()->is('perjanjian/perjanjian-kinerja') ? 'active' : '' }}">Perjanjian Kinerja</a>
        <a href="{{ route('perjanjian.index','perjanjian-iku') }}"       class="nav-sub {{ request()->is('perjanjian/perjanjian-iku') ? 'active' : '' }}">IKU</a>
        <a href="{{ route('perjanjian.index','pelaksanaan-anggaran') }}" class="nav-sub {{ request()->is('perjanjian/pelaksanaan-anggaran') ? 'active' : '' }}">Rencana Aksi</a>
        <a href="{{ route('perjanjian.index','dpa') }}"                  class="nav-sub {{ request()->is('perjanjian/dpa') ? 'active' : '' }}">DPA</a>
        <a href="{{ route('perjanjian.cascading') }}"                    class="nav-sub {{ request()->routeIs('perjanjian.cascading') ? 'active' : '' }}">Cascading / Pohon Kinerja</a>
      </div>

      <a class="nav-item nav-group-toggle" onclick="toggleNav('grpPengukuranAdmin', this)">
        <span class="nav-icon">📈</span>
        <span class="nav-label">Pengukuran Kinerja</span>
        <span class="nav-arrow">▾</span>
      </a>
      <div class="nav-group-body {{ request()->routeIs('pengukuran.*') ? 'open' : '' }}" id="grpPengukuranAdmin">
        <a href="{{ route('pengukuran.periodik') }}" class="nav-sub {{ request()->routeIs('pengukuran.periodik*') ? 'active' : '' }}">Minimal Pengukuran Kinerja Periodik</a>
      </div>

      <a class="nav-item nav-group-toggle" onclick="toggleNav('grpPelaporanAdmin', this)">
        <span class="nav-icon">📋</span>
        <span class="nav-label">Pelaporan Kinerja</span>
        <span class="nav-arrow">▾</span>
      </a>
      <div class="nav-group-body {{ request()->routeIs('lkip.*') || request()->routeIs('juknis.*') ? 'open' : '' }}" id="grpPelaporanAdmin">
        <a href="{{ route('lkip.index') }}"   class="nav-sub {{ request()->routeIs('lkip.*') ? 'active' : '' }}">LKIP Perangkat Daerah</a>
        <a href="{{ route('juknis.index') }}" class="nav-sub {{ request()->routeIs('juknis.index') ? 'active' : '' }}">Kelola Juknis</a>
      </div>

      <a class="nav-item nav-group-toggle" onclick="toggleNav('grpEvaluasiAdmin', this)">
        <span class="nav-icon">📊</span>
        <span class="nav-label">Evaluasi Kinerja</span>
        <span class="nav-arrow">▾</span>
      </a>
      <div class="nav-group-body {{ request()->is('evaluasi/*') ? 'open' : '' }}" id="grpEvaluasiAdmin">
        <a href="{{ route('evaluasi.juknis') }}" class="nav-sub {{ request()->routeIs('evaluasi.juknis') ? 'active' : '' }}">Juknis</a>
        <a href="{{ route('evaluasi.lke') }}"    class="nav-sub {{ request()->routeIs('evaluasi.lke') ? 'active' : '' }}">LKE AKIP</a>
      </div>

      <div class="nav-divider"></div>
      <div class="sidebar-label">Manajemen Pengguna</div>

      <a href="{{ route('admin.landing') }}" class="nav-item {{ request()->routeIs('admin.landing') ? 'active' : '' }}">
      <span class="nav-icon">🖼️</span>
      <span class="nav-label">Landing Page</span>
      </a>

      <a href="{{ route('admin.pengguna') }}" class="nav-item {{ request()->routeIs('admin.pengguna') ? 'active' : '' }}">
        <span class="nav-icon">👥</span>
        <span class="nav-label">Kelola OPD</span>
      </a>

      <a href="{{ route('admin.persetujuan') }}" class="nav-item {{ request()->routeIs('admin.persetujuan') ? 'active' : '' }}">
        <span class="nav-icon">✅</span>
        <span class="nav-label">Persetujuan Akun</span>
        @php $jmlMenunggu = \Illuminate\Support\Facades\DB::table('pengguna')->where('status_daftar','menunggu')->count(); @endphp
        @if($jmlMenunggu > 0)
          <span class="nav-badge">{{ $jmlMenunggu }}</span>
        @endif
      </a>

      <a href="{{ route('admin.klaster') }}" class="nav-item {{ request()->routeIs('admin.klaster*') ? 'active' : '' }}">
        <span class="nav-icon">🏷️</span>
        <span class="nav-label">Kelola Klaster </span>
      </a>

      <a href="{{ route('admin.arsip') }}" class="nav-item {{ request()->routeIs('admin.arsip*') ? 'active' : '' }}">
        <span class="nav-icon">🗂️</span>
        <span class="nav-label">Arsip Dokumen</span>
      </a>

      <a href="{{ route('rekapan.hasil') }}" class="nav-item {{ request()->routeIs('rekapan.hasil*') ? 'active' : '' }}">
        <span class="nav-icon">📊</span>
        <span class="nav-label">Rekapan Hasil LKE</span>
        <span class="nav-badge" style="background: #6366f1;">AKIP</span>
      </a>

    @endif

    {{-- ════ OPERATOR ════ --}}
    @if($userRole === 'operator')

      <div class="sidebar-label">Dokumen</div>

      <a class="nav-item nav-group-toggle" onclick="toggleNav('grpPerjanjian', this)">
        <span class="nav-icon">🤝</span>
        <span class="nav-label">Perencanaan Kinerja</span>
        <span class="nav-arrow">▾</span>
      </a>
      <div class="nav-group-body {{ request()->is('perjanjian/*') ? 'open' : '' }}" id="grpPerjanjian">
        <a href="{{ route('perjanjian.index','renstra-iku') }}"          class="nav-sub {{ request()->is('perjanjian/renstra-iku') ? 'active' : '' }}">RENSTRA / IKU</a>
        <a href="{{ route('perjanjian.index','perjanjian-kinerja') }}"   class="nav-sub {{ request()->is('perjanjian/perjanjian-kinerja') ? 'active' : '' }}">Perjanjian Kinerja</a>
        <a href="{{ route('perjanjian.index','perjanjian-iku') }}"       class="nav-sub {{ request()->is('perjanjian/perjanjian-iku') ? 'active' : '' }}">IKU</a>
        <a href="{{ route('perjanjian.index','pelaksanaan-anggaran') }}" class="nav-sub {{ request()->is('perjanjian/pelaksanaan-anggaran') ? 'active' : '' }}">Rencana Aksi</a>
        <a href="{{ route('perjanjian.index','dpa') }}"                  class="nav-sub {{ request()->is('perjanjian/dpa') ? 'active' : '' }}">DPA</a>
        <a href="{{ route('perjanjian.cascading') }}"                    class="nav-sub {{ request()->routeIs('perjanjian.cascading') ? 'active' : '' }}">Cascading / Pohon Kinerja</a>
      </div>

      <a class="nav-item nav-group-toggle" onclick="toggleNav('grpPengukuran', this)">
        <span class="nav-icon">📈</span>
        <span class="nav-label">Pengukuran Kinerja</span>
        <span class="nav-arrow">▾</span>
      </a>
      <div class="nav-group-body {{ request()->routeIs('pengukuran.*') ? 'open' : '' }}" id="grpPengukuran">
        <a href="{{ route('pengukuran.periodik') }}" class="nav-sub {{ request()->routeIs('pengukuran.periodik*') ? 'active' : '' }}">Minimal Pengukuran Kinerja Periodik</a>
      </div>

      <a class="nav-item nav-group-toggle" onclick="toggleNav('grpPelaporan', this)">
        <span class="nav-icon">📋</span>
        <span class="nav-label">Pelaporan Kinerja</span>
        <span class="nav-arrow">▾</span>
      </a>
      <div class="nav-group-body {{ request()->routeIs('lkip.*') || request()->routeIs('juknis.*') ? 'open' : '' }}" id="grpPelaporan">
        <a href="{{ route('lkip.index') }}"  class="nav-sub {{ request()->routeIs('lkip.*') ? 'active' : '' }}">LKIP Perangkat Daerah</a>
        <a href="{{ route('juknis.user') }}" class="nav-sub {{ request()->routeIs('juknis.user') ? 'active' : '' }}">Petunjuk Teknis</a>
      </div>

      <a class="nav-item nav-group-toggle" onclick="toggleNav('grpEvaluasi', this)">
        <span class="nav-icon">📊</span>
        <span class="nav-label">Evaluasi Kinerja</span>
        <span class="nav-arrow">▾</span>
      </a>
      <div class="nav-group-body {{ request()->is('evaluasi/*') ? 'open' : '' }}" id="grpEvaluasi">
        <a href="{{ route('evaluasi.juknis') }}" class="nav-sub {{ request()->routeIs('evaluasi.juknis') ? 'active' : '' }}">Juknis</a>
        <a href="{{ route('evaluasi.lke') }}"    class="nav-sub {{ request()->routeIs('evaluasi.lke') ? 'active' : '' }}">LKE AKIP</a>
      </div>

    @endif

    {{-- ════ EVALUATOR ════ --}}
    @if($userRole === 'evaluator')
      <div class="sidebar-label">Evaluasi</div>
      <a class="nav-item nav-group-toggle" onclick="toggleNav('grpEvaluatorEval', this)">
        <span class="nav-icon">📊</span>
        <span class="nav-label">Evaluasi Kinerja</span>
        <span class="nav-arrow">▾</span>
      </a>
      <div class="nav-group-body {{ request()->is('evaluasi/*') ? 'open' : '' }}" id="grpEvaluatorEval">
        <a href="{{ route('evaluasi.juknis') }}" class="nav-sub {{ request()->routeIs('evaluasi.juknis') ? 'active' : '' }}">Juknis</a>
        <a href="{{ route('evaluasi.lke') }}"    class="nav-sub {{ request()->routeIs('evaluasi.lke') ? 'active' : '' }}">LKE AKIP</a>
      </div>
    @endif
{{-- ════ TOGGLE THEME ════ --}}
<div class="sidebar-footer">
  
  {{-- 🆕 TOMBOL TOGGLE TEMA --}}
  <div style="padding: 0 12px 12px 12px;">
    <button onclick="toggleTheme()" 
            style="width:100%; padding:10px 12px; 
                   background:rgba(255,255,255,.06); 
                   border:1px solid rgba(255,255,255,.1); 
                   border-radius:10px; 
                   color:var(--text-muted); 
                   cursor:pointer; 
                   font-family:inherit; 
                   font-size:13px; 
                   font-weight:600; 
                   display:flex; 
                   align-items:center; 
                   justify-content:center; 
                   gap:8px;
                   transition:all .2s;"
                   onmouseover="this.style.background='rgba(255,255,255,.12)'"
                   onmouseout="this.style.background='rgba(255,255,255,.06)'">
      <span id="themeIconSidebar">🌙</span>
      <span id="themeLabelSidebar">Mode Gelap</span>
    </button>
  </div>

  {{-- USER CARD --}}
  <div class="user-card">
    <div class="user-avatar">{{ strtoupper(substr(session('user.nama','A'),0,1)) }}</div>
    <div class="user-info">
      <div class="user-name">{{ session('user.nama') }}</div>
      <div class="user-role">
        @if($userRole === 'admin')        Administrator
        @elseif($userRole === 'evaluator') Evaluator
        @else {{ session('user.nama_daerah') ?? 'Operator' }}
        @endif
      </div>
    </div>
  </div>
  
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn-logout">
      <span>🚪</span> Keluar
    </button>
  </form>
</div>

  </aside>

  {{-- ══════════════════════════════════════
       MAIN CONTENT
  ══════════════════════════════════════ --}}
  <div class="main-content">

    {{-- TOPBAR --}}
    <div class="topbar">
      <button class="btn-hamburger" id="btnHamburger" onclick="openSidebar()">☰</button>

      <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
      <span class="topbar-sub">@yield('page-sub', '')</span>

      @hasSection('topbar-actions')
        <div class="topbar-actions-wrapper">
          @yield('topbar-actions')
        </div>
      @endif

      {{-- Kanan: notif + chat --}}
      <div style="margin-left:auto;display:flex;align-items:center;gap:8px;">

        {{-- NOTIFIKASI --}}
        <div style="position:relative;">
          <button class="topbar-icon-btn" id="btnNotif" onclick="toggleNotif()">
            🔔
            <span class="icon-badge notif-badge-bg" id="notifBadge"></span>
          </button>
          <div class="notif-dropdown" id="dropNotif">
            <div class="notif-header">
              <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:13px;font-weight:700;color:var(--text-primary);">Notifikasi</span>
                <span id="notifCount" style="display:none;font-size:10px;background:rgba(99,102,241,.2);color:#a5b4fc;padding:2px 8px;border-radius:20px;font-weight:600;"></span>
              </div>
              <button onclick="bacaSemua()" style="font-size:11px;color:#6366f1;background:none;border:none;cursor:pointer;font-weight:600;padding:4px 8px;border-radius:6px;transition:background .15s;" onmouseover="this.style.background='rgba(99,102,241,.1)'" onmouseout="this.style.background='none'">
                Baca semua
              </button>
            </div>
            <div class="notif-list" id="notifList">
              <div style="padding:24px;text-align:center;color:var(--text-muted);font-size:12px;">Memuat...</div>
            </div>
          </div>
        </div>

        {{-- CHAT --}}
        <a href="{{ route('chat.index') }}" class="topbar-icon-btn" onmouseover="this.style.background='rgba(255,255,255,.13)'" onmouseout="this.style.background='rgba(255,255,255,.07)'">
          💬
          <span class="icon-badge chat-badge-bg" id="chatBadge"></span>
        </a>

      </div>
    </div>

    {{-- PAGE CONTENT --}}
    <div class="page-content">

      {{-- File error popup --}}
      @if(session('file_error'))
      <div id="fileErrorOverlay" class="file-error-overlay">
        <div class="file-error-box">
          <div class="file-error-icon">{{ session('file_error_type') === 'type' ? '🚫' : '⚠️' }}</div>
          <div class="file-error-title">Upload Gagal</div>
          <div class="file-error-message">{{ session('file_error') }}</div>
          <div class="file-error-badge">
            @if(session('file_error_type') === 'type')
              📄 Format: <strong>PDF, XLS, XLSX</strong>
            @else
              📁 Maks: <strong>5 MB</strong>
            @endif
          </div>
          <button class="file-error-btn" onclick="document.getElementById('fileErrorOverlay').style.display='none'">OK, Mengerti</button>
        </div>
      </div>
      @endif

      {{-- Flash messages --}}
      @if(session('success'))<div class="alert alert-success">✅ {{ session('success') }}</div>@endif
      @if(session('error'))<div class="alert alert-danger">❌ {{ session('error') }}</div>@endif
      @if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)❌ {{ $e }}<br>@endforeach</div>@endif

      @yield('content')
    </div>
  </div>
</div>

<script>
/* ══ Sidebar mobile ══════════════════════════ */
function openSidebar() {
  document.getElementById('sidebar').classList.add('open');
  document.getElementById('sidebarOverlay').classList.add('active');
  document.body.style.overflow = 'hidden';
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebarOverlay').classList.remove('active');
  document.body.style.overflow = '';
}

/* ══ Nav group toggle ════════════════════════ */
function toggleNav(id, el) {
  var body = document.getElementById(id);
  if (!body) return;
  var arr  = el.querySelector('.nav-arrow');
  var open = body.classList.toggle('open');
  if (arr) arr.style.transform = open ? 'rotate(180deg)' : '';
}

/* Auto-open grup yang aktif */
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.nav-group-body.open').forEach(function(g) {
    var arr = g.previousElementSibling && g.previousElementSibling.querySelector('.nav-arrow');
    if (arr) arr.style.transform = 'rotate(180deg)';
  });
});

/* ══ Notifikasi ══════════════════════════════ */
const WARNA_MAP = {
  indigo: { bg:'rgba(99,102,241,.15)',  border:'rgba(99,102,241,.35)', dot:'#6366f1' },
  green:  { bg:'rgba(16,185,129,.12)',  border:'rgba(16,185,129,.3)',  dot:'#10b981' },
  amber:  { bg:'rgba(245,158,11,.12)',  border:'rgba(245,158,11,.3)',  dot:'#f59e0b' },
  red:    { bg:'rgba(239,68,68,.12)',   border:'rgba(239,68,68,.28)',  dot:'#ef4444' },
  blue:   { bg:'rgba(59,130,246,.12)',  border:'rgba(59,130,246,.28)', dot:'#3b82f6' },
};

function toggleNotif() {
  var d = document.getElementById('dropNotif');
  var open = d.style.display === 'block';
  d.style.display = open ? 'none' : 'block';
  if (!open) muatNotifikasi();
}

function muatNotifikasi() {
  fetch('{{ route("notifikasi.ambil") }}')
    .then(r => r.json())
    .then(data => { renderNotif(data.notifikasi); updateBadge(data.belum_dibaca); })
    .catch(err => console.error('Gagal muat notifikasi:', err));
}

function renderNotif(list) {
  var el = document.getElementById('notifList');
  if (!list || !list.length) {
    el.innerHTML = '<div style="padding:28px;text-align:center;color:var(--text-muted);font-size:12px;">Belum ada notifikasi</div>';
    return;
  }
  el.innerHTML = list.map(function(n) {
    var w = WARNA_MAP[n.warna] || WARNA_MAP.indigo;
    var bgRow = n.sudah_dibaca ? 'transparent' : 'rgba(99,102,241,.04)';
    return `<div onclick="klikNotif(${n.id},'${n.url}')"
      style="display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border-light);cursor:pointer;background:${bgRow};transition:background .15s;"
      onmouseover="this.style.background='var(--bg-hover)'"
      onmouseout="this.style.background='${bgRow}'">
      <div style="width:36px;height:36px;border-radius:10px;flex-shrink:0;background:${w.bg};border:1px solid ${w.border};display:flex;align-items:center;justify-content:center;font-size:16px;">${n.ikon}</div>
      <div style="flex:1;min-width:0;">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:3px;">
          <span style="font-size:12px;font-weight:600;color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${n.judul}</span>
          ${!n.sudah_dibaca ? `<span style="width:7px;height:7px;border-radius:50%;background:${w.dot};flex-shrink:0;"></span>` : ''}
        </div>
        <p style="font-size:11.5px;color:var(--text-muted);line-height:1.45;margin:0 0 4px;">${n.pesan}</p>
        <span style="font-size:10.5px;color:var(--text-light);">${n.waktu}</span>
      </div>
    </div>`;
  }).join('');
}

function updateBadge(jumlah) {
  var badge = document.getElementById('notifBadge');
  var count = document.getElementById('notifCount');
  if (jumlah > 0) {
    badge.textContent = jumlah > 99 ? '99+' : jumlah;
    badge.style.display = 'flex';
    count.textContent = jumlah + ' baru';
    count.style.display = 'inline';
  } else {
    badge.style.display = 'none';
    count.style.display = 'none';
  }
}

function updateChatBadge() {
  fetch('/chat/unread-count')
    .then(r => r.json())
    .then(data => {
      var badge = document.getElementById('chatBadge');
      if (badge) {
        if (data.unread_count > 0) { badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count; badge.style.display = 'flex'; }
        else badge.style.display = 'none';
      }
    }).catch(() => {});
}

function klikNotif(id, url) {
  fetch(`/notifikasi/${id}/baca`, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
  }).then(() => { muatNotifikasi(); if (url && url !== '#') window.location.href = url; });
}

function bacaSemua() {
  fetch('{{ route("notifikasi.bacaSemua") }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
  }).then(() => muatNotifikasi());
}

/* Tutup dropdown notif saat klik di luar */
document.addEventListener('click', function(e) {
  if (!e.target.closest('#btnNotif') && !e.target.closest('#dropNotif')) {
    var d = document.getElementById('dropNotif');
    if (d) d.style.display = 'none';
  }
});

/* ══ Toggle Theme (Dark / Light) ════════════════ */
function toggleTheme() {
  const html = document.documentElement;
  const currentTheme = html.getAttribute('data-theme');
  const newTheme = currentTheme === 'light' ? 'dark' : 'light';
  
  html.setAttribute('data-theme', newTheme);
  localStorage.setItem('theme', newTheme);
  updateThemeUI(newTheme);
}

function updateThemeUI(theme) {
  const icon = document.getElementById('themeIconSidebar');
  const label = document.getElementById('themeLabelSidebar');
  
  if (icon && label) {
    if (theme === 'light') {
      icon.textContent = '☀️';
      label.textContent = 'Mode Terang';
    } else {
      icon.textContent = '🌙';
      label.textContent = 'Mode Gelap';
    }
  }
}

function loadTheme() {
  const savedTheme = localStorage.getItem('theme');
  const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const defaultTheme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
  
  document.documentElement.setAttribute('data-theme', defaultTheme);
  updateThemeUI(defaultTheme);
}

// Load theme saat halaman dimuat
document.addEventListener('DOMContentLoaded', loadTheme);

// Update CSS transition untuk smooth
document.documentElement.style.transition = 'background 0.3s ease, color 0.3s ease';

/* Inisialisasi */
muatNotifikasi();
updateChatBadge();
setInterval(muatNotifikasi, 120000);
setInterval(updateChatBadge, 60000);
</script>

@yield('scripts')
</body>
</html>
