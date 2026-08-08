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
        Schema::create('academic_calendars', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Identitas Tahun Ajaran
            |--------------------------------------------------------------------------
            */
            $table->string('academic_year', 20)->comment('Contoh: 2026/2027');
            $table->date('date')->comment('Tanggal kalender');
            $table->string('day_name', 15)->comment('Nama hari: Senin, Selasa, ...');
            $table->tinyInteger('month')->unsigned()->comment('Nomor bulan 1-12');
            $table->string('semester', 10)->comment('Ganjil atau Genap');

            /*
            |--------------------------------------------------------------------------
            | Klasifikasi Hari
            |--------------------------------------------------------------------------
            */
            $table->string('status', 50)->comment('Hari Belajar, Libur Nasional, dsb.');
            $table->string('category', 50)->comment('Akademik, Libur, Kegiatan Sekolah, dsb.');
            $table->string('activity', 150)->nullable()->comment('Nama kegiatan atau keterangan singkat');

            /*
            |--------------------------------------------------------------------------
            | Flag Operasional (untuk integrasi tahap berikutnya)
            |--------------------------------------------------------------------------
            */
            $table->boolean('qr_status')->default(false)->comment('QR Scan aktif pada hari ini?');
            $table->boolean('teacher_attendance')->default(false)->comment('Absensi guru aktif?');
            $table->boolean('student_attendance')->default(false)->comment('Absensi siswa aktif?');
            $table->boolean('operator_attendance')->default(false)->comment('Operator aktif?');

            /*
            |--------------------------------------------------------------------------
            | Keterangan & Status Record
            |--------------------------------------------------------------------------
            */
            $table->text('description')->nullable()->comment('Keterangan panjang');
            $table->boolean('is_active')->default(false)->comment('Apakah tahun ajaran ini aktif?');

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Index untuk Query Performa Tinggi
            |--------------------------------------------------------------------------
            */
            $table->index('date');
            $table->index('academic_year');
            $table->index('is_active');
            $table->index(['academic_year', 'date']);
            $table->index(['academic_year', 'is_active']);
            $table->unique(['academic_year', 'date'], 'unique_academic_year_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_calendars');
    }
};
