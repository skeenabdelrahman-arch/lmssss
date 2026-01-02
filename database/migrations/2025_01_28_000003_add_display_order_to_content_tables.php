<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة حقل display_order للتحكم في ترتيب الظهور
     */
    public function up(): void
    {
        // إضافة display_order للمحاضرات
        Schema::table('lectures', function (Blueprint $table) {
            $table->integer('display_order')->default(0)->after('is_featured');
        });
        
        // إضافة display_order للامتحانات
        Schema::table('exam_names', function (Blueprint $table) {
            $table->integer('display_order')->default(0)->after('hide_public_result');
        });
        
        // إضافة display_order للأشهر
        Schema::table('months', function (Blueprint $table) {
            $table->integer('display_order')->default(0)->after('price');
        });
        
        // إضافة display_order للمذكرات
        Schema::table('pdfs', function (Blueprint $table) {
            $table->integer('display_order')->default(0)->after('status');
        });
        
        // تعيين display_order تلقائياً بناءً على id الحالي
        DB::statement('UPDATE lectures SET display_order = id');
        DB::statement('UPDATE exam_names SET display_order = id');
        DB::statement('UPDATE months SET display_order = id');
        DB::statement('UPDATE pdfs SET display_order = id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lectures', function (Blueprint $table) {
            $table->dropColumn('display_order');
        });
        
        Schema::table('exam_names', function (Blueprint $table) {
            $table->dropColumn('display_order');
        });
        
        Schema::table('months', function (Blueprint $table) {
            $table->dropColumn('display_order');
        });
        
        Schema::table('pdfs', function (Blueprint $table) {
            $table->dropColumn('display_order');
        });
    }
};

