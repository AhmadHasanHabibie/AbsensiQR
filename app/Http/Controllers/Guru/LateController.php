<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LateController extends Controller
{
    /**
     * Menampilkan daftar siswa yang ditandai terlambat hari ini.
     */
    public function index()
    {
        $class = $this->homeroomClass();

        if (! $class) {
            return $this->classNotAssigned();
        }

        $lateAttendances = Attendance::with('student.schoolClass')
            ->whereDate('attendance_date', today())
            ->where('is_late', true)
            ->whereHas('student', function ($query) use ($class) {
                $query->where('class_id', $class->id);
            })
            ->orderBy('late_time')
            ->get();

        return view('Guru.Terlambat.Index', compact(
            'class',
            'lateAttendances'
        ));
    }

    /**
     * Form penandaan siswa terlambat.
     */
    public function create()
    {
        $class = $this->homeroomClass();

        if (! $class) {
            return $this->classNotAssigned();
        }

        $students = Attendance::with('student')
            ->whereDate('attendance_date', today())
            ->where('status', 'alpa')
            ->where(function ($query) {
                $query->where('is_late', false)
                    ->orWhereNull('is_late');
            })
            ->whereHas('student', function ($query) use ($class) {
                $query->where('class_id', $class->id)
                    ->where('role', 'student')
                    ->where('status', true);
            })
            ->orderBy('student_id')
            ->get()
            ->pluck('student')
            ->filter()
            ->unique('id')
            ->values();

        return view('Guru.Terlambat.Create', compact(
            'class',
            'students'
        ));
    }

    /**
     * Memperbarui absensi alpa menjadi hadir terlambat.
     */
    public function store(Request $request)
    {
        $class = $this->homeroomClass();

        if (! $class) {
            return $this->classNotAssigned();
        }

        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:users,id'],
            'late_time' => ['required', 'date_format:H:i'],
            'late_note' => ['nullable', 'string'],
        ]);

        $attendance = Attendance::whereDate('attendance_date', today())
            ->where('student_id', $validated['student_id'])
            ->where('status', 'alpa')
            ->where(function ($query) {
                $query->where('is_late', false)
                    ->orWhereNull('is_late');
            })
            ->whereHas('student', function ($query) use ($class) {
                $query->where('class_id', $class->id)
                    ->where('role', 'student')
                    ->where('status', true);
            })
            ->firstOrFail();

        $attendance->status = 'hadir';
        $attendance->is_late = true;
        $attendance->late_time = $validated['late_time'];
        $attendance->late_note = $validated['late_note'];
        $attendance->save();

        return redirect()
            ->route('guru.terlambat.index')
            ->with('success', 'Siswa berhasil ditandai hadir terlambat.');
    }

    /**
     * Mengambil kelas wali dari guru yang sedang login.
     */
    private function homeroomClass()
    {
        return Auth::user()->loadMissing('homeroomClass')->homeroomClass;
    }

    /**
     * Respons saat guru belum ditetapkan sebagai wali kelas.
     */
    private function classNotAssigned()
    {
        return back()->with(
            'error',
            'Anda belum ditetapkan sebagai wali kelas.'
        );
    }
}
