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
        if (Schema::hasTable('student_subscriptions')) {
            Schema::table('student_subscriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('student_subscriptions', 'deactivation_reason')) {
                    $table->text('deactivation_reason')->nullable()->after('is_active');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('student_subscriptions')) {
            Schema::table('student_subscriptions', function (Blueprint $table) {
                if (Schema::hasColumn('student_subscriptions', 'deactivation_reason')) {
                    $table->dropColumn('deactivation_reason');
                }
            });
        }
    }
};
