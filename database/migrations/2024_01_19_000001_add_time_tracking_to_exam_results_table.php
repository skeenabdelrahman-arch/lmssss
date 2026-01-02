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
        if (!Schema::hasTable('exam_results')) {
            // إنشاء الجدول لو مش موجود (يمكن إضافة الأعمدة الأساسية حسب المشروع)
            Schema::create('exam_results', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_id')->constrained('exam_names')->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
                $table->boolean('is_marked')->default(false);
                $table->timestamps();
            });

            // إضافة الأعمدة الجديدة
            Schema::table('exam_results', function (Blueprint $table) {
                $table->timestamp('started_at')->nullable()->after('is_marked');
                $table->timestamp('completed_at')->nullable()->after('started_at');
            });
        } else {
            Schema::table('exam_results', function (Blueprint $table) {
                if (!Schema::hasColumn('exam_results', 'started_at')) {
                    $table->timestamp('started_at')->nullable()->after('is_marked');
                }
                if (!Schema::hasColumn('exam_results', 'completed_at')) {
                    $table->timestamp('completed_at')->nullable()->after('started_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            if (Schema::hasColumn('exam_results', 'started_at')) {
                $table->dropColumn('started_at');
            }
            if (Schema::hasColumn('exam_results', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
        });
    }
};
