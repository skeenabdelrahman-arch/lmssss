<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentAttendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'attendance_date',
        'is_present',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'is_present' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get attendance statistics for a student
     */
    public static function getAttendanceStats($studentId)
    {
        $total = self::where('student_id', $studentId)->count();
        $present = self::where('student_id', $studentId)->where('is_present', true)->count();
        $absent = $total - $present;
        $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'percentage' => $percentage,
        ];
    }
}
