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
        // التحقق من وجود الجدول قبل إنشائه
        if (!Schema::hasTable('exam_results_public')) {
            Schema::create('exam_results_public', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('exam_id');
                $table->string('student_name');
                $table->unsignedBigInteger('question_id');
                $table->text('answer')->nullable();
                $table->boolean('is_correct')->default(0);
                $table->timestamps();
                
                $table->foreign('exam_id')->references('id')->on('exam_names')->onDelete('cascade');
                $table->foreign('question_id')->references('id')->on('exam_questions')->onDelete('cascade');
                $table->index(['exam_id', 'student_name']);
            });
        }
        // إذا كان الجدول موجوداً بالفعل، سيتم إضافة العمود is_correct من migration منفصلة
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_results_public');
    }
};

