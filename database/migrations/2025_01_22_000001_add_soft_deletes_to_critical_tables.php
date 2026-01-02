<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة SoftDeletes لحماية البيانات من الحذف الدائم
     */
    public function up(): void
    {
        // إضافة deleted_at للجداول الحرجة
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('exam_names', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_names', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('exam_answers', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_answers', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('exam_results', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_results', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('exam_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_questions', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('lectures', function (Blueprint $table) {
            if (!Schema::hasColumn('lectures', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('months', function (Blueprint $table) {
            if (!Schema::hasColumn('months', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('pdfs', function (Blueprint $table) {
            if (!Schema::hasColumn('pdfs', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('student_subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('student_subscriptions', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('exam_names', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('exam_answers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('exam_results', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('exam_questions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('lectures', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('months', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('pdfs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('student_subscriptions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};








