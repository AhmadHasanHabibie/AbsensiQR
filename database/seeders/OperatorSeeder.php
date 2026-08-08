<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OperatorSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'operator'],
            [
                'name'     => 'Operator Absensi',
                'nip'      => '19876543210001',
                'nis'      => null,
                'password' => Hash::make('123456'),
                'role'     => 'operator',
                'class_id' => null,
                'qr_code'  => null,
                'status'   => 1,
            ]
        );
    }
}
