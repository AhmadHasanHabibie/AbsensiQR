<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EmergencyAuditExport;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\EmergencyAttendanceAudit;
use App\Models\SchoolClass;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class EmergencyAuditController extends Controller
{
    /**
     * Halaman Audit Absensi Darurat untuk Admin.
     */
    public function index(Request $request)
    {
        ActivityLog::log(
            'Akses Audit Absensi Darurat',
            'Audit',
            'Admin ' . Auth::user()->name . ' membuka Halaman Audit Center Absensi Darurat.'
        );

        $query  = $this->buildQuery($request);
        $audits = $query->paginate(10)->withQueryString();

        $todayAudits  = EmergencyAttendanceAudit::whereDate('input_at', today())->get();
        $totalHariIni = $todayAudits->count();
        $total7Hari   = EmergencyAttendanceAudit::where('input_at', '>=', now()->subDays(7))->count();
        $total30Hari  = EmergencyAttendanceAudit::where('input_at', '>=', now()->subDays(30))->count();

        $disetujuiCount = $todayAudits->filter(fn ($a) => strtolower($a->final_status) === 'hadir')->count();
        $diubahCount    = $todayAudits->filter(fn ($a) => in_array(strtolower($a->final_status), ['izin', 'sakit', 'terlambat']))->count();
        $ditolakCount   = $todayAudits->filter(fn ($a) => strtolower($a->final_status) === 'alpa')->count();

        $totalOperators = EmergencyAttendanceAudit::distinct('operator_id')->count('operator_id');
        $totalTeachers  = EmergencyAttendanceAudit::whereNotNull('teacher_id')->distinct('teacher_id')->count('teacher_id');

        $classes   = SchoolClass::where('status', true)->orderBy('name')->get();
        $operators = User::where('role', 'operator')->where('status', true)->orderBy('name')->get();
        $teachers  = User::where('role', 'teacher')->where('status', true)->orderBy('name')->get();

        return view('Admin.EmergencyAudit.Index', compact(
            'audits',
            'totalHariIni',
            'total7Hari',
            'total30Hari',
            'disetujuiCount',
            'diubahCount',
            'ditolakCount',
            'totalOperators',
            'totalTeachers',
            'classes',
            'operators',
            'teachers'
        ));
    }

    /**
     * Export PDF Audit Absensi Darurat.
     */
    public function exportPdf(Request $request)
    {
        ActivityLog::log(
            'Export Audit Absensi Darurat PDF',
            'Audit',
            'Admin ' . Auth::user()->name . ' mengunduh laporan PDF Audit Absensi Darurat.'
        );

        $query  = $this->buildQuery($request);
        $audits = $query->get();

        $pdf = Pdf::loadView('Admin.EmergencyAudit.ExportPdf', [
            'audits'    => $audits,
            'printedAt' => now()->isoFormat('D MMMM YYYY, HH:mm:ss'),
            'adminName' => Auth::user()->name,
        ]);

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('Audit_Absensi_Darurat_' . now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Export Excel Audit Absensi Darurat.
     */
    public function exportExcel(Request $request)
    {
        ActivityLog::log(
            'Export Audit Absensi Darurat Excel',
            'Audit',
            'Admin ' . Auth::user()->name . ' mengunduh laporan Excel Audit Absensi Darurat.'
        );

        $query  = $this->buildQuery($request);
        $audits = $query->get();

        return Excel::download(
            new EmergencyAuditExport($audits),
            'Audit_Absensi_Darurat_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    /**
     * Export CSV Audit Absensi Darurat.
     */
    public function exportCsv(Request $request)
    {
        ActivityLog::log(
            'Export Audit Absensi Darurat CSV',
            'Audit',
            'Admin ' . Auth::user()->name . ' mengunduh laporan CSV Audit Absensi Darurat.'
        );

        $query  = $this->buildQuery($request);
        $audits = $query->get();

        return Excel::download(
            new EmergencyAuditExport($audits),
            'Audit_Absensi_Darurat_' . now()->format('Ymd_His') . '.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    /**
     * Build Query Filter & Search
     */
    private function buildQuery(Request $request)
    {
        $query = EmergencyAttendanceAudit::with([
            'student.schoolClass',
            'schoolClass',
            'operator',
            'teacher',
            'attendance',
        ])->orderBy('input_at', 'desc');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('input_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay(),
            ]);
        } elseif ($request->filled('date')) {
            $query->whereDate('input_at', $request->date);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('operator_id')) {
            $query->where('operator_id', $request->operator_id);
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->filled('initial_status')) {
            $query->where('initial_status', $request->initial_status);
        }

        if ($request->filled('final_status')) {
            $query->where('final_status', $request->final_status);
        }

        if ($request->filled('validation_status')) {
            $valStatus = strtolower($request->validation_status);
            if ($valStatus === 'disetujui') {
                $query->whereRaw('LOWER(final_status) = ?', ['hadir']);
            } elseif ($valStatus === 'diubah') {
                $query->whereIn(DB::raw('LOWER(final_status)'), ['izin', 'sakit', 'terlambat']);
            } elseif ($valStatus === 'ditolak') {
                $query->whereRaw('LOWER(final_status) = ?', ['alpa']);
            } elseif ($valStatus === 'menunggu') {
                $query->whereNull('final_status');
            }
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($s) use ($search) {
                    $s->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                })
                    ->orWhereHas('operator', fn ($o) => $o->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('teacher', fn ($t) => $t->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('schoolClass', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        return $query;
    }
}
