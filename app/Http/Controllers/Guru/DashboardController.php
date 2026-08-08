<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\StudentMailbox;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard Guru
     */
    public function index(Request $request)
    {
        $teacher = Auth::user();

        $class = $teacher->homeroomClass()
            ->with([
                'students' => function ($query) {
                    $query->where('role', 'student')
                        ->where('status', true)
                        ->orderBy('name');
                },
            ])
            ->first();

        if (! $class) {
            return back()->with('error', 'Anda belum ditetapkan sebagai wali kelas.');
        }

        $students   = $class->students;
        $studentIds = $students->pluck('id');
        $totalSiswa = $students->count();

        $attendanceQuery = $this->buildAttendanceQuery($request, $class);

        $statistikPeriode = (clone $attendanceQuery)
            ->selectRaw("SUM(CASE WHEN attendances.status = 'hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN attendances.status = 'izin'  THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN attendances.status = 'sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN attendances.status = 'alpa'  THEN 1 ELSE 0 END) as alpa,
                SUM(CASE WHEN attendances.status = 'hadir' AND attendances.is_late = 1 THEN 1 ELSE 0 END) as terlambat")
            ->first();

        $hadirHariIni     = (int) ($statistikPeriode->hadir ?? 0);
        $izinHariIni      = (int) ($statistikPeriode->izin ?? 0);
        $sakitHariIni     = (int) ($statistikPeriode->sakit ?? 0);
        $alpaHariIni      = (int) ($statistikPeriode->alpa ?? 0);
        $terlambatPeriode = (int) ($statistikPeriode->terlambat ?? 0);

        $studentStatistics = (clone $attendanceQuery)
            ->whereIn('student_id', $studentIds)
            ->selectRaw("student_id,
                SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = 'izin'  THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status = 'alpa'  THEN 1 ELSE 0 END) as alpa,
                SUM(CASE WHEN status = 'hadir' AND is_late = 1 THEN 1 ELSE 0 END) as terlambat")
            ->groupBy('student_id')
            ->get()
            ->keyBy('student_id');

        $selectedDate = Carbon::parse($request->input('date', now()->toDateString()));
        $weekStart    = $selectedDate->clone()->startOfWeek(Carbon::MONDAY)->toDateString();
        $weekEnd      = $selectedDate->clone()->startOfWeek(Carbon::MONDAY)->addDays(5)->toDateString();

        $weeklyStats = Attendance::whereIn('student_id', $studentIds)
            ->whereBetween('attendance_date', [$weekStart, $weekEnd])
            ->selectRaw("student_id,
                SUM(CASE WHEN status = 'alpa' THEN 1 ELSE 0 END) as total_alpa,
                SUM(CASE WHEN is_late = 1 OR (status = 'hadir' AND is_late = 1) THEN 1 ELSE 0 END) as total_late,
                SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as total_permission")
            ->groupBy('student_id')
            ->get()
            ->keyBy('student_id');

        $existingMailboxesGrouped = StudentMailbox::whereIn('student_id', $studentIds)
            ->where('week_start', $weekStart)
            ->where('week_end', $weekEnd)
            ->get()
            ->groupBy('student_id');

        $students->each(function ($student) use ($studentStatistics, $weeklyStats, $existingMailboxesGrouped) {
            $statistics = $studentStatistics->get($student->id);
            $weeklyStat = $weeklyStats->get($student->id);

            $student->attendance_statistics = [
                'hadir' => (int) ($statistics->hadir ?? 0),
                'izin'  => (int) ($statistics->izin  ?? 0),
                'sakit' => (int) ($statistics->sakit ?? 0),
                'alpa'  => (int) ($statistics->alpa  ?? 0),
            ];

            $totalAttendance                 = array_sum($student->attendance_statistics);
            $student->attendance_percentage = $totalAttendance > 0
                ? round(($student->attendance_statistics['hadir'] / $totalAttendance) * 100)
                : 0;

            $student->late_count        = (int) ($statistics->terlambat ?? 0);
            $student->weekly_alpa       = (int) ($weeklyStat->total_alpa ?? 0);
            $student->weekly_late       = (int) ($weeklyStat->total_late ?? 0);
            $student->weekly_permission = (int) ($weeklyStat->total_permission ?? 0);

            $studentMailboxes                = $existingMailboxesGrouped->get($student->id) ?? collect();
            $student->has_alpha_mailbox      = $studentMailboxes->contains('mail_type', 'alpha');
            $student->has_late_mailbox       = $studentMailboxes->contains('mail_type', 'late');
            $student->has_permission_mailbox = $studentMailboxes->contains('mail_type', 'permission');
            $student->has_mailbox            = $student->has_alpha_mailbox;
        });

        $classStatistics = [
            'hadir' => $students->sum(fn ($student) => $student->attendance_statistics['hadir']),
            'izin'  => $students->sum(fn ($student) => $student->attendance_statistics['izin']),
            'sakit' => $students->sum(fn ($student) => $student->attendance_statistics['sakit']),
            'alpa'  => $students->sum(fn ($student) => $student->attendance_statistics['alpa']),
        ];

        $classTotalAttendance       = array_sum($classStatistics);
        $classAttendancePercentage = $classTotalAttendance > 0
            ? round(($classStatistics['hadir'] / $classTotalAttendance) * 100)
            : 0;

        $classLateCount = $students->sum('late_count');
        $periodLabel    = $this->periodLabel($request);
        $belumScan      = $totalSiswa - $hadirHariIni;
        $dailyStatus    = \App\Services\AcademicCalendarService::getDailyStatus();

        return view('Guru.Dashboard.Dashboard', compact(
            'class',
            'totalSiswa',
            'hadirHariIni',
            'izinHariIni',
            'sakitHariIni',
            'alpaHariIni',
            'terlambatPeriode',
            'belumScan',
            'students',
            'classStatistics',
            'classAttendancePercentage',
            'classLateCount',
            'periodLabel',
            'weekStart',
            'weekEnd',
            'dailyStatus'
        ));
    }

    /**
     * Query absensi kelas wali sesuai periode statistik yang dipilih.
     */
    private function buildAttendanceQuery(Request $request, $class)
    {
        $period = $request->input('period', 'day');

        $query = Attendance::query()
            ->whereHas('student', function ($query) use ($class) {
                $query->where('class_id', $class->id)
                    ->where('role', 'student')
                    ->where('status', true);
            });

        switch ($period) {
            case 'month':
                $query->whereMonth('attendance_date', $request->input('month', now()->month))
                    ->whereYear('attendance_date', $request->input('year', now()->year));
                break;

            case 'quarter':
                $quarter    = (int) $request->input('quarter', 1);
                $year       = $request->input('year', now()->year);
                $startMonth = 1 + (($quarter - 1) * 3);

                $query->whereBetween('attendance_date', [
                    Carbon::create($year, $startMonth, 1)->startOfMonth()->toDateString(),
                    Carbon::create($year, $startMonth + 2, 1)->endOfMonth()->toDateString(),
                ]);
                break;

            case 'semester':
                $semester   = (int) $request->input('semester', 1);
                $year       = $request->input('year', now()->year);
                $startMonth = $semester === 1 ? 7 : 1;
                $endMonth   = $semester === 1 ? 12 : 6;

                $query->whereBetween('attendance_date', [
                    Carbon::create($year, $startMonth, 1)->startOfMonth()->toDateString(),
                    Carbon::create($year, $endMonth, 1)->endOfMonth()->toDateString(),
                ]);
                break;

            case 'year':
                $query->whereYear('attendance_date', $request->input('year', now()->year));
                break;

            case 'day':
            default:
                $query->whereDate('attendance_date', $request->input('date', now()->toDateString()));
                break;
        }

        return $query;
    }

    /**
     * Label periode aktif pada Dashboard Guru.
     */
    private function periodLabel(Request $request): string
    {
        return match ($request->input('period', 'day')) {
            'month'    => Carbon::createFromDate($request->input('year', now()->year), $request->input('month', now()->month), 1)->translatedFormat('F Y'),
            'quarter'  => 'Triwulan ' . $request->input('quarter', 1) . ' ' . $request->input('year', now()->year),
            'semester' => 'Semester ' . $request->input('semester', 1) . ' ' . $request->input('year', now()->year),
            'year'     => (string) $request->input('year', now()->year),
            default    => Carbon::parse($request->input('date', now()->toDateString()))->translatedFormat('d F Y'),
        };
    }
}
