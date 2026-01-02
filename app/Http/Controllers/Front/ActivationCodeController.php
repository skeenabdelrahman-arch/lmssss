<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ActivationCode;
use App\Models\StudentSubscriptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivationCodeController extends Controller
{
    /**
     * تفعيل الكورس باستخدام كود التفعيل
     */
    public function activate(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $student = Auth::guard('student')->user();
        $code = strtoupper(trim($request->code));

        // البحث عن الكود
        $activationCode = ActivationCode::where('code', $code)->first();

        if (!$activationCode) {
            return redirect()->back()->with('error', 'كود التفعيل غير صحيح');
        }

        // التحقق من صلاحية الكود
        $validation = $activationCode->isValid();
        if (!$validation['valid']) {
            return redirect()->back()->with('error', $validation['message']);
        }

        // التحقق من أن الطالب غير مشترك بالفعل
        $existingSubscription = StudentSubscriptions::where('student_id', $student->id)
            ->where('month_id', $activationCode->month_id)
            ->where('is_active', 1)
            ->first();

        if ($existingSubscription) {
            return redirect()->back()->with('error', 'أنت مشترك بالفعل في هذا الكورس');
        }

        // تفعيل الكود وإنشاء الاشتراك
        if ($activationCode->activate($student->id)) {
            // إنشاء أو تحديث الاشتراك
            $subscription = StudentSubscriptions::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'month_id' => $activationCode->month_id,
                ],
                [
                    'first_name' => $student->first_name,
                    'second_name' => $student->second_name,
                    'third_name' => $student->third_name,
                    'forth_name' => $student->forth_name,
                    'grade' => $student->grade,
                    'is_active' => 1,
                ]
            );

            // إرسال إشعار للطالب عند تفعيل الكورس
            try {
                \App\Services\NotificationService::notifyCourseActivated($subscription);
            } catch (\Exception $e) {
                \Log::error('Error sending course activation notification', ['subscription_id' => $subscription->id, 'error' => $e->getMessage()]);
            }

            return redirect()->route('month.content', ['month_id' => $activationCode->month_id])
                ->with('success', 'تم تفعيل الكورس بنجاح!');
        }

        return redirect()->back()->with('error', 'حدث خطأ أثناء تفعيل الكود');
    }

    /**
     * التحقق من صحة كود التفعيل (AJAX)
     */
    public function validateCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'month_id' => 'required|exists:months,id',
        ]);

        $code = strtoupper(trim($request->code));
        $activationCode = ActivationCode::where('code', $code)
            ->where('month_id', $request->month_id)
            ->first();

        if (!$activationCode) {
            return response()->json([
                'valid' => false,
                'message' => 'كود التفعيل غير صحيح'
            ]);
        }

        $validation = $activationCode->isValid();
        return response()->json($validation);
    }

    /**
     * عرض صفحة تفعيل الكود
     */
    public function index()
    {
        return view('front.activation_code.index');
    }

    /**
     * صفحة تعليمات التفعيل (للـ QR Code)
     */
    public function instructions()
    {
        return view('front.activation_code.instructions');
    }
}
