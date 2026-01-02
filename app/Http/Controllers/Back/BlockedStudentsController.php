<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class BlockedStudentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    /**
     * عرض قائمة الطلاب المحظورين
     */
    public function index()
    {
        $blockedStudents = Student::where('is_blocked', true)
            ->where(function($query) {
                $query->whereNull('blocked_until')
                    ->orWhere('blocked_until', '>', now());
            })
            ->orderBy('blocked_until', 'desc')
            ->paginate(20);

        return view('back.blocked_students.index', compact('blockedStudents'));
    }

    /**
     * إزالة الحظر من طالب
     */
    public function unblock($id)
    {
        $student = Student::findOrFail($id);
        
        $student->is_blocked = false;
        $student->blocked_until = null;
        $student->failed_login_attempts = 0;
        $student->last_failed_login_at = null;
        $student->save();

        return redirect()->back()->with('success', 'تم إزالة الحظر من الطالب بنجاح');
    }

    /**
     * إزالة الحظر من عدة طلاب
     */
    public function unblockMultiple(Request $request)
    {
        $ids = $request->input('student_ids', []);
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'لم يتم تحديد أي طلاب');
        }

        Student::whereIn('id', $ids)->update([
            'is_blocked' => false,
            'blocked_until' => null,
            'failed_login_attempts' => 0,
            'last_failed_login_at' => null,
        ]);

        return redirect()->back()->with('success', 'تم إزالة الحظر من ' . count($ids) . ' طالب بنجاح');
    }
}
