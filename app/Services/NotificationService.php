<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create a notification for admin users
     * Creates a single notification that all admins can see
     */
    public static function createNotification($type, $title, $message, $icon = null, $color = 'info', $relatedId = null, $relatedType = null, $url = null)
    {
        // Create a single notification for all admins (not tied to specific user)
        // Use null for notifiable to make it global for all admins
        Notification::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'color' => $color,
            'notifiable_type' => null, // Global notification for all admins
            'notifiable_id' => null,
            'related_id' => $relatedId,
            'related_type' => $relatedType,
            'url' => $url, // URL to open when clicking the notification
            'is_read' => false,
        ]);
    }

    /**
     * Notify when a new student registers
     */
    public static function notifyStudentRegistered($student)
    {
        $studentName = $student->first_name . ' ' . $student->second_name . ' ' . $student->third_name . ' ' . $student->forth_name;
        
        // URL to student profile
        $url = url('student-profile/' . $student->id);
        
        self::createNotification(
            'student_registered',
            'طالب جديد',
            "تم تسجيل طالب جديد: {$studentName} ({$student->grade})",
            'fa-user-plus',
            'success',
            $student->id,
            \App\Models\Student::class,
            $url
        );
    }

    /**
     * Notify when a subscription is added
     */
    public static function notifySubscriptionAdded($subscription)
    {
        $student = $subscription->student;
        $month = $subscription->month;
        $studentName = $student->first_name . ' ' . $student->second_name;
        
        self::createNotification(
            'subscription_added',
            'اشتراك جديد',
            "تم إضافة اشتراك جديد للطالب {$studentName} في {$month->name}",
            'fa-user-graduate',
            'info',
            $subscription->id,
            \App\Models\StudentSubscriptions::class
        );
    }

    /**
     * Create a notification for a specific student
     */
    public static function createStudentNotification($student, $type, $title, $message, $icon = null, $color = 'info', $relatedId = null, $relatedType = null, $url = null)
    {
        Notification::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'color' => $color,
            'notifiable_type' => \App\Models\Student::class,
            'notifiable_id' => $student->id,
            'related_id' => $relatedId,
            'related_type' => $relatedType,
            'url' => $url,
            'is_read' => false,
        ]);
    }

    /**
     * Notify students when a new lecture is added
     */
    public static function notifyNewLecture($lecture)
    {
        $month = $lecture->month;
        if (!$month) return;

        // Get all students subscribed to this course
        $subscribedStudents = \App\Models\StudentSubscriptions::where('month_id', $month->id)
            ->where('is_active', 1)
            ->with('student')
            ->get();

        foreach ($subscribedStudents as $subscription) {
            $student = $subscription->student;
            if (!$student) continue;

            $url = route('course_details', $month->id);
            
            self::createStudentNotification(
                $student,
                'new_lecture',
                'محاضرة جديدة',
                "تم إضافة محاضرة جديدة: {$lecture->title} في كورس {$month->name}",
                'fa-video',
                'primary',
                $lecture->id,
                \App\Models\Lecture::class,
                $url
            );
        }
    }

    /**
     * Notify students when a new exam is added
     */
    public static function notifyNewExam($exam)
    {
        $month = $exam->month;
        if (!$month) return;

        // Get all students subscribed to this course
        $subscribedStudents = \App\Models\StudentSubscriptions::where('month_id', $month->id)
            ->where('is_active', 1)
            ->with('student')
            ->get();

        foreach ($subscribedStudents as $subscription) {
            $student = $subscription->student;
            if (!$student) continue;

            $url = route('course_details', $month->id);
            
            self::createStudentNotification(
                $student,
                'new_exam',
                'امتحان جديد',
                "تم إضافة امتحان جديد: {$exam->exam_title} في كورس {$month->name}",
                'fa-file-alt',
                'warning',
                $exam->id,
                \App\Models\ExamName::class,
                $url
            );
        }
    }

    /**
     * Notify students when a new PDF/note is added
     */
    public static function notifyNewPdf($pdf)
    {
        $month = $pdf->month;
        if (!$month) return;

        // Get all students subscribed to this course
        $subscribedStudents = \App\Models\StudentSubscriptions::where('month_id', $month->id)
            ->where('is_active', 1)
            ->with('student')
            ->get();

        foreach ($subscribedStudents as $subscription) {
            $student = $subscription->student;
            if (!$student) continue;

            $url = route('course_details', $month->id);
            
            self::createStudentNotification(
                $student,
                'new_pdf',
                'مذكرة جديدة',
                "تم إضافة مذكرة جديدة: {$pdf->title} في كورس {$month->name}",
                'fa-file-pdf',
                'danger',
                $pdf->id,
                \App\Models\Pdf::class,
                $url
            );
        }
    }

    /**
     * Notify students when a new course is added
     */
    public static function notifyNewCourse($month)
    {
        // Get all students with the same grade
        $students = \App\Models\Student::where('grade', $month->grade)
            ->where('is_blocked', 0)
            ->get();

        foreach ($students as $student) {
            $url = route('course_details', $month->id);
            
            self::createStudentNotification(
                $student,
                'new_course',
                'كورس جديد',
                "تم إضافة كورس جديد: {$month->name}",
                'fa-book',
                'success',
                $month->id,
                \App\Models\Month::class,
                $url
            );
        }
    }

    /**
     * Notify student when their course subscription is activated
     */
    public static function notifyCourseActivated($subscription)
    {
        // تحميل العلاقات إذا لم تكن محملة
        if (!$subscription->relationLoaded('student')) {
            $subscription->load('student');
        }
        if (!$subscription->relationLoaded('month')) {
            $subscription->load('month');
        }
        
        $student = $subscription->student;
        $month = $subscription->month;
        
        if (!$student || !$month) {
            \Log::warning('Cannot send course activation notification: missing student or month', [
                'subscription_id' => $subscription->id,
                'student_id' => $subscription->student_id,
                'month_id' => $subscription->month_id,
                'has_student' => !is_null($student),
                'has_month' => !is_null($month)
            ]);
            return;
        }

        $url = route('course_details', $month->id);
        
        try {
            self::createStudentNotification(
                $student,
                'course_activated',
                'تم تفعيل الكورس',
                "تم تفعيل اشتراكك في كورس: {$month->name}",
                'fa-check-circle',
                'success',
                $subscription->id,
                \App\Models\StudentSubscriptions::class,
                $url
            );
            
            \Log::info('Course activation notification sent', [
                'subscription_id' => $subscription->id,
                'student_id' => $student->id,
                'month_id' => $month->id
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating course activation notification', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Notify student when their exam grade becomes visible
     */
    public static function notifyExamGradeVisible($exam_result)
    {
        // Load relationships if not loaded
        if (!$exam_result->relationLoaded('student')) {
            $exam_result->load('student');
        }
        if (!$exam_result->relationLoaded('exam')) {
            $exam_result->load('exam');
        }
        
        $student = $exam_result->student;
        $exam = $exam_result->exam;
        
        if (!$student || !$exam) {
            \Log::warning('Cannot send exam grade notification: missing student or exam', [
                'exam_result_id' => $exam_result->id,
                'student_id' => $exam_result->student_id,
                'exam_id' => $exam_result->exam_id,
                'has_student' => !is_null($student),
                'has_exam' => !is_null($exam)
            ]);
            return;
        }

        $url = route('exams'); // Link to student's exam results page
        
        try {
            self::createStudentNotification(
                $student,
                'exam_grade_visible',
                'درجة الامتحان متاحة',
                "تم إظهار درجتك في امتحان: {$exam->exam_title} ({$exam_result->degree})",
                'fa-certificate',
                'info',
                $exam_result->id,
                \App\Models\ExamResult::class,
                $url
            );
            
            \Log::info('Exam grade notification sent', [
                'exam_result_id' => $exam_result->id,
                'student_id' => $student->id,
                'exam_id' => $exam->id,
                'degree' => $exam_result->degree
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating exam grade notification', [
                'exam_result_id' => $exam_result->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}

