<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('pdfs')) {
            Schema::table('pdfs', function (Blueprint $table) {
                if (!Schema::hasColumn('pdfs', 'lecture_id')) {
                    $table->foreignId('lecture_id')->nullable()->after('month_id')->constrained('lectures')->cascadeOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pdfs') && Schema::hasColumn('pdfs', 'lecture_id')) {
            Schema::table('pdfs', function (Blueprint $table) {
                $table->dropForeign(['lecture_id']);
                $table->dropColumn('lecture_id');
            });
        }
    }
};
