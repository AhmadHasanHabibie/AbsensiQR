<?php

namespace App\Services;

use App\Models\User;

/**
 * DuplicateAccountService
 *
 * Memvalidasi bahwa tidak ada akun duplikat lintas role.
 *
 * Aturan:
 * - Username harus unik secara global di seluruh tabel users
 * - NIP harus unik secara global di seluruh tabel users
 * - Jika username DAN NIP keduanya sudah dipakai role lain = akun dianggap identik = ditolak
 */
class DuplicateAccountService
{
    /**
     * Label human-readable untuk setiap role.
     */
    private const ROLE_LABELS = [
        'teacher'    => 'Guru',
        'operator'   => 'Operator',
        'piket'      => 'Guru Piket',
        'admin'      => 'Administrator',
        'super_admin'=> 'Super Administrator',
        'student'    => 'Siswa',
    ];

    /**
     * Validasi apakah username sudah digunakan oleh akun lain.
     *
     * @param  string   $username    Username yang akan dicek
     * @param  int|null $excludeId   ID user yang dikecualikan (saat edit)
     * @return string|null           Pesan error jika duplikat, null jika aman
     */
    public function checkUsername(string $username, ?int $excludeId = null): ?string
    {
        $query = User::where('username', $username);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        $existing = $query->first();

        if ($existing) {
            $roleLabel = self::ROLE_LABELS[$existing->role] ?? ucfirst($existing->role);
            return "Username '{$username}' sudah digunakan oleh akun {$roleLabel}.";
        }

        return null;
    }

    /**
     * Validasi apakah NIP sudah digunakan oleh akun lain.
     *
     * @param  string   $nip         NIP yang akan dicek
     * @param  int|null $excludeId   ID user yang dikecualikan (saat edit)
     * @return string|null           Pesan error jika duplikat, null jika aman
     */
    public function checkNip(string $nip, ?int $excludeId = null): ?string
    {
        $query = User::where('nip', $nip);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        $existing = $query->first();

        if ($existing) {
            $roleLabel = self::ROLE_LABELS[$existing->role] ?? ucfirst($existing->role);
            return "NIP '{$nip}' sudah digunakan oleh akun {$roleLabel}.";
        }

        return null;
    }

    /**
     * Validasi duplikat akun saat Import Excel (dalam satu file).
     *
     * Mendeteksi apakah pasangan username+nip sudah pernah muncul
     * di baris sebelumnya dalam file yang sama.
     *
     * @param  string $username
     * @param  string $nip
     * @param  array  &$processedInFile  Array berisi ['username:nip' => true] baris sebelumnya
     * @return string|null               Pesan error jika duplikat dalam file, null jika aman
     */
    public function checkDuplicateInFile(string $username, string $nip, array &$processedInFile): ?string
    {
        $key = strtolower(trim($username)) . '|' . strtolower(trim($nip));

        if (isset($processedInFile[$key])) {
            return 'Data duplikat ditemukan di dalam file Excel (username dan NIP sudah ada pada baris sebelumnya).';
        }

        $processedInFile[$key] = true;
        return null;
    }

    /**
     * Validasi lengkap untuk satu akun (username + NIP).
     * Mengembalikan array berisi semua error yang ditemukan, atau array kosong jika aman.
     *
     * @param  string   $username
     * @param  string   $nip
     * @param  int|null $excludeId   ID user yang dikecualikan (saat edit)
     * @return array                 Array of error strings
     */
    public function validateAccount(string $username, string $nip, ?int $excludeId = null): array
    {
        $errors = [];

        $usernameError = $this->checkUsername($username, $excludeId);
        if ($usernameError !== null) {
            $errors[] = $usernameError;
        }

        $nipError = $this->checkNip($nip, $excludeId);
        if ($nipError !== null) {
            $errors[] = $nipError;
        }

        return $errors;
    }
}
