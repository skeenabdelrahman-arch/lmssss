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
        Schema::create('exam_names', function (Blueprint $table) {
            $table->id();
            $table->string('exam_title');
            $table->string('exam_description')->nullable();
            $table->foreignId('month_id')->references('id')->on('months')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('exam_time');
            $table->string('grade');
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_names');
    }
};
