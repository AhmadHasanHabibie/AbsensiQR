<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use Illuminate\Support\Facades\Auth;

class LoginHistoryController extends Controller
{
    /**
     * Menampilkan Login History milik Guru yang sedang terautentikasi.
     */
    public function index()
    {
        $userId = Auth::id();

        // 4 Summary Metric Cards
        $loginHariIni = LoginHistory::where('user_id', $userId)
            ->whereDate('login_at', today())
            ->count();

        $loginBulanIni = LoginHistory::where('user_id', $userId)
            ->whereMonth('login_at', now()->month)
            ->whereYear('login_at', now()->year)
            ->count();

        $lastLogin = LoginHistory::where('user_id', $userId)
            ->orderBy('login_at', 'desc')
            ->first();

        $totalLogin = LoginHistory::where('user_id', $userId)->count();

        // Query Login Histories Paginasi
        $loginHistories = LoginHistory::where('user_id', $userId)
            ->orderBy('login_at', 'desc')
            ->paginate(15);

        return view('Guru.LoginHistory.Index', compact(
            'loginHariIni',
            'loginBulanIni',
            'lastLogin',
            'totalLogin',
            'loginHistories'
        ));
    }
}
