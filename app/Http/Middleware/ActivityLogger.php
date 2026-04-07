<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Log activity for authenticated users
        if (Auth::check()) {
            $user = Auth::user();
            $action = $request->method() . ' ' . $request->path();
            
            // Only log important actions (POST, PUT, DELETE, and specific GET)
            $shouldLog = $this->shouldLog($request);
            
            if ($shouldLog) {
                Log::info('User Activity', [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'action' => $action,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'timestamp' => now()->toDateTimeString(),
                ]);
            }
        }
        
        // Log failed login attempts
        if ($request->path() === 'login' && $request->method() === 'POST') {
            Log::info('Login Attempt', [
                'username' => $request->input('username'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ]);
        }

        return $next($request);
    }
    
    /**
     * Determine if the request should be logged.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function shouldLog(Request $request): bool
    {
        // Log all POST, PUT, DELETE
        if (in_array($request->method(), ['POST', 'PUT', 'DELETE'])) {
            // Skip logging password in login
            if ($request->path() === 'login') {
                return false;
            }
            return true;
        }
        
        // Log specific GET actions
        $loggedPaths = [
            'logout',
            'export',
            'generate',
        ];
        
        foreach ($loggedPaths as $path) {
            if (str_contains($request->path(), $path)) {
                return true;
            }
        }
        
        return false;
    }
}
