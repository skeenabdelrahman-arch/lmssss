<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use App\Models\Pdf;
use App\Models\Student;
use App\Models\StudentSubscriptions;
use App\Models\LectureView;
use App\Models\PdfView;
use Illuminate\Http\Request;

class ViewsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    /**
     * عرض قائمة المحاضرات مع إحصائيات المشاهدات
     */
    public function lectures()
    {
        $lectures = Lecture::with('month')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('back.views.lectures', compact('lectures'));
    }

    /**
     * عرض تفاصيل مشاهدات محاضرة معينة
     */
    public function lectureViews($id)
    {
        $lecture = Lecture::with('month')->findOrFail($id);
        
        // جلب جميع الطلاب المشتركين في الكورس
        $subscribedStudents = StudentSubscriptions::where('month_id', $lecture->month_id)
            ->where('is_active', 1)
            ->with('student')
            ->get()
            ->pluck('student')
            ->filter()
            ->unique('id');
        
        // جلب الطلاب الذين شاهدوا المحاضرة
        $viewedStudents = LectureView::where('lecture_id', $id)
            ->with('student')
            ->get()
            ->pluck('student')
            ->filter()
            ->keyBy('id');
        
        // تحديد الطلاب الذين شاهدوا والذين لم يشاهدوا
        $studentsData = [];
        foreach ($subscribedStudents as $student) {
            $studentsData[] = [
                'student' => $student,
                'viewed' => $viewedStudents->has($student->id),
                'viewed_at' => $viewedStudents->has($student->id) 
                    ? LectureView::where('lecture_id', $id)
                        ->where('student_id', $student->id)
                        ->first()->viewed_at 
                    : null,
            ];
        }
        
        return view('back.views.lecture_views', compact('lecture', 'studentsData'));
    }

    /**
     * عرض قائمة المذكرات مع إحصائيات المشاهدات
     */
    public function pdfs()
    {
        $pdfs = Pdf::with('month')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('back.views.pdfs', compact('pdfs'));
    }

    /**
     * عرض تفاصيل مشاهدات مذكرة معينة
     */
    public function pdfViews($id)
    {
        $pdf = Pdf::with('month')->findOrFail($id);
        
        // جلب جميع الطلاب المشتركين في الكورس
        $subscribedStudents = StudentSubscriptions::where('month_id', $pdf->month_id)
            ->where('is_active', 1)
            ->with('student')
            ->get()
            ->pluck('student')
            ->filter()
            ->unique('id');
        
        // جلب الطلاب الذين شاهدوا المذكرة
        $viewedStudents = PdfView::where('pdf_id', $id)
            ->with('student')
            ->get()
            ->pluck('student')
            ->filter()
            ->keyBy('id');
        
        // تحديد الطلاب الذين شاهدوا والذين لم يشاهدوا
        $studentsData = [];
        foreach ($subscribedStudents as $student) {
            $studentsData[] = [
                'student' => $student,
                'viewed' => $viewedStudents->has($student->id),
                'viewed_at' => $viewedStudents->has($student->id) 
                    ? PdfView::where('pdf_id', $id)
                        ->where('student_id', $student->id)
                        ->first()->viewed_at 
                    : null,
            ];
        }
        
        return view('back.views.pdf_views', compact('pdf', 'studentsData'));
    }
}

