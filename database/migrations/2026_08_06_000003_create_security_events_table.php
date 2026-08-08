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
        Schema::create('security_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('username')->nullable();
            $table->string('role')->nullable();
            $table->enum('event_type', [
                'failed_login',
                'multiple_failed_login',
                'rate_limit',
                'suspicious_request',
                'invalid_route',
                'forbidden_access',
                'sql_injection_attempt',
                'xss_attempt',
                'csrf_failure',
                'scan_spam',
                'unusual_activity'
            ]);
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->string('device')->nullable();
            $table->text('request_url')->nullable();
            $table->string('request_method')->nullable();
            $table->text('user_agent')->nullable();
            $table->enum('status', ['new', 'reviewed', 'resolved'])->default('new');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_events');
    }
};
