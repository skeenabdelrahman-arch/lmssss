<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->nullable(); // اسم الكود (مثل: خصم الصيف)
            $table->enum('type', ['percentage', 'fixed'])->default('percentage'); // نسبة أو مبلغ ثابت
            $table->decimal('value', 10, 2); // قيمة الخصم
            $table->decimal('min_amount', 10, 2)->nullable(); // الحد الأدنى للطلب
            $table->integer('max_uses')->nullable(); // عدد الاستخدامات الأقصى
            $table->integer('used_count')->default(0); // عدد الاستخدامات الحالية
            $table->dateTime('starts_at')->nullable(); // تاريخ البدء
            $table->dateTime('expires_at')->nullable(); // تاريخ الانتهاء
            $table->boolean('is_active')->default(1);
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_codes');
    }
};




