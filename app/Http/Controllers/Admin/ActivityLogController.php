<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Menampilkan Audit / Activity Log seluruh pengguna untuk Admin.
     */
    public function index(Request $request)
    {
        // Daftar role SuperAdmin — digunakan untuk mengeksklusikan dari seluruh query Admin
        // ActivityLog menyimpan field 'role' langsung, bukan relasi user_id untuk filter awal
        $excludedRoles = ['super_admin'];

        // 4 Summary Cards — tidak menghitung SuperAdmin
        $aktivitasHariIni = ActivityLog::whereDate('created_at', today())
            ->whereNotIn('role', $excludedRoles)
            ->count();

        $aktivitasMingguIni = ActivityLog::whereDate('created_at', '>=', now()->startOfWeek())
            ->whereNotIn('role', $excludedRoles)
            ->count();

        $aktivitasBulanIni = ActivityLog::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereNotIn('role', $excludedRoles)
            ->count();

        $totalAktivitas = ActivityLog::whereNotIn('role', $excludedRoles)->count();

        // Query dengan Eager Loading — tidak menyertakan SuperAdmin
        $query = ActivityLog::with('user')
            ->whereNotIn('role', $excludedRoles)
            ->orderBy('created_at', 'desc');

        if ($request->filled('role')) {
            $roleParam = $request->role;
            // Tolak secara diam-diam jika admin mencoba memfilter super_admin secara manual
            if ($roleParam === 'super_admin') {
                // Abaikan filter — query sudah mengeksklusikan SuperAdmin di atas
            } elseif ($roleParam === 'piket' || $roleParam === 'guru_piket') {
                $query->whereIn('role', ['piket', 'guru_piket']);
            } else {
                $query->where('role', $roleParam);
            }
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('activity', 'like', "%{$search}%")
                    ->orWhere('module', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                    });
            });
            // Pastikan hasil pencarian tetap tidak menyertakan SuperAdmin
            $query->whereNotIn('role', ['super_admin']);
        }

        if ($request->filled('date_range')) {
            match ($request->date_range) {
                'today'  => $query->whereDate('created_at', today()),
                '7days'  => $query->whereDate('created_at', '>=', now()->subDays(7)),
                '30days' => $query->whereDate('created_at', '>=', now()->subDays(30)),
                'all'    => null,
                'custom' => tap($query, function ($q) use ($request) {
                    if ($request->filled('start_date')) {
                        $q->whereDate('created_at', '>=', $request->start_date);
                    }
                    if ($request->filled('end_date')) {
                        $q->whereDate('created_at', '<=', $request->end_date);
                    }
                }),
                default  => null,
            };
        }

        $activityLogs = $query->paginate(15)->withQueryString();

        return view('Admin.ActivityLog.Index', compact(
            'aktivitasHariIni',
            'aktivitasMingguIni',
            'aktivitasBulanIni',
            'totalAktivitas',
            'activityLogs'
        ));
    }
}
