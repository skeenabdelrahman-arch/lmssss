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
        if (!Schema::hasTable('lecture_views')) {
            // إنشاء الجدول لو مش موجود
            Schema::create('lecture_views', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lecture_id')->constrained('lectures')->onDelete('cascade');
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->timestamp('viewed_at')->useCurrent();
                $table->timestamps();

                // منع تكرار المشاهدة لنفس الطالب لنفس المحاضرة
                $table->unique(['lecture_id', 'student_id']);
            });
        } else {
            // الجدول موجود بالفعل، ممكن تضيف أعمدة جديدة لو محتاج
            Schema::table('lecture_views', function (Blueprint $table) {
                if (!Schema::hasColumn('lecture_views', 'viewed_at')) {
                    $table->timestamp('viewed_at')->useCurrent()->after('student_id');
                }
                // لو هتحب تضيف أعمدة جديدة مستقبلًا، ضيفها هنا مع hasColumn
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecture_views');
    }
};
