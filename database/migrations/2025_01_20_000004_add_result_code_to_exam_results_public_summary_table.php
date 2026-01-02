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
        Schema::table('exam_results_public_summary', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_results_public_summary', 'result_code')) {
                $table->string('result_code', 20)->unique()->nullable()->after('id');
                $table->index('result_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_results_public_summary', function (Blueprint $table) {
            if (Schema::hasColumn('exam_results_public_summary', 'result_code')) {
                $table->dropIndex(['result_code']);
                $table->dropColumn('result_code');
            }
        });
    }
};

