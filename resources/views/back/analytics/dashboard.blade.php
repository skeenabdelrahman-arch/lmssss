@extends('back_layouts.master')

@section('title', 'لوحة الإحصائيات المتقدمة')

@section('css')
<style>
    /* التنسيق العام للحاويات */
    .analytics-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        margin-bottom: 25px;
        border: 1px solid #f1f1f1;
        transition: all 0.3s ease;
    }
    
    /* كروت الأرقام السريعة */
    .stat-card-modern {
        background: #fff;
        border-radius: 20px;
        padding: 25px;
        display: flex;
        align-items: center;
        border: none;
        box-shadow: 0 8px 20px rgba(0,0,0,0.02);
        position: relative;
        overflow: hidden;
    }
    .stat-card-modern .icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-left: 20px;
    }
    
    /* التدرجات اللونية */
    .bg-gradient-primary { background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: white; }
    .bg-gradient-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
    .bg-gradient-info { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; }
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; }

    .stat-number-big {
        font-size: 28px;
        font-weight: 800;
        color: #1e293b;
        display: block;
    }
    .stat-label-modern {
        color: #64748b;
        font-size: 14px;
        font-weight: 500;
    }

    /* الرسوم البيانية */
    .chart-container {
        position: relative;
        height: 350px;
        width: 100%;
    }

    /* هيدر الصفحة */
    .page-header-modern {
        background: white;
        padding: 25px;
        border-radius: 20px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        border-right: 6px solid #6366f1;
    }

    /* كروت الإيرادات المصغرة */
    .revenue-item {
        background: #f8fafc;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        border: 1px solid #edf2f7;
        transition: 0.3s;
    }
    .revenue-item:hover { background: #fff; border-color: #6366f1; transform: translateY(-3px); }
    .rev-value { font-size: 22px; font-weight: 800; color: #6366f1; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="page-header-modern d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-analytics text-primary me-2"></i> لوحة التحليل الذكي</h4>
            <p class="text-muted mb-0 small">متابعة الأداء، النمو، وتوزيع الطلاب لحظياً</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4" onclick="window.print()">
            <i class="fas fa-download me-2"></i> تصدير التقرير
        </button>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card-modern">
                <div class="icon-wrapper bg-gradient-primary shadow-primary">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div>
                    <span class="stat-label-modern">إجمالي الطلاب</span>
                    <span class="stat-number-big">{{ number_format($stats['total_students']) }}</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card-modern">
                <div class="icon-wrapper bg-gradient-success shadow-success">
                    <i class="fas fa-signal"></i>
                </div>
                <div>
                    <span class="stat-label-modern">الطلاب النشطين</span>
                    <span class="stat-number-big">{{ number_format($stats['active_students']) }}</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card-modern">
                <div class="icon-wrapper bg-gradient-info shadow-info">
                    <i class="fas fa-video"></i>
                </div>
                <div>
                    <span class="stat-label-modern">المحاضرات</span>
                    <span class="stat-number-big">{{ number_format($stats['total_lectures']) }}</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card-modern">
                <div class="icon-wrapper bg-gradient-warning shadow-warning">
                    <i class="fas fa-wallet"></i>
                </div>
                <div>
                    <span class="stat-label-modern">الإيرادات (L.E)</span>
                    <span class="stat-number-big">{{ number_format($payment_stats['total_paid'], 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="analytics-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0"><i class="fas fa-chart-line text-primary me-2"></i> اتجاه النمو الشهري</h5>
            <div class="badge bg-light text-dark rounded-pill px-3">آخر 12 شهر</div>
        </div>
        <div class="chart-container">
            <canvas id="growthChart"></canvas>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="analytics-card" style="height: 480px;">
                <h5 class="fw-bold mb-4"><i class="fas fa-pie-chart text-primary me-2"></i> توزيع الطلاب (الصفوف)</h5>
                <div class="chart-container">
                    <canvas id="gradeChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-7">
            <div class="analytics-card" style="height: 480px;">
                <h5 class="fw-bold mb-4"><i class="fas fa-bolt text-warning me-2"></i> المحاضرات الأكثر تفاعلاً</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>اسم المحاضرة</th>
                                <th class="text-center">المشاهدات</th>
                                <th>النشاط</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($top_lectures as $lecture)
                            <tr>
                                <td class="fw-bold text-dark">{{ $lecture->title }}</td>
                                <td class="text-center">
                                    <span class="badge bg-soft-primary text-primary rounded-pill px-3">
                                        {{ number_format($lecture->views) }}
                                    </span>
                                </td>
                                <td width="150">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-primary" style="width: 85%"></div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="analytics-card">
        <h5 class="fw-bold mb-4"><i class="fas fa-coins text-warning me-2"></i> تحليل الإيرادات المالي</h5>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="revenue-item">
                    <div class="stat-label-modern mb-1">الإجمالي العام</div>
                    <div class="rev-value">{{ number_format($revenue_stats['total'], 0) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="revenue-item" style="border-right: 4px solid #10b981;">
                    <div class="stat-label-modern mb-1">الشهر الحالي</div>
                    <div class="rev-value text-success">{{ number_format($revenue_stats['this_month'], 0) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="revenue-item">
                    <div class="stat-label-modern mb-1">الشهر الماضي</div>
                    <div class="rev-value text-muted">{{ number_format($revenue_stats['last_month'], 0) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="revenue-item" style="background: #6366f1; color: white;">
                    <div class="stat-label-modern mb-1 text-white opacity-75">إجمالي السنة</div>
                    <div class="rev-value text-white">{{ number_format($revenue_stats['this_year'], 0) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.font.family = 'Cairo, sans-serif';

    // Growth Chart (تطوير الألوان والتأثيرات)
    const growthCtx = document.getElementById('growthChart').getContext('2d');
    const gradientStudents = growthCtx.createLinearGradient(0, 0, 0, 400);
    gradientStudents.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
    gradientStudents.addColorStop(1, 'rgba(99, 102, 241, 0)');

    new Chart(growthCtx, {
        type: 'line',
        data: {
            labels: @json($growth_data).map(d => d.label),
            datasets: [{
                label: 'انضمام الطلاب',
                data: @json($growth_data).map(d => d.students),
                borderColor: '#6366f1',
                backgroundColor: gradientStudents,
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#6366f1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { borderDash: [5, 5] }, beginAtZero: true },
                x: { grid: { display: false } }
            }
        }
    });

    // Grade Chart (تنسيق الـ Doughnut)
    const gradeCtx = document.getElementById('gradeChart').getContext('2d');
    new Chart(gradeCtx, {
        type: 'doughnut',
        data: {
            labels: @json($students_by_grade).map(d => d.grade || 'غير محدد'),
            datasets: [{
                data: @json($students_by_grade).map(d => d.count),
                backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#3b82f6', '#ef4444', '#8b5cf6'],
                hoverOffset: 15,
                borderWidth: 0
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
</script>
@endsection