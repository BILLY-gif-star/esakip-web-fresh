@extends('layouts.app')

@section('title', 'Assign OPD untuk Evaluator')
@section('page-title', 'Assign OPD')
@section('page-sub', 'Pilih OPD yang akan dinilai oleh ' . $evaluator->nama)

@section('content')

<style>
    .opd-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 12px;
        margin-top: 20px;
    }
    .opd-checkbox-item {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 12px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .opd-checkbox-item:hover {
        background: rgba(255,255,255,0.1);
        border-color: rgba(212,152,46,0.4);
    }
    .opd-checkbox-item input {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #d4982e;
    }
    .opd-checkbox-item label {
        flex: 1;
        cursor: pointer;
        font-size: 14px;
        color: #fff;
    }
    .selected-count {
        background: rgba(212,152,46,0.2);
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 12px;
        color: #f0b84a;
    }
    .btn-save {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 40px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-cancel {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        padding: 10px 24px;
        border-radius: 40px;
        color: #fff;
        text-decoration: none;
    }
    .btn-outline {
        background: transparent;
        border: 1px solid rgba(255,255,255,0.2);
        padding: 6px 14px;
        border-radius: 40px;
        color: #fff;
        cursor: pointer;
    }
</style>

<div class="card">
    <div class="card-header">
        <div class="card-title">📋 Pilih OPD untuk {{ $evaluator->nama }}</div>
        <div class="card-subtitle">Evaluator akan dapat menilai OPD yang dipilih (dapat memilih lebih dari satu)</div>
    </div>
    <div class="card-body">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <span class="selected-count" id="selectedCountDisplay">0 OPD dipilih</span>
            </div>
            <div>
                <button type="button" onclick="selectAll()" class="btn-outline" style="margin-right: 8px;">✅ Pilih Semua</button>
                <button type="button" onclick="deselectAll()" class="btn-outline">❌ Batal Pilih Semua</button>
            </div>
        </div>

        <form id="assignForm">
            @csrf
            <input type="hidden" id="evaluator_id" value="{{ $evaluator->id }}">
            
            <div class="opd-grid" id="opdGrid">
                @foreach($listOpd as $opd)
                <div class="opd-checkbox-item" onclick="toggleCheckbox(this, {{ $opd->id }})">
                    <input type="checkbox" 
                           name="opd_ids[]" 
                           value="{{ $opd->id }}" 
                           id="opd_{{ $opd->id }}"
                           {{ in_array($opd->id, $assignedOpd) ? 'checked' : '' }}
                           onchange="updateSelectedCount()">
                    <label for="opd_{{ $opd->id }}">{{ $opd->nama }}</label>
                </div>
                @endforeach
            </div>
            
            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                <a href="{{ route('admin.pengguna') }}" class="btn-cancel">Kembali</a>
                <button type="submit" class="btn-save">💾 Simpan Assign OPD</button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateSelectedCount() {
        const checkboxes = document.querySelectorAll('input[name="opd_ids[]"]:checked');
        const count = checkboxes.length;
        document.getElementById('selectedCountDisplay').innerHTML = count + ' OPD dipilih';
    }
    
    function selectAll() {
        const checkboxes = document.querySelectorAll('input[name="opd_ids[]"]');
        checkboxes.forEach(cb => cb.checked = true);
        updateSelectedCount();
    }
    
    function deselectAll() {
        const checkboxes = document.querySelectorAll('input[name="opd_ids[]"]');
        checkboxes.forEach(cb => cb.checked = false);
        updateSelectedCount();
    }
    
    function toggleCheckbox(div, opdId) {
        const cb = document.getElementById('opd_' + opdId);
        cb.checked = !cb.checked;
        updateSelectedCount();
    }
    
    document.getElementById('assignForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const evaluatorId = document.getElementById('evaluator_id').value;
        const checkboxes = document.querySelectorAll('input[name="opd_ids[]"]:checked');
        const opdIds = Array.from(checkboxes).map(cb => cb.value);
        
        try {
            const response = await fetch(`{{ url("admin/evaluator") }}/${evaluatorId}/assign-opd`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ opd_ids: opdIds })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert(result.message);
                window.location.href = '{{ route("admin.pengguna") }}';
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            alert('Terjadi kesalahan: ' + error);
        }
    });
    
    // Initial count
    updateSelectedCount();
</script>

@endsection