<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RoleAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Admin.');
        }
        return $next($request);
    }
}
