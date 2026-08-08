<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentMailbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MailboxController extends Controller
{
    /**
     * Halaman daftar seluruh Surat Pemanggilan untuk Admin.
     */
    public function index(Request $request)
    {
        $query = StudentMailbox::with(['student.schoolClass', 'teacher']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $mailboxes = $query->latest()->paginate(15)->withQueryString();

        return view('Admin.Mailbox.Index', compact('mailboxes'));
    }

    /**
     * Download PDF Surat Pemanggilan oleh Admin.
     */
    public function download($id)
    {
        $mailbox = StudentMailbox::with('student')->findOrFail($id);

        if (! Storage::disk('public')->exists($mailbox->pdf_file)) {
            return back()->with('error', 'File PDF tidak ditemukan di sistem.');
        }

        $fileName = 'Surat_Pemanggilan_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $mailbox->student->name ?? 'Siswa') . '_' . $mailbox->created_at->format('Ymd') . '.pdf';

        return Storage::disk('public')->download($mailbox->pdf_file, $fileName);
    }
}
