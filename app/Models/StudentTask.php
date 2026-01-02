<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'title',
        'description',
        'task_type', // homework, exam, etc
        'due_date',
        'status', // pending, completed, overdue
        'grade',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get task statistics for a student
     */
    public static function getTaskStats($studentId)
    {
        $total = self::where('student_id', $studentId)->count();
        $completed = self::where('student_id', $studentId)->where('status', 'completed')->count();
        $pending = self::where('student_id', $studentId)->where('status', 'pending')->count();
        $overdue = self::where('student_id', $studentId)->where('status', 'overdue')->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $pending,
            'overdue' => $overdue,
        ];
    }
}
