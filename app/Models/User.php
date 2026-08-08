<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN       = 'admin';
    public const ROLE_TEACHER     = 'teacher';
    public const ROLE_STUDENT     = 'student';
    public const ROLE_OPERATOR    = 'operator';
    public const ROLE_PIKET       = 'piket';

    protected $fillable = [
        'name',
        'nip',
        'nis',
        'username',
        'password',
        'pin',
        'role',
        'photo',
        'class_id',
        'qr_token',
        'qr_code',
        'status',
        'is_system',
    ];

    protected $hidden = [
        'password',
        'pin',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'  => 'hashed',
            'status'    => 'boolean',
            'is_system' => 'boolean',
        ];
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function homeroomClass()
    {
        return $this->hasOne(SchoolClass::class, 'teacher_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id', 'id');
    }

    public function mailboxes()
    {
        return $this->hasMany(StudentMailbox::class, 'student_id', 'id');
    }

    public function sentMailboxes()
    {
        return $this->hasMany(StudentMailbox::class, 'teacher_id', 'id');
    }

    public function receivedTeacherMailboxes()
    {
        return $this->hasMany(TeacherMailbox::class, 'receiver_id', 'id');
    }

    public function sentTeacherMailboxes()
    {
        return $this->hasMany(TeacherMailbox::class, 'sender_id', 'id');
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class, 'user_id', 'id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id', 'id');
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'SuperAdministrator',
            self::ROLE_ADMIN       => 'Administrator',
            self::ROLE_TEACHER     => 'Guru',
            self::ROLE_OPERATOR    => 'Operator',
            self::ROLE_PIKET, 'guru_piket' => 'Guru Piket',
            self::ROLE_STUDENT     => 'Siswa',
            default                => ! empty($this->role) ? ucfirst($this->role) : 'User',
        };
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'bg-dark text-white',
            self::ROLE_ADMIN       => 'bg-purple text-white',
            self::ROLE_TEACHER     => 'bg-success text-white',
            self::ROLE_OPERATOR    => 'bg-warning text-dark',
            self::ROLE_PIKET, 'guru_piket' => 'bg-info text-dark',
            self::ROLE_STUDENT     => 'bg-primary text-white',
            default                => 'bg-secondary text-white',
        };
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function verifyPin(string $pin): bool
    {
        if (empty($this->pin)) {
            return $pin === '123456';
        }

        return Hash::check($pin, $this->pin) || $this->pin === $pin;
    }

    public function isSystemAccount(): bool
    {
        return $this->is_system === true || $this->isSuperAdmin();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    public function isGuruPiket(): bool
    {
        return $this->role === 'piket' || $this->role === 'guru_piket';
    }
}