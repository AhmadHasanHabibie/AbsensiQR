<?php

namespace App\Services;

use App\Models\AcademicCalendar;
use App\Models\AttendanceOperationOverride;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AcademicCalendarService
{
    /*
    |--------------------------------------------------------------------------
    | SINGLE SOURCE OF TRUTH — Central Engine Status
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan status operasional harian terpusat untuk tanggal tertentu.
     *
     * Prioritas:
     * 1. Emergency Override
     * 2. Kalender Akademik
     * 3. Fallback (Hari Belajar / Libur Akhir Pekan)
     *
     * @param  string|null  $date  Format YYYY-MM-DD (Default: Hari Ini)
     * @return array
     */
    public static function getDailyStatus(?string $date = null): array
    {
        $targetDate = $date ? Carbon::parse($date)->toDateString() : Carbon::now('Asia/Jakarta')->toDateString();
        $dt = Carbon::parse($targetDate);

        /*
        |--------------------------------------------------------------------------
        | 1. EMERGENCY OVERRIDE (PRIORITAS UTAMA)
        |--------------------------------------------------------------------------
        */
        $override = AttendanceOperationOverride::getOverrideForDate($targetDate);

        if ($override && $override->is_emergency_holiday) {
            return [
                'date'                  => $targetDate,
                'day_name'              => static::getDayNameIndonesian($dt->dayOfWeekIso),
                'is_school_day'         => false,
                'is_holiday'            => true,
                'is_emergency'          => true,
                'status'                => 'Libur Darurat',
                'category'              => 'Libur Darurat',
                'activity'              => $override->reason ?: 'Libur Darurat diaktifkan oleh Super Administrator',
                'description'           => $override->reason ?: 'Seluruh kegiatan operasional absensi dihentikan sementara.',
                'source'                => 'Emergency Override',
                'badge_class'           => 'bg-danger text-white',
                'badge_style'           => 'badge-national-hol',
                'allow_qr_scan'         => false,
                'allow_confirmation'    => false,
                'allow_emergency_input' => false,
                'allow_reminder'        => false,
                'updated_by'            => optional($override->createdBy)->name ?? 'Super Administrator',
                'updated_at'            => $override->updated_at?->format('d/m/Y H:i') ?? '—',
                'reason'                => $override->reason,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 2. KALENDER AKADEMIK (PRIORITAS KEDUA)
        |--------------------------------------------------------------------------
        */
        $calendarEntry = AcademicCalendar::where('date', $targetDate)->first();

        if ($calendarEntry) {
            $isSchoolDay = ($calendarEntry->status === AcademicCalendar::STATUS_SCHOOL_DAY);

            return [
                'date'                  => $targetDate,
                'day_name'              => $calendarEntry->day_name,
                'is_school_day'         => $isSchoolDay,
                'is_holiday'            => ! $isSchoolDay,
                'is_emergency'          => false,
                'status'                => $calendarEntry->status,
                'category'              => $calendarEntry->category,
                'activity'              => $calendarEntry->activity ?: $calendarEntry->status,
                'description'           => $calendarEntry->description,
                'source'                => 'Kalender Akademik',
                'badge_class'           => $calendarEntry->status_badge_class,
                'badge_style'           => static::getBadgeStyleForStatus($calendarEntry->status),
                'allow_qr_scan'         => $isSchoolDay && $calendarEntry->qr_status,
                'allow_confirmation'    => $isSchoolDay && $calendarEntry->teacher_attendance,
                'allow_emergency_input' => $isSchoolDay && $calendarEntry->operator_attendance,
                'allow_reminder'        => $isSchoolDay,
                'updated_by'            => 'System (Kalender Akademik)',
                'updated_at'            => $calendarEntry->updated_at?->format('d/m/Y H:i') ?? '—',
                'reason'                => $calendarEntry->activity ?: $calendarEntry->status,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 3. FALLBACK (Jika belum ada data di Kalender)
        |--------------------------------------------------------------------------
        */
        $isWeekend  = $dt->isWeekend();
        $statusName = $isWeekend ? AcademicCalendar::STATUS_WEEKEND : AcademicCalendar::STATUS_SCHOOL_DAY;

        return [
            'date'                  => $targetDate,
            'day_name'              => static::getDayNameIndonesian($dt->dayOfWeekIso),
            'is_school_day'         => ! $isWeekend,
            'is_holiday'            => $isWeekend,
            'is_emergency'          => false,
            'status'                => $statusName,
            'category'              => $isWeekend ? AcademicCalendar::CATEGORY_WEEKEND : AcademicCalendar::CATEGORY_ACADEMIC,
            'activity'              => $statusName,
            'description'           => null,
            'source'                => 'Sistem (Fallback Standard)',
            'badge_class'           => $isWeekend ? 'bg-secondary' : 'bg-success',
            'badge_style'           => $isWeekend ? 'badge-weekend' : 'badge-school-day',
            'allow_qr_scan'         => ! $isWeekend,
            'allow_confirmation'    => ! $isWeekend,
            'allow_emergency_input' => ! $isWeekend,
            'allow_reminder'        => ! $isWeekend,
            'updated_by'            => 'System',
            'updated_at'            => '—',
            'reason'                => $statusName,
        ];
    }

    /**
     * Static helper: Apakah tanggal ini hari belajar?
     */
    public static function isSchoolDay(?string $date = null): bool
    {
        return static::getDailyStatus($date)['is_school_day'];
    }

    /**
     * Static helper: Apakah tanggal ini hari libur?
     */
    public static function isHoliday(?string $date = null): bool
    {
        return static::getDailyStatus($date)['is_holiday'];
    }

    /**
     * Static helper: Apakah tanggal ini sedang dalam Emergency Override?
     */
    public static function isEmergencyOverride(?string $date = null): bool
    {
        return static::getDailyStatus($date)['is_emergency'];
    }

    /**
     * Static helper: Status string untuk tanggal ini (e.g. "Hari Belajar", "Libur Nasional", "Libur Darurat").
     */
    public static function currentStatus(?string $date = null): string
    {
        return static::getDailyStatus($date)['status'];
    }

    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan statistik kalender untuk tahun ajaran tertentu.
     *
     * @param  string|null  $year  Jika null, gunakan tahun aktif
     * @return array
     */
    public function getStatsByYear(?string $year = null): array
    {
        $year = $year ?? AcademicCalendar::activeYear();

        if (! $year) {
            return $this->emptyStats();
        }

        $query = AcademicCalendar::byYear($year);

        $total      = (clone $query)->count();
        $schoolDays = (clone $query)->schoolDays()->count();
        $holidays   = (clone $query)->holidays()->count();
        $today      = AcademicCalendar::today();

        return [
            'academic_year' => $year,
            'total'         => $total,
            'school_days'   => $schoolDays,
            'holidays'      => $holidays,
            'today'         => $today,
            'has_data'      => $total > 0,
        ];
    }

    /**
     * Statistik kosong (belum ada data).
     */
    private function emptyStats(): array
    {
        return [
            'academic_year' => null,
            'total'         => 0,
            'school_days'   => 0,
            'holidays'      => 0,
            'today'         => null,
            'has_data'      => false,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Daftar Tahun Ajaran yang Tersedia
    |--------------------------------------------------------------------------
    */

    public function getAvailableYears(): \Illuminate\Support\Collection
    {
        return AcademicCalendar::select('academic_year', 'is_active')
            ->groupBy('academic_year', 'is_active')
            ->orderByDesc('academic_year')
            ->get()
            ->unique('academic_year')
            ->map(function ($item) {
                return [
                    'year'      => $item->academic_year,
                    'is_active' => $item->is_active,
                ];
            })
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | Aktivasi Tahun Ajaran
    |--------------------------------------------------------------------------
    */

    public function activateYear(string $year): bool
    {
        $exists = AcademicCalendar::byYear($year)->exists();

        if (! $exists) {
            throw new \Exception("Tahun ajaran {$year} tidak ditemukan dalam database.");
        }

        DB::transaction(function () use ($year) {
            AcademicCalendar::query()->update(['is_active' => false]);
            AcademicCalendar::where('academic_year', $year)->update(['is_active' => true]);
        });

        return true;
    }

    public function getYearSummary(): array
    {
        $rows = AcademicCalendar::select(
            'academic_year',
            DB::raw('MAX(is_active) as is_active'),
            DB::raw('COUNT(*) as total_days'),
            DB::raw('SUM(CASE WHEN status = "Hari Belajar" THEN 1 ELSE 0 END) as school_days'),
            DB::raw('SUM(CASE WHEN status != "Hari Belajar" THEN 1 ELSE 0 END) as holiday_days')
        )
        ->groupBy('academic_year')
        ->orderByDesc('academic_year')
        ->get();

        return $rows->map(function ($row) {
            return [
                'academic_year' => $row->academic_year,
                'is_active'     => (bool) $row->is_active,
                'total_days'    => $row->total_days,
                'school_days'   => $row->school_days,
                'holiday_days'  => $row->holiday_days,
            ];
        })->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Private Helpers
    |--------------------------------------------------------------------------
    */

    private static function getDayNameIndonesian(int $isoDay): string
    {
        return match ($isoDay) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
            default => 'Senin',
        };
    }

    private static function getBadgeStyleForStatus(string $status): string
    {
        return match ($status) {
            'Hari Belajar'      => 'badge-school-day',
            'Libur Nasional'    => 'badge-national-hol',
            'Libur Sekolah'     => 'badge-school-hol',
            'Libur Akhir Pekan' => 'badge-weekend',
            'Hari Pengganti'    => 'badge-substitute',
            'MPLS'              => 'badge-mpls',
            'Ujian'             => 'badge-exam',
            'Pembagian Rapor'   => 'badge-report-card',
            default             => 'badge-other',
        };
    }
}
