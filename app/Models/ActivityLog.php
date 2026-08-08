<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'role',
        'activity',
        'module',
        'description',
        'ip_address',
        'browser',
        'platform',
        'device',
    ];

    /**
     * User yang melakukan aktivitas.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getRoleLabelAttribute(): string
    {
        if ($this->user) {
            return $this->user->role_label;
        }

        return match (strtolower($this->role ?? '')) {
            'super_admin'          => 'SuperAdministrator',
            'admin'                => 'Administrator',
            'teacher'              => 'Guru',
            'operator'             => 'Operator',
            'piket', 'guru_piket'  => 'Guru Piket',
            'student'              => 'Siswa',
            'system'               => 'System',
            default                => !empty($this->role) ? ucfirst($this->role) : 'System',
        };
    }

    public function getRoleBadgeClassAttribute(): string
    {
        if ($this->user) {
            return $this->user->role_badge_class;
        }

        return match (strtolower($this->role ?? '')) {
            'super_admin'          => 'bg-dark text-white',
            'admin'                => 'bg-purple text-white',
            'teacher'              => 'bg-success text-white',
            'operator'             => 'bg-warning text-dark',
            'piket', 'guru_piket'  => 'bg-info text-dark',
            'student'              => 'bg-primary text-white',
            default                => 'bg-secondary text-white',
        };
    }

    /**
     * Helper statis untuk mencatat Activity Log secara otomatis.
     */
    public static function log(string $activity, string $module, ?string $description = null, ?User $user = null): ?self
    {
        try {
            $currentUser = $user ?? auth()->user();
            $request     = request();

            $agentData = LoginHistory::parseUserAgent($request ? $request->userAgent() : null);

            return self::create([
                'user_id'     => $currentUser ? $currentUser->id : null,
                'role'        => $currentUser ? $currentUser->role : 'system',
                'activity'    => $activity,
                'module'      => $module,
                'description' => $description,
                'ip_address'  => $request ? $request->ip() : null,
                'browser'     => $agentData['browser'],
                'platform'    => $agentData['platform'],
                'device'      => $agentData['device'],
            ]);
        } catch (\Throwable $e) {
            // Biarkan silent agar tidak mengganggu transaksi utama
            return null;
        }
    }
}
