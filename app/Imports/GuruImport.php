<?php

namespace App\Imports;

use App\Models\User;
use App\Models\SchoolClass;
use App\Services\DuplicateAccountService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;

class GuruImport implements ToCollection
{
    /**
     * Jumlah data berhasil
     */
    public $success = 0;

    /**
     * Data yang gagal
     */
    public $failed = [];

    private DuplicateAccountService $duplicateService;

    public function __construct()
    {
        $this->duplicateService = new DuplicateAccountService();
    }

    public function collection(Collection $rows)
    {
        /*
        |--------------------------------------------------------------------------
        | Hapus Header Excel
        |--------------------------------------------------------------------------
        */

        $rows->shift();

        /*
        |--------------------------------------------------------------------------
        | Tracker duplikat dalam satu file
        |--------------------------------------------------------------------------
        */
        $processedInFile = [];

        foreach ($rows as $index => $row) {

            try {

                /*
                |--------------------------------------------------------------------------
                | Ambil Data Excel
                |--------------------------------------------------------------------------
                */

                $name = trim($row[0] ?? '');

                $nip = trim($row[1] ?? '');

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
                    empty($nip) ||
                    empty($username) ||
                    empty($password)
                ) {

                    throw new \Exception(
                        'Nama, NIP, Username, dan Password wajib diisi.'
                    );

                }

                /*
                |--------------------------------------------------------------------------
                | Cek Duplikat dalam File (baris sebelumnya)
                |--------------------------------------------------------------------------
                */

                $inFileError = $this->duplicateService->checkDuplicateInFile(
                    $username,
                    $nip,
                    $processedInFile
                );

                if ($inFileError !== null) {
                    throw new \Exception($inFileError);
                }

                /*
                |--------------------------------------------------------------------------
                | Cek Duplikat Lintas Role (database)
                |--------------------------------------------------------------------------
                */

                $duplicateErrors = $this->duplicateService->validateAccount($username, $nip);

                if (!empty($duplicateErrors)) {
                    throw new \Exception(implode(' | ', $duplicateErrors));
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
                | Simpan Guru
                |--------------------------------------------------------------------------
                */

                User::create([

                    'name' => $name,

                    'nip' => $nip,

                    'nis' => null,

                    'username' => $username,

                    'password' => Hash::make(
                        $password
                    ),

                    'role' => 'teacher',

                    'class_id' => optional($class)->id,

                    'status' => $status,

                    'qr_token' => null,

                    'qr_code' => null,

                    'photo' => null,

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