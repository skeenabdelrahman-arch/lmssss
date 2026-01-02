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
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->references('id')->on('exam_names')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('question_title')->nullable();
            $table->string('ch_1')->nullable();
            $table->string('ch_2')->nullable();
            $table->string('ch_3')->nullable();
            $table->string('ch_4')->nullable();
            $table->string('img')->nullable(); // ممكن الصورة تبقي هي السؤال يعني
            $table->string('correct_answer')->nullable();
            $table->string('Q_degree');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
    }
};
