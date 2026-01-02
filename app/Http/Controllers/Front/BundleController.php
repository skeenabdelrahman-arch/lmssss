<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ActivationCode;
use App\Models\DiscountCode;
use App\Models\Month;
use App\Models\Payment;
use App\Models\StudentSubscriptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BundleController extends Controller
{
    // Kashier API Configuration
    private $kashierApiKey;
    private $kashierMerchantId;
    private $kashierMode;

    public function __construct()
    {
        $this->kashierApiKey = config('services.kashier.api_key');
        $this->kashierMerchantId = config('services.kashier.merchant_id');
        $this->kashierMode = config('services.kashier.mode', 'test');
    }
    /**
     * عرض جميع الحزم المتاحة
     */
    public function index()
    {
        $student = Auth::guard('student')->user();
        
        if (!$student) {
            return redirect()->route('student.login')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        $bundles = DiscountCode::where('is_bundle', 1)
            ->where('is_active', 1)
            ->with('months')
            ->get()
            ->filter(function($bundle) {
                // التحقق من صلاحية الحزمة
                return $bundle->isValid(0)['valid'] && $bundle->months->count() > 0;
            });

        return view('front.bundles.index', compact('bundles'));
    }

    /**
     * عرض تفاصيل الحزمة
     */
    public function show($id)
    {
        $student = Auth::guard('student')->user();
        
        if (!$student) {
            return redirect()->route('student.login')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        $bundle = DiscountCode::with('months')->findOrFail($id);

        if (!$bundle->is_bundle) {
            return redirect()->route('bundles.index')->with('error', 'هذه ليست حزمة');
        }

        // التحقق من صلاحية الحزمة
        $validation = $bundle->isValid(0);
        
        return view('front.bundles.show', compact('bundle', 'validation'));
    }

    /**
     * عرض صفحة الدفع للحزمة
     */
    public function showPayment($bundle_id)
    {
        // التحقق من تفعيل الدفع الأونلاين
        if (!is_payment_method_enabled('online_payment')) {
            return redirect()->route('courses.index')->with('error', 'خدمة الحزم غير متاحة حالياً');
        }

        $student = Auth::guard('student')->user();
        
        if (!$student) {
            return redirect()->route('student.login')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        $bundle = DiscountCode::with('months')->findOrFail($bundle_id);

        if (!$bundle->is_bundle) {
            return redirect()->route('bundles.index')->with('error', 'هذه ليست حزمة');
        }

        // التحقق من صلاحية الحزمة
        $validation = $bundle->isValid(0);
        
        if (!$validation['valid']) {
            return redirect()->route('bundle.details', $bundle_id)->with('error', $validation['message']);
        }

        // التحقق من أن الطالب لم يشتري الحزمة من قبل
        $hasAllCourses = true;
        foreach ($bundle->months as $month) {
            $subscription = StudentSubscriptions::where('student_id', $student->id)
                ->where('month_id', $month->id)
                ->where('is_active', 1)
                ->first();
            
            if (!$subscription) {
                $hasAllCourses = false;
                break;
            }
        }

        if ($hasAllCourses) {
            return redirect()->route('bundles.index')->with('info', 'أنت مشترك بالفعل في جميع الكورسات الموجودة في هذه الحزمة');
        }

        return view('front.bundles.payment', compact('bundle'));
    }

    /**
     * شراء الحزمة
     */
    public function purchaseBundle(Request $request, $bundle_id)
    {
        $student = Auth::guard('student')->user();
        
        if (!$student) {
            return redirect()->route('student.login')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        $bundle = DiscountCode::with('months')->findOrFail($bundle_id);

        if (!$bundle->is_bundle) {
            return redirect()->route('bundles.index')->with('error', 'هذه ليست حزمة');
        }

        // التحقق من صلاحية الحزمة
        $validation = $bundle->isValid(0);
        
        if (!$validation['valid']) {
            return redirect()->route('bundle.details', $bundle_id)->with('error', $validation['message']);
        }

        // التحقق من وجود كورسات في الحزمة
        if ($bundle->months->count() == 0) {
            return redirect()->back()->with('error', 'الحزمة لا تحتوي على أي كورسات');
        }

        $bundlePrice = $bundle->bundle_price ?? 0;

        if ($bundlePrice <= 0) {
            return redirect()->back()->with('error', 'سعر الحزمة غير صحيح');
        }

        // التحقق من إعدادات Kashier
        if (empty($this->kashierApiKey) || empty($this->kashierMerchantId)) {
            return redirect()->back()->with('error', 'خطأ في إعدادات الدفع. يرجى التواصل مع الإدارة.');
        }

        // إنشاء سجل دفع خاص بالحزمة (month_id = null للحزم)
        $kashierOrderId = 'BUNDLE_' . time() . '_' . Str::random(10);
        
        $payment = Payment::create([
            'student_id' => $student->id,
            'month_id' => null, // الحزمة ليست مرتبطة بكورس واحد
            'kashier_order_id' => $kashierOrderId,
            'amount' => $bundlePrice,
            'original_amount' => $bundlePrice,
            'discount_amount' => 0,
            'discount_code_id' => $bundle_id,
            'currency' => 'EGP',
            'status' => 'pending',
        ]);

        // زيادة عدد الاستخدامات
        $bundle->incrementUsage();

        // Use Kashier Payment Service (Official Demo Code) - Direct Redirect
        try {
            $kashierService = new \App\Services\KashierPaymentService();
            
            // Generate payment URL for direct redirect
            $callbackUrl = route('payment.callback');
            $additionalParams = [
                'redirectUrl' => $callbackUrl,
                'merchantRedirect' => $callbackUrl,
                'mode' => $this->kashierMode,
                'displayLang' => 'ar',
                'allowedMethods' => 'card,wallet',
            ];
            
            $paymentUrl = $kashierService->generatePaymentUrl(
                $payment->kashier_order_id,
                $bundlePrice,
                'EGP',
                $student->id,
                $additionalParams
            );
            
            \Log::info('Kashier Payment URL Generated for Bundle', [
                'payment_id' => $payment->id,
                'order_id' => $payment->kashier_order_id,
                'amount' => $bundlePrice,
                'payment_url' => $paymentUrl
            ]);
            
            // Direct redirect to Kashier payment page
            return redirect($paymentUrl);
            
        } catch (\Exception $e) {
            \Log::error('Kashier Payment Error for Bundle', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'حدث خطأ أثناء معالجة الدفع: ' . $e->getMessage());
        }
    }

    /**
     * عرض صفحة تفعيل الحزمة بكود التفعيل
     */
    public function showActivateForm($bundle_id)
    {
        $student = Auth::guard('student')->user();
        
        if (!$student) {
            return redirect()->route('student.login')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        $bundle = DiscountCode::with('months')->findOrFail($bundle_id);

        if (!$bundle->is_bundle) {
            return redirect()->route('bundles.index')->with('error', 'هذه ليست حزمة');
        }

        // التحقق من صلاحية الحزمة
        $validation = $bundle->isValid(0);
        
        if (!$validation['valid']) {
            return redirect()->route('bundle.details', $bundle_id)->with('error', $validation['message']);
        }

        return view('front.bundles.activate', compact('bundle'));
    }

    /**
     * تفعيل الحزمة باستخدام كود التفعيل
     */
    public function activateWithCode(Request $request, $bundle_id)
    {
        $request->validate([
            'activation_code' => 'required|string|max:50',
        ], [
            'activation_code.required' => 'يرجى إدخال كود التفعيل',
            'activation_code.max' => 'كود التفعيل غير صحيح',
        ]);

        $student = Auth::guard('student')->user();
        
        if (!$student) {
            return redirect()->route('student.login')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        $bundle = DiscountCode::with('months')->findOrFail($bundle_id);

        if (!$bundle->is_bundle) {
            return redirect()->route('bundles.index')->with('error', 'هذه ليست حزمة');
        }

        // التحقق من صلاحية الحزمة
        $validation = $bundle->isValid(0);
        
        if (!$validation['valid']) {
            return redirect()->route('bundle.details', $bundle_id)->with('error', $validation['message']);
        }

        $code = strtoupper(trim($request->activation_code));

        // البحث عن كود التفعيل
        $activationCode = ActivationCode::where('code', $code)
            ->where('bundle_id', $bundle_id)
            ->where('is_active', 1)
            ->first();

        if (!$activationCode) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'كود التفعيل غير صحيح أو غير متطابق مع هذه الحزمة');
        }

        // التحقق من صلاحية الكود
        $validation = $activationCode->isValid();
        
        if (!$validation['valid']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $validation['message']);
        }

        // التحقق من أن الطالب لم يفعّل الحزمة من قبل
        $hasAllCourses = true;
        foreach ($bundle->months as $month) {
            $subscription = StudentSubscriptions::where('student_id', $student->id)
                ->where('month_id', $month->id)
                ->where('is_active', 1)
                ->first();
            
            if (!$subscription) {
                $hasAllCourses = false;
                break;
            }
        }

        if ($hasAllCourses) {
            return redirect()->route('bundles.index')
                ->with('info', 'أنت مشترك بالفعل في جميع الكورسات الموجودة في هذه الحزمة');
        }

        DB::beginTransaction();
        
        try {
            // تفعيل الكود للطالب
            $activationCode->update([
                'student_id' => $student->id,
                'used_at' => now(),
            ]);

            $activatedCount = 0;
            
            // تفعيل جميع الكورسات في الحزمة
            foreach ($bundle->months as $month) {
                $existingSubscription = StudentSubscriptions::where('student_id', $student->id)
                    ->where('month_id', $month->id)
                    ->first();

                if ($existingSubscription) {
                    // إذا كان موجود، فعّله فقط
                    if (!$existingSubscription->is_active) {
                        $existingSubscription->update(['is_active' => 1]);
                        $activatedCount++;
                        
                        // إرسال إشعار
                        try {
                            \App\Services\NotificationService::notifyCourseActivated($existingSubscription);
                        } catch (\Exception $e) {
                            \Log::error('Error sending bundle activation notification', [
                                'subscription_id' => $existingSubscription->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                } else {
                    // إنشاء اشتراك جديد
                    $newSubscription = StudentSubscriptions::create([
                        'student_id' => $student->id,
                        'month_id' => $month->id,
                        'first_name' => $student->first_name,
                        'second_name' => $student->second_name,
                        'third_name' => $student->third_name,
                        'forth_name' => $student->forth_name,
                        'grade' => $student->grade,
                        'is_active' => 1,
                    ]);
                    
                    $activatedCount++;
                    
                    // إرسال إشعار
                    try {
                        \App\Services\NotificationService::notifyCourseActivated($newSubscription);
                    } catch (\Exception $e) {
                        \Log::error('Error sending bundle activation notification', [
                            'subscription_id' => $newSubscription->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            DB::commit();

            \Log::info('Bundle activated with code', [
                'bundle_id' => $bundle_id,
                'student_id' => $student->id,
                'code' => $code,
                'activated_courses' => $activatedCount
            ]);

            return redirect()->route('courses.index')
                ->with('success', "تم تفعيل الحزمة بنجاح! تم تفعيل {$activatedCount} كورس");

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error activating bundle with code', [
                'bundle_id' => $bundle_id,
                'student_id' => $student->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تفعيل الحزمة. يرجى المحاولة مرة أخرى.');
        }
    }

}


