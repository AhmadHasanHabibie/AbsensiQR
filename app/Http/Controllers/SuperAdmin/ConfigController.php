<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfigController extends Controller
{
    /**
     * Halaman Konfigurasi Sistem (Readonly).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Audit Log
        ActivityLog::log(
            'Melihat Konfigurasi',
            'Konfigurasi Sistem',
            "Pengguna {$user->name} melihat halaman Konfigurasi Sistem (Readonly).",
            $user
        );

        $freeSpace  = @disk_free_space(base_path());
        $totalSpace = @disk_total_space(base_path());
        $storageInfo = $freeSpace && $totalSpace
            ? number_format($freeSpace / (1024 * 1024 * 1024), 2) . ' GB free of ' . number_format($totalSpace / (1024 * 1024 * 1024), 2) . ' GB'
            : 'Standard Local Storage';

        $configs = [
            'APP_NAME'         => config('app.name'),
            'APP_ENV'          => config('app.env'),
            'APP_URL'          => config('app.url', url('/')),
            'TIMEZONE'         => config('app.timezone'),
            'MAIL'             => config('mail.default') . ' (' . (config('mail.mailers.' . config('mail.default') . '.host') ?? '127.0.0.1') . ')',
            'CACHE'            => config('cache.default'),
            'SESSION'          => config('session.driver'),
            'QUEUE'            => config('queue.default'),
            'Storage'          => $storageInfo,
        ];

        return view('SuperAdmin.Config.Index', compact('configs'));
    }
}
