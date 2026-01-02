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
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->references('id')->on('lecture_quizzes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('student_id')->references('id')->on('students')->cascadeOnDelete()->cascadeOnUpdate();
            $table->json('answers')->nullable(); // إجابات الطالب
            $table->integer('score')->default(0); // النقاط المكتسبة
            $table->integer('total_score')->default(0); // إجمالي النقاط
            $table->decimal('percentage', 5, 2)->default(0); // النسبة المئوية
            $table->boolean('is_passed')->default(0); // نجح أو رسب
            $table->timestamp('started_at')->nullable(); // وقت البداية
            $table->timestamp('completed_at')->nullable(); // وقت الإنهاء
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};

