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
        // التحقق من وجود الجدول
        if (!Schema::hasTable('students')) {
            return;
        }
        
        try {
            // تغيير student_phone من integer إلى bigInteger
            if (Schema::hasColumn('students', 'student_phone')) {
                DB::statement('ALTER TABLE students MODIFY student_phone BIGINT NULL');
            }
            
            // تغيير parent_phone من integer إلى bigInteger
            if (Schema::hasColumn('students', 'parent_phone')) {
                DB::statement('ALTER TABLE students MODIFY parent_phone BIGINT NULL');
            }
        } catch (\Exception $e) {
            \Log::warning('Could not modify phone columns to bigInteger: ' . $e->getMessage());
            // لا نتوقف، فقط نسجل الخطأ
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // إرجاع إلى integer (مع التحذير أنه قد يفقد بيانات)
            try {
                DB::statement('ALTER TABLE students MODIFY student_phone INTEGER');
                DB::statement('ALTER TABLE students MODIFY parent_phone INTEGER');
            } catch (\Exception $e) {
                // إذا فشل، يعني أن هناك أرقام أكبر من الحد الأقصى
                \Log::warning('Could not revert phone columns to integer: ' . $e->getMessage());
            }
        });
    }
};

