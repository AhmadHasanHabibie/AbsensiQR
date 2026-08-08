<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Halaman Riwayat Aktivitas System Owner (Timeline Audit).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Audit Log
        ActivityLog::log(
            'Melihat Riwayat Aktivitas',
            'Riwayat Aktivitas',
            "Pengguna {$user->name} mengakses Timeline Riwayat Aktivitas Sistem.",
            $user
        );

        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        // Filter Activity / Search
        if ($request->filled('activity')) {
            $activitySearch = $request->activity;
            $query->where(function ($q) use ($activitySearch) {
                $q->where('activity', 'like', "%{$activitySearch}%")
                    ->orWhere('module', 'like', "%{$activitySearch}%")
                    ->orWhere('description', 'like', "%{$activitySearch}%");
            });
        }

        // Filter Date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter Role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $logs = $query->paginate(20)->withQueryString();

        // Available Roles for Filter
        $roles = ActivityLog::select('role')
            ->distinct()
            ->whereNotNull('role')
            ->pluck('role');

        return view('SuperAdmin.ActivityLog.Index', compact('logs', 'roles'));
    }
}
