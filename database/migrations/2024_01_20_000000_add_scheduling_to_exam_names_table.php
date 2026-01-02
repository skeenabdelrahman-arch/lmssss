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
        if (!Schema::hasTable('exam_names')) {
            // إنشاء الجدول لو مش موجود (ممكن تضيف الأعمدة الأساسية حسب المشروع)
            Schema::create('exam_names', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->boolean('public_access')->default(false);
                $table->timestamps();
            });

            // إضافة الأعمدة الجديدة
            Schema::table('exam_names', function (Blueprint $table) {
                $table->timestamp('opens_at')->nullable()->after('public_access');
                $table->timestamp('closes_at')->nullable()->after('opens_at');
                $table->boolean('auto_show_results')->default(false)->after('closes_at');
            });
        } else {
            Schema::table('exam_names', function (Blueprint $table) {
                if (!Schema::hasColumn('exam_names', 'opens_at')) {
                    $table->timestamp('opens_at')->nullable()->after('public_access');
                }
                if (!Schema::hasColumn('exam_names', 'closes_at')) {
                    $table->timestamp('closes_at')->nullable()->after('opens_at');
                }
                if (!Schema::hasColumn('exam_names', 'auto_show_results')) {
                    $table->boolean('auto_show_results')->default(false)->after('closes_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_names', function (Blueprint $table) {
            if (Schema::hasColumn('exam_names', 'opens_at')) {
                $table->dropColumn('opens_at');
            }
            if (Schema::hasColumn('exam_names', 'closes_at')) {
                $table->dropColumn('closes_at');
            }
            if (Schema::hasColumn('exam_names', 'auto_show_results')) {
                $table->dropColumn('auto_show_results');
            }
        });
    }
};
