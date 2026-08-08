<?php

namespace App\Http\Controllers\Guru;

use App\Exports\GuruAttendanceExport;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Halaman laporan rekap absensi wali kelas.
     */
    public function index(Request $request)
    {
        $class = $this->homeroomClass();

        if (! $class) {
            return $this->classNotAssigned();
        }

        [$attendances, $classGroups, $statistics, $dateLabel] = $this->reportData($request, $class);
        $classGroup = $classGroups->first();

        return view('Guru.Laporan.Index', compact(
            'class',
            'attendances',
            'classGroup',
            'statistics',
            'dateLabel'
        ));
    }

    /**
     * Export laporan rekap guru ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $class = $this->homeroomClass();

        if (! $class) {
            return $this->classNotAssigned();
        }

        [, $classGroups, $statistics, $dateLabel] = $this->reportData($request, $class);

        $pdf = Pdf::loadView('Guru.Laporan.ExportPdf', [
            'dateLabel'   => $dateLabel,
            'classGroups' => $classGroups,
            'hadir'       => $statistics['hadir'],
            'izin'        => $statistics['izin'],
            'sakit'       => $statistics['sakit'],
            'alpa'        => $statistics['alpa'],
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('Laporan_Absensi_' . str_replace(' ', '_', $class->name) . '_' . now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Export laporan rekap guru ke Excel.
     */
    public function exportExcel(Request $request)
    {
        $class = $this->homeroomClass();

        if (! $class) {
            return $this->classNotAssigned();
        }

        [$attendances] = $this->reportData($request, $class);

        return Excel::download(
            new GuruAttendanceExport($attendances),
            'Laporan_Absensi_' . str_replace(' ', '_', $class->name) . '_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    /**
     * Mengambil kelas yang menjadi tanggung jawab guru login.
     */
    private function homeroomClass()
    {
        return Auth::user()->loadMissing('homeroomClass')->homeroomClass;
    }

    /**
     * Menyiapkan seluruh data laporan dari satu query yang dibatasi wali kelas.
     */
    private function reportData(Request $request, $class): array
    {
        [$query, $dateLabel] = $this->buildQuery($request, $class);

        $attendances = $query->orderBy('student_id')->get();
        $classGroups = GuruAttendanceExport::groupClasses($attendances);

        return [
            $attendances,
            $classGroups,
            [
                'hadir'     => $attendances->where('status', 'hadir')->count(),
                'terlambat' => $attendances->where('is_late', true)->where('status', 'hadir')->count(),
                'izin'      => $attendances->where('status', 'izin')->count(),
                'sakit'     => $attendances->where('status', 'sakit')->count(),
                'alpa'      => $attendances->where('status', 'alpa')->count(),
            ],
            $dateLabel,
        ];
    }

    /**
     * Query laporan guru dengan filter periode, status, dan pencarian Admin.
     */
    private function buildQuery(Request $request, $class): array
    {
        $period = $request->input('period', 'day');

        $query = Attendance::with([
            'student',
            'student.schoolClass',
        ])->whereHas('student', fn ($q) => $q->where('class_id', $class->id));

        switch ($period) {
            case 'month':
                $month = $request->input('month', now()->month);
                $year  = $request->input('year', now()->year);
                $query->whereMonth('attendance_date', $month)->whereYear('attendance_date', $year);
                $dateLabel = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
                break;

            case 'quarter':
                $quarter    = (int) $request->input('quarter', 1);
                $year       = $request->input('year', now()->year);
                $startMonth = 1 + (($quarter - 1) * 3);
                $query->whereBetween('attendance_date', [
                    Carbon::create($year, $startMonth, 1)->startOfMonth()->toDateString(),
                    Carbon::create($year, $startMonth + 2, 1)->endOfMonth()->toDateString(),
                ]);
                $dateLabel = "Triwulan {$quarter} {$year}";
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

            $query->whereHas('student', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        return [$query, $dateLabel];
    }

    /**
     * Respons saat guru belum menjadi wali kelas.
     */
    private function classNotAssigned()
    {
        return back()->with(
            'error',
            'Anda belum ditetapkan sebagai wali kelas.'
        );
    }
}
