@extends('back_layouts.master')

@section('title', 'تحليل تفاعل المحتوى - Heat Maps')

@section('css')
<style>
    /* تدرجات حرارية للخلفية */
    .heat-header {
        background: linear-gradient(135deg, #f53d2d 0%, #ff8c00 100%);
        color: white;
        padding: 40px;
        border-radius: 20px;
        margin-bottom: 30px;
        box-shadow: 0 10px 20px rgba(245, 61, 45, 0.2);
    }

    /* كروت الإحصائيات السريعة */
    .stat-box {
        border: none;
        border-radius: 15px;
        transition: 0.3s;
        overflow: hidden;
        background: white;
        border: 1px solid #eee;
    }
    .stat-box:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    .stat-icon {
        width: 60px; height: 60px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px; margin-bottom: 15px;
    }

    /* جدول الحرارة الذكي */
    .heat-table tbody tr { transition: 0.2s; }
    .heat-table td { vertical-align: middle; padding: 15px; }
    
    .progress-heat {
        height: 12px;
        border-radius: 10px;
        background-color: #f0f0f0;
        overflow: visible;
        position: relative;
    }
    .progress-heat .progress-bar {
        border-radius: 10px;
        position: relative;
    }
    /* إضافة توهج للبار النشط جداً */
    .bg-hot { box-shadow: 0 0 10px rgba(220, 53, 69, 0.5); }

    /* الرسوم البيانية */
    .chart-wrapper {
        background: white;
        padding: 20px;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    
    <div class="heat-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1"><i class="fas fa-fire-alt me-2"></i> خرائط حرارة المحتوى</h3>
            <p class="mb-0 opacity-75">تحليل عميق للمحاضرات الأكثر جذباً للطلاب وتوقيتات الذروة</p>
        </div>
        <div class="d-none d-md-block">
            <i class="fas fa-chart-line fa-3x opacity-25"></i>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-box p-4">
                <div class="stat-icon bg-soft-primary text-primary"><i class="fas fa-video"></i></div>
                <h2 class="fw-black mb-1">{{ $featured_stats['total'] }}</h2>
                <div class="text-muted small fw-bold">محاضرة مميزة قيد التحليل</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-box p-4">
                <div class="stat-icon bg-soft-danger text-danger"><i class="fas fa-eye"></i></div>
                <h2 class="fw-black mb-1">{{ number_format($featured_stats['total_views']) }}</h2>
                <div class="text-muted small fw-bold">إجمالي مشاهدات المحتوى</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-box p-4">
                <div class="stat-icon bg-soft-success text-success"><i class="fas fa-users"></i></div>
                <h2 class="fw-black mb-1">{{ number_format($featured_stats['avg_views'], 1) }}</h2>
                <div class="text-muted small fw-bold">متوسط التفاعل لكل محاضرة</div>
            </div>
        </div>
    </div>

    <div class="modern-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0"><i class="fas fa-fire text-danger me-2"></i> المحتوى "الأكثر سخونة"</h5>
            <span class="badge bg-light text-dark border">مرتب حسب كثافة المشاهدة</span>
        </div>
        
        <div class="table-responsive">
            <table class="table heat-table">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 px-4">المحاضرة</th>
                        <th class="border-0 text-center">كثافة المشاهدة</th>
                        <th class="border-0" width="40%">مقياس التفاعل الحراري</th>
                    </tr>
                </thead>
                <tbody>
                    @php $maxViews = $top_lectures->max('views') ?? 1; @endphp
                    @foreach($top_lectures as $index => $lecture)
                    @php
                        $percentage = ($lecture->views / $maxViews) * 100;
                        // تحديد اللون بناءً على السخونة
                        $colorClass = $percentage > 80 ? '#ef4444' : ($percentage > 50 ? '#f59e0b' : '#10b981');
                        $shadowClass = $percentage > 80 ? 'bg-hot' : '';
                    @endphp
                    <tr>
                        <td class="px-4">
                            <div class="fw-bold text-dark">{{ $lecture->title }}</div>
                            <div class="small text-muted">ترتيب الأهمية: #{{ $index + 1 }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill p-2 px-3" style="background: {{ $colorClass }}; color: white;">
                                <i class="fas fa-fire me-1"></i> {{ number_format($lecture->views) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="progress progress-heat flex-grow-1">
                                    <div class="progress-bar {{ $shadowClass }}" 
                                         style="width: {{ $percentage }}%; background: {{ $colorClass }};">
                                    </div>
                                </div>
                                <span class="fw-bold small" style="color: {{ $colorClass }};">{{ number_format($percentage, 0) }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="chart-wrapper shadow-sm">
                <h6 class="fw-bold mb-4"><i class="fas fa-chart-pie me-2 text-primary"></i> تحليل المشاهدات حسب الصفوف</h6>
                <div style="height: 300px;">
                    <canvas id="gradeViewsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="chart-wrapper shadow-sm">
                <h6 class="fw-bold mb-4"><i class="fas fa-chart-bar me-2 text-primary"></i> الذروة الشهرية للمشاهدات</h6>
                <div style="height: 300px;">
                    <canvas id="monthViewsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // الإعدادات المشتركة للرسوم البيانية
    Chart.defaults.font.family = 'Cairo, sans-serif';

    // 1. Doughnut Chart (Grades)
    new Chart(document.getElementById('gradeViewsChart'), {
        type: 'doughnut',
        data: {
            labels: @json($views_by_grade).map(d => d.grade || 'غير محدد'),
            datasets: [{
                data: @json($views_by_grade).map(d => d.total_views),
                backgroundColor: ['#6366f1', '#f59e0b', '#10b981', '#ef4444', '#ec4899'],
                borderWidth: 0,
                hoverOffset: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
            },
            cutout: '70%'
        }
    });

    // 2. Bar Chart (Months)
    const monthViewsCtx = document.getElementById('monthViewsChart').getContext('2d');
    const mGradient = monthViewsCtx.createLinearGradient(0, 0, 0, 300);
    mGradient.addColorStop(0, '#6366f1');
    mGradient.addColorStop(1, '#a855f7');

    new Chart(monthViewsCtx, {
        type: 'bar',
        data: {
            labels: @json($views_by_month).map(d => d.month_name || 'غير محدد'),
            datasets: [{
                label: 'عدد المشاهدات',
                data: @json($views_by_month).map(d => d.total_views),
                backgroundColor: mGradient,
                borderRadius: 8,
                barThickness: 30
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { display: false }, beginAtZero: true },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endsection