<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamAnswer extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'student_id', 'exam_id', 'question_id', 'student_answer',
    ];
}
