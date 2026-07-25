<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class NotifikasiController extends Controller
{
    public function ambil()
    {
        $user = Session::get('user');
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        // Gunakan Query Builder langsung, bukan Model
        $notifikasi = DB::table('notifikasi')
            ->where('user_id', $user['id'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $item->sudah_dibaca = !is_null($item->dibaca_at);
                $item->waktu = $item->created_at ? \Carbon\Carbon::parse($item->created_at)->diffForHumans() : '';
                return $item;
            });
        
        $belumDibaca = DB::table('notifikasi')
            ->where('user_id', $user['id'])
            ->whereNull('dibaca_at')
            ->count();
        
        return response()->json([
            'notifikasi' => $notifikasi,
            'belum_dibaca' => $belumDibaca
        ]);
    }
    
    public function baca($id)
    {
        $user = Session::get('user');
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        DB::table('notifikasi')
            ->where('id', $id)
            ->where('user_id', $user['id'])
            ->update(['dibaca_at' => now()]);
        
        return response()->json(['success' => true]);
    }
    
    public function bacaSemua()
    {
        $user = Session::get('user');
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        DB::table('notifikasi')
            ->where('user_id', $user['id'])
            ->whereNull('dibaca_at')
            ->update(['dibaca_at' => now()]);
        
        return response()->json(['success' => true]);
    }
}