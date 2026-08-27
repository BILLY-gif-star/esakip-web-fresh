@extends('layouts.app')
@section('title', 'Minimal Pengukuran Kinerja Periodik')
@section('page-title', 'Evaluasi Kinerja')
@section('page-sub', 'Minimal Pengukuran Kinerja Periodik')

@section('topbar-actions')
    @if(session('user.role') === 'operator')
    <div style="display:flex;gap:10px;">
        <button onclick="window.print();" class="btn-print-glass">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9V3h12v6M6 21H4a2 2 0 0 1-2-2v-6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-2"/>
                <path d="M6 15h12v6H6z"/>
            </svg>
            Cetak / PDF
        </button>
    </div>
    @endif
@endsection

@section('content')
<style>
/* ═══════════════════════════════════════════════════════
   DARK GLASS THEME — konsisten dengan SIAP-REAKSI RB
═══════════════════════════════════════════════════════ */
*{box-sizing:border-box;}

/* ─── TOOLBAR (dulu Excel-white, sekarang glass bar) ─── */
.xls-toolbar{
    background:rgba(255,255,255,.04);
    border:1px solid rgba(255,255,255,.06);
    border-radius:10px;
    padding:14px 18px;margin-bottom:16px;
    display:flex;align-items:center;gap:8px;flex-wrap:wrap;
}
.xls-toolbar-label{font-size:12px;font-weight:600;color:rgba(255,255,255,.6);white-space:nowrap;}
.xls-toolbar select{
    height:34px;padding:0 10px;border-radius:6px;font-size:12px;
    background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);
    color:#fff;outline:none;transition:border-color .2s;cursor:pointer;
}
.xls-toolbar select:hover{border-color:rgba(69,198,122,.4);}
.xls-toolbar select:focus{border-color:#45c67a;}
.xls-toolbar select option{background:#1a1a2e;color:#fff;}
.xls-toolbar-sep{width:1px;height:22px;background:rgba(255,255,255,.1);margin:0 4px;}

.btn-tampilkan{
    height:34px;padding:0 18px;border:none;border-radius:6px;
    background:rgba(69,198,122,.25);color:#fff;font-size:12px;font-weight:600;
    cursor:pointer;transition:all .2s;
}
.btn-tampilkan:hover{background:rgba(69,198,122,.4);}

.btn-print-glass{
    height:34px;padding:0 16px;border-radius:6px;
    background:rgba(255,255,255,.05);color:rgba(255,255,255,.75);
    border:1px solid rgba(255,255,255,.1);font-size:12px;cursor:pointer;
    display:inline-flex;align-items:center;gap:6px;transition:all .2s;
}
.btn-print-glass:hover{background:rgba(255,255,255,.1);color:#fff;}

.btn-add-main{
    height:34px;padding:0 18px;border:none;border-radius:6px;
    background:rgba(69,198,122,.25);color:#fff;font-size:12px;font-weight:600;
    cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:all .2s;
}
.btn-add-main:hover{background:rgba(69,198,122,.4);transform:translateY(-1px);}

/* ─── SHEET WRAPPER (dulu putih, sekarang glass card) ── */
.sheet-wrap{
    background:rgba(255,255,255,.02);
    border:1px solid rgba(255,255,255,.06);
    border-radius:10px;overflow:hidden;margin-bottom:14px;
}
.sheet-header{
    background:rgba(69,198,122,.1);
    border-bottom:1px solid rgba(69,198,122,.2);
    padding:10px 16px;display:flex;align-items:center;justify-content:space-between;
}
.sheet-title-bar{color:#fff;font-size:12px;font-weight:600;display:flex;align-items:center;gap:8px;}
.sheet-tabs{display:flex;gap:4px;}
.sheet-tab{
    padding:5px 14px;background:rgba(255,255,255,.06);color:rgba(255,255,255,.6);
    font-size:11px;border-radius:6px;cursor:pointer;transition:all .2s;
}
.sheet-tab.active{background:rgba(69,198,122,.25);color:#fff;font-weight:600;}

/* ─── TABLE ──────────────────────────────────────────── */
.xls-scroll{
    overflow:visible!important;
    max-height:none!important;
    zoom: 0.55;
}

.xls-table{
    border-collapse:collapse;font-size:12px;width:100%;
    min-width:2400px;table-layout:auto;
}
.xls-table th,.xls-table td{
    border:1px solid rgba(255,255,255,.06);padding:0;vertical-align:middle;
}

.row-num{
    width:36px;min-width:36px;text-align:center;font-size:10px;
    color:rgba(255,255,255,.35);padding:3px 0;
    background:rgba(255,255,255,.03);
    border-right:1px solid rgba(255,255,255,.08);
    font-family:monospace;font-weight:500;position:sticky;left:0;z-index:5;
}

.xls-th{
    background:rgba(69,198,122,.12);color:rgba(255,255,255,.85);
    text-align:center;font-size:10.5px;font-weight:600;
    text-transform:uppercase;letter-spacing:.4px;
    padding:8px 6px;white-space:nowrap;
    position:sticky;top:0;z-index:10;
    border-bottom:2px solid rgba(69,198,122,.25);
}
.xls-th.sub{
    background:rgba(69,198,122,.07);font-size:10px;font-weight:500;
    text-transform:none;letter-spacing:0;padding:6px 4px;
    position:sticky;top:34px;z-index:10;
}

/* Kategori kolom — hanya tint tipis, bukan blok warna solid */
.xls-th.sec-target,.xls-th.sec-program{background:rgba(59,130,246,.14);}
.xls-th.sub.sec-target,.xls-th.sub.sec-program{background:rgba(59,130,246,.08);}
.xls-th.sec-anggaran{background:rgba(217,164,65,.16);}
.xls-th.sub.sec-anggaran{background:rgba(217,164,65,.09);}
.xls-th.sec-cap-k,.xls-th.sec-cap-p{background:rgba(69,198,122,.16);}
.xls-th.sub.sec-cap-k,.xls-th.sub.sec-cap-p{background:rgba(69,198,122,.09);}
.xls-th.sec-cap-a{background:rgba(217,164,65,.16);}
.xls-th.sub.sec-cap-a{background:rgba(217,164,65,.09);}

.xls-th.row-num{
    background:rgba(69,198,122,.12);color:rgba(255,255,255,.4);font-size:9px;
    border:1px solid rgba(69,198,122,.15);position:sticky;top:0;left:0;z-index:20;
}
.xls-th.sub.row-num{background:rgba(69,198,122,.07);position:sticky;top:34px;left:0;z-index:20;}

.xls-td{
    padding:7px 8px;background:transparent;font-size:12px;color:rgba(255,255,255,.85);
    text-align:center;white-space:normal;word-break:break-word;
}
.xls-td.center{text-align:center;}
.xls-td.left{text-align:left;}
.xls-td.nowrap{white-space:nowrap;}

tbody tr:hover .xls-td{background:rgba(255,255,255,.03);}

/* ── sasaran cell (dulu biru solid mode-terang) ── */
.sasaran-cell{
    background:rgba(59,130,246,.08);
    border-left:3px solid #3b82f6 !important;
    border-top:2px solid rgba(59,130,246,.25) !important;
    vertical-align:top !important;padding:10px 10px 8px !important;min-width:175px;
}
.sasaran-cell-content{display:flex;flex-direction:column;gap:6px;}
.sasaran-cell-num{
    font-size:10px;font-weight:800;color:#fff;background:#3b82f6;
    border-radius:20px;padding:1px 8px;display:inline-block;align-self:flex-start;
}
.sasaran-cell-text{font-size:12px;font-weight:700;color:#93c5fd;line-height:1.45;white-space:normal;word-break:break-word;}
.sasaran-cell-actions{display:flex;gap:3px;flex-wrap:wrap;margin-top:2px;}

.row-group-start td{border-top:2px solid rgba(59,130,246,.3) !important;}

/* ── input di dalam sel ── */
.xls-input{
    width:100%;border:none;min-width:55px;padding:6px 5px;text-align:center;
    font-size:12px;font-family:inherit;background:transparent;color:#fff;outline:none;
    white-space:normal;word-break:break-word;resize:none;line-height:1.4;
    transition:background .15s;
}
.xls-input:focus{background:rgba(69,198,122,.12);box-shadow:inset 0 0 0 2px rgba(69,198,122,.5);}
.xls-input.readonly{background:rgba(255,255,255,.02);color:rgba(255,255,255,.4);cursor:default;}
.xls-input.anggaran-fmt{font-size:11px;}
.xls-input::placeholder{color:rgba(255,255,255,.25);}

/* ── action buttons ── */
.aksi-btn{
    width:26px;height:26px;border:none;border-radius:6px;cursor:pointer;font-size:13px;
    display:inline-flex;align-items:center;justify-content:center;transition:all .15s;flex-shrink:0;
}
.aksi-btn.edit{background:rgba(59,130,246,.2);color:#93c5fd;}
.aksi-btn.edit:hover{background:rgba(59,130,246,.35);transform:scale(1.05);}
.aksi-btn.del{background:rgba(226,75,74,.2);color:#f3a5a4;}
.aksi-btn.del:hover{background:rgba(226,75,74,.35);transform:scale(1.05);}
.aksi-btn.add{background:rgba(69,198,122,.2);color:#7fe3a8;}
.aksi-btn.add:hover{background:rgba(69,198,122,.35);transform:scale(1.05);}

.aksi-sm{width:22px;height:22px;border:none;border-radius:5px;cursor:pointer;font-size:11px;display:inline-flex;align-items:center;justify-content:center;transition:all .15s;}
.aksi-sm.edit{background:rgba(59,130,246,.2);color:#93c5fd;}
.aksi-sm.edit:hover{background:rgba(59,130,246,.35);}
.aksi-sm.del{background:rgba(226,75,74,.2);color:#f3a5a4;}
.aksi-sm.del:hover{background:rgba(226,75,74,.35);}
.aksi-sm.add{background:rgba(69,198,122,.2);color:#7fe3a8;}
.aksi-sm.add:hover{background:rgba(69,198,122,.35);}

/* ── statusbar ── */
.xls-statusbar{
    background:rgba(69,198,122,.1);border-top:1px solid rgba(69,198,122,.2);
    color:rgba(255,255,255,.75);padding:8px 16px;font-size:11px;
    display:flex;gap:20px;align-items:center;
}
.xls-statusbar span{opacity:.85;display:flex;align-items:center;gap:5px;}

/* ─── DROPDOWN CONTEXT MENU ───────────────────────── */
.ctx-menu{
    position:fixed;z-index:9999;background:#16161f;
    border:1px solid rgba(255,255,255,.1);border-radius:8px;
    box-shadow:0 12px 32px rgba(0,0,0,.5);min-width:200px;overflow:hidden;
    animation:ctxFade .12s ease;
}
@keyframes ctxFade{from{opacity:0;transform:scale(.95)}to{opacity:1;transform:scale(1)}}
.ctx-item{
    display:flex;align-items:center;gap:10px;padding:9px 14px;font-size:12px;font-weight:500;
    color:rgba(255,255,255,.85);cursor:pointer;transition:background .1s;
    border:none;background:none;width:100%;text-align:left;
}
.ctx-item:hover{background:rgba(255,255,255,.06);}
.ctx-item.danger{color:#f3a5a4;}
.ctx-item.danger:hover{background:rgba(226,75,74,.1);}
.ctx-item.success{color:#7fe3a8;}
.ctx-item.success:hover{background:rgba(69,198,122,.1);}
.ctx-divider{height:1px;background:rgba(255,255,255,.08);margin:2px 0;}
.ctx-label{padding:5px 14px 3px;font-size:10px;font-weight:700;color:rgba(255,255,255,.35);text-transform:uppercase;letter-spacing:.05em;}

/* ─── FORM PANEL ──────────────────────────────────── */
.form-panel{
    display:none;background:rgba(255,255,255,.03);
    border:1px solid rgba(255,255,255,.08);border-radius:10px;
    margin-top:12px;overflow:hidden;animation:slideDown .2s ease;
}
.form-panel.open{display:block;}
@keyframes slideDown{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}
.form-panel-header{
    background:rgba(69,198,122,.15);color:#fff;padding:12px 18px;
    display:flex;align-items:center;justify-content:space-between;
}
.form-panel-title{font-size:13px;font-weight:600;display:flex;align-items:center;gap:8px;}
.form-panel-close{
    background:rgba(255,255,255,.1);border:none;color:#fff;width:26px;height:26px;
    border-radius:6px;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;
}
.form-panel-close:hover{background:rgba(255,255,255,.2);}
.form-ribbon{
    background:rgba(255,255,255,.02);border-bottom:1px solid rgba(255,255,255,.06);
    padding:9px 18px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;
}
.ribbon-step{display:flex;align-items:center;gap:6px;font-size:11px;color:rgba(255,255,255,.35);}
.ribbon-step.done{color:#7fe3a8;}
.step-num{
    width:20px;height:20px;border-radius:50%;background:rgba(255,255,255,.08);
    color:rgba(255,255,255,.5);display:flex;align-items:center;justify-content:center;
    font-size:10px;font-weight:700;flex-shrink:0;
}
.ribbon-step.done .step-num{background:rgba(69,198,122,.35);color:#fff;}
.ribbon-arrow{font-size:11px;color:rgba(255,255,255,.2);}
.form-body{padding:18px;}
.info-tip{
    background:rgba(217,164,65,.1);border:1px solid rgba(217,164,65,.3);
    border-radius:6px;padding:9px 13px;font-size:11px;color:#e8c476;
    display:flex;align-items:flex-start;gap:8px;margin-bottom:14px;line-height:1.5;
}
.form-section{border:1px solid rgba(255,255,255,.08);border-radius:8px;margin-bottom:14px;overflow:hidden;}
.form-section-head{
    background:rgba(255,255,255,.04);border-bottom:1px solid rgba(255,255,255,.08);
    padding:7px 13px;font-size:11px;font-weight:700;color:rgba(255,255,255,.7);
    display:flex;align-items:center;gap:6px;
}
.form-section-body{padding:13px;}
.form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.form-grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;}
.form-lbl{font-size:11px;font-weight:600;color:rgba(255,255,255,.5);margin-bottom:4px;display:block;}
.form-inp{
    width:100%;height:32px;border-radius:6px;padding:0 10px;font-size:12px;
    background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
    color:#fff;outline:none;font-family:inherit;transition:border-color .2s;
}
.form-inp:focus{border-color:#45c67a;background:rgba(255,255,255,.09);}
.form-inp::placeholder{color:rgba(255,255,255,.3);}
.form-ta{
    width:100%;border-radius:6px;padding:7px 10px;font-size:12px;
    background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
    color:#fff;outline:none;resize:vertical;min-height:54px;font-family:inherit;transition:border-color .2s;
}
.form-ta:focus{border-color:#45c67a;background:rgba(255,255,255,.09);}
.form-ta::placeholder{color:rgba(255,255,255,.3);}
.form-file{
    width:100%;border-radius:6px;padding:6px 10px;font-size:11px;
    background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.6);outline:none;
}

.tw-section{margin-bottom:10px;}
.tw-section-label{font-size:11px;font-weight:600;color:rgba(255,255,255,.6);margin-bottom:5px;display:flex;align-items:center;gap:6px;}
.tw-dot{width:8px;height:8px;border-radius:50%;display:inline-block;flex-shrink:0;}
.tw-card{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);border-radius:6px;overflow:hidden;}
.tw-card-head{background:rgba(255,255,255,.04);text-align:center;font-size:10px;font-weight:700;color:rgba(255,255,255,.5);padding:3px 0;border-bottom:1px solid rgba(255,255,255,.08);}
.tw-card-body{padding:4px;}
.tw-card-inp{width:100%;border:none;text-align:center;font-size:12px;padding:4px;outline:none;background:transparent;color:#fff;font-family:inherit;}
.tw-card-inp:focus{background:rgba(69,198,122,.12);}
.tw-card-inp::placeholder{color:rgba(255,255,255,.25);}

.indikator-block{background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.08);border-radius:8px;margin-bottom:12px;overflow:hidden;animation:slideDown .2s ease;}
.indikator-block-head{background:rgba(99,102,241,.15);border-bottom:1px solid rgba(99,102,241,.25);padding:7px 13px;display:flex;align-items:center;justify-content:space-between;}
.indikator-block-title{font-size:11px;font-weight:700;color:#a5b4fc;}
.btn-hapus-block{background:rgba(226,75,74,.2);border:none;width:22px;height:22px;border-radius:5px;cursor:pointer;color:#f3a5a4;font-size:13px;line-height:1;}
.btn-hapus-block:hover{background:rgba(226,75,74,.35);}
.indikator-block-body{padding:13px;}
.tw-block{background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:6px;padding:10px;margin-top:6px;}
.btn-tambah-ind{
    height:32px;padding:0 15px;background:rgba(99,102,241,.12);border:1.5px dashed rgba(99,102,241,.5);
    border-radius:6px;font-size:11px;font-weight:600;color:#a5b4fc;cursor:pointer;
    display:inline-flex;align-items:center;gap:5px;transition:background .2s;
}
.btn-tambah-ind:hover{background:rgba(99,102,241,.2);}
.form-footer{padding:11px 18px;border-top:1px solid rgba(255,255,255,.08);background:rgba(255,255,255,.02);display:flex;gap:8px;justify-content:flex-end;align-items:center;}
.btn-batal{height:34px;padding:0 20px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:6px;font-size:12px;font-weight:500;cursor:pointer;color:rgba(255,255,255,.7);}
.btn-batal:hover{background:rgba(255,255,255,.1);color:#fff;}
.btn-simpan{height:34px;padding:0 24px;background:rgba(69,198,122,.25);color:#fff;border:1px solid #45c67a;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:all .2s;}
.btn-simpan:hover{background:rgba(69,198,122,.4);}

/* ─── MODAL ──────────────────────────────────────── */
.modal-backdrop{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.65);backdrop-filter:blur(4px);z-index:1000;justify-content:center;align-items:center;}
.modal-box{background:#16161f;border:1px solid rgba(255,255,255,.1);border-radius:10px;width:520px;max-width:92%;max-height:90vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.6);animation:modalIn .2s ease;}
.modal-box.lg{width:700px;}
@keyframes modalIn{from{opacity:0;transform:scale(.96)}to{opacity:1;transform:scale(1)}}
.modal-head{background:rgba(69,198,122,.15);border-bottom:1px solid rgba(69,198,122,.25);color:#fff;padding:12px 18px;display:flex;align-items:center;justify-content:space-between;border-radius:10px 10px 0 0;}
.modal-head.green{background:rgba(69,198,122,.2);border-bottom-color:rgba(69,198,122,.35);}
.modal-head.amber{background:rgba(217,164,65,.18);border-bottom-color:rgba(217,164,65,.3);}
.modal-head-title{font-size:13px;font-weight:600;}
.modal-close-btn{background:rgba(255,255,255,.1);border:none;color:#fff;width:26px;height:26px;border-radius:6px;cursor:pointer;font-size:18px;line-height:1;display:flex;align-items:center;justify-content:center;}
.modal-close-btn:hover{background:rgba(255,255,255,.2);}
.modal-body{padding:18px;}
.modal-foot{padding:11px 18px;border-top:1px solid rgba(255,255,255,.08);background:rgba(255,255,255,.02);display:flex;gap:8px;justify-content:flex-end;border-radius:0 0 10px 10px;}

/* save status */
#saveStatusBar{position:fixed;bottom:20px;right:20px;z-index:9998;background:#16161f;border:1px solid rgba(255,255,255,.12);color:#fff;border-radius:10px;padding:10px 16px;font-size:12px;font-weight:600;box-shadow:0 8px 28px rgba(0,0,0,.4);display:none;align-items:center;gap:8px;}

/* alert & empty */
.alert{border-radius:8px;padding:11px 16px;font-size:13px;margin-bottom:14px;border:1px solid;}
.alert-success{background:rgba(69,198,122,.1);border-color:rgba(69,198,122,.3);color:#7fe3a8;}
.alert-danger{background:rgba(226,75,74,.1);border-color:rgba(226,75,74,.3);color:#f3a5a4;}
.empty-state{text-align:center;padding:70px 20px;}
.empty-icon{font-size:52px;margin-bottom:14px;opacity:.5;}
.empty-title{font-size:17px;font-weight:700;color:#fff;margin-bottom:6px;}
.empty-sub{font-size:13px;color:rgba(255,255,255,.5);line-height:1.6;}

/* highlight badge sasaran */
.sasaran-info-badge{
    background:rgba(69,198,122,.1);border:1px solid rgba(69,198,122,.3);border-radius:6px;
    padding:8px 12px;margin-bottom:14px;display:flex;gap:8px;align-items:flex-start;
}

/* Print — tetap terang & rapi, dark theme tidak dipaksakan ke hasil cetak */
@media print{
    @page{ size: landscape; margin: 6mm; }

    .xls-toolbar,.form-panel,#saveStatusBar,.no-print,.ctx-menu,
    .aksi-btn,.aksi-sm{display:none!important;}

    .xls-scroll{
        overflow:visible!important;
        max-height:none!important;
        transform: scale(0.6);
        transform-origin: top left;
    }
    .xls-table{
        min-width:0!important;
        width:max-content!important;
        table-layout:auto!important;
    }
    .xls-th,.row-num{ position:static!important; }
    .xls-table th,.xls-table td{ padding:3px 5px!important; font-size:9px!important; white-space:normal!important; }
    .xls-th{ font-size:8.5px!important; }
    .xls-td{ word-break:normal!important; overflow-wrap:break-word!important; }

    .xls-table th{background:#e2e8f0!important;color:#000!important;}
    .xls-table td{color:#000!important;background:#fff!important;}
    .sasaran-cell{background:#dbeafe!important;}
    .sasaran-cell-text{color:#1d4ed8!important;}
    .xls-input{color:#000!important;font-size:9px!important;}
    .sheet-header,.xls-statusbar{background:#e2e8f0!important;color:#000!important;}
}

@media (max-width: 768px) {
  .klaster-grid { grid-template-columns: 1fr; }
}
</style>

@php
    $userRole   = session('user.role');
    $isOperator = ($userRole === 'operator');
    $isAdmin    = ($userRole === 'admin');

    /* TW sections — dipakai di form & modal */
    $twSecs = [
    ['label'=>'Target Kinerja',           'dot'=>'#1f4e79','prefix'=>'target_kinerja_tw',  'fmt'=>false],
    ['label'=>'Target Program/Kegiatan',  'dot'=>'#1f4e79','prefix'=>'target_program_tw',  'fmt'=>false],
    ['label'=>'Anggaran (Rp)',            'dot'=>'#92600a','prefix'=>'anggaran_tw',         'fmt'=>true ],
    ['label'=>'Capaian Kinerja',          'dot'=>'#145a32','prefix'=>'capaian_kinerja_tw',  'fmt'=>false,'note'=>'(kosongkan jika belum ada)'],
    ['label'=>'Capaian Program/Kegiatan', 'dot'=>'#145a32','prefix'=>'capaian_program_tw',  'fmt'=>false],
    ['label'=>'Capaian Anggaran (Rp)',    'dot'=>'#92600a','prefix'=>'capaian_anggaran_tw', 'fmt'=>true ],
];
@endphp

@if(session('success'))
    <div class="alert alert-success">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">❌ {{ session('error') }}</div>
@endif

{{-- ══ TOOLBAR ══ --}}
<div class="xls-toolbar no-print">
    <form method="GET" action="{{ route('pengukuran.periodik') }}"
          style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;width:100%;">
        <span class="xls-toolbar-label">📅 Tahun:</span>
        <select name="tahun">
            @foreach($listTahun as $t)
                <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
        @if($isAdmin)
        <div class="xls-toolbar-sep"></div>
        <span class="xls-toolbar-label">🏢 OPD:</span>
        <select name="opd_id" style="min-width:220px;">
            <option value="">-- Pilih OPD --</option>
            @foreach($listOpd as $opd)
                <option value="{{ $opd->id }}" {{ $opdId == $opd->id ? 'selected' : '' }}>{{ $opd->nama }}</option>
            @endforeach
        </select>
        @endif
        <button type="submit" class="btn-tampilkan">Tampilkan</button>
        <div style="flex:1;"></div>
        <button type="button" onclick="window.print();" class="btn-print-glass">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V3h12v6M6 21H4a2 2 0 0 1-2-2v-6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-2"/><path d="M6 15h12v6H6z"/></svg>
            Cetak / PDF
        </button>
        @if($isOperator)
        <button type="button" id="btnTambah" class="btn-add-main">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Sasaran + Indikator
        </button>
        @endif
    </form>
</div>

{{-- ══ EMPTY ══ --}}
@if(!$opdId && $isAdmin)
<div class="empty-state">
    <div class="empty-icon">📋</div>
    <div class="empty-title">Pilih OPD terlebih dahulu</div>
    <div class="empty-sub">Gunakan filter di atas untuk memilih OPD yang akan dievaluasi.</div>
</div>
@elseif(!$opdId && $isOperator)
<div class="empty-state">
    <div class="empty-icon">📋</div>
    <div class="empty-title">Belum Ada Data</div>
    <div class="empty-sub">
        Anda belum mengisi data pengukuran kinerja untuk tahun {{ $tahun }}.<br>
        Klik tombol <strong>"Tambah Sasaran + Indikator"</strong> di toolbar untuk memulai.
    </div>
</div>

@else

{{-- ══ SHEET / TABLE ══ --}}
<div class="sheet-wrap">
    <div class="sheet-header">
        <div class="sheet-title-bar">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18"/></svg>
            Minimal Pengukuran Kinerja Periodik — {{ $tahun }}
        </div>
        <div class="sheet-tabs">
            <div class="sheet-tab active">Data Pengukuran</div>
            <div class="sheet-tab">Rekap</div>
        </div>
    </div>

    <div class="xls-scroll">
        <table class="xls-table">
            {{--
                TIDAK pakai <colgroup> fixed agar table-layout:auto bisa bekerja.
                Kolom akan melebar sendiri sesuai konten.
            --}}
            <thead>
                <tr>
                    <th class="xls-th row-num" rowspan="2" style="min-width:36px;">▽</th>
                    <th class="xls-th" rowspan="2" style="min-width:34px;font-size:10px;">No</th>
                    <th class="xls-th" rowspan="2" style="min-width:175px;text-align:left;padding-left:8px;background:#1a3560;">
                        Sasaran Strategis<br><span style="opacity:.7;font-weight:400;">(1)</span>
                    </th>
                    <th class="xls-th" rowspan="2" style="min-width:30px;font-size:10px;">#</th>
                    <th class="xls-th" rowspan="2" style="min-width:160px;text-align:left;padding-left:8px;">
                        Indikator Kinerja<br><span style="opacity:.7;font-weight:400;">(2)</span>
                    </th>
                    <th colspan="4" class="xls-th sec-target">Target Kinerja <span style="opacity:.7;font-weight:400;">(3)</span></th>
                    <th class="xls-th" rowspan="2" style="min-width:130px;text-align:left;padding-left:6px;font-size:10px;">
                        Sasaran Program/Kegiatan<br><span style="opacity:.7;font-weight:400;">(4)</span>
                    </th>
                    <th colspan="4" class="xls-th sec-program">Target Program/Kegiatan <span style="opacity:.7;font-weight:400;">(5)</span></th>
                    <th class="xls-th" rowspan="2" style="min-width:100px;font-size:10px;">
                        Penanggung Jawab<br><span style="opacity:.7;font-weight:400;">(6)</span>
                    </th>
                    <th colspan="4" class="xls-th sec-anggaran">Anggaran <span style="opacity:.7;font-weight:400;">(7)</span></th>
                    <th colspan="4" class="xls-th sec-cap-k">Capaian Kinerja <span style="opacity:.7;font-weight:400;">(8)</span></th>
                    <th colspan="4" class="xls-th sec-cap-p">Capaian Program/Kegiatan <span style="opacity:.7;font-weight:400;">(9)</span></th>
                    <th colspan="4" class="xls-th sec-cap-a">Capaian Anggaran <span style="opacity:.7;font-weight:400;">(10)</span></th>
                    <th class="xls-th" rowspan="2" style="min-width:72px;font-size:10px;">Aksi</th>
                </tr>
               @php
                    $twGroups = ['sec-target','sec-program','sec-anggaran','sec-cap-k','sec-cap-p','sec-cap-a'];
                    $twWide   = ['sec-anggaran','sec-cap-a']; // grup yang butuh kolom lebih lebar (angka rupiah)
                @endphp
                <tr>
                    @foreach($twGroups as $secClass)
                        @foreach(['TW1','TW2','TW3','TW4'] as $twLabel)
                        <th class="xls-th sub {{ $secClass }}" style="min-width:{{ in_array($secClass, $twWide) ? '90px' : '60px' }};">{{ $twLabel }}</th>
                        @endforeach
                    @endforeach
                </tr>
            </thead>

            <tbody>
            @php $no = 1; $rowNum = 3; @endphp

            @foreach($data as $sasaran => $indikators)
            @php $jumlahInd = $indikators->count(); @endphp

            @foreach($indikators as $idx => $item)
            <tr class="{{ $idx === 0 ? 'row-group-start' : '' }}">

                {{-- ── ROW NUM + SASARAN (rowspan, hanya baris pertama) ── --}}
                @if($idx === 0)
                <td class="row-num" rowspan="{{ $jumlahInd }}">{{ $rowNum }}</td>
                <td class="xls-td center" rowspan="{{ $jumlahInd }}"
                    style="font-weight:800;color:#1d4ed8;font-size:13px;vertical-align:middle;">
                    {{ $no }}
                </td>

                {{-- SASARAN CELL --}}
                <td class="sasaran-cell" rowspan="{{ $jumlahInd }}">
                    <div class="sasaran-cell-content">
                        <span class="sasaran-cell-num">{{ $jumlahInd }} indikator</span>
                        <div class="sasaran-cell-text">{{ $sasaran ?: 'Tanpa Sasaran Strategis' }}</div>
                        @if($isOperator)
                        <div class="sasaran-cell-actions">
                            <button type="button" class="aksi-sm edit"
                                onclick="showCtxSasaran(event,'{{ addslashes($sasaran) }}',{{ $tahun }},{{ $opdId }})"
                                title="Menu sasaran">
                                ⋯
                            </button>
                        </div>
                        @endif
                    </div>
                </td>
                @endif

                {{-- NO INDIKATOR --}}
                <td class="xls-td center" style="color:#94a3b8;font-size:11px;">{{ $idx + 1 }}</td>

                {{-- NAMA INDIKATOR --}}
                <td class="xls-td left" style="font-weight:600;min-width:160px;">{{ $item->indikator ?? '-' }}</td>

               {{-- TARGET KINERJA TW 1-4 --}}
                @include('partials.tw-input-cells', ['prefix' => 'target_kinerja_tw', 'fmt' => false])

                {{-- SASARAN PROGRAM --}}
                <td class="xls-td left" style="font-size:11px;color:#475569;min-width:130px;">
                    {{ $item->sasaran_program ?? '-' }}
                </td>

                {{-- TARGET PROGRAM TW1-4 --}}
                @include('partials.tw-input-cells', ['prefix' => 'target_program_tw', 'fmt' => false])

                {{-- PENANGGUNG JAWAB --}}
                <td class="xls-td left" style="font-size:11px;color:#475569;min-width:100px;">
                    {{ $item->penanggung_jawab ?? '-' }}
                </td>

                {{-- ANGGARAN TW1-4 --}}
                @include('partials.tw-input-cells', ['prefix' => 'anggaran_tw', 'fmt' => true])

               {{-- CAPAAIAN KINERJA TW1-4 --}}
                @include('partials.tw-input-cells', ['prefix' => 'capaian_kinerja_tw', 'fmt' => false])

                {{-- CAPAIAN PROGRAM TW1-4 --}}
               @include('partials.tw-input-cells', ['prefix' => 'capaian_program_tw', 'fmt' => false])

                {{-- CAPAIAN ANGGARAN TW1-4 --}}
               @include('partials.tw-input-cells', ['prefix' => 'capaian_anggaran_tw', 'fmt' => true])

                {{-- AKSI INDIKATOR --}}
                <td class="xls-td nowrap" style="padding:4px 3px;">
                    @if($isOperator)
                        <button type="button" class="aksi-btn edit"
                            onclick="showCtxIndikator(event,{{ $item->id }},'{{ addslashes($item->indikator) }}')"
                            title="Menu indikator">
                            ⋯
                        </button>
                    @else
                        <span style="color:#94a3b8;">—</span>
                    @endif
                </td>
            </tr>
            @endforeach

            @php $rowNum += $jumlahInd; $no++; @endphp
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="xls-statusbar">
        <span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
            {{ $data->sum(fn($v) => $v->count()) }} indikator
        </span>
        <span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>
            {{ $data->count() }} sasaran strategis
        </span>
        @if($isOperator)
        <span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
            Auto-save aktif
        </span>
        @endif
        <div style="flex:1;"></div>
        <span id="cellRefDisplay" style="font-family:monospace;font-size:10px;opacity:.6;">Klik sel untuk mulai edit</span>
    </div>
</div>

{{-- ════════════════════════════════════════════════════
     DROPDOWN CONTEXT MENU — SASARAN
════════════════════════════════════════════════════ --}}
<div id="ctxSasaran" class="ctx-menu" style="display:none;">
    <div class="ctx-label">Sasaran Strategis</div>
    <button class="ctx-item" onclick="bukaModalEditSasaran()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Edit Sasaran Strategis
    </button>
    <div class="ctx-divider"></div>
    <div class="ctx-label">Indikator</div>
    <button class="ctx-item success" onclick="bukaModalTambahIndikator()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        Tambah Indikator ke Sasaran Ini
    </button>
    <div class="ctx-divider"></div>
    <button class="ctx-item danger" onclick="eksekusiHapusSasaran()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
        Hapus Sasaran &amp; Semua Indikator
    </button>
</div>

{{-- ════════════════════════════════════════════════════
     DROPDOWN CONTEXT MENU — INDIKATOR
════════════════════════════════════════════════════ --}}
<div id="ctxIndikator" class="ctx-menu" style="display:none;">
    <div class="ctx-label">Indikator Kinerja</div>
    <button class="ctx-item" onclick="bukaModalEditIndikator()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Edit Indikator Ini
    </button>
    <div class="ctx-divider"></div>
    <button class="ctx-item danger" onclick="eksekusiHapusIndikator()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
        Hapus Indikator Ini
    </button>
</div>

{{-- ════════════════════════════════════════════════════
     MODAL 1 — EDIT INDIKATOR
════════════════════════════════════════════════════ --}}
<div id="modalEditIndikator" class="modal-backdrop" style="display:none;">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-head-title">✏️ Edit Indikator Kinerja</div>
            <button class="modal-close-btn" onclick="tutupModal('modalEditIndikator')">×</button>
        </div>
        <div class="modal-body">
            <form id="formEditIndikator" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_id" name="id">
                <div style="margin-bottom:12px;">
                    <label class="form-lbl">Indikator Kinerja <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="edit_indikator" name="indikator" class="form-inp" required placeholder="Nama indikator kinerja">
                </div>
                <div class="form-grid-2" style="margin-bottom:12px;">
                    <div>
                        <label class="form-lbl">Satuan</label>
                        <input type="text" id="edit_satuan" name="satuan" class="form-inp" placeholder="%, Poin, Orang, dll">
                    </div>
                    <div>
                        <label class="form-lbl">Penanggung Jawab</label>
                        <input type="text" id="edit_penanggung_jawab" name="penanggung_jawab" class="form-inp" placeholder="Nama jabatan">
                    </div>
                </div>
                <div>
                    <label class="form-lbl">Sasaran Program / Kegiatan</label>
                    <textarea id="edit_sasaran_program" name="sasaran_program" class="form-ta" rows="3"></textarea>
                </div>
                <div class="modal-foot" style="margin:14px -18px -18px;">
                    <button type="button" class="btn-batal" onclick="tutupModal('modalEditIndikator')">Batal</button>
                    <button type="submit" class="btn-simpan">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════
     MODAL 2 — EDIT SASARAN
════════════════════════════════════════════════════ --}}
<div id="modalEditSasaran" class="modal-backdrop" style="display:none;">
    <div class="modal-box">
        <div class="modal-head amber">
            <div class="modal-head-title">✏️ Edit Sasaran Strategis</div>
            <button class="modal-close-btn" onclick="tutupModal('modalEditSasaran')">×</button>
        </div>
        <div class="modal-body">
            <form id="formEditSasaran">
                <input type="hidden" id="es_lama">
                <input type="hidden" id="es_tahun">
                <input type="hidden" id="es_opd">
                <div class="info-tip" style="margin-bottom:12px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="2" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Perubahan sasaran akan diperbarui pada semua indikator yang tergabung.
                </div>
                <label class="form-lbl">Sasaran Strategis <span style="color:#dc2626;">*</span></label>
                <textarea id="es_baru" name="sasaran_baru" class="form-ta" rows="3" required placeholder="Rumusan sasaran strategis..."></textarea>
                <div class="modal-foot" style="margin:14px -18px -18px;">
                    <button type="button" class="btn-batal" onclick="tutupModal('modalEditSasaran')">Batal</button>
                    <button type="submit" class="btn-simpan" style="background:#92400e;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════
     MODAL 3 — TAMBAH INDIKATOR KE SASARAN YANG ADA
     TW: FIXED (tidak bertambah, 1 indikator per submit)
════════════════════════════════════════════════════ --}}
<div id="modalTambahIndikator" class="modal-backdrop" style="display:none;">
    <div class="modal-box lg">
        <div class="modal-head green">
            <div class="modal-head-title">➕ Tambah Indikator ke Sasaran</div>
            <button class="modal-close-btn" onclick="tutupModal('modalTambahIndikator')">×</button>
        </div>
        <div class="modal-body">
            <form id="formTambahIndikatorSasaran" method="POST"
                  action="{{ route('pengukuran.periodik.simpan') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tahun"  id="ti_tahun">
                <input type="hidden" name="opd_id" id="ti_opd">
                <input type="hidden" name="sasaran_strategis" id="ti_sasaran_val">

                <div class="sasaran-info-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#065f46" stroke-width="2" style="flex-shrink:0;margin-top:2px;"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>
                    <div>
                        <div style="font-size:10px;font-weight:700;color:#065f46;margin-bottom:2px;">SASARAN STRATEGIS</div>
                        <div id="ti_sasaran_display" style="font-size:12px;font-weight:600;color:#1e293b;"></div>
                    </div>
                </div>

                <div class="form-grid-2" style="margin-bottom:12px;">
                    <div>
                        <label class="form-lbl">Indikator Kinerja <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="indikator[]" class="form-inp" required placeholder="Nama indikator kinerja">
                    </div>
                    <div>
                        <label class="form-lbl">Satuan</label>
                        <input type="text" name="satuan[]" class="form-inp" placeholder="%, Poin, Orang, dll">
                    </div>
                    <div>
                        <label class="form-lbl">Sasaran Program / Kegiatan</label>
                        <textarea name="sasaran_program[]" class="form-ta" rows="2" placeholder="Program/kegiatan pendukung"></textarea>
                    </div>
                    <div>
                        <label class="form-lbl">Penanggung Jawab</label>
                        <input type="text" name="penanggung_jawab[]" class="form-inp" placeholder="Nama jabatan atau unit kerja">
                    </div>
                </div>

                <div class="tw-block">
                    <div style="font-size:11px;font-weight:700;color:#444;margin-bottom:8px;">Isian Per Triwulan (TW1 — TW4)</div>
                    @foreach($twSecs as $sec)
                    <div class="tw-section" style="{{ $loop->last ? 'margin-bottom:0;':'' }}">
                        <div class="tw-section-label">
                            <span class="tw-dot" style="background:{{ $sec['dot'] }};"></span>
                            {{ $sec['label'] }}
                            @isset($sec['note'])<span style="font-weight:400;color:#94a3b8;">{{ $sec['note'] }}</span>@endisset
                        </div>
                        <div class="form-grid-4">
                            @foreach([1,2,3,4] as $tw)
                            <div class="tw-card">
                                <div class="tw-card-head">TW {{ ['I','II','III','IV'][$tw-1] }}</div>
                                <div class="tw-card-body">
                                    <input type="text" name="{{ $sec['prefix'] }}{{ $tw }}[]"
                                           class="tw-card-inp{{ $sec['fmt'] ? ' anggaran-fmt':'' }}"
                                           placeholder="{{ $sec['fmt'] ? '0':'—' }}">
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="form-grid-2" style="margin-top:12px;">
                    <div>
                        <label class="form-lbl">Keterangan / Catatan</label>
                        <textarea name="keterangan[]" class="form-ta" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
                    </div>
                    <div>
                        <label class="form-lbl">Upload Bukti Dukung <span style="font-weight:400;color:#94a3b8;">(maks 5MB)</span></label>
                        <input type="file" name="file[]" class="form-file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                    </div>
                </div>

                <div class="modal-foot" style="margin:14px -18px -18px;">
                    <button type="button" class="btn-batal" onclick="tutupModal('modalTambahIndikator')">Batal</button>
                    <button type="submit" class="btn-simpan" style="background:#065f46;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                        Tambah Indikator
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($isOperator)
<div id="saveStatusBar"><span id="saveStatusText">⏳ Menyimpan...</span></div>
@endif

{{-- ════════════════════════════════════════════════════
     FORM PANEL — TAMBAH SASARAN + INDIKATOR BARU
════════════════════════════════════════════════════ --}}
@if($isOperator)
<div id="formTambahWrap" class="form-panel no-print">
    <div class="form-panel-header">
        <div class="form-panel-title">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
            Tambah Sasaran Strategis &amp; Indikator Baru
        </div>
        <button type="button" id="btnTutupForm" class="form-panel-close">✕</button>
    </div>
    <div class="form-ribbon">
        <div class="ribbon-step done"><div class="step-num">1</div> Sasaran Strategis</div>
        <div class="ribbon-arrow">›</div>
        <div class="ribbon-step done"><div class="step-num">2</div> Data Indikator</div>
        <div class="ribbon-arrow">›</div>
        <div class="ribbon-step done"><div class="step-num">3</div> Target &amp; Realisasi per TW</div>
        <div class="ribbon-arrow">›</div>
        <div class="ribbon-step done"><div class="step-num">4</div> Simpan</div>
    </div>
    <div class="form-body">
        <div class="info-tip">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div><strong>Cara pengisian:</strong> Isi Sasaran Strategis dahulu, lalu tambahkan indikator beserta target &amp; realisasi per triwulan.
            Untuk menambah indikator ke sasaran yang <em>sudah ada</em>, klik tombol <strong>⋯</strong> di kolom sasaran → <em>Tambah Indikator ke Sasaran Ini</em>.</div>
        </div>
        <form method="POST" action="{{ route('pengukuran.periodik.simpan') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="tahun"  value="{{ $tahun }}">
            <input type="hidden" name="opd_id" value="{{ $opdId }}">

            {{-- Langkah 1 --}}
            <div class="form-section">
                <div class="form-section-head">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
                    Langkah 1 — Sasaran Strategis <span style="color:#dc2626;margin-left:2px;">*</span>
                </div>
                <div class="form-section-body">
                    <label class="form-lbl">Rumusan sasaran strategis untuk tahun {{ $tahun }}</label>
                    <textarea name="sasaran_strategis" class="form-ta" rows="2"
                        placeholder="Contoh: Meningkatnya kualitas pelayanan publik yang responsif dan inovatif" required></textarea>
                </div>
            </div>

            {{-- Langkah 2 --}}
            <div class="form-section">
                <div class="form-section-head">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    Langkah 2 — Daftar Indikator Kinerja
                </div>
                <div class="form-section-body">
                    <div id="indikatorContainer">
                        <div class="indikator-block">
                            <div class="indikator-block-head">
                                <div class="indikator-block-title">📌 Indikator #1</div>
                            </div>
                            <div class="indikator-block-body">
                                <div class="form-grid-2" style="margin-bottom:10px;">
                                    <div>
                                        <label class="form-lbl">Indikator Kinerja <span style="color:#dc2626;">*</span></label>
                                        <input type="text" name="indikator[]" class="form-inp" required placeholder="Contoh: Persentase kepuasan masyarakat (%)">
                                    </div>
                                    <div>
                                        <label class="form-lbl">Satuan</label>
                                        <input type="text" name="satuan[]" class="form-inp" placeholder="%, Poin, Orang, dll">
                                    </div>
                                    <div>
                                        <label class="form-lbl">Sasaran Program / Kegiatan</label>
                                        <textarea name="sasaran_program[]" class="form-ta" rows="2" placeholder="Program/kegiatan pendukung"></textarea>
                                    </div>
                                    <div>
                                        <label class="form-lbl">Penanggung Jawab</label>
                                        <input type="text" name="penanggung_jawab[]" class="form-inp" placeholder="Nama jabatan atau unit kerja">
                                    </div>
                                </div>
                                <div class="tw-block">
                                    <div style="font-size:11px;font-weight:700;color:#444;margin-bottom:8px;">Isian Per Triwulan (TW1 — TW4)</div>
                                    @foreach($twSecs as $sec)
                                    <div class="tw-section" style="{{ $loop->last ? 'margin-bottom:0;':'' }}">
                                        <div class="tw-section-label">
                                            <span class="tw-dot" style="background:{{ $sec['dot'] }};"></span>
                                            {{ $sec['label'] }}
                                            @isset($sec['note'])<span style="font-weight:400;color:#94a3b8;">{{ $sec['note'] }}</span>@endisset
                                        </div>
                                        <div class="form-grid-4">
                                            @foreach([1,2,3,4] as $tw)
                                            <div class="tw-card">
                                                <div class="tw-card-head">TW {{ ['I','II','III','IV'][$tw-1] }}</div>
                                                <div class="tw-card-body">
                                                    <input type="text" name="{{ $sec['prefix'] }}{{ $tw }}[]"
                                                        class="tw-card-inp{{ $sec['fmt'] ? ' anggaran-fmt':'' }}"
                                                        placeholder="{{ $sec['fmt'] ? '0':'—' }}">
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="form-grid-2" style="margin-top:10px;">
                                    <div>
                                        <label class="form-lbl">Keterangan / Catatan</label>
                                        <textarea name="keterangan[]" class="form-ta" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
                                    </div>
                                    <div>
                                        <label class="form-lbl">Upload Bukti Dukung <span style="font-weight:400;color:#94a3b8;">(PDF/Word/Excel/Gambar, maks 5MB)</span></label>
                                        <input type="file" name="file[]" class="form-file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;margin-top:4px;">
                        <button type="button" id="btnTambahIndikator" class="btn-tambah-ind">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                            Tambah Indikator Lagi
                        </button>
                        <span style="font-size:11px;color:#94a3b8;">Total: <strong id="countIndikator">1</strong> indikator</span>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <span style="font-size:11px;color:#94a3b8;margin-right:auto;">Data disimpan setelah klik "Simpan Semua Data"</span>
                <button type="button" id="btnBatalForm" class="btn-batal">Batal</button>
                <button type="submit" class="btn-simpan">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Simpan Semua Data
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endif
@endsection

@section('scripts')
<script>
/* ══════════════════════════════════════════════════
   FORMAT RIBUAN
══════════════════════════════════════════════════ */
function applyFormatRibuan(el){
    el.addEventListener('blur',function(){
        var raw=this.value.replace(/\./g,'').replace(/\D/g,'');
        if(raw) this.value=parseInt(raw,10).toLocaleString('id-ID');
    });
}
document.querySelectorAll('.anggaran-fmt').forEach(applyFormatRibuan);

/* ══════════════════════════════════════════════════
   TUTUP MODAL
══════════════════════════════════════════════════ */
function tutupModal(id){ document.getElementById(id).style.display='none'; }
window.addEventListener('click',function(e){
    ['modalEditIndikator','modalEditSasaran','modalTambahIndikator'].forEach(function(id){
        var m=document.getElementById(id);
        if(m&&e.target===m) m.style.display='none';
    });
});

/* ══════════════════════════════════════════════════
   CONTEXT MENU — STATE
══════════════════════════════════════════════════ */
var _ctxSasaran = {nama:'',tahun:0,opdId:0};
var _ctxIndikator = {id:0,nama:''};
var _activeCtx = null;

function tutupSemuaCtx(){
    document.getElementById('ctxSasaran').style.display='none';
    document.getElementById('ctxIndikator').style.display='none';
    _activeCtx=null;
}

/* posisi menu dekat tombol */
function posisiCtx(menu, e){
    menu.style.display='block';
    var rect=e.currentTarget.getBoundingClientRect();
    var mw=menu.offsetWidth, mh=menu.offsetHeight;
    var x=rect.left+window.scrollX;
    var y=rect.bottom+window.scrollY+4;
    if(x+mw>window.innerWidth-8) x=window.innerWidth-mw-8;
    if(y+mh>window.innerHeight+window.scrollY-8) y=rect.top+window.scrollY-mh-4;
    menu.style.left=x+'px';
    menu.style.top=y+'px';
}

document.addEventListener('click',function(e){
    if(_activeCtx && !_activeCtx.contains(e.target)){
        tutupSemuaCtx();
    }
});

/* ── SASARAN CTX ── */
function showCtxSasaran(e,nama,tahun,opdId){
    e.stopPropagation();
    tutupSemuaCtx();
    _ctxSasaran={nama:nama,tahun:tahun,opdId:opdId};
    var menu=document.getElementById('ctxSasaran');
    posisiCtx(menu,e);
    _activeCtx=menu;
}
function bukaModalEditSasaran(){
    tutupSemuaCtx();
    document.getElementById('es_lama').value =_ctxSasaran.nama;
    document.getElementById('es_tahun').value=_ctxSasaran.tahun;
    document.getElementById('es_opd').value  =_ctxSasaran.opdId;
    document.getElementById('es_baru').value =_ctxSasaran.nama;
    document.getElementById('modalEditSasaran').style.display='flex';
}
function bukaModalTambahIndikator(){
    tutupSemuaCtx();
    document.getElementById('ti_sasaran_display').textContent=_ctxSasaran.nama;
    document.getElementById('ti_sasaran_val').value=_ctxSasaran.nama;
    document.getElementById('ti_tahun').value=_ctxSasaran.tahun;
    document.getElementById('ti_opd').value=_ctxSasaran.opdId;
    /* reset input */
    document.querySelectorAll('#modalTambahIndikator input[type="text"],#modalTambahIndikator textarea').forEach(function(el){el.value='';});
    document.querySelectorAll('#modalTambahIndikator input[type="file"]').forEach(function(el){el.value='';});
    document.getElementById('modalTambahIndikator').style.display='flex';
}
function eksekusiHapusSasaran(){
    tutupSemuaCtx();
    if(!confirm('Yakin ingin menghapus sasaran "'+_ctxSasaran.nama+'" beserta semua indikatornya?')) return;
    fetch('{{ route("pengukuran.periodik.delete-sasaran") }}',{
        method:'POST',
        headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},
        body:JSON.stringify({sasaran:_ctxSasaran.nama,tahun:_ctxSasaran.tahun,opd_id:_ctxSasaran.opdId})
    })
    .then(function(r){return r.json();})
    .then(function(d){if(d.success){alert(d.message);location.reload();}else alert('Gagal: '+d.message);})
    .catch(function(){alert('Terjadi kesalahan.');});
}

/* ── INDIKATOR CTX ── */
function showCtxIndikator(e,id,nama){
    e.stopPropagation();
    tutupSemuaCtx();
    _ctxIndikator={id:id,nama:nama};
    var menu=document.getElementById('ctxIndikator');
    posisiCtx(menu,e);
    _activeCtx=menu;
}
function bukaModalEditIndikator(){
    tutupSemuaCtx();
    fetch('/pengukuran/periodik/'+_ctxIndikator.id+'/data')
        .then(function(r){return r.json();})
        .then(function(d){
            document.getElementById('edit_id').value              =d.id;
            document.getElementById('edit_indikator').value       =d.indikator||'';
            document.getElementById('edit_satuan').value          =d.satuan||'';
            document.getElementById('edit_sasaran_program').value =d.sasaran_program||'';
            document.getElementById('edit_penanggung_jawab').value=d.penanggung_jawab||'';
            document.getElementById('formEditIndikator').action   ='/pengukuran/periodik/'+d.id+'/update';
            document.getElementById('modalEditIndikator').style.display='flex';
        })
        .catch(function(){alert('Gagal mengambil data indikator.');});
}
function eksekusiHapusIndikator(){
    tutupSemuaCtx();
    if(!confirm('Yakin ingin menghapus indikator "'+_ctxIndikator.nama+'"?')) return;
    fetch('/pengukuran/periodik/'+_ctxIndikator.id+'/delete',{
        method:'DELETE',
        headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}
    })
    .then(function(r){return r.json();})
    .then(function(d){if(d.success){alert(d.message);location.reload();}else alert('Gagal: '+d.message);})
    .catch(function(){alert('Terjadi kesalahan.');});
}

/* ══════════════════════════════════════════════════
   FORM EDIT INDIKATOR — submit via AJAX
══════════════════════════════════════════════════ */
(function(){
    var f=document.getElementById('formEditIndikator');
    if(!f) return;
    f.addEventListener('submit',function(e){
        e.preventDefault();
        fetch(this.action,{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},body:new FormData(this)})
        .then(function(r){return r.json();})
        .then(function(d){if(d.success){alert(d.message);tutupModal('modalEditIndikator');location.reload();}else alert('Gagal: '+d.message);})
        .catch(function(){alert('Terjadi kesalahan saat update indikator.');});
    });
})();

/* ══════════════════════════════════════════════════
   FORM EDIT SASARAN — submit via AJAX
══════════════════════════════════════════════════ */
(function(){
    var f=document.getElementById('formEditSasaran');
    if(!f) return;
    f.addEventListener('submit',function(e){
        e.preventDefault();
        fetch('{{ route("pengukuran.periodik.update-sasaran") }}',{
            method:'POST',
            headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},
            body:JSON.stringify({
                sasaran_lama:document.getElementById('es_lama').value,
                sasaran_baru:document.getElementById('es_baru').value,
                tahun:document.getElementById('es_tahun').value,
                opd_id:document.getElementById('es_opd').value
            })
        })
        .then(function(r){return r.json();})
        .then(function(d){if(d.success){alert(d.message);tutupModal('modalEditSasaran');location.reload();}else alert('Gagal: '+d.message);})
        .catch(function(){alert('Terjadi kesalahan saat update sasaran.');});
    });
})();

/* ══════════════════════════════════════════════════
   FORMAT RIBUAN MODAL TAMBAH INDIKATOR
══════════════════════════════════════════════════ */
document.querySelectorAll('#modalTambahIndikator .anggaran-fmt').forEach(applyFormatRibuan);

/* ══════════════════════════════════════════════════
   FORM PANEL BUKA/TUTUP
══════════════════════════════════════════════════ */
(function(){
    var fw=document.getElementById('formTambahWrap');
    function buka(){if(fw){fw.classList.add('open');setTimeout(function(){fw.scrollIntoView({behavior:'smooth',block:'start'});},50);}}
    function tutup(){if(fw)fw.classList.remove('open');}
    var b=document.getElementById('btnTambah');    if(b)b.addEventListener('click',buka);
    var x=document.getElementById('btnTutupForm'); if(x)x.addEventListener('click',tutup);
    var c=document.getElementById('btnBatalForm'); if(c)c.addEventListener('click',tutup);
})();

/* ══════════════════════════════════════════════════
   AUTO-SAVE
══════════════════════════════════════════════════ */
@if(session('user.role') === 'operator')
(function(){
    var sb=document.getElementById('saveStatusBar');
    var st=document.getElementById('saveStatusText');
    var ht,at;
    function show(msg,bg){if(!sb)return;st.textContent=msg;sb.style.background=bg||'#1e293b';sb.style.display='flex';clearTimeout(ht);}
    function hide(){ht=setTimeout(function(){if(sb)sb.style.display='none';},2500);}
    document.querySelectorAll('.xls-input:not(.readonly)').forEach(function(inp){
        inp.addEventListener('input',function(){
            clearTimeout(at);
            var id=inp.dataset.id,field=inp.dataset.field;
            if(!id||!field)return;
            show('⏳ Menunggu...','#334155');
            at=setTimeout(function(){
                show('💾 Menyimpan...','#1e40af');
                fetch('{{ route("pengukuran.periodik.update",":id") }}'.replace(':id',id),{
                    method:'PUT',
                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
                    body:JSON.stringify({field:field,value:inp.value.replace(/\./g,'')})
                })
                .then(function(r){if(!r.ok)throw new Error('HTTP '+r.status);return r.json();})
                .then(function(d){show(d.success?'✅ Tersimpan':'❌ Gagal simpan',d.success?'#065f46':'#991b1b');hide();})
                .catch(function(e){show('❌ Error: '+e.message,'#991b1b');hide();});
            },1500);
        });
        inp.addEventListener('focus',function(){
            var r=document.getElementById('cellRefDisplay');
            if(r)r.textContent='Field: '+(inp.dataset.field||'—')+' | ID: '+(inp.dataset.id||'—');
        });
    });
})();
@endif

/* ══════════════════════════════════════════════════
   TAMBAH INDIKATOR DINAMIS (form panel saja)
   Tabel TW di halaman TIDAK terpengaruh
══════════════════════════════════════════════════ */
(function(){
    var container=document.getElementById('indikatorContainer');
    var btnTambah=document.getElementById('btnTambahIndikator');
    var countEl=document.getElementById('countIndikator');
    if(!container||!btnTambah) return;

    function renumber(){
        var blocks=container.querySelectorAll('.indikator-block');
        blocks.forEach(function(b,i){
            var l=b.querySelector('.indikator-block-title');
            if(l)l.textContent='📌 Indikator #'+(i+1);
        });
        if(countEl)countEl.textContent=blocks.length;
    }

    var twSecs=[
        {label:'Target Kinerja',          dot:'#1f4e79',prefix:'target_kinerja_tw', fmt:false},
        {label:'Target Program/Kegiatan', dot:'#1f4e79',prefix:'target_program_tw', fmt:false},
        {label:'Anggaran (Rp)',           dot:'#4a235a',prefix:'anggaran_tw',        fmt:true },
        {label:'Capaian Kinerja',         dot:'#145a32',prefix:'capaian_kinerja_tw', fmt:false,note:'(kosongkan jika belum ada)'},
        {label:'Capaian Program/Kegiatan',dot:'#1a5276',prefix:'capaian_program_tw', fmt:false},
        {label:'Capaian Anggaran (Rp)',   dot:'#7b241c',prefix:'capaian_anggaran_tw',fmt:true },
    ];

    function buildBlock(){
        var block=document.createElement('div');block.className='indikator-block';
        var head=document.createElement('div');head.className='indikator-block-head';
        var title=document.createElement('div');title.className='indikator-block-title';
        var bH=document.createElement('button');bH.type='button';bH.className='btn-hapus-block';bH.textContent='✕';bH.title='Hapus';
        bH.addEventListener('click',function(){block.remove();renumber();});
        head.appendChild(title);head.appendChild(bH);block.appendChild(head);
        var body=document.createElement('div');body.className='indikator-block-body';
        /* info grid */
        var grid=document.createElement('div');grid.className='form-grid-2';grid.style.marginBottom='10px';
        [{lbl:'Indikator Kinerja *',name:'indikator[]',tag:'input',req:true,ph:'Nama indikator kinerja'},
         {lbl:'Satuan',name:'satuan[]',tag:'input',req:false,ph:'%, Poin, Orang, dll'},
         {lbl:'Sasaran Program/Kegiatan',name:'sasaran_program[]',tag:'textarea',req:false,ph:'Program/kegiatan pendukung'},
         {lbl:'Penanggung Jawab',name:'penanggung_jawab[]',tag:'input',req:false,ph:'Nama jabatan atau unit kerja'}
        ].forEach(function(f){
            var div=document.createElement('div');
            var lbl=document.createElement('label');lbl.className='form-lbl';lbl.textContent=f.lbl;div.appendChild(lbl);
            var el=document.createElement(f.tag);el.name=f.name;el.className=f.tag==='textarea'?'form-ta':'form-inp';
            el.placeholder=f.ph;if(f.req)el.required=true;if(f.tag==='textarea')el.rows=2;
            div.appendChild(el);grid.appendChild(div);
        });
        body.appendChild(grid);
        /* tw block */
        var twWrap=document.createElement('div');twWrap.className='tw-block';
        var twTitle=document.createElement('div');twTitle.style.cssText='font-size:11px;font-weight:700;color:#444;margin-bottom:8px;';
        twTitle.textContent='Isian Per Triwulan (TW1 — TW4)';twWrap.appendChild(twTitle);
        twSecs.forEach(function(sec,si){
            var sect=document.createElement('div');sect.className='tw-section';if(si===twSecs.length-1)sect.style.marginBottom='0';
            var secLbl=document.createElement('div');secLbl.className='tw-section-label';
            secLbl.innerHTML='<span class="tw-dot" style="background:'+sec.dot+';"></span> '+sec.label+(sec.note?' <span style="font-weight:400;color:#94a3b8;">'+sec.note+'</span>':'');
            sect.appendChild(secLbl);
            var secGrid=document.createElement('div');secGrid.className='form-grid-4';
            for(var i=1;i<=4;i++){
                var card=document.createElement('div');card.className='tw-card';
                var ch=document.createElement('div');ch.className='tw-card-head';ch.textContent='TW '+['I','II','III','IV'][i-1];
                var cb=document.createElement('div');cb.className='tw-card-body';
                var ci=document.createElement('input');ci.type='text';ci.name=sec.prefix+i+'[]';
                ci.className='tw-card-inp'+(sec.fmt?' anggaran-fmt':'');ci.placeholder=sec.fmt?'0':'—';
                cb.appendChild(ci);card.appendChild(ch);card.appendChild(cb);secGrid.appendChild(card);
            }
            sect.appendChild(secGrid);twWrap.appendChild(sect);
        });
        body.appendChild(twWrap);
        /* keterangan & file */
        var bg=document.createElement('div');bg.className='form-grid-2';bg.style.marginTop='10px';
        var kd=document.createElement('div');kd.innerHTML='<label class="form-lbl">Keterangan / Catatan</label>';
        var kt=document.createElement('textarea');kt.name='keterangan[]';kt.className='form-ta';kt.rows=2;kt.placeholder='Catatan tambahan (opsional)';kd.appendChild(kt);
        var fd=document.createElement('div');fd.innerHTML='<label class="form-lbl">Upload Bukti Dukung <span style="font-weight:400;color:#94a3b8;">(maks 5MB)</span></label>';
        var fi=document.createElement('input');fi.type='file';fi.name='file[]';fi.className='form-file';fi.accept='.pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png';fd.appendChild(fi);
        bg.appendChild(kd);bg.appendChild(fd);body.appendChild(bg);
        block.appendChild(body);return block;
    }

    btnTambah.addEventListener('click',function(){
        var nb=buildBlock();container.appendChild(nb);renumber();
        nb.querySelectorAll('.anggaran-fmt').forEach(applyFormatRibuan);
        nb.scrollIntoView({behavior:'smooth',block:'nearest'});
    });
})();
</script>
@endsection