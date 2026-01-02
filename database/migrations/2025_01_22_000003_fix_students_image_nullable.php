<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إصلاح حقل image ليقبل NULL
     */
    public function up(): void
    {
        // التأكد من أن حقل image يقبل NULL
        try {
            DB::statement('ALTER TABLE students MODIFY image VARCHAR(255) NULL DEFAULT NULL');
        } catch (\Exception $e) {
            \Log::warning('Could not modify image column: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا حاجة لعكس التغيير
    }
};








