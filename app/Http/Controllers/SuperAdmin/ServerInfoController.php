<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServerInfoController extends Controller
{
    /**
     * Halaman Informasi Server & Pengguna.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Audit Log
        ActivityLog::log(
            'Masuk Informasi Server',
            'Informasi Server',
            "Pengguna {$user->name} melihat Informasi Server & Statistik Pengguna.",
            $user
        );

        // System Technical Metrics
        $serverInfo = [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'bootstrap_version' => '5.3.7',
            'timezone' => config('app.timezone'),
            'environment' => config('app.env'),
            'server_time' => now()->translatedFormat('l, d F Y - H:i:s T'),
            'app_version' => config('app.version', 'v1.0.0-PROD'),
            'build_number' => 'Build-2026.08.07-REV2',
        ];

        // Storage metrics
        $freeSpace = @disk_free_space(base_path());
        $totalSpace = @disk_total_space(base_path());
        $storageInfo = [
            'free' => $freeSpace ? number_format($freeSpace / (1024 * 1024 * 1024), 2) . ' GB' : 'N/A',
            'total' => $totalSpace ? number_format($totalSpace / (1024 * 1024 * 1024), 2) . ' GB' : 'N/A',
            'used' => ($totalSpace && $freeSpace) ? number_format(($totalSpace - $freeSpace) / (1024 * 1024 * 1024), 2) . ' GB' : 'N/A',
        ];

        // User Breakdown Counts (Informasional)
        $userCounts = [
            'total_users' => User::count(),
            'total_admin' => User::where('role', 'admin')->count(),
            'total_guru' => User::where('role', 'teacher')->count(),
            'total_operator' => User::where('role', 'operator')->count(),
            'total_piket' => User::where('role', 'piket')->orWhere('role', 'guru_piket')->count(),
            'total_siswa' => User::where('role', 'student')->count(),
            'total_super_admin' => User::where('role', 'super_admin')->count(),
        ];

        return view('SuperAdmin.ServerInfo.Index', compact('serverInfo', 'storageInfo', 'userCounts'));
    }
}
