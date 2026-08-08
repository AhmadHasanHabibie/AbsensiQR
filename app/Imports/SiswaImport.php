<?php

namespace App\Imports;

use App\Models\User;
use App\Models\SchoolClass;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SiswaImport implements ToCollection
{
    /**
     * Jumlah data berhasil
     */
    public $success = 0;

    /**
     * Data yang gagal
     */
    public $failed = [];

    public function collection(Collection $rows)
    {
        /*
        |--------------------------------------------------------------------------
        | Hapus Header Excel
        |--------------------------------------------------------------------------
        */

        $rows->shift();

        foreach ($rows as $index => $row) {

            try {

                /*
                |--------------------------------------------------------------------------
                | Ambil Data Excel
                |--------------------------------------------------------------------------
                */

                $name = trim($row[0] ?? '');

                $nis = trim($row[1] ?? '');

                $username = trim($row[2] ?? '');

                $password = trim($row[3] ?? '');

                $statusExcel = trim($row[4] ?? '');

                $className = trim($row[5] ?? '');

                /*
                |--------------------------------------------------------------------------
                | Validasi Data Wajib
                |--------------------------------------------------------------------------
                */

                if (
                    empty($name) ||
                    empty($nis) ||
                    empty($username) ||
                    empty($password)
                ) {

                    throw new \Exception(
                        'Nama, NIS, Username, dan Password wajib diisi.'
                    );

                }

                /*
                |--------------------------------------------------------------------------
                | Validasi Password Length
                |--------------------------------------------------------------------------
                */

                if (strlen($password) < 6) {

                    throw new \Exception(
                        'Password minimal 6 karakter.'
                    );

                }

                /*
                |--------------------------------------------------------------------------
                | Cek Username
                |--------------------------------------------------------------------------
                */

                if (
                    User::where(
                        'username',
                        $username
                    )->exists()
                ) {

                    throw new \Exception(
                        'Username sudah digunakan.'
                    );

                }

                /*
                |--------------------------------------------------------------------------
                | Cek NIS
                |--------------------------------------------------------------------------
                */

                if (
                    User::where(
                        'nis',
                        $nis
                    )->exists()
                ) {

                    throw new \Exception(
                        'NIS sudah digunakan.'
                    );

                }

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                |
                | Jika kosong otomatis Aktif
                |
                */

                $status = true;

                if ($statusExcel != '') {

                    $status = in_array(
                        strtolower($statusExcel),
                        [
                            'aktif',
                            '1',
                            'true',
                            'ya',
                            'yes'
                        ]
                    );

                }

                /*
                |--------------------------------------------------------------------------
                | Cari Kelas
                |--------------------------------------------------------------------------
                |
                | Kalau tidak ditemukan
                | class_id = null
                |
                */

                $class = null;

                if (!empty($className)) {

                    $class = SchoolClass::where(
                        'name',
                        $className
                    )->first();

                }

                /*
                |--------------------------------------------------------------------------
                | Generate QR Permanent
                |--------------------------------------------------------------------------
                */

                $qrToken = Str::uuid()->toString();

                $fileName = 'student_'
                    . time()
                    . '_'
                    . $index
                    . '.svg';

                Storage::disk('public')->put(

                    'qrcodes/' . $fileName,

                    QrCode::format('svg')
                        ->size(300)
                        ->margin(2)
                        ->generate($qrToken)

                );

                /*
                |--------------------------------------------------------------------------
                | Simpan Siswa
                |--------------------------------------------------------------------------
                */

                User::create([

                    'name' => $name,

                    'nip' => null,

                    'nis' => $nis,

                    'username' => $username,

                    'password' => Hash::make(
                        $password
                    ),

                    'role' => 'student',

                    'class_id' => optional($class)->id,

                    'qr_token' => $qrToken,

                    'qr_code' =>
                        'qrcodes/' . $fileName,

                    'photo' => null,

                    'status' => $status,

                ]);

                $this->success++;

            } catch (\Exception $e) {

                /*
                |--------------------------------------------------------------------------
                | Simpan Error
                |--------------------------------------------------------------------------
                */

                $this->failed[] = [

                    'row' => $index + 2,

                    'name' => $row[0] ?? '-',

                    'error' => $e->getMessage(),

                ];

            }

        }

    }
}

