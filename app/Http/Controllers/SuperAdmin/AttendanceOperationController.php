<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AttendanceOperationOverride;
use App\Services\AcademicCalendarService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceOperationController extends Controller
{
    /**
     * Halaman Pengaturan Operasional Absensi & Emergency Override.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Audit Log
        ActivityLog::log(
            'Melihat Operasional Absensi',
            'Konfigurasi Sistem',
            "Pengguna {$user->name} membuka Halaman Operasional Absensi & Status Libur Darurat.",
            $user
        );

        $todayStr = Carbon::now('Asia/Jakarta')->toDateString();
        $dailyStatus = AcademicCalendarService::getDailyStatus($todayStr);
        $overrideToday = AttendanceOperationOverride::getOverrideForDate($todayStr);

        $overrideHistory = AttendanceOperationOverride::with('createdBy')
            ->orderByDesc('date')
            ->paginate(15);

        return view('SuperAdmin.AttendanceOperation.Index', compact(
            'todayStr',
            'dailyStatus',
            'overrideToday',
            'overrideHistory'
        ));
    }

    /**
     * Toggle status Libur Darurat untuk hari ini (Aktifkan / Nonaktifkan).
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'action' => ['required', 'in:enable,disable'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        $todayStr = Carbon::now('Asia/Jakarta')->toDateString();
        $enable = ($request->action === 'enable');
        $reason = trim($request->input('reason') ?? '');

        if ($enable && empty($reason)) {
            $reason = 'Libur Darurat diaktifkan oleh Super Administrator';
        }

        $override = AttendanceOperationOverride::firstOrNew(['date' => $todayStr]);
        $override->is_emergency_holiday = $enable;
        $override->reason = $enable ? $reason : null;
        $override->created_by_user_id = $user->id;
        $override->save();

        if ($enable) {
            ActivityLog::log(
                'Aktifkan Libur Darurat',
                'Operasional Absensi',
                "Super Administrator {$user->name} MENGAKTIFKAN Libur Darurat untuk tanggal {$todayStr}. Alasan: {$reason}",
                $user
            );

            return back()->with('success', 'LIBUR DARURAT BERHASIL DIAKTIFKAN. Seluruh proses absensi hari ini dihentikan.');
        } else {
            ActivityLog::log(
                'Nonaktifkan Libur Darurat',
                'Operasional Absensi',
                "Super Administrator {$user->name} MENONAKTIFKAN Libur Darurat untuk tanggal {$todayStr}. Sistem kembali mengikuti Kalender Akademik.",
                $user
            );

            return back()->with('success', 'LIBUR DARURAT DINONAKTIFKAN. Operasional absensi kembali mengikuti Kalender Akademik.');
        }
    }
}
