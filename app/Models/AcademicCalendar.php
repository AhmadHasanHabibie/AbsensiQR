<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicCalendar extends Model
{
    use HasFactory;

    protected $table = 'academic_calendars';

    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'academic_year',
        'date',
        'day_name',
        'month',
        'semester',
        'status',
        'category',
        'activity',
        'qr_status',
        'teacher_attendance',
        'student_attendance',
        'operator_attendance',
        'description',
        'is_active',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'date'                => 'date',
        'is_active'           => 'boolean',
        'qr_status'           => 'boolean',
        'teacher_attendance'  => 'boolean',
        'student_attendance'  => 'boolean',
        'operator_attendance' => 'boolean',
        'month'               => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Enum Constants — Status
    |--------------------------------------------------------------------------
    */
    const STATUS_SCHOOL_DAY    = 'Hari Belajar';
    const STATUS_NATIONAL_HOL  = 'Libur Nasional';
    const STATUS_SCHOOL_HOL    = 'Libur Sekolah';
    const STATUS_WEEKEND       = 'Libur Akhir Pekan';
    const STATUS_SUBSTITUTE    = 'Hari Pengganti';
    const STATUS_MPLS           = 'MPLS';
    const STATUS_EXAM          = 'Ujian';
    const STATUS_REPORT_CARD   = 'Pembagian Rapor';
    const STATUS_OTHER         = 'Lainnya';

    const STATUSES = [
        self::STATUS_SCHOOL_DAY,
        self::STATUS_NATIONAL_HOL,
        self::STATUS_SCHOOL_HOL,
        self::STATUS_WEEKEND,
        self::STATUS_SUBSTITUTE,
        self::STATUS_MPLS,
        self::STATUS_EXAM,
        self::STATUS_REPORT_CARD,
        self::STATUS_OTHER,
    ];

    /*
    |--------------------------------------------------------------------------
    | Enum Constants — Kategori
    |--------------------------------------------------------------------------
    */
    const CATEGORY_ACADEMIC      = 'Akademik';
    const CATEGORY_NATIONAL_HOL  = 'Libur Nasional';
    const CATEGORY_SCHOOL_HOL    = 'Libur Sekolah';
    const CATEGORY_WEEKEND       = 'Libur Akhir Pekan';
    const CATEGORY_EVENT         = 'Kegiatan Sekolah';
    const CATEGORY_EXAM          = 'Ujian';
    const CATEGORY_ADMIN         = 'Administrasi';
    const CATEGORY_OTHER         = 'Lainnya';

    const CATEGORIES = [
        self::CATEGORY_ACADEMIC,
        self::CATEGORY_NATIONAL_HOL,
        self::CATEGORY_SCHOOL_HOL,
        self::CATEGORY_WEEKEND,
        self::CATEGORY_EVENT,
        self::CATEGORY_EXAM,
        self::CATEGORY_ADMIN,
        self::CATEGORY_OTHER,
    ];

    /*
    |--------------------------------------------------------------------------
    | Enum Constants — Semester
    |--------------------------------------------------------------------------
    */
    const SEMESTER_ODD  = 'Ganjil';
    const SEMESTER_EVEN = 'Genap';

    const SEMESTERS = [
        self::SEMESTER_ODD,
        self::SEMESTER_EVEN,
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope: filter berdasarkan tahun ajaran aktif.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: filter berdasarkan tahun ajaran tertentu.
     */
    public function scopeByYear(Builder $query, string $year): Builder
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Scope: filter berdasarkan semester.
     */
    public function scopeBySemester(Builder $query, string $semester): Builder
    {
        return $query->where('semester', $semester);
    }

    /**
     * Scope: filter berdasarkan status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: filter berdasarkan kategori.
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: hari libur (bukan hari belajar).
     */
    public function scopeHolidays(Builder $query): Builder
    {
        return $query->where('status', '!=', self::STATUS_SCHOOL_DAY);
    }

    /**
     * Scope: hanya hari belajar.
     */
    public function scopeSchoolDays(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SCHOOL_DAY);
    }

    /*
    |--------------------------------------------------------------------------
    | Static Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan record kalender hari ini.
     */
    public static function today(): ?self
    {
        return static::where('date', today()->toDateString())->first();
    }

    /**
     * Mendapatkan tahun ajaran yang sedang aktif.
     * Mengembalikan string tahun ajaran (e.g. "2026/2027") atau null.
     */
    public static function activeYear(): ?string
    {
        return static::where('is_active', true)
            ->value('academic_year');
    }

    /**
     * Mendapatkan status hari ini sebagai string.
     * Mengembalikan null jika tidak ada data.
     */
    public static function todayStatus(): ?string
    {
        return static::today()?->status;
    }

    /**
     * Apakah hari ini adalah hari libur?
     */
    public static function isHoliday(): bool
    {
        $today = static::today();

        if (! $today) {
            return false;
        }

        return $today->status !== self::STATUS_SCHOOL_DAY;
    }

    /**
     * Apakah hari ini adalah hari belajar?
     */
    public static function isSchoolDay(): bool
    {
        $today = static::today();

        if (! $today) {
            return false;
        }

        return $today->status === self::STATUS_SCHOOL_DAY;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessor Helper
    |--------------------------------------------------------------------------
    */

    /**
     * Badge class Bootstrap berdasarkan status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_SCHOOL_DAY   => 'bg-success',
            self::STATUS_NATIONAL_HOL => 'bg-danger',
            self::STATUS_SCHOOL_HOL   => 'bg-warning text-dark',
            self::STATUS_WEEKEND      => 'bg-secondary',
            self::STATUS_SUBSTITUTE   => 'bg-info text-dark',
            self::STATUS_MPLS         => 'bg-primary',
            self::STATUS_EXAM         => 'bg-purple',
            self::STATUS_REPORT_CARD  => 'bg-orange',
            default                   => 'bg-secondary',
        };
    }

    /**
     * Badge class Bootstrap berdasarkan kategori.
     */
    public function getCategoryBadgeClassAttribute(): string
    {
        return match ($this->category) {
            self::CATEGORY_ACADEMIC     => 'bg-success-subtle text-success border border-success-subtle',
            self::CATEGORY_NATIONAL_HOL => 'bg-danger-subtle text-danger border border-danger-subtle',
            self::CATEGORY_SCHOOL_HOL   => 'bg-warning-subtle text-warning border border-warning-subtle',
            self::CATEGORY_WEEKEND      => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            self::CATEGORY_EVENT        => 'bg-info-subtle text-info border border-info-subtle',
            self::CATEGORY_EXAM         => 'bg-primary-subtle text-primary border border-primary-subtle',
            self::CATEGORY_ADMIN        => 'bg-dark bg-opacity-10 text-dark border border-secondary-subtle',
            default                     => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
        };
    }
}
