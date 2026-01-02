<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view the student profile.
     * الطالب يمكنه فقط رؤية بروفايله الخاص
     * الأدمن يمكنه رؤية جميع البروفايلات
     */
    public function view($user, Student $student)
    {
        // إذا كان المستخدم طالب، يجب أن يكون هو نفسه
        if (auth()->guard('student')->check()) {
            return auth()->guard('student')->id() === $student->id;
        }
        
        // إذا كان المستخدم أدمن (User model)
        if (auth()->guard('web')->check() && $user instanceof \App\Models\User) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine if the user can update the student.
     * الطالب يمكنه فقط تحديث بياناته الخاصة
     * الأدمن يمكنه تحديث أي طالب
     */
    public function update($user, Student $student)
    {
        // إذا كان المستخدم طالب، يجب أن يكون هو نفسه
        if (auth()->guard('student')->check()) {
            return auth()->guard('student')->id() === $student->id;
        }
        
        // إذا كان المستخدم أدمن (User model)
        if (auth()->guard('web')->check() && $user instanceof \App\Models\User) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine if the user can delete the student.
     * فقط الأدمن يمكنه حذف الطلاب
     */
    public function delete($user, Student $student)
    {
        return auth()->guard('web')->check() && $user instanceof \App\Models\User;
    }

    /**
     * Determine if the user can view any students.
     * فقط الأدمن يمكنه رؤية قائمة الطلاب
     */
    public function viewAny($user)
    {
        return auth()->guard('web')->check() && $user instanceof \App\Models\User;
    }
}

