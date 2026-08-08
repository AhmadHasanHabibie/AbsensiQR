<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\EmergencyAttendanceAudit;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\AttendanceTimeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmergencyController extends Controller
{
    /**
     * Menampilkan Halaman Presensi Darurat Operator.
     */
    public function index()
    {
        ActivityLog::log(
            'Akses Form Absensi Darurat',
            'Attendance',
            'Operator ' . Auth::user()->name . ' membuka Form Presensi Darurat.'
        );

        $isScanOpen = AttendanceTimeService::isAttendanceOpen();

        $excludedStudentIds = Attendance::whereDate('attendance_date', today())->pluck('student_id');

        $students = User::where('role', 'student')
            ->where('status', true)
            ->whereNotIn('id', $excludedStudentIds)
            ->with('schoolClass')
            ->orderBy('name')
            ->get();

        $classes = SchoolClass::where('status', true)
            ->orderBy('name')
            ->get();

        $emergencyAttendances = Attendance::with(['student.schoolClass', 'operator'])
            ->whereDate('attendance_date', today())
            ->where('is_emergency', true)
            ->orderBy('id', 'desc')
            ->get();

        $totalEmergencyHariIni = $emergencyAttendances->count();
        $dailyStatus           = \App\Services\AcademicCalendarService::getDailyStatus();

        return view('Operator.Emergency.Index', compact(
            'isScanOpen',
            'students',
            'classes',
            'emergencyAttendances',
            'totalEmergencyHariIni',
            'dailyStatus'
        ));
    }

    /**
     * Menyimpan Data Absensi Darurat.
     */
    public function store(Request $request)
    {
        if (! \App\Services\AcademicCalendarService::isSchoolDay()) {
            $status = \App\Services\AcademicCalendarService::currentStatus();
            return back()
                ->withInput()
                ->with('error', "Absensi Darurat tidak dapat dicatat karena hari ini adalah {$status}.");
        }

        $validated = $request->validate([
            'class_id'       => ['nullable', 'integer', 'exists:school_classes,id'],
            'student_id'     => ['required', 'integer', 'exists:users,id'],
            'check_in'       => ['required', 'date_format:H:i'],
            'reason_option'  => ['required', 'string'],
            'reason_custom'  => ['nullable', 'string', 'max:255'],
            'emergency_note' => ['nullable', 'string', 'max:500'],
        ], [
            'student_id.required'    => 'Nama siswa wajib dipilih.',
            'check_in.required'      => 'Jam datang wajib diisi.',
            'reason_option.required' => 'Alasan presensi darurat wajib dipilih.',
        ]);

        if ($validated['reason_option'] === 'Lainnya' && empty(trim($validated['reason_custom'] ?? ''))) {
            return back()
                ->withInput()
                ->with('error', 'Keterangan spesifik wajib diisi apabila memilih alasan Lainnya.');
        }

        $existing = Attendance::whereDate('attendance_date', today())
            ->where('student_id', $validated['student_id'])
            ->exists();

        if ($existing) {
            return redirect()
                ->route('operator.emergency.index')
                ->with('error', 'Siswa tersebut sudah memiliki data absensi hari ini.');
        }

        $reason = $validated['reason_option'] === 'Lainnya'
            ? trim($validated['reason_custom'])
            : $validated['reason_option'];

        $checkInFormatted = Carbon::createFromFormat('H:i', $validated['check_in'])->format('H:i:s');
        $isLate           = ($checkInFormatted > '06:30:59');

        $attendance = new Attendance([
            'student_id'       => $validated['student_id'],
            'attendance_date'  => today(),
            'check_in'         => $checkInFormatted,
            'status'           => 'hadir',
            'is_late'          => $isLate,
            'late_time'        => $isLate ? $checkInFormatted : null,
            'late_note'        => $isLate ? ($validated['emergency_note'] ?? $reason) : null,
            'is_emergency'     => true,
            'emergency_reason' => $reason,
            'emergency_note'   => $validated['emergency_note'] ?? null,
            'operator_id'      => Auth::id(),
        ]);

        $attendance->save();

        $student      = $attendance->student;
        $studentName  = optional($student)->name ?? 'Siswa';
        $className    = optional(optional($student)->schoolClass)->name ?? '-';
        $operatorName = Auth::user()->name;

        EmergencyAttendanceAudit::create([
            'attendance_id'  => $attendance->id,
            'operator_id'    => Auth::id(),
            'student_id'     => $attendance->student_id,
            'class_id'       => optional($student)->class_id,
            'reason'         => $reason,
            'note'           => $validated['emergency_note'] ?? null,
            'ip_address'     => $request->ip(),
            'user_agent'     => substr($request->userAgent() ?? '', 0, 255),
            'device'         => 'Web Browser',
            'initial_status' => 'Hadir Manual',
            'input_at'       => now('Asia/Jakarta'),
        ]);

        ActivityLog::log(
            'Absensi Darurat',
            'Attendance',
            "Operator {$operatorName} mencatat absensi darurat (Hadir Manual) untuk siswa {$studentName} ({$className}) karena {$reason}."
        );

        return redirect()
            ->route('operator.emergency.index')
            ->with('success', "Absensi Darurat (Hadir Manual) untuk {$studentName} berhasil disimpan.");
    }
}
