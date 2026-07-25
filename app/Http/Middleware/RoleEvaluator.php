<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class RoleEvaluator
{
    public function handle($request, Closure $next)
    {
        $user = Session::get('user');
        
        if (!$user || !in_array($user['role'], ['admin', 'evaluator'])) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Anda bukan evaluator.');
        }
        
        return $next($request);
    }
}