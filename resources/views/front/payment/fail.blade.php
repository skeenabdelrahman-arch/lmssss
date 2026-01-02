@extends('front.layouts.app')
@section('title')
فشل الدفع
@endsection
@section('content')
<section class="feature-section oh pos-rel padding-bottom-2 pb-xl-0" style="margin-top: 200px;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-lg text-center" style="border-radius: 20px; border: none;">
                    <div class="card-body p-5">
                        <div class="mb-4">
                            <i class="fas fa-times-circle" style="font-size: 100px; color: #dc3545;"></i>
                        </div>
                        <h2 style="color: #dc3545; font-weight: bold; margin-bottom: 20px;">
                            فشل عملية الدفع
                        </h2>
                        <p style="font-size: 18px; color: #666; margin-bottom: 30px;">
                            لم يتم إتمام عملية الدفع. يرجى المحاولة مرة أخرى
                        </p>

                        <div class="mt-4">
                            <a href="{{ route('payment.show', $payment->month_id) }}" class="btn btn-lg" style="background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%); color: white; padding: 15px 40px; border-radius: 50px; font-size: 18px; font-weight: bold; text-decoration: none; box-shadow: 0 4px 15px rgba(25, 118, 210, 0.3);">
                                <i class="fas fa-redo me-2"></i> المحاولة مرة أخرى
                            </a>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('courses.index') }}" style="color: #666; text-decoration: none;">
                                <i class="fas fa-arrow-right me-2"></i> العودة للكورسات
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

