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
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('is_emergency')->default(false)->after('late_note');
            $table->string('emergency_reason')->nullable()->after('is_emergency');
            $table->text('emergency_note')->nullable()->after('emergency_reason');
            $table->foreignId('operator_id')->nullable()->after('emergency_note')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['operator_id']);
            $table->dropColumn([
                'is_emergency',
                'emergency_reason',
                'emergency_note',
                'operator_id',
            ]);
        });
    }
};
