@extends('front.layouts.app')
@section('title')
تم الدفع بنجاح
@endsection
@section('content')
<section class="feature-section oh pos-rel padding-bottom-2 pb-xl-0" style="margin-top: 200px;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-lg text-center" style="border-radius: 20px; border: none;">
                    <div class="card-body p-5">
                        <div class="mb-4">
                            <i class="fas fa-check-circle" style="font-size: 100px; color: #28a745;"></i>
                        </div>
                        <h2 style="color: #28a745; font-weight: bold; margin-bottom: 20px;">
                            تم الدفع بنجاح! 🎉
                        </h2>
                        <p style="font-size: 18px; color: #666; margin-bottom: 30px;">
                            تم تفعيل اشتراكك في الكورس تلقائياً
                        </p>

                        <div class="payment-info mb-4" style="background: #f5f5f5; padding: 20px; border-radius: 15px; text-align: right;">
                            <div class="row mb-2">
                                <div class="col-6"><strong>الكورس:</strong></div>
                                <div class="col-6">{{ $payment->month->name }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6"><strong>المبلغ:</strong></div>
                                <div class="col-6">{{ number_format($payment->amount, 2) }} ج.م</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6"><strong>رقم الطلب:</strong></div>
                                <div class="col-6"><small>{{ $payment->kashier_order_id }}</small></div>
                            </div>
                            <div class="row">
                                <div class="col-6"><strong>تاريخ الدفع:</strong></div>
                                <div class="col-6">{{ $payment->paid_at->format('Y-m-d H:i') }}</div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('month.content', $payment->month_id) }}" class="btn btn-lg" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 15px 40px; border-radius: 50px; font-size: 18px; font-weight: bold; text-decoration: none; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);">
                                <i class="fas fa-play me-2"></i> ابدأ التعلم الآن
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

