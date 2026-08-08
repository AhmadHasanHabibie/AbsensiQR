<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\MaintenanceService;
use Symfony\Component\HttpFoundation\Response;

class PreventRequestsDuringMaintenance
{
    /**
     * Handle an incoming request when system maintenance is active.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $maintenanceService = app(MaintenanceService::class);

        if ($maintenanceService->isMaintenanceActive()) {
            // URL yang selalu dikecualikan agar Super Admin bisa login & kelola maintenance
            if (
                $request->is('login') ||
                $request->is('logout') ||
                $request->is('superadmin*')
            ) {
                return $next($request);
            }

            // Jika pengguna terautentikasi adalah Super Administrator
            if (Auth::check() && Auth::user()->isSuperAdmin()) {
                return $next($request);
            }

            // Bagi pengguna biasa (Admin Sekolah, Guru, Operator, Piket, Siswa, Guest)
            $details = $maintenanceService->getMaintenanceDetails();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $details['message'] ?? 'Sistem sedang dalam pemeliharaan rutin.',
                    'status'  => 'maintenance'
                ], 503);
            }

            return response()->view('errors.503', [
                'maintenanceDetails' => $details
            ], 503);
        }

        return $next($request);
    }
}
