<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'type',
        'question_text',
        'max_marks',
        'is_required',
        'auto_grade',
        'allow_text',
        'allow_file',
        'display_order',
    ];

    protected $casts = [
        'max_marks' => 'decimal:2',
        'is_required' => 'boolean',
        'auto_grade' => 'boolean',
        'allow_text' => 'boolean',
        'allow_file' => 'boolean',
        'display_order' => 'integer',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function options()
    {
        return $this->hasMany(AssignmentQuestionOption::class, 'question_id')->orderBy('display_order');
    }

    public function answers()
    {
        return $this->hasMany(AssignmentQuestionAnswer::class, 'question_id');
    }

    public function correctOptionIds(): array
    {
        return $this->options()->where('is_correct', true)->pluck('id')->all();
    }
}
