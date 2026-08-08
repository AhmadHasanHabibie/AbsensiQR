<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    use HasFactory;

    protected $table = 'login_histories';

    protected $fillable = [
        'user_id',
        'login_at',
        'logout_at',
        'ip_address',
        'browser',
        'platform',
        'device',
        'login_status',
    ];

    protected $casts = [
        'login_at'  => 'datetime',
        'logout_at' => 'datetime',
    ];

    /**
     * User yang melakukan login.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getRoleLabelAttribute(): string
    {
        return $this->user ? $this->user->role_label : '-';
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return $this->user ? $this->user->role_badge_class : 'bg-secondary text-white';
    }

    /**
     * Accessor untuk menghitung durasi login secara format teks.
     */
    public function getFormattedDurationAttribute(): string
    {
        if (! $this->logout_at || ! $this->login_at) {
            return 'Sedang Aktif';
        }

        $diff = $this->login_at->diff($this->logout_at);
        $parts = [];

        if ($diff->d > 0) {
            $parts[] = $diff->d . ' Hari';
        }
        if ($diff->h > 0) {
            $parts[] = $diff->h . ' Jam';
        }
        if ($diff->i > 0) {
            $parts[] = $diff->i . ' Menit';
        }
        if (empty($parts)) {
            $parts[] = max(1, $diff->s) . ' Detik';
        }

        return implode(' ', $parts);
    }

    /**
     * Helper untuk memparsing User-Agent HTTP header.
     */
    public static function parseUserAgent(?string $userAgent): array
    {
        $userAgent = $userAgent ?? '';

        // Browser
        $browser = 'Unknown';
        if (preg_match('/Edg\/([0-9\.]+)/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/Chrome\/([0-9\.]+)/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox\/([0-9\.]+)/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari\/([0-9\.]+)/i', $userAgent) && !preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/OPR\/([0-9\.]+)/i', $userAgent) || preg_match('/Opera/i', $userAgent)) {
            $browser = 'Opera';
        } elseif (preg_match('/MSIE|Trident/i', $userAgent)) {
            $browser = 'Internet Explorer';
        }

        // Platform
        $platform = 'Unknown';
        if (preg_match('/windows|win32/i', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/android/i', $userAgent)) {
            $platform = 'Android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            $platform = 'iOS';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $platform = 'macOS';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $platform = 'Linux';
        }

        // Device
        $device = 'Desktop';
        if (preg_match('/ipad/i', $userAgent)) {
            $device = 'Tablet';
        } elseif (preg_match('/tablet/i', $userAgent)) {
            $device = 'Tablet';
        } elseif (preg_match('/mobile|iphone|ipod|android/i', $userAgent)) {
            $device = 'Mobile';
        }

        return [
            'browser'  => $browser,
            'platform' => $platform,
            'device'   => $device,
        ];
    }
}
