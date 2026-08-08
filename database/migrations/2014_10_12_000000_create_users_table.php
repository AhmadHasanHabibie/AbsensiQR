<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {

            $table->id();


            /*
            |--------------------------------------------------------------------------
            | Data Utama
            |--------------------------------------------------------------------------
            */
            $table->string('name');


            /*
            |--------------------------------------------------------------------------
            | Identitas Guru
            |--------------------------------------------------------------------------
            */
            $table->string('nip')
                ->nullable()
                ->unique();


            /*
            |--------------------------------------------------------------------------
            | Identitas Siswa
            |--------------------------------------------------------------------------
            */
            $table->string('nis')
                ->nullable()
                ->unique();


            /*
            |--------------------------------------------------------------------------
            | QR Permanent Siswa
            |--------------------------------------------------------------------------
            | Setiap siswa memiliki 1 QR unik
            */
            $table->string('qr_code')
                ->nullable()
                ->unique();


            /*
            |--------------------------------------------------------------------------
            | Login
            |--------------------------------------------------------------------------
            */
            $table->string('username')
                ->unique();

            $table->string('password');


            /*
            |--------------------------------------------------------------------------
            | Role User
            |--------------------------------------------------------------------------
            */
            $table->enum('role', [
                'admin',
                'teacher',
                'student',
            ]);


            /*
            |--------------------------------------------------------------------------
            | Kelas Siswa
            |--------------------------------------------------------------------------
            | Foreign key dibuat di migration terpisah
            */
            $table->unsignedBigInteger('class_id')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | Status Akun
            |--------------------------------------------------------------------------
            */
            $table->boolean('status')
                ->default(true);


            $table->rememberToken();

            $table->timestamps();

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};