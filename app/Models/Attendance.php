<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'attendance_date',
        'check_in',
        'check_out',
        'status',
        'is_late',
        'late_time',
        'late_note',
        'is_emergency',
        'emergency_reason',
        'emergency_note',
        'operator_id',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in'        => 'datetime:H:i:s',
        'check_out'       => 'datetime:H:i:s',
        'is_emergency'    => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id', 'id');
    }

    public function emergencyAudit()
    {
        return $this->hasOne(EmergencyAttendanceAudit::class, 'attendance_id', 'id');
    }

    public function getJamMasukAttribute(): string
    {
        return '06:30';
    }

    public function getTotalTerlambatFormattedAttribute(): string
    {
        $timeStr = null;

        if ($this->late_time) {
            $timeStr = Carbon::parse($this->late_time)->format('H:i:s');
        } elseif ($this->check_in) {
            $timeStr = Carbon::parse($this->check_in)->format('H:i:s');
        }

        if (! $timeStr) {
            return '-';
        }

        try {
            $masuk  = Carbon::createFromFormat('H:i:s', '06:30:00');
            $datang = Carbon::createFromFormat('H:i:s', $timeStr);

            if ($datang->lte($masuk)) {
                return '00:00 Menit';
            }

            $diffInSeconds = $masuk->diffInSeconds($datang);
            $hours         = (int) floor($diffInSeconds / 3600);
            $minutes       = (int) floor(($diffInSeconds % 3600) / 60);

            return sprintf('%02d:%02d Menit', $hours, $minutes);
        } catch (\Exception $e) {
            return '-';
        }
    }

    public function hasCheckedIn(): bool
    {
        return ! is_null($this->check_in);
    }

    public function hasCheckedOut(): bool
    {
        return ! is_null($this->check_out);
    }

    public function isPresent(): bool
    {
        return $this->status === 'hadir';
    }

    public function isPermission(): bool
    {
        return $this->status === 'izin';
    }

    public function isSick(): bool
    {
        return $this->status === 'sakit';
    }

    public function isAbsent(): bool
    {
        return $this->status === 'alpa';
    }
}