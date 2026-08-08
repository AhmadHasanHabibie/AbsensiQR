<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Menampilkan Audit / Activity Log seluruh pengguna untuk Admin.
     */
    public function index(Request $request)
    {
        // 4 Summary Cards
        $aktivitasHariIni = ActivityLog::whereDate('created_at', today())->count();

        $aktivitasMingguIni = ActivityLog::whereDate('created_at', '>=', now()->startOfWeek())->count();

        $aktivitasBulanIni = ActivityLog::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $totalAktivitas = ActivityLog::count();

        // Query dengan Eager Loading
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        if ($request->filled('role')) {
            $roleParam = $request->role;
            if ($roleParam === 'piket' || $roleParam === 'guru_piket') {
                $query->whereIn('role', ['piket', 'guru_piket']);
            } else {
                $query->where('role', $roleParam);
            }
        } else {
            $query->where(function ($q) {
                $q->where('role', '!=', 'super_admin')
                  ->orWhereNull('role');
            });
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
