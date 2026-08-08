<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function index()
    {
        return view('Auth.Login');
    }

    /**
     * Proses login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = \Illuminate\Support\Str::transliterate(
            \Illuminate\Support\Str::lower($request->input('username')) . '|' . $request->ip()
        );

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);

            \App\Models\BlockedIp::block($request->ip(), 'Login gagal lebih dari 5 kali dalam 10 menit.', 30);

            return back()
                ->withErrors([
                    'username' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$minutes} menit.",
                ])
                ->onlyInput('username');
        }

        if (!Auth::attempt($credentials)) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 600);

            return back()
                ->withErrors([
                    'username' => 'Username atau password salah.',
                ])
                ->onlyInput('username');
        }

        \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        $user = Auth::user();

        // Record Login History & Activity Log
        $agentData = \App\Models\LoginHistory::parseUserAgent($request->userAgent());
        \App\Models\LoginHistory::create([
            'user_id'      => $user->id,
            'login_at'     => now(),
            'logout_at'    => null,
            'ip_address'   => $request->ip(),
            'browser'      => $agentData['browser'],
            'platform'     => $agentData['platform'],
            'device'       => $agentData['device'],
            'login_status' => 'success',
        ]);

        $roleLabel = $user->role_label;
        \App\Models\ActivityLog::log('Login', 'Authentication', "Pengguna {$user->name} ({$roleLabel}) berhasil login ke sistem.", $user);

        return match ($user->role) {
            'super_admin' => redirect()->route('superadmin.pin.verify'),
            'admin'       => redirect()->route('admin.dashboard'),
            'teacher'     => redirect()->route('guru.dashboard'),
            'student'     => redirect()->route('siswa.dashboard'),
            'operator'    => redirect()->route('operator.dashboard'),
            'piket'       => redirect()->route('piket.dashboard'),

            default => tap(function () use ($request) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }, function () {
                //
            }) ?? redirect()
                ->route('login')
                ->with('error', 'Role tidak ditemukan.'),
        };
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $latestHistory = \App\Models\LoginHistory::where('user_id', $user->id)
                ->whereNull('logout_at')
                ->latest('login_at')
                ->first();

            if ($latestHistory) {
                $latestHistory->update([
                    'logout_at'    => now(),
                    'login_status' => 'logout',
                ]);
            }

            $roleLabel = $user->role_label;
            \App\Models\ActivityLog::log('Logout', 'Authentication', "Pengguna {$user->name} ({$roleLabel}) telah logout dari sistem.", $user);
        }

        $request->session()->forget('super_admin_verified');
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Anda berhasil keluar dari sistem.');
    }
}