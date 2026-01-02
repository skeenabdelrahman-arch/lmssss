<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
            $table->enum('type', ['mcq_single', 'mcq_multi', 'essay']);
            $table->text('question_text');
            $table->decimal('max_marks', 8, 2)->default(0);
            $table->boolean('is_required')->default(true);
            $table->boolean('auto_grade')->default(true); // MCQ only
            $table->boolean('allow_text')->default(true); // essay only
            $table->boolean('allow_file')->default(false); // essay only
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('assignment_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('assignment_questions')->onDelete('cascade');
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('assignment_question_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('assignment_submissions')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('assignment_questions')->onDelete('cascade');
            $table->longText('answer_text')->nullable();
            $table->json('selected_options')->nullable();
            $table->string('attachment_path')->nullable();
            $table->decimal('awarded_marks', 8, 2)->nullable();
            $table->enum('status', ['pending', 'auto_graded', 'reviewed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_question_answers');
        Schema::dropIfExists('assignment_question_options');
        Schema::dropIfExists('assignment_questions');
    }
};
