<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use App\Models\ExamName;
use App\Models\Month;
use App\Models\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ContentController extends Controller
{
    /**
     * Content Scheduling - جدولة المحتوى
     */
    public function scheduleIndex()
    {
        // التحقق من وجود العمود أولاً
        $hasScheduledColumn = \Illuminate\Support\Facades\Schema::hasColumn('lectures', 'scheduled_at');
        
        // المحاضرات المجدولة
        $scheduled_lectures = collect();
        if ($hasScheduledColumn) {
            $scheduled_lectures = Lecture::whereNotNull('scheduled_at')
                ->where('scheduled_at', '>', now())
                ->orderBy('scheduled_at', 'asc')
                ->get();
        }
        
        // جميع المحاضرات (لاختيار محاضرة للجدولة)
        $all_lectures = Lecture::orderBy('created_at', 'desc')->get();
        
        return view('back.content.schedule', compact('scheduled_lectures', 'all_lectures', 'hasScheduledColumn'));
    }
    
    /**
     * حفظ جدولة المحتوى
     */
    public function scheduleStore(Request $request, $id)
    {
        // التحقق من وجود العمود أولاً
        if (!\Illuminate\Support\Facades\Schema::hasColumn('lectures', 'scheduled_at')) {
            return redirect()->back()->with('error', 'يجب تشغيل الـ migration أولاً. قم بتشغيل: php artisan migrate');
        }
        
        $request->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);
        
        $lecture = Lecture::findOrFail($id);
        $lecture->scheduled_at = Carbon::parse($request->scheduled_at);
        $lecture->status = 0; // غير منشور حتى وقت الجدولة
        $lecture->save();
        
        return redirect()->back()->with('success', 'تم جدولة المحاضرة بنجاح');
    }
    
    /**
     * Content Versioning - إصدارات المحتوى
     */
    public function versions($id)
    {
        $lecture = Lecture::findOrFail($id);
        
        // في حالة عدم وجود نظام versioning، نعرض تاريخ التعديلات
        $versions = [
            [
                'version' => '1.0',
                'created_at' => $lecture->created_at,
                'title' => $lecture->title,
                'description' => $lecture->description,
                'status' => 'current'
            ]
        ];
        
        return view('back.content.versions', compact('lecture', 'versions'));
    }
    
    /**
     * تحديث ترتيب الظهور
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'type' => 'required|in:lecture,exam,month,pdf',
            'id' => 'required|integer',
            'display_order' => 'required|integer|min:0',
        ]);
        
        $type = $request->type;
        $id = $request->id;
        $displayOrder = $request->display_order;
        
        try {
            switch ($type) {
                case 'lecture':
                    $item = Lecture::findOrFail($id);
                    break;
                case 'exam':
                    $item = ExamName::findOrFail($id);
                    break;
                case 'month':
                    $item = Month::findOrFail($id);
                    break;
                case 'pdf':
                    $item = Pdf::findOrFail($id);
                    break;
                default:
                    return response()->json(['success' => false, 'message' => 'نوع غير صحيح'], 400);
            }
            
            $item->display_order = $displayOrder;
            $item->save();
            
            return response()->json(['success' => true, 'message' => 'تم تحديث الترتيب بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }
}
