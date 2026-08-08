<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\StudentMailbox;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MailboxController extends Controller
{
    /**
     * Halaman form pembuatan Surat Mailbox (Pemanggilan / Pembinaan / Klarifikasi).
     */
    public function create(Request $request, $studentId)
    {
        $teacher = Auth::user();
        $class   = $teacher->homeroomClass;

        if (! $class) {
            return back()->with('error', 'Anda belum ditetapkan sebagai wali kelas.');
        }

        $student = User::where('id', $studentId)
            ->where('class_id', $class->id)
            ->where('role', 'student')
            ->firstOrFail();

        $mailType = $request->input('mail_type', 'alpha');
        if (! in_array($mailType, ['alpha', 'late', 'permission'])) {
            $mailType = 'alpha';
        }

        $weekStartInput = $request->input('week_start', now()->startOfWeek(Carbon::MONDAY)->toDateString());
        $weekStart      = Carbon::parse($weekStartInput)->startOfWeek(Carbon::MONDAY);
        $weekEnd        = Carbon::parse($request->input('week_end', $weekStart->clone()->addDays(5)->toDateString()));

        $existing = StudentMailbox::where('student_id', $student->id)
            ->where('mail_type', $mailType)
            ->where('week_start', $weekStart->toDateString())
            ->where('week_end', $weekEnd->toDateString())
            ->exists();

        if ($existing) {
            return redirect()->route('guru.dashboard')->with('error', 'Surat untuk jenis tersebut pada minggu ini sudah pernah dikirim.');
        }

        $categoryTotal = match ($mailType) {
            'late' => Attendance::where('student_id', $student->id)
                ->where(function ($q) {
                    $q->where('is_late', true)->orWhere(fn ($q2) => $q2->where('status', 'hadir')->where('is_late', true));
                })
                ->whereBetween('attendance_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->count(),

            'permission' => Attendance::where('student_id', $student->id)
                ->where('status', 'izin')
                ->whereBetween('attendance_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->count(),

            default => Attendance::where('student_id', $student->id)
                ->where('status', 'alpa')
                ->whereBetween('attendance_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->count(),
        };

        $defaultTitle = match ($mailType) {
            'late'       => 'Surat Pembinaan Keterlambatan Siswa',
            'permission' => 'Surat Klarifikasi Izin Siswa',
            default      => 'Surat Pemanggilan Orang Tua / Wali Siswa',
        };

        $defaultDesc = match ($mailType) {
            'late'       => 'Berdasarkan rekapitulasi kehadiran sekolah, siswa bersangkutan tercatat sering datang terlambat dalam kegiatan belajar mengajar minggu ini. Sehubungan dengan hal tersebut, kami mengundang Orang Tua / Wali murid untuk hadir pembinaan dan bimbingan kedisiplinan.',
            'permission' => 'Berdasarkan rekapitulasi absensi mingguan, siswa bersangkutan mengajukan izin sebanyak beberapa kali dalam minggu ini. Kami mengundang Bapak/Ibu Orang Tua / Wali murid untuk melakukan klarifikasi dan permohonan informasi terkait kondisi siswa.',
            default      => 'Mengingat pentingnya koordinasi antara pihak sekolah dan Orang Tua / Wali murid mengenai tingkat kehadiran serta keikutsertaan dalam kegiatan pembelajaran, kami mengundang Bapak/Ibu untuk hadir berkonsultasi langsung mengenai kedisiplinan putra/putri Bapak/Ibu.',
        };

        $countLabel = match ($mailType) {
            'late'       => 'Total Terlambat Minggu Ini',
            'permission' => 'Total Izin Minggu Ini',
            default      => 'Total Alfa Minggu Ini',
        };

        return view('Guru.Mailbox.Create', compact(
            'student',
            'teacher',
            'class',
            'weekStart',
            'weekEnd',
            'mailType',
            'categoryTotal',
            'defaultTitle',
            'defaultDesc',
            'countLabel'
        ));
    }

    /**
     * Proses pembuatan PDF dan penyimpanan data Mailbox.
     */
    public function store(Request $request)
    {
        $teacher = Auth::user();
        $class   = $teacher->homeroomClass;

        if (! $class) {
            return back()->with('error', 'Anda belum ditetapkan sebagai wali kelas.');
        }

        $request->validate([
            'student_id'       => 'required|exists:users,id',
            'mail_type'        => 'required|in:alpha,late,permission',
            'week_start'       => 'required|date',
            'week_end'         => 'required|date',
            'title'            => 'required|string|max:255',
            'description'      => 'required|string',
            'meeting_date'     => 'required|date',
            'meeting_time'     => 'required|string',
            'meeting_location' => 'required|string',
            'notes'            => 'nullable|string',
        ]);

        $student = User::where('id', $request->student_id)
            ->where('class_id', $class->id)
            ->where('role', 'student')
            ->firstOrFail();

        $mailType  = $request->mail_type;
        $weekStart = Carbon::parse($request->week_start)->toDateString();
        $weekEnd   = Carbon::parse($request->week_end)->toDateString();

        $existing = StudentMailbox::where('student_id', $student->id)
            ->where('mail_type', $mailType)
            ->where('week_start', $weekStart)
            ->where('week_end', $weekEnd)
            ->exists();

        if ($existing) {
            return redirect()->route('guru.dashboard')->with('error', 'Surat jenis tersebut untuk siswa ini pada minggu ini sudah pernah dikirim.');
        }

        $categoryTotal = match ($mailType) {
            'late' => Attendance::where('student_id', $student->id)
                ->where(function ($q) {
                    $q->where('is_late', true)->orWhere(fn ($q2) => $q2->where('status', 'hadir')->where('is_late', true));
                })
                ->whereBetween('attendance_date', [$weekStart, $weekEnd])
                ->count(),

            'permission' => Attendance::where('student_id', $student->id)
                ->where('status', 'izin')
                ->whereBetween('attendance_date', [$weekStart, $weekEnd])
                ->count(),

            default => Attendance::where('student_id', $student->id)
                ->where('status', 'alpa')
                ->whereBetween('attendance_date', [$weekStart, $weekEnd])
                ->count(),
        };

        $prefixCode = match ($mailType) {
            'late'       => 'SKT',
            'permission' => 'SKI',
            default      => 'SP',
        };

        $letterNumber = '00' . rand(10, 99) . '/' . $prefixCode . '/SMKN17/' . Carbon::now()->format('m/Y');
        $createdDate  = Carbon::now();

        $pdfData = [
            'letterNumber'    => $letterNumber,
            'createdDate'     => $createdDate,
            'student'         => $student,
            'class'           => $class,
            'teacher'         => $teacher,
            'weekStart'       => Carbon::parse($weekStart),
            'weekEnd'         => Carbon::parse($weekEnd),
            'mailType'        => $mailType,
            'categoryTotal'   => $categoryTotal,
            'title'           => $request->title,
            'description'     => $request->description,
            'meetingDate'     => Carbon::parse($request->meeting_date),
            'meetingTime'     => $request->meeting_time,
            'meetingLocation' => $request->meeting_location,
            'notes'           => $request->notes,
        ];

        $pdf = Pdf::loadView('Guru.Mailbox.Pdf', $pdfData);
        $pdf->setPaper('A4', 'portrait');

        $fileName = 'surat_' . $mailType . '_' . $student->id . '_' . time() . '.pdf';
        $pdfPath  = 'mailboxes/' . $fileName;

        Storage::disk('public')->makeDirectory('mailboxes');
        Storage::disk('public')->put($pdfPath, $pdf->output());

        StudentMailbox::create([
            'student_id'  => $student->id,
            'teacher_id'  => $teacher->id,
            'mail_type'   => $mailType,
            'title'       => $request->title,
            'description' => $request->description,
            'pdf_file'    => $pdfPath,
            'week_start'  => $weekStart,
            'week_end'    => $weekEnd,
            'alpha_total' => $categoryTotal,
            'status'      => 'unread',
        ]);

        return redirect()->route('guru.dashboard')->with('success', 'Surat Mailbox berhasil dibuat dan dikirim ke siswa.');
    }
}
