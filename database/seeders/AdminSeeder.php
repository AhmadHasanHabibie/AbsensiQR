<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'nip' => null,
                'nis' => null,
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'class_id' => null,
                'qr_code' => null,
                'status' => true,
            ]
        );
    }
}