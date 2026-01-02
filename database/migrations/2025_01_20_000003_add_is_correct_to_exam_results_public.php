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
        if (Schema::hasTable('exam_results_public')) {
            Schema::table('exam_results_public', function (Blueprint $table) {
                if (!Schema::hasColumn('exam_results_public', 'is_correct')) {
                    $table->boolean('is_correct')->default(0)->after('answer');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('exam_results_public')) {
            Schema::table('exam_results_public', function (Blueprint $table) {
                if (Schema::hasColumn('exam_results_public', 'is_correct')) {
                    $table->dropColumn('is_correct');
                }
            });
        }
    }
};

