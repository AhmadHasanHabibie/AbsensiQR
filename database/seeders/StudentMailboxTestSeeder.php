<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class StudentMailboxTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Dapatkan atau buat kelas X RPL 1
        $class = SchoolClass::firstOrCreate(
            ['name' => 'X RPL 1'],
            ['status' => true]
        );

        // 2. Generate QR Code
        $qrToken = Str::uuid()->toString();
        $fileName = 'student_test_' . time() . '.svg';
        
        Storage::disk('public')->makeDirectory('qrcodes');
        Storage::disk('public')->put(
            'qrcodes/' . $fileName,
            QrCode::format('svg')
                ->size(300)
                ->margin(2)
                ->generate($qrToken)
        );

        // 3. Buat atau update akun siswa baru 'adit' (NIS 12345)
        $studentAdit = User::where('username', 'adit')->orWhere('nis', '12345')->first();
        if (! $studentAdit) {
            $studentAdit = User::create([
                'name'     => 'Rehan Aditia Permani',
                'nip'      => null,
                'nis'      => '12345',
                'username' => 'adit',
                'password' => Hash::make('123456'),
                'role'     => 'student',
                'class_id' => $class->id,
                'qr_token' => $qrToken,
                'qr_code'  => 'qrcodes/' . $fileName,
                'status'   => true,
            ]);
        } else {
            $studentAdit->update([
                'name'     => 'Rehan Aditia Permani',
                'nis'      => '12345',
                'username' => 'adit',
                'password' => Hash::make('123456'),
                'role'     => 'student',
                'class_id' => $class->id,
                'status'   => true,
            ]);
        }

        // Buat atau update akun siswa alternatif 'rehan' (NIS 12346)
        $studentRehan = User::where('username', 'rehan')->orWhere('nis', '12346')->first();
        if (! $studentRehan) {
            $studentRehan = User::create([
                'name'     => 'Rehan Aditya Permana',
                'nip'      => null,
                'nis'      => '12346',
                'username' => 'rehan',
                'password' => Hash::make('123456'),
                'role'     => 'student',
                'class_id' => $class->id,
                'qr_token' => Str::uuid()->toString(),
                'qr_code'  => 'qrcodes/' . $fileName,
                'status'   => true,
            ]);
        } else {
            $studentRehan->update([
                'name'     => 'Rehan Aditya Permana',
                'nis'      => '12346',
                'username' => 'rehan',
                'password' => Hash::make('123456'),
                'role'     => 'student',
                'class_id' => $class->id,
                'status'   => true,
            ]);
        }

        // 4. Buat data absensi bulan Juli 2026
        // ALFA 1 minggu berturut-turut: Senin 13 Juli s/d Jumat 17 Juli 2026 (5 hari ALFA)
        $alpaDates = [
            '2026-07-13',
            '2026-07-14',
            '2026-07-15',
            '2026-07-16',
            '2026-07-17',
        ];

        // Loop untuk tanggal 1 s/d 31 Juli 2026
        $startDate = Carbon::create(2026, 7, 1);
        $endDate = Carbon::create(2026, 7, 31);

        $studentIds = [$studentAdit->id, $studentRehan->id];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Lewati hari Minggu
            if ($date->isSunday()) {
                continue;
            }

            $dateStr = $date->toDateString();
            $isAlpa = in_array($dateStr, $alpaDates);

            foreach ($studentIds as $studentId) {
                if ($isAlpa) {
                    Attendance::updateOrCreate(
                        [
                            'student_id'      => $studentId,
                            'attendance_date' => $dateStr,
                        ],
                        [
                            'check_in'  => null,
                            'check_out' => null,
                            'status'    => 'alpa',
                        ]
                    );
                } else {
                    // Waktu check-in acak: 07:00, 07:01, 07:02, 07:03
                    $randomMinute = str_pad(rand(0, 3), 2, '0', STR_PAD_LEFT);
                    $checkInTime = "07:{$randomMinute}:00";
                    $checkOutTime = "15:30:00";

                    Attendance::updateOrCreate(
                        [
                            'student_id'      => $studentId,
                            'attendance_date' => $dateStr,
                        ],
                        [
                            'check_in'  => $checkInTime,
                            'check_out' => $checkOutTime,
                            'status'    => 'hadir',
                        ]
                    );
                }
            }
        }
    }
}
