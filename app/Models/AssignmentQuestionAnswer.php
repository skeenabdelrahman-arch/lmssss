<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentQuestionAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'question_id',
        'answer_text',
        'selected_options',
        'attachment_path',
        'awarded_marks',
        'status',
    ];

    protected $casts = [
        'selected_options' => 'array',
        'awarded_marks' => 'decimal:2',
    ];

    public function submission()
    {
        return $this->belongsTo(AssignmentSubmission::class, 'submission_id');
    }

    public function question()
    {
        return $this->belongsTo(AssignmentQuestion::class, 'question_id');
    }
}
