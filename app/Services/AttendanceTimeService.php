<?php

namespace App\Services;

use Carbon\Carbon;

class AttendanceTimeService
{
    /**
     * Jam Buka Absensi QR (WIB)
     */
    public const OPEN_TIME = '03:00:00';

    /**
     * Jam Tutup Absensi QR (WIB)
     */
    public const CLOSE_TIME = '06:31:00';

    /**
     * Batas Tepat Waktu (WIB)
     */
    public const ON_TIME_LIMIT = '06:30:59';

    /**
     * Mengetes apakah QR Attendance sedang DIBUKA berdasarkan waktu Server (Asia/Jakarta)
     * DAN status Kalender Akademik / Emergency Override.
     *
     * @return bool
     */
    public static function isAttendanceOpen(): bool
    {
        // 1. Cek Kalender Akademik & Emergency Override
        if (! AcademicCalendarService::isSchoolDay()) {
            return false;
        }

        // 2. Cek Jam Server
        $now = Carbon::now('Asia/Jakarta');
        $timeStr = $now->format('H:i:s');

        return ($timeStr >= self::OPEN_TIME && $timeStr <= self::ON_TIME_LIMIT);
    }

    /**
     * Mengetes apakah waktu absensi QR tepat waktu sudah KEDALUWARSA / DITUTUP.
     *
     * @return bool
     */
    public static function isAttendanceExpired(): bool
    {
        if (! AcademicCalendarService::isSchoolDay()) {
            return true;
        }

        $now = Carbon::now('Asia/Jakarta');
        $timeStr = $now->format('H:i:s');

        return ($timeStr >= self::CLOSE_TIME);
    }

    /**
     * Mengembalikan pesan penolakan resmi dalam Bahasa Indonesia.
     *
     * @return string
     */
    public static function getClosedReasonMessage(): string
    {
        $statusInfo = AcademicCalendarService::getDailyStatus();

        if (! $statusInfo['is_school_day']) {
            if ($statusInfo['is_emergency']) {
                $reason = $statusInfo['reason'] ? ": {$statusInfo['reason']}" : '';
                return "Hari ini ditetapkan sebagai LIBUR DARURAT oleh Super Administrator{$reason}. Seluruh proses absensi dihentikan.";
            }

            $activity = $statusInfo['activity'] ? " ({$statusInfo['activity']})" : '';
            return "Hari ini adalah {$statusInfo['status']}{$activity}. Tidak ada proses absensi.";
        }

        $now = Carbon::now('Asia/Jakarta');
        $timeStr = $now->format('H:i:s');

        if ($timeStr < self::OPEN_TIME) {
            return 'Absensi QR belum dibuka. Pintu absensi dibuka pukul 03:00 WIB.';
        }

        return 'Waktu absensi QR telah berakhir pukul 06.30 WIB.';
    }

    /**
     * Mengembalikan ringkasan status waktu absensi untuk UI / Badges.
     *
     * @return array
     */
    public static function getAttendanceTimeStatus(): array
    {
        $dailyStatus = AcademicCalendarService::getDailyStatus();

        if (! $dailyStatus['is_school_day']) {
            return [
                'is_open'      => false,
                'is_past'      => true,
                'current_time' => Carbon::now('Asia/Jakarta')->format('H:i:s'),
                'open_time'    => self::OPEN_TIME,
                'close_time'   => self::CLOSE_TIME,
                'label'        => strtoupper($dailyStatus['status']) . ($dailyStatus['activity'] ? " — {$dailyStatus['activity']}" : ''),
                'daily_status' => $dailyStatus,
            ];
        }

        $now = Carbon::now('Asia/Jakarta');
        $timeStr = $now->format('H:i:s');
        $isOpen = ($timeStr >= self::OPEN_TIME && $timeStr <= self::ON_TIME_LIMIT);
        $isPastLimit = ($timeStr >= self::CLOSE_TIME);

        return [
            'is_open'      => $isOpen,
            'is_past'      => $isPastLimit,
            'current_time' => $timeStr,
            'open_time'    => self::OPEN_TIME,
            'close_time'   => self::CLOSE_TIME,
            'label'        => $isOpen ? 'DIBUKA OTOMATIS (03:00 - 06:30 WIB)' : ($isPastLimit ? 'DITUTUP OTOMATIS (Pukul 06:31 WIB)' : 'BELUM DIBUKA (Pukul 03:00 WIB)'),
            'daily_status' => $dailyStatus,
        ];
    }
}
