<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentSubscriptions;
use App\Models\ExamResult;
use App\Models\StudentAttendance;
use App\Models\StudentPaymentRecord;
use App\Models\StudentTask;
use App\Models\LectureView;
use App\Models\Lecture;
use App\Models\ExamName;
use Illuminate\Http\Request;

class ParentPortalController extends Controller
{
    /**
     * Show parent portal search page
     */
    public function index()
    {
        return view('front.parent_portal.index');
    }

    /**
     * Search for student by parent ID and student ID
     */
    public function search(Request $request)
    {
        $request->validate([
            'parent_id' => 'required|string',
            'student_id' => 'required|string',
        ]);
        $studentPhoneRaw    = trim($request->student_id);
        $studentPhoneDigits = preg_replace('/\D+/', '', $studentPhoneRaw);
        $parentPhoneRaw     = trim($request->parent_id);
        $parentPhoneDigits  = preg_replace('/\D+/', '', $parentPhoneRaw);

        // البحث الأساسي برقم هاتف الطالب، مع السماح بالمطابقة الجزئية + الكود كخيار ثانوي
        $student = Student::where(function ($q) use ($studentPhoneRaw, $studentPhoneDigits) {
                $q->where('student_phone', $studentPhoneRaw)
                  ->orWhere('student_phone', 'like', "%{$studentPhoneDigits}")
                  ->orWhere('student_code', $studentPhoneRaw); // احتياطي إذا عُرف الكود
            })
            ->where(function ($q) use ($parentPhoneRaw, $parentPhoneDigits) {
                // تحقق من رقم ولي الأمر (أو رقم الطالب إذا تم تخزينه كولي الأمر)
                $q->where('parent_phone', $parentPhoneRaw)
                  ->orWhere('parent_phone', 'like', "%{$parentPhoneDigits}")
                  ->orWhere('student_phone', $parentPhoneRaw)
                  ->orWhere('student_phone', 'like', "%{$parentPhoneDigits}");
            })
            ->first();

        if (!$student) {
            return back()
                ->withInput()
                ->withErrors(['student_id' => 'لم يتم العثور على الطالب. تأكد من رقم الطالب ورقم ولي الأمر.']);
        }

        return redirect()->route('parent-portal.report', ['student' => $student->id]);
    }

    /**
     * Display comprehensive student report
     */
    public function report(Student $student)
    {
        // Get subscription months
        $subscriptions = StudentSubscriptions::where('student_id', $student->id)
            ->with('month')
            ->get();

        // Get exam results (exclude deleted exams)
        $examResults = ExamResult::where('student_id', $student->id)
            ->with(['exam' => function($query) {
                $query->whereNull('deleted_at');
            }, 'exam.questions'])
            ->whereHas('exam', function($query) {
                $query->whereNull('deleted_at');
            })
            ->orderBy('completed_at', 'desc')
            ->get();

        // Get attendance records
        $attendance = StudentAttendance::where('student_id', $student->id)
            ->orderBy('attendance_date', 'desc')
            ->get();

        $attendanceStats = StudentAttendance::getAttendanceStats($student->id);

        // Get payment records
        $payments = StudentPaymentRecord::where('student_id', $student->id)
            ->with('month')
            ->orderBy('payment_date', 'desc')
            ->get();

        $paymentStats = StudentPaymentRecord::getPaymentStats($student->id);

        // Get tasks
        $tasks = StudentTask::where('student_id', $student->id)
            ->orderBy('due_date', 'desc')
            ->get();

        $taskStats = StudentTask::getTaskStats($student->id);

        // Get lecture views
        $lectureViewsQuery = LectureView::where('student_id', $student->id);

        // Total watched lectures (full count)
        $lectureViewsTotal = (clone $lectureViewsQuery)->count();

        // Recent 20 views for display
        $lectureViews = (clone $lectureViewsQuery)
            ->with('lecture')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Calculate overall performance
        $performance = $this->calculatePerformance(
            $student,
            $examResults,
            $attendance,
            $subscriptions,
            $lectureViewsTotal
        );

        return view('front.parent_portal.report', [
            'student' => $student,
            'subscriptions' => $subscriptions,
            'examResults' => $examResults,
            'attendance' => $attendance,
            'attendanceStats' => $attendanceStats,
            'payments' => $payments,
            'paymentStats' => $paymentStats,
            'tasks' => $tasks,
            'taskStats' => $taskStats,
            'lectureViews' => $lectureViews,
            'lectureViewsTotal' => $lectureViewsTotal,
            'performance' => $performance,
        ]);
    }

    /**
     * Calculate overall performance metrics
     */
    private function calculatePerformance($student, $examResults, $attendance, $subscriptions, $lectureViewsTotal)
    {
        $avgGrade = 0;
        if ($examResults->count() > 0) {
            $totalPercentage = 0;
            $validExamsCount = 0;
            
            foreach ($examResults as $result) {
                // حساب الدرجة الكلية للامتحان من مجموع درجات الأسئلة
                $examTotalDegree = $result->exam->questions()->sum('Q_degree');
                
                if ($examTotalDegree > 0) {
                    $studentDegree = $result->degree ?? 0;
                    $percentage = ($studentDegree / $examTotalDegree) * 100;
                    $totalPercentage += $percentage;
                    $validExamsCount++;
                }
            }
            
            if ($validExamsCount > 0) {
                $avgGrade = round($totalPercentage / $validExamsCount, 2);
            }
        }

        $level = $this->determineLevel($avgGrade);
        $attendancePercentage = $attendance->count() > 0 
            ? round(($attendance->where('is_present', true)->count() / $attendance->count()) * 100, 2)
            : 0;

        $activeSubscriptions = $subscriptions->where('is_active', true)->count();
        $totalSubscriptions = $subscriptions->count();

        // Totals available on the platform
        $totalExamsAvailable = ExamName::count();
        $totalLecturesAvailable = Lecture::count();

        return [
            'average_grade' => $avgGrade,
            'level' => $level,
            'attendance_percentage' => $attendancePercentage,
            'active_subscriptions' => $activeSubscriptions,
            'total_subscriptions' => $totalSubscriptions,
            'exams_taken' => $examResults->count(),
            'total_exams' => $totalExamsAvailable,
            'lectures_watched' => $lectureViewsTotal ?? 0,
            'total_lectures' => $totalLecturesAvailable,
        ];
    }

    /**
     * Determine student level based on average grade
     */
    private function determineLevel($avgGrade)
    {
        if ($avgGrade >= 90) {
            return 'ممتاز';
        } elseif ($avgGrade >= 80) {
            return 'جيد جداً';
        } elseif ($avgGrade >= 70) {
            return 'جيد';
        } elseif ($avgGrade >= 60) {
            return 'مقبول';
        } else {
            return 'يحتاج مساعدة';
        }
    }

    /**
     * Export student report as PDF
     */
    public function exportPDF(Student $student)
    {
        // This will be implemented with a PDF library like DomPDF
        // For now, returning the report
        return $this->report($student);
    }
}
