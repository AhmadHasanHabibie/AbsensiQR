<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\User;
use App\Services\AttendanceTimeService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    /**
     * Halaman Scan QR Operator (Fully Automatic Time-Based Attendance)
     */
    public function index()
    {
        $isScanOpen  = AttendanceTimeService::isAttendanceOpen();
        $isPastLimit = AttendanceTimeService::isAttendanceExpired();
        $currentTime = Carbon::now('Asia/Jakarta')->format('H:i:s');

        $recentScans = Attendance::whereDate('attendance_date', today())
            ->whereNotNull('check_in')
            ->with(['student.schoolClass'])
            ->latest()
            ->take(10)
            ->get();

        return view('Operator.Scan.Index', compact('isScanOpen', 'isPastLimit', 'currentTime', 'recentScans'));
    }

    /**
     * Proses Scan QR Siswa oleh Operator
     */
    public function store(Request $request)
    {
        if (! AttendanceTimeService::isAttendanceOpen()) {
            return response()->json([
                'success' => false,
                'message' => AttendanceTimeService::getClosedReasonMessage(),
            ], 403);
        }

        $request->validate([
            'qr_token' => ['required', 'string'],
        ]);

        $student = User::where('role', 'student')
            ->where('status', true)
            ->where('qr_token', $request->qr_token)
            ->with('schoolClass')
            ->first();

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid.',
            ], 404);
        }

        $attendance = Attendance::where('student_id', $student->id)
            ->whereDate('attendance_date', today())
            ->first();

        if ($attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa sudah melakukan absensi hari ini.',
                'student' => [
                    'name'  => $student->name,
                    'nis'   => $student->nis,
                    'class' => optional($student->schoolClass)->name ?? '-',
                ],
            ]);
        }

        $now = Carbon::now('Asia/Jakarta');

        $attendance = Attendance::create([
            'student_id'      => $student->id,
            'attendance_date' => today(),
            'check_in'        => $now->format('H:i:s'),
            'check_out'       => null,
            'status'          => 'hadir',
        ]);

        $className = optional($student->schoolClass)->name ?? '-';
        ActivityLog::log(
            'Scan QR Berhasil',
            'Attendance',
            "Operator berhasil melakukan scan absensi untuk siswa {$student->name} ({$className})."
        );

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil.',
            'student' => [
                'id'    => $student->id,
                'name'  => $student->name,
                'nis'   => $student->nis,
                'class' => optional($student->schoolClass)->name ?? '-',
            ],
            'attendance' => [
                'id'              => $attendance->id,
                'attendance_date' => $attendance->attendance_date,
                'check_in'        => $attendance->check_in,
                'status'          => $attendance->status,
            ],
            'time' => $now->format('H:i:s'),
        ]);
    }
}
