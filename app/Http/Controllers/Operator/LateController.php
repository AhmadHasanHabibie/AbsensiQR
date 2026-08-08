<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\User;
use App\Services\AcademicCalendarService;
use App\Services\AttendanceTimeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LateController extends Controller
{
    /**
     * Menampilkan daftar siswa terlambat hari ini untuk Operator.
     */
    public function index(Request $request)
    {
        $dailyStatus = AcademicCalendarService::getDailyStatus();
        $isScanOpen  = AttendanceTimeService::isAttendanceOpen();

        $dateStr    = $request->input('date', today()->toDateString());
        $targetDate = Carbon::parse($dateStr);

        $lateAttendances = Attendance::with(['student.schoolClass', 'operator'])
            ->whereDate('attendance_date', $targetDate)
            ->where(function ($query) {
                $query->where('is_late', true)
                    ->orWhere(function ($sub) {
                        $sub->where('status', 'hadir')
                            ->whereTime('check_in', '>', '06:30:59');
                    });
            })
            ->get();

        $totalTerlambat = $lateAttendances->count();

        return view('Operator.Terlambat.Index', compact('isScanOpen', 'lateAttendances', 'totalTerlambat', 'dateStr', 'dailyStatus'));
    }

    /**
     * Form penandaan siswa terlambat.
     */
    public function create()
    {
        /*
        |--------------------------------------------------------------------------
        | Holiday Lock (TAHAP 4)
        |--------------------------------------------------------------------------
        */
        if (AcademicCalendarService::isHoliday()) {
            $status = AcademicCalendarService::currentStatus();
            return redirect()
                ->route('operator.dashboard')
                ->with('error', "Data Terlambat tidak dapat diinput karena hari ini adalah {$status}.");
        }

        $isScanOpen = AttendanceTimeService::isAttendanceOpen();

        if ($isScanOpen) {
            return redirect()
                ->route('operator.terlambat.index')
                ->with('error', 'Scan QR Absensi sedang berlangsung otomatis (00:01 - 06:30 WIB). Input keterlambatan dapat dilakukan setelah pukul 06:31 WIB.');
        }

        $excludedStudentIds = Attendance::whereDate('attendance_date', today())
            ->where(function ($query) {
                $query->whereIn('status', ['hadir', 'izin', 'sakit'])
                    ->orWhere('is_late', true);
            })
            ->pluck('student_id');

        $students = User::where('role', 'student')
            ->where('status', true)
            ->whereNotIn('id', $excludedStudentIds)
            ->with('schoolClass')
            ->orderBy('name')
            ->get();

        return view('Operator.Terlambat.Create', compact('students'));
    }

    /**
     * Simpan data siswa terlambat.
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Holiday Lock (TAHAP 4)
        |--------------------------------------------------------------------------
        */
        if (AcademicCalendarService::isHoliday()) {
            $status = AcademicCalendarService::currentStatus();
            return redirect()
                ->route('operator.terlambat.index')
                ->with('error', "Data Terlambat tidak dapat disimpan karena hari ini adalah {$status}.");
        }

        $isScanOpen = AttendanceTimeService::isAttendanceOpen();

        if ($isScanOpen) {
            return redirect()
                ->route('operator.terlambat.index')
                ->with('error', 'Scan QR Absensi sedang berlangsung otomatis (00:01 - 06:30 WIB). Input keterlambatan dapat dilakukan setelah pukul 06:31 WIB.');
        }

        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:users,id'],
            'late_time'  => ['required', 'date_format:H:i'],
            'late_note'  => ['nullable', 'string'],
        ]);

        $attendance = Attendance::whereDate('attendance_date', today())
            ->where('student_id', $validated['student_id'])
            ->first();

        if (! $attendance) {
            $attendance = new Attendance([
                'student_id'      => $validated['student_id'],
                'attendance_date' => today(),
            ]);
        }

        $checkInFormatted = Carbon::createFromFormat('H:i', $validated['late_time'])->format('H:i:s');
        $isLate           = ($checkInFormatted > '06:30:59');

        $attendance->status      = 'hadir';
        $attendance->is_late     = $isLate;
        $attendance->late_time   = $checkInFormatted;
        $attendance->check_in    = $checkInFormatted;
        $attendance->late_note   = $validated['late_note'];
        $attendance->operator_id = Auth::id();
        $attendance->save();

        $studentName = optional($attendance->student)->name ?? 'Siswa';
        ActivityLog::log(
            'Input Terlambat',
            'Attendance',
            "Operator mencatat siswa {$studentName} terlambat hadir pada jam {$validated['late_time']}."
        );

        return redirect()
            ->route('operator.terlambat.index')
            ->with('success', 'Data keterlambatan siswa berhasil disimpan.');
    }

    /**
     * Detail siswa terlambat.
     */
    public function show(string $id)
    {
        $attendance = Attendance::with(['student.schoolClass', 'operator'])
            ->where(function ($query) {
                $query->where('is_late', true)
                    ->orWhere(function ($sub) {
                        $sub->where('status', 'hadir')
                            ->whereTime('check_in', '>', '06:30:59');
                    });
            })
            ->findOrFail($id);

        return view('Operator.Terlambat.Show', compact('attendance'));
    }
}
