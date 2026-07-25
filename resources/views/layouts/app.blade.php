<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>E-SAKIPKU — @yield('title', 'Sistem Akuntabilitas Kinerja')</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ══════════════════════════════════════════
   VARIABLES
══════════════════════════════════════════ */
:root {
  --navy:       #0a1628;
  --navy-2:     #0f1f3d;
  --navy-3:     #162442;
  --blue:       #2563eb;
  --blue-2:     #1d4ed8;
  --gold:       #d4982e;
  --gold-2:     #f0b84a;
  --gold-3:     #fcd34d;
  --green:      #059669;
  --red:        #dc2626;
  --sidebar-w:  260px;
  --topbar-h:   58px;
  --radius:     12px;
  --radius-sm:  8px;
  --radius-lg:  16px;
}

*,*::before,*::after { margin:0; padding:0; box-sizing:border-box; }

html { scroll-behavior:smooth; }

body {
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  background: #070d1a;
  color: #ffffff;
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
   SIDEBAR OVERLAY (mobile)
══════════════════════════════════════════ */
.sidebar-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.6);
  backdrop-filter: blur(4px);
  z-index: 199;
}
.sidebar-overlay.active { display: block; }

/* ══════════════════════════════════════════
   SIDEBAR
══════════════════════════════════════════ */
.sidebar {
  width: var(--sidebar-w);
  background: var(--navy);
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
  position: fixed;
  top: 0; left: 0;
  height: 100vh;
  overflow-y: auto;
  overflow-x: hidden;
  z-index: 200;
  transition: transform .3s cubic-bezier(.4,0,.2,1);
  border-right: 1px solid rgba(255,255,255,.06);
}

/* Decorative line */
.sidebar::after {
  content: '';
  position: absolute;
  top: 0; right: 0;
  width: 1px; height: 100%;
  background: linear-gradient(
    to bottom,
    transparent 0%,
    rgba(212,152,46,.4) 30%,
    rgba(212,152,46,.15) 70%,
    transparent 100%
  );
  pointer-events: none;
}

/* Scrollbar dalam sidebar */
.sidebar::-webkit-scrollbar { width: 3px; }
.sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 99px; }

/* ── Brand ── */
.sidebar-brand {
  padding: 18px 16px 14px;
  display: flex;
  align-items: center;
  gap: 12px;
  border-bottom: 1px solid rgba(255,255,255,.06);
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
  color: #fff;
  letter-spacing: .2px;
  line-height: 1.2;
}
.brand-text span {
  font-size: 9px;
  color: rgba(255,255,255,.3);
  letter-spacing: 1.5px;
  text-transform: uppercase;
}

/* Tombol tutup sidebar (mobile only) */
.btn-close-sidebar {
  display: none;
  margin-left: auto;
  width: 28px; height: 28px;
  border-radius: 8px;
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.12);
  color: rgba(255,255,255,.6);
  cursor: pointer;
  font-size: 14px;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all .2s;
}
.btn-close-sidebar:hover { background: rgba(255,255,255,.15); color: #fff; }

/* ── Nav label ── */
.sidebar-label {
  padding: 14px 18px 4px;
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 1.8px;
  text-transform: uppercase;
  color: rgba(255,255,255,.2);
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
  color: rgba(255,255,255,.5);
  cursor: pointer;
  transition: all .15s;
  text-decoration: none;
  border-left: 2px solid transparent;
  margin: 1px 10px;
  border-radius: 9px;
  line-height: 1.4;
}
.nav-item:hover {
  background: rgba(255,255,255,.06);
  color: rgba(255,255,255,.85);
  text-decoration: none;
}
.nav-item.active {
  background: linear-gradient(135deg, rgba(212,152,46,.16), rgba(37,99,235,.1));
  color: #fff;
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
.nav-item.active .nav-icon { background: rgba(212,152,46,.18); }
.nav-item:hover .nav-icon  { background: rgba(255,255,255,.1); }

.nav-label { flex: 1; min-width: 0; }

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
.nav-group-body { display: none; }
.nav-group-body.open { display: block; }

.nav-sub {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 7px 14px 7px 50px;
  font-size: 12px;
  font-weight: 400;
  color: rgba(255,255,255,.38);
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
  color: rgba(255,255,255,.75);
  background: rgba(255,255,255,.04);
  text-decoration: none;
}
.nav-sub:hover::before { background: var(--gold-2); }
.nav-sub.active {
  color: var(--gold-2);
  font-weight: 600;
  background: rgba(212,152,46,.06);
}
.nav-sub.active::before { background: var(--gold-2); }

/* ── Divider ── */
.nav-divider {
  height: 1px;
  background: rgba(255,255,255,.06);
  margin: 8px 14px;
}

/* ── Footer ── */
.sidebar-footer {
  margin-top: auto;
  padding: 12px;
  border-top: 1px solid rgba(255,255,255,.06);
  flex-shrink: 0;
}
.user-card {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  background: rgba(255,255,255,.04);
  border: 1px solid rgba(255,255,255,.07);
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
.user-info { min-width: 0; }
.user-name {
  font-size: 12px; font-weight: 700; color: #fff;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.user-role { font-size: 10px; color: rgba(255,255,255,.35); margin-top: 2px; }

/* Klaster badge di user card */
.klaster-badge {
  display: inline-flex; align-items: center; gap: 4px;
  padding: 2px 8px;
  border-radius: 20px;
  font-size: 9px; font-weight: 700;
  margin-top: 3px;
}
.klaster-utama     { background: rgba(99,102,241,.2); color: #a5b4fc; border: 1px solid rgba(99,102,241,.3); }
.klaster-pendukung { background: rgba(16,185,129,.2); color: #6ee7b7; border: 1px solid rgba(16,185,129,.3); }
.klaster-tambahan  { background: rgba(245,158,11,.2); color: #fcd34d; border: 1px solid rgba(245,158,11,.3); }

.btn-logout {
  width: 100%; padding: 8px 12px;
  background: rgba(220,38,38,.1);
  border: 1px solid rgba(220,38,38,.2);
  border-radius: 9px;
  color: #fca5a5;
  font-size: 12px; font-weight: 600;
  cursor: pointer; transition: all .15s;
  font-family: inherit;
  display: flex; align-items: center; justify-content: center; gap: 6px;
}
.btn-logout:hover { background: rgba(220,38,38,.2); border-color: rgba(220,38,38,.35); }

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
  background: rgba(10,22,40,.7);
  backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(255,255,255,.07);
  display: flex;
  align-items: center;
  padding: 0 24px;
  gap: 10px;
  position: sticky;
  top: 0; z-index: 50;
  flex-shrink: 0;
}

/* Hamburger (mobile) */
.btn-hamburger {
  display: none;
  width: 36px; height: 36px;
  border-radius: 9px;
  background: rgba(255,255,255,.07);
  border: 1px solid rgba(255,255,255,.1);
  color: rgba(255,255,255,.7);
  cursor: pointer;
  font-size: 16px;
  align-items: center; justify-content: center;
  flex-shrink: 0;
  transition: all .2s;
}
.btn-hamburger:hover { background: rgba(255,255,255,.12); color: #fff; }

.topbar-title {
  font-family: 'Outfit', sans-serif;
  font-size: 16px; font-weight: 700;
  color: #fff;
  letter-spacing: -.2px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.topbar-sub {
  font-size: 11px;
  color: rgba(255,255,255,.55);
  padding: 2px 10px;
  background: rgba(255,255,255,.08);
  border-radius: 20px;
  border: 1px solid rgba(255,255,255,.08);
  white-space: nowrap;
  flex-shrink: 0;
}
.topbar-actions-wrapper {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-left: 12px;
}

/* Tombol notif & chat */
.topbar-icon-btn {
  width: 36px; height: 36px;
  border-radius: 50%;
  background: rgba(255,255,255,.07);
  border: 1px solid rgba(255,255,255,.1);
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
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
  font-size: 9px; font-weight: 800;
  align-items: center; justify-content: center;
  padding: 0 4px;
  border: 2px solid #070d1a;
}
.notif-badge-bg { background: #ef4444; color: #fff; }
.chat-badge-bg  { background: #10b981; color: #fff; }

/* ── Notif dropdown ── */
.notif-dropdown {
  display: none;
  position: absolute;
  top: calc(100% + 8px); right: 0;
  width: 340px;
  max-height: 480px;
  background: linear-gradient(135deg, #1a1f2e, #141824);
  border: 1px solid rgba(255,255,255,.1);
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0,0,0,.5);
  overflow: hidden;
  z-index: 999;
}
.notif-header {
  padding: 14px 16px;
  border-bottom: 1px solid rgba(255,255,255,.07);
  display: flex; align-items: center; justify-content: space-between;
}
.notif-list { max-height: 380px; overflow-y: auto; }
.notif-list::-webkit-scrollbar { width: 3px; }
.notif-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 99px; }

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
.card { background:linear-gradient(135deg,#1a1f2e,#141824); border-radius:var(--radius); border:1px solid rgba(255,255,255,.07); overflow:hidden; transition:box-shadow .2s; margin-bottom:20px; }
.card:hover { box-shadow:0 8px 32px rgba(0,0,0,.35); }
.card-header { padding:16px 20px; border-bottom:1px solid rgba(255,255,255,.07); display:flex; align-items:center; justify-content:space-between; background:rgba(255,255,255,.02); }
.card-title { font-family:'Outfit',sans-serif; font-size:14px; font-weight:700; color:#fff; }
.card-subtitle { font-size:12px; color:rgba(255,255,255,.45); margin-top:2px; }
.card-body { padding:20px; }

/* Buttons */
.btn { display:inline-flex; align-items:center; gap:6px; padding:8px 18px; border-radius:var(--radius-sm); font-family:'DM Sans',sans-serif; font-size:13px; font-weight:600; cursor:pointer; border:none; transition:all .18s; white-space:nowrap; text-decoration:none; }
.btn:active { transform:translateY(1px); }
.btn-primary { background:linear-gradient(135deg,var(--blue),var(--blue-2)); color:#fff; box-shadow:0 2px 8px rgba(37,99,235,.3); }
.btn-primary:hover { box-shadow:0 4px 16px rgba(37,99,235,.4); color:#fff; }
.btn-gold { background:linear-gradient(135deg,var(--gold),#b8841e); color:#fff; box-shadow:0 2px 8px rgba(212,152,46,.3); }
.btn-gold:hover { box-shadow:0 4px 16px rgba(212,152,46,.4); color:#fff; }
.btn-success { background:linear-gradient(135deg,var(--green),#047857); color:#fff; box-shadow:0 2px 6px rgba(5,150,105,.25); }
.btn-success:hover { box-shadow:0 4px 12px rgba(5,150,105,.35); color:#fff; }
.btn-danger { background:linear-gradient(135deg,var(--red),#b91c1c); color:#fff; box-shadow:0 2px 6px rgba(220,38,38,.25); }
.btn-danger:hover { box-shadow:0 4px 12px rgba(220,38,38,.35); color:#fff; }
.btn-outline { background:transparent; color:rgba(255,255,255,.7); border:1.5px solid rgba(255,255,255,.18); }
.btn-outline:hover { background:rgba(255,255,255,.08); border-color:rgba(255,255,255,.35); color:#fff; }
.btn-sm { padding:5px 12px; font-size:12px; border-radius:6px; }

/* Forms */
.form-group { margin-bottom:16px; }
.form-label { display:block; font-size:12px; font-weight:600; color:rgba(255,255,255,.65); margin-bottom:6px; letter-spacing:.2px; }
.form-control { width:100%; padding:9px 13px; border:1.5px solid rgba(255,255,255,.12); border-radius:var(--radius-sm); font-family:'DM Sans',sans-serif; font-size:13px; color:#fff; outline:none; transition:all .2s; background:rgba(255,255,255,.07); }
.form-control:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(37,99,235,.18); }
.form-control::placeholder { color:rgba(255,255,255,.3); }

/* Table */
.table-wrap { overflow-x:auto; }
table { width:100%; border-collapse:collapse; }
th { padding:11px 16px; font-size:11px; font-weight:700; text-align:left; color:rgba(255,255,255,.55); background:rgba(255,255,255,.04); border-bottom:1px solid rgba(255,255,255,.08); letter-spacing:.8px; text-transform:uppercase; }
td { padding:12px 16px; font-size:13px; border-bottom:1px solid rgba(255,255,255,.05); color:rgba(255,255,255,.8); }
tr:last-child td { border-bottom:none; }
tbody tr:hover td { background:rgba(255,255,255,.025); }

/* Badges */
.badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
.badge-success { background:rgba(16,185,129,.18); color:#34d399; border:1px solid rgba(16,185,129,.28); }
.badge-warning { background:rgba(245,158,11,.18); color:#fbbf24; border:1px solid rgba(245,158,11,.28); }
.badge-danger  { background:rgba(239,68,68,.18);  color:#f87171; border:1px solid rgba(239,68,68,.28); }
.badge-info    { background:rgba(59,130,246,.18);  color:#60a5fa; border:1px solid rgba(59,130,246,.28); }

/* Alerts */
.alert { padding:12px 16px; border-radius:var(--radius-sm); margin-bottom:16px; font-size:13px; border-left:3px solid; }
.alert-success { background:rgba(16,185,129,.08); border-color:#10b981; color:#34d399; }
.alert-danger  { background:rgba(239,68,68,.08);  border-color:#ef4444; color:#f87171; }
.alert-warning { background:rgba(245,158,11,.08); border-color:#f59e0b; color:#fbbf24; }
.alert-info    { background:rgba(59,130,246,.08);  border-color:#3b82f6; color:#60a5fa; }

/* Modal */
.modal-wrap { display:none; position:fixed; inset:0; background:rgba(0,0,0,.7); backdrop-filter:blur(8px); z-index:1000; align-items:center; justify-content:center; }
.modal-wrap.open { display:flex; }
.modal-box { background:linear-gradient(135deg,#1a1f2e,#141824); border-radius:16px; width:90%; max-width:560px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 50px rgba(0,0,0,.5); border:1px solid rgba(255,255,255,.09); animation:modalIn .22s cubic-bezier(.34,1.56,.64,1); }
@keyframes modalIn { from{opacity:0;transform:scale(.93) translateY(-10px)} to{opacity:1;transform:scale(1) translateY(0)} }
.modal-hd { padding:18px 22px; border-bottom:1px solid rgba(255,255,255,.08); display:flex; align-items:center; justify-content:space-between; }
.modal-title { font-family:'Outfit',sans-serif; font-size:15px; font-weight:700; color:#fff; }
.modal-close { width:30px; height:30px; border-radius:50%; background:rgba(255,255,255,.08); border:1.5px solid rgba(255,255,255,.15); cursor:pointer; font-size:13px; color:#fff; display:flex; align-items:center; justify-content:center; transition:all .15s; }
.modal-close:hover { background:rgba(255,255,255,.15); }
.modal-bd { padding:22px; }
.modal-ft { padding:16px 22px; border-top:1px solid rgba(255,255,255,.08); display:flex; justify-content:flex-end; gap:8px; background:rgba(0,0,0,.15); }

/* Stats */
.stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:16px; margin-bottom:20px; }
.stat-card { background:linear-gradient(135deg,#1a1f2e,#141824); border-radius:var(--radius); padding:20px; border:1px solid rgba(255,255,255,.07); transition:all .2s; position:relative; overflow:hidden; }
.stat-card:hover { transform:translateY(-2px); box-shadow:0 8px 32px rgba(0,0,0,.35); }
.stat-icon { font-size:22px; margin-bottom:10px; display:block; }
.stat-value { font-family:'Outfit',sans-serif; font-size:28px; font-weight:800; color:#fff; line-height:1; letter-spacing:-1px; }
.stat-label { font-size:12px; color:rgba(255,255,255,.55); margin-top:4px; font-weight:500; }

/* Empty state */
.empty-state { text-align:center; padding:52px 20px; color:rgba(255,255,255,.45); }
.empty-state .icon { font-size:42px; margin-bottom:14px; display:block; }
.empty-state h3 { font-size:15px; font-weight:700; color:rgba(255,255,255,.65); margin-bottom:6px; font-family:'Outfit',sans-serif; }

/* Scrollbar global */
::-webkit-scrollbar { width:5px; height:5px; }
::-webkit-scrollbar-thumb { background:rgba(255,255,255,.15); border-radius:99px; }
::-webkit-scrollbar-thumb:hover { background:rgba(255,255,255,.3); }

/* Animations */
@keyframes fadeUp { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
.page-content > * { animation:fadeUp .28s ease both; }
.page-content > *:nth-child(2) { animation-delay:.05s; }
.page-content > *:nth-child(3) { animation-delay:.1s; }

/* File error popup */
@keyframes popIn { from{opacity:0;transform:scale(.75) translateY(20px)} to{opacity:1;transform:scale(1) translateY(0)} }
.file-error-overlay { position:fixed; inset:0; background:rgba(0,0,0,.65); backdrop-filter:blur(8px); z-index:99999; display:flex; align-items:center; justify-content:center; }
.file-error-box { background:linear-gradient(135deg,#1a1f2e,#141824); border:1px solid rgba(239,68,68,.3); border-radius:24px; padding:44px 48px; max-width:500px; width:90%; text-align:center; box-shadow:0 24px 80px rgba(0,0,0,.5); animation:popIn .35s cubic-bezier(.34,1.56,.64,1); }
.file-error-icon { width:72px; height:72px; background:rgba(239,68,68,.12); border:2px solid rgba(239,68,68,.25); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:34px; margin:0 auto 20px; }
.file-error-title { font-family:'Outfit',sans-serif; font-size:22px; font-weight:800; color:#f87171; margin-bottom:12px; }
.file-error-message { font-size:14px; color:rgba(255,255,255,.7); margin-bottom:14px; line-height:1.6; }
.file-error-badge { display:inline-flex; align-items:center; gap:6px; background:rgba(239,68,68,.08); border:1px solid rgba(239,68,68,.22); border-radius:20px; padding:5px 14px; font-size:12px; font-weight:700; color:#fca5a5; margin-bottom:24px; }
.file-error-btn { background:linear-gradient(135deg,#ef4444,#dc2626); color:#fff; border:none; padding:12px 44px; border-radius:40px; font-size:14px; font-weight:700; cursor:pointer; box-shadow:0 4px 20px rgba(239,68,68,.35); transition:all .2s; font-family:'DM Sans',sans-serif; }
.file-error-btn:hover { transform:translateY(-2px); box-shadow:0 8px 28px rgba(239,68,68,.45); }

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

  .btn-close-sidebar { display: flex; }
  .btn-hamburger     { display: flex; }

  .main-content { margin-left: 0; }

  .page-content { padding: 16px; }

  .topbar { padding: 0 16px; }
  .topbar-title { font-size: 14px; }
  .topbar-sub { display: none; }
  .topbar-actions-wrapper { display: none; }

  /* Tabel scroll di mobile */
  .table-wrap { -webkit-overflow-scrolling: touch; }

  /* Stats grid 2 kolom di mobile */
  .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }

  /* Modal full width */
  .modal-box { width: 95%; max-width: 95%; }

  /* File error box */
  .file-error-box { padding: 32px 24px; }
}

@media (max-width: 480px) {
  .stats-grid { grid-template-columns: 1fr; }
  .page-content { padding: 12px; }
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
        <h2>E-SAKIPKU</h2>
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
      <div class="sidebar-label">Manajemen</div>

      <a href="{{ route('admin.pengguna') }}" class="nav-item {{ request()->routeIs('admin.pengguna') ? 'active' : '' }}">
        <span class="nav-icon">👥</span>
        <span class="nav-label">Pengguna</span>
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
        <span class="nav-label">Klaster OPD</span>
      </a>

      <a href="{{ route('admin.arsip') }}" class="nav-item {{ request()->routeIs('admin.arsip*') ? 'active' : '' }}">
        <span class="nav-icon">🗂️</span>
        <span class="nav-label">Arsip Dokumen</span>
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

      <div class="nav-divider"></div>
      <div class="sidebar-label">Klaster Evaluasi</div>

      <a class="nav-item nav-group-toggle" onclick="toggleNav('grpKlaster', this)">
        <span class="nav-icon">🏆</span>
        <span class="nav-label">Klaster Evaluasi</span>
        <span class="nav-arrow">▾</span>
      </a>
      <div class="nav-group-body {{ request()->is('klaster/*') ? 'open' : '' }}" id="grpKlaster">
        <a href="{{ route('klaster.hasil.lke.gabungan') }}" class="nav-sub {{ request()->routeIs('klaster.hasil.lke.gabungan') ? 'active' : '' }}">Hasil LKE Gabungan</a>

        @php
          $kt = session('user.klaster_type');
          $kl = session('user.klaster_level');
          $klasterRoutes = [
            'utama'     => ['klaster.utama.1','klaster.utama.2','klaster.utama.3'],
            'pendukung' => ['klaster.pendukung.1','klaster.pendukung.2','klaster.pendukung.3'],
            'tambahan'  => ['klaster.tambahan.1','klaster.tambahan.2','klaster.tambahan.3'],
          ];
        @endphp

        @if($kt && $kl)
          {{-- Operator hanya lihat klaster miliknya --}}
          <a href="{{ route('klaster.'.$kt.'.'.$kl) }}"
             class="nav-sub {{ request()->routeIs('klaster.'.$kt.'.'.$kl) ? 'active' : '' }}">
             {{ ucfirst($kt) }} {{ ['1'=>'I','2'=>'II','3'=>'III'][$kl] ?? $kl }}
             <span style="margin-left:auto;font-size:9px;background:rgba(212,152,46,.2);color:#f0b84a;padding:1px 6px;border-radius:10px;">Saya</span>
          </a>
        @endif
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

    {{-- ════ FOOTER USER CARD ════ --}}
    <div class="sidebar-footer">
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
          @if($userRole === 'operator' && session('user.klaster_type'))
            @php
              $kt = session('user.klaster_type');
              $kl = session('user.klaster_level');
              $lvMap = ['1'=>'I','2'=>'II','3'=>'III'];
            @endphp
            <div class="klaster-badge klaster-{{ $kt }}">
              {{ $kt === 'utama' ? '🏆' : ($kt === 'pendukung' ? '🛡️' : '➕') }}
              {{ ucfirst($kt) }} {{ $lvMap[$kl] ?? '' }}
            </div>
          @endif
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
                <span style="font-size:13px;font-weight:700;color:#fff;">Notifikasi</span>
                <span id="notifCount" style="display:none;font-size:10px;background:rgba(99,102,241,.2);color:#a5b4fc;padding:2px 8px;border-radius:20px;font-weight:600;"></span>
              </div>
              <button onclick="bacaSemua()" style="font-size:11px;color:#6366f1;background:none;border:none;cursor:pointer;font-weight:600;padding:4px 8px;border-radius:6px;transition:background .15s;" onmouseover="this.style.background='rgba(99,102,241,.1)'" onmouseout="this.style.background='none'">
                Baca semua
              </button>
            </div>
            <div class="notif-list" id="notifList">
              <div style="padding:24px;text-align:center;color:#52525b;font-size:12px;">Memuat...</div>
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
    el.innerHTML = '<div style="padding:28px;text-align:center;color:#52525b;font-size:12px;">Belum ada notifikasi</div>';
    return;
  }
  el.innerHTML = list.map(function(n) {
    var w = WARNA_MAP[n.warna] || WARNA_MAP.indigo;
    var bgRow = n.sudah_dibaca ? 'transparent' : 'rgba(99,102,241,.04)';
    return `<div onclick="klikNotif(${n.id},'${n.url}')"
      style="display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-bottom:1px solid rgba(255,255,255,.04);cursor:pointer;background:${bgRow};transition:background .15s;"
      onmouseover="this.style.background='rgba(255,255,255,.03)'"
      onmouseout="this.style.background='${bgRow}'">
      <div style="width:36px;height:36px;border-radius:10px;flex-shrink:0;background:${w.bg};border:1px solid ${w.border};display:flex;align-items:center;justify-content:center;font-size:16px;">${n.ikon}</div>
      <div style="flex:1;min-width:0;">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:3px;">
          <span style="font-size:12px;font-weight:600;color:#e4e4e7;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${n.judul}</span>
          ${!n.sudah_dibaca ? `<span style="width:7px;height:7px;border-radius:50%;background:${w.dot};flex-shrink:0;"></span>` : ''}
        </div>
        <p style="font-size:11.5px;color:#a1a1aa;line-height:1.45;margin:0 0 4px;">${n.pesan}</p>
        <span style="font-size:10.5px;color:#52525b;">${n.waktu}</span>
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

/* Inisialisasi */
muatNotifikasi();
updateChatBadge();
setInterval(muatNotifikasi, 120000);
setInterval(updateChatBadge, 60000);
</script>

@yield('scripts')
</body>
</html>