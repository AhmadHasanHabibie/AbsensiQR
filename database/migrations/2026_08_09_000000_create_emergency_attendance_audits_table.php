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
        Schema::create('emergency_attendance_audits', function (Blueprint $table) {
            $table->id();

            $table->foreignId('attendance_id')
                ->constrained('attendances')
                ->cascadeOnDelete();

            $table->foreignId('operator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('teacher_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('class_id')
                ->nullable()
                ->constrained('school_classes')
                ->nullOnDelete();

            $table->string('reason');
            $table->text('note')->nullable();

            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device')->nullable();

            $table->string('initial_status')->default('Hadir Manual');
            $table->string('final_status')->nullable();
            $table->string('validation_type')->nullable(); // 'automatic' or 'manual'

            $table->timestamp('input_at')->useCurrent();
            $table->timestamp('validated_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_attendance_audits');
    }
};
