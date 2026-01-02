@extends('back_layouts.master')

@section('title', 'تقارير الإيرادات')

@section('content')
<div class="page-header-modern">
    <h4><i class="fas fa-dollar-sign me-2"></i> تقارير الإيرادات</h4>
</div>

<!-- ملخص الإيرادات -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card primary">
            <div class="stat-icon primary">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-value">{{ number_format($total_revenue, 2) }}</div>
            <div class="stat-label">إجمالي الإيرادات (جنيه)</div>
        </div>
    </div>
</div>

<!-- الإيرادات الشهرية -->
<div class="modern-card mb-4">
    <h5 class="mb-4"><i class="fas fa-chart-line me-2"></i> الإيرادات الشهرية (آخر 12 شهر)</h5>
    <div class="chart-container" style="height: 300px;">
        <canvas id="monthlyRevenueChart"></canvas>
    </div>
</div>

<!-- الإيرادات اليومية -->
<div class="modern-card mb-4">
    <h5 class="mb-4"><i class="fas fa-calendar-day me-2"></i> الإيرادات اليومية (آخر 30 يوم)</h5>
    <div class="chart-container" style="height: 300px;">
        <canvas id="dailyRevenueChart"></canvas>
    </div>
</div>

<!-- الإيرادات حسب الكورس -->
<div class="modern-card">
    <h5 class="mb-4"><i class="fas fa-book me-2"></i> الإيرادات حسب الكورس</h5>
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>الكورس</th>
                    <th>إجمالي الإيرادات</th>
                    <th>عدد الطلاب</th>
                </tr>
            </thead>
            <tbody>
                @foreach($revenue_by_course as $course)
                <tr>
                    <td>{{ $course->course_name }}</td>
                    <td><span class="badge bg-success">{{ number_format($course->total, 2) }} جنيه</span></td>
                    <td>{{ $course->student_count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Revenue Chart
    const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
    const monthlyData = @json($monthly_revenue);
    
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: monthlyData.map(d => {
                const months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
                return months[d.month - 1] + ' ' + d.year;
            }),
            datasets: [{
                label: 'الإيرادات (جنيه)',
                data: monthlyData.map(d => parseFloat(d.total)),
                borderColor: '#9B5FFF',
                backgroundColor: 'rgba(155, 95, 255, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Daily Revenue Chart
    const dailyCtx = document.getElementById('dailyRevenueChart').getContext('2d');
    const dailyData = @json($daily_revenue);
    
    new Chart(dailyCtx, {
        type: 'bar',
        data: {
            labels: dailyData.map(d => d.date),
            datasets: [{
                label: 'الإيرادات (جنيه)',
                data: dailyData.map(d => parseFloat(d.total)),
                backgroundColor: '#7A35FF'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection




