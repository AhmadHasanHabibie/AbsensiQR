<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceOperationOverride extends Model
{
    use HasFactory;

    protected $table = 'attendance_operation_overrides';

    protected $fillable = [
        'date',
        'is_emergency_holiday',
        'reason',
        'created_by_user_id',
    ];

    protected $casts = [
        'date'                 => 'date',
        'is_emergency_holiday' => 'boolean',
    ];

    /**
     * User (Super Administrator) yang membuat/mengubah override ini.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Mendapatkan record override untuk tanggal tertentu (default hari ini).
     */
    public static function getOverrideForDate(?string $date = null): ?self
    {
        $targetDate = $date ?? Carbon::now('Asia/Jakarta')->toDateString();

        return static::with('createdBy')
            ->where('date', $targetDate)
            ->first();
    }

    /**
     * Mengecek apakah tanggal tertentu (default hari ini) berada dalam Libur Darurat aktif.
     */
    public static function isEmergencyHoliday(?string $date = null): bool
    {
        $override = static::getOverrideForDate($date);

        return $override && $override->is_emergency_holiday;
    }
}
