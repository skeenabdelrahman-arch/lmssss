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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('month_id')->constrained('months')->onDelete('cascade');
            $table->string('kashier_order_id')->unique()->nullable();
            $table->string('payment_id')->nullable(); // Payment ID from Kashier
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('EGP');
            $table->enum('status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending');
            $table->string('payment_method')->nullable(); // card, wallet, etc.
            $table->text('kashier_response')->nullable(); // Full response from Kashier
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->index('student_id');
            $table->index('month_id');
            $table->index('status');
            $table->index('kashier_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

