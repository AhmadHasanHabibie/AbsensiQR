<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\AttendanceLock;
use App\Models\SchoolClass;
use App\Services\AttendanceTimeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use PDF;

class MonitoringController extends Controller
{
    /**
     * Menampilkan Halaman Monitoring Absensi Harian (Bisa Pilih Tanggal)
     */
    public function index(Request $request)
    {
        $dateStr    = $request->input('date', today()->toDateString());
        $targetDate = Carbon::parse($dateStr);
        $isToday    = $targetDate->isToday();
        $isScanOpen = $isToday ? AttendanceTimeService::isAttendanceOpen() : false;
        $dateLabel   = $targetDate->isoFormat('D MMMM YYYY');
        $dailyStatus = \App\Services\AcademicCalendarService::getDailyStatus($dateStr);

        $classList = SchoolClass::where('status', true)
            ->orderBy('name')
            ->get();

        $classesQuery = SchoolClass::where('status', true)
            ->with(['teacher', 'students' => fn ($q) => $q->where('role', 'student')->where('status', true)]);

        if ($request->filled('class_id')) {
            $classesQuery->where('id', $request->class_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $classesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('teacher', fn ($t) => $t->where('name', 'like', "%{$search}%"));
            });
        }

        $classes = $classesQuery->get();

        $locks = AttendanceLock::whereDate('attendance_date', $targetDate)
            ->get()
            ->keyBy('class_id');

        $attendances = Attendance::whereDate('attendance_date', $targetDate)
            ->get()
            ->groupBy('student_id');

        $mapped = $classes->map(function ($cls) use ($attendances, $locks, $isScanOpen) {
            $students   = $cls->students;
            $total      = $students->count();
            $studentIds = $students->pluck('id');

            $hadirCount = 0; $izinCount = 0; $sakitCount = 0;
            $alpaCount = 0; $belumCount = 0; $lateCount = 0;

            foreach ($studentIds as $sId) {
                $attList = $attendances->get($sId);
                $att     = $attList ? $attList->first() : null;

                if (! $att) {
                    $belumCount++;
                } else {
                    if ($att->status === 'hadir') {
                        $hadirCount++;
                        if ($att->is_late) {
                            $lateCount++;
                        }
                    } elseif ($att->status === 'izin') {
                        $izinCount++;
                    } elseif ($att->status === 'sakit') {
                        $sakitCount++;
                    } elseif ($att->status === 'alpa') {
                        $alpaCount++;
                    }
                }
            }

            $lock        = $locks->get($cls->id);
            $isConfirmed = ($lock && $lock->is_locked);

            if ($isConfirmed) {
                $statusKey   = 'sudah_konfirmasi';
                $statusLabel = 'Sudah Konfirmasi';
                $statusBadge = 'success';
            } elseif ($isScanOpen) {
                $statusKey   = 'menunggu_konfirmasi';
                $statusLabel = 'Sedang Berlangsung';
                $statusBadge = 'warning text-dark';
            } else {
                $statusKey   = 'menunggu_konfirmasi';
                $statusLabel = 'Menunggu Konfirmasi';
                $statusBadge = 'secondary';
            }

            $scanBadge  = $isScanOpen ? 'success' : 'secondary';
            $scanStatus = $isScanOpen ? 'SCAN DIBUKA' : 'SCAN DITUTUP';

            return (object) [
                'id'                => $cls->id,
                'name'              => $cls->name,
                'teacher_name'      => optional($cls->teacher)->name ?? '-',
                'students_count'    => $total,
                'status_key'        => $statusKey,
                'status_label'      => $statusLabel,
                'status_badge'      => $statusBadge,
                'locked_at'         => $lock ? $lock->locked_at : null,
                'scan_badge'        => $scanBadge,
                'scan_status'       => $scanStatus,
                'has_reminder_sent' => false,
                'is_confirmed'      => $isConfirmed,
                'total_siswa'       => $total,
                'hadir'             => $hadirCount,
                'izin'              => $izinCount,
                'sakit'             => $sakitCount,
                'alpa'              => $alpaCount,
                'belum_scan'        => $belumCount,
                'terlambat'         => $lateCount,
            ];
        });

        if ($request->filled('status')) {
            $mapped = $mapped->filter(fn ($item) => $item->status_key === $request->status);
        }

        $sortField = $request->input('sort', 'name');
        $sortDir   = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        $mapped = $mapped->sortBy(fn ($item) => match ($sortField) {
            'teacher_name' => $item->teacher_name,
            'status'       => $item->status_label,
            'locked_at'    => $item->locked_at ? $item->locked_at->timestamp : 0,
            default        => $item->name,
        }, SORT_REGULAR, $sortDir === 'desc')->values();

        $page    = LengthAwarePaginator::resolveCurrentPage() ?: 1;
        $perPage = 10;
        $items   = $mapped->slice(($page - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator(
            $items,
            $mapped->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $totalClasses     = $classList->count();
        $confirmedCount   = $mapped->where('is_confirmed', true)->count();
        $unconfirmedCount = max(0, $totalClasses - $confirmedCount);
        $percentage       = $totalClasses > 0 ? round(($confirmedCount / $totalClasses) * 100, 1) : 0;

        return view('Piket.Monitoring.Index', compact(
            'dateStr',
            'targetDate',
            'dateLabel',
            'isToday',
            'isScanOpen',
            'classList',
            'paginated',
            'totalClasses',
            'confirmedCount',
            'unconfirmedCount',
            'percentage',
            'dailyStatus'
        ));
    }

    /**
     * Detail Monitoring Kelas
     */
    public function show(string $id, Request $request)
    {
        $dateStr    = $request->input('date', today()->toDateString());
        $targetDate = Carbon::parse($dateStr);
        $dateLabel  = $targetDate->isoFormat('D MMMM YYYY');

        $schoolClass = SchoolClass::with(['teacher', 'students' => fn ($q) => $q->where('role', 'student')->where('status', true)->orderBy('name')])
            ->findOrFail($id);

        $lock = AttendanceLock::where('class_id', $schoolClass->id)
            ->whereDate('attendance_date', $targetDate)
            ->first();

        $isConfirmed = ($lock && $lock->is_locked);
        $isToday     = $targetDate->isToday();
        $isScanOpen  = $isToday ? AttendanceTimeService::isAttendanceOpen() : false;

        if ($isConfirmed) {
            $statusLabel = 'Sudah Konfirmasi';
            $statusBadge = 'success';
        } elseif ($isScanOpen) {
            $statusLabel = 'Sedang Berlangsung';
            $statusBadge = 'warning text-dark';
        } else {
            $statusLabel = 'Menunggu Konfirmasi';
            $statusBadge = 'secondary';
        }

        $attendances = Attendance::whereDate('attendance_date', $targetDate)
            ->whereIn('student_id', $schoolClass->students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        $hadirCount = 0; $terlambatCount = 0; $izinCount = 0;
        $sakitCount = 0; $alpaCount = 0; $belumHadir = 0;

        $studentList = $schoolClass->students->map(function ($std) use ($attendances, &$hadirCount, &$terlambatCount, &$izinCount, &$sakitCount, &$alpaCount, &$belumHadir) {
            $att = $attendances->get($std->id);
            $st  = $att ? $att->status : 'belum';
            $isL = $att ? $att->is_late : false;

            if ($st === 'hadir') {
                $hadirCount++;
                if ($isL) {
                    $terlambatCount++;
                }
            } elseif ($st === 'izin') {
                $izinCount++;
            } elseif ($st === 'sakit') {
                $sakitCount++;
            } elseif ($st === 'alpa') {
                $alpaCount++;
            } else {
                $belumHadir++;
            }

            $badgeClass = match ($st) {
                'hadir' => $isL ? 'warning text-dark' : 'success',
                'izin'  => 'warning text-dark',
                'sakit' => 'info',
                'alpa'  => 'danger',
                default => 'secondary',
            };

            $label = match ($st) {
                'hadir' => $isL ? 'Hadir Terlambat' : 'Hadir Tepat Waktu',
                'izin'  => 'Izin',
                'sakit' => 'Sakit',
                'alpa'  => 'Alpa',
                default => 'Belum Absen',
            };

            return [
                'student'        => $std,
                'nis'            => $std->nis ?? '-',
                'name'           => $std->name,
                'status'         => $st,
                'is_late'        => $isL,
                'check_in'       => $att && $att->check_in ? $att->check_in : '-',
                'status_label'   => $label,
                'badge_class'    => $badgeClass,
                'is_emergency'   => $att ? $att->is_emergency : false,
                'emergency_note' => $att ? $att->emergency_note : null,
                'late_note'      => $att ? $att->late_note : null,
            ];
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'        => true,
                'class_name'     => $schoolClass->name,
                'teacher_name'   => optional($schoolClass->teacher)->name ?? '-',
                'date_formatted' => $dateLabel,
                'status_label'   => $statusLabel,
                'status_badge'   => $statusBadge,
                'locked_at'      => $lock ? $lock->locked_at->isoFormat('HH:mm:ss, D MMM YYYY') . ' WIB' : 'Belum Konfirmasi',
                'stats'          => [
                    'hadir'       => $hadirCount,
                    'terlambat'   => $terlambatCount,
                    'izin'        => $izinCount,
                    'sakit'       => $sakitCount,
                    'alpa'        => $alpaCount,
                    'belum_hadir' => $belumHadir,
                ],
                'students'       => $studentList->values(),
            ]);
        }

        return view('Piket.Monitoring.Show', compact(
            'schoolClass',
            'targetDate',
            'dateLabel',
            'isConfirmed',
            'statusLabel',
            'statusBadge',
            'lock',
            'studentList'
        ));
    }

    /**
     * Mengirimkan Pengingat kepada Wali Kelas
     */
    public function sendReminder(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'date'     => 'nullable|date',
        ]);

        $dateStr     = $request->input('date', today()->toDateString());
        $targetDate  = Carbon::parse($dateStr)->startOfDay();
        $serverToday = Carbon::now('Asia/Jakarta')->startOfDay();

        if (! $targetDate->isSameDay($serverToday)) {
            return back()->with('error', 'Reminder hanya dapat dikirim pada tanggal absensi hari ini.');
        }

        if (! \App\Services\AcademicCalendarService::isSchoolDay($dateStr)) {
            $status = \App\Services\AcademicCalendarService::currentStatus($dateStr);
            return back()->with('error', "Reminder tidak dapat dikirim karena hari ini adalah {$status}.");
        }

        $schoolClass = SchoolClass::with('teacher')->findOrFail($request->class_id);

        ActivityLog::log(
            'Kirim Pengingat Wali Kelas',
            'Attendance',
            "Guru Piket mengirim pengingat konfirmasi absensi kepada Wali Kelas {$schoolClass->name} (" . optional($schoolClass->teacher)->name . ")."
        );

        return back()->with('success', "Pengingat berhasil dikirim kepada Wali Kelas {$schoolClass->name}.");
    }

    /**
     * Export PDF Laporan Monitoring Absensi Guru Piket (Harian)
     */
    public function exportPdf(Request $request)
    {
        $dateStr    = $request->input('date', today()->toDateString());
        $targetDate = Carbon::parse($dateStr);
        $dateLabel  = $targetDate->isoFormat('D MMMM YYYY');
        $printedAt  = now('Asia/Jakarta')->isoFormat('D MMMM YYYY, HH:mm:ss');
        $piketName  = Auth::user()->name ?? 'Guru Piket';

        $classes = SchoolClass::where('status', true)
            ->with('teacher')
            ->orderBy('name')
            ->get();

        $locks = AttendanceLock::whereDate('attendance_date', $targetDate)
            ->get()
            ->keyBy('class_id');

        $totalClasses = $classes->count();
        $confirmed    = 0;

        $items = $classes->map(function ($cls) use ($locks, &$confirmed) {
            $lock   = $locks->get($cls->id);
            $isConf = ($lock && $lock->is_locked);
            if ($isConf) {
                $confirmed++;
            }

            return (object) [
                'name'           => $cls->name,
                'teacher_name'   => optional($cls->teacher)->name ?? '-',
                'students_count' => $cls->students()->where('role', 'student')->where('status', true)->count(),
                'status_label'   => $isConf ? 'Sudah Konfirmasi' : 'Belum Konfirmasi',
            ];
        });

        $confirmedCount   = $confirmed;
        $unconfirmedCount = max(0, $totalClasses - $confirmedCount);
        $percentage       = $totalClasses > 0 ? round(($confirmedCount / $totalClasses) * 100, 1) : 0;
        $periodLabel      = "Harian ({$dateLabel})";

        $pdf = PDF::loadView('Piket.Monitoring.ExportPdf', compact(
            'periodLabel',
            'printedAt',
            'piketName',
            'totalClasses',
            'confirmedCount',
            'unconfirmedCount',
            'percentage',
            'items'
        ));

        return $pdf->download("Monitoring_Absensi_Sekolah_{$targetDate->format('Y-m-d')}.pdf");
    }

    /**
     * Export Excel/CSV Laporan Monitoring Absensi Guru Piket (Harian)
     */
    public function exportExcel(Request $request)
    {
        $dateStr    = $request->input('date', today()->toDateString());
        $targetDate = Carbon::parse($dateStr);

        $classes = SchoolClass::where('status', true)
            ->with('teacher')
            ->orderBy('name')
            ->get();

        $locks = AttendanceLock::whereDate('attendance_date', $targetDate)
            ->get()
            ->keyBy('class_id');

        $filename = "Monitoring_Absensi_Sekolah_{$targetDate->format('Y-m-d')}.csv";

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($classes, $locks) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Kelas', 'Guru Wali Kelas', 'Jumlah Siswa', 'Status Konfirmasi']);

            foreach ($classes as $index => $cls) {
                $lock   = $locks->get($cls->id);
                $isConf = ($lock && $lock->is_locked);
                fputcsv($file, [
                    $index + 1,
                    $cls->name,
                    optional($cls->teacher)->name ?? '-',
                    $cls->students()->where('role', 'student')->where('status', true)->count(),
                    $isConf ? 'Sudah Konfirmasi' : 'Belum Konfirmasi',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
