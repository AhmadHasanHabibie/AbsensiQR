<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;

class LoginHistoryController extends Controller
{
    /**
     * Menampilkan Dashboard Monitoring Login History untuk Admin.
     */
    public function index(Request $request)
    {
        // Daftar ID SuperAdmin — digunakan untuk mengeksklusikan dari seluruh query Admin
        $superAdminIds = User::where('role', 'super_admin')->pluck('id');

        // 5 Summary Metric Cards — tidak menghitung SuperAdmin
        $loginHariIni = LoginHistory::whereDate('login_at', today())
            ->whereNotIn('user_id', $superAdminIds)
            ->count();

        $loginMingguIni = LoginHistory::whereDate('login_at', '>=', now()->startOfWeek())
            ->whereNotIn('user_id', $superAdminIds)
            ->count();

        $loginBulanIni = LoginHistory::whereMonth('login_at', now()->month)
            ->whereYear('login_at', now()->year)
            ->whereNotIn('user_id', $superAdminIds)
            ->count();

        $currentlyActiveCount = LoginHistory::whereNull('logout_at')
            ->whereDate('login_at', '>=', today()->subDays(1))
            ->whereNotIn('user_id', $superAdminIds)
            ->count();

        $totalLoginAll = LoginHistory::whereNotIn('user_id', $superAdminIds)->count();

        // Query Login Histories dengan Eager Loading
        $query = LoginHistory::with('user')
            ->whereNotIn('user_id', $superAdminIds)
            ->orderBy('login_at', 'desc');

        if ($request->filled('role')) {
            $roleParam = $request->role;
            // Tolak secara diam-diam jika admin mencoba memfilter super_admin secara manual
            if ($roleParam === 'super_admin') {
                // Abaikan filter — query sudah mengeksklusikan SuperAdmin di atas
            } elseif ($roleParam === 'piket' || $roleParam === 'guru_piket') {
                $query->whereHas('user', fn ($q) => $q->whereIn('role', ['piket', 'guru_piket']));
            } else {
                $query->whereHas('user', fn ($q) => $q->where('role', $roleParam));
            }
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
