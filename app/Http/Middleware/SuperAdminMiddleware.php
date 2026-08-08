<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request for Super Administrator routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->isSuperAdmin()) {
            abort(403, 'Akses Halaman Tidak Diizinkan.');
        }

        // Jika rute saat ini adalah halaman verifikasi PIN
        if ($request->routeIs('superadmin.pin.*')) {
            return $next($request);
        }

        // Jika PIN belum terverifikasi, alihkan ke halaman Verifikasi PIN
        if (session('super_admin_verified') !== true) {
            return redirect()->route('superadmin.pin.verify');
        }

        return $next($request);
    }
}
