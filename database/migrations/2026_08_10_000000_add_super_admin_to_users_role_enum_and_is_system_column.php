<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter ENUM column role pada tabel users untuk menambahkan 'super_admin'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'teacher', 'student', 'operator', 'piket', 'super_admin') NOT NULL");

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_system')) {
                $table->boolean('is_system')->default(false)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_system')) {
                $table->dropColumn('is_system');
            }
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'teacher', 'student', 'operator', 'piket') NOT NULL");
    }
};
