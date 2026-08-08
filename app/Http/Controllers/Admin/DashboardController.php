<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalGuru  = User::where('role', 'teacher')->where('status', true)->count();
        $totalSiswa = User::where('role', 'student')->where('status', true)->count();
        $totalKelas = SchoolClass::where('status', true)->count();

        $absensiHariIni = Attendance::whereDate('attendance_date', today())->count();
        $hadirHariIni   = Attendance::whereDate('attendance_date', today())->where('status', 'hadir')->count();
        $izinHariIni    = Attendance::whereDate('attendance_date', today())->where('status', 'izin')->count();
        $sakitHariIni   = Attendance::whereDate('attendance_date', today())->where('status', 'sakit')->count();
        $alpaHariIni    = Attendance::whereDate('attendance_date', today())->where('status', 'alpa')->count();

        $belumAbsenHariIni = $totalSiswa - $absensiHariIni;

        $classes = SchoolClass::query()
            ->where('status', true)
            ->withCount([
                'students as students_count' => fn ($q) => $q->where('role', 'student')->where('status', true),
            ])
            ->orderBy('name')
            ->get();

        $attendanceStatistics = $this->attendanceQueryForPeriod($request)
            ->join('users', 'attendances.student_id', '=', 'users.id')
            ->where('users.role', 'student')
            ->where('users.status', true)
            ->whereIn('users.class_id', $classes->pluck('id'))
            ->selectRaw("users.class_id,
                SUM(CASE WHEN attendances.status = 'hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN attendances.status = 'izin'  THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN attendances.status = 'sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN attendances.status = 'alpa'  THEN 1 ELSE 0 END) as alpa,
                SUM(CASE WHEN attendances.is_late = 1       THEN 1 ELSE 0 END) as terlambat")
            ->groupBy('users.class_id')
            ->get()
            ->keyBy('class_id');

        $classes->each(function ($class) use ($attendanceStatistics) {
            $stat = $attendanceStatistics->get($class->id);

            $class->attendance_statistics = [
                'hadir' => (int) ($stat->hadir ?? 0),
                'izin'  => (int) ($stat->izin  ?? 0),
                'sakit' => (int) ($stat->sakit  ?? 0),
                'alpa'  => (int) ($stat->alpa   ?? 0),
            ];

            $total = array_sum($class->attendance_statistics);

            $class->attendance_percentage = $total > 0
                ? round(($class->attendance_statistics['hadir'] / $total) * 100)
                : 0;

            $class->late_count = (int) ($stat->terlambat ?? 0);
        });

        $totalTerlambatPeriode  = $classes->sum('late_count');
        $periodLabel            = $this->periodLabel($request);
        $totalEmergencyHariIni  = \App\Models\EmergencyAttendanceAudit::whereDate('input_at', today())->count();

        $dailyStatus            = \App\Services\AcademicCalendarService::getDailyStatus();

        return view('Admin.Dashboard.Dashboard', compact(
            'totalGuru',
            'totalSiswa',
            'totalKelas',
            'absensiHariIni',
            'hadirHariIni',
            'izinHariIni',
            'sakitHariIni',
            'alpaHariIni',
            'belumAbsenHariIni',
            'classes',
            'totalTerlambatPeriode',
            'periodLabel',
            'totalEmergencyHariIni',
            'dailyStatus'
        ));
    }

    public function classDetail(Request $request, $id)
    {
        $class = SchoolClass::with([
            'teacher',
            'students' => fn ($q) => $q->where('role', 'student')->where('status', true)->orderBy('name'),
        ])
            ->where('status', true)
            ->findOrFail($id);

        $statistics = $this->attendanceQueryForPeriod($request)
            ->join('users', 'attendances.student_id', '=', 'users.id')
            ->where('users.class_id', $class->id)
            ->where('users.role', 'student')
            ->where('users.status', true)
            ->selectRaw("SUM(CASE WHEN attendances.status = 'hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN attendances.status = 'izin'  THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN attendances.status = 'sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN attendances.status = 'alpa'  THEN 1 ELSE 0 END) as alpa,
                SUM(CASE WHEN attendances.is_late = 1       THEN 1 ELSE 0 END) as terlambat")
            ->first();

        $studentStatistics = $this->attendanceQueryForPeriod($request)
            ->whereIn('student_id', $class->students->pluck('id'))
            ->selectRaw("student_id,
                SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = 'izin'  THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status = 'alpa'  THEN 1 ELSE 0 END) as alpa,
                SUM(CASE WHEN is_late = 1       THEN 1 ELSE 0 END) as terlambat")
            ->groupBy('student_id')
            ->get()
            ->keyBy('student_id');

        $class->students->each(function ($student) use ($studentStatistics) {
            $stat = $studentStatistics->get($student->id);

            $student->attendance_statistics = [
                'hadir' => (int) ($stat->hadir ?? 0),
                'izin'  => (int) ($stat->izin  ?? 0),
                'sakit' => (int) ($stat->sakit  ?? 0),
                'alpa'  => (int) ($stat->alpa   ?? 0),
            ];

            $total = array_sum($student->attendance_statistics);

            $student->attendance_percentage = $total > 0
                ? round(($student->attendance_statistics['hadir'] / $total) * 100)
                : 0;

            $student->late_count = (int) ($stat->terlambat ?? 0);
        });

        $search   = trim((string) $request->input('search', ''));
        $sort     = $request->input('sort', 'name_asc');
        $students = $class->students;

        if ($search !== '') {
            $students = $students->filter(
                fn ($s) => mb_stripos($s->name, $search) !== false
                    || mb_stripos((string) $s->nis, $search) !== false
            );
        }

        $students = match ($sort) {
            'name_desc'       => $students->sortByDesc('name', SORT_NATURAL | SORT_FLAG_CASE),
            'attendance_high' => $students->sortByDesc('attendance_percentage'),
            'attendance_low'  => $students->sortBy('attendance_percentage'),
            default           => $students->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE),
        };

        $students = $students->values();

        $classStatistics = [
            'hadir' => (int) ($statistics->hadir ?? 0),
            'izin'  => (int) ($statistics->izin  ?? 0),
            'sakit' => (int) ($statistics->sakit  ?? 0),
            'alpa'  => (int) ($statistics->alpa   ?? 0),
        ];

        $totalAttendance     = array_sum($classStatistics);
        $attendancePercentage = $totalAttendance > 0
            ? round(($classStatistics['hadir'] / $totalAttendance) * 100)
            : 0;

        $classLateCount = (int) ($statistics->terlambat ?? 0);

        return view('Admin.Dashboard.ClassDetail', compact(
            'class',
            'classStatistics',
            'studentStatistics',
            'attendancePercentage',
            'classLateCount',
            'students',
            'search',
            'sort'
        ))->with('periodLabel', $this->periodLabel($request));
    }

    private function attendanceQueryForPeriod(Request $request)
    {
        $period = $request->input('period', 'day');
        $query  = Attendance::query();

        switch ($period) {
            case 'month':
                $query->whereMonth('attendances.attendance_date', $request->input('month', now()->month))
                      ->whereYear('attendances.attendance_date', $request->input('year', now()->year));
                break;

            case 'quarter':
                $quarter    = (int) $request->input('quarter', 1);
                $year       = $request->input('year', now()->year);
                $startMonth = 1 + (($quarter - 1) * 3);
                $query->whereBetween('attendances.attendance_date', [
                    Carbon::create($year, $startMonth, 1)->startOfMonth()->toDateString(),
                    Carbon::create($year, $startMonth + 2, 1)->endOfMonth()->toDateString(),
                ]);
                break;

            case 'semester':
                $semester   = (int) $request->input('semester', 1);
                $year       = $request->input('year', now()->year);
                $startMonth = $semester === 1 ? 7 : 1;
                $endMonth   = $semester === 1 ? 12 : 6;
                $query->whereBetween('attendances.attendance_date', [
                    Carbon::create($year, $startMonth, 1)->startOfMonth()->toDateString(),
                    Carbon::create($year, $endMonth, 1)->endOfMonth()->toDateString(),
                ]);
                break;

            case 'year':
                $query->whereYear('attendances.attendance_date', $request->input('year', now()->year));
                break;

            case 'day':
            default:
                $query->whereDate('attendances.attendance_date', $request->input('date', now()->toDateString()));
                break;
        }

        return $query;
    }

    private function periodLabel(Request $request): string
    {
        $period = $request->input('period', 'day');

        return match ($period) {
            'month'    => Carbon::createFromDate(
                              $request->input('year', now()->year),
                              $request->input('month', now()->month),
                              1
                          )->translatedFormat('F Y'),
            'quarter'  => 'Triwulan ' . $request->input('quarter', 1) . ' ' . $request->input('year', now()->year),
            'semester' => 'Semester '  . $request->input('semester', 1) . ' ' . $request->input('year', now()->year),
            'year'     => (string) $request->input('year', now()->year),
            default    => Carbon::parse($request->input('date', now()->toDateString()))->translatedFormat('d F Y'),
        };
    }
}
