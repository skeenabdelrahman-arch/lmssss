<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Month;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MonthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Month::query();
        
        // فلترة حسب الصف الدراسي
        if ($request->has('grade') && $request->grade != '') {
            $query->where('grade', $request->grade);
        }
        
        // ترتيب حسب الصف الدراسي بناءً على ترتيب الصفوف في الإعدادات
        $signupGrades = signup_grades();
        if (!empty($signupGrades)) {
            $caseStatement = "CASE grade ";
            foreach ($signupGrades as $index => $grade) {
                $order = $index + 1;
                $caseStatement .= "WHEN '" . addslashes($grade['value']) . "' THEN {$order} ";
            }
            $caseStatement .= "ELSE " . (count($signupGrades) + 1) . " END";
            $months = $query->orderByRaw($caseStatement)->orderBy('name')->get();
        } else {
            // إذا لم توجد صفوف في الإعدادات، استخدم الترتيب العادي
            $months = $query->orderBy('grade')->orderBy('name')->get();
        }
        
        return view('back.month.index', compact('months'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('back.month.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $List_Monthes = $request->List_Monthes;
        foreach($List_Monthes as $index => $month)
        {
            $save = new Month();
            $save->name = $month['name'];
            $save->grade = $month['grade'];
            $save->price = $month['price'];
            $save->save();
            
            // رفع الصورة بعد الحصول على ID
            $fileKey = "List_Monthes.{$index}.image";
            if ($request->hasFile($fileKey)) {
                $file = $request->file($fileKey);
                if ($file && $file->isValid()) {
                    // التحقق من نوع وحجم الملف
                    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                    if (!in_array($file->getMimeType(), $allowedTypes)) {
                        continue; // تخطي إذا كان نوع الملف غير مدعوم
                    }
                    if ($file->getSize() > 5242880) { // 5MB
                        continue; // تخطي إذا كان حجم الملف أكبر من 5MB
                    }
                    
                    $ext = $file->getClientOriginalExtension();
                    $filename = strtolower($save->id . Str::random(20) . '.' . $ext);
                    $file->move('upload_files/', $filename);
                    $save->image = $filename;
                    $save->save();
                }
            }
            
            // إرسال إشعار للطلاب عند إضافة كورس جديد
            try {
                \App\Services\NotificationService::notifyNewCourse($save);
            } catch (\Exception $e) {
                \Log::error('Error sending course notification', ['month_id' => $save->id, 'error' => $e->getMessage()]);
            }
        }
        return redirect()->route('month.index')->with('success', 'تم حفظ البيانات بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $month = Month::findOrFail($id);
        return view('back.month.edit', compact('month'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $month = Month::findorfail($id);
        $month->name = $request->name;
        $month->grade = $request->grade;
        $month->price = $request->price;
        
        // رفع الصورة الجديدة إذا تم رفعها
        if (!empty($request->file('image'))) {
            $request->validate([
                'image' => 'image|mimes:jpeg,jpg,png,webp|max:5120', // 5MB max
            ]);
            
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = strtolower($month->id . Str::random(20) . '.' . $ext);
            
            // حذف الصورة القديمة إن وجدت
            if ($month->image && file_exists('upload_files/' . $month->image)) {
                unlink('upload_files/' . $month->image);
            }
            
            $file->move('upload_files/', $filename);
            $month->image = $filename;
        }
        
        $month->save();
        return redirect()->route('month.index')->with('success', 'تم تحديث البيانات بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete( $id)
    {
        Month::findorfail($id)->delete();
        return redirect()->back()->with('success', 'تم حذف البيانات بنجاح');
    }

    public function deleteAllMonthes(Request $request)
    {
        // حماية إضافية: يتطلب تأكيد صريح
        if (!$request->has('confirm') || $request->confirm !== 'DELETE_ALL_MONTHS') {
            return redirect()->back()->with('error', 'يجب تأكيد العملية. هذه العملية خطيرة جداً!');
        }
        
        // استخدام soft delete بدلاً من truncate
        $count = Month::query()->delete();
        return redirect()->back()->with('success', "تم حذف {$count} شهر (يمكن استعادتهم من البيانات المحذوفة)");
    }
}
