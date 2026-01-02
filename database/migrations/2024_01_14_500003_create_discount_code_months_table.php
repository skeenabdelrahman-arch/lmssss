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
        Schema::create('discount_code_months', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discount_code_id')->references('id')->on('discount_codes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('month_id')->references('id')->on('months')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
            
            // منع تكرار نفس الكورس في نفس الكود
            $table->unique(['discount_code_id', 'month_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_code_months');
    }
};

