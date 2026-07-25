<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Services\NotifikasiService;

class AuthController extends Controller
{
    // FIX #2: Kirim $listOpd ke view agar tidak perlu query di Blade
    public function showLogin()
    {
        if (Session::has('user')) return redirect()->route('dashboard');

        $listOpd = DB::table('perangkat_daerah')->orderBy('nama')->get();

        return view('auth.login', compact('listOpd'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = DB::table('pengguna as p')
            ->leftJoin('perangkat_daerah as pd', 'p.perangkat_daerah_id', '=', 'pd.id')
            ->select('p.*', 'pd.nama as nama_daerah')
            ->where('p.username', $request->username)
            ->first();

        if (!$user) {
            return back()->withErrors(['username' => 'Username tidak ditemukan.'])->withInput();
        }

        $valid = false;
        try {
            if (password_verify($request->password, $user->password)) {
                $valid = true;
            }
        } catch (\Exception $e) {
            $valid = false;
        }

        if (!$valid) {
            return back()->withErrors(['password' => 'Password salah.'])->withInput();
        }

        if (($user->status_daftar ?? 'disetujui') === 'menunggu') {
            return back()->withErrors(['username' => 'Akun menunggu persetujuan Admin Biro Organisasi.'])->withInput();
        }

        if (($user->status_daftar ?? 'disetujui') === 'ditolak') {
            return back()->withErrors(['username' => 'Akun ditolak. Hubungi Admin Biro Organisasi.'])->withInput();
        }

        if (!$user->is_active) {
            return back()->withErrors(['username' => 'Akun dinonaktifkan. Hubungi Admin.'])->withInput();
        }

        // Update password ke format Laravel bcrypt jika perlu
        $laravelHash = password_hash($request->password, PASSWORD_BCRYPT, ['cost' => 12]);
        DB::table('pengguna')->where('id', $user->id)->update([
            'password'   => $laravelHash,
            'last_login' => now(),
        ]);

        Session::put('user', [
            'id'          => $user->id,
            'username'    => $user->username,
            'nama'        => $user->nama,
            'role'        => $user->role,
            'daerah_id'   => $user->perangkat_daerah_id,
            'nama_daerah' => $user->nama_daerah,
        ]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Session::forget('user');
        return redirect()->route('login');
    }

    public function daftar(Request $request)
    {
        $request->validate([
            'nama'       => 'required|string|max:200',
            'username'   => 'required|string|min:4|max:100',
            'password'   => 'required|string|min:6',
            'konfirmasi' => 'required|same:password',
            'daerah_id'  => 'required|integer',
        ], [
            'nama.required'      => 'Nama lengkap wajib diisi.',
            'username.required'  => 'Username wajib diisi.',
            'username.min'       => 'Username minimal 4 karakter.',
            'password.min'       => 'Password minimal 6 karakter.',
            'konfirmasi.same'    => 'Konfirmasi password tidak sama.',
            'daerah_id.required' => 'Pilih Perangkat Daerah.',
        ]);

        // ✅ CEK USERNAME SUDAH TERPAKAI (yang Anda minta sudah ada di sini)
        $existing = DB::table('pengguna')->where('username', $request->username)->first();
        if ($existing) {
            return back()->withErrors(['daftar_username' => 'Username sudah digunakan.'])->withInput();
        }

        $opd     = DB::table('perangkat_daerah')->where('id', $request->daerah_id)->first();
        $namaOpd = $opd->nama ?? 'Unknown';

        DB::table('pengguna')->insert([
            'username'            => $request->username,
            'password'            => password_hash($request->password, PASSWORD_BCRYPT, ['cost' => 12]),
            'nama'                => $request->nama,
            'role'                => 'operator',
            'perangkat_daerah_id' => $request->daerah_id,
            'is_active'           => 0,
            'status_daftar'       => 'menunggu',
            'created_at'          => now(),
        ]);

        // Notifikasi ke semua admin
        $adminUsers = DB::table('pengguna')->where('role', 'admin')->get();
        foreach ($adminUsers as $admin) {
            DB::table('notifikasi')->insert([
                'user_id'    => $admin->id,
                'judul'      => 'Pendaftaran Operator Baru',
                'pesan'      => "Operator {$request->nama} dari OPD {$namaOpd} telah mendaftar. Silakan lakukan persetujuan.",
                'tipe'       => 'operator_daftar',
                'ikon'       => '👤',
                'warna'      => 'amber',
                'url'        => route('admin.persetujuan'),
                'nama_opd'   => $namaOpd,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('daftar_sukses', 'Pendaftaran berhasil! Tunggu persetujuan Admin Biro Organisasi.');
    }
}