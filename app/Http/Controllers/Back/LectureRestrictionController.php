<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\LectureRestriction;
use App\Models\Student;
use App\Models\Lecture;
use App\Models\Month;
use Illuminate\Http\Request;

class LectureRestrictionController extends Controller
{
    /**
     * عرض صفحة إدارة قيود المحاضرات
     */
    public function index()
    {
        $restrictions = LectureRestriction::with(['student', 'lecture.month'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('back.lecture_restrictions.index', compact('restrictions'));
    }

    /**
     * عرض صفحة إضافة قيد جديد
     */
    public function create()
    {
        $students = Student::orderBy('first_name')->get();
        $months = Month::with('lectures')->orderBy('grade')->orderBy('name')->get();
        
        return view('back.lecture_restrictions.create', compact('students', 'months'));
    }

    /**
     * حفظ قيد جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'lectures' => 'required|array|min:1',
            'lectures.*' => 'exists:lectures,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $added = 0;
        $existing = 0;

        foreach ($request->student_ids as $studentId) {
            foreach ($request->lectures as $lectureId) {
                $result = LectureRestriction::addRestriction(
                    $studentId,
                    $lectureId,
                    $request->reason
                );

                if ($result->wasRecentlyCreated) {
                    $added++;
                } else {
                    $existing++;
                }
            }
        }

        $message = "تم إضافة {$added} قيد بنجاح";
        if ($existing > 0) {
            $message .= " ({$existing} قيد كان موجود مسبقاً)";
        }

        return redirect()->route('admin.lecture_restrictions.index')
            ->with('success', $message);
    }

    /**
     * حذف قيد
     */
    public function destroy($id)
    {
        $restriction = LectureRestriction::findOrFail($id);
        $restriction->delete();

        return redirect()->route('admin.lecture_restrictions.index')
            ->with('success', 'تم إزالة القيد بنجاح');
    }

    /**
     * حذف جميع القيود
     */
    public function destroyAll()
    {
        $count = LectureRestriction::count();
        LectureRestriction::truncate();

        return redirect()->route('admin.lecture_restrictions.index')
            ->with('success', "تم حذف جميع القيود ({$count} قيد)");
    }

    /**
     * حذف جميع قيود طالب معين (POST)
     */
    public function destroyByStudentPost(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id'
        ]);

        $count = LectureRestriction::where('student_id', $request->student_id)->delete();

        return redirect()->route('admin.lecture_restrictions.index')
            ->with('success', "تم إزالة {$count} قيد للطالب");
    }

    /**
     * حذف جميع قيود محاضرة معينة (POST)
     */
    public function destroyByLecturePost(Request $request)
    {
        $request->validate([
            'lecture_id' => 'required|exists:lectures,id'
        ]);

        $count = LectureRestriction::where('lecture_id', $request->lecture_id)->delete();
        $lecture = Lecture::find($request->lecture_id);
        $lectureName = $lecture ? $lecture->title : 'المحاضرة';

        return redirect()->route('admin.lecture_restrictions.index')
            ->with('success', "تم إزالة {$count} قيد من {$lectureName}");
    }

    /**
     * API: الحصول على محاضرات كورس معين
     */
    public function getLecturesByMonth($monthId)
    {
        $lectures = Lecture::where('month_id', $monthId)
            ->where('status', 1)
            ->orderBy('display_order')
            ->orderBy('id')
            ->get(['id', 'title', 'month_id']);

        return response()->json($lectures);
    }

    /**
     * عرض قيود طالب معين
     */
    public function studentRestrictions($studentId)
    {
        $student = Student::findOrFail($studentId);
        $restrictions = LectureRestriction::where('student_id', $studentId)
            ->with(['lecture.month'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('back.lecture_restrictions.student', compact('student', 'restrictions'));
    }
}
