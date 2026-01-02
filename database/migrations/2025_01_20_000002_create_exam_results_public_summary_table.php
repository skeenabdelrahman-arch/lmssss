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
        if (!Schema::hasTable('exam_results_public_summary')) {
            Schema::create('exam_results_public_summary', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('exam_id');
                $table->string('student_name');
                $table->decimal('total_degree', 8, 2)->default(0);
                $table->decimal('student_degree', 8, 2)->default(0);
                $table->decimal('percentage', 5, 2)->default(0);
                $table->timestamps();
                
                $table->foreign('exam_id')->references('id')->on('exam_names')->onDelete('cascade');
                $table->index(['exam_id', 'student_name']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_results_public_summary');
    }
};

