<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {

            if (Auth::guard($guard)->check()) {

                $user = Auth::guard($guard)->user();

                return match ($user->role) {
                    'super_admin' => session('super_admin_verified') === true
                        ? redirect()->route('superadmin.dashboard')
                        : redirect()->route('superadmin.pin.verify'),
                    'admin'       => redirect()->route('admin.dashboard'),
                    'teacher'     => redirect()->route('guru.dashboard'),
                    'student'     => redirect()->route('siswa.dashboard'),
                    'operator'    => redirect()->route('operator.dashboard'),
                    'piket'       => redirect()->route('piket.dashboard'),
                    default       => redirect('/'),
                };
            }
        }

        return $next($request);
    }
}