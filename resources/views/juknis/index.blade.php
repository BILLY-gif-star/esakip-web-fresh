@extends('layouts.app')
@section('title', 'Kelola Juknis')
@section('page-title', 'Petunjuk Teknis')
@section('page-sub', 'Kelola Juknis LKE AKIP')

@section('topbar-actions')
  <button class="btn-upload" onclick="toggleModal('modalUpload', true)">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M12 3v12m0 0-3-3m3 3 3-3M5 21h14"/>
    </svg>
    Upload Juknis Baru
  </button>
@endsection

@section('content')

<style>
  /* Tambahan transisi halus untuk modal */
  .modal-friendly {
    transition: opacity 0.3s ease;
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(4px);
    z-index: 1000;
    align-items: center;
    justify-content: center;
  }
  .modal-friendly.open { display: flex; }

  .card-glass { background: white; border-radius: 20px; border: 1px solid #e5e7eb; overflow: hidden; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
  .card-header-glass { background: linear-gradient(135deg, #f8fafc, #f1f5f9); padding: 16px 24px; border-bottom: 1px solid #e5e7eb; }
  .card-header-glass h3 { font-size: 16px; font-weight: 700; margin: 0; }
  .card-body-glass { padding: 24px; }
  
  .table-glass { width: 100%; border-collapse: collapse; }
  .table-glass th { background: #f8fafc; padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 700; color: #6b7280; border-bottom: 1px solid #e5e7eb; }
  .table-glass td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
  .table-glass tbody tr:hover { background: #f9fafb; }

  .btn-upload { background: linear-gradient(135deg, #f59e0b, #d97706); border: none; border-radius: 40px; padding: 8px 20px; color: white; font-weight: 600; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
  .btn-primary-glass { background: linear-gradient(135deg, #4f46e5, #6366f1); border: none; border-radius: 40px; padding: 6px 14px; color: white; font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; }
  .btn-outline-glass { background: white; border: 1px solid #e5e7eb; border-radius: 40px; padding: 6px 14px; color: #6b7280; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
  .btn-danger-glass { background: white; border: 1px solid #fee2e2; border-radius: 40px; padding: 6px 14px; color: #dc2626; font-size: 12px; font-weight: 600; cursor: pointer; }

  .modal-content-friendly { background: white; border-radius: 24px; width: 500px; max-width: 95%; animation: slideIn 0.3s ease; }
  @keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
  
  .form-control-friendly { width: 100%; border: 1px solid #e5e7eb; border-radius: 12px; padding: 10px 12px; margin-top: 4px; outline: none; }
  .form-control-friendly:focus { border-color: #6366f1; box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.1); }
  
  .badge { display: inline-block; padding: 4px 12px; border-radius: 30px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
  .badge-active { background: #d1fae5; color: #065f46; }
  .badge-inactive { background: #fee2e2; color: #991b1b; }
</style>

<div class="card-glass">
  <div class="card-header-glass">
    <h3>📋 Daftar Juknis LKE AKIP</h3>
  </div>
  <div class="card-body-glass">
    <div class="table-responsive">
      <table class="table-glass">
        <thead>
          <tr>
            <th>No</th>
            <th>Judul & File</th>
            <th>Urutan</th>
            <th>Status</th>
            <th>Tgl Upload</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($juknisList as $j)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                <div style="font-weight: 600;">{{ $j->judul }}</div>
                <small class="text-muted">{{ $j->file_name }}</small>
            </td>
            <td>
                <span class="badge" style="background: #f3f4f6; color: #374151;">
                    {{ $j->urutan ?: 'Tidak diatur' }}
                </span>
            </td>
            <td>
              <span class="badge {{ $j->is_active ? 'badge-active' : 'badge-inactive' }}">
                {{ $j->is_active ? 'Aktif' : 'Nonaktif' }}
              </span>
            </td>
            <td>{{ $j->created_at->format('d/m/Y') }}</td>
            <td>
              <div style="display:flex;gap:6px;">
                <a href="{{ route('juknis.preview', $j->id) }}" target="_blank" class="btn-outline-glass">👁️ Preview</a>
                <button 
                    onclick="openEditModal(@json($j))" 
                    class="btn-primary-glass">✏️ Edit</button>
                
                <form method="POST" action="{{ route('juknis.delete', $j->id) }}" onsubmit="return confirm('Hapus juknis ini?')" style="display:inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn-danger-glass">🗑️</button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" style="text-align:center;padding:48px; color: #9ca3af;">📭 Belum ada juknis yang diupload</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- MODAL UPLOAD --}}
<div class="modal-friendly" id="modalUpload" onclick="closeOnOverlay(event, 'modalUpload')">
  <div class="modal-content-friendly">
    <div class="modal-header-friendly">
      <div class="modal-title-friendly">📤 Upload Juknis Baru</div>
      <button class="modal-close-friendly" onclick="toggleModal('modalUpload', false)">✕</button>
    </div>
    <form method="POST" action="{{ route('juknis.upload') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-body-friendly">
        <div style="margin-bottom:16px">
          <label class="small font-weight-bold">📝 Judul Juknis</label>
          <input type="text" name="judul" class="form-control-friendly" placeholder="Masukkan judul..." required>
        </div>
        <div style="margin-bottom:16px">
          <label class="small font-weight-bold">📁 File (PDF/DOCX/XLSX) <span class="text-danger">*</span></label>
          <input type="file" name="file" class="form-control-friendly" required>
        </div>
        <div style="margin-bottom:16px">
          <label class="small font-weight-bold">📝 Deskripsi (opsional)</label>
          <textarea name="deskripsi" rows="3" class="form-control-friendly"></textarea>
        </div>
        <div>
          <label class="small font-weight-bold">🔢 Urutan Tampil</label>
          <select name="urutan" class="form-control-friendly">
            <option value="0">Tanpa urutan</option>
            <option value="1">Urutan 1 (Kiri)</option>
            <option value="2">Urutan 2 (Tengah)</option>
            <option value="3">Urutan 3 (Kanan)</option>
          </select>
        </div>
      </div>
      <div class="modal-footer-friendly">
        <button type="button" class="btn-outline-glass" onclick="toggleModal('modalUpload', false)">Batal</button>
        <button type="submit" class="btn-upload">📂 Upload</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal-friendly" id="modalEdit" onclick="closeOnOverlay(event, 'modalEdit')">
  <div class="modal-content-friendly">
    <div class="modal-header-friendly">
      <div class="modal-title-friendly">✏️ Edit Juknis</div>
      <button class="modal-close-friendly" onclick="toggleModal('modalEdit', false)">✕</button>
    </div>
    <form method="POST" id="formEdit" enctype="multipart/form-data">
      @csrf @method('PUT')
      <div class="modal-body-friendly">
        <div style="margin-bottom:16px">
          <label class="small font-weight-bold">📝 Judul Juknis</label>
          <input type="text" name="judul" id="editJudul" class="form-control-friendly" required>
        </div>
        <div style="margin-bottom:16px">
          <label class="small font-weight-bold">📁 Ganti File <small class="text-muted">(Biarkan kosong jika tidak diganti)</small></label>
          <input type="file" name="file" class="form-control-friendly">
        </div>
        <div style="margin-bottom:16px">
          <label class="small font-weight-bold">📝 Deskripsi</label>
          <textarea name="deskripsi" id="editDeskripsi" rows="3" class="form-control-friendly"></textarea>
        </div>
        <div style="margin-bottom:16px">
          <label class="small font-weight-bold">🔢 Urutan Tampil</label>
          <select name="urutan" id="editUrutan" class="form-control-friendly">
            <option value="0">Tanpa urutan</option>
            <option value="1">Urutan 1 (Kiri)</option>
            <option value="2">Urutan 2 (Tengah)</option>
            <option value="3">Urutan 3 (Kanan)</option>
          </select>
        </div>
        <div>
          <label class="small font-weight-bold">✅ Status Aktif</label>
          <select name="is_active" id="editIsActive" class="form-control-friendly">
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
          </select>
        </div>
      </div>
      <div class="modal-footer-friendly">
        <button type="button" class="btn-outline-glass" onclick="toggleModal('modalEdit', false)">Batal</button>
        <button type="submit" class="btn-upload">💾 Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<script>
/**
 * Global function untuk buka/tutup modal
 */
function toggleModal(id, show) {
    const modal = document.getElementById(id);
    if (show) modal.classList.add('open');
    else modal.classList.remove('open');
}

/**
 * Tutup modal saat klik di luar kotak (overlay)
 */
function closeOnOverlay(e, id) {
    if (e.target.id === id) toggleModal(id, false);
}

/**
 * Handle data untuk Modal Edit
 */
function openEditModal(juknis) {
    const form = document.getElementById('formEdit');
    
    // Set Action URL menggunakan template rute (pastikan rute name 'juknis.update' ada)
    form.action = `{{ url('juknis/update') }}/${juknis.id}`;
    
    // Isi field
    document.getElementById('editJudul').value = juknis.judul;
    document.getElementById('editDeskripsi').value = juknis.deskripsi || '';
    document.getElementById('editUrutan').value = juknis.urutan || 0;
    document.getElementById('editIsActive').value = juknis.is_active ? 1 : 0;
    
    toggleModal('modalEdit', true);
}
</script>

@endsection