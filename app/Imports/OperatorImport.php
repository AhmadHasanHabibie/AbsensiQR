<?php

namespace App\Imports;

use App\Models\User;
use App\Services\DuplicateAccountService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;

class OperatorImport implements ToCollection
{
    /**
     * Jumlah data berhasil diimport
     */
    public int $success = 0;

    /**
     * Data baris yang gagal
     * Format: [['row' => int, 'name' => string, 'error' => string], ...]
     */
    public array $failed = [];

    private DuplicateAccountService $duplicateService;

    public function __construct()
    {
        $this->duplicateService = new DuplicateAccountService();
    }

    public function collection(Collection $rows): void
    {
        /*
        |--------------------------------------------------------------------------
        | Hapus baris header Excel
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

            $rowNumber = $index + 2; // +2: baris 1 = header, mulai data dari baris 2

            try {

                /*
                |--------------------------------------------------------------------------
                | Ambil Data dari Kolom Excel
                | Kolom: Nama Operator | NIP | Username | Password | Status
                |--------------------------------------------------------------------------
                */

                $name      = trim($row[0] ?? '');
                $nip       = trim($row[1] ?? '');
                $username  = trim($row[2] ?? '');
                $password  = trim($row[3] ?? '');
                $statusRaw = trim($row[4] ?? '');

                /*
                |--------------------------------------------------------------------------
                | Validasi Field Wajib
                |--------------------------------------------------------------------------
                */

                if (empty($name) || empty($username) || empty($password) || empty($nip)) {
                    throw new \Exception('Nama, Username, Password, dan NIP wajib diisi.');
                }

                if (strlen($password) < 6) {
                    throw new \Exception('Password minimal 6 karakter.');
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
                | Parse Status
                |--------------------------------------------------------------------------
                */

                $status = true;
                if ($statusRaw !== '') {
                    $status = in_array(strtolower($statusRaw), ['aktif', '1', 'true', 'ya', 'yes', 'active']);
                }

                /*
                |--------------------------------------------------------------------------
                | Simpan Operator
                |--------------------------------------------------------------------------
                */

                User::create([
                    'name'      => $name,
                    'nip'       => $nip,
                    'nis'       => null,
                    'username'  => $username,
                    'password'  => Hash::make($password),
                    'role'      => 'operator',
                    'class_id'  => null,
                    'qr_token'  => null,
                    'qr_code'   => null,
                    'photo'     => null,
                    'status'    => $status,
                ]);

                $this->success++;

            } catch (\Exception $e) {

                $this->failed[] = [
                    'row'   => $rowNumber,
                    'name'  => $row[0] ?? '-',
                    'error' => $e->getMessage(),
                ];

            }
        }
    }
}
