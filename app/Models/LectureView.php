<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LectureView extends Model
{
    use HasFactory;

    protected $fillable = [
        'lecture_id',
        'student_id',
        'watch_percentage',
        'watch_duration',
        'completed',
        'last_position',
        'completed_at',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
        'completed_at' => 'datetime',
        'completed' => 'boolean',
        'watch_percentage' => 'decimal:2',
    ];

    /**
     * علاقة المشاهدة بالمحاضرة
     */
    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }

    /**
     * علاقة المشاهدة بالطالب
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}

