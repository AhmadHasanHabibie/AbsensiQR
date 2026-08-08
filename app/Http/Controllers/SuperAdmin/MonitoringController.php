<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MonitoringController extends Controller
{
    /**
     * Halaman Monitoring Sistem.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Audit Log
        ActivityLog::log(
            'Masuk Monitoring Sistem',
            'Monitoring Sistem',
            "Pengguna {$user->name} mengakses halaman Monitoring Sistem.",
            $user
        );

        // 1. Status Server
        $serverOk = function_exists('proc_open') || function_exists('exec');
        $statusServer = [
            'name'    => 'Status Server',
            'label'   => $serverOk ? 'Normal' : 'Perlu Perhatian',
            'color'   => $serverOk ? 'success' : 'warning',
            'details' => 'PHP ' . PHP_VERSION . ' (' . PHP_OS_FAMILY . ')'
        ];

        // 2. Status Database
        try {
            DB::connection()->getPdo();
            $hasUsersTable = Schema::hasTable('users');
            $statusDatabase = [
                'name'    => 'Status Database',
                'label'   => $hasUsersTable ? 'Normal' : 'Perlu Perhatian',
                'color'   => $hasUsersTable ? 'success' : 'warning',
                'details' => 'Connected (' . config('database.default') . ')'
            ];
        } catch (\Throwable $e) {
            $statusDatabase = [
                'name'    => 'Status Database',
                'label'   => 'Error',
                'color'   => 'danger',
                'details' => $e->getMessage()
            ];
        }

        // 3. Status Storage
        $freeSpace   = @disk_free_space(base_path());
        $totalSpace  = @disk_total_space(base_path());
        $usedPercent = ($totalSpace && $freeSpace) ? round((($totalSpace - $freeSpace) / $totalSpace) * 100, 1) : 0;
        $storageColor = 'success';
        $storageLabel = 'Normal';
        if ($usedPercent > 90) {
            $storageColor = 'danger';
            $storageLabel = 'Error (Hampir Penuh)';
        } elseif ($usedPercent > 75) {
            $storageColor = 'warning';
            $storageLabel = 'Perlu Perhatian';
        }

        $statusStorage = [
            'name'    => 'Status Storage',
            'label'   => $storageLabel,
            'color'   => $storageColor,
            'details' => "Terpakai {$usedPercent}% (" . number_format(($totalSpace - $freeSpace) / (1024 * 1024 * 1024), 2) . " GB)"
        ];

        // 4. Status Cache
        try {
            Cache::put('system_health_test', true, 10);
            $cacheWorking = Cache::get('system_health_test') === true;
            $statusCache = [
                'name'    => 'Status Cache',
                'label'   => $cacheWorking ? 'Normal' : 'Perlu Perhatian',
                'color'   => $cacheWorking ? 'success' : 'warning',
                'details' => 'Driver: ' . config('cache.default')
            ];
        } catch (\Throwable $e) {
            $statusCache = [
                'name'    => 'Status Cache',
                'label'   => 'Error',
                'color'   => 'danger',
                'details' => $e->getMessage()
            ];
        }

        // 5. Status Session
        $statusSession = [
            'name'    => 'Status Session',
            'label'   => 'Normal',
            'color'   => 'success',
            'details' => 'Driver: ' . config('session.driver')
        ];

        // 6. Status Queue
        $queueConn = config('queue.default');
        $statusQueue = [
            'name'    => 'Status Queue',
            'label'   => 'Normal',
            'color'   => 'success',
            'details' => 'Driver: ' . $queueConn
        ];

        // 7. Status Mail
        $mailer   = config('mail.default');
        $mailHost = config('mail.mailers.' . $mailer . '.host') ?? config('mail.host') ?? 'Not Configured';
        $statusMail = [
            'name'    => 'Status Mail',
            'label'   => ($mailer === 'smtp' && $mailHost === '127.0.0.1') ? 'Perlu Perhatian' : 'Normal',
            'color'   => ($mailer === 'smtp' && $mailHost === '127.0.0.1') ? 'warning' : 'success',
            'details' => "Mailer: {$mailer} ({$mailHost})"
        ];

        // 8. Status APP_ENV
        $env = config('app.env');
        $statusEnv = [
            'name'    => 'Status APP_ENV',
            'label'   => ($env === 'production') ? 'Normal' : 'Perlu Perhatian',
            'color'   => ($env === 'production') ? 'success' : 'warning',
            'details' => "Environment: {$env}"
        ];

        // 9. Status APP_DEBUG
        $debug = config('app.debug');
        $statusDebug = [
            'name'    => 'Status APP_DEBUG',
            'label'   => $debug ? 'Perlu Perhatian (Mode Debug Aktif)' : 'Normal (Mode Debug Off)',
            'color'   => $debug ? 'warning' : 'success',
            'details' => "APP_DEBUG = " . ($debug ? 'true' : 'false')
        ];

        $rawMonitors = [
            $statusServer,
            $statusDatabase,
            $statusStorage,
            $statusCache,
            $statusSession,
            $statusQueue,
            $statusMail,
            $statusEnv,
            $statusDebug
        ];

        $metrics  = [];
        $monitors = [];

        foreach ($rawMonitors as $item) {
            $formatted = [
                'name'      => $item['name'] ?? 'Status',
                'component' => $item['name'] ?? 'Status',
                'label'     => $item['label'] ?? 'Normal',
                'status'    => $item['label'] ?? 'Normal',
                'value'     => $item['label'] ?? 'Normal',
                'badge'     => $item['color'] ?? 'success',
                'color'     => $item['color'] ?? 'success',
                'details'   => $item['details'] ?? '-',
                'desc'      => $item['details'] ?? '-',
            ];

            $slugKey = Str::slug($item['name'] ?? 'status', '_');
            $metrics[$slugKey] = $formatted;
            $monitors[]        = $formatted;
        }

        return view('SuperAdmin.Monitoring.Index', compact('metrics', 'monitors'));
    }
}
