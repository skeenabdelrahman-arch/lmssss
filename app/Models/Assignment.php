<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'month_id',
        'lecture_id',
        'file_path',
        'total_marks',
        'deadline',
        'status',
        'display_order',
        'auto_grade_all',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'total_marks' => 'integer',
        'display_order' => 'integer',
    ];

    // Relationships
    public function month()
    {
        return $this->belongsTo(Month::class);
    }

    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function questions()
    {
        return $this->hasMany(AssignmentQuestion::class)->orderBy('display_order');
    }

    // Helper Methods
    public function isOverdue()
    {
        return $this->deadline && $this->deadline->isPast();
    }

    public function submittedCount()
    {
        return $this->submissions()->count();
    }

    public function gradedCount()
    {
        return $this->submissions()->where('status', 'graded')->count();
    }

    public function averageMarks()
    {
        return $this->submissions()->where('status', 'graded')->avg('marks');
    }

    public function getSubmissionForStudent($studentId)
    {
        return $this->submissions()->where('student_id', $studentId)->first();
    }
}
