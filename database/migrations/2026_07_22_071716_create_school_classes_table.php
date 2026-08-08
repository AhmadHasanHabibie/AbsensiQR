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
        Schema::create('school_classes', function (Blueprint $table) {

            $table->id();

            // Nama kelas
            $table->string('name')->unique();

            // Wali kelas
            // Foreign key akan ditambahkan setelah tabel users selesai dibuat
            $table->unsignedBigInteger('teacher_id')->nullable();

            // Status kelas
            $table->boolean('status')->default(true);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};