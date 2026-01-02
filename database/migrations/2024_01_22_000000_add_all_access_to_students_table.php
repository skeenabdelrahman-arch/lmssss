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
        if (!Schema::hasTable('students')) {
            // إنشاء الجدول لو مش موجود (ممكن تضيف الأعمدة الأساسية هنا لو محتاج)
            Schema::create('students', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamps();
                // تضيف باقي الأعمدة الأساسية حسب المشروع
            });

            // إضافة العمود الجديد
            Schema::table('students', function (Blueprint $table) {
                $table->boolean('has_all_access')
                    ->default(false)
                    ->after('last_failed_login_at');
            });
        } else {
            // الجدول موجود بالفعل
            Schema::table('students', function (Blueprint $table) {
                if (!Schema::hasColumn('students', 'has_all_access')) {
                    $table->boolean('has_all_access')
                        ->default(false)
                        ->after('last_failed_login_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'has_all_access')) {
                $table->dropColumn('has_all_access');
            }
        });
    }
};
