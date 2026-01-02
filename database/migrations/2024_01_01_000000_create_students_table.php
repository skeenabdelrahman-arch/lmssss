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
        Schema::create('students', function (Blueprint $table) {
            $table->id()->startingValue(1000);
            $table->string('first_name');
            $table->string('second_name');
            $table->string('third_name');
            $table->string('forth_name');
            $table->string('email')->unique();
            $table->integer('student_phone');
            $table->integer('parent_phone');
            $table->string('city');
            $table->string('gender');
            $table->string('grade');
            $table->string('register');
            $table->string('student_code')->nullable();
            $table->string('password');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
