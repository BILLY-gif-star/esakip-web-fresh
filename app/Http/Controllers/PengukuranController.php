<?php
// app/Http/Controllers/PengukuranController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PengukuranController extends Controller
{
    public function index(Request $request)
    {
        $user = Session::get('user');
        return view('pengukuran.minimal', compact('user'));
    }
    
    public function simpan(Request $request)
    {
        return back()->with('success', 'Data berhasil disimpan');
    }
    
    public function download($ikuId)
    {
        abort(404, 'Fitur dalam pengembangan');
    }
}