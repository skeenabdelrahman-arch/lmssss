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
        Schema::table('students', function (Blueprint $table) {
            $table->boolean('is_blocked')->default(false)->after('password');
            $table->timestamp('blocked_until')->nullable()->after('is_blocked');
            $table->integer('failed_login_attempts')->default(0)->after('blocked_until');
            $table->timestamp('last_failed_login_at')->nullable()->after('failed_login_attempts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['is_blocked', 'blocked_until', 'failed_login_attempts', 'last_failed_login_at']);
        });
    }
};
