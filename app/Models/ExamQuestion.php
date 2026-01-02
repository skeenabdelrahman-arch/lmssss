<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exam_id',
        'model_name',
        'question_title',
        'question_type', // multiple_choice, true_false
        'question_title_formatted',
        'question_font_family',
        'question_font_size',
        'question_text_color',
        'ch_1',
        'ch_1_img',
        'ch_1_formatted',
        'ch_2',
        'ch_2_img',
        'ch_2_formatted',
        'ch_3',
        'ch_3_img',
        'ch_3_formatted',
        'ch_4',
        'ch_4_img',
        'ch_4_formatted',
        'img',
        'correct_answer',
        'correct_answers',
        'is_bonus',
        'Q_degree',
    ];

    protected $casts = [
        'correct_answers' => 'array',
        'is_bonus' => 'boolean',
    ];
}
