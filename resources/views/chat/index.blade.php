@extends('layouts.app')
@section('title', 'Pesan')
@section('page-title', 'Chat')

@section('content')
<style>
.chat-list {
    display: flex;
    flex-direction: column;
}
.chat-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 20px;
    border-bottom: 1px solid rgba(255,255,255,.08);
    text-decoration: none;
    color: inherit;
    transition: background 0.2s;
}
.chat-item:hover {
    background: rgba(255,255,255,.05);
}
.chat-avatar {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 600;
    flex-shrink: 0;
}
.chat-info {
    flex: 1;
}
.chat-name {
    font-weight: 600;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.chat-last-message {
    font-size: 12px;
    color: #a1a1aa;
}
.chat-time {
    font-size: 11px;
    color: #6b7280;
}
.chat-badge {
    background: #ef4444;
    color: white;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 20px;
}
.section-title {
    padding: 12px 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #6b7280;
    background: rgba(0,0,0,0.2);
    border-bottom: 1px solid rgba(255,255,255,.05);
}
.role-badge {
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 20px;
    background: rgba(99,102,241,.2);
    color: #a5b4fc;
}
.empty-state {
    text-align: center;
    padding: 60px 20px;
}
.empty-state .icon {
    font-size: 48px;
    margin-bottom: 16px;
}
.empty-state .title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
}
.empty-state .desc {
    font-size: 13px;
    color: #6b7280;
}
</style>

<div class="card">
    <div class="card-header">
        <div class="card-title">💬 Pesan</div>
    </div>
    <div class="card-body" style="padding: 0;">
        
        {{-- DAFTAR PERCAKAPAN YANG SUDAH ADA --}}
        @if($percakapan->count() > 0)
            <div class="section-title">Percakapan Aktif</div>
            <div class="chat-list">
                @foreach($percakapan as $p)
                    @php $otherUser = $p->other_user; @endphp
                    @if($otherUser)
                    <a href="{{ route('chat.with', $otherUser->id) }}" class="chat-item">
                        <div class="chat-avatar">
                            {{ strtoupper(substr($otherUser->nama, 0, 1)) }}
                        </div>
                        <div class="chat-info">
                            <div class="chat-name">
                                {{ $otherUser->nama }}
                                <span class="role-badge">
                                    {{ $otherUser->role === 'admin' ? 'Admin' : 'Operator' }}
                                </span>
                            </div>
                            <div class="chat-last-message">
                                {{ $p->pesanTerakhir ? Str::limit($p->pesanTerakhir->isi, 50) : 'Belum ada pesan' }}
                            </div>
                        </div>
                        <div class="chat-time">
                            {{ $p->pesanTerakhir ? $p->pesanTerakhir->created_at->diffForHumans() : '-' }}
                        </div>
                        @if($p->unread_count > 0)
                            <div class="chat-badge">{{ $p->unread_count }}</div>
                        @endif
                    </a>
                    @endif
                @endforeach
            </div>
        @endif
        
        {{-- DAFTAR USER YANG BISA DIAJAK CHAT (BELUM PERNAH CHAT) --}}
        @if($newUsers->count() > 0)
            <div class="section-title">Mulai Percakapan Baru</div>
            <div class="chat-list">
                @foreach($newUsers as $u)
                    <a href="{{ route('chat.with', $u->id) }}" class="chat-item">
                        <div class="chat-avatar">
                            {{ strtoupper(substr($u->nama, 0, 1)) }}
                        </div>
                        <div class="chat-info">
                            <div class="chat-name">
                                {{ $u->nama }}
                                <span class="role-badge">
                                    {{ $u->role === 'admin' ? 'Admin' : 'Operator' }}
                                </span>
                            </div>
                            <div class="chat-last-message">Klik untuk memulai percakapan</div>
                        </div>
                        <div class="chat-time">→</div>
                    </a>
                @endforeach
            </div>
        @endif
        
        {{-- KOSONG --}}
        @if($percakapan->count() == 0 && $newUsers->count() == 0)
            <div class="empty-state">
                <div class="icon">💬</div>
                <div class="title">Belum Ada Percakapan</div>
                <div class="desc">
                    @if($user['role'] === 'admin')
                        Belum ada operator yang aktif atau terdaftar.
                    @else
                        Belum ada percakapan dengan admin.
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection