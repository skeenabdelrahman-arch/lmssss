<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('month_id')->constrained('months')->onDelete('cascade');
            $table->foreignId('lecture_id')->nullable()->constrained('lectures')->onDelete('cascade');
            $table->string('file_path')->nullable(); // ملف الواجب (PDF مثلاً)
            $table->integer('total_marks')->default(10);
            $table->timestamp('deadline')->nullable();
            $table->boolean('status')->default(1);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assignments');
    }
};
