<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Lecture;
use App\Models\ExamName;
use App\Models\Month;
use App\Models\StudentSubscriptions;
use App\Models\Payment;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Dashboard إحصائي متقدم
     */
    public function dashboard()
    {
        // إحصائيات عامة (مع Cache)
        $cachedStats = CacheService::getStats('analytics_dashboard_stats');
        $stats = [
            'total_students' => $cachedStats['total_students'] ?? Student::count(),
            'active_students' => Cache::remember('active_students', 3600, function() {
                return StudentSubscriptions::where('is_active', 1)->distinct('student_id')->count();
            }),
            'total_lectures' => $cachedStats['total_lectures'] ?? Lecture::count(),
            'published_lectures' => Cache::remember('published_lectures', 3600, function() {
                return Lecture::where('status', 1)->count();
            }),
            'total_exams' => $cachedStats['total_exams'] ?? ExamName::count(),
            'total_months' => Cache::remember('total_months', 3600, function() {
                return Month::count();
            }),
        ];
        
        // إحصائيات الطلاب الجدد (آخر 30 يوم)
        $new_students = Student::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        
        // إحصائيات الإيرادات
        $revenue_stats = $this->getRevenueStats();
        
        // إحصائيات المدفوعات
        $payment_stats = [
            'total_paid' => Payment::where('status', 'paid')->sum('amount'),
            'total_pending' => Payment::where('status', 'pending')->sum('amount'),
            'total_failed' => Payment::where('status', 'failed')->sum('amount'),
        ];
        
        // إحصائيات المحاضرات الأكثر مشاهدة
        $top_lectures = Lecture::orderBy('views', 'desc')->take(10)->get();
        
        // إحصائيات الطلاب حسب الصف
        $students_by_grade = Student::select('grade', DB::raw('count(*) as count'))
            ->groupBy('grade')
            ->get();
        
        // إحصائيات المحاضرات حسب الشهر
        $lectures_by_month = Lecture::select('month_id', DB::raw('count(*) as count'))
            ->with('month')
            ->groupBy('month_id')
            ->get();
        
        // إحصائيات النمو (آخر 12 شهر)
        $growth_data = $this->getGrowthData();
        
        return view('back.analytics.dashboard', compact(
            'stats',
            'new_students',
            'revenue_stats',
            'payment_stats',
            'top_lectures',
            'students_by_grade',
            'lectures_by_month',
            'growth_data'
        ));
    }
    
    /**
     * أعلى 10 طلاب في جميع الامتحانات
     */
    public function topStudents(Request $request)
    {
        // احصل على قائمة الامتحانات النشطة فقط (غير مسحوفة)
        $exams = DB::table('exam_names')
            ->where('status', 1)
            ->where('deleted_at', null) // تأكد من عدم حذفها
            ->select('id', 'exam_title')
            ->orderBy('exam_title', 'asc')
            ->get();
        
        // احصل على الامتحانات المختارة من الطلب (إن وجدت)
        $selectedExams = $request->input('selected_exams', []);
        
        // إذا لم تكن هناك امتحانات مختارة، استخدم جميع الامتحانات
        $examIdsToUse = !empty($selectedExams) ? $selectedExams : $exams->pluck('id')->toArray();
        
        // احصل على أفضل 10 طلاب بناءً على الامتحانات المختارة
        $topStudents = DB::table('exam_results')
            ->join('students', 'exam_results.student_id', '=', 'students.id')
            ->join('exam_names', 'exam_results.exam_id', '=', 'exam_names.id')
            ->whereIn('exam_results.exam_id', $examIdsToUse)
            ->select(
                'students.id',
                'students.first_name',
                'students.second_name',
                'students.third_name',
                'students.forth_name',
                'students.grade',
                'students.gender',
                DB::raw('COUNT(DISTINCT exam_results.exam_id) as total_exams'),
                DB::raw('COUNT(exam_results.id) as total_attempts'),
                DB::raw('AVG(CAST(exam_results.degree AS DECIMAL(10,2))) as avg_degree'),
                DB::raw('MAX(CAST(exam_results.degree AS DECIMAL(10,2))) as max_degree'),
                DB::raw('SUM(CAST(exam_results.degree AS DECIMAL(10,2))) as total_degree')
            )
            ->groupBy('students.id', 'students.first_name', 'students.second_name', 
                     'students.third_name', 'students.forth_name', 'students.grade', 'students.gender')
            ->orderBy('avg_degree', 'desc')
            ->orderBy('total_degree', 'desc')
            ->take(10)
            ->get();
        
        // إضافة بيانات تفصيلية لكل امتحان لكل طالب
        foreach ($topStudents as $student) {
            $student->full_name = trim($student->first_name . ' ' . $student->second_name . ' ' . 
                                      ($student->third_name ?? '') . ' ' . ($student->forth_name ?? ''));
            
            // احصل على بيانات الطالب لكل امتحان (من الامتحانات المختارة فقط)
            $examResults = DB::table('exam_results')
                ->where('student_id', $student->id)
                ->whereIn('exam_id', $examIdsToUse)
                ->select(
                    'exam_id',
                    DB::raw('AVG(CAST(degree AS DECIMAL(10,2))) as degree'),
                    DB::raw('COUNT(*) as attempts'),
                    DB::raw('MAX(CAST(degree AS DECIMAL(10,2))) as max_exam_degree')
                )
                ->groupBy('exam_id')
                ->get();
            
            $student->exams = [];
            foreach ($examResults as $result) {
                $examName = $exams->firstWhere('id', $result->exam_id);
                if ($examName) {
                    $student->exams[] = [
                        'exam_id' => $result->exam_id,
                        'exam_title' => $examName->exam_title ?? 'الامتحان',
                        'degree' => $result->degree,
                        'attempts' => $result->attempts,
                        'max_degree' => $result->max_exam_degree,
                        'percentage' => $result->max_exam_degree > 0 ? ($result->degree / $result->max_exam_degree) * 100 : 0
                    ];
                }
            }
        }
        
        return view('back.analytics.top_students', compact('topStudents', 'exams', 'selectedExams'));
    }
    
    /**
     * تقارير أداء الطلاب
     */
    public function studentPerformance()
    {
        // الطلاب الأكثر نشاطاً
        $active_students = DB::table('students')
            ->leftJoin('exam_results', function($join) {
                $join->on('students.id', '=', 'exam_results.student_id')
                     ->where('exam_results.created_at', '>=', Carbon::now()->subDays(30));
            })
            ->select(
                'students.id',
                'students.first_name',
                'students.second_name',
                'students.third_name',
                'students.forth_name',
                'students.grade',
                DB::raw('COUNT(exam_results.id) as exam_results_count')
            )
            ->groupBy('students.id', 'students.first_name', 'students.second_name', 'students.third_name', 'students.forth_name', 'students.grade')
            ->orderBy('exam_results_count', 'desc')
            ->take(20)
            ->get();
        
        // أداء الطلاب في الامتحانات
        $exam_performance = DB::table('exam_results')
            ->join('students', 'exam_results.student_id', '=', 'students.id')
            ->join('exam_names', 'exam_results.exam_id', '=', 'exam_names.id')
            ->select(
                'students.id',
                'students.first_name',
                'students.second_name',
                'students.third_name',
                'students.forth_name',
                'students.grade',
                'exam_names.exam_title as exam_name',
                DB::raw('AVG(CAST(exam_results.degree AS DECIMAL(10,2))) as avg_degree'),
                DB::raw('COUNT(exam_results.id) as exam_count')
            )
            ->groupBy('students.id', 'students.first_name', 'students.second_name', 
                     'students.third_name', 'students.forth_name', 'students.grade', 'exam_names.exam_title')
            ->orderBy('avg_degree', 'desc')
            ->get();
        
        // توزيع الدرجات
        $grade_distribution = DB::table('exam_results')
            ->select(
                DB::raw('CASE 
                    WHEN CAST(degree AS DECIMAL(10,2)) >= 90 THEN "ممتاز (90-100)"
                    WHEN CAST(degree AS DECIMAL(10,2)) >= 80 THEN "جيد جداً (80-89)"
                    WHEN CAST(degree AS DECIMAL(10,2)) >= 70 THEN "جيد (70-79)"
                    WHEN CAST(degree AS DECIMAL(10,2)) >= 60 THEN "مقبول (60-69)"
                    ELSE "ضعيف (أقل من 60)"
                END as grade_range'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('grade_range')
            ->get();
        
        return view('back.analytics.student_performance', compact(
            'active_students',
            'exam_performance',
            'grade_distribution'
        ));
    }
    
    /**
     * تقارير الإيرادات
     */
    public function revenue()
    {
        // إجمالي الإيرادات (المدفوعات الناجحة فقط)
        $total_revenue = Payment::where('status', 'paid')->sum('amount');
        
        // الإيرادات الشهرية (آخر 12 شهر) - المدفوعات الناجحة فقط
        $monthly_revenue = Payment::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(amount) as total')
        )
        ->where('status', 'paid')
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();
        
        // الإيرادات اليومية (آخر 30 يوم) - المدفوعات الناجحة فقط
        $daily_revenue = Payment::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(amount) as total'),
            DB::raw('COUNT(*) as count')
        )
        ->where('status', 'paid')
        ->where('created_at', '>=', Carbon::now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();
        
        // الإيرادات حسب الكورس - المدفوعات الناجحة فقط
        $revenue_by_course = DB::table('payments')
            ->join('months', 'payments.month_id', '=', 'months.id')
            ->select(
                'months.name as course_name',
                DB::raw('SUM(payments.amount) as total'),
                DB::raw('COUNT(DISTINCT payments.student_id) as student_count')
            )
            ->where('payments.status', 'paid')
            ->groupBy('months.name')
            ->orderBy('total', 'desc')
            ->get();
        
        return view('back.analytics.revenue', compact(
            'total_revenue',
            'monthly_revenue',
            'daily_revenue',
            'revenue_by_course'
        ));
    }
    
    /**
     * Heat Maps لاستخدام المحتوى
     */
    public function contentUsage()
    {
        // المحاضرات الأكثر مشاهدة
        $lecture_views = Lecture::select('id', 'title', 'views', 'created_at')
            ->orderBy('views', 'desc')
            ->get();
        
        // المحاضرات الأكثر تفاعلاً (حسب عدد المشاهدات)
        $top_lectures = Lecture::select('id', 'title', 'views')
            ->orderBy('views', 'desc')
            ->take(20)
            ->get();
        
        // توزيع المشاهدات حسب الصف
        $views_by_grade = Lecture::select('grade', DB::raw('SUM(views) as total_views'), DB::raw('COUNT(*) as lecture_count'))
            ->groupBy('grade')
            ->orderBy('total_views', 'desc')
            ->get();
        
        // توزيع المشاهدات حسب الشهر
        $views_by_month = Lecture::select('month_id', DB::raw('SUM(views) as total_views'), DB::raw('COUNT(*) as lecture_count'))
            ->with('month')
            ->groupBy('month_id')
            ->orderBy('total_views', 'desc')
            ->get();
        
        // إحصائيات المحاضرات المميزة
        $featured_stats = [
            'total' => Lecture::where('is_featured', 1)->count(),
            'total_views' => Lecture::where('is_featured', 1)->sum('views'),
            'avg_views' => Lecture::where('is_featured', 1)->avg('views'),
        ];
        
        return view('back.analytics.content_usage', compact(
            'lecture_views',
            'top_lectures',
            'views_by_grade',
            'views_by_month',
            'featured_stats'
        ));
    }
    
    /**
     * الحصول على إحصائيات الإيرادات
     */
    private function getRevenueStats()
    {
        return [
            'total' => Payment::where('status', 'paid')->sum('amount'),
            'this_month' => Payment::where('status', 'paid')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('amount'),
            'last_month' => Payment::where('status', 'paid')
                ->whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->whereYear('created_at', Carbon::now()->subMonth()->year)
                ->sum('amount'),
            'this_year' => Payment::where('status', 'paid')
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('amount'),
        ];
    }
    
    /**
     * الحصول على بيانات النمو
     */
    private function getGrowthData()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $data[] = [
                'month' => $date->format('Y-m'),
                'label' => $date->format('M Y'),
                'students' => Student::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'revenue' => Payment::where('status', 'paid')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount'),
            ];
        }
        return $data;
    }
}
