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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // نوع الإشعار: student_registered, subscription_added, etc.
            $table->string('title'); // عنوان الإشعار
            $table->text('message'); // نص الإشعار
            $table->string('icon')->nullable(); // أيقونة الإشعار
            $table->string('color')->default('info'); // لون الإشعار: info, success, warning, danger
            $table->string('notifiable_type')->nullable(); // polymorphic relation للربط بأي model (nullable للإشعارات العامة)
            $table->unsignedBigInteger('notifiable_id')->nullable(); // ID للمستخدم (nullable للإشعارات العامة)
            $table->foreignId('related_id')->nullable(); // ID للعنصر المرتبط (مثل student_id)
            $table->string('related_type')->nullable(); // نوع العنصر المرتبط (مثل Student)
            $table->string('url')->nullable(); // رابط للانتقال عند الضغط على الإشعار
            $table->boolean('is_read')->default(false); // هل تم قراءة الإشعار
            $table->timestamp('read_at')->nullable(); // وقت قراءة الإشعار
            $table->timestamps();
            
            // Indexes
            $table->index('is_read');
            $table->index('type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

