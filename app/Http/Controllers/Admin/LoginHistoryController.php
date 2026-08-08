<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use Illuminate\Http\Request;

class LoginHistoryController extends Controller
{
    /**
     * Menampilkan Dashboard Monitoring Login History untuk Admin.
     */
    public function index(Request $request)
    {
        // 5 Summary Metric Cards
        $loginHariIni = LoginHistory::whereDate('login_at', today())->count();

        $loginMingguIni = LoginHistory::whereDate('login_at', '>=', now()->startOfWeek())->count();

        $loginBulanIni = LoginHistory::whereMonth('login_at', now()->month)
            ->whereYear('login_at', now()->year)
            ->count();

        $currentlyActiveCount = LoginHistory::whereNull('logout_at')
            ->whereDate('login_at', '>=', today()->subDays(1))
            ->count();

        $totalLoginAll = LoginHistory::count();

        // Query Login Histories dengan Eager Loading
        $query = LoginHistory::with('user')
            ->orderBy('login_at', 'desc');

        if ($request->filled('role')) {
            $roleParam = $request->role;
            if ($roleParam === 'piket' || $roleParam === 'guru_piket') {
                $query->whereHas('user', fn ($q) => $q->whereIn('role', ['piket', 'guru_piket']));
            } else {
                $query->whereHas('user', fn ($q) => $q->where('role', $roleParam));
            }
        } else {
            $query->whereHas('user', function ($q) {
                $q->where('role', '!=', 'super_admin');
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_range')) {
            match ($request->date_range) {
                'today'  => $query->whereDate('login_at', today()),
                '7days'  => $query->whereDate('login_at', '>=', now()->subDays(7)),
                '30days' => $query->whereDate('login_at', '>=', now()->subDays(30)),
                'all'    => null,
                'custom' => tap($query, function ($q) use ($request) {
                    if ($request->filled('start_date')) {
                        $q->whereDate('login_at', '>=', $request->start_date);
                    }
                    if ($request->filled('end_date')) {
                        $q->whereDate('login_at', '<=', $request->end_date);
                    }
                }),
                default  => null,
            };
        }

        $loginHistories = $query->paginate(15)->withQueryString();

        return view('Admin.LoginHistory.Index', compact(
            'loginHariIni',
            'loginMingguIni',
            'loginBulanIni',
            'currentlyActiveCount',
            'totalLoginAll',
            'loginHistories'
        ));
    }
}
