<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\TeacherMailbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InternalMailboxController extends Controller
{
    /**
     * Menampilkan Kotak Masuk Mailbox Guru Wali Kelas
     */
    public function index(Request $request)
    {
        $query = TeacherMailbox::with('sender')
            ->where('receiver_id', Auth::id());

        if ($request->filled('status') && in_array($request->status, ['read', 'unread'])) {
            $query->where('status', $request->status);
        }

        $messages = $query->latest()->paginate(10);

        $unreadCount = TeacherMailbox::where('receiver_id', Auth::id())
            ->where('status', 'unread')
            ->count();

        $readCount = TeacherMailbox::where('receiver_id', Auth::id())
            ->where('status', 'read')
            ->count();

        return view('Guru.Mailbox.Index', compact('messages', 'unreadCount', 'readCount'));
    }

    /**
     * Detail Pesan Mailbox Guru & tandai sudah dibaca
     */
    public function show(string $id)
    {
        $message = TeacherMailbox::with('sender')
            ->where('receiver_id', Auth::id())
            ->findOrFail($id);

        if ($message->status === 'unread') {
            $message->update([
                'status'  => 'read',
                'read_at' => now(),
            ]);
        }

        return view('Guru.Mailbox.Show', compact('message'));
    }
}
