<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\SchoolClass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class LateController extends Controller
{
    /**
     * Menampilkan daftar seluruh siswa terlambat untuk Guru Piket.
     */
    public function index(Request $request)
    {
        $dateStr    = $request->input('date', today()->toDateString());
        $targetDate = Carbon::parse($dateStr);

        $classList = SchoolClass::where('status', true)
            ->orderBy('name')
            ->get();

        $query = Attendance::with(['student.schoolClass', 'operator'])
            ->whereDate('attendance_date', $targetDate)
            ->where(function ($q) {
                $q->where('is_late', true)
                    ->orWhere(function ($sub) {
                        $sub->where('status', 'hadir')
                            ->whereTime('check_in', '>', '06:30:59');
                    });
            });

        if ($request->filled('class_id')) {
            $query->whereHas('student', fn ($q) => $q->where('class_id', $request->class_id));
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $allLateRecords = $query->get();

        $mapped = $allLateRecords->map(function ($att) {
            $totalLateCount = Attendance::where('student_id', $att->student_id)
                ->where(function ($q) {
                    $q->where('is_late', true)
                        ->orWhere(function ($sub) {
                            $sub->where('status', 'hadir')
                                ->whereTime('check_in', '>', '06:30:59');
                        });
                })
                ->count();

            return (object) [
                'id'               => $att->id,
                'student_id'       => $att->student_id,
                'student_name'     => optional($att->student)->name ?? '-',
                'student_nis'      => optional($att->student)->nis ?? '-',
                'class_name'       => optional(optional($att->student)->schoolClass)->name ?? '-',
                'jam_masuk'        => '06:30',
                'jam_datang'       => $att->late_time ?? $att->check_in ?? '-',
                'total_late_count' => $totalLateCount,
                'late_note'        => $att->late_note ?? $att->emergency_reason ?? $att->emergency_note ?? '-',
                'operator_name'    => optional($att->operator)->name ?? ($att->is_emergency ? 'Operator Lapangan' : 'System (Scan QR)'),
                'is_emergency'     => $att->is_emergency,
                'attendance_date'  => $att->attendance_date,
            ];
        });

        $sortField = $request->input('sort', 'name');
        $sortDir   = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        $mapped = $mapped->sortBy(fn ($item) => match ($sortField) {
            'jam_datang' => $item->jam_datang,
            'class'      => $item->class_name,
            default      => $item->student_name,
        }, SORT_REGULAR, $sortDir === 'desc')->values();

        $page    = LengthAwarePaginator::resolveCurrentPage() ?: 1;
        $perPage = 10;
        $items   = $mapped->slice(($page - 1) * $perPage, $perPage)->values();

        $lateAttendances = new LengthAwarePaginator(
            $items,
            $mapped->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $totalTerlambatHariIni = $mapped->count();

        return view('Piket.Terlambat.Index', compact(
            'lateAttendances',
            'classList',
            'dateStr',
            'totalTerlambatHariIni'
        ));
    }

    /**
     * Endpoint Detail Siswa Terlambat (READ-ONLY JSON)
     */
    public function show(string $id)
    {
        $attendance = Attendance::with(['student.schoolClass', 'operator'])
            ->where(function ($q) {
                $q->where('is_late', true)
                    ->orWhere(function ($sub) {
                        $sub->where('status', 'hadir')
                            ->whereTime('check_in', '>', '06:30:59');
                    });
            })
            ->findOrFail($id);

        $totalLateCount = Attendance::where('student_id', $attendance->student_id)
            ->where(function ($q) {
                $q->where('is_late', true)
                    ->orWhere(function ($sub) {
                        $sub->where('status', 'hadir')
                            ->whereTime('check_in', '>', '06:30:59');
                    });
            })
            ->count();

        $student   = $attendance->student;
        $className = optional($student->schoolClass)->name ?? '-';

        return response()->json([
            'success'          => true,
            'student_name'     => $student->name,
            'student_nis'      => $student->nis ?? '-',
            'class_name'       => $className,
            'attendance_date'  => $attendance->attendance_date->isoFormat('D MMMM YYYY'),
            'jam_masuk'        => '06:30 WIB',
            'jam_datang'       => ($attendance->late_time ?? $attendance->check_in ?? '-') . ' WIB',
            'total_late_count' => $totalLateCount . ' Kali',
            'late_note'        => $attendance->late_note ?? $attendance->emergency_reason ?? $attendance->emergency_note ?? 'Tidak ada alasan khusus.',
            'operator_name'    => optional($attendance->operator)->name ?? ($attendance->is_emergency ? 'Operator Lapangan Absensi' : 'System (Scan QR)'),
            'is_emergency'     => $attendance->is_emergency,
        ]);
    }
}
