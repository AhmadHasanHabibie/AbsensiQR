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
        Schema::create('attendance_operation_overrides', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique()->comment('Tanggal override operasional');
            $table->boolean('is_emergency_holiday')->default(true)->comment('True jika di-set sebagai Libur Darurat');
            $table->text('reason')->nullable()->comment('Alasan libur darurat');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('date');
            $table->index('is_emergency_holiday');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_operation_overrides');
    }
};
