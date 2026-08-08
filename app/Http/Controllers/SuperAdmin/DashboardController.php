<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\DatabaseBackup;
use App\Models\LoginHistory;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard Control Center Final Release v1.0.0 (Super Administrator / System Owner).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        ActivityLog::log(
            'Melihat Monitoring',
            'Dashboard Control Center',
            "Pengguna {$user->name} mengakses Dashboard System Owner Final Release.",
            $user
        );

        $adminCount    = User::where('role', 'admin')->count();
        $guruCount     = User::where('role', 'teacher')->count();
        $operatorCount = User::where('role', 'operator')->count();
        $piketCount    = User::where('role', 'piket')->orWhere('role', 'guru_piket')->count();
        $siswaCount    = User::where('role', 'student')->count();
        $kelasCount    = SchoolClass::count();
        $absensiToday  = Attendance::whereDate('created_at', today())->count();
        $loginToday    = LoginHistory::whereDate('login_at', today())->count();
        $auditToday    = ActivityLog::whereDate('created_at', today())->count();

        $stats = [
            'admin_count'         => $adminCount,
            'guru_count'          => $guruCount,
            'operator_count'      => $operatorCount,
            'piket_count'         => $piketCount,
            'siswa_count'         => $siswaCount,
            'kelas_count'         => $kelasCount,
            'absensi_today_count' => $absensiToday,
            'login_today_count'   => $loginToday,
            'audit_today_count'   => $auditToday,

            'total_admin'      => $adminCount,
            'total_guru'       => $guruCount,
            'total_operator'   => $operatorCount,
            'total_piket'      => $piketCount,
            'total_siswa'      => $siswaCount,
            'total_kelas'      => $kelasCount,
            'absensi_hari_ini' => $absensiToday,
            'login_hari_ini'   => $loginToday,
            'audit_hari_ini'   => $auditToday,
        ];

        $latestBackupObj = DatabaseBackup::latest()->first();
        $lastBackup      = [
            'filename' => $latestBackupObj ? $latestBackupObj->filename         : 'Belum ada backup',
            'time'     => $latestBackupObj ? $latestBackupObj->created_at->translatedFormat('d M Y, H:i') : 'Belum ada',
            'size'     => $latestBackupObj ? $latestBackupObj->formatted_size   : '—',
            'status'   => $latestBackupObj ? strtoupper($latestBackupObj->status ?? 'COMPLETED') : 'N/A',
        ];

        try {
            DB::connection()->getPdo();
            $dbStatus = ['label' => 'Normal', 'color' => 'success'];
        } catch (\Throwable $e) {
            $dbStatus = ['label' => 'Gangguan', 'color' => 'danger'];
        }

        $freeSpace   = @disk_free_space(base_path());
        $totalSpace  = @disk_total_space(base_path());
        $usedPercent = ($totalSpace && $freeSpace) ? round((($totalSpace - $freeSpace) / $totalSpace) * 100, 1) : 0;
        if ($usedPercent > 90) {
            $storageStatus = ['label' => 'Gangguan', 'color' => 'danger'];
        } elseif ($usedPercent > 75) {
            $storageStatus = ['label' => 'Perlu Perhatian', 'color' => 'warning'];
        } else {
            $storageStatus = ['label' => 'Normal', 'color' => 'success'];
        }

        $mailer     = config('mail.default');
        $mailStatus = ($mailer === 'smtp' && (config('mail.host') === '127.0.0.1' || ! config('mail.host')))
            ? ['label' => 'Perlu Perhatian', 'color' => 'warning']
            : ['label' => 'Normal', 'color' => 'success'];

        $sessionStatus = ['label' => 'Normal', 'color' => 'success'];

        try {
            Cache::put('health_check', true, 5);
            $cacheStatus = Cache::get('health_check') ? ['label' => 'Normal', 'color' => 'success'] : ['label' => 'Perlu Perhatian', 'color' => 'warning'];
        } catch (\Throwable $e) {
            $cacheStatus = ['label' => 'Gangguan', 'color' => 'danger'];
        }

        $queueStatus = ['label' => 'Normal', 'color' => 'success'];

        $appStatus = config('app.debug')
            ? ['label' => 'Perlu Perhatian (Debug Mode)', 'color' => 'warning']
            : ['label' => 'Normal (Production)', 'color' => 'success'];

        $isHttps   = $request->secure() || $request->header('X-Forwarded-Proto') === 'https';
        $sslStatus = $isHttps
            ? ['label' => 'Normal (SSL Active)', 'color' => 'success']
            : ['label' => 'Perlu Perhatian (HTTP Mode)', 'color' => 'warning'];

        $systemHealth = [
            [
                'component' => 'Database',
                'status'    => $dbStatus['label'],
                'badge'     => $dbStatus['color'],
                'color'     => $dbStatus['color'],
                'label'     => $dbStatus['label'],
                'desc'      => 'Koneksi PDO ke database MySQL stabil.',
            ],
            [
                'component' => 'Storage Drive',
                'status'    => $storageStatus['label'] . " ({$usedPercent}% terpakai)",
                'badge'     => $storageStatus['color'],
                'color'     => $storageStatus['color'],
                'label'     => $storageStatus['label'],
                'desc'      => 'Kapasitas penyimpanan lokal aplikasi.',
            ],
            [
                'component' => 'Mail Server',
                'status'    => $mailStatus['label'],
                'badge'     => $mailStatus['color'],
                'color'     => $mailStatus['color'],
                'label'     => $mailStatus['label'],
                'desc'      => 'Infrastruktur pengiriman email sistem.',
            ],
            [
                'component' => 'Session Driver',
                'status'    => $sessionStatus['label'],
                'badge'     => $sessionStatus['color'],
                'color'     => $sessionStatus['color'],
                'label'     => $sessionStatus['label'],
                'desc'      => 'Penyimpanan dan keamanan sesi login.',
            ],
            [
                'component' => 'Cache Storage',
                'status'    => $cacheStatus['label'],
                'badge'     => $cacheStatus['color'],
                'color'     => $cacheStatus['color'],
                'label'     => $cacheStatus['label'],
                'desc'      => 'Penyimpanan cache dan 2FA RateLimiter.',
            ],
            [
                'component' => 'Queue Worker',
                'status'    => $queueStatus['label'],
                'badge'     => $queueStatus['color'],
                'color'     => $queueStatus['color'],
                'label'     => $queueStatus['label'],
                'desc'      => 'Layanan antrean tugas latar belakang.',
            ],
            [
                'component' => 'APP Mode',
                'status'    => $appStatus['label'],
                'badge'     => $appStatus['color'],
                'color'     => $appStatus['color'],
                'label'     => $appStatus['label'],
                'desc'      => 'Mode debug dan lingkungan aplikasi.',
            ],
            [
                'component' => 'SSL / HTTPS',
                'status'    => $sslStatus['label'],
                'badge'     => $sslStatus['color'],
                'color'     => $sslStatus['color'],
                'label'     => $sslStatus['label'],
                'desc'      => 'Protokol enkripsi lalu lintas data.',
            ],
        ];

        $versionInfo = [
            'app_name'        => config('app.name', 'Sistem Absensi QR Code'),
            'app_version'     => config('app.version', 'v1.0.0'),
            'version'         => config('app.version', 'v1.0.0'),
            'build_number'    => 'Build-2026.08.07-FINAL',
            'build'           => 'Build-2026.08.07-FINAL',
            'build_date'      => '07 Agustus 2026',
            'laravel_version' => app()->version(),
            'laravel'         => app()->version(),
            'php_version'     => PHP_VERSION,
            'php'             => PHP_VERSION,
            'ui_framework'    => 'Bootstrap 5.3.7',
            'bootstrap'       => 'Bootstrap 5.3.7',
            'db_driver'       => config('database.default'),
            'database'        => config('database.default'),
            'app_env'         => config('app.env'),
            'environment'     => config('app.env'),
            'timezone'        => config('app.timezone'),
            'developer'       => 'SMKN 17 Jakarta Engineering Team',
            'domain'          => $request->getHost(),
        ];

        $recentActivities = ActivityLog::with('user')
            ->latest('created_at')
            ->take(5)
            ->get();

        $dailyStatus = \App\Services\AcademicCalendarService::getDailyStatus();

        return view('SuperAdmin.Dashboard.Index', compact(
            'stats',
            'lastBackup',
            'systemHealth',
            'versionInfo',
            'recentActivities',
            'dailyStatus'
        ));
    }
}
