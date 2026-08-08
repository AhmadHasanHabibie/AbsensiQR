<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Seed akun Super Administrator dengan PIN default 123456.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name'      => 'Super Administrator',
                'nip'       => null,
                'nis'       => null,
                'password'  => Hash::make('superadmin123'),
                'pin'       => Hash::make('123456'),
                'role'      => User::ROLE_SUPER_ADMIN ?? 'super_admin',
                'class_id'  => null,
                'qr_code'   => null,
                'status'    => true,
                'is_system' => true,
            ]
        );
    }
}
