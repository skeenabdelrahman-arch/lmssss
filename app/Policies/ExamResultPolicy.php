<?php

namespace App\Policies;

use App\Models\ExamResult;
use App\Models\Student;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamResultPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view the exam result.
     * الطالب يمكنه فقط رؤية نتائج امتحاناته
     * الأدمن يمكنه رؤية جميع النتائج
     */
    public function view($user, ExamResult $examResult)
    {
        // إذا كان المستخدم أدمن (User model) فنعطيه صلاحية العرض أولاً
        if (auth()->guard('web')->check() && $user instanceof \App\Models\User) {
            return true;
        }

        // إذا كان المستخدم طالب، يجب أن تكون النتيجة خاصة به
        // و/أو يجب أن يسمح عرض الدرجة (`show_degree` = 1)
        if (auth()->guard('student')->check()) {
            $studentId = auth()->guard('student')->id();
            return $studentId === $examResult->student_id && (int)$examResult->show_degree === 1;
        }

        return false;
    }

    /**
     * Determine if the user can update the exam result.
     * فقط الأدمن يمكنه تحديث النتائج
     */
    public function update($user, ExamResult $examResult)
    {
        return auth()->guard('web')->check() && $user instanceof \App\Models\User;
    }

    /**
     * Determine if the user can delete the exam result.
     * فقط الأدمن يمكنه حذف النتائج
     */
    public function delete($user, ExamResult $examResult)
    {
        return auth()->guard('web')->check() && $user instanceof \App\Models\User;
    }

    /**
     * Determine if the user can view any exam results.
     * فقط الأدمن يمكنه رؤية قائمة النتائج
     */
    public function viewAny($user)
    {
        return auth()->guard('web')->check() && $user instanceof \App\Models\User;
    }
}

