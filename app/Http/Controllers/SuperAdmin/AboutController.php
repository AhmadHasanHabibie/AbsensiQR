<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AboutController extends Controller
{
    /**
     * Halaman Tentang Aplikasi.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Audit Log
        ActivityLog::log(
            'Masuk Tentang Aplikasi',
            'Tentang Aplikasi',
            "Pengguna {$user->name} membuka halaman Tentang Aplikasi.",
            $user
        );

        $appInfo = [
            'name' => 'Sistem Absensi QR Code (Multi-Role Enterprise)',
            'version' => config('app.version', 'v1.0.0-PROD'),
            'build' => 'Build-2026.08.07-REV2',
            'laravel' => app()->version(),
            'php' => PHP_VERSION,
            'bootstrap' => '5.3.7',
            'developer' => 'Google DeepMind Agentic Engineering & System Owner Team',
            'copyright' => '© ' . date('Y') . ' System Owner / SMKN 17. All Rights Reserved.',
            'build_date' => '07 Agustus 2026',
        ];

        return view('SuperAdmin.About.Index', compact('appInfo'));
    }
}
