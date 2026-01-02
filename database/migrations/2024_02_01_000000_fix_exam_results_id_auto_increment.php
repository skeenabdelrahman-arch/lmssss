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
        if (!Schema::hasTable('exam_results')) {
            return;
        }

        // Ensure the id column is BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY (works for MySQL)
        try {
            DB::statement("ALTER TABLE `exam_results` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY;");
        } catch (\Exception $e) {
            // If alter fails, log but don't break the migration to allow manual inspection
            \Log::error('Failed to modify exam_results.id to auto_increment: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('exam_results')) {
            return;
        }

        // Attempt to remove AUTO_INCREMENT if possible (best-effort)
        try {
            DB::statement("ALTER TABLE `exam_results` MODIFY `id` BIGINT UNSIGNED NOT NULL;");
        } catch (\Exception $e) {
            \Log::error('Failed to revert exam_results.id auto_increment: ' . $e->getMessage());
        }
    }
};
