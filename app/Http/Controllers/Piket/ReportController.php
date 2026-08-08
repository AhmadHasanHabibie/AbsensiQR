<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLock;
use App\Models\SchoolClass;
use App\Services\AttendanceTimeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Menampilkan Halaman Laporan Absensi Harian Guru Piket
     */
    public function index(Request $request)
    {
        $dateStr    = $request->input('date', today()->toDateString());
        $targetDate = Carbon::parse($dateStr);
        $dateLabel  = $targetDate->isoFormat('D MMMM YYYY');

        $classList = SchoolClass::where('status', true)
            ->orderBy('name')
            ->get();

        $classesQuery = SchoolClass::where('status', true)
            ->with(['teacher', 'students' => fn ($q) => $q->where('role', 'student')->where('status', true)]);

        if ($request->filled('class_id')) {
            $classesQuery->where('id', $request->class_id);
        }

        $classes = $classesQuery->orderBy('name')->get();

        $attendances = Attendance::whereDate('attendance_date', $targetDate)
            ->get()
            ->groupBy('student_id');

        $locks = AttendanceLock::whereDate('attendance_date', $targetDate)
            ->get()
            ->keyBy('class_id');

        $isScanOpen = $targetDate->isToday() ? AttendanceTimeService::isAttendanceOpen() : false;

        $classReports = $classes->map(function ($cls) use ($attendances, $locks, $isScanOpen) {
            $lock        = $locks->get($cls->id);
            $isConfirmed = ($lock && $lock->is_locked);

            $studentsInClass = $cls->students->pluck('id');
            $hadirCount = 0; $izinCount = 0; $sakitCount = 0;
            $alpaCount = 0; $belumCount = 0; $lateCount = 0;

            foreach ($studentsInClass as $sId) {
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

            if ($isConfirmed) {
                $statusLabel = 'Sudah Konfirmasi';
                $statusBadge = 'success';
            } elseif ($isScanOpen) {
                $statusLabel = 'Sedang Berlangsung';
                $statusBadge = 'warning text-dark';
            } else {
                $statusLabel = 'Belum Konfirmasi';
                $statusBadge = 'secondary';
            }

            return (object) [
                'class_id'       => $cls->id,
                'class_name'     => $cls->name,
                'teacher_name'   => optional($cls->teacher)->name ?? '-',
                'total_students' => $cls->students->count(),
                'hadir'          => $hadirCount,
                'izin'           => $izinCount,
                'sakit'          => $sakitCount,
                'alpa'           => $alpaCount,
                'belum'          => $belumCount,
                'terlambat'      => $lateCount,
                'is_confirmed'   => $isConfirmed,
                'status_label'   => $statusLabel,
                'status_badge'   => $statusBadge,
                'confirmed_at'   => $lock ? $lock->locked_at : null,
            ];
        });

        $hadirCount        = $classReports->sum('hadir');
        $terlambatCount    = $classReports->sum('terlambat');
        $izinCount         = $classReports->sum('izin');
        $sakitCount        = $classReports->sum('sakit');
        $alpaCount         = $classReports->sum('alpa');
        $totalStudents     = $classReports->sum('total_students');
        $overallPercentage = $totalStudents > 0 ? round(($hadirCount / $totalStudents) * 100, 1) : 0;

        return view('Piket.Laporan.Index', compact(
            'dateStr',
            'targetDate',
            'dateLabel',
            'classList',
            'classReports',
            'hadirCount',
            'terlambatCount',
            'izinCount',
            'sakitCount',
            'alpaCount',
            'overallPercentage'
        ));
    }

    /**
     * Detail Laporan per Kelas
     */
    public function show(string $id, Request $request)
    {
        $dateStr     = $request->input('date', today()->toDateString());
        $targetDate  = Carbon::parse($dateStr);
        $schoolClass = SchoolClass::with(['teacher', 'students' => fn ($q) => $q->where('role', 'student')->where('status', true)->orderBy('name')])
            ->findOrFail($id);

        $lock = AttendanceLock::where('class_id', $schoolClass->id)
            ->whereDate('attendance_date', $targetDate)
            ->first();

        $isConfirmed = ($lock && $lock->is_locked);

        $attendances = Attendance::whereDate('attendance_date', $targetDate)
            ->whereIn('student_id', $schoolClass->students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        $reports = $schoolClass->students->map(function ($std) use ($attendances) {
            $att = $attendances->get($std->id);

            $totalLate = Attendance::where('student_id', $std->id)
                ->where('is_late', true)
                ->count();

            $st  = $att ? $att->status : 'alpa';
            $isL = $att ? $att->is_late : false;

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
                'student'      => $std,
                'name'         => $std->name,
                'nis'          => $std->nis ?? '-',
                'status'       => $st,
                'is_late'      => $isL,
                'jam_masuk'    => '06:30',
                'jam_datang'   => $att && $att->check_in ? $att->check_in : '-',
                'total_late'   => $totalLate,
                'status_label' => $label,
                'badge_class'  => $badgeClass,
                'note'         => $att ? ($att->late_note ?? $att->emergency_note) : '-',
            ];
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'class_name'   => $schoolClass->name,
                'teacher_name' => optional($schoolClass->teacher)->name ?? '-',
                'date'         => $targetDate->isoFormat('D MMMM YYYY'),
                'students'     => $reports->values(),
            ]);
        }

        return view('Piket.Laporan.Show', compact(
            'schoolClass',
            'targetDate',
            'isConfirmed',
            'lock',
            'reports'
        ));
    }

    /**
     * Export PDF Laporan Absensi Harian
     */
    public function exportPdf(Request $request)
    {
        $dateStr    = $request->input('date', today()->toDateString());
        $targetDate = Carbon::parse($dateStr);
        $dateLabel  = $targetDate->isoFormat('D MMMM YYYY');
        $printedAt  = now('Asia/Jakarta')->isoFormat('D MMMM YYYY, HH:mm:ss');
        $piketName  = Auth::user()->name ?? 'Guru Piket';

        $classes = SchoolClass::where('status', true)
            ->with(['teacher', 'students' => fn ($q) => $q->where('role', 'student')->where('status', true)->orderBy('name')])
            ->orderBy('name')
            ->get();

        $attendances = Attendance::whereDate('attendance_date', $targetDate)
            ->get()
            ->groupBy('student_id');

        $hadirTotal = 0; $terlambatTotal = 0; $izinTotal = 0; $sakitTotal = 0; $alpaTotal = 0;

        $classGroups = $classes->map(function ($cls) use ($attendances, &$hadirTotal, &$terlambatTotal, &$izinTotal, &$sakitTotal, &$alpaTotal) {
            $hadir = 0; $terlambat = 0; $izin = 0; $sakit = 0; $alpa = 0;

            $studentData = $cls->students->map(function ($std) use ($attendances, &$hadir, &$terlambat, &$izin, &$sakit, &$alpa) {
                $attList = $attendances->get($std->id);
                $att     = $attList ? $attList->first() : null;

                $h = 0; $t = 0; $i = 0; $s = 0; $a = 0;
                if ($att) {
                    if ($att->status === 'hadir') {
                        $h = 1; $hadir++;
                        if ($att->is_late) {
                            $t = 1; $terlambat++;
                        }
                    } elseif ($att->status === 'izin') {
                        $i = 1; $izin++;
                    } elseif ($att->status === 'sakit') {
                        $s = 1; $sakit++;
                    } elseif ($att->status === 'alpa') {
                        $a = 1; $alpa++;
                    }
                } else {
                    $a = 1; $alpa++;
                }

                return [
                    'name'       => $std->name,
                    'nis'        => $std->nis ?? '-',
                    'hadir'      => $h,
                    'terlambat'  => $t,
                    'izin'       => $i,
                    'sakit'      => $s,
                    'alpa'       => $a,
                    'percentage' => $h === 1 ? 100 : 0,
                ];
            });

            $hadirTotal     += $hadir;
            $terlambatTotal += $terlambat;
            $izinTotal      += $izin;
            $sakitTotal     += $sakit;
            $alpaTotal      += $alpa;

            $totalInClass    = $cls->students->count();
            $classPercentage = $totalInClass > 0 ? round(($hadir / $totalInClass) * 100, 1) : 0;

            return [
                'class_name'   => $cls->name,
                'teacher_name' => optional($cls->teacher)->name ?? '-',
                'students'     => $studentData,
                'totals'       => [
                    'hadir'     => $hadir,
                    'terlambat' => $terlambat,
                    'izin'      => $izin,
                    'sakit'     => $sakit,
                    'alpa'      => $alpa,
                ],
                'percentage'   => $classPercentage,
            ];
        });

        $hadir     = $hadirTotal;
        $terlambat = $terlambatTotal;
        $izin      = $izinTotal;
        $sakit     = $sakitTotal;
        $alpa      = $alpaTotal;

        $pdf = \PDF::loadView('Piket.Laporan.ExportPdf', compact(
            'dateLabel',
            'printedAt',
            'piketName',
            'hadir',
            'terlambat',
            'izin',
            'sakit',
            'alpa',
            'classGroups'
        ));

        return $pdf->download("Laporan_Absensi_Sekolah_{$targetDate->format('Y-m-d')}.pdf");
    }

    /**
     * Export Excel Laporan Absensi Harian
     */
    public function exportExcel(Request $request)
    {
        $dateStr    = $request->input('date', today()->toDateString());
        $targetDate = Carbon::parse($dateStr);

        $query = Attendance::with(['student.schoolClass'])
            ->whereDate('attendance_date', $targetDate);

        $records = $query->get();

        $filename = "Laporan_Absensi_Siswa_{$targetDate->format('Y-m-d')}.csv";

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'NIS', 'Nama Siswa', 'Kelas', 'Status', 'Jam Check-in', 'Terlambat', 'Catatan']);

            foreach ($records as $index => $row) {
                fputcsv($file, [
                    $index + 1,
                    optional($row->student)->nis ?? '-',
                    optional($row->student)->name ?? '-',
                    optional(optional($row->student)->schoolClass)->name ?? '-',
                    strtoupper($row->status),
                    $row->check_in ?? '-',
                    $row->is_late ? 'YA' : 'TIDAK',
                    $row->late_note ?? $row->emergency_note ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
