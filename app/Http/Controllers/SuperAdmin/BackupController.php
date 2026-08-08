<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\DatabaseBackup;
use App\Models\ActivityLog;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    protected BackupService $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * Halaman Backup Database.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Audit Log
        ActivityLog::log(
            'Melihat Backup',
            'Backup Database',
            "Pengguna {$user->name} mengakses halaman Backup Database.",
            $user
        );

        $lastBackup  = $this->backupService->getLastBackup();
        $backups     = DatabaseBackup::with('creator')->orderBy('created_at', 'desc')->paginate(10);

        // Summary stats — semua dari sumber data yang sama (DatabaseBackup model)
        $totalBackups = DatabaseBackup::count();
        $totalBytes   = (int) DatabaseBackup::sum('file_size');

        // Format total size menggunakan helper yang sama dengan model
        if ($totalBytes >= 1073741824) {
            $totalSizeFormatted = number_format($totalBytes / 1073741824, 2) . ' GB';
        } elseif ($totalBytes >= 1048576) {
            $totalSizeFormatted = number_format($totalBytes / 1048576, 2) . ' MB';
        } elseif ($totalBytes >= 1024) {
            $totalSizeFormatted = number_format($totalBytes / 1024, 2) . ' KB';
        } elseif ($totalBytes > 0) {
            $totalSizeFormatted = $totalBytes . ' B';
        } else {
            $totalSizeFormatted = '—';
        }

        $latestRecord   = DatabaseBackup::latest()->first();
        $lastBackupDate = $latestRecord
            ? $latestRecord->created_at->translatedFormat('d M Y, H:i')
            : 'Belum ada backup';

        return view('SuperAdmin.Backup.Index', compact(
            'lastBackup',
            'backups',
            'totalBackups',
            'totalSizeFormatted',
            'lastBackupDate'
        ));
    }

    /**
     * Eksekusi pembuatan backup database baru.
     */
    public function store(Request $request)
    {
        try {
            $backup = $this->backupService->createBackup();

            return redirect()
                ->route('superadmin.backup.index')
                ->with('success', "Backup database ({$backup->filename}) berhasil dibuat! (" . $backup->formatted_size . ")");
        } catch (\Throwable $e) {
            return redirect()
                ->route('superadmin.backup.index')
                ->with('error', 'Gagal membuat backup database: ' . $e->getMessage());
        }
    }

    /**
     * Download file backup database.
     */
    public function download(string $id)
    {
        $backup = DatabaseBackup::findOrFail($id);
        $relativePath = 'backups/' . $backup->filename;

        if (Storage::disk('local')->exists($relativePath)) {
            ActivityLog::log(
                'Download Backup Database',
                'Backup Database',
                "Super Administrator mengunduh file backup {$backup->filename}.",
                Auth::user()
            );

            return Storage::disk('local')->download($relativePath, $backup->filename);
        }

        return back()->with('error', 'File backup tidak ditemukan di penyimpanan server.');
    }
}
