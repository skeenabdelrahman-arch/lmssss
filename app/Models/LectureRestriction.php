<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LectureRestriction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'lecture_id',
        'reason',
    ];

    /**
     * العلاقة مع الطالب
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * العلاقة مع المحاضرة
     */
    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }

    /**
     * التحقق من وجود قيد على طالب لمحاضرة معينة
     */
    public static function isRestricted($studentId, $lectureId)
    {
        return self::where('student_id', $studentId)
            ->where('lecture_id', $lectureId)
            ->exists();
    }

    /**
     * إضافة قيد على طالب لمحاضرة
     */
    public static function addRestriction($studentId, $lectureId, $reason = null)
    {
        return self::firstOrCreate(
            [
                'student_id' => $studentId,
                'lecture_id' => $lectureId,
            ],
            [
                'reason' => $reason,
            ]
        );
    }

    /**
     * إزالة قيد من طالب لمحاضرة
     */
    public static function removeRestriction($studentId, $lectureId)
    {
        return self::where('student_id', $studentId)
            ->where('lecture_id', $lectureId)
            ->delete();
    }

    /**
     * الحصول على جميع المحاضرات المحظورة لطالب
     */
    public static function getStudentRestrictions($studentId)
    {
        return self::where('student_id', $studentId)
            ->with('lecture')
            ->get();
    }
}
