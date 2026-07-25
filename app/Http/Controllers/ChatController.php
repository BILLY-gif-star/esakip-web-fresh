<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Percakapan;
use App\Models\Pesan;

class ChatController extends Controller
{
    /**
     * Halaman utama chat (daftar percakapan)
     */
    public function index()
    {
        $user = Session::get('user');

        if (!$user) {
            return redirect()->route('login');
        }

        // 1. Ambil percakapan yang sudah ada
        $percakapan = Percakapan::where('opd_user_id', $user['id'])
            ->orWhere('admin_user_id', $user['id'])
            ->with(['opdUser', 'adminUser', 'pesanTerakhir'])
            ->orderBy('pesan_terakhir_at', 'desc')
            ->get()
            ->map(function ($p) use ($user) {
                $otherUser = $p->opd_user_id == $user['id'] ? $p->adminUser : $p->opdUser;
                $p->other_user = $otherUser;
                $p->unread_count = Pesan::where('percakapan_id', $p->id)
                    ->where('pengirim_id', '!=', $user['id'])
                    ->whereNull('dibaca_at')
                    ->count();
                return $p;
            })
            ->filter(function ($p) use ($user) {
                // ⭐ Jangan tampilkan percakapan dengan diri sendiri
                return $p->other_user != null && $p->other_user->id != $user['id'];
            });

        // 2. Ambil semua user yang bisa diajak chat
        $otherUsers = collect();

        if ($user['role'] === 'admin') {
            $otherUsers = DB::table('pengguna')
                ->where('role', 'operator')
                ->where('id', '!=', $user['id'])
                ->where('is_active', 1)
                ->where('status_daftar', 'disetujui')
                ->select('id', 'nama', 'role')
                ->get();
        } elseif ($user['role'] === 'evaluator') {
            // Evaluator bisa chat dengan admin
            $otherUsers = DB::table('pengguna')
                ->where('role', 'admin')
                ->where('id', '!=', $user['id'])
                ->where('is_active', 1)
                ->where('status_daftar', 'disetujui')
                ->select('id', 'nama', 'role')
                ->get();
        } else {
            // Operator
            $otherUsers = DB::table('pengguna')
                ->where('id', '!=', $user['id'])
                ->where('is_active', 1)
                ->where('status_daftar', 'disetujui')
                ->whereIn('role', ['admin', 'operator'])
                ->select('id', 'nama', 'role')
                ->get();
        }

        // 3. Filter user yang sudah ada percakapannya
        $existingUserIds = $percakapan->pluck('other_user.id')->filter()->toArray();
        $newUsers = $otherUsers->whereNotIn('id', $existingUserIds);

        return view('chat.index', compact('percakapan', 'newUsers', 'user'));
    }

    /**
     * Chat dengan user tertentu
     */
    public function chatWith($userId)
    {
        $user = Session::get('user');

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user['id'] == $userId) {
            return redirect()->route('chat.index')->with('error', 'Tidak bisa chat dengan diri sendiri.');
        }

        $targetUser = DB::table('pengguna')->where('id', $userId)->first();

        if (!$targetUser) {
            return redirect()->route('chat.index')->with('error', 'User tidak ditemukan');
        }

        $percakapan = $this->getOrCreatePercakapan($user['id'], $targetUser->id, $user['role'], $targetUser->role);

        // Ambil semua pesan awal
        $pesan = DB::table('pesan')
            ->where('percakapan_id', $percakapan->id)
            ->orderBy('created_at', 'asc')
            ->select('id', 'isi', 'pengirim_id', 'created_at', 'dibaca_at')
            ->get();

        // Tandai pesan masuk sebagai dibaca
        DB::table('pesan')
            ->where('percakapan_id', $percakapan->id)
            ->where('pengirim_id', '!=', $user['id'])
            ->whereNull('dibaca_at')
            ->update(['dibaca_at' => now()]);

        return view('chat.room', compact('percakapan', 'targetUser', 'pesan', 'user'));
    }

    /**
     * Helper: cari atau buat percakapan
     */
    private function getOrCreatePercakapan($userId1, $userId2, $role1, $role2)
    {
        // Cari percakapan yang sudah ada
        $percakapan = Percakapan::where(function ($q) use ($userId1, $userId2) {
            $q->where('opd_user_id', $userId1)->where('admin_user_id', $userId2);
        })->orWhere(function ($q) use ($userId1, $userId2) {
            $q->where('opd_user_id', $userId2)->where('admin_user_id', $userId1);
        })->first();

        if ($percakapan) {
            // Update pesan_terakhir_at
            $percakapan->update(['pesan_terakhir_at' => now()]);
            return $percakapan;
        }

        // Tentukan opd_user_id dan admin_user_id
        $opdId = null;
        $adminId = null;
        $isOperatorChat = false;

        if (($role1 === 'operator' && $role2 === 'operator') || 
            ($role1 === 'evaluator' && $role2 === 'evaluator')) {
            // Chat antar operator atau antar evaluator
            $opdId = $userId1;
            $adminId = $userId2;
            $isOperatorChat = true;
        } elseif ($role1 === 'operator' && $role2 === 'admin') {
            $opdId = $userId1;
            $adminId = $userId2;
            $isOperatorChat = false;
        } elseif ($role1 === 'admin' && $role2 === 'operator') {
            $opdId = $userId2;
            $adminId = $userId1;
            $isOperatorChat = false;
        } elseif ($role1 === 'evaluator' && $role2 === 'admin') {
            // Evaluator chat dengan admin
            $opdId = $userId1;
            $adminId = $userId2;
            $isOperatorChat = false;
        } elseif ($role1 === 'admin' && $role2 === 'evaluator') {
            $opdId = $userId2;
            $adminId = $userId1;
            $isOperatorChat = false;
        } else {
            // Default
            $opdId = $userId1;
            $adminId = $userId2;
        }

        // Cek sekali lagi sebelum insert (untuk menghindari race condition)
        $existingPercakapan = Percakapan::where('opd_user_id', $opdId)
            ->where('admin_user_id', $adminId)
            ->first();

        if ($existingPercakapan) {
            $existingPercakapan->update(['pesan_terakhir_at' => now()]);
            return $existingPercakapan;
        }

        // Buat percakapan baru
        return Percakapan::create([
            'opd_user_id'      => $opdId,
            'admin_user_id'    => $adminId,
            'is_operator_chat' => $isOperatorChat,
            'pesan_terakhir_at' => now(),
        ]);
    }

    /**
     * Kirim pesan — selalu return JSON
     */
    public function send(Request $request, $userId)
    {
        $user = Session::get('user');

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'isi' => 'required|string|max:1000',
        ]);

        $targetUser = DB::table('pengguna')->where('id', $userId)->first();

        if (!$targetUser) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
        }

        $percakapan = $this->getOrCreatePercakapan($user['id'], $targetUser->id, $user['role'], $targetUser->role);

        $pesan = Pesan::create([
            'percakapan_id' => $percakapan->id,
            'pengirim_id'   => $user['id'],
            'isi'           => $request->isi,
        ]);

        $percakapan->update(['pesan_terakhir_at' => now()]);

        $this->kirimNotifikasiChat($targetUser->id, $user['nama'], $percakapan->id);

        return response()->json([
            'success' => true,
            'pesan'   => [
                'id'          => $pesan->id,
                'isi'         => $pesan->isi,
                'pengirim_id' => $pesan->pengirim_id,
                'created_at'  => $pesan->created_at->setTimezone('Asia/Makassar')->toISOString(),
                'dibaca_at'   => null,
            ]
        ]);
    }

    /**
     * Ambil pesan — support parameter ?after=id untuk hanya ambil pesan baru
     */
    public function messages($percakapanId)
    {
        $user = Session::get('user');

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $percakapan = Percakapan::find($percakapanId);

        if (!$percakapan || ($percakapan->opd_user_id != $user['id'] && $percakapan->admin_user_id != $user['id'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $afterId = (int) request()->query('after', 0);

        $query = DB::table('pesan')
            ->where('percakapan_id', $percakapanId)
            ->orderBy('created_at', 'asc')
            ->select('id', 'isi', 'pengirim_id', 'created_at', 'dibaca_at');

        // Jika ada ?after=X, hanya ambil pesan yang lebih baru
        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        }

        $pesan = $query->get();

        // Tandai pesan masuk sebagai dibaca (hanya jika ada pesan baru)
        if ($pesan->isNotEmpty()) {
            DB::table('pesan')
                ->where('percakapan_id', $percakapanId)
                ->where('pengirim_id', '!=', $user['id'])
                ->whereNull('dibaca_at')
                ->update(['dibaca_at' => now()]);
        }

        return response()->json([
            'pesan' => $pesan->map(function ($p) {
                return [
                    'id'          => $p->id,
                    'isi'         => $p->isi,
                    'pengirim_id' => $p->pengirim_id,
                    'created_at'  => $p->created_at,
                    'dibaca_at'   => $p->dibaca_at,
                ];
            })
        ]);
    }

    /**
     * Bersihkan semua pesan dalam percakapan
     */
    public function clearChat($percakapanId)
    {
        $user = Session::get('user');

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $percakapan = Percakapan::find($percakapanId);

        if (!$percakapan || ($percakapan->opd_user_id != $user['id'] && $percakapan->admin_user_id != $user['id'])) {
            return response()->json(['success' => false, 'message' => 'Tidak diizinkan'], 403);
        }

        DB::table('pesan')->where('percakapan_id', $percakapanId)->delete();

        $percakapan->update(['pesan_terakhir_at' => null]);

        return response()->json(['success' => true, 'message' => 'Chat berhasil dibersihkan']);
    }

    /**
     * Tandai pesan sebagai dibaca
     */
    public function markAsRead($percakapanId)
    {
        $user = Session::get('user');

        $updated = Pesan::where('percakapan_id', $percakapanId)
            ->where('pengirim_id', '!=', $user['id'])
            ->whereNull('dibaca_at')
            ->update(['dibaca_at' => now()]);

        return response()->json(['success' => true, 'updated' => $updated]);
    }

    /**
     * Jumlah pesan belum dibaca
     */
    public function unreadCount()
    {
        $user = Session::get('user');

        $count = DB::table('pesan')
            ->join('percakapan', 'pesan.percakapan_id', '=', 'percakapan.id')
            ->where(function ($q) use ($user) {
                $q->where('percakapan.opd_user_id', $user['id'])
                  ->orWhere('percakapan.admin_user_id', $user['id']);
            })
            ->where('pesan.pengirim_id', '!=', $user['id'])
            ->whereNull('pesan.dibaca_at')
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    /**
     * Kirim notifikasi chat ke penerima
     */
    private function kirimNotifikasiChat($penerimaId, $pengirimNama, $percakapanId)
    {
        DB::table('notifikasi')->insert([
            'user_id'    => $penerimaId,
            'judul'      => 'Pesan Baru',
            'pesan'      => "Anda mendapat pesan baru dari {$pengirimNama}",
            'tipe'       => 'chat',
            'ikon'       => '💬',
            'warna'      => 'blue',
            'url'        => '/chat/with/' . $penerimaId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}