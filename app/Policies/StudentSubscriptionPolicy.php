<?php

namespace App\Policies;

use App\Models\StudentSubscriptions;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentSubscriptionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view the subscription.
     * الطالب يمكنه فقط رؤية اشتراكاته
     * الأدمن يمكنه رؤية جميع الاشتراكات
     */
    public function view($user, StudentSubscriptions $subscription)
    {
        // إذا كان المستخدم طالب، يجب أن يكون الاشتراك خاص به
        if (auth()->guard('student')->check()) {
            return auth()->guard('student')->id() === $subscription->student_id;
        }
        
        // إذا كان المستخدم أدمن
        if (auth()->guard('web')->check()) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine if the user can access month content.
     * التحقق من أن الطالب مشترك في الشهر
     * هذه دالة مساعدة يمكن استدعاؤها مباشرة
     */
    public static function canAccessMonth($monthId)
    {
        // إذا كان المستخدم أدمن
        if (auth()->guard('web')->check()) {
            return true;
        }
        
        // إذا كان المستخدم طالب
        if (auth()->guard('student')->check()) {
            $student = auth()->guard('student')->user();
            $studentId = $student->id;
            $month = \App\Models\Month::find($monthId);

            // إذا كان الطالب لديه صلاحية الوصول لجميع الكورسات
            $studentHasAllAccess = isset($student->has_all_access) && (bool)$student->has_all_access;

            if ($studentHasAllAccess) {
                return true;
            }

            // إذا كان السعر 0، الكورس مفتوح للجميع
            $monthIsFree = $month && ((float)$month->price == 0);
            
            if ($monthIsFree) {
                return true;
            }

            // التحقق من الاشتراك النشط
            $hasSubscription = \App\Models\StudentSubscriptions::where('student_id', $studentId)
                ->where('month_id', $monthId)
                ->where('is_active', 1)
                ->exists();

            if ($hasSubscription) {
                return true;
            }

            // التحقق من وجود كود تفعيل مستخدم
            $hasActivationCode = \App\Models\ActivationCode::where('student_id', $studentId)
                ->where('month_id', $monthId)
                ->whereNotNull('used_at')
                ->exists();
            
            if ($hasActivationCode) {
                return true;
            }

            return false;
        }
        
        return false;
    }

    /**
     * الحصول على سبب إلغاء التفعيل إذا وجد
     */
    public static function getDeactivationReason($monthId)
    {
        if (auth()->guard('student')->check()) {
            $student = auth()->guard('student')->user();
            $subscription = \App\Models\StudentSubscriptions::where('student_id', $student->id)
                ->where('month_id', $monthId)
                ->where('is_active', 0)
                ->first();
            
            return $subscription ? $subscription->deactivation_reason : null;
        }
        
        return null;
    }

    /**
     * Determine if the user can view any subscriptions.
     * فقط الأدمن يمكنه رؤية قائمة الاشتراكات
     */
    public function viewAny($user)
    {
        return auth()->guard('web')->check() && $user instanceof \App\Models\User;
    }
}

