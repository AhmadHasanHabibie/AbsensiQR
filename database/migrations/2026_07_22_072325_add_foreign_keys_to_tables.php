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
        /*
        |--------------------------------------------------------------------------
        | Users -> School Classes
        |--------------------------------------------------------------------------
        | Siswa memiliki kelas
        */
        Schema::table('users', function (Blueprint $table) {

            $table->foreign('class_id')
                ->references('id')
                ->on('school_classes')
                ->nullOnDelete();

        });

        /*
        |--------------------------------------------------------------------------
        | School Classes -> Users
        |--------------------------------------------------------------------------
        | Kelas memiliki wali kelas (Guru)
        */
        Schema::table('school_classes', function (Blueprint $table) {

            $table->foreign('teacher_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

        });

        // Tidak perlu lagi membuat foreign key attendances.student_id
        // karena sudah dibuat di create_attendances_table.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_classes', function (Blueprint $table) {

            $table->dropForeign(['teacher_id']);

        });

        Schema::table('users', function (Blueprint $table) {

            $table->dropForeign(['class_id']);

        });
    }
};