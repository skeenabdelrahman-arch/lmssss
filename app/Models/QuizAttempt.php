<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'student_id',
        'answers',
        'score',
        'total_score',
        'percentage',
        'is_passed',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'is_passed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * علاقة المحاولة بالكويز
     */
    public function quiz()
    {
        return $this->belongsTo(LectureQuiz::class, 'quiz_id');
    }

    /**
     * علاقة المحاولة بالطالب
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}

