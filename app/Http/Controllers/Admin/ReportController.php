<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AdminAttendanceExport;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Halaman laporan admin
     */
    public function index(Request $request)
    {
        [$query, $dateLabel] = $this->buildQuery($request);

        $attendances = $query->orderBy('student_id')->get();

        $hadir     = $attendances->where('status', 'hadir')->count();
        $izin      = $attendances->where('status', 'izin')->count();
        $sakit     = $attendances->where('status', 'sakit')->count();
        $alpa      = $attendances->where('status', 'alpa')->count();
        $terlambat = $attendances->where('is_late', true)->count();

        $dateStr     = $request->input('date', today()->toDateString());
        $dailyStatus = \App\Services\AcademicCalendarService::getDailyStatus($dateStr);

        return view('Admin.Laporan.Index', compact(
            'attendances',
            'hadir',
            'izin',
            'sakit',
            'alpa',
            'terlambat',
            'dateLabel',
            'dailyStatus'
        ));
    }

    /**
     * Export PDF
     */
    public function exportPdf(Request $request)
    {
        [$query, $dateLabel] = $this->buildQuery($request);

        $attendances = $query->orderBy('student_id')->get();

        $hadir     = $attendances->where('status', 'hadir')->count();
        $izin      = $attendances->where('status', 'izin')->count();
        $sakit     = $attendances->where('status', 'sakit')->count();
        $alpa      = $attendances->where('status', 'alpa')->count();
        $terlambat = $attendances->where('is_late', true)->count();

        $classGroups = AdminAttendanceExport::groupClasses($attendances);

        $dateStr     = $request->input('date', today()->toDateString());
        $dailyStatus = \App\Services\AcademicCalendarService::getDailyStatus($dateStr);

        $pdf = Pdf::loadView('Admin.Laporan.ExportPdf', [
            'dateLabel'   => $dateLabel,
            'classGroups' => $classGroups,
            'dailyStatus' => $dailyStatus,
            'hadir'       => $hadir,
            'izin'        => $izin,
            'sakit'       => $sakit,
            'alpa'        => $alpa,
            'terlambat'   => $terlambat,
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('Laporan_Absensi_' . now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Export Excel
     */
    public function exportExcel(Request $request)
    {
        [$query, $dateLabel] = $this->buildQuery($request);

        $attendances = $query->orderBy('student_id')->get();

        return Excel::download(
            new AdminAttendanceExport($attendances),
            'Laporan_Absensi_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    /**
     * Group attendances by class and student summaries.
     */
    public function groupClassAttendances($attendances)
    {
        return AdminAttendanceExport::groupClasses($attendances);
    }

    /**
     * Build Query
     */
    private function buildQuery(Request $request): array
    {
        $period = $request->input('period', 'day');

        $query = Attendance::with([
            'student',
            'student.schoolClass',
        ]);

        $dateLabel = '';

        switch ($period) {
            case 'month':
                $month = $request->input('month', now()->month);
                $year  = $request->input('year', now()->year);

                $query->whereMonth('attendance_date', $month)
                    ->whereYear('attendance_date', $year);

                $dateLabel = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
                break;

            case 'quarter':
                $quarter    = (int) $request->input('quarter', 1);
                $year       = $request->input('year', now()->year);
                $startMonth = 1 + (($quarter - 1) * 3);
                $endMonth   = $startMonth + 2;

                $start = Carbon::create($year, $startMonth, 1)->startOfMonth();
                $end   = Carbon::create($year, $endMonth, 1)->endOfMonth();

                $query->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()]);

                $dateLabel = "Triwulan {$quarter} {$year}";
                break;

            case 'semester':
                $semester   = (int) $request->input('semester', 1);
                $year       = $request->input('year', now()->year);
                $startMonth = $semester === 1 ? 7 : 1;
                $endMonth   = $semester === 1 ? 12 : 6;

                $start = Carbon::create($year, $startMonth, 1)->startOfMonth();
                $end   = Carbon::create($year, $endMonth, 1)->endOfMonth();

                $query->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()]);

                $dateLabel = "Semester {$semester} {$year}";
                break;

            case 'year':
                $year = $request->input('year', now()->year);

                $query->whereYear('attendance_date', $year);

                $dateLabel = (string) $year;
                break;

            case 'day':
            default:
                $date = $request->input('date', now()->toDateString());

                $query->whereDate('attendance_date', $date);

                $dateLabel = Carbon::parse($date)->translatedFormat('d F Y');
                break;
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        return [$query, $dateLabel];
    }
}
