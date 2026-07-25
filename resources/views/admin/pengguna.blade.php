@extends('layouts.app')
@section('title','Manajemen Pengguna')
@section('page-title','Manajemen Pengguna')

@section('content')

<style>
    .btn-icon {
        background: transparent;
        border: none;
        cursor: pointer;
        font-size: 16px;
        padding: 4px 8px;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .btn-icon:hover { background: rgba(255,255,255,.1); }

    /* ── Modal umum ── */
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.6);
        backdrop-filter: blur(6px);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    .modal.active { display: flex; }
    .modal-content {
        background: linear-gradient(135deg, #1e1e2e, #181825);
        border: 1px solid rgba(255,255,255,.1);
        border-radius: 20px;
        width: 500px;
        max-width: 92%;
        max-height: 90vh;
        overflow-y: auto;
        padding: 24px;
        animation: modalIn 0.2s ease;
    }
    /* Modal assign OPD lebih lebar */
    .modal-content.wide { width: 640px; }
    @keyframes modalIn {
        from { opacity:0; transform:scale(.95); }
        to   { opacity:1; transform:scale(1); }
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(255,255,255,.1);
    }
    .modal-header h3 { margin:0; font-size:16px; color:#fff; }

    .form-group   { margin-bottom: 16px; }
    .form-label   { display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:rgba(255,255,255,.7); }
    .form-control { width:100%; padding:10px 14px; border:2px solid rgba(255,255,255,.15); border-radius:12px; font-size:14px; color:#fff; background:rgba(255,255,255,.08); transition:border-color .2s; }
    .form-control:focus { outline:none; border-color:#4f46e5; }
    .form-select  { width:100%; padding:10px 14px; border:2px solid rgba(255,255,255,.15); border-radius:12px; font-size:14px; color:#fff; background:rgba(30,30,46,.9); }

    .btn-save   { background:linear-gradient(135deg,#10b981,#059669); color:#fff; border:none; padding:10px 24px; border-radius:40px; font-weight:600; cursor:pointer; }
    .btn-cancel { background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2); color:#fff; padding:10px 24px; border-radius:40px; font-weight:600; cursor:pointer; }
    .btn-primary-sm { background:linear-gradient(135deg,#4f46e5,#6366f1); border:none; border-radius:40px; padding:6px 16px; color:#fff; font-weight:600; cursor:pointer; font-size:12px; }

    /* ── Stats ── */
    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px; }
    .stat-card  { background:linear-gradient(135deg,#1e1e2e,#181825); border:1px solid rgba(255,255,255,.08); border-radius:16px; padding:16px; text-align:center; }
    .stat-value { font-size:28px; font-weight:800; color:#4f46e5; }
    .stat-label { font-size:12px; color:rgba(255,255,255,.5); margin-top:4px; }

    /* ── Assign OPD checklist ── */
    .opd-search {
        width:100%; padding:9px 13px;
        border:1.5px solid rgba(255,255,255,.2);
        border-radius:10px; font-size:13px;
        color:#fff; background:rgba(255,255,255,.08);
        margin-bottom:10px;
    }
    .opd-search:focus { outline:none; border-color:#4f46e5; }
    .opd-list {
        max-height:300px; overflow-y:auto;
        border:1px solid rgba(255,255,255,.1);
        border-radius:12px; padding:6px;
        background:rgba(0,0,0,.2);
    }
    .opd-item {
        display:flex; align-items:center; gap:10px;
        padding:9px 12px; border-radius:8px;
        cursor:pointer; transition:background .15s;
        font-size:13px; color:rgba(255,255,255,.8);
    }
    .opd-item:hover { background:rgba(255,255,255,.06); }
    .opd-item input[type=checkbox] { width:16px; height:16px; accent-color:#4f46e5; cursor:pointer; flex-shrink:0; }
    .opd-item label { cursor:pointer; flex:1; }

    /* Badge tombol assign */
    .btn-assign {
        display:inline-flex; align-items:center; gap:5px;
        padding:3px 10px; border-radius:20px;
        background:rgba(212,152,46,.15);
        border:1px solid rgba(212,152,46,.3);
        color:#f0b84a; font-size:11px; font-weight:700;
        cursor:pointer; transition:all .2s;
    }
    .btn-assign:hover {
        background:rgba(212,152,46,.28);
        border-color:rgba(212,152,46,.55);
        color:#f5c542;
    }
</style>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">{{ $list->count() }}</div>
        <div class="stat-label">Total Pengguna</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $list->where('role','admin')->count() }}</div>
        <div class="stat-label">Admin</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $list->where('role','operator')->count() }}</div>
        <div class="stat-label">Operator</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">{{ $list->where('is_active',0)->count() }}</div>
        <div class="stat-label">Nonaktif</div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <div class="card-title">👥 Daftar Pengguna</div>
        <button class="btn-primary-sm" onclick="openAddModal()" style="padding:8px 20px;font-size:13px;">+ Tambah Pengguna</button>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama / Username/NIP</th>
                    <th>Role</th>
                    <th>OPD</th>
                    <th>Status</th>
                    <th>Terakhir Login</th>
                    <th>Assign OPD</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($list as $i => $u)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    <div style="font-weight:600;color:#fff">{{ $u->nama }}</div>
                    <div style="font-size:11px;color:var(--text-3)">{{ $u->username }}</div>
                </td>
                <td>
                    @if($u->role === 'admin')
                        <span class="badge badge-danger">Administrator</span>
                    @elseif($u->role === 'evaluator')
                        <span class="badge badge-warning">Evaluator</span>
                    @else
                        <span class="badge badge-info">Operator</span>
                    @endif
                </td>
                <td>{{ $u->nama_daerah ?? '—' }}</td>
                <td>
                    @if(($u->status_daftar ?? 'disetujui') === 'menunggu')
                        <span class="badge badge-warning">⏳ Menunggu</span>
                    @elseif($u->is_active)
                        <span class="badge badge-success">✅ Aktif</span>
                    @else
                        <span class="badge badge-danger">❌ Nonaktif</span>
                    @endif
                </td>
                <td>
                    {{ $u->last_login ? \Carbon\Carbon::parse($u->last_login)->format('d/m/Y H:i') : '—' }}
                </td>

                {{-- ── KOLOM ASSIGN OPD ── --}}
                <td>
                    @if($u->role === 'evaluator')
                        {{--
                            FIX: Sebelumnya ini hanya <span> tanpa onclick.
                            Sekarang dibuat jadi tombol yang memanggil openAssignModal()
                        --}}
                        <button
                            class="btn-assign"
                            onclick="openAssignModal({{ $u->id }}, '{{ addslashes($u->nama) }}')"
                            title="Kelola OPD yang diassign ke evaluator ini">
                            🏷️ {{ $u->assigned_opd_count ?? 0 }} OPD
                        </button>
                    @else
                        <span style="color:rgba(255,255,255,.25);font-size:12px;">—</span>
                    @endif
                </td>

                {{-- ── KOLOM AKSI ── --}}
                <td>
                    @if($u->role === 'admin')
                        <button class="btn-icon" onclick="openResetModal({{ $u->id }}, '{{ addslashes($u->nama) }}')" title="Reset Password">🔑</button>
                    @else
                        <button class="btn-icon" onclick="openEditModal({{ $u->id }})" title="Edit">✏️</button>
                        <button class="btn-icon" onclick="openResetModal({{ $u->id }}, '{{ addslashes($u->nama) }}')" title="Reset Password">🔑</button>
                        <button class="btn-icon" onclick="deleteUser({{ $u->id }}, '{{ addslashes($u->nama) }}')" title="Hapus" style="color:#ef4444;">🗑️</button>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;padding:48px;color:rgba(255,255,255,.4);">Tidak ada data</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ══════════ MODAL TAMBAH / EDIT ══════════ --}}
<div id="userModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah Pengguna</h3>
            <button class="btn-icon" onclick="closeModal()" style="font-size:20px;color:#fff;">✕</button>
        </div>
        <form id="userForm">
            @csrf
            <input type="hidden" id="user_id"     name="user_id">
            <input type="hidden" id="form_action" name="form_action">

            <div class="form-group">
                <label class="form-label">Username/NIP *</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" id="nama" name="nama" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Role *</label>
                <select id="role" name="role" class="form-select" onchange="toggleOpdField()">
                    <option value="operator">Operator</option>
                    <option value="evaluator">Evaluator</option>
                </select>
            </div>
            <div class="form-group" id="opd_group">
                <label class="form-label">Perangkat Daerah *</label>
                <select id="perangkat_daerah_id" name="perangkat_daerah_id" class="form-select">
                    <option value="">Pilih OPD</option>
                    @foreach($listOpd ?? [] as $opd)
                        <option value="{{ $opd->id }}">{{ $opd->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" id="password_group">
                <label class="form-label" id="password_label">Password *</label>
                <input type="password" id="password" name="password" class="form-control">
                <small style="color:rgba(255,255,255,.4);font-size:11px;">Minimal 6 karakter</small>
            </div>
            <div class="form-group" id="active_group" style="display:none;">
                <label class="form-label">Status</label>
                <select id="is_active" name="is_active" class="form-select">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:24px;">
                <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-save">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════ MODAL RESET PASSWORD ══════════ --}}
<div id="resetModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Reset Password</h3>
            <button class="btn-icon" onclick="closeResetModal()" style="font-size:20px;color:#fff;">✕</button>
        </div>
        <form id="resetForm">
            @csrf
            <input type="hidden" id="reset_user_id" name="user_id">
            <div class="form-group">
                <label class="form-label">Password Baru *</label>
                <input type="password" id="reset_password" name="password" class="form-control" required>
                <small style="color:rgba(255,255,255,.4);font-size:11px;">Minimal 6 karakter</small>
            </div>
            <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:24px;">
                <button type="button" class="btn-cancel" onclick="closeResetModal()">Batal</button>
                <button type="submit" class="btn-save">Reset Password</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════ MODAL ASSIGN OPD (BARU) ══════════ --}}
<div id="assignModal" class="modal">
    <div class="modal-content wide">
        <div class="modal-header">
            <h3>🏷️ Assign OPD — <span id="assignNamaEvaluator" style="color:#f0b84a;"></span></h3>
            <button class="btn-icon" onclick="closeAssignModal()" style="font-size:20px;color:#fff;">✕</button>
        </div>

        <input type="hidden" id="assign_evaluator_id">

        {{-- Search --}}
        <input type="text"
               id="opdSearch"
               class="opd-search"
               placeholder="🔍 Cari nama OPD..."
               oninput="filterOpd(this.value)">

        {{-- Info jumlah terpilih --}}
        <div style="margin-bottom:8px;font-size:12px;color:rgba(255,255,255,.5);">
            Terpilih: <span id="assignCount" style="color:#f0b84a;font-weight:700;">0</span> OPD
        </div>

        {{-- Daftar OPD checkbox --}}
        <div class="opd-list" id="opdCheckList">
            <div style="padding:20px;text-align:center;color:rgba(255,255,255,.4);font-size:13px;">
                Memuat daftar OPD...
            </div>
        </div>

        <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:20px;">
            <button type="button" class="btn-cancel" onclick="closeAssignModal()">Batal</button>
            <button type="button" class="btn-save" onclick="simpanAssignOpd()">💾 Simpan Assign</button>
        </div>
    </div>
</div>

<script>
// ═══════════════════════════════════════════════════════
//  URL HELPER — pakai prefix /admin/ sesuai route group
// ═══════════════════════════════════════════════════════
const BASE_URL  = '{{ url("admin") }}';
const CSRF      = '{{ csrf_token() }}';

// Semua OPD tersedia untuk ditampilkan di modal assign
const SEMUA_OPD = @json($listOpd ?? []);

// ═══════════════════════════════════════════════════════
//  MODAL TAMBAH / EDIT
// ═══════════════════════════════════════════════════════
function openAddModal() {
    document.getElementById('modalTitle').innerText  = 'Tambah Pengguna';
    document.getElementById('form_action').value     = 'add';
    document.getElementById('user_id').value         = '';
    document.getElementById('username').value        = '';
    document.getElementById('nama').value            = '';
    document.getElementById('role').value            = 'operator';
    document.getElementById('password').value        = '';
    document.getElementById('password_group').style.display = 'block';
    document.getElementById('password_label').innerText     = 'Password *';
    document.getElementById('active_group').style.display   = 'none';
    toggleOpdField();
    document.getElementById('userModal').classList.add('active');
}

function openEditModal(id) {
    fetch(`${BASE_URL}/pengguna/${id}/data`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('modalTitle').innerText      = 'Edit Pengguna';
            document.getElementById('form_action').value         = 'edit';
            document.getElementById('user_id').value             = data.id;
            document.getElementById('username').value            = data.username;
            document.getElementById('nama').value                = data.nama;
            document.getElementById('role').value                = data.role;
            document.getElementById('is_active').value           = data.is_active ? '1' : '0';
            document.getElementById('password_group').style.display = 'none';
            document.getElementById('active_group').style.display   = 'block';
            if (data.perangkat_daerah_id) {
                document.getElementById('perangkat_daerah_id').value = data.perangkat_daerah_id;
            }
            toggleOpdField();
            document.getElementById('userModal').classList.add('active');
        })
        .catch(() => alert('Gagal mengambil data user'));
}

function closeModal() {
    document.getElementById('userModal').classList.remove('active');
}

function toggleOpdField() {
    var role = document.getElementById('role').value;
    document.getElementById('opd_group').style.display = (role === 'operator') ? 'block' : 'none';
}

document.getElementById('userForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const action = document.getElementById('form_action').value;
    const userId = document.getElementById('user_id').value;
    const url    = action === 'add'
        ? `${BASE_URL}/pengguna/store`
        : `${BASE_URL}/pengguna/${userId}/update`;

    const formData = {
        username : document.getElementById('username').value,
        nama     : document.getElementById('nama').value,
        role     : document.getElementById('role').value,
        _token   : CSRF,
    };
    if (action === 'add')  formData.password  = document.getElementById('password').value;
    if (action === 'edit') formData.is_active = document.getElementById('is_active').value;
    if (formData.role === 'operator') {
        formData.perangkat_daerah_id = document.getElementById('perangkat_daerah_id').value;
    }

    try {
        const res    = await fetch(url, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(formData) });
        const result = await res.json();
        if (result.success) { alert(result.message); location.reload(); }
        else alert('Error: ' + (result.errors ? JSON.stringify(result.errors) : result.message));
    } catch(err) { alert('Terjadi kesalahan: ' + err); }
});

// ═══════════════════════════════════════════════════════
//  MODAL RESET PASSWORD
// ═══════════════════════════════════════════════════════
function openResetModal(id, nama) {
    document.getElementById('reset_user_id').value = id;
    document.getElementById('reset_password').value = '';
    document.getElementById('resetModal').classList.add('active');
}
function closeResetModal() {
    document.getElementById('resetModal').classList.remove('active');
}

document.getElementById('resetForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const userId = document.getElementById('reset_user_id').value;
    try {
        const res    = await fetch(`${BASE_URL}/pengguna/${userId}/reset-password`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ password: document.getElementById('reset_password').value, _token: CSRF })
        });
        const result = await res.json();
        if (result.success) { alert(result.message); closeResetModal(); }
        else alert('Error: ' + result.message);
    } catch(err) { alert('Terjadi kesalahan: ' + err); }
});

// ═══════════════════════════════════════════════════════
//  HAPUS USER
// ═══════════════════════════════════════════════════════
async function deleteUser(id, nama) {
    if (!confirm(`Yakin ingin menghapus user "${nama}"?`)) return;
    try {
        const res    = await fetch(`${BASE_URL}/pengguna/${id}/delete`, {
            method: 'DELETE',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const result = await res.json();
        if (result.success) { alert(result.message); location.reload(); }
        else alert('Error: ' + result.message);
    } catch(err) { alert('Terjadi kesalahan: ' + err); }
}

// ═══════════════════════════════════════════════════════
//  MODAL ASSIGN OPD — FIX UTAMA
// ═══════════════════════════════════════════════════════

/**
 * Buka modal assign OPD untuk evaluator.
 * 1. Set nama evaluator di header modal
 * 2. Render semua OPD sebagai checkbox
 * 3. Fetch OPD yang sudah diassign, lalu centang otomatis
 */
function openAssignModal(evaluatorId, namaEvaluator) {
    document.getElementById('assign_evaluator_id').value     = evaluatorId;
    document.getElementById('assignNamaEvaluator').innerText = namaEvaluator;
    document.getElementById('opdSearch').value               = '';
    document.getElementById('assignModal').classList.add('active');

    // Render seluruh OPD dulu (unchecked)
    renderOpdList(SEMUA_OPD, []);

    // Ambil OPD yang sudah diassign dari server
    fetch(`${BASE_URL}/evaluator/${evaluatorId}/assigned-opd`)
        .then(r => r.json())
        .then(data => {
            // data.assigned_opd = array id OPD
            var assigned = data.assigned_opd || [];
            renderOpdList(SEMUA_OPD, assigned);
        })
        .catch(() => {
            // Tetap tampilkan daftar meski gagal fetch assigned
            renderOpdList(SEMUA_OPD, []);
        });
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.remove('active');
}

/** Render daftar OPD checkbox */
function renderOpdList(opdList, checkedIds) {
    var container = document.getElementById('opdCheckList');
    if (!opdList || opdList.length === 0) {
        container.innerHTML = '<div style="padding:20px;text-align:center;color:rgba(255,255,255,.4);">Tidak ada OPD tersedia</div>';
        updateAssignCount();
        return;
    }

    container.innerHTML = opdList.map(function(opd) {
        var checked = checkedIds.includes(opd.id) ? 'checked' : '';
        return `
        <div class="opd-item" data-nama="${opd.nama.toLowerCase()}" onclick="toggleCheck(${opd.id})">
            <input type="checkbox"
                   id="opd_check_${opd.id}"
                   value="${opd.id}"
                   ${checked}
                   onclick="event.stopPropagation(); updateAssignCount()">
            <label for="opd_check_${opd.id}">${opd.nama}</label>
        </div>`;
    }).join('');

    updateAssignCount();
}

/** Toggle checkbox saat klik baris OPD */
function toggleCheck(opdId) {
    var cb = document.getElementById('opd_check_' + opdId);
    if (cb) {
        cb.checked = !cb.checked;
        updateAssignCount();
    }
}

/** Update counter OPD terpilih */
function updateAssignCount() {
    var count = document.querySelectorAll('#opdCheckList input[type=checkbox]:checked').length;
    document.getElementById('assignCount').innerText = count;
}

/** Filter OPD berdasarkan teks pencarian */
function filterOpd(keyword) {
    var q = keyword.toLowerCase().trim();
    document.querySelectorAll('#opdCheckList .opd-item').forEach(function(item) {
        var nama = item.getAttribute('data-nama') || '';
        item.style.display = (!q || nama.includes(q)) ? '' : 'none';
    });
}

/** Kirim data assign OPD ke server */
async function simpanAssignOpd() {
    var evaluatorId = document.getElementById('assign_evaluator_id').value;

    // Kumpulkan semua OPD yang dicentang
    var checkedBoxes = document.querySelectorAll('#opdCheckList input[type=checkbox]:checked');
    var opdIds = Array.from(checkedBoxes).map(cb => parseInt(cb.value));

    try {
        const res = await fetch(`${BASE_URL}/evaluator/${evaluatorId}/assign-opd`, {
            method: 'POST',
            headers: {
                'Content-Type'  : 'application/json',
                'X-CSRF-TOKEN'  : CSRF,
            },
            body: JSON.stringify({ opd_ids: opdIds })
        });

        const result = await res.json();

        if (result.success) {
            alert(result.message);
            closeAssignModal();
            location.reload();
        } else {
            alert('Gagal: ' + result.message);
        }
    } catch(err) {
        alert('Terjadi kesalahan saat menyimpan: ' + err);
    }
}
</script>
@endsection