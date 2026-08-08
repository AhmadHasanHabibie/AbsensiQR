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
        Schema::create('attendance_locks', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Guru
            |--------------------------------------------------------------------------
            */

            $table->foreignId('teacher_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Kelas
            |--------------------------------------------------------------------------
            */

            $table->foreignId('class_id')
                ->constrained('school_classes')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Tanggal Absensi
            |--------------------------------------------------------------------------
            */

            $table->date('attendance_date');

            /*
            |--------------------------------------------------------------------------
            | Status Lock
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_locked')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | Waktu Konfirmasi
            |--------------------------------------------------------------------------
            */

            $table->timestamp('locked_at');

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Satu kelas hanya boleh lock sekali setiap hari
            |--------------------------------------------------------------------------
            */

            $table->unique([
                'class_id',
                'attendance_date'
            ]);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_locks');
    }
};