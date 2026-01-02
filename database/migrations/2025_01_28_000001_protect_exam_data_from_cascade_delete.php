<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * حماية بيانات الامتحانات من الحذف التلقائي
     */
    public function up(): void
    {
        // حماية exam_questions من الحذف التلقائي عند حذف exam_names
        Schema::table('exam_questions', function (Blueprint $table) {
            // حذف Foreign Key القديم
            $table->dropForeign(['exam_id']);
        });
        
        Schema::table('exam_questions', function (Blueprint $table) {
            // إضافة Foreign Key جديد مع restrict بدلاً من cascade
            $table->foreign('exam_id')
                  ->references('id')
                  ->on('exam_names')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });

        // حماية exam_answers من الحذف التلقائي
        Schema::table('exam_answers', function (Blueprint $table) {
            $table->dropForeign(['exam_id']);
            $table->dropForeign(['question_id']);
        });
        
        Schema::table('exam_answers', function (Blueprint $table) {
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

        // حماية exam_results من الحذف التلقائي
        Schema::table('exam_results', function (Blueprint $table) {
            $table->dropForeign(['exam_id']);
        });
        
        Schema::table('exam_results', function (Blueprint $table) {
            $table->foreign('exam_id')
                  ->references('id')
                  ->on('exam_names')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إعادة Foreign Keys القديمة مع cascade
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->dropForeign(['exam_id']);
        });
        
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->foreign('exam_id')
                  ->references('id')
                  ->on('exam_names')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        Schema::table('exam_answers', function (Blueprint $table) {
            $table->dropForeign(['exam_id']);
            $table->dropForeign(['question_id']);
        });
        
        Schema::table('exam_answers', function (Blueprint $table) {
            $table->foreign('exam_id')
                  ->references('id')
                  ->on('exam_names')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
                  
            $table->foreign('question_id')
                  ->references('id')
                  ->on('exam_questions')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        Schema::table('exam_results', function (Blueprint $table) {
            $table->dropForeign(['exam_id']);
        });
        
        Schema::table('exam_results', function (Blueprint $table) {
            $table->foreign('exam_id')
                  ->references('id')
                  ->on('exam_names')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }
};

