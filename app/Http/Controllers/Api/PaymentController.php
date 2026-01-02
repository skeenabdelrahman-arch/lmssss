<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Month;
use App\Models\Payment;
use App\Models\StudentSubscriptions;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Get payment history
     */
    public function index(Request $request)
    {
        $student = $request->user();
        
        $payments = Payment::where('student_id', $student->id)
            ->with('month')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => (float)$payment->amount,
                    'status' => $payment->status,
                    'payment_method' => $payment->payment_method,
                    'course_name' => $payment->month->name ?? 'N/A',
                    'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                    'paid_at' => $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i:s') : null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    /**
     * Initiate payment
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'month_id' => 'required|exists:months,id',
            'discount_code' => 'nullable|string',
        ]);

        $student = $request->user();
        $month = Month::findOrFail($request->month_id);

        // Check if already subscribed
        $subscription = StudentSubscriptions::where('student_id', $student->id)
            ->where('month_id', $month->id)
            ->where('is_active', 1)
            ->first();

        if ($subscription) {
            return response()->json([
                'success' => false,
                'message' => 'أنت مشترك بالفعل في هذا الكورس'
            ], 400);
        }

        $originalAmount = $month->price;
        $discountAmount = 0;
        $finalAmount = $originalAmount;

        // Apply discount code if provided
        if ($request->discount_code) {
            $discountCode = \App\Models\DiscountCode::where('code', strtoupper($request->discount_code))->first();
            
            if ($discountCode) {
                $validation = $discountCode->isValid($originalAmount);
                
                if ($validation['valid']) {
                    $discountAmount = $discountCode->calculateDiscount($originalAmount);
                    $finalAmount = $originalAmount - $discountAmount;
                }
            }
        }

        // Create payment record
        $orderId = 'MOBILE_' . time() . '_' . \Str::random(10);
        
        $payment = Payment::create([
            'student_id' => $student->id,
            'month_id' => $month->id,
            'kashier_order_id' => $orderId,
            'amount' => $finalAmount,
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'currency' => 'EGP',
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء طلب الدفع',
            'data' => [
                'payment_id' => $payment->id,
                'order_id' => $orderId,
                'amount' => (float)$finalAmount,
                'payment_url' => route('payment.initiate', $month->id), // Redirect to web payment
            ]
        ]);
    }
}


