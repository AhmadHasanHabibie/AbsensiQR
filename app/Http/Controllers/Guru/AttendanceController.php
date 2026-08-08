<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\AttendanceLock;
use App\Models\EmergencyAttendanceAudit;
use App\Models\User;
use App\Services\AcademicCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Rekap absensi hari ini.
     */
    public function index()
    {
        $teacher = Auth::user();

        ActivityLog::log(
            'Akses Konfirmasi Absensi',
            'Attendance',
            "Guru Wali Kelas {$teacher->name} membuka Halaman Rekap & Konfirmasi Kehadiran Hari Ini."
        );

        if (! $teacher->homeroomClass) {
            return back()->with('error', 'Anda belum ditetapkan sebagai wali kelas.');
        }

        $class = $teacher->homeroomClass;

        $students = User::where('role', 'student')
            ->where('status', true)
            ->where('class_id', $class->id)
            ->orderBy('name')
            ->get();

        $attendances = Attendance::with(['operator', 'emergencyAudit.operator', 'emergencyAudit.teacher'])
            ->whereDate('attendance_date', today())
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        foreach ($students as $student) {
            $attendance          = $attendances->get($student->id);
            $student->attendance = $attendance;

            if ($attendance) {
                if ($attendance->is_emergency && ! AttendanceLock::isLocked($class->id)) {
                    $student->attendance_status = 'hadir_manual';
                } else {
                    $student->attendance_status = $attendance->status;
                }
            } else {
                $student->attendance_status = 'belum_hadir';
            }
        }

        $hadir       = $students->where('attendance_status', 'hadir')->count();
        $hadirManual = $students->where('attendance_status', 'hadir_manual')->count();
        $izin        = $students->where('attendance_status', 'izin')->count();
        $sakit       = $students->where('attendance_status', 'sakit')->count();
        $alpa        = $students->where('attendance_status', 'alpa')->count();
        $belumHadir  = $students->where('attendance_status', 'belum_hadir')->count();

        $isLocked = AttendanceLock::isLocked($class->id);
        $dailyStatus = AcademicCalendarService::getDailyStatus();

        return view('Guru.Attendance.Index', compact(
            'class',
            'students',
            'hadir',
            'hadirManual',
            'izin',
            'sakit',
            'alpa',
            'belumHadir',
            'isLocked',
            'dailyStatus'
        ));
    }

    /**
     * Konfirmasi absensi.
     */
    public function confirm(Request $request)
    {
        $teacher = Auth::user();

        if (! $teacher->homeroomClass) {
            return back()->with('error', 'Anda belum ditetapkan sebagai wali kelas.');
        }

        $class = $teacher->homeroomClass;

        if (! AcademicCalendarService::isSchoolDay()) {
            $status = AcademicCalendarService::currentStatus();
            return redirect()
                ->route('guru.attendance.index')
                ->with('error', "Konfirmasi absensi ditutup karena hari ini adalah {$status}.");
        }

        if (AttendanceLock::isLocked($class->id)) {
            return redirect()
                ->route('guru.attendance.index')
                ->with('error', 'Absensi hari ini sudah dikonfirmasi.');
        }

        $autoApprovedCount  = 0;
        $manualChangedCount = 0;

        DB::transaction(function () use ($request, $teacher, $class, &$autoApprovedCount, &$manualChangedCount) {
            $students = User::where('role', 'student')
                ->where('status', true)
                ->where('class_id', $class->id)
                ->get();

            $attendances = Attendance::whereDate('attendance_date', today())
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->keyBy('student_id');

            foreach ($students as $student) {
                $attendance  = $attendances->get($student->id);
                $chosenStatus = $request->input('status.' . $student->id);

                if ($attendance) {
                    if ($attendance->is_emergency) {
                        $isAutoApproved = false;
                        if ($chosenStatus && in_array($chosenStatus, ['hadir', 'izin', 'sakit', 'alpa', 'terlambat'])) {
                            $attendance->status = $chosenStatus;
                            if ($chosenStatus === 'terlambat') {
                                $attendance->is_late = true;
                            }
                            if ($chosenStatus === 'hadir') {
                                $isAutoApproved = true;
                                $autoApprovedCount++;
                            } else {
                                $manualChangedCount++;
                            }
                        } else {
                            $attendance->status = 'hadir';
                            $isAutoApproved     = true;
                            $autoApprovedCount++;
                        }
                        $attendance->save();

                        $audit = EmergencyAttendanceAudit::where('attendance_id', $attendance->id)->first();
                        if ($audit) {
                            $audit->teacher_id      = $teacher->id;
                            $audit->final_status    = ucfirst($attendance->status);
                            $audit->validation_type = $isAutoApproved ? 'automatic' : 'manual';
                            $audit->validated_at    = now();
                            $audit->save();
                        }
                    } elseif ($chosenStatus && in_array($chosenStatus, ['hadir', 'izin', 'sakit', 'alpa', 'terlambat'])) {
                        $attendance->status = $chosenStatus;
                        if ($chosenStatus === 'terlambat') {
                            $attendance->is_late = true;
                        }
                        $attendance->save();
                        $manualChangedCount++;
                    }
                    continue;
                }

                $status = $chosenStatus ?? 'alpa';

                Attendance::create([
                    'student_id'      => $student->id,
                    'attendance_date' => today(),
                    'check_in'        => null,
                    'check_out'       => null,
                    'status'          => $status,
                ]);
            }

            AttendanceLock::lock($teacher->id, $class->id);

            ActivityLog::log(
                'Konfirmasi Absensi',
                'Attendance',
                "Guru {$teacher->name} mengonfirmasi seluruh absensi hari ini untuk kelas {$class->name}."
            );
        });

        $message = 'Absensi hari ini berhasil dikonfirmasi. Seluruh data telah dikunci.';
        if ($autoApprovedCount > 0) {
            $message = "{$autoApprovedCount} Absensi Darurat telah berhasil divalidasi dan diubah menjadi Hadir. Seluruh data telah dikunci.";
        } elseif ($manualChangedCount > 0) {
            $message = 'Perubahan status berhasil disimpan dan dikonfirmasi. Seluruh data telah dikunci.';
        }

        return redirect()
            ->route('guru.attendance.index')
            ->with('success', $message);
    }

    /**
     * Update status (opsional AJAX).
     */
    public function updateStatus(Request $request)
    {
        return response()->json([
            'success' => true,
        ]);
    }
}
