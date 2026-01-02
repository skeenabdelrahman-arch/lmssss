<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'second_name',
        'third_name',
        'forth_name',
        'email',
        'student_phone',
        'parent_phone',
        'city',
        'gender',
        'grade',
        'register',
        'student_code',
        'password',
        'image',
        'is_blocked',
        'blocked_until',
        'failed_login_attempts',
        'last_failed_login_at',
        'session_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'password',
        // 'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime',
        // 'password' => 'hashed',
        'is_blocked' => 'boolean',
        'has_all_access' => 'boolean',
        'blocked_until' => 'datetime',
        'last_failed_login_at' => 'datetime',
    ];
    
    /**
     * Check if student is currently blocked
     */
    public function isCurrentlyBlocked()
    {
        if (!$this->is_blocked) {
            return false;
        }
        
        if ($this->blocked_until && $this->blocked_until->isFuture()) {
            return true;
        }
        
        // If blocked_until is past, unblock the student
        if ($this->blocked_until && $this->blocked_until->isPast()) {
            $this->is_blocked = false;
            $this->blocked_until = null;
            $this->failed_login_attempts = 0;
            $this->save();
            return false;
        }
        
        return $this->is_blocked;
    }

    /**
     * تعريف العلاقة بين الطالب ونتائج الامتحانات
     * الطالب له العديد من نتائج الامتحانات
     */
    public function examResults()
    {
        return $this->hasMany(ExamResult::class, 'student_id');
    }

    /**
     * تعريف العلاقة بين الطالب والاشتراكات
     * الطالب له العديد من الاشتراكات
     */
    public function subscriptions()
    {
        return $this->hasMany(StudentSubscriptions::class, 'student_id');
    }

    /**
     * علاقة الطالب بقيود المحاضرات
     */
    public function restrictions()
    {
        return $this->hasMany(LectureRestriction::class);
    }

    /**
     * Check if the student can access a given month.
     * Access is granted when:
     * - Student has an active subscription to the month, or
     * - Student has all-access flag enabled.
     */
    public function canAccessMonth($monthId): bool
    {
        // All-access shortcut (attribute from Excel import)
        // Use Eloquent attribute access; property_exists() does not detect DB attributes
        if ((bool) $this->getAttribute('has_all_access') === true) {
            return true;
        }

        // Blocked students cannot access
        if ($this->isCurrentlyBlocked()) {
            return false;
        }

        // Active subscription for the month
        return $this->subscriptions()
            ->where('month_id', $monthId)
            ->where('is_active', true)
            ->exists();
    }
    /**
     * Get attendance records for a student
     */
    public function attendanceRecords()
    {
        return $this->hasMany(\App\Models\StudentAttendance::class, 'student_id');
    }

    /**
     * Get payment records for a student
     */
    public function paymentRecords()
    {
        return $this->hasMany(\App\Models\StudentPaymentRecord::class, 'student_id');
    }

    /**
     * Get tasks for a student
     */
    public function tasks()
    {
        return $this->hasMany(\App\Models\StudentTask::class, 'student_id');
    }

    /**
     * Get lecture views for a student
     */
    public function lectureViews()
    {
        return $this->hasMany(\App\Models\LectureView::class, 'student_id');
    }

    /**
     * Get parent portal data for report
     */
    public function getParentPortalReport()
    {
        return [
            'subscriptions' => $this->subscriptions()->with('month')->get(),
            'exam_results' => $this->examResults()->with('exam')->get(),
            'attendance' => $this->attendanceRecords()->orderBy('attendance_date', 'desc')->get(),
            'payments' => $this->paymentRecords()->with('month')->orderBy('payment_date', 'desc')->get(),
            'tasks' => $this->tasks()->orderBy('due_date', 'desc')->get(),
            'lecture_views' => $this->lectureViews()->with('lecture')->orderBy('created_at', 'desc')->limit(20)->get(),
        ];
    }
}
