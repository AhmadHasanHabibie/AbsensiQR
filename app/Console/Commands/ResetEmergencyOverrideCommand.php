<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\AttendanceOperationOverride;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResetEmergencyOverrideCommand extends Command
{
    /**
     * Nama dan signature dari artisan command.
     *
     * @var string
     */
    protected $signature = 'attendance:reset-override';

    /**
     * Deskripsi command.
     *
     * @var string
     */
    protected $description = 'Otomatis mengakhiri sesi Emergency Override kemarin pada 00:01 WIB dan mengembalikan sistem ke Kalender Akademik.';

    /**
     * Eksekusi command.
     */
    public function handle(): int
    {
        $yesterdayStr = Carbon::yesterday('Asia/Jakarta')->toDateString();

        $activeOverrideYesterday = AttendanceOperationOverride::where('date', $yesterdayStr)
            ->where('is_emergency_holiday', true)
            ->first();

        if ($activeOverrideYesterday) {
            $this->info("Menemukan Libur Darurat aktif kemarin ({$yesterdayStr}). Mengakhiri sesi override...");

            ActivityLog::create([
                'user_id'     => null,
                'role'        => 'system',
                'activity'    => 'Auto Reset Libur Darurat',
                'module'      => 'Operasional Absensi',
                'description' => "Auto Reset Libur Darurat: Sesi override tanggal {$yesterdayStr} telah berakhir. Sistem secara otomatis kembali mengikuti Kalender Akademik.",
                'ip_address'  => '127.0.0.1',
                'browser'     => 'CLI Scheduler',
                'platform'    => 'CronJob',
                'device'      => 'Server Task',
            ]);

            $this->info("Auto reset berhasil dicatat ke Activity Log.");
        } else {
            $this->info("Tidak ada Libur Darurat aktif kemarin ({$yesterdayStr}). Sistem beroperasi normal.");
        }

        return Command::SUCCESS;
    }
}
