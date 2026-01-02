<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LectureQuiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'lecture_id',
        'title',
        'description',
        'is_required',
        'is_active',
        'exclude_excel_students',
        'passing_score',
        'time_limit',
        'order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'exclude_excel_students' => 'boolean',
    ];

    /**
     * علاقة الكويز بالمحاضرة
     */
    public function lecture()
    {
        return $this->belongsTo(Lecture::class, 'lecture_id');
    }

    /**
     * علاقة الكويز بالأسئلة
     */
    public function questions()
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id')->orderBy('order');
    }

    /**
     * علاقة الكويز بمحاولات الطلاب
     */
    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id');
    }

    /**
     * الحصول على محاولة طالب محدد
     */
    public function getStudentAttempt($studentId)
    {
        return $this->attempts()->where('student_id', $studentId)->latest()->first();
    }

    /**
     * التحقق من نجاح طالب في الكويز
     */
    public function hasStudentPassed($studentId)
    {
        $attempt = $this->getStudentAttempt($studentId);
        return $attempt && $attempt->is_passed;
    }

    /**
     * التحقق من إمكانية الطالب الوصول للكويز
     */
    public function canStudentAccess($student)
    {
        // إذا كان الكويز معطل
        if (!$this->is_active) {
            return false;
        }

        // الآن جميع الطلاب يمكنهم رؤية الكويز
        // (exclude_excel_students فقط يلغي الإجبارية، لا يمنع الوصول)
        return true;
    }

    /**
     * التحقق من إذا كان الكويز إجباري على طالب معين
     */
    public function isRequiredForStudent($student)
    {
        // إذا الكويز مش إجباري أصلاً
        if (!$this->is_required) {
            return false;
        }

        // إذا كان الطالب مستورد من Excel والكويز يستثنيهم من الإجبارية
        if ($this->exclude_excel_students && $student->register === 'excel_import') {
            return false;
        }

        // الكويز إجباري على باقي الطلاب
        return true;
    }
}

