@extends('layouts.app')
@section('title','Persetujuan Akun')
@section('page-title','Persetujuan Akun')
@section('page-sub','Kelola Pendaftaran Operator')

@section('content')
<div class="card">
  <div class="card-header">
    <div>
      <div class="card-title">✅ Persetujuan Pendaftaran Akun</div>
      <div class="card-subtitle">Operator yang menunggu aktivasi akun</div>
    </div>
    <span class="badge {{ $list->count() > 0 ? 'badge-warning' : 'badge-success' }}">
      {{ $list->count() }} Menunggu
    </span>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>Nama</th><th>Perangkat Daerah</th><th>Tanggal Daftar</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        @forelse($list as $u)
        <tr>
          <td>
            <div style="font-weight:600">{{ $u->nama }}</div>
            <div style="font-size:11px;color:var(--text-3)">@{{ $u->username }}</div>
          </td>
          <td>{{ $u->nama_daerah ?? '—' }}</td>
          <td style="font-size:12px">{{ \Carbon\Carbon::parse($u->created_at)->format('d M Y') }}</td>
          <td>
            <div style="display:flex;gap:6px">
              <form method="POST" action="{{ route('admin.persetujuan.proses', $u->id) }}" style="display:inline">
                @csrf
                <input type="hidden" name="status" value="disetujui">
                <button type="submit" class="btn btn-success btn-sm"
                        onclick="return confirm('Setujui dan aktifkan akun ini?')">
                  ✅ Setujui
                </button>
              </form>
              <form method="POST" action="{{ route('admin.persetujuan.proses', $u->id) }}" style="display:inline">
                @csrf
                <input type="hidden" name="status" value="ditolak">
                <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Tolak pendaftaran ini?')">
                  ❌ Tolak
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4">
            <div class="empty-state">
              <span class="icon">✅</span>
              <h3>Tidak ada pendaftaran baru</h3>
              <p>Semua pendaftaran sudah diproses</p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
