<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('exam_answers')) {
            return;
        }

        try {
            DB::statement("ALTER TABLE `exam_answers` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY;");
        } catch (\Exception $e) {
            \Log::error('Failed to modify exam_answers.id to auto_increment: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('exam_answers')) {
            return;
        }

        try {
            DB::statement("ALTER TABLE `exam_answers` MODIFY `id` BIGINT UNSIGNED NOT NULL;");
        } catch (\Exception $e) {
            \Log::error('Failed to revert exam_answers.id auto_increment: ' . $e->getMessage());
        }
    }
};
