<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * جعل month_id nullable لدعم الحزم
     */
    public function up(): void
    {
        try {
            // إزالة foreign key constraint أولاً
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'payments' 
                AND COLUMN_NAME = 'month_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            foreach ($foreignKeys as $fk) {
                try {
                    DB::statement("ALTER TABLE `payments` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                } catch (\Exception $e) {
                    \Log::warning("Could not drop foreign key {$fk->CONSTRAINT_NAME}: " . $e->getMessage());
                }
            }
            
            // تعديل العمود ليكون nullable
            DB::statement('ALTER TABLE `payments` MODIFY `month_id` BIGINT UNSIGNED NULL');
            
            // إعادة إضافة foreign key constraint
            Schema::table('payments', function (Blueprint $table) {
                $table->foreign('month_id')->references('id')->on('months')->onDelete('cascade');
            });
        } catch (\Exception $e) {
            \Log::error('Error making month_id nullable in payments table: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // حذف جميع المدفوعات التي لا تحتوي على month_id قبل إعادة القيد
        DB::table('payments')->whereNull('month_id')->delete();
        
        // إزالة foreign key constraint
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'payments' 
            AND COLUMN_NAME = 'month_id'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        foreach ($foreignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE `payments` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            } catch (\Exception $e) {
                \Log::warning("Could not drop foreign key {$fk->CONSTRAINT_NAME}: " . $e->getMessage());
            }
        }
        
        // إعادة العمود ليكون required
        DB::statement('ALTER TABLE `payments` MODIFY `month_id` BIGINT UNSIGNED NOT NULL');
        
        // إعادة إضافة foreign key constraint
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('month_id')->references('id')->on('months')->onDelete('cascade');
        });
    }
};
