<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Belum login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Role tidak sesuai
        if (Auth::user()->role !== $role) {
            // Super Administrator memiliki Full Access terhadap seluruh modul aplikasi
            if (Auth::user()->isSuperAdmin()) {
                return $next($request);
            }

            // Guru Piket alias (piket / guru_piket)
            if (($role === 'piket' || $role === 'guru_piket') && Auth::user()->isGuruPiket()) {
                return $next($request);
            }

            if (Auth::user()->role === 'operator') {
                return redirect()->route('operator.dashboard');
            }
            if (Auth::user()->isGuruPiket()) {
                return redirect()->route('piket.dashboard');
            }
            abort(403, 'Anda tidak memiliki hak akses.');
        }

        return $next($request);
    }
}