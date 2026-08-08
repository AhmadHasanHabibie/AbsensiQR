<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\StudentMailbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MailboxController extends Controller
{
    /**
     * Halaman daftar surat di Mailbox Siswa.
     */
    public function index()
    {
        $siswa = Auth::user();

        $mailboxes = StudentMailbox::with('teacher')
            ->where('student_id', $siswa->id)
            ->latest()
            ->paginate(10);

        return view('Siswa.Mailbox.Index', compact('mailboxes'));
    }

    /**
     * Detail surat dan ubah status menjadi Read.
     */
    public function show($id)
    {
        $siswa = Auth::user();

        $mailbox = StudentMailbox::with(['teacher', 'student.schoolClass'])
            ->where('id', $id)
            ->where('student_id', $siswa->id)
            ->firstOrFail();

        // Otomatis ubah status menjadi read setelah dibaca
        if ($mailbox->status === 'unread') {
            $mailbox->update([
                'status' => 'read',
            ]);
        }

        return view('Siswa.Mailbox.Show', compact('mailbox'));
    }

    /**
     * Download PDF Surat Pemanggilan.
     */
    public function download($id)
    {
        $siswa = Auth::user();

        $mailbox = StudentMailbox::where('id', $id)
            ->where('student_id', $siswa->id)
            ->firstOrFail();

        if (! Storage::disk('public')->exists($mailbox->pdf_file)) {
            return back()->with('error', 'File PDF tidak ditemukan di sistem.');
        }

        $fileName = 'Surat_Pemanggilan_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $siswa->name) . '_' . $mailbox->created_at->format('Ymd') . '.pdf';

        return Storage::disk('public')->download($mailbox->pdf_file, $fileName);
    }
}
