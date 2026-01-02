@extends('front.layouts.app')
@section('title')
سجل المدفوعات
@endsection
@section('content')
<section class="feature-section oh pos-rel padding-bottom-2 pb-xl-0" style="margin-top: 200px;">
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-12">
                <h2 style="color: #1976d2; font-weight: bold; margin-bottom: 30px;">
                    <i class="fas fa-history me-2"></i> سجل المدفوعات
                </h2>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm" style="border-radius: 15px; border: none;">
                    <div class="card-body text-center">
                        <i class="fas fa-receipt" style="font-size: 40px; color: #1976d2; margin-bottom: 10px;"></i>
                        <h3 style="color: #1976d2; font-weight: bold;">{{ $stats['total_payments'] }}</h3>
                        <p class="text-muted mb-0">إجمالي المدفوعات</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm" style="border-radius: 15px; border: none;">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle" style="font-size: 40px; color: #28a745; margin-bottom: 10px;"></i>
                        <h3 style="color: #28a745; font-weight: bold;">{{ $stats['paid_payments'] }}</h3>
                        <p class="text-muted mb-0">مدفوعة</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm" style="border-radius: 15px; border: none;">
                    <div class="card-body text-center">
                        <i class="fas fa-money-bill-wave" style="font-size: 40px; color: #ffc107; margin-bottom: 10px;"></i>
                        <h3 style="color: #ffc107; font-weight: bold;">{{ number_format($stats['total_spent'], 2) }}</h3>
                        <p class="text-muted mb-0">إجمالي المدفوع (ج.م)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm" style="border-radius: 15px; border: none;">
                    <div class="card-body text-center">
                        <i class="fas fa-tag" style="font-size: 40px; color: #17a2b8; margin-bottom: 10px;"></i>
                        <h3 style="color: #17a2b8; font-weight: bold;">{{ number_format($stats['total_discounts'], 2) }}</h3>
                        <p class="text-muted mb-0">إجمالي الخصومات (ج.م)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="card shadow-lg" style="border-radius: 20px; border: none;">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th>#</th>
                                <th>الكورس</th>
                                <th>المبلغ الأصلي</th>
                                <th>الخصم</th>
                                <th>المبلغ النهائي</th>
                                <th>كود الخصم</th>
                                <th>الحالة</th>
                                <th>تاريخ الدفع</th>
                                <th>رقم الطلب</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($payment->month)
                                        <strong>{{ $payment->month->name }}</strong><br>
                                        <small class="text-muted">{{ $payment->month->grade }}</small>
                                    @elseif($payment->discountCode && $payment->discountCode->is_bundle)
                                        <strong>{{ $payment->discountCode->name ?? $payment->discountCode->code }}</strong><br>
                                        <small class="text-muted">
                                            <i class="fas fa-gift me-1"></i>
                                            حزمة ({{ $payment->discountCode->months->count() ?? 0 }} كورسات)
                                        </small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span style="text-decoration: line-through; color: #999;">
                                        {{ number_format($payment->original_amount ?? $payment->amount, 2) }} ج.م
                                    </span>
                                </td>
                                <td>
                                    @if($payment->discount_amount > 0)
                                        <span style="color: #28a745; font-weight: bold;">
                                            -{{ number_format($payment->discount_amount, 2) }} ج.م
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <strong style="color: #1976d2; font-size: 18px;">
                                        {{ number_format($payment->amount, 2) }} ج.م
                                    </strong>
                                </td>
                                <td>
                                    @if($payment->discountCode)
                                        <span class="badge bg-info">{{ $payment->discountCode->code }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($payment->status == 'paid')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> مدفوعة
                                        </span>
                                    @elseif($payment->status == 'pending')
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock"></i> معلقة
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times"></i> فاشلة
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($payment->paid_at)
                                        {{ $payment->paid_at->format('Y-m-d H:i') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $payment->kashier_order_id }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-inbox" style="font-size: 60px; color: #ddd; margin-bottom: 15px;"></i>
                                    <p class="text-muted">لا توجد مدفوعات حتى الآن</p>
                                    <a href="{{ route('courses.index') }}" class="btn btn-primary">
                                        <i class="fas fa-shopping-cart me-2"></i> تصفح الكورسات
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($payments->hasPages())
                <div class="mt-4">
                    {{ $payments->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection




