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
        Schema::create('attendances', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relasi Siswa
            |--------------------------------------------------------------------------
            */

            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Data Absensi
            |--------------------------------------------------------------------------
            */

            // Tanggal absensi
            $table->date('attendance_date');

            // Jam masuk
            $table->time('check_in')->nullable();

            // Jam pulang
            $table->time('check_out')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status Absensi
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'hadir',
                'izin',
                'sakit',
                'alpa',
            ])->default('hadir');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};