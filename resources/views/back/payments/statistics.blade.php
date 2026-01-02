@extends('back_layouts.master')

@section('title')
إحصائيات المدفوعات
@endsection

@section('content')
<div class="page-header">
    <h2><i class="fas fa-chart-line me-2"></i> إحصائيات المدفوعات</h2>
    <a href="{{ route('admin.payments.index') }}" class="btn btn-modern-secondary">
        <i class="fas fa-arrow-right me-2"></i> العودة
    </a>
</div>

<!-- Summary Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="modern-card text-center">
            <i class="fas fa-calendar-day" style="font-size: 40px; color: #667eea; margin-bottom: 15px;"></i>
            <h3 style="color: #667eea;">{{ $dailyStats->sum('total') }}</h3>
            <p class="text-muted mb-0">إجمالي آخر 30 يوم (ج.م)</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="modern-card text-center">
            <i class="fas fa-calendar-alt" style="font-size: 40px; color: #f093fb; margin-bottom: 15px;"></i>
            <h3 style="color: #f093fb;">{{ $monthlyStats->sum('total') }}</h3>
            <p class="text-muted mb-0">إجمالي آخر 12 شهر (ج.م)</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="modern-card text-center">
            <i class="fas fa-book" style="font-size: 40px; color: #4facfe; margin-bottom: 15px;"></i>
            <h3 style="color: #4facfe;">{{ $topCourses->count() }}</h3>
            <p class="text-muted mb-0">أفضل الكورسات</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="modern-card text-center">
            <i class="fas fa-credit-card" style="font-size: 40px; color: #43e97b; margin-bottom: 15px;"></i>
            <h3 style="color: #43e97b;">{{ $paymentMethods->count() }}</h3>
            <p class="text-muted mb-0">طرق الدفع المستخدمة</p>
        </div>
    </div>
</div>

<!-- Top Courses -->
<div class="row g-4">
    <div class="col-md-6">
        <div class="modern-card">
            <h5 class="mb-4"><i class="fas fa-trophy me-2"></i> أفضل الكورسات من حيث الإيرادات</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الكورس</th>
                            <th>عدد المبيعات</th>
                            <th>الإيرادات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topCourses as $course)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $course->month->name ?? 'غير محدد' }}</td>
                            <td>{{ $course->count }}</td>
                            <td><strong>{{ number_format($course->total, 2) }} ج.م</strong></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">لا توجد بيانات</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="modern-card">
            <h5 class="mb-4"><i class="fas fa-wallet me-2"></i> طرق الدفع</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>طريقة الدفع</th>
                            <th>عدد المعاملات</th>
                            <th>الإيرادات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paymentMethods as $method)
                        <tr>
                            <td>{{ $method->payment_method ?? 'غير محدد' }}</td>
                            <td>{{ $method->count }}</td>
                            <td><strong>{{ number_format($method->total, 2) }} ج.م</strong></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">لا توجد بيانات</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Statistics -->
<div class="modern-card mt-4">
    <h5 class="mb-4"><i class="fas fa-chart-bar me-2"></i> الإحصائيات الشهرية (آخر 12 شهر)</h5>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>الشهر</th>
                    <th>عدد المبيعات</th>
                    <th>الإيرادات (ج.م)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($monthlyStats as $stat)
                <tr>
                    <td>{{ $stat->year }}-{{ str_pad($stat->month, 2, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $stat->count }}</td>
                    <td><strong>{{ number_format($stat->total, 2) }}</strong></td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">لا توجد بيانات</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection




