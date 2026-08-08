<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLock;
use App\Models\User;
use App\Services\AttendanceTimeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
{
    /**
     * Menampilkan halaman scanner QR.
     */
    public function index()
    {
        $isScanOpen  = AttendanceTimeService::isAttendanceOpen();
        $isPastLimit = AttendanceTimeService::isAttendanceExpired();

        return view('Guru.Scan.Index', compact('isScanOpen', 'isPastLimit'));
    }

    /**
     * Proses scan QR siswa.
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Centralized Server Time Validation (Asia/Jakarta)
        |--------------------------------------------------------------------------
        */
        if (!AttendanceTimeService::isAttendanceOpen()) {
            return response()->json([
                'success' => false,
                'message' => AttendanceTimeService::getClosedReasonMessage(),
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi Request
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'qr_token' => ['required', 'string'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Ambil Guru Login
        |--------------------------------------------------------------------------
        */

        $teacher = Auth::user();

        if (!$teacher->homeroomClass) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum memiliki kelas wali.',
            ], 403);
        }

        $class = $teacher->homeroomClass;

        /*
        |--------------------------------------------------------------------------
        | Cek Lock Absensi Hari Ini
        |--------------------------------------------------------------------------
        */

        $lock = AttendanceLock::where('class_id', $class->id)
            ->whereDate('attendance_date', today())
            ->first();

        if ($lock) {
            return response()->json([
                'success' => false,
                'message' => 'Absensi hari ini sudah dikonfirmasi dan telah dikunci.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Cari Siswa Berdasarkan QR Token
        |--------------------------------------------------------------------------
        */

        $student = User::where('role', 'student')
            ->where('status', true)
            ->where('class_id', $class->id)
            ->where('qr_token', $request->qr_token)
            ->with('schoolClass')
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau siswa bukan berasal dari kelas Anda.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Cek Apakah Sudah Scan Hari Ini
        |--------------------------------------------------------------------------
        */

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
                    'class' => optional($student->schoolClass)->name,
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Simpan Absensi
        |--------------------------------------------------------------------------
        */

        $attendance = Attendance::create([
            'student_id'      => $student->id,
            'attendance_date' => today(),
            'check_in'        => now('Asia/Jakarta')->format('H:i:s'),
            'check_out'       => null,
            'status'          => 'hadir',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Response Berhasil
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil.',

            'student' => [
                'id'    => $student->id,
                'name'  => $student->name,
                'nis'   => $student->nis,
                'class' => optional($student->schoolClass)->name,
            ],

            'attendance' => [
                'id'              => $attendance->id,
                'attendance_date' => $attendance->attendance_date,
                'check_in'        => $attendance->check_in,
                'status'          => $attendance->status,
            ],

            'time' => now('Asia/Jakarta')->format('H:i:s'),
        ]);
    }
}