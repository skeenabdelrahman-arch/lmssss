<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentSubscriptions;
use App\Models\Month;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentSubscriptionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // تهيئة المتغيرات
        $grade = null;
        $month_id = null;
        $subscribedStudents = collect();
        $notSubscribedStudents = collect();
        $subscriptions = collect(); // لا نجلب الاشتراكات إلا عند الحاجة
        
        // الحصول على القيم من request
        $grade = $request->input('grade');
        $month_id = $request->input('month_id');
        
        // إذا تم اختيار الصف والشهر
        if ($grade && $month_id) {
            // التأكد من أن grade و month_id هما string/int وليس array
            $grade = is_array($grade) ? (string)($grade[0] ?? '') : (string)$grade;
            $month_id = is_array($month_id) ? (int)($month_id[0] ?? 0) : (int)$month_id;
            
            // التحقق من أن القيم صحيحة
            if (!empty($grade) && $month_id > 0) {
                \Log::info('Loading students for subscription', [
                    'grade' => $grade,
                    'month_id' => $month_id
                ]);
                // جلب جميع الطلاب في هذا الصف
                $allStudentsQuery = Student::where('grade', $grade);
                
                // إذا كان هناك بحث، تطبيقه على الطلاب أيضاً
                if ($request->has('search') && $request->search) {
                    $search = $request->search;
                    $allStudentsQuery->where(function($q) use ($search) {
                        $q->where('id', 'like', "%{$search}%")
                          ->orWhere('student_phone', 'like', "%{$search}%")
                          ->orWhere('first_name', 'like', "%{$search}%")
                          ->orWhere('second_name', 'like', "%{$search}%")
                          ->orWhere('third_name', 'like', "%{$search}%")
                          ->orWhere('forth_name', 'like', "%{$search}%")
                          ->orWhereRaw("CONCAT(first_name, ' ', second_name, ' ', third_name, ' ', forth_name) LIKE ?", ["%{$search}%"]);
                    });
                }
                
                $allStudents = $allStudentsQuery->get();
                
                // جلب الطلاب المشتركين في هذا الشهر
                $subscribedStudentIds = StudentSubscriptions::where('month_id', $month_id)
                    ->where('grade', $grade)
                    ->pluck('student_id')
                    ->toArray();
                
                // تقسيم الطلاب إلى مشتركين وغير مشتركين
                $subscribedStudents = $allStudents->filter(function($student) use ($subscribedStudentIds) {
                    return in_array($student->id, $subscribedStudentIds);
                })->values();
                
                $notSubscribedStudents = $allStudents->filter(function($student) use ($subscribedStudentIds) {
                    return !in_array($student->id, $subscribedStudentIds);
                })->values();
            } else {
                // إذا كانت القيم غير صحيحة، إعادة تعيينها إلى null
                $grade = null;
                $month_id = null;
            }
        }
        
        return view('back.subscription.index', compact(
            'subscriptions', 
            'subscribedStudents', 
            'notSubscribedStudents',
            'grade',
            'month_id'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $save = new StudentSubscriptions();
            $save->student_id = $request->student_id ;

        // $save->first_name = $request->first_name;
        // $save->second_name = $request->second_name;
        // $save->third_name = $request->third_name;
        // $save->forth_name = $request->forth_name;
        $save->month_id = $request->month_id ;
        $save->grade = $request->grade;

        if(isset($request->is_active)){
            $save->is_active = 1;
        }
        else{
            $save->is_active = 0;
        }
        $save->save();
        
        // إرسال إشعار للطالب عند تفعيل الكورس
        if ($save->is_active == 1) {
            try {
                $save->refresh(); // تحديث الـ model لضمان تحميل العلاقات
                \App\Services\NotificationService::notifyCourseActivated($save);
            } catch (\Exception $e) {
                \Log::error('Error sending course activation notification', [
                    'subscription_id' => $save->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        return redirect()->back()->with('success','تم اضافة اشتراك للطالب بنجاح');
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
        $subscription = StudentSubscriptions::findOrFail($id);
        return view('back.subscription.edit', compact('subscription'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $save = StudentSubscriptions::findorfail($id);
        $save->student_id = $request->student_id ;
        // $save->first_name = $request->first_name;
        // $save->second_name = $request->second_name;
        // $save->third_name = $request->third_name;
        // $save->forth_name = $request->forth_name;
        $save->month_id = $request->month_id ;
        $save->grade = $request->grade;
        $wasActive = $save->is_active;
        if(isset($request->is_active)){
            $save->is_active = 1;
            // مسح سبب إلغاء التفعيل تلقائياً عند إعادة التفعيل
            $save->deactivation_reason = null;
        }
        else{
            $save->is_active = 0;
        }
        $save->save();
        
        // إرسال إشعار للطالب عند تفعيل الكورس (إذا تم التفعيل الآن ولم يكن مفعل من قبل)
        if ($save->is_active == 1 && $wasActive != 1) {
            try {
                $save->refresh(); // تحديث الـ model لضمان تحميل العلاقات
                \App\Services\NotificationService::notifyCourseActivated($save);
            } catch (\Exception $e) {
                \Log::error('Error sending course activation notification', [
                    'subscription_id' => $save->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        return redirect()->route('student_subscription.index')->with('success','تم تحديث اشتراك الطالب بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        StudentSubscriptions::findorfail($id)->delete();
        return redirect()->back()->with('success','تم حذف المشترك بنجاح');
    }
    public function deleteAllSubscriptions(Request $request)
    {
        // حماية إضافية: يتطلب تأكيد صريح
        if (!$request->has('confirm') || $request->confirm !== 'DELETE_ALL_SUBSCRIPTIONS') {
            return redirect()->back()->with('error', 'يجب تأكيد العملية. هذه العملية خطيرة جداً!');
        }
        
        // استخدام soft delete بدلاً من truncate
        $count = StudentSubscriptions::query()->delete();
        return redirect()->back()->with('success', "تم حذف {$count} اشتراك (يمكن استعادتهم من البيانات المحذوفة)");
    }

    public function get_students($grade)
    {
        $students = Student::where('grade', $grade)
        ->select('id', DB::raw("CONCAT(first_name, ' ', second_name, ' ', third_name, ' ' , forth_name) as full_name"))
        ->pluck('full_name', 'id');

        return response()->json($students);
    }

    public function active_all()
    {
        // جلب جميع الاشتراكات غير المفعلة قبل التحديث
        $inactiveSubscriptions = StudentSubscriptions::where('is_active', 0)->get();
        
        // تفعيل جميع الاشتراكات
        StudentSubscriptions::query()->update(['is_active' => 1]);
        
        // إرسال إشعارات للطلاب الذين تم تفعيل اشتراكاتهم
        foreach ($inactiveSubscriptions as $subscription) {
            try {
                $subscription->refresh(); // تحديث الـ model لضمان تحميل العلاقات
                \App\Services\NotificationService::notifyCourseActivated($subscription);
            } catch (\Exception $e) {
                \Log::error('Error sending course activation notification', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        return redirect()->back()->with('success','تم تفعيل جميع المشتركين');
    }

    /**
     * تفعيل اشتراكات متعددة للطلاب
     */
    public function activateMultiple(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'required|integer|exists:students,id',
            'month_id' => 'required|exists:months,id',
            'grade' => 'required|string',
        ]);

        $activated = 0;
        $alreadySubscribed = 0;

        foreach ($request->student_ids as $student_id) {
            // التحقق من وجود اشتراك سابق
            $subscription = StudentSubscriptions::where('student_id', $student_id)
                ->where('month_id', $request->month_id)
                ->first();

            if ($subscription) {
                // تحديث الاشتراك الموجود
                $wasActive = $subscription->is_active;
                $subscription->is_active = 1;
                $subscription->save();
                
                // إرسال إشعار إذا تم التفعيل الآن ولم يكن مفعل من قبل
                if ($wasActive != 1) {
                    try {
                        $subscription->refresh(); // تحديث الـ model لضمان تحميل العلاقات
                        \App\Services\NotificationService::notifyCourseActivated($subscription);
                    } catch (\Exception $e) {
                        \Log::error('Error sending course activation notification', [
                            'subscription_id' => $subscription->id,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }
                
                $alreadySubscribed++;
            } else {
                // إنشاء اشتراك جديد
                $newSubscription = new StudentSubscriptions();
                $newSubscription->student_id = $student_id;
                $newSubscription->month_id = $request->month_id;
                $newSubscription->grade = $request->grade;
                $newSubscription->is_active = 1;
                $newSubscription->save();
                
                // إرسال إشعار للطالب عند تفعيل الكورس
                try {
                    $newSubscription->refresh(); // تحديث الـ model لضمان تحميل العلاقات
                    \App\Services\NotificationService::notifyCourseActivated($newSubscription);
                } catch (\Exception $e) {
                    \Log::error('Error sending course activation notification', [
                        'subscription_id' => $newSubscription->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
                
                $activated++;
            }
        }

        $message = "تم تفعيل {$activated} اشتراك جديد";
        if ($alreadySubscribed > 0) {
            $message .= " وتحديث {$alreadySubscribed} اشتراك موجود";
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * جلب الأشهر بناءً على الصف
     */
    public function get_months($grade)
    {
        $months = Month::where('grade', $grade)
            ->pluck('name', 'id');
        return response()->json($months);
    }

    /**
     * البحث اللحظي في الطلاب
     */
    public function search(Request $request)
    {
        $grade = $request->input('grade');
        $month_id = $request->input('month_id');
        $search = $request->input('search', '');

        if (!$grade || !$month_id) {
            return response()->json([
                'subscribedStudents' => [],
                'notSubscribedStudents' => []
            ]);
        }

        // جلب جميع الطلاب في هذا الصف
        $allStudentsQuery = Student::where('grade', $grade);
        
        // إذا كان هناك بحث، تطبيقه على الطلاب
        if ($search) {
            $allStudentsQuery->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('student_phone', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('second_name', 'like', "%{$search}%")
                  ->orWhere('third_name', 'like', "%{$search}%")
                  ->orWhere('forth_name', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', second_name, ' ', third_name, ' ', forth_name) LIKE ?", ["%{$search}%"]);
            });
        }
        
        $allStudents = $allStudentsQuery->get();
        
        // جلب الطلاب المشتركين في هذا الشهر
        $subscribedStudentIds = StudentSubscriptions::where('month_id', $month_id)
            ->where('grade', $grade)
            ->pluck('student_id')
            ->toArray();
        
        $subscribedStudents = $allStudents->whereIn('id', $subscribedStudentIds)->values();
        $notSubscribedStudents = $allStudents->whereNotIn('id', $subscribedStudentIds)->values();
        
        // تحضير البيانات للعرض
        $subscribedData = $subscribedStudents->map(function($student) use ($month_id) {
            $subscription = StudentSubscriptions::where('student_id', $student->id)
                ->where('month_id', $month_id)
                ->first();
            
            return [
                'id' => $student->id,
                'name' => $student->first_name . ' ' . $student->second_name . ' ' . $student->third_name . ' ' . $student->forth_name,
                'phone' => $student->student_phone,
                'is_active' => $subscription ? $subscription->is_active : 0
            ];
        });
        
        $notSubscribedData = $notSubscribedStudents->map(function($student) {
            return [
                'id' => $student->id,
                'name' => $student->first_name . ' ' . $student->second_name . ' ' . $student->third_name . ' ' . $student->forth_name,
                'phone' => $student->student_phone
            ];
        });
        
        return response()->json([
            'subscribedStudents' => $subscribedData,
            'notSubscribedStudents' => $notSubscribedData
        ]);
    }

    /**
     * تحميل الطلاب بناءً على الصف والشهر
     */
    public function loadStudents(Request $request)
    {
        $request->validate([
            'grade' => 'required|string',
            'month_id' => 'required|integer|exists:months,id'
        ]);

        $grade = $request->input('grade');
        $month_id = $request->input('month_id');

        // جلب جميع الطلاب في هذا الصف
        $allStudents = Student::where('grade', $grade)->get();
        
        // جلب الطلاب المشتركين في هذا الشهر
        $subscribedStudentIds = StudentSubscriptions::where('month_id', $month_id)
            ->where('grade', $grade)
            ->pluck('student_id')
            ->toArray();
        
        // تقسيم الطلاب
        $subscribedStudents = $allStudents->whereIn('id', $subscribedStudentIds)->values();
        $notSubscribedStudents = $allStudents->whereNotIn('id', $subscribedStudentIds)->values();
        
        // تحضير البيانات للعرض
        $subscribedData = $subscribedStudents->map(function($student) use ($month_id) {
            $subscription = StudentSubscriptions::where('student_id', $student->id)
                ->where('month_id', $month_id)
                ->first();
            
            return [
                'id' => $student->id,
                'name' => trim($student->first_name . ' ' . $student->second_name . ' ' . $student->third_name . ' ' . $student->forth_name),
                'phone' => $student->student_phone,
                'is_active' => $subscription ? $subscription->is_active : 0
            ];
        });
        
        $notSubscribedData = $notSubscribedStudents->map(function($student) {
            return [
                'id' => $student->id,
                'name' => trim($student->first_name . ' ' . $student->second_name . ' ' . $student->third_name . ' ' . $student->forth_name),
                'phone' => $student->student_phone
            ];
        });
        
        return response()->json([
            'subscribedStudents' => $subscribedData,
            'notSubscribedStudents' => $notSubscribedData
        ]);
    }
}
