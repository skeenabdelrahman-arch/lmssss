@extends('front.layouts.app')
@section('title')
دفع الحزمة - {{ $bundle->name ?? $bundle->code }}
@endsection

@section('content')
<section class="feature-section oh pos-rel padding-bottom-2 pb-xl-0" style="margin-top: 200px;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg" style="border-radius: 20px; border: none;">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-gift" style="font-size: 60px; color: #1976d2; margin-bottom: 20px;"></i>
                            <h2 style="color: #1976d2; font-weight: bold;">دفع الحزمة التعليمية</h2>
                            <p class="text-muted">
                                <i class="fas fa-shield-alt me-2"></i>
                                الدفع آمن ومشفر عبر بوابة Kashier
                            </p>
                        </div>

                        <div class="payment-details mb-4" style="background: #f5f5f5; padding: 25px; border-radius: 15px;">
                            <h4 style="color: #424242; margin-bottom: 20px;">
                                <i class="fas fa-gift me-2"></i> تفاصيل الحزمة
                            </h4>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <strong>اسم الحزمة:</strong>
                                </div>
                                <div class="col-6 text-end">
                                    {{ $bundle->name ?? $bundle->code }}
                                </div>
                            </div>
                            <hr>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <strong>عدد الكورسات:</strong>
                                </div>
                                <div class="col-6 text-end">
                                    {{ $bundle->months->count() }} كورس
                                </div>
                            </div>
                            <hr>
                            @php
                                $originalPrice = $bundle->getOriginalBundlePrice();
                                $bundlePrice = $bundle->bundle_price;
                            @endphp
                            @if($originalPrice > $bundlePrice)
                            <div class="row mb-3">
                                <div class="col-6">
                                    <strong>السعر الأصلي:</strong>
                                </div>
                                <div class="col-6 text-end">
                                    <span style="font-size: 20px; color: #666; text-decoration: line-through;">
                                        {{ number_format($originalPrice, 2) }} ج.م
                                    </span>
                                </div>
                            </div>
                            <hr>
                            @endif
                            <div class="row">
                                <div class="col-6">
                                    <strong>المبلغ النهائي:</strong>
                                </div>
                                <div class="col-6 text-end">
                                    <span style="font-size: 28px; color: #1976d2; font-weight: bold;">
                                        {{ number_format($bundlePrice, 2) }} ج.م
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>ملاحظة:</strong> بعد إتمام الدفع، سيتم تفعيل جميع الكورسات في الحزمة تلقائياً.
                        </div>

                        <form method="POST" action="{{ route('payment.bundle.purchase', $bundle->id) }}">
                            @csrf
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-lg" style="background: linear-gradient(135deg, #1976d2, #1565c0); color: white; padding: 15px 50px; border-radius: 50px; font-size: 20px; font-weight: bold; border: none; box-shadow: 0 4px 15px rgba(25, 118, 210, 0.3);">
                                    <i class="fas fa-credit-card me-2"></i> ادفع الآن
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

