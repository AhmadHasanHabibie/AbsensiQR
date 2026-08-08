<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OperatorMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Belum login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Role bukan operator
        if (Auth::user()->role !== 'operator') {
            return match (Auth::user()->role) {
                'admin'   => redirect()->route('admin.dashboard'),
                'teacher' => redirect()->route('guru.dashboard'),
                'student' => redirect()->route('siswa.dashboard'),
                default   => redirect()->route('login'),
            };
        }

        return $next($request);
    }
}
