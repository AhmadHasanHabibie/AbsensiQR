<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLock extends Model
{
    use HasFactory;

    /**
     * Nama tabel.
     */
    protected $table = 'attendance_locks';

    /**
     * Mass Assignment.
     */
    protected $fillable = [

        'teacher_id',

        'class_id',

        'attendance_date',

        'is_locked',

        'locked_at',

    ];

    /**
     * Casting.
     */
    protected $casts = [

        'attendance_date' => 'date',

        'locked_at' => 'datetime',

        'is_locked' => 'boolean',

    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    /**
     * Guru yang melakukan lock.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Kelas yang dikunci.
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek apakah kelas sudah dikunci hari ini.
     */
    public static function isLocked($classId)
    {
        return self::where('class_id', $classId)
            ->whereDate('attendance_date', today())
            ->exists();
    }

    /**
     * Membuat lock absensi.
     */
    public static function lock($teacherId, $classId)
    {
        return self::create([

            'teacher_id'      => $teacherId,

            'class_id'        => $classId,

            'attendance_date' => today(),

            'is_locked'       => true,

            'locked_at'       => now(),

        ]);
    }
}