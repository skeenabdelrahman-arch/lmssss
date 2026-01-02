<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول exam_results
        if (Schema::hasTable('exam_results')) {
            Schema::table('exam_results', function (Blueprint $table) {
                if (!Schema::hasColumn('exam_results', 'time_elapsed')) {
                    $table->integer('time_elapsed')->default(0)->after('completed_at')->comment('الوقت المستغرق بالثواني');
                }
                if (!Schema::hasColumn('exam_results', 'assigned_model')) {
                    $table->string('assigned_model')->default('A')->after('exam_id');
                }
            });
        }

        // جدول exam_names
        if (Schema::hasTable('exam_names')) {
            Schema::table('exam_names', function (Blueprint $table) {
                if (!Schema::hasColumn('exam_names', 'randomize_questions')) {
                    $table->boolean('randomize_questions')->default(false)->after('auto_show_results');
                }
            });
        }

        // جدول exam_questions
        if (Schema::hasTable('exam_questions')) {
            Schema::table('exam_questions', function (Blueprint $table) {
                if (!Schema::hasColumn('exam_questions', 'model_name')) {
                    $table->string('model_name')->default('A')->after('exam_id')->comment('اسم النموذج A, B, C');
                    $table->index(['exam_id', 'model_name']);
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('exam_results')) {
            Schema::table('exam_results', function (Blueprint $table) {
                if (Schema::hasColumn('exam_results', 'time_elapsed')) {
                    $table->dropColumn('time_elapsed');
                }
                if (Schema::hasColumn('exam_results', 'assigned_model')) {
                    $table->dropColumn('assigned_model');
                }
            });
        }

        if (Schema::hasTable('exam_names')) {
            Schema::table('exam_names', function (Blueprint $table) {
                if (Schema::hasColumn('exam_names', 'randomize_questions')) {
                    $table->dropColumn('randomize_questions');
                }
            });
        }

        if (Schema::hasTable('exam_questions')) {
            Schema::table('exam_questions', function (Blueprint $table) {
                if (Schema::hasColumn('exam_questions', 'model_name')) {
                    $table->dropColumn('model_name');
                }
            });
        }
    }
};
