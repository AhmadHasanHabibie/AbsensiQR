<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BlockedIp;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SecurityCenterController extends Controller
{
    /**
     * Menampilkan Hub Keamanan Sistem Terpadu untuk Admin.
     */
    public function index(Request $request)
    {
        $data = $this->getDashboardData($request);

        return view('Admin.SecurityCenter.Index', $data);
    }

    /**
     * Endpoint API untuk Auto Refresh 60 Detik via AJAX.
     */
    public function data(Request $request)
    {
        $data = $this->getDashboardData($request);

        return response()->json($data);
    }

    /**
     * Helper privat untuk mengkalkulasi seluruh metrik & data modul keamanan.
     */
    private function getDashboardData(?Request $request = null): array
    {
        $today = today();

        // Daftar ID & Role SuperAdmin — digunakan untuk mengeksklusikan dari seluruh query Admin
        $superAdminIds   = User::where('role', 'super_admin')->pluck('id');
        $excludedRoles   = ['super_admin'];

        $totalLoginHariIni = LoginHistory::whereDate('login_at', $today)
            ->whereNotIn('user_id', $superAdminIds)
            ->count();

        $loginBerhasil = LoginHistory::whereDate('login_at', $today)
            ->where('login_status', '!=', 'failed')
            ->whereNotIn('user_id', $superAdminIds)
            ->count();

        $loginGagal = LoginHistory::whereDate('login_at', $today)
            ->where('login_status', 'failed')
            ->whereNotIn('user_id', $superAdminIds)
            ->count();

        $aktivitasHariIni = ActivityLog::whereDate('created_at', $today)
            ->whereNotIn('role', $excludedRoles)
            ->count();

        $sessionAktif = LoginHistory::whereNull('logout_at')
            ->whereDate('login_at', '>=', $today->subDays(1))
            ->whereNotIn('user_id', $superAdminIds)
            ->count();

        $blockedIpTotal   = BlockedIp::count();
        $blockedTempCount = BlockedIp::where('is_permanent', false)->where('blocked_until', '>', now())->count();
        $blockedPermCount = BlockedIp::where('is_permanent', true)->count();

        $rawScore    = ($loginGagal * 5) + ($blockedTempCount * 10);
        $threatScore = min(100, max(0, $rawScore));

        if ($threatScore >= 81) {
            $threatScoreLabel      = 'Critical';
            $threatScoreBadgeClass = 'bg-danger';
        } elseif ($threatScore >= 51) {
            $threatScoreLabel      = 'High';
            $threatScoreBadgeClass = 'badge-orange';
        } elseif ($threatScore >= 21) {
            $threatScoreLabel      = 'Medium';
            $threatScoreBadgeClass = 'bg-warning text-dark';
        } else {
            $threatScoreLabel      = 'Low';
            $threatScoreBadgeClass = 'bg-success';
        }

        if ($threatScore >= 81 || $loginGagal > 10) {
            $systemStatus      = 'WASPADA';
            $statusBadgeClass  = 'bg-danger';
            $statusDescription = "Threat Score berada pada tingkat High/Critical ({$threatScore}/100) karena lonjakan login gagal.";
        } elseif ($threatScore >= 21 || $loginGagal > 3) {
            $systemStatus      = 'PERLU PERHATIAN';
            $statusBadgeClass  = 'bg-warning text-dark';
            $statusDescription = "Threat Score berada pada tingkat {$threatScoreLabel} ({$threatScore}/100) yang memerlukan perhatian admin.";
        } else {
            $systemStatus      = 'AMAN';
            $statusBadgeClass  = 'bg-success';
            $statusDescription = 'Sistem beroperasi normal tanpa ancaman keamanan yang signifikan.';
        }

        $chartLogin24h = [
            'labels' => [],
            'data'   => [],
        ];
        for ($i = 23; $i >= 0; $i--) {
            $hourTime                  = now()->subHours($i);
            $chartLogin24h['labels'][] = $hourTime->format('H:00');
            $chartLogin24h['data'][]   = LoginHistory::whereBetween('login_at', [
                $hourTime->copy()->startOfHour(),
                $hourTime->copy()->endOfHour(),
            ])->whereNotIn('user_id', $superAdminIds)->count();
        }

        $chartActivity7d = [
            'labels' => [],
            'data'   => [],
        ];
        for ($i = 6; $i >= 0; $i--) {
            $date                        = today()->subDays($i);
            $chartActivity7d['labels'][] = $date->isoFormat('DD MMM');
            $chartActivity7d['data'][]   = ActivityLog::whereDate('created_at', $date)
                ->whereNotIn('role', $excludedRoles)
                ->count();
        }

        $recentActivities = ActivityLog::with('user')
            ->whereNotIn('role', $excludedRoles)
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($log) => [
                'id'          => $log->id,
                'user_name'   => optional($log->user)->name ?? 'System',
                'role'        => $log->role_label,
                'activity'    => $log->activity,
                'module'      => $log->module,
                'description' => $log->description,
                'ip_address'  => $log->ip_address,
                'device'      => $log->device,
                'time'        => $log->created_at ? $log->created_at->isoFormat('DD MMM YYYY, HH:mm') . ' WIB' : '-',
            ]);

        $topUsers = ActivityLog::with('user')
            ->whereDate('created_at', today())
            ->whereNotIn('role', $excludedRoles)
            ->select('user_id', 'role', DB::raw('count(*) as total'))
            ->groupBy('user_id', 'role')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->map(fn ($item) => [
                'name'  => optional($item->user)->name ?? 'System User',
                'role'  => optional($item->user)->role_label ?? ($item->role ? ucfirst($item->role) : 'System'),
                'total' => $item->total,
            ]);

        $topModules = ActivityLog::whereDate('created_at', today())
            ->whereNotIn('role', $excludedRoles)
            ->select('module', DB::raw('count(*) as total'))
            ->groupBy('module')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->map(fn ($item) => [
                'module' => $item->module,
                'total'  => $item->total,
            ]);

        $blockedIpsList   = BlockedIp::latest()->paginate(10, ['*'], 'ips_page');
        $failedLoginsList = LoginHistory::where('login_status', 'failed')
            ->whereNotIn('user_id', $superAdminIds)
            ->latest('login_at')
            ->paginate(10, ['*'], 'failed_page');

        return compact(
            'totalLoginHariIni',
            'loginBerhasil',
            'loginGagal',
            'aktivitasHariIni',
            'sessionAktif',
            'blockedIpTotal',
            'blockedTempCount',
            'blockedPermCount',
            'threatScore',
            'threatScoreLabel',
            'threatScoreBadgeClass',
            'systemStatus',
            'statusBadgeClass',
            'statusDescription',
            'chartLogin24h',
            'chartActivity7d',
            'recentActivities',
            'topUsers',
            'topModules',
            'blockedIpsList',
            'failedLoginsList'
        );
    }
}
