<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Month;
use App\Models\StudentSubscriptions;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WebhookController extends Controller
{
    /**
     * Webhook لتسجيل طالب جديد
     * 
     * Expected JSON:
     * {
     *   "action": "register_student",
     *   "phone": "01234567890",
     *   "student_code": "12345",
     *   "first_name": "أحمد",
     *   "second_name": "محمد",
     *   "third_name": "علي",
     *   "forth_name": "حسن",
     *   "email": "student@example.com",
     *   "parent_phone": "01234567891",
     *   "city": "القاهرة",
     *   "gender": "ذكر",
     *   "grade": "الصف الأول الثانوي"
     * }
     */
    public function registerStudent(Request $request)
    {
        Log::info('Webhook: Register Student Request', $request->all());

        // التحقق من البيانات
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
            'student_code' => 'required|string|max:50',
            'first_name' => 'nullable|string|max:255',
            'second_name' => 'nullable|string|max:255',
            'third_name' => 'nullable|string|max:255',
            'forth_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:ذكر,أنثى',
            'grade' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::warning('Webhook: Validation Failed', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $request->input('phone');
        $studentCode = $request->input('student_code');

        // التحقق من وجود الطالب
        $student = Student::where('student_phone', $phone)->first();

        if ($student) {
            // تحديث بيانات الطالب إذا كان موجوداً
            $updateData = [
                'student_code' => $studentCode,
                'password' => Hash::make($studentCode), // كلمة المرور = الكود
            ];
            
            // تحديث الأسماء فقط إذا تم إرسالها
            if ($request->has('first_name') && !empty($request->input('first_name'))) {
                $updateData['first_name'] = $request->input('first_name');
            }
            if ($request->has('second_name') && !empty($request->input('second_name'))) {
                $updateData['second_name'] = $request->input('second_name');
            }
            if ($request->has('third_name')) {
                $updateData['third_name'] = $request->input('third_name', '');
            }
            if ($request->has('forth_name')) {
                $updateData['forth_name'] = $request->input('forth_name', '');
            }
            if ($request->has('email') && !empty($request->input('email'))) {
                $updateData['email'] = $request->input('email');
            }
            if ($request->has('parent_phone')) {
                $updateData['parent_phone'] = $request->input('parent_phone', $student->parent_phone);
            }
            if ($request->has('city')) {
                $updateData['city'] = $request->input('city', $student->city);
            }
            if ($request->has('gender')) {
                $updateData['gender'] = $request->input('gender', $student->gender);
            }
            if ($request->has('grade')) {
                $updateData['grade'] = $request->input('grade', $student->grade);
            }
            
            $student->update($updateData);

            Log::info('Webhook: Student Updated', ['student_id' => $student->id, 'phone' => $phone]);
            
            return response()->json([
                'success' => true,
                'message' => 'Student updated successfully',
                'student_id' => $student->id,
                'action' => 'updated'
            ], 200);
        }

        // إنشاء طالب جديد
        // إنشاء email فريد إذا لم يكن موجوداً
        $email = $request->input('email');
        if (empty($email)) {
            $email = 'student_' . $phone . '@platform.local';
        }

        // التأكد من أن البريد الإلكتروني فريد
        $emailExists = Student::where('email', $email)->exists();
        if ($emailExists) {
            $email = 'student_' . $phone . '_' . time() . '@platform.local';
        }

        $student = Student::create([
            'first_name' => $request->input('first_name', 'طالب') ?: 'طالب',
            'second_name' => $request->input('second_name', 'جديد') ?: 'جديد',
            'third_name' => $request->input('third_name', '') ?: '',
            'forth_name' => $request->input('forth_name', '') ?: '',
            'email' => $email,
            'student_phone' => $phone,
            'parent_phone' => $request->input('parent_phone', '0') ?: '0',
            'city' => $request->input('city', 'غير محدد') ?: 'غير محدد',
            'gender' => $request->input('gender', 'ذكر') ?: 'ذكر',
            'grade' => $request->input('grade', 'غير محدد') ?: 'غير محدد',
            'register' => 'webhook', // علامة أن التسجيل من Webhook
            'student_code' => $studentCode,
            'password' => Hash::make($studentCode), // كلمة المرور = الكود
        ]);

        Log::info('Webhook: Student Created', ['student_id' => $student->id, 'phone' => $phone]);

        return response()->json([
            'success' => true,
            'message' => 'Student registered successfully',
            'student_id' => $student->id,
            'phone' => $phone,
            'action' => 'created'
        ], 201);
    }

    /**
     * Webhook لتفعيل الكورس بعد الدفع
     * 
     * Expected JSON:
     * {
     *   "action": "activate_subscription",
     *   "phone": "01234567890",
     *   "month_number": 10,  // رقم الشهر (1-12)
     *   "payment_amount": 500,
     *   "payment_date": "2024-10-15",
     *   "payment_reference": "PAY123456"  // رقم مرجعي من النظام الخارجي
     * }
     */
    public function activateSubscription(Request $request)
    {
        Log::info('Webhook: Activate Subscription Request', $request->all());

        // التحقق من البيانات
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
            'month_number' => 'required|integer|min:1|max:12',
            'payment_amount' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::warning('Webhook: Validation Failed', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $request->input('phone');
        $monthNumber = $request->input('month_number');

        // البحث عن الطالب
        $student = Student::where('student_phone', $phone)->first();

        if (!$student) {
            Log::warning('Webhook: Student Not Found', ['phone' => $phone]);
            return response()->json([
                'success' => false,
                'message' => 'Student not found. Please register the student first.'
            ], 404);
        }

        // البحث عن الكورس بناءً على تاريخ الإنشاء (created_at)
        // نبحث عن الكورس الذي تم إنشاؤه في نفس الشهر من نفس السنة
        $currentYear = date('Y');
        $currentMonth = date('m');
        
        // إذا كان رقم الشهر المطلوب هو نفس الشهر الحالي، نستخدم السنة الحالية
        // وإلا نستخدم السنة الحالية أيضاً (يمكن تعديلها لاحقاً)
        $targetYear = $currentYear;
        
        // البحث عن الكورس الذي تم إنشاؤه في الشهر المطلوب
        $month = Month::whereYear('created_at', $targetYear)
            ->whereMonth('created_at', $monthNumber)
            ->first();

        // إذا لم يتم العثور عليه، نبحث في السنة السابقة (في حالة الكورسات القديمة)
        if (!$month) {
            $month = Month::whereYear('created_at', $targetYear - 1)
                ->whereMonth('created_at', $monthNumber)
                ->first();
        }

        // إذا لم يتم العثور عليه، نبحث في أي سنة (آخر كورس تم إنشاؤه في هذا الشهر)
        if (!$month) {
            $month = Month::whereMonth('created_at', $monthNumber)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        if (!$month) {
            Log::warning('Webhook: Month Not Found', [
                'month_number' => $monthNumber,
                'year' => $targetYear
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Course not found for month number: ' . $monthNumber . '. Please create a course in this month first.'
            ], 404);
        }

        // التحقق من وجود اشتراك سابق
        $existingSubscription = StudentSubscriptions::where('student_id', $student->id)
            ->where('month_id', $month->id)
            ->first();

        if ($existingSubscription) {
            // تحديث الاشتراك الموجود
            $existingSubscription->update([
                'is_active' => 1,
                'first_name' => $student->first_name,
                'second_name' => $student->second_name,
                'third_name' => $student->third_name,
                'forth_name' => $student->forth_name,
                'grade' => $student->grade,
            ]);

            Log::info('Webhook: Subscription Updated', [
                'student_id' => $student->id,
                'month_id' => $month->id,
                'subscription_id' => $existingSubscription->id
            ]);
            $subscription = $existingSubscription;
        } else {
            // إنشاء اشتراك جديد
            $subscription = StudentSubscriptions::create([
                'student_id' => $student->id,
                'month_id' => $month->id,
                'first_name' => $student->first_name,
                'second_name' => $student->second_name,
                'third_name' => $student->third_name,
                'forth_name' => $student->forth_name,
                'grade' => $student->grade,
                'is_active' => 1,
            ]);

            Log::info('Webhook: Subscription Created', [
                'student_id' => $student->id,
                'month_id' => $month->id,
                'subscription_id' => $subscription->id
            ]);
        }

        // إنشاء سجل دفع (اختياري)
        if ($request->has('payment_amount') && $request->input('payment_amount') > 0) {
            $payment = Payment::create([
                'student_id' => $student->id,
                'month_id' => $month->id,
                'amount' => $request->input('payment_amount'),
                'status' => 'paid',
                'payment_method' => 'webhook',
                'payment_reference' => $request->input('payment_reference', 'WEBHOOK_' . time()),
                'paid_at' => $request->input('payment_date') ? date('Y-m-d H:i:s', strtotime($request->input('payment_date'))) : now(),
                'kashier_response' => json_encode([
                    'source' => 'external_system',
                    'webhook_data' => $request->all()
                ]),
            ]);

            Log::info('Webhook: Payment Record Created', ['payment_id' => $payment->id]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Subscription activated successfully',
            'student_id' => $student->id,
            'month_id' => $month->id,
            'month_name' => $month->name ?? 'N/A',
            'month_created_at' => $month->created_at->format('Y-m-d'),
            'subscription_id' => $subscription->id ?? null
        ], 200);
    }

    /**
     * Webhook للدفع - يجمع التسجيل والتفعيل في endpoint واحد
     * 
     * هذا الـ endpoint يعمل تلقائياً:
     * 1. إذا كان الطالب موجود → يفعّل الاشتراك مباشرة
     * 2. إذا كان الطالب غير موجود → يسجله أولاً ثم يفعّل الاشتراك
     * 
     * Expected JSON:
     * {
     *   "phone": "01234567890",
     *   "student_code": "12345",  // مطلوب إذا كان الطالب غير موجود
     *   "month_number": 10,  // رقم الشهر (1-12)
     *   "first_name": "أحمد",  // اختياري
     *   "second_name": "محمد",  // اختياري
     *   "third_name": "علي",  // اختياري
     *   "forth_name": "حسن",  // اختياري
     *   "email": "student@example.com",  // اختياري
     *   "parent_phone": "01234567891",  // اختياري
     *   "city": "القاهرة",  // اختياري
     *   "gender": "ذكر",  // اختياري
     *   "grade": "الصف الأول الثانوي",  // اختياري
     *   "payment_amount": 500,  // اختياري
     *   "payment_date": "2024-10-15",  // اختياري
     *   "payment_reference": "PAY123456"  // اختياري
     * }
     */
    public function payment(Request $request)
    {
        // Log with detailed information to ensure logging is working
        Log::info('=== WEBHOOK PAYMENT REQUEST START ===', [
            'timestamp' => now()->toDateTimeString(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'data' => $request->all()
        ]);

        // التحقق من البيانات الأساسية
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
            'month_number' => 'required|integer|min:1|max:12',
            'student_code' => 'nullable|string|max:50', // اختياري (مطلوب فقط إذا كان الطالب غير موجود)
            'first_name' => 'nullable|string|max:255',
            'second_name' => 'nullable|string|max:255',
            'third_name' => 'nullable|string|max:255',
            'forth_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:ذكر,أنثى',
            'grade' => 'nullable|string|max:255',
            'payment_amount' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::warning('Webhook: Validation Failed', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $request->input('phone');
        $monthNumber = $request->input('month_number');
        $studentCode = $request->input('student_code');

        // البحث عن الطالب
        $student = Student::where('student_phone', $phone)->first();
        $studentWasCreated = false;

        if (!$student) {
            // الطالب غير موجود - نحتاج student_code للتسجيل
            if (empty($studentCode)) {
                Log::warning('Webhook: Student Code Required for New Student', ['phone' => $phone]);
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found. Please provide "student_code" to register the student first.'
                ], 400);
            }

            // إنشاء طالب جديد
            $email = $request->input('email');
            if (empty($email)) {
                $email = 'student_' . $phone . '@platform.local';
            }

            // التأكد من أن البريد الإلكتروني فريد
            $emailExists = Student::where('email', $email)->exists();
            if ($emailExists) {
                $email = 'student_' . $phone . '_' . time() . '@platform.local';
            }

            $student = Student::create([
                'first_name' => $request->input('first_name', 'طالب') ?: 'طالب',
                'second_name' => $request->input('second_name', 'جديد') ?: 'جديد',
                'third_name' => $request->input('third_name', '') ?: '',
                'forth_name' => $request->input('forth_name', '') ?: '',
                'email' => $email,
                'student_phone' => $phone,
                'parent_phone' => $request->input('parent_phone', '0') ?: '0',
                'city' => $request->input('city', 'غير محدد') ?: 'غير محدد',
                'gender' => $request->input('gender', 'ذكر') ?: 'ذكر',
                'grade' => $request->input('grade', 'غير محدد') ?: 'غير محدد',
                'register' => 'webhook',
                'student_code' => $studentCode,
                'password' => Hash::make($studentCode),
            ]);

            $studentWasCreated = true;
            Log::info('Webhook: Student Created During Payment', ['student_id' => $student->id, 'phone' => $phone]);
        } else {
            // الطالب موجود - تحديث البيانات إذا تم إرسالها
            $updateData = [];
            
            if (!empty($studentCode)) {
                $updateData['student_code'] = $studentCode;
                $updateData['password'] = Hash::make($studentCode);
            }
            
            if ($request->has('first_name') && !empty($request->input('first_name'))) {
                $updateData['first_name'] = $request->input('first_name');
            }
            if ($request->has('second_name') && !empty($request->input('second_name'))) {
                $updateData['second_name'] = $request->input('second_name');
            }
            if ($request->has('third_name')) {
                $updateData['third_name'] = $request->input('third_name', '');
            }
            if ($request->has('forth_name')) {
                $updateData['forth_name'] = $request->input('forth_name', '');
            }
            if ($request->has('email') && !empty($request->input('email'))) {
                $updateData['email'] = $request->input('email');
            }
            if ($request->has('parent_phone')) {
                $updateData['parent_phone'] = $request->input('parent_phone', $student->parent_phone);
            }
            if ($request->has('city')) {
                $updateData['city'] = $request->input('city', $student->city);
            }
            if ($request->has('gender')) {
                $updateData['gender'] = $request->input('gender', $student->gender);
            }
            if ($request->has('grade')) {
                $updateData['grade'] = $request->input('grade', $student->grade);
            }

            if (!empty($updateData)) {
                $student->update($updateData);
                Log::info('Webhook: Student Updated During Payment', ['student_id' => $student->id, 'phone' => $phone]);
            }
        }

        // الآن نفعّل الاشتراك
        // البحث عن الكورس بناءً على تاريخ الإنشاء (created_at)
        $currentYear = date('Y');
        $targetYear = $currentYear;
        
        // البحث عن الكورس الذي تم إنشاؤه في الشهر المطلوب
        $month = Month::whereYear('created_at', $targetYear)
            ->whereMonth('created_at', $monthNumber)
            ->first();

        // إذا لم يتم العثور عليه، نبحث في السنة السابقة
        if (!$month) {
            $month = Month::whereYear('created_at', $targetYear - 1)
                ->whereMonth('created_at', $monthNumber)
                ->first();
        }

        // إذا لم يتم العثور عليه، نبحث في أي سنة (آخر كورس تم إنشاؤه في هذا الشهر)
        if (!$month) {
            $month = Month::whereMonth('created_at', $monthNumber)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        if (!$month) {
            Log::warning('Webhook: Month Not Found', [
                'month_number' => $monthNumber,
                'year' => $targetYear
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Course not found for month number: ' . $monthNumber . '. Please create a course in this month first.'
            ], 404);
        }

        // التحقق من وجود اشتراك سابق
        $existingSubscription = StudentSubscriptions::where('student_id', $student->id)
            ->where('month_id', $month->id)
            ->first();

        if ($existingSubscription) {
            // تحديث الاشتراك الموجود
            $existingSubscription->update([
                'is_active' => 1,
                'first_name' => $student->first_name,
                'second_name' => $student->second_name,
                'third_name' => $student->third_name,
                'forth_name' => $student->forth_name,
                'grade' => $student->grade,
            ]);

            Log::info('Webhook: Subscription Updated', [
                'student_id' => $student->id,
                'month_id' => $month->id,
                'subscription_id' => $existingSubscription->id
            ]);
            $subscription = $existingSubscription;
        } else {
            // إنشاء اشتراك جديد
            $subscription = StudentSubscriptions::create([
                'student_id' => $student->id,
                'month_id' => $month->id,
                'first_name' => $student->first_name,
                'second_name' => $student->second_name,
                'third_name' => $student->third_name,
                'forth_name' => $student->forth_name,
                'grade' => $student->grade,
                'is_active' => 1,
            ]);

            Log::info('Webhook: Subscription Created', [
                'student_id' => $student->id,
                'month_id' => $month->id,
                'subscription_id' => $subscription->id
            ]);
        }

        // إنشاء سجل دفع (اختياري)
        if ($request->has('payment_amount') && $request->input('payment_amount') > 0) {
            $payment = Payment::create([
                'student_id' => $student->id,
                'month_id' => $month->id,
                'amount' => $request->input('payment_amount'),
                'status' => 'paid',
                'payment_method' => 'webhook',
                'payment_reference' => $request->input('payment_reference', 'WEBHOOK_' . time()),
                'paid_at' => $request->input('payment_date') ? date('Y-m-d H:i:s', strtotime($request->input('payment_date'))) : now(),
                'kashier_response' => json_encode([
                    'source' => 'external_system',
                    'webhook_data' => $request->all()
                ]),
            ]);

            Log::info('Webhook: Payment Record Created', ['payment_id' => $payment->id]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully',
            'student_created' => $studentWasCreated,
            'student_id' => $student->id,
            'month_id' => $month->id,
            'month_name' => $month->name ?? 'N/A',
            'month_created_at' => $month->created_at->format('Y-m-d'),
            'subscription_id' => $subscription->id ?? null
        ], 200);
    }

    /**
     * Webhook عام (يدعم عدة actions)
     */
    public function handle(Request $request)
    {
        $action = $request->input('action');

        switch ($action) {
            case 'register_student':
                return $this->registerStudent($request);
            
            case 'activate_subscription':
                return $this->activateSubscription($request);
            
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Unknown action: ' . $action
                ], 400);
        }
    }
}
