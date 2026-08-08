<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginHistoryController extends Controller
{
    public function index()
    {
        $histories = LoginHistory::where('user_id', Auth::id())
            ->latest('login_at')
            ->paginate(10);

        return view('Piket.LoginHistory.Index', compact('histories'));
    }
}
