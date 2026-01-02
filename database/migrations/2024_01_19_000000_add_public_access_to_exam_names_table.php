<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_names', function (Blueprint $table) {
            $table->boolean('public_access')->default(0)->after('status'); // 0 = غير عام، 1 = عام
        });
    }

    public function down(): void
    {
        Schema::table('exam_names', function (Blueprint $table) {
            $table->dropColumn('public_access');
        });
    }
};
