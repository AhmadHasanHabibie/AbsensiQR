<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\MaintenanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceController extends Controller
{
    protected MaintenanceService $maintenanceService;

    public function __construct(MaintenanceService $maintenanceService)
    {
        $this->maintenanceService = $maintenanceService;
    }

    /**
     * Halaman Pemeliharaan Sistem (Maintenance).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Audit Log
        ActivityLog::log(
            'Melihat Maintenance',
            'Pemeliharaan Sistem',
            "Pengguna {$user->name} melihat halaman Pemeliharaan Sistem.",
            $user
        );

        $isMaintenance = $this->maintenanceService->isMaintenanceActive();
        $maintenanceDetails = $this->maintenanceService->getMaintenanceDetails();

        return view('SuperAdmin.Maintenance.Index', compact('isMaintenance', 'maintenanceDetails'));
    }

    /**
     * Toggle status maintenance (Aktifkan / Nonaktifkan).
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'action'              => ['required', 'in:enable,disable'],
            'message'             => ['nullable', 'string', 'max:255'],
            'estimate_completion' => ['nullable', 'string', 'max:100'],
        ]);

        $active = ($request->action === 'enable');
        $message = $request->input('message') ?? 'Aplikasi sedang dalam proses pemeliharaan.';
        $estimateCompletion = $request->input('estimate_completion') ?? 'Segera (30-60 Menit)';

        $this->maintenanceService->setMaintenance($active, $message, $estimateCompletion);

        $statusMsg = $active
            ? 'Pemeliharaan Sistem (Maintenance Mode) BERHASIL DIAKTIFKAN. Seluruh pengguna biasa akan melihat halaman 503.'
            : 'Pemeliharaan Sistem (Maintenance Mode) BERHASIL DINONAKTIFKAN. Seluruh pengguna sekolah dapat mengakses sistem normal kembali.';

        return redirect()
            ->route('superadmin.maintenance.index')
            ->with('success', $statusMsg);
    }
}
