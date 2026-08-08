<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLock;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Guru Piket dengan statistik realtime konfirmasi absensi
     */
    public function index()
    {
        $totalClasses = SchoolClass::where('status', true)->count();

        $confirmedCount = AttendanceLock::whereDate('attendance_date', today())
            ->where('is_locked', true)
            ->count();

        $unconfirmedCount = max(0, $totalClasses - $confirmedCount);

        $percentage = $totalClasses > 0 ? round(($confirmedCount / $totalClasses) * 100, 1) : 0;

        $dailyStatus = \App\Services\AcademicCalendarService::getDailyStatus();

        return view('Piket.Dashboard.Index', compact(
            'totalClasses',
            'confirmedCount',
            'unconfirmedCount',
            'percentage',
            'dailyStatus'
        ));
    }

    /**
     * Menampilkan Profil Guru Piket
     */
    public function profil()
    {
        return view('Piket.Profil.Index');
    }
}
