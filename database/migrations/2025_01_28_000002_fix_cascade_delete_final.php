<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // تعطيل التحقق من المفاتيح الأجنبية مؤقتاً
        Schema::disableForeignKeyConstraints();

        try {
            // حذف جميع المفاتيح الأجنبية من exam_answers
            Schema::table('exam_answers', function (Blueprint $table) {
                $table->dropForeign('exam_answers_student_id_foreign');
            });
        } catch (\Exception $e) {
            // تجاهل الخطأ إذا كان المفتاح غير موجود
        }

        try {
            Schema::table('exam_answers', function (Blueprint $table) {
                $table->dropForeign('exam_answers_exam_id_foreign');
            });
        } catch (\Exception $e) {
        }

        try {
            Schema::table('exam_answers', function (Blueprint $table) {
                $table->dropForeign('exam_answers_question_id_foreign');
            });
        } catch (\Exception $e) {
        }

        // إضافة المفاتيح الجديدة مع RESTRICT
        Schema::table('exam_answers', function (Blueprint $table) {
            $table->foreign('student_id')
                  ->references('id')
                  ->on('students')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->foreign('exam_id')
                  ->references('id')
                  ->on('exam_names')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->foreign('question_id')
                  ->references('id')
                  ->on('exam_questions')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });

        // إعادة تفعيل التحقق من المفاتيح الأجنبية
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('exam_answers', function (Blueprint $table) {
            try {
                $table->dropForeign('exam_answers_student_id_foreign');
            } catch (\Exception $e) {
            }
            try {
                $table->dropForeign('exam_answers_exam_id_foreign');
            } catch (\Exception $e) {
            }
            try {
                $table->dropForeign('exam_answers_question_id_foreign');
            } catch (\Exception $e) {
            }
        });

        Schema::enableForeignKeyConstraints();
    }
};
