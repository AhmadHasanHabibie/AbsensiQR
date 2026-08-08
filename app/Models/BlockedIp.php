<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedIp extends Model
{
    use HasFactory;

    protected $table = 'blocked_ips';

    protected $fillable = [
        'ip_address',
        'reason',
        'blocked_until',
        'is_permanent',
    ];

    protected $casts = [
        'blocked_until' => 'datetime',
        'is_permanent'  => 'boolean',
    ];

    /**
     * Memeriksa apakah IP sedang dalam status diblokir (Permanent atau Temporary belum expired).
     */
    public static function isBlocked(?string $ip): bool
    {
        if (!$ip) {
            return false;
        }

        return self::where('ip_address', $ip)
            ->where(function ($query) {
                $query->where('is_permanent', true)
                    ->orWhere('blocked_until', '>', now());
            })
            ->exists();
    }

    /**
     * Helper statis untuk memblokir IP secara otomatis.
     */
    public static function block(string $ip, string $reason, int $minutes = 30, bool $isPermanent = false): self
    {
        $blockedIp = self::firstOrNew(['ip_address' => $ip]);
        $blockedIp->reason        = $reason;
        $blockedIp->is_permanent  = $isPermanent;
        $blockedIp->blocked_until = $isPermanent ? null : now()->addMinutes($minutes);
        $blockedIp->save();

        return $blockedIp;
    }
}
