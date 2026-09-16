@extends('layouts.app')

@section('title', 'LHE AKIP')
@section('page-title', 'Laporan Hasil Evaluasi AKIP')
@section('page-sub', 'Tahun ' . $tahun)

@section('topbar-actions')
  <a href="{{ route('lhe-akip.create') }}" class="btn btn-gold btn-sm">
    ➕ Buat LHE Baru
  </a>
@endsection

@section('content')

<div class="card">
  <div class="card-header">
    <div>
      <div class="card-title">Daftar LHE AKIP per OPD</div>
      <div class="card-subtitle">{{ $data->count() }} OPD tahun {{ $tahun }} — nilai diambil otomatis dari hasil Evaluasi Kinerja</div>
    </div>

    <form method="GET" style="display:flex;gap:8px;align-items:center;">
      <select name="tahun" class="form-control" style="width:auto;" onchange="this.form.submit()">
        @foreach($daftarTahun as $th)
          <option value="{{ $th }}" {{ $th == $tahun ? 'selected' : '' }}>{{ $th }}</option>
        @endforeach
      </select>
    </form>
  </div>

  <div class="card-body" style="padding:0;">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>OPD</th>
            <th>Nomor Surat</th>
            <th>Total Nilai (live)</th>
            <th>Kategori</th>
            <th>Status Surat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($data as $i => $row)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td>{{ $row->nama_opd }}</td>
              <td>{{ $row->nomor_surat }}</td>
              <td>
                <strong>{{ number_format($row->total_nilai, 2, ',', '.') }}</strong>
                @if(!$row->ada_data_lke)
                  <span class="badge badge-warning" style="margin-left:6px;">Belum ada nilai LKE</span>
                @endif
              </td>
              <td><span class="badge badge-{{ \App\Models\LheAkip::warnaKategori($row->kategori) }}">{{ $row->kategori }}</span></td>
              <td>
                @if($row->status === 'final')
                  <span class="badge badge-success">Final</span>
                @else
                  <span class="badge badge-warning">Draft</span>
                @endif
              </td>
              <td style="display:flex;gap:6px;">
                <a href="{{ route('lhe-akip.cetak', $row->id) }}" class="btn btn-gold btn-sm" target="_blank">🖨️ Cetak</a>
                <a href="{{ route('lhe-akip.edit', $row->id) }}" class="btn btn-outline btn-sm">✏️ Edit</a>
                <form method="POST" action="{{ route('lhe-akip.destroy', $row->id) }}"
                      onsubmit="return confirm('Hapus LHE AKIP OPD ini?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7">
                <div class="empty-state">
                  <span class="icon">📋</span>
                  <h3>Belum ada LHE AKIP dibuat untuk tahun {{ $tahun }}</h3>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection