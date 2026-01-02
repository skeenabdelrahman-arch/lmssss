<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'notes',
        'file_path',
        'submitted_at',
        'marks',
        'feedback',
        'graded_at',
        'status',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'marks' => 'decimal:2',
    ];

    public function answers()
    {
        return $this->hasMany(AssignmentQuestionAnswer::class, 'submission_id');
    }

    // Relationships
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Helper Methods
    public function isLate()
    {
        if (!$this->assignment->deadline) {
            return false;
        }
        return $this->submitted_at->isAfter($this->assignment->deadline);
    }

    public function getPercentage()
    {
        if (!$this->marks || !$this->assignment->total_marks) {
            return 0;
        }
        return round(($this->marks / $this->assignment->total_marks) * 100, 2);
    }

    public function isPassed()
    {
        return $this->getPercentage() >= 50;
    }
}
