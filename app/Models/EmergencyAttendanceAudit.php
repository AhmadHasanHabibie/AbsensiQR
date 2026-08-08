<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyAttendanceAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'operator_id',
        'teacher_id',
        'student_id',
        'class_id',
        'reason',
        'note',
        'ip_address',
        'user_agent',
        'device',
        'initial_status',
        'final_status',
        'validation_type',
        'input_at',
        'validated_at',
    ];

    protected $casts = [
        'input_at'     => 'datetime',
        'validated_at' => 'datetime',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}
