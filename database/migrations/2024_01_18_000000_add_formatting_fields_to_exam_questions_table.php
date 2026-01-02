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
        Schema::table('exam_questions', function (Blueprint $table) {
            // حقول التنسيق للسؤال
            $table->text('question_title_formatted')->nullable()->after('question_title'); // HTML formatted
            $table->string('question_font_family')->nullable()->after('question_title_formatted');
            $table->string('question_font_size')->nullable()->after('question_font_family');
            $table->string('question_text_color')->nullable()->after('question_font_size');
            
            // حقول الإجابات كصور
            $table->string('ch_1_img')->nullable()->after('ch_1');
            $table->string('ch_2_img')->nullable()->after('ch_2');
            $table->string('ch_3_img')->nullable()->after('ch_3');
            $table->string('ch_4_img')->nullable()->after('ch_4');
            
            // حقول التنسيق للإجابات (اختياري)
            $table->text('ch_1_formatted')->nullable()->after('ch_1_img');
            $table->text('ch_2_formatted')->nullable()->after('ch_2_img');
            $table->text('ch_3_formatted')->nullable()->after('ch_3_img');
            $table->text('ch_4_formatted')->nullable()->after('ch_4_img');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_questions', function (Blueprint $table) {
            $table->dropColumn([
                'question_title_formatted',
                'question_font_family',
                'question_font_size',
                'question_text_color',
                'ch_1_img',
                'ch_2_img',
                'ch_3_img',
                'ch_4_img',
                'ch_1_formatted',
                'ch_2_formatted',
                'ch_3_formatted',
                'ch_4_formatted'
            ]);
        });
    }
};
