@extends('layouts.app')
@section('title', 'Kelola Klaster OPD')
@section('page-title', 'Kelola Klaster OPD')
@section('page-sub', 'Atur klaster dan tugas setiap Perangkat Daerah')

@section('topbar-actions')
<a href="{{ route('dashboard') }}" class="btn-outline-glass">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
    </svg>
    Kembali ke Dashboard
</a>
@endsection

@section('content')

<style>
    .klaster-section {
        background: linear-gradient(135deg, #1e1e2e, #181825);
        border-radius: 20px;
        border: 1px solid rgba(255,255,255,.08);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .klaster-header {
        background: linear-gradient(135deg, #1e3a5f, #2563eb);
        padding: 14px 20px;
        color: white;
        font-weight: 700;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .klaster-body {
        padding: 0;
    }
    .opd-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 20px;
        border-bottom: 1px solid rgba(255,255,255,.05);
        transition: background 0.2s;
    }
    .opd-item:hover {
        background: rgba(255,255,255,.03);
    }
    .opd-info {
        flex: 1;
    }
    .opd-nama {
        font-weight: 600;
        color: #fff;
        margin-bottom: 4px;
    }
    .opd-tugas {
        font-size: 12px;
        color: #a1a1aa;
    }
    .opd-tugas-empty {
        font-size: 12px;
        color: #f59e0b;
        font-style: italic;
    }
    .opd-actions {
        display: flex;
        gap: 8px;
    }
    .btn-icon {
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 8px;
        padding: 6px 12px;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.2s;
    }
    .btn-icon:hover {
        background: rgba(99,102,241,.2);
        border-color: rgba(99,102,241,.4);
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: linear-gradient(135deg, #1e1e2e, #181825);
        border-radius: 16px;
        padding: 16px;
        border: 1px solid rgba(255,255,255,.08);
        text-align: center;
    }
    .stat-value {
        font-size: 28px;
        font-weight: 800;
    }
    .stat-label {
        font-size: 11px;
        color: #a1a1aa;
        margin-top: 4px;
    }
    .stat-utama { color: #6366f1; }
    .stat-pendukung { color: #10b981; }
    .stat-tambahan { color: #f59e0b; }
    .stat-belum { color: #ef4444; }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
        backdrop-filter: blur(8px);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    .modal.active {
        display: flex;
    }
    .modal-content {
        background: linear-gradient(135deg, #1e1e2e, #181825);
        border-radius: 20px;
        width: 500px;
        max-width: 90%;
        padding: 24px;
        border: 1px solid rgba(255,255,255,.1);
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(255,255,255,.1);
    }
    .modal-header h3 {
        margin: 0;
        font-size: 18px;
        color: #fff;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #e4e4e7;
    }
    .form-control, .form-select {
        width: 100%;
        padding: 10px 14px;
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.15);
        border-radius: 10px;
        color: #fff;
        font-size: 14px;
    }
    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: #6366f1;
    }
    textarea.form-control {
        resize: vertical;
        font-family: inherit;
    }
    .btn-save {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 40px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-cancel {
        background: rgba(255,255,255,.1);
        border: none;
        padding: 10px 20px;
        border-radius: 40px;
        color: #fff;
        cursor: pointer;
    }
    .btn-outline-glass {
        background: transparent;
        border: 2px solid #e2e8f0;
        padding: 8px 20px;
        border-radius: 40px;
        color: #475569;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .btn-outline-glass:hover {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border-color: transparent;
        color: white;
        transform: translateY(-2px);
        text-decoration: none;
    }
</style>

{{-- STATISTIK --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value stat-utama">{{ $stats['utama'] }}</div>
        <div class="stat-label">Klaster Utama</div>
    </div>
    <div class="stat-card">
        <div class="stat-value stat-pendukung">{{ $stats['pendukung'] }}</div>
        <div class="stat-label">Klaster Pendukung</div>
    </div>
    <div class="stat-card">
        <div class="stat-value stat-tambahan">{{ $stats['tambahan'] }}</div>
        <div class="stat-label">Klaster Tambahan</div>
    </div>
    <div class="stat-card">
        <div class="stat-value stat-belum">{{ $stats['belum'] }}</div>
        <div class="stat-label">Belum Ditetapkan</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $stats['total'] }}</div>
        <div class="stat-label">Total OPD</div>
    </div>
</div>

{{-- KLASTER UTAMA --}}
<div class="klaster-section">
    <div class="klaster-header">
        <span>🏆 Klaster Utama ({{ $stats['utama'] }} OPD)</span>
        <span style="font-size: 12px; opacity: 0.7;">Total OPD dalam klaster utama</span>
    </div>
    <div class="klaster-body">
        @forelse($groupedOpd['utama'] as $opd)
        <div class="opd-item">
            <div class="opd-info">
                <div class="opd-nama">{{ $opd->nama }}</div>
                <div class="opd-tugas">
                    @if($opd->tugas)
                        📋 {{ $opd->tugas }}
                    @else
                        <span class="opd-tugas-empty">⚡ Tugas belum ditentukan</span>
                    @endif
                </div>
            </div>
            <div class="opd-actions">
                <button class="btn-icon" onclick="editOpd({{ $opd->id }}, '{{ addslashes($opd->nama) }}', '{{ $opd->klaster }}', '{{ addslashes($opd->tugas) }}')">✏️ Edit</button>
            </div>
        </div>
        @empty
        <div style="padding: 20px; text-align: center; color: #71717a;">Tidak ada OPD</div>
        @endforelse
    </div>
</div>

{{-- KLASTER PENDUKUNG --}}
<div class="klaster-section">
    <div class="klaster-header">
        <span>🛡️ Klaster Pendukung ({{ $stats['pendukung'] }} OPD)</span>
        <span style="font-size: 12px; opacity: 0.7;">Total OPD dalam klaster pendukung</span>
    </div>
    <div class="klaster-body">
        @forelse($groupedOpd['pendukung'] as $opd)
        <div class="opd-item">
            <div class="opd-info">
                <div class="opd-nama">{{ $opd->nama }}</div>
                <div class="opd-tugas">
                    @if($opd->tugas)
                        📋 {{ $opd->tugas }}
                    @else
                        <span class="opd-tugas-empty">⚡ Tugas belum ditentukan</span>
                    @endif
                </div>
            </div>
            <div class="opd-actions">
                <button class="btn-icon" onclick="editOpd({{ $opd->id }}, '{{ addslashes($opd->nama) }}', '{{ $opd->klaster }}', '{{ addslashes($opd->tugas) }}')">✏️ Edit</button>
            </div>
        </div>
        @empty
        <div style="padding: 20px; text-align: center; color: #71717a;">Tidak ada OPD</div>
        @endforelse
    </div>
</div>

{{-- KLASTER TAMBAHAN --}}
<div class="klaster-section">
    <div class="klaster-header">
        <span>➕ Klaster Tambahan ({{ $stats['tambahan'] }} OPD)</span>
        <span style="font-size: 12px; opacity: 0.7;">Total OPD dalam klaster tambahan</span>
    </div>
    <div class="klaster-body">
        @forelse($groupedOpd['tambahan'] as $opd)
        <div class="opd-item">
            <div class="opd-info">
                <div class="opd-nama">{{ $opd->nama }}</div>
                <div class="opd-tugas">
                    @if($opd->tugas)
                        📋 {{ $opd->tugas }}
                    @else
                        <span class="opd-tugas-empty">⚡ Tugas belum ditentukan</span>
                    @endif
                </div>
            </div>
            <div class="opd-actions">
                <button class="btn-icon" onclick="editOpd({{ $opd->id }}, '{{ addslashes($opd->nama) }}', '{{ $opd->klaster }}', '{{ addslashes($opd->tugas) }}')">✏️ Edit</button>
            </div>
        </div>
        @empty
        <div style="padding: 20px; text-align: center; color: #71717a;">Tidak ada OPD</div>
        @endforelse
    </div>
</div>

{{-- BELUM DITETAPKAN --}}
@if(count($groupedOpd['belum']) > 0)
<div class="klaster-section">
    <div class="klaster-header" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
        <span>⚠️ Belum Ditetapkan ({{ $stats['belum'] }} OPD)</span>
        <span style="font-size: 12px; opacity: 0.7;">Perlu segera ditetapkan klasternya</span>
    </div>
    <div class="klaster-body">
        @foreach($groupedOpd['belum'] as $opd)
        <div class="opd-item">
            <div class="opd-info">
                <div class="opd-nama">{{ $opd->nama }}</div>
                <div class="opd-tugas">
                    @if($opd->tugas)
                        📋 {{ $opd->tugas }}
                    @else
                        <span class="opd-tugas-empty">⚡ Tugas belum ditentukan</span>
                    @endif
                </div>
            </div>
            <div class="opd-actions">
                <button class="btn-icon" onclick="editOpd({{ $opd->id }}, '{{ addslashes($opd->nama) }}', '{{ $opd->klaster }}', '{{ addslashes($opd->tugas) }}')">✏️ Edit</button>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- MODAL EDIT --}}
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Edit OPD</h3>
            <button class="btn-icon" onclick="closeModal()" style="font-size: 18px; background: rgba(255,255,255,.1);">✕</button>
        </div>
        <form id="editForm">
            @csrf
            <input type="hidden" id="opd_id" name="opd_id">
            
            <div class="form-group">
                <label class="form-label">Nama OPD</label>
                <input type="text" id="opd_nama" class="form-control" readonly disabled style="opacity: 0.7;">
            </div>
            
            <div class="form-group">
                <label class="form-label">Klaster *</label>
                <select id="klaster" name="klaster" class="form-select">
                    <option value="">Pilih Klaster</option>
                    <option value="utama">🏆 Klaster Utama</option>
                    <option value="pendukung">🛡️ Klaster Pendukung</option>
                    <option value="tambahan">➕ Klaster Tambahan</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Tugas / Peran</label>
                <textarea id="tugas" name="tugas" rows="3" class="form-control" placeholder="Isi tugas atau peran OPD dalam klaster ini..."></textarea>
                <small style="color: #71717a; font-size: 11px;">Maksimal 300 karakter</small>
            </div>
            
            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-save">💾 Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
const modal = document.getElementById('editModal');

function editOpd(id, nama, klaster, tugas) {
    document.getElementById('opd_id').value = id;
    document.getElementById('opd_nama').value = nama;
    document.getElementById('klaster').value = klaster || '';
    document.getElementById('tugas').value = (tugas === 'null' || tugas === 'undefined') ? '' : (tugas || '');
    modal.classList.add('active');
}

function closeModal() {
    modal.classList.remove('active');
}

// Submit form edit
document.getElementById('editForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const opdId = document.getElementById('opd_id').value;
    const klaster = document.getElementById('klaster').value;
    const tugas = document.getElementById('tugas').value;
    
    if (!klaster) {
        alert('Pilih klaster terlebih dahulu!');
        return;
    }
    
    // Update klaster
    const klasterResponse = await fetch(`{{ url("admin/klaster") }}/${opdId}/klaster`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ klaster: klaster })
    });
    
    const klasterResult = await klasterResponse.json();
    
    if (!klasterResult.success) {
        alert('Gagal update klaster: ' + klasterResult.message);
        return;
    }
    
    // Update tugas
    const tugasResponse = await fetch(`{{ url("admin/klaster") }}/${opdId}/tugas`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ tugas: tugas })
    });
    
    const tugasResult = await tugasResponse.json();
    
    if (tugasResult.success) {
        alert('✅ ' + tugasResult.message);
        location.reload();
    } else {
        alert('⚠️ Klaster berhasil diupdate, tapi tugas gagal: ' + tugasResult.message);
        location.reload();
    }
});

// Tutup modal saat klik di luar
modal.addEventListener('click', function(e) {
    if (e.target === modal) {
        closeModal();
    }
});
</script>

@endsection