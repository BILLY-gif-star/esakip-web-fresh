@extends('layouts.app')
@section('title', 'Chat')
@section('page-title', 'CHAT')
@section('page-sub', 'Percakapan dengan ' . ($targetUser->nama ?? ''))

@section('content')
<style>
    .chat-room-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 180px);
        min-height: 500px;
        background: rgba(26, 26, 36, 0.6);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        overflow: hidden;
    }

    .chat-header {
        padding: 16px 20px;
        background: rgba(20, 20, 28, 0.8);
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
    }

    .chat-header-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 600;
        color: #fff;
        flex-shrink: 0;
    }

    .chat-header-info { flex: 1; min-width: 0; }

    .chat-header-info h3 {
        font-size: 15px;
        font-weight: 600;
        color: #fff;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .chat-header-info p {
        font-size: 11px;
        color: #a1a1aa;
        margin: 2px 0 0;
    }

    .chat-clear-btn {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.25);
        border-radius: 10px;
        padding: 8px 14px;
        color: #f87171;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .chat-clear-btn:hover { background: rgba(239, 68, 68, 0.2); }
    .chat-clear-btn:disabled { opacity: 0.5; cursor: not-allowed; }

    .chat-back-btn {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        padding: 8px 14px;
        color: #fff;
        text-decoration: none;
        font-size: 12px;
        transition: all 0.2s;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .chat-back-btn:hover { background: rgba(99, 102, 241, 0.2); }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .message {
        display: flex;
        max-width: 70%;
    }

    .message-own  { align-self: flex-end; }
    .message-other { align-self: flex-start; }

    .message-bubble {
        padding: 10px 14px;
        border-radius: 18px;
        font-size: 13px;
        line-height: 1.5;
        word-wrap: break-word;
        word-break: break-word;
    }

    .message-own .message-bubble {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff;
        border-bottom-right-radius: 4px;
    }

    .message-other .message-bubble {
        background: rgba(255, 255, 255, 0.08);
        color: #e4e4e7;
        border-bottom-left-radius: 4px;
    }

    .message-info {
        font-size: 10px;
        color: #52525b;
        margin-top: 4px;
        padding: 0 4px;
    }

    .message-own .message-info { text-align: right; }

    .chat-input-area {
        padding: 16px 20px;
        background: rgba(20, 20, 28, 0.8);
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        gap: 12px;
        flex-shrink: 0;
    }

    .chat-input {
        flex: 1;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px;
        padding: 12px 18px;
        color: #fff;
        font-size: 13px;
        outline: none;
        transition: all 0.2s;
    }

    .chat-input::placeholder { color: rgba(255,255,255,0.3); }
    .chat-input:focus {
        border-color: #6366f1;
        background: rgba(255, 255, 255, 0.08);
    }

    .chat-send-btn {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border: none;
        border-radius: 24px;
        padding: 0 24px;
        color: #fff;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .chat-send-btn:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }

    .chat-send-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    .empty-messages {
        text-align: center;
        padding: 60px 20px;
        color: #52525b;
        margin: auto;
    }

    .empty-messages .icon { font-size: 48px; margin-bottom: 12px; }
    .empty-messages p { font-size: 13px; }

    @media (max-width: 768px) {
        .message { max-width: 88%; }
        .chat-clear-btn span { display: none; }
    }
</style>

<div class="chat-room-container">

    {{-- ── HEADER ── --}}
    <div class="chat-header">
        <div class="chat-header-avatar">
            {{ strtoupper(substr($targetUser->nama ?? 'U', 0, 1)) }}
        </div>
        <div class="chat-header-info">
            <h3>{{ $targetUser->nama ?? 'Unknown' }}</h3>
            <p>{{ $targetUser->role === 'admin' ? 'Admin' : 'Operator OPD' }}</p>
        </div>
        <button id="clearChatBtn" class="chat-clear-btn" title="Bersihkan Chat">
            🗑️ <span>Bersihkan</span>
        </button>
        <a href="{{ route('chat.index') }}" class="chat-back-btn">← Kembali</a>
    </div>

    {{-- ── AREA PESAN ── --}}
    <div class="chat-messages" id="chatMessages">
        @forelse($pesan as $p)
            <div class="message {{ $p->pengirim_id == $user['id'] ? 'message-own' : 'message-other' }}"
                 data-id="{{ $p->id }}">
                <div>
                    <div class="message-bubble">{{ $p->isi }}</div>
                    <div class="message-info">
                        {{ \Carbon\Carbon::parse($p->created_at)->setTimezone('Asia/Makassar')->format('H:i') }}
                        @if($p->pengirim_id == $user['id'])
                            {{ $p->dibaca_at ? '✓✓' : '✓' }}
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-messages" id="emptyState">
                <div class="icon">💬</div>
                <p>Belum ada pesan. Mulai percakapan sekarang!</p>
            </div>
        @endforelse
    </div>

    {{-- ── INPUT AREA ── --}}
    <div class="chat-input-area">
        <input type="text" id="messageInput" class="chat-input"
               placeholder="Ketik pesan..." autocomplete="off">
        <button id="sendBtn" class="chat-send-btn">Kirim</button>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // ── KONSTANTA ─────────────────────────────────────────────
    const chatMessages  = document.getElementById('chatMessages');
    const messageInput  = document.getElementById('messageInput');
    const sendBtn       = document.getElementById('sendBtn');
    const clearChatBtn  = document.getElementById('clearChatBtn');

    const currentUserId = {{ $user['id'] }};

    // Path relatif — hindari masalah http vs https / APP_URL mismatch
    const SEND_URL     = '/chat/send/{{ $targetUser->id }}';
    const MESSAGES_URL = '/chat/messages/{{ $percakapan->id }}';
    const CLEAR_URL    = '/chat/clear/{{ $percakapan->id }}';
    const CSRF         = '{{ csrf_token() }}';

    // Ambil lastMessageId dari pesan yang sudah di-render server
    let lastMessageId = 0;
    document.querySelectorAll('#chatMessages .message').forEach(el => {
        const id = parseInt(el.getAttribute('data-id') || '0');
        if (id > lastMessageId) lastMessageId = id;
    });

    // ── HELPERS ───────────────────────────────────────────────

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatTime(raw) {
        if (!raw) return '';
        // ISO string dari response kirim pesan
        if (raw.includes('T') || raw.includes('+')) {
            return new Date(raw).toLocaleTimeString('id-ID', {
                hour: '2-digit', minute: '2-digit',
                hour12: false, timeZone: 'Asia/Makassar'
            });
        }
        // "YYYY-MM-DD HH:mm:ss" dari DB
        const parts = raw.split(' ');
        if (parts.length === 2) return parts[1].substring(0, 5);
        return raw.substring(0, 5);
    }

    function showEmpty() {
        chatMessages.innerHTML = `
            <div class="empty-messages" id="emptyState">
                <div class="icon">💬</div>
                <p>Belum ada pesan. Mulai percakapan sekarang!</p>
            </div>`;
    }

    function removeEmpty() {
        const el = document.getElementById('emptyState');
        if (el) el.remove();
    }

    function appendMessage(pesan, isOwn) {
        // Cegah duplikat
        if (document.querySelector(`.message[data-id="${pesan.id}"]`)) return;

        removeEmpty();

        const div = document.createElement('div');
        div.className = `message ${isOwn ? 'message-own' : 'message-other'}`;
        div.setAttribute('data-id', pesan.id);

        const time    = formatTime(pesan.created_at);
        const centang = isOwn ? (pesan.dibaca_at ? ' ✓✓' : ' ✓') : '';

        div.innerHTML = `
            <div>
                <div class="message-bubble">${escapeHtml(pesan.isi)}</div>
                <div class="message-info">${time}${centang}</div>
            </div>`;

        chatMessages.appendChild(div);
    }

    // ── KIRIM PESAN ───────────────────────────────────────────

    function sendMessage() {
        const isi = messageInput.value.trim();
        if (!isi) return;

        const backup          = isi;
        messageInput.value    = '';
        messageInput.disabled = true;
        sendBtn.disabled      = true;

        fetch(SEND_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept':       'application/json'
            },
            body: JSON.stringify({ isi })
        })
        .then(res => {
            const ct = res.headers.get('content-type') || '';
            if (!ct.includes('application/json')) {
                throw new Error('Response bukan JSON (status ' + res.status + ')');
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                appendMessage(data.pesan, true);
                lastMessageId = data.pesan.id;
                scrollToBottom();
            } else {
                messageInput.value = backup;
                alert('Gagal mengirim: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(err => {
            console.error('Send error:', err);
            messageInput.value = backup;
            alert('Gagal mengirim pesan: ' + err.message);
        })
        .finally(() => {
            messageInput.disabled = false;
            sendBtn.disabled      = false;
            messageInput.focus();
        });
    }

    sendBtn.addEventListener('click', e => { e.preventDefault(); sendMessage(); });
    messageInput.addEventListener('keypress', e => {
        if (e.key === 'Enter') { e.preventDefault(); sendMessage(); }
    });

    // ── AUTO REFRESH — hanya ambil pesan baru ─────────────────

    let isRefreshing = false;

    setInterval(() => {
        if (isRefreshing) return; // skip jika request sebelumnya belum selesai
        isRefreshing = true;

        fetch(`${MESSAGES_URL}?after=${lastMessageId}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.pesan || data.pesan.length === 0) return;
            data.pesan.forEach(p => {
                appendMessage(p, p.pengirim_id == currentUserId);
                if (p.id > lastMessageId) lastMessageId = p.id;
            });
            scrollToBottom();
        })
        .catch(err => console.error('Refresh error:', err))
        .finally(() => { isRefreshing = false; });
    }, 5000); // setiap 5 detik

    // ── BERSIHKAN CHAT ────────────────────────────────────────

    clearChatBtn.addEventListener('click', function () {
        if (!confirm('Yakin ingin menghapus semua pesan? Tindakan ini tidak bisa dibatalkan.')) return;

        clearChatBtn.disabled    = true;
        clearChatBtn.textContent = '⏳ Membersihkan...';

        fetch(CLEAR_URL, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept':       'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showEmpty();
                lastMessageId = 0;
            } else {
                alert('Gagal: ' + data.message);
            }
        })
        .catch(err => alert('Terjadi kesalahan: ' + err.message))
        .finally(() => {
            clearChatBtn.disabled  = false;
            clearChatBtn.innerHTML = '🗑️ <span>Bersihkan</span>';
        });
    });

    // ── INIT ──────────────────────────────────────────────────
    scrollToBottom();
    messageInput.focus();
</script>
@endsection