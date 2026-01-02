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
        if (Schema::hasTable('activation_codes')) {
            Schema::table('activation_codes', function (Blueprint $table) {
                // تعديل month_id لو مش nullable بالفعل
                if (Schema::hasColumn('activation_codes', 'month_id')) {
                    $table->foreignId('month_id')->nullable()->change();
                }

                // إضافة bundle_id لو مش موجود
                if (!Schema::hasColumn('activation_codes', 'bundle_id')) {
                    $table->foreignId('bundle_id')->nullable()->after('month_id')
                        ->references('id')->on('discount_codes')->cascadeOnDelete();

                    $table->index(['bundle_id', 'is_active']);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('activation_codes')) {
            Schema::table('activation_codes', function (Blueprint $table) {
                if (Schema::hasColumn('activation_codes', 'bundle_id')) {
                    $table->dropForeign(['bundle_id']);
                    $table->dropColumn('bundle_id');
                }

                // إعادة month_id كـ required إذا موجود
                if (Schema::hasColumn('activation_codes', 'month_id')) {
                    $table->foreignId('month_id')->nullable(false)->change();
                }
            });
        }
    }
};
