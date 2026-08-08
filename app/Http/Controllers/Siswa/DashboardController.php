<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $siswa = Auth::user();

        $riwayat = $siswa->attendances()
            ->orderBy('attendance_date', 'desc')
            ->orderBy('check_in', 'desc')
            ->limit(5)
            ->get();

        $attendanceQuery = $this->buildAttendanceQuery($request, $siswa);

        $statistics = (clone $attendanceQuery)
            ->selectRaw("SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = 'izin'  THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status = 'alpa'  THEN 1 ELSE 0 END) as alpa,
                SUM(CASE WHEN is_late = 1       THEN 1 ELSE 0 END) as terlambat,
                COUNT(*) as total_absensi,
                COUNT(DISTINCT attendance_date) as total_hari")
            ->first();

        $attendanceStatistics = [
            'hadir'      => (int) ($statistics->hadir ?? 0),
            'izin'       => (int) ($statistics->izin ?? 0),
            'sakit'      => (int) ($statistics->sakit ?? 0),
            'alpa'       => (int) ($statistics->alpa ?? 0),
            'terlambat'  => (int) ($statistics->terlambat ?? 0),
            'total_hari' => (int) ($statistics->total_hari ?? 0),
        ];

        $totalAttendance      = (int) ($statistics->total_absensi ?? 0);
        $attendancePercentage = $totalAttendance > 0
            ? round(($attendanceStatistics['hadir'] / $totalAttendance) * 100)
            : 0;

        $periodLabel = $this->periodLabel($request);

        $dailyStatus = \App\Services\AcademicCalendarService::getDailyStatus();

        return view('Siswa.Dashboard.Dashboard', [
            'siswa'                => $siswa,
            'riwayat'              => $riwayat,
            'attendanceStatistics' => $attendanceStatistics,
            'attendancePercentage' => $attendancePercentage,
            'periodLabel'          => $periodLabel,
            'dailyStatus'          => $dailyStatus,
        ]);
    }

    /**
     * Query absensi milik siswa login sesuai periode yang dipilih.
     */
    private function buildAttendanceQuery(Request $request, $siswa)
    {
        $period = $request->input('period', 'day');
        $query  = $siswa->attendances();

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
     * Label periode statistik pada Dashboard Siswa.
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
