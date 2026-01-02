@extends('back_layouts.master')
@section('css')

@section('title')
    متابعة المدفوعات
@stop
@endsection
@section('page-header')
<div class="page-header-modern">
    <h4><i class="fas fa-money-bill-wave me-2"></i> متابعة المدفوعات</h4>
</div>
@endsection
@section('content')
<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card primary">
            <div class="stat-icon primary">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">إجمالي المدفوعات</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card success">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-value">{{ $stats['paid'] }}</div>
            <div class="stat-label">مدفوعة</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card warning">
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-value">{{ $stats['pending'] }}</div>
            <div class="stat-label">معلقة</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card info">
            <div class="stat-icon info">
                <i class="fas fa-money-bill"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_amount'], 2) }}</div>
            <div class="stat-label">إجمالي المبلغ (ج.م)</div>
        </div>
    </div>
</div>

<!-- Additional Statistics -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="stat-icon" style="color: white;">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-value" style="color: white;">{{ number_format($stats['today_amount'] ?? 0, 2) }}</div>
            <div class="stat-label" style="color: rgba(255,255,255,0.9);">مبيعات اليوم (ج.م)</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="stat-icon" style="color: white;">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-value" style="color: white;">{{ number_format($stats['month_amount'] ?? 0, 2) }}</div>
            <div class="stat-label" style="color: rgba(255,255,255,0.9);">مبيعات الشهر (ج.م)</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="stat-icon" style="color: white;">
                <i class="fas fa-tag"></i>
            </div>
            <div class="stat-value" style="color: white;">{{ number_format($stats['total_discounts'] ?? 0, 2) }}</div>
            <div class="stat-label" style="color: rgba(255,255,255,0.9);">إجمالي الخصومات (ج.م)</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <div class="stat-icon" style="color: white;">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-value" style="color: white;">{{ number_format($stats['total_original'] ?? 0, 2) }}</div>
            <div class="stat-label" style="color: rgba(255,255,255,0.9);">إجمالي الأصلي (ج.م)</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="modern-card mb-4">
    <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-3">
        <div class="col-md-2">
            <label class="form-label">الحالة:</label>
            <select name="status" class="form-select">
                <option value="">جميع الحالات</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فاشلة</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">الكورس:</label>
            <select name="month_id" class="form-select">
                <option value="">جميع الكورسات</option>
                @foreach($months as $month)
                    <option value="{{ $month->id }}" {{ request('month_id') == $month->id ? 'selected' : '' }}>
                        {{ $month->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">من تاريخ:</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">إلى تاريخ:</label>
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">البحث:</label>
            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="اسم، تليفون، كود طلب...">
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="submit" class="btn btn-modern btn-modern-primary w-100">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>
    <div class="mt-3">
        <a href="{{ route('admin.payments.export', request()->all()) }}" class="btn btn-modern-success">
            <i class="fas fa-file-excel me-2"></i> تصدير Excel
        </a>
        <a href="{{ route('admin.payments.statistics') }}" class="btn btn-modern-info">
            <i class="fas fa-chart-line me-2"></i> الإحصائيات
        </a>
    </div>
</div>

<!-- Payments Table -->
<div class="modern-card">
    <div class="modern-table">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الطالب</th>
                    <th>الكورس</th>
                    <th>المبلغ الأصلي</th>
                    <th>الخصم</th>
                    <th>المبلغ النهائي</th>
                    <th>الحالة</th>
                    <th>رقم الطلب</th>
                    <th>تاريخ الدفع</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $payment->student->first_name }} {{ $payment->student->second_name }}</strong><br>
                        <small class="text-muted">{{ $payment->student->student_phone }}</small>
                    </td>
                    <td>{{ $payment->month ? $payment->month->name : 'شهر محذوف' }}</td>
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
                            @if($payment->discountCode)
                                <br><small class="badge bg-info">{{ $payment->discountCode->code }}</small>
                            @endif
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
                        @if($payment->status == 'paid')
                            <span class="badge-modern badge-modern-success">
                                <i class="fas fa-check"></i> مدفوعة
                            </span>
                        @elseif($payment->status == 'pending')
                            <span class="badge-modern badge-modern-warning">
                                <i class="fas fa-clock"></i> معلقة
                            </span>
                        @else
                            <span class="badge-modern badge-modern-danger">
                                <i class="fas fa-times"></i> فاشلة
                            </span>
                        @endif
                    </td>
                    <td><small>{{ $payment->kashier_order_id }}</small></td>
                    <td>
                        @if($payment->paid_at)
                            {{ $payment->paid_at->format('Y-m-d H:i') }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-modern btn-modern-info">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-5">
                        <i class="fas fa-inbox" style="font-size: 48px; color: #ddd; margin-bottom: 15px;"></i>
                        <p>لا توجد مدفوعات</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $payments->links() }}
    </div>
</div>
@endsection
@section('js')

@endsection

