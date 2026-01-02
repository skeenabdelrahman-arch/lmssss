<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Month;
use App\Models\Payment;
use App\Models\StudentSubscriptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    // Kashier API Configuration
    private $kashierApiKey;
    private $kashierMerchantId;
    private $kashierMode; // test or live

    public function __construct()
    {
        $this->kashierApiKey = config('services.kashier.api_key');
        $this->kashierMerchantId = config('services.kashier.merchant_id');
        $this->kashierMode = config('services.kashier.mode', 'test');
        
        // Validate configuration
        $secretKey = config('services.kashier.secret_key');
        if (empty($secretKey) && empty($this->kashierApiKey)) {
            \Log::warning('Kashier payment gateway is not configured. Please add KASHIER_SECRET_KEY and KASHIER_MERCHANT_ID to .env file');
        }
        if (empty($this->kashierMerchantId)) {
            \Log::warning('Kashier Merchant ID is missing. Please add KASHIER_MERCHANT_ID to .env file');
        }
    }

    /**
     * Show payment page for a course
     */
    public function showPayment($month_id)
    {
        $student = Auth::guard('student')->user();
        $month = Month::findOrFail($month_id);

        // Check if already subscribed
        $subscription = StudentSubscriptions::where('student_id', $student->id)
            ->where('month_id', $month_id)
            ->where('is_active', 1)
            ->first();

        if ($subscription) {
            return redirect()->route('month.content', ['month_id' => $month_id])
                ->with('info', 'أنت مشترك بالفعل في هذا الكورس');
        }

        // Check if there's a pending payment
        $pendingPayment = Payment::where('student_id', $student->id)
            ->where('month_id', $month_id)
            ->where('status', 'pending')
            ->first();

        // Check if Kashier is configured
        $kashierConfigured = !empty($this->kashierApiKey) && !empty($this->kashierMerchantId);

        return view('front.payment.payment', compact('month', 'student', 'pendingPayment', 'kashierConfigured'));
    }

    /**
     * Initiate payment with Kashier
     */
    public function initiatePayment(Request $request, $month_id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'discount_code' => 'nullable|string',
        ]);

        $student = Auth::guard('student')->user();
        $month = Month::findOrFail($month_id);

        // Check if already subscribed
        $subscription = StudentSubscriptions::where('student_id', $student->id)
            ->where('month_id', $month_id)
            ->where('is_active', 1)
            ->first();

        if ($subscription) {
            return redirect()->back()->with('error', 'أنت مشترك بالفعل في هذا الكورس');
        }

        $originalAmount = $month->price;
        $discountAmount = 0;
        $discountCodeId = null;
        $finalAmount = $originalAmount;

        // Apply discount code if provided
        if ($request->discount_code) {
            $discountCode = \App\Models\DiscountCode::where('code', strtoupper($request->discount_code))->first();
            
            if ($discountCode) {
                $validation = $discountCode->isValid($originalAmount);
                
                if ($validation['valid']) {
                    $discountAmount = $discountCode->calculateDiscount($originalAmount);
                    $finalAmount = $originalAmount - $discountAmount;
                    $discountCodeId = $discountCode->id;
                } else {
                    return redirect()->back()->with('error', $validation['message']);
                }
            } else {
                return redirect()->back()->with('error', 'كود الخصم غير صحيح');
            }
        }

        // Validate Kashier configuration before creating payment
        if (empty($this->kashierApiKey) || empty($this->kashierMerchantId)) {
            \Log::error('Kashier payment gateway is not configured. Missing API Key or Merchant ID.');
            return redirect()->back()->with('error', 'خطأ في إعدادات الدفع. يرجى التواصل مع الإدارة أو التحقق من إعدادات Kashier في لوحة التحكم.');
        }

        // Create payment record
        $kashierOrderId = 'ORDER_' . time() . '_' . Str::random(10);
        
        $payment = Payment::create([
            'student_id' => $student->id,
            'month_id' => $month_id,
            'kashier_order_id' => $kashierOrderId,
            'amount' => $finalAmount,
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'discount_code_id' => $discountCodeId,
            'currency' => 'EGP',
            'status' => 'pending',
        ]);

        // Increment discount code usage if used
        if ($discountCodeId) {
            \App\Models\DiscountCode::find($discountCodeId)->incrementUsage();
        }

        // Use Kashier Payment Service (Official Demo Code) - Direct Redirect
        try {
$merchantId = config('services.kashier.merchant_id');
$secretKey  = config('services.kashier.secret_key');

$orderId  = $payment->kashier_order_id;
$amount   = number_format($finalAmount, 2, '.', '');
$currency = 'EGP';

$hash = hash(
    'sha256',
    $merchantId . $orderId . $amount . $currency . $secretKey
);

$baseUrl = config('services.kashier.mode') === 'live'
    ? 'https://checkout.kashier.io'
    : 'https://checkout.kashier.io/test';

$paymentUrl = $baseUrl . '?' . http_build_query([
    'merchantId'        => $merchantId,
    'orderId'           => $orderId,
    'amount'            => $amount,
    'currency'          => $currency,
    'hash'              => $hash,
    'merchantName'      => 'SkEdu',
'redirectUrl' => 'https://samehsalah.com/payment/callback',
'merchantRedirect' => 'https://samehsalah.com/payment/callback',
    'displayLang'       => 'ar',
]);

return redirect($paymentUrl);
            
        } catch (\Exception $e) {
            \Log::error('Kashier Payment Error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'حدث خطأ أثناء معالجة الدفع: ' . $e->getMessage());
        }
    }


    /**
     * Verify Payment using Kashier Payment Service
     */
public function verifyPayment(Request $request, $payment = null)
{
    \Log::info('Payment Verification Request', [
        'payment_param' => $payment,
        'request_all' => $request->all()
    ]);

    // 1️⃣ حاول تجيب الـ payment من الـ ID لو موجود
    if ($payment) {
        $paymentRecord = Payment::find($payment);
    } else {
        // 2️⃣ حاول تجيب من orderId أو payment_id اللي راجع من Kashier
        $orderId = $request->input('orderId') ?? $request->input('payment_id');
        if ($orderId) {
            $paymentRecord = Payment::where('kashier_order_id', $orderId)->first();
        } else {
            // 3️⃣ لو مفيش params، خد آخر payment pending للطالب (حل للمشكلة اللي حصلت معاك)
            $student = Auth::guard('student')->user();
            $paymentRecord = Payment::where('student_id', $student->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if (!$paymentRecord) {
                \Log::error('Payment Verification: No payment ID, order ID or pending payment found', [
                    'request' => $request->all()
                ]);
                return redirect()->route('courses.index')->with('error', 'خطأ في بيانات الدفع');
            }
        }
    }

    if (!$paymentRecord) {
        \Log::error('Payment Verification: Payment not found', ['request' => $request->all()]);
        return redirect()->route('courses.index')->with('error', 'لم يتم العثور على عملية الدفع');
    }

    // 4️⃣ استخدم خدمة Kashier للتحقق
    try {
        $kashierService = new \App\Services\KashierPaymentService();
        $verification = $kashierService->verifyPayment($request);

        \Log::info('Payment Verification Result', [
            'payment_id' => $paymentRecord->id,
            'verification' => $verification
        ]);

        if (!empty($verification['success']) && $verification['success'] === true) {
            // الدفع ناجح
            if ($paymentRecord->status !== 'paid') {
                $paymentRecord->update([
                    'status' => 'paid',
                    'payment_id' => $verification['payment_id'] ?? $paymentRecord->kashier_order_id,
                    'paid_at' => now(),
                    'payment_method' => $request->input('paymentMethod', 'card'),
                    'kashier_response' => $request->all(),
                ]);

                // تفعيل الاشتراك تلقائي
                $this->activateSubscription($paymentRecord);

                \Log::info('Payment verified and activated', ['payment_id' => $paymentRecord->id]);
            }

            return redirect()->route('payment.success', $paymentRecord->id);
        } else {
            // الدفع فشل
            $paymentRecord->update([
                'status' => 'failed',
                'kashier_response' => $request->all(),
            ]);

            \Log::info('Payment verification failed', [
                'payment_id' => $paymentRecord->id,
                'message' => $verification['message'] ?? 'Payment verification failed'
            ]);

            return redirect()->route('payment.fail', $paymentRecord->id);
        }
    } catch (\Exception $e) {
        \Log::error('Payment Verification Error', [
            'payment_id' => $paymentRecord->id ?? null,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->route('courses.index')->with('error', 'حدث خطأ أثناء التحقق من الدفع');
    }
}

    /**
     * Payment Callback from Kashier - Uses verifyPayment method
     */
public function callback(Request $request)
{
    // إذا الكاشير ما رجعش أي params، حاول ناخد آخر pending payment للطالب
    $student = Auth::guard('student')->user();
    $payment = Payment::where('student_id', $student->id)
        ->where('status', 'pending')
        ->latest()
        ->first();

    if (!$payment) {
        \Log::error('Payment Callback: No pending payment found for student', ['student_id' => $student->id]);
        return redirect()->route('courses.index')->with('error', 'لم يتم العثور على عملية دفع للمتابعة');
    }

    // تابع التحقق من الدفع باستخدام Webhook أو manual verify
    return $this->verifyPayment($request, $payment->id);
}

    /**
     * Webhook from Kashier
     */
    public function webhook(Request $request)
    {
        \Log::info('Kashier Webhook received', $request->all());
        
        // Verify webhook signature if provided
        $signature = $request->header('X-Kashier-Signature');
        
        // Process webhook data
        $orderId = $request->input('orderId');
        $paymentStatus = $request->input('paymentStatus');
        $paymentId = $request->input('paymentId');

        if (!$orderId) {
            \Log::error('Kashier Webhook: Missing orderId');
            return response()->json(['status' => 'error', 'message' => 'Missing orderId'], 400);
        }

        $payment = Payment::where('kashier_order_id', $orderId)->first();

        if (!$payment) {
            \Log::error('Kashier Webhook: Payment not found', ['orderId' => $orderId]);
            return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
        }

        // Verify signature if provided
        if ($signature && !empty($this->kashierApiKey)) {
            $expectedSignature = hash_hmac('sha256', $orderId . $paymentStatus . $paymentId, $this->kashierApiKey);
            if ($signature !== $expectedSignature) {
                \Log::warning('Kashier Webhook: Invalid signature', ['orderId' => $orderId]);
                // Don't return error, just log it
            }
        }

        if (($paymentStatus === 'SUCCESS' || $paymentStatus === 'success') && $payment->status !== 'paid') {
            $payment->update([
                'status' => 'paid',
                'payment_id' => $paymentId,
                'paid_at' => now(),
                'payment_method' => $request->input('paymentMethod', 'card'),
                'kashier_response' => $request->all(),
            ]);

            // Activate subscription automatically
            $this->activateSubscription($payment);
            
            \Log::info('Payment activated via webhook', ['payment_id' => $payment->id, 'orderId' => $orderId]);
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Activate subscription after successful payment
     */
    private function activateSubscription($payment)
    {
        // إذا كانت الحزمة (month_id = null)، فعّل جميع الكورسات المرتبطة بالحزمة فقط
        if ($payment->month_id === null && $payment->discount_code_id) {
            $discountCode = \App\Models\DiscountCode::with('months')->find($payment->discount_code_id);
            
            if ($discountCode && $discountCode->is_bundle && $discountCode->hasMonths()) {
                $activatedMonths = $discountCode->activateMonthsForStudent($payment->student_id);
                
                \Log::info('Bundle months activated', [
                    'payment_id' => $payment->id,
                    'discount_code_id' => $discountCode->id,
                    'activated_months' => count($activatedMonths),
                    'student_id' => $payment->student_id,
                ]);
                
                // إرسال إشعارات للطلاب عند تفعيل الحزمة
                foreach ($activatedMonths as $subscription) {
                    try {
                        \App\Services\NotificationService::notifyCourseActivated($subscription);
                    } catch (\Exception $e) {
                        \Log::error('Error sending course activation notification', ['subscription_id' => $subscription->id, 'error' => $e->getMessage()]);
                    }
                }
                
                return true; // تم التفعيل
            }
        } else {
            // تفعيل الكورس المدفوع
            $subscription = StudentSubscriptions::updateOrCreate(
                [
                    'student_id' => $payment->student_id,
                    'month_id' => $payment->month_id,
                ],
                [
                    'grade' => $payment->student->grade,
                    'is_active' => 1,
                ]
            );

            // إرسال إشعار للطالب عند تفعيل الكورس
            try {
                \App\Services\NotificationService::notifyCourseActivated($subscription);
            } catch (\Exception $e) {
                \Log::error('Error sending course activation notification', ['subscription_id' => $subscription->id, 'error' => $e->getMessage()]);
            }

            // إذا كان هناك كود خصم مرتبط به كورسات، قم بتفعيلها أيضاً
            if ($payment->discount_code_id) {
                $discountCode = \App\Models\DiscountCode::with('months')->find($payment->discount_code_id);
                
                if ($discountCode && $discountCode->hasMonths() && !$discountCode->is_bundle) {
                    $activatedMonths = $discountCode->activateMonthsForStudent($payment->student_id);
                    
                    \Log::info('Discount code months activated', [
                        'payment_id' => $payment->id,
                        'discount_code_id' => $discountCode->id,
                        'activated_months' => count($activatedMonths),
                        'student_id' => $payment->student_id,
                    ]);
                    
                    // إرسال إشعارات للطلاب عند تفعيل الكورسات
                    foreach ($activatedMonths as $activatedSubscription) {
                        try {
                            \App\Services\NotificationService::notifyCourseActivated($activatedSubscription);
                        } catch (\Exception $e) {
                            \Log::error('Error sending course activation notification', ['subscription_id' => $activatedSubscription->id, 'error' => $e->getMessage()]);
                        }
                    }
                }
            }

            return $subscription;
        }
    }

    /**
     * Payment Success Page
     */
    public function success($payment_id)
    {
        $payment = Payment::with(['student', 'month'])->findOrFail($payment_id);
        
        if ($payment->status !== 'paid') {
            return redirect()->route('payment.fail', $payment_id);
        }

        return view('front.payment.success', compact('payment'));
    }

    /**
     * Payment Fail Page
     */
    public function fail($payment_id)
    {
        $payment = Payment::with(['student', 'month'])->findOrFail($payment_id);
        
        return view('front.payment.fail', compact('payment'));
    }

    /**
     * Payment History for Student
     */
    public function history()
    {
        $student = Auth::guard('student')->user();
        
        $payments = Payment::where('student_id', $student->id)
            ->with(['month', 'discountCode.months'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Statistics
        $stats = [
            'total_payments' => Payment::where('student_id', $student->id)->count(),
            'paid_payments' => Payment::where('student_id', $student->id)->where('status', 'paid')->count(),
            'pending_payments' => Payment::where('student_id', $student->id)->where('status', 'pending')->count(),
            'total_spent' => Payment::where('student_id', $student->id)->where('status', 'paid')->sum('amount'),
            'total_discounts' => Payment::where('student_id', $student->id)->where('status', 'paid')->sum('discount_amount'),
        ];
        
        return view('front.payment.history', compact('payments', 'stats'));
    }
}
