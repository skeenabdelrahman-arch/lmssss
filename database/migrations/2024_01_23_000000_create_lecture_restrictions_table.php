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
        if (!Schema::hasTable('lecture_restrictions')) {
            // إنشاء الجدول لو مش موجود
            Schema::create('lecture_restrictions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->foreignId('lecture_id')->constrained('lectures')->cascadeOnDelete();
                $table->string('reason')->nullable()->comment('سبب حظر المحاضرة');
                $table->timestamps();

                // منع تكرار نفس القيد
                $table->unique(['student_id', 'lecture_id']);

                // فهرس للبحث السريع
                $table->index(['student_id', 'lecture_id']);
            });
        } else {
            // الجدول موجود بالفعل، ممكن تضيف أعمدة جديدة مستقبلًا هنا
            Schema::table('lecture_restrictions', function (Blueprint $table) {
                if (!Schema::hasColumn('lecture_restrictions', 'reason')) {
                    $table->string('reason')->nullable()->comment('سبب حظر المحاضرة')->after('lecture_id');
                }
                // لو عايز تضيف أعمدة جديدة مستقبلًا استخدم نفس الفكرة مع hasColumn
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecture_restrictions');
    }
};
