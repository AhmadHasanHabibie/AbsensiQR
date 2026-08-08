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
        Schema::table('student_mailboxes', function (Blueprint $table) {
            $table->enum('mail_type', ['alpha', 'late', 'permission'])->default('alpha')->after('teacher_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_mailboxes', function (Blueprint $table) {
            $table->dropColumn('mail_type');
        });
    }
};
