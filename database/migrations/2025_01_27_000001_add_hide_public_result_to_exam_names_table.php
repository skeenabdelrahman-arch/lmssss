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
        Schema::table('exam_names', function (Blueprint $table) {
            $table->boolean('hide_public_result')->default(0)->after('public_access');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_names', function (Blueprint $table) {
            $table->dropColumn('hide_public_result');
        });
    }
};

