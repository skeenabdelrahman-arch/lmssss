<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exam_id',
        'student_id',
        'degree',
        'show_degree',
        'is_marked',
        'started_at',
        'completed_at',
        'time_elapsed',
        'assigned_model',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function exam()
    {
        return $this->belongsTo(ExamName::class, 'exam_id');
    }

    /**
     * Get formatted time spent on exam
     */
    public function getTimeSpentFormattedAttribute()
    {
        if (!$this->started_at || !$this->completed_at) {
            return 'غير متاح';
        }

        $minutes = $this->started_at->diffInMinutes($this->completed_at);
        
        if ($minutes < 60) {
            return $minutes . ' دقيقة';
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($remainingMinutes == 0) {
            return $hours . ' ساعة';
        }
        
        return $hours . ' ساعة و ' . $remainingMinutes . ' دقيقة';
    }
}
