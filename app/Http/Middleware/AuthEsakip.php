<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthEsakip
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('user')) {
            return redirect()->route('login')->with('error', 'Silakan login untuk mengakses sistem.');
        }
        return $next($request);
    }
}
