<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->text('notes')->nullable(); // ملاحظات الطالب
            $table->longText('file_path')->nullable(); // ملف الإجابة
            $table->timestamp('submitted_at');
            $table->integer('marks')->nullable(); // الدرجة
            $table->text('feedback')->nullable(); // تعليق المدرس
            $table->timestamp('graded_at')->nullable();
            $table->enum('status', ['pending', 'graded', 'late'])->default('pending');
            $table->timestamps();
            
            // منع الطالب من رفع أكثر من إجابة واحدة
            $table->unique(['assignment_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
