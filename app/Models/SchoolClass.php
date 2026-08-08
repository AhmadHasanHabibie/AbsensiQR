<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    protected $table = 'school_classes';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'name',
        'teacher_id',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casting
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'status' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    /**
     * Wali kelas.
     */
    public function teacher()
    {
        return $this->belongsTo(
            User::class,
            'teacher_id',
            'id'
        );
    }

    /**
     * Daftar siswa dalam kelas.
     */
    public function students()
    {
        return $this->hasMany(
            User::class,
            'class_id',
            'id'
        );
    }

    /**
     * Seluruh absensi siswa dalam kelas.
     */
    public function attendances()
    {
        return $this->hasManyThrough(
            Attendance::class,
            User::class,
            'class_id',    // Foreign key pada tabel users
            'student_id',  // Foreign key pada tabel attendances
            'id',          // Local key pada school_classes
            'id'           // Local key pada users
        );
    }
}