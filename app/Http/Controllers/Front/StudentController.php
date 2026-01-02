<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ExamResult; // إضافة الموديل الخاص بنتائج الامتحانات
use App\Models\LoginAttempt;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    // عرض صفحة تسجيل الدخول
    public function login()
    {
        // التحقق من تفعيل تسجيل الدخول
        if (!is_login_enabled()) {
            return view('front.login_disabled');
        }
        return view('front.login');
    }

    // عرض صفحة التسجيل
    public function signup()
    {
        return view('front.signup');
    }

    // تسجيل الدخول
    public function goLogin(Request $request)
    {
        $browser_id = md5($_SERVER['HTTP_USER_AGENT']); // معرّف فريد باستخدام المتصفح وعنوان IP
        $ip_address = $request->ip();
        $maxAttempts = login_attempts();

        $student = Student::where('student_phone', $request->student_phone)->first();

        // التحقق من الحظر
        if ($student && $student->isCurrentlyBlocked()) {
            $blockedUntil = $student->blocked_until ? $student->blocked_until->format('Y-m-d H:i:s') : 'غير محدد';
            \App\Models\LoginAttempt::create([
                'student_phone' => $request->student_phone,
                'ip_address' => $ip_address,
                'success' => false,
                'attempted_at' => now(),
            ]);
            return redirect()->back()->with('error', "تم حظر هذا الحساب. سيتم إلغاء الحظر في: {$blockedUntil}");
        }

        if ($student && $student->password === $request->password) {
            // تسجيل محاولة ناجحة
            \App\Models\LoginAttempt::create([
                'student_phone' => $request->student_phone,
                'ip_address' => $ip_address,
                'success' => true,
                'attempted_at' => now(),
            ]);
            
            // إعادة تعيين محاولات تسجيل الدخول الفاشلة
            $student->failed_login_attempts = 0;
            $student->is_blocked = false;
            $student->blocked_until = null;
            
            // تحديث session_id دائماً عند تسجيل الدخول (يسمح بتسجيل الدخول من جهاز جديد)
            // هذا يلغي الجلسة القديمة تلقائياً
            $student->session_id = $browser_id;
            $student->save();
            
            // تسجيل الدخول وإعادة توليد session ID لإلغاء الجلسة القديمة
            Auth::guard('student')->login($student);
            // إعادة توليد session ID مع حذف الجلسة القديمة (true = يحذف الجلسة القديمة)
            $request->session()->regenerate(true);
            
            return redirect('courses');
        } else {
            // تسجيل محاولة فاشلة
            \App\Models\LoginAttempt::create([
                'student_phone' => $request->student_phone ?? 'unknown',
                'ip_address' => $ip_address,
                'success' => false,
                'attempted_at' => now(),
            ]);
            
            // تحديث محاولات تسجيل الدخول الفاشلة
            if ($student) {
                $student->failed_login_attempts = ($student->failed_login_attempts ?? 0) + 1;
                $student->last_failed_login_at = now();
                
                // إذا تجاوز العدد المسموح، حظر الطالب
                if ($student->failed_login_attempts >= $maxAttempts) {
                    $student->is_blocked = true;
                    $student->blocked_until = now()->addMinutes(15); // حظر لمدة 15 دقيقة
                    $student->save();
                    return redirect()->back()->with('error', "تم حظر الحساب بعد {$maxAttempts} محاولات فاشلة. سيتم إلغاء الحظر بعد 15 دقيقة.");
                }
                $student->save();
            }
            
            $remainingAttempts = $student ? ($maxAttempts - $student->failed_login_attempts) : $maxAttempts;
            return redirect()->back()->with('error', "خطأ في بيانات التسجيل. المحاولات المتبقية: {$remainingAttempts}");
        }
    }

    // تسجيل الخروج
    public function studentLogout()
    {
        Auth::guard('student')->logout();
        return redirect('/');
    }

    // عرض صفحة البروفايل مع نتائج الامتحانات
    public function index()
    {
        // استرجاع الطالب الحالي
        $student = Auth::guard('student')->user();

        // استرجاع نتائج الامتحانات الخاصة بالطالب مع أسماء الامتحانات
        $examResults = $student->examResults()->with('exam')->get();

        // تمرير البيانات إلى العرض
        return view('front.profile', compact('student', 'examResults'));
    }

    // عرض صفحة إنشاء طالب جديد
    public function create()
    {
        //
    }

    // تخزين الطالب الجديد
    public function store(Request $request)
    {
        try {
            $request->validate([
                'student_phone' => 'required|unique:students,student_phone',
                'parent_phone' => 'required|different:student_phone',
                'password' => 'required|string|min:8|confirmed',
                'email' => 'required|unique:students,email',
            ]);

            $save = new Student();
            $save->first_name = $request->first_name;
            $save->second_name = $request->second_name;
            $save->third_name = $request->third_name;
            $save->forth_name = $request->forth_name;
            $save->email = $request->email;
            $save->student_phone = $request->student_phone;
            $save->parent_phone = $request->parent_phone;
            $save->city = $request->city;
            $save->gender = $request->gender;
            $save->grade = $request->grade;
            $save->register = $request->register ?? 'اونلاين';
            // جعل كود الطالب هو نفس كلمة المرور
            $save->student_code = $request->password;
            $save->password = $save->student_code;
            $save->remember_token = Str::random(60); // <-- هنا            
            // حفظ الطالب أولاً للحصول على ID
            $save->save();
            
            // تحميل الصورة بعد الحصول على ID
            if (!empty($request->file('image'))) {
                $file = $request->file('image');
                $ext = $file->getClientOriginalExtension();
                $filename = strtolower($save->id . Str::random(20) . '.' . $ext);
                $file->move('upload_files/', $filename);
                $save->image = $filename;
                $save->save();
            }

            // تسجيل الدخول مباشرة بعد إضافة الطالب
            $student = Student::where('student_phone', $request->student_phone)->first();
            Auth::guard('student')->login($student);

            // إرسال إشعار للمديرين عند تسجيل طالب جديد
            NotificationService::notifyStudentRegistered($student);

            return redirect()->route('courses.index')->with('success', 'تم تسجيل الطالب بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    // تحديث صورة الطالب والإيميل
    public function updateImage(Request $request)
    {
        // الحصول على الطالب المسجل دخوله حالياً
        $student = Auth::guard('student')->user();
        
        if (!$student) {
            return redirect()->route('student.login')->with('error', 'يرجى تسجيل الدخول أولاً');
        }
        
        // التحقق من صحة البيانات
        $request->validate([
            'email' => 'required|email|unique:students,email,' . $student->id,
            'image' => empty($student->image) ? 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120' : 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل',
            'image.required' => 'الصورة الشخصية مطلوبة',
            'image.image' => 'يجب أن يكون الملف صورة',
            'image.mimes' => 'نوع الصورة غير مدعوم (JPG, PNG, GIF, WEBP فقط)',
            'image.max' => 'حجم الصورة لا يجب أن يتجاوز 5 ميجابايت',
        ]);
        
        // تحديث الإيميل
        if ($request->has('email') && !empty($request->email)) {
            $student->email = $request->email;
        }
        
        // تحديث الصورة
        if (!empty($request->file('image'))) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($student->image && file_exists('upload_files/' . $student->image)) {
                @unlink('upload_files/' . $student->image);
            }
            
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = strtolower($student->id . Str::random(20) . '.' . $ext);
            $file->move('upload_files/', $filename);
            $student->image = $filename;
        }
        
        $student->save();
        
        // التوجيه إلى صفحة الكورسات بعد الحفظ الناجح
        return redirect()->route('courses.index')->with('success', 'تم حفظ الصورة والإيميل بنجاح! يمكنك الآن تصفح الكورسات');
    }
    // تحديث كلمة المرور للطالب
    public function updatePassword(Request $request)
    {
        $student = Auth::guard('student')->user();
        
        if (!$student) {
            return redirect()->route('student.login')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        // التحقق من كلمة المرور الحالية
        if ($student->password !== $request->current_password) {
            return redirect()->back()->with('error', 'كلمة المرور الحالية غير صحيحة');
        }

        // التحقق من أن كلمة المرور الجديدة تطابق الشروط (مثل تأكيدها)
        if ($request->new_password !== $request->new_password_confirmation) {
            return redirect()->back()->with('error', 'كلمة المرور الجديدة غير متطابقة');
        }

        // تحديث كلمة المرور الجديدة
        $student->password = $request->new_password; // حفظ الباسورد كنص عادي
        $student->save();

        return redirect()->back()->with('success', 'تم تغيير كلمة المرور بنجاح');
    }
}
