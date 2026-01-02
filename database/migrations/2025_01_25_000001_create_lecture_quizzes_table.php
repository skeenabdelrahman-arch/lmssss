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
        Schema::create('lecture_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecture_id')->references('id')->on('lectures')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(1); // إجباري أو اختياري
            $table->boolean('is_active')->default(1); // مفعل أو معطل
            $table->boolean('exclude_excel_students')->default(0); // إخفاء عن طلاب Excel
            $table->integer('passing_score')->default(50); // النسبة المئوية للنجاح
            $table->integer('time_limit')->nullable(); // وقت الكويز بالدقائق (null = بدون وقت)
            $table->integer('order')->default(0); // ترتيب الكويز
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecture_quizzes');
    }
};

