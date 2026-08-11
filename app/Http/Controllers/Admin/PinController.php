<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PinController extends Controller
{
    /**
     * Menampilkan form verifikasi PIN 6-digit Administrator.
     */
    public function showVerifyForm()
    {
        $user = Auth::user();

        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses sebagai Administrator.');
        }

        if (session('admin_verified') === true) {
            return redirect()->route('admin.dashboard');
        }

        // Cek status Cooldown Server-Side
        $cooldownData = $this->getCooldownStatus($user->id);

        return view('Admin.Auth.VerifyPin', [
            'isCooldownActive'  => $cooldownData['active'],
            'cooldownSeconds'   => $cooldownData['seconds'],
            'attemptsCount'     => $cooldownData['attempts'],
            'attemptsRemaining' => max(0, 3 - $cooldownData['attempts']),
        ]);
    }

    /**
     * Proses verifikasi PIN 6-digit Administrator.
     */
    public function processVerify(Request $request)
    {
        $user = Auth::user();

        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Anda tidak memiliki hak akses sebagai Administrator.');
        }

        // 1. VALIDASI SERVER-SIDE: Cek apakah Cooldown sedang aktif
        $cooldownData = $this->getCooldownStatus($user->id);
        if ($cooldownData['active']) {
            return back()
                ->withErrors([
                    'pin' => 'Akun Administrator dikunci sementara. Silakan coba kembali dalam ' . sprintf('%02d:%02d', floor($cooldownData['seconds'] / 60), $cooldownData['seconds'] % 60) . '.'
                ])
                ->with('cooldown_seconds', $cooldownData['seconds']);
        }

        // 2. Validasi Format PIN Input
        $request->validate([
            'pin' => ['required', 'numeric', 'digits:6'],
        ], [
            'pin.required' => 'PIN wajib diisi.',
            'pin.numeric'  => 'PIN harus terdiri dari 6 digit angka.',
            'pin.digits'   => 'PIN harus terdiri dari 6 digit angka.',
        ]);

        $attemptsKey = "pin_attempts_admin_{$user->id}";
        $cooldownKey = "pin_cooldown_admin_{$user->id}";

        // 3. Evaluasi Kelayakan PIN
        if ($user->verifyPin($request->pin)) {
            // SUCCESS: Reset Counter & Clear Cooldown
            Cache::forget($attemptsKey);
            Cache::forget($cooldownKey);
            Cache::forget("pin_cooldown_logged_admin_{$user->id}");

            session(['admin_verified' => true]);

            ActivityLog::log(
                'PIN Benar',
                'Authentication 2FA',
                "PIN 6-digit berhasil diverifikasi untuk Administrator {$user->name}.",
                $user
            );

            return redirect()->route('admin.dashboard');
        }

        // FAILURE: Increment Attempt Counter
        $attempts = (int) Cache::get($attemptsKey, 0) + 1;
        Cache::put($attemptsKey, $attempts, 86400); // 24 hours expiry for counter unless reset

        if ($attempts < 3) {
            $remaining = 3 - $attempts;

            ActivityLog::log(
                "PIN Salah (Percobaan ke-{$attempts})",
                'Security 2FA',
                "Percobaan PIN ke-{$attempts} gagal untuk Administrator {$user->name}. Sisa percobaan: {$remaining}.",
                $user
            );

            return back()
                ->withErrors([
                    'pin' => "PIN yang Anda masukkan tidak benar. Sisa percobaan: {$remaining}"
                ])
                ->with('attempts_remaining', $remaining)
                ->onlyInput('pin');
        }

        // ATTEMPT 3 REACHED: Aktifkan Cooldown 3 Menit (180 Detik)
        $cooldownDuration = 180; // 3 Menit
        $cooldownEnd = now()->addSeconds($cooldownDuration)->timestamp;

        Cache::put($cooldownKey, $cooldownEnd, $cooldownDuration);
        Cache::put("pin_cooldown_logged_admin_{$user->id}", true, $cooldownDuration);

        ActivityLog::log(
            'Cooldown Dimulai',
            'Security 2FA',
            "Batas 3 kali kesalahan PIN tercapai. Cooldown 3 menit diaktifkan untuk Administrator {$user->name}.",
            $user
        );

        return back()
            ->withErrors([
                'pin' => 'Akun Administrator dikunci sementara selama 3 menit karena 3 kali kesalahan PIN.'
            ])
            ->with('cooldown_seconds', $cooldownDuration);
    }

    /**
     * Helper privat untuk mengecek status Cooldown Server-Side.
     */
    protected function getCooldownStatus(int $userId): array
    {
        $cooldownKey = "pin_cooldown_admin_{$userId}";
        $attemptsKey = "pin_attempts_admin_{$userId}";
        $loggedKey   = "pin_cooldown_logged_admin_{$userId}";

        $cooldownEnd = Cache::get($cooldownKey);
        $now = now()->timestamp;

        if ($cooldownEnd && $cooldownEnd > $now) {
            $secondsRemaining = $cooldownEnd - $now;
            return [
                'active'   => true,
                'seconds'  => $secondsRemaining,
                'attempts' => 3,
            ];
        }

        // Jika cooldown sebelumnya aktif namun sudah kedaluwarsa
        if (Cache::has($loggedKey)) {
            $user = Auth::user();
            if ($user) {
                ActivityLog::log(
                    'Cooldown Berakhir',
                    'Security 2FA',
                    "Masa cooldown 3 menit telah berakhir untuk Administrator {$user->name}. Counter percobaan di-reset.",
                    $user
                );
            }

            Cache::forget($cooldownKey);
            Cache::forget($attemptsKey);
            Cache::forget($loggedKey);
        }

        $attempts = (int) Cache::get($attemptsKey, 0);

        return [
            'active'   => false,
            'seconds'  => 0,
            'attempts' => $attempts,
        ];
    }
}
