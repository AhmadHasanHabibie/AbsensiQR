<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\ImportAcademicCalendarRequest;
use App\Imports\AcademicCalendarImport;
use App\Exports\AcademicCalendarTemplateExport;
use App\Models\AcademicCalendar;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\AcademicCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class AcademicCalendarController extends Controller
{
    public function __construct(
        protected AcademicCalendarService $calendarService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX — Halaman Utama Kalender Akademik
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $user = Auth::user();

        // -------------------------------------------------------
        // Audit Log
        // -------------------------------------------------------
        ActivityLog::log(
            'Melihat Kalender Akademik',
            'Kalender Akademik',
            "Pengguna {$user->name} membuka halaman Kalender Akademik.",
            $user
        );

        // -------------------------------------------------------
        // Filter Parameters
        // -------------------------------------------------------
        $search       = $request->input('search');
        $semester     = $request->input('semester');
        $status       = $request->input('status');
        $category     = $request->input('category');
        $academicYear = $request->input('academic_year');
        $dateFrom     = $request->input('date_from');
        $dateTo       = $request->input('date_to');

        // -------------------------------------------------------
        // Query Builder
        // -------------------------------------------------------
        $query = AcademicCalendar::query()->orderBy('date', 'asc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('activity', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('academic_year', 'like', "%{$search}%");
            });
        }

        if ($semester) {
            $query->where('semester', $semester);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($academicYear) {
            $query->where('academic_year', $academicYear);
        }

        if ($dateFrom) {
            $query->where('date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('date', '<=', $dateTo);
        }

        $calendars    = $query->paginate(30)->withQueryString();
        $stats        = $this->calendarService->getStatsByYear($academicYear);
        $yearSummary  = $this->calendarService->getYearSummary();
        $activeYear   = AcademicCalendar::activeYear();

        $isSuperAdmin = ($user->role === User::ROLE_SUPER_ADMIN);
        $layout       = match ($user->role) {
            User::ROLE_ADMIN               => 'Layouts.LayoutAdmin',
            User::ROLE_TEACHER             => 'Layouts.LayoutGuru',
            User::ROLE_PIKET, 'guru_piket' => 'Layouts.LayoutGuruPiket',
            default                        => 'Layouts.LayoutSuperAdmin',
        };

        return view('SuperAdmin.AcademicCalendar.Index', compact(
            'calendars',
            'stats',
            'yearSummary',
            'activeYear',
            'search',
            'semester',
            'status',
            'category',
            'academicYear',
            'dateFrom',
            'dateTo',
            'isSuperAdmin',
            'layout'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW — Detail satu record (AJAX/Modal)
    |--------------------------------------------------------------------------
    */

    public function show(int $id)
    {
        $user     = Auth::user();
        $calendar = AcademicCalendar::findOrFail($id);

        // Audit Log
        ActivityLog::log(
            'Melihat Detail Kalender',
            'Kalender Akademik',
            "Pengguna {$user->name} melihat detail kalender tanggal {$calendar->date->format('d/m/Y')} ({$calendar->academic_year}).",
            $user
        );

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => [
                    'id'                  => $calendar->id,
                    'academic_year'       => $calendar->academic_year,
                    'date'                => $calendar->date->format('d F Y'),
                    'date_raw'            => $calendar->date->toDateString(),
                    'day_name'            => $calendar->day_name,
                    'month'               => $calendar->month,
                    'semester'            => $calendar->semester,
                    'status'              => $calendar->status,
                    'status_badge'        => $calendar->status_badge_class,
                    'category'            => $calendar->category,
                    'category_badge'      => $calendar->category_badge_class,
                    'activity'            => $calendar->activity,
                    'qr_status'           => $calendar->qr_status,
                    'teacher_attendance'  => $calendar->teacher_attendance,
                    'student_attendance'  => $calendar->student_attendance,
                    'operator_attendance' => $calendar->operator_attendance,
                    'description'         => $calendar->description,
                    'is_active'           => $calendar->is_active,
                    'created_at'          => $calendar->created_at?->format('d/m/Y H:i'),
                    'updated_at'          => $calendar->updated_at?->format('d/m/Y H:i'),
                ],
            ]);
        }

        abort(404);
    }

    /*
    |--------------------------------------------------------------------------
    | IMPORT — Proses Upload Excel
    |--------------------------------------------------------------------------
    */

    public function import(ImportAcademicCalendarRequest $request)
    {
        $user = Auth::user();

        try {
            $importer = new AcademicCalendarImport();
            Excel::import($importer, $request->file('file'));

            // -------------------------------------------------------
            // Jika Ada Error
            // -------------------------------------------------------
            if (! empty($importer->failed)) {
                ActivityLog::log(
                    'Import Kalender Gagal',
                    'Kalender Akademik',
                    "Pengguna {$user->name} gagal import kalender. " . count($importer->failed) . " error ditemukan.",
                    $user
                );

                return back()->with('import_errors', $importer->failed);
            }

            // -------------------------------------------------------
            // Berhasil
            // -------------------------------------------------------
            ActivityLog::log(
                'Import Kalender Berhasil',
                'Kalender Akademik',
                "Pengguna {$user->name} berhasil import {$importer->success} data kalender untuk tahun ajaran {$importer->detectedYear}.",
                $user
            );

            return back()->with([
                'import_success'      => true,
                'import_count'        => $importer->success,
                'import_year'         => $importer->detectedYear,
            ]);

        } catch (\Throwable $e) {
            ActivityLog::log(
                'Import Kalender Error',
                'Kalender Akademik',
                "Pengguna {$user->name} mengalami error sistem saat import: " . $e->getMessage(),
                $user
            );

            return back()->with('error', 'Terjadi kesalahan sistem saat memproses file: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD TEMPLATE
    |--------------------------------------------------------------------------
    */

    public function downloadTemplate()
    {
        $user = Auth::user();

        ActivityLog::log(
            'Download Template Kalender',
            'Kalender Akademik',
            "Pengguna {$user->name} mengunduh template Excel Kalender Akademik.",
            $user
        );

        return Excel::download(
            new AcademicCalendarTemplateExport(),
            'template_kalender_akademik.xlsx'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIVATE YEAR — Jadikan Tahun Aktif
    |--------------------------------------------------------------------------
    */

    public function activateYear(Request $request)
    {
        $request->validate([
            'academic_year' => ['required', 'string', 'max:20'],
        ]);

        $user        = Auth::user();
        $year        = $request->input('academic_year');
        $previousYear = AcademicCalendar::activeYear();

        try {
            $this->calendarService->activateYear($year);

            ActivityLog::log(
                'Aktivasi Tahun Ajaran',
                'Kalender Akademik',
                "Pengguna {$user->name} mengaktifkan tahun ajaran {$year}" .
                ($previousYear ? " (sebelumnya: {$previousYear})" : '') . ".",
                $user
            );

            return back()->with('success', "Tahun ajaran {$year} berhasil dijadikan tahun aktif.");

        } catch (\Exception $e) {
            ActivityLog::log(
                'Aktivasi Tahun Ajaran Gagal',
                'Kalender Akademik',
                "Pengguna {$user->name} gagal mengaktifkan tahun ajaran {$year}: " . $e->getMessage(),
                $user
            );

            return back()->with('error', $e->getMessage());
        }
    }
}
