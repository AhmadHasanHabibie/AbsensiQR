<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MaintenanceService
{
    protected string $filePath = 'system_maintenance.json';

    /**
     * Memeriksa apakah mode maintenance aktif.
     */
    public function isMaintenanceActive(): bool
    {
        if (!Storage::disk('local')->exists($this->filePath)) {
            return false;
        }

        $content = Storage::disk('local')->get($this->filePath);
        $data = json_decode($content, true);

        return isset($data['active']) && $data['active'] === true;
    }

    /**
     * Mendapatkan rincian data maintenance.
     */
    public function getMaintenanceDetails(): array
    {
        if (!Storage::disk('local')->exists($this->filePath)) {
            return [
                'active' => false,
                'activated_at' => null,
                'activated_by' => null,
                'message' => 'Sistem sedang dalam proses pemeliharaan.',
                'estimate_completion' => 'Segera',
            ];
        }

        $content = Storage::disk('local')->get($this->filePath);
        return json_decode($content, true) ?? [
            'active' => false,
            'activated_at' => null,
            'activated_by' => null,
            'message' => 'Sistem sedang dalam proses pemeliharaan.',
            'estimate_completion' => 'Segera',
        ];
    }

    /**
     * Toggle status maintenance (Aktifkan / Nonaktifkan).
     */
    public function setMaintenance(bool $active, ?string $message = null, ?string $estimateCompletion = null): bool
    {
        $currentUser = Auth::user();
        $data = [
            'active' => $active,
            'activated_at' => $active ? now()->toIso8601String() : null,
            'activated_by' => $active && $currentUser ? $currentUser->name : null,
            'message' => $message ?? 'Aplikasi sedang dalam proses pemeliharaan.',
            'estimate_completion' => $estimateCompletion ?? 'Segera (Estimasi 30-60 Menit)',
        ];

        Storage::disk('local')->put($this->filePath, json_encode($data, JSON_PRETTY_PRINT));

        $statusText = $active ? 'Mengaktifkan Mode Pemeliharaan (Maintenance Mode)' : 'Menonaktifkan Mode Pemeliharaan (Maintenance Mode)';
        ActivityLog::log(
            $statusText,
            'Pemeliharaan Sistem',
            "Super Administrator " . strtolower($statusText) . " (Estimasi: {$data['estimate_completion']}).",
            $currentUser
        );

        return $active;
    }
}
