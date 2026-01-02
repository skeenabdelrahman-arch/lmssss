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
        Schema::create('lectures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('month_id')->references('id')->on('months')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('grade');
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_server')->nullable();
            $table->string('image')->nullable();
            $table->boolean('status')->default(1);
            $table->integer('views')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lectures');
    }
};
