<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إضافة حقل session_id للطلاب لتتبع الجلسات
     */
    public function up(): void
    {
        // إضافة session_id إذا لم يكن موجوداً
        if (!Schema::hasColumn('students', 'session_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('session_id')->nullable()->after('image');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('students', 'session_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('session_id');
            });
        }
    }
};

