<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة حقل remember_token للطلاب
     */
    public function up(): void
    {
        // إضافة remember_token إذا لم يكن موجوداً
        if (!Schema::hasColumn('students', 'remember_token')) {
            Schema::table('students', function (Blueprint $table) {
                $table->rememberToken()->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('students', 'remember_token')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropRememberToken();
            });
        }
    }
};








