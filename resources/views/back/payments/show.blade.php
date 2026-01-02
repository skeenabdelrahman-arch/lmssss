@extends('back_layouts.master')

@section('title')
تفاصيل الدفعة
@endsection

@section('content')
<div class="page-header">
    <h2><i class="fas fa-eye me-2"></i> تفاصيل الدفعة</h2>
    <a href="{{ route('admin.payments.index') }}" class="btn btn-modern-secondary">
        <i class="fas fa-arrow-right me-2"></i> العودة
    </a>
</div>

<div class="row g-4">
    <!-- Payment Details -->
    <div class="col-md-8">
        <div class="modern-card">
            <h5 class="mb-4"><i class="fas fa-info-circle me-2"></i> معلومات الدفعة</h5>
            
            <div class="row mb-3">
                <div class="col-md-4"><strong>رقم الطلب:</strong></div>
                <div class="col-md-8">
                    <code>{{ $payment->kashier_order_id }}</code>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4"><strong>رقم المعاملة:</strong></div>
                <div class="col-md-8">
                    {{ $payment->payment_id ?? '<span class="text-muted">-</span>' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4"><strong>الحالة:</strong></div>
                <div class="col-md-8">
                    @if($payment->status == 'paid')
                        <span class="badge bg-success">
                            <i class="fas fa-check"></i> مدفوعة
                        </span>
                    @elseif($payment->status == 'pending')
                        <span class="badge bg-warning">
                            <i class="fas fa-clock"></i> معلقة
                        </span>
                    @elseif($payment->status == 'failed')
                        <span class="badge bg-danger">
                            <i class="fas fa-times"></i> فاشلة
                        </span>
                    @elseif($payment->status == 'refunded')
                        <span class="badge bg-secondary">
                            <i class="fas fa-undo"></i> مسترجعة
                        </span>
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4"><strong>المبلغ الأصلي:</strong></div>
                <div class="col-md-8">
                    <strong style="font-size: 18px;">{{ number_format($payment->original_amount ?? $payment->amount, 2) }} ج.م</strong>
                </div>
            </div>

            @if($payment->discount_amount > 0)
            <div class="row mb-3">
                <div class="col-md-4"><strong>الخصم:</strong></div>
                <div class="col-md-8">
                    <span style="color: #28a745; font-weight: bold; font-size: 18px;">
                        -{{ number_format($payment->discount_amount, 2) }} ج.م
                    </span>
                    @if($payment->discountCode)
                        <span class="badge bg-info ms-2">{{ $payment->discountCode->code }}</span>
                    @endif
                </div>
            </div>
            @endif

            <div class="row mb-3">
                <div class="col-md-4"><strong>المبلغ النهائي:</strong></div>
                <div class="col-md-8">
                    <strong style="color: #1976d2; font-size: 24px;">{{ number_format($payment->amount, 2) }} ج.م</strong>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4"><strong>طريقة الدفع:</strong></div>
                <div class="col-md-8">
                    {{ $payment->payment_method ?? 'Kashier' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4"><strong>تاريخ الإنشاء:</strong></div>
                <div class="col-md-8">
                    {{ $payment->created_at->format('Y-m-d H:i:s') }}
                </div>
            </div>

            @if($payment->paid_at)
            <div class="row mb-3">
                <div class="col-md-4"><strong>تاريخ الدفع:</strong></div>
                <div class="col-md-8">
                    {{ $payment->paid_at->format('Y-m-d H:i:s') }}
                </div>
            </div>
            @endif
        </div>

        <!-- Student Information -->
        <div class="modern-card mt-4">
            <h5 class="mb-4"><i class="fas fa-user me-2"></i> معلومات الطالب</h5>
            
            <div class="row mb-3">
                <div class="col-md-4"><strong>الاسم:</strong></div>
                <div class="col-md-8">
                    {{ $payment->student->first_name }} {{ $payment->student->second_name }} 
                    {{ $payment->student->third_name }} {{ $payment->student->forth_name }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4"><strong>رقم الهاتف:</strong></div>
                <div class="col-md-8">{{ $payment->student->student_phone }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4"><strong>الصف:</strong></div>
                <div class="col-md-8">{{ $payment->student->grade }}</div>
            </div>

            <div class="row">
                <div class="col-md-4"><strong>الإجراءات:</strong></div>
                <div class="col-md-8">
                    <a href="{{ route('admin.student.profile', $payment->student->id) }}" class="btn btn-sm btn-modern-info">
                        <i class="fas fa-user-circle me-2"></i> عرض البروفايل
                    </a>
                </div>
            </div>
        </div>

        <!-- Course Information -->
        <div class="modern-card mt-4">
            <h5 class="mb-4"><i class="fas fa-book me-2"></i> معلومات الكورس</h5>
            
            <div class="row mb-3">
                <div class="col-md-4"><strong>اسم الكورس:</strong></div>
                <div class="col-md-8">{{ $payment->month ? $payment->month->name : 'شهر محذوف' }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4"><strong>الصف:</strong></div>
                <div class="col-md-8">{{ $payment->month->grade }}</div>
            </div>

            <div class="row">
                <div class="col-md-4"><strong>السعر:</strong></div>
                <div class="col-md-8">
                    <strong>{{ number_format($payment->month->price, 2) }} ج.م</strong>
                </div>
            </div>
        </div>

        <!-- Kashier Response (if available) -->
        @if($payment->kashier_response)
        <div class="modern-card mt-4">
            <h5 class="mb-4"><i class="fas fa-code me-2"></i> استجابة Kashier</h5>
            <pre style="background: #f8f9fa; padding: 15px; border-radius: 8px; max-height: 300px; overflow-y: auto;">{{ json_encode($payment->kashier_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
        @endif
    </div>

    <!-- Actions Sidebar -->
    <div class="col-md-4">
        <div class="modern-card">
            <h5 class="mb-4"><i class="fas fa-cog me-2"></i> الإجراءات</h5>
            
            @if($payment->status == 'paid')
            <form method="POST" action="{{ route('admin.payments.refund', $payment->id) }}" onsubmit="return confirm('هل أنت متأكد من استرجاع هذه الدفعة؟ سيتم إلغاء اشتراك الطالب تلقائياً.')">
                @csrf
                <div class="mb-3">
                    <label class="form-label">سبب الاسترجاع:</label>
                    <textarea name="refund_reason" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-modern-danger w-100">
                    <i class="fas fa-undo me-2"></i> استرجاع الدفعة
                </button>
            </form>
            @endif

            <div class="mt-4">
                <a href="{{ route('admin.payments.export', ['payment_id' => $payment->id]) }}" class="btn btn-modern-info w-100">
                    <i class="fas fa-download me-2"></i> تصدير كـ PDF
                </a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="modern-card mt-4">
            <h5 class="mb-4"><i class="fas fa-chart-bar me-2"></i> إحصائيات سريعة</h5>
            
            <div class="mb-3">
                <small class="text-muted">إجمالي دفعات الطالب:</small>
                <strong class="d-block">{{ \App\Models\Payment::where('student_id', $payment->student_id)->where('status', 'paid')->count() }}</strong>
            </div>

            <div class="mb-3">
                <small class="text-muted">إجمالي ما دفعه:</small>
                <strong class="d-block">{{ number_format(\App\Models\Payment::where('student_id', $payment->student_id)->where('status', 'paid')->sum('amount'), 2) }} ج.م</strong>
            </div>

            <div>
                <small class="text-muted">عدد اشتراكاته النشطة:</small>
                <strong class="d-block">{{ \App\Models\StudentSubscriptions::where('student_id', $payment->student_id)->where('is_active', 1)->count() }}</strong>
            </div>
        </div>
    </div>
</div>
@endsection
