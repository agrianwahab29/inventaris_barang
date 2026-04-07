<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

        if ($role === 'admin' && !$user->isAdmin()) {
            // Return 403 for API/testing requests, redirect for web requests
            if ($request->expectsJson() || $request->ajax()) {
                abort(403, 'Akses ditolak. Anda tidak memiliki akses ke halaman ini.');
            }
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        if ($role === 'pengguna' && !($user->isAdmin() || $user->isPengguna())) {
            return redirect('/login');
        }

        return $next($request);
    }
}
