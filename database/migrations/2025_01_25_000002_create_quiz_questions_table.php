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
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->references('id')->on('lecture_quizzes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('question');
            $table->string('type')->default('multiple_choice'); // multiple_choice, true_false, text
            $table->json('options')->nullable(); // للاختيار من متعدد
            $table->string('correct_answer'); // الإجابة الصحيحة
            $table->integer('points')->default(1); // النقاط
            $table->integer('order')->default(0); // ترتيب السؤال
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};

