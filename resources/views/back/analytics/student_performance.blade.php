@extends('back_layouts.master')

@section('title', 'تقارير أداء الطلاب الذكية')

@section('css')
<style>
    /* تحسينات عامة وتأثيرات زجاجية */
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        --glass-bg: rgba(255, 255, 255, 0.9);
    }

    .dashboard-container { padding: 20px; background: #f8fafc; min-height: 100vh; }

    /* هيدر الصفحة بتصميم مميز */
    .page-header-premium {
        background: white;
        padding: 30px;
        border-radius: 24px;
        border: 1px solid #edf2f7;
        box-shadow: 0 10px 25px rgba(0,0,0,0.02);
        margin-bottom: 35px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* كروت الإحصائيات (الطلاب الأكثر نشاطاً) */
    .activity-card {
        background: white;
        border-radius: 24px;
        padding: 25px;
        height: 100%;
        border: 1px solid #f1f5f9;
        transition: 0.3s;
    }
    .activity-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.05); }

    /* ستايل الجدول الاحترافي جداً */
    .premium-table { width: 100%; border-collapse: separate; border-spacing: 0 12px; }
    .premium-table thead th {
        padding: 15px 20px;
        color: #94a3b8;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        border: none;
    }
    .premium-table tbody tr {
        background: white;
        box-shadow: 0 4px 6px rgba(0,0,0,0.01);
        border-radius: 16px;
        transition: 0.2s;
    }
    .premium-table tbody tr:hover { background: #fdfdff; transform: scale(1.01); }
    .premium-table td { padding: 20px; border: none; vertical-align: middle; }
    .premium-table td:first-child { border-radius: 16px 0 0 16px; }
    .premium-table td:last-child { border-radius: 0 16px 16px 0; }

    /* أيقونات الطلاب (Initials) */
    .student-avatar {
        width: 45px; height: 45px; border-radius: 14px;
        background: var(--primary-gradient);
        color: white; display: flex; align-items: center;
        justify-content: center; font-weight: 700; font-size: 18px;
    }

    /* مؤشر الدرجات الدائري المصغر */
    .score-badge {
        width: 50px; height: 50px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 14px;
        border: 3px solid;
    }
    .score-high { border-color: #10b981; color: #10b981; background: #ecfdf5; }
    .score-mid { border-color: #f59e0b; color: #f59e0b; background: #fffbeb; }
    .score-low { border-color: #ef4444; color: #ef4444; background: #fef2f2; }

    /* قسم توزيع الدرجات */
    .distribution-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
    .dist-card {
        background: white; padding: 20px; border-radius: 20px;
        text-align: center; border-bottom: 4px solid #6366f1;
        transition: 0.3s;
    }
    .dist-card:hover { background: #6366f1; }
    .dist-card:hover * { color: white !important; }
</style>
@endsection

@section('content')
<div class="dashboard-container">
    
    <div class="page-header-premium">
        <div>
            <h3 class="fw-black mb-1" style="color: #1e293b;">تحليلات الأداء <span class="badge bg-soft-primary text-primary ms-2" style="font-size: 12px;">Live</span></h3>
            <p class="text-muted mb-0">تقارير ذكية تفصل مستويات الطلاب وتفاعلهم</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-white border-0 shadow-sm rounded-pill px-4" onclick="location.reload()">
                <i class="fas fa-sync-alt me-2"></i> تحديث
            </button>
            <button class="btn btn-primary rounded-pill px-4 shadow-primary" onclick="window.print()">
                <i class="fas fa-file-pdf me-2"></i> تصدير التقرير
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-4 col-lg-5">
            <div class="activity-card shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0"><i class="fas fa-bolt text-warning me-2"></i> شعلة النشاط</h5>
                    <span class="small text-muted">آخر 30 يوم</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <tbody>
                            @foreach($active_students as $student)
                            <tr>
                                <td width="50">
                                    <div class="student-avatar">{{ mb_substr($student->first_name, 0, 1) }}</div>
                                </td>
                                <td>
                                    <a href="{{ url('admin/student-profile/' . $student->id) }}" class="text-dark fw-bold text-decoration-none d-block">
                                        {{ $student->first_name }} {{ $student->second_name }}
                                    </a>
                                    <span class="small text-muted">{{ $student->grade }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="badge bg-light text-primary rounded-pill px-3 py-2">{{ $student->exam_results_count }} امتحان</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="activity-card shadow-sm">
                <h5 class="fw-bold mb-4"><i class="fas fa-chart-bar text-primary me-2"></i> مصفوفة أداء الطلاب</h5>
                <div class="table-responsive">
                    <table class="premium-table">
                        <thead>
                            <tr>
                                <th>الطالب</th>
                                <th>الاختبار الأخير</th>
                                <th>المعدل الحالي</th>
                                <th>المحاولات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exam_performance as $performance)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $performance->first_name }}</div>
                                    <div class="small text-muted">{{ $performance->grade }}</div>
                                </td>
                                <td><span class="text-truncate d-inline-block" style="max-width: 140px;">{{ $performance->exam_name }}</span></td>
                                <td>
                                    @php 
                                        $type = $performance->avg_degree >= 80 ? 'high' : ($performance->avg_degree >= 60 ? 'mid' : 'low');
                                    @endphp
                                    <div class="score-badge score-{{ $type }}">
                                        {{ number_format($performance->avg_degree, 0) }}%
                                    </div>
                                </td>
                                <td class="text-center fw-black text-secondary">{{ $performance->exam_count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="activity-card shadow-sm">
                <h5 class="fw-bold mb-4"><i class="fas fa-shapes text-info me-2"></i> توزيع المستويات العلمية</h5>
                <div class="distribution-grid mb-5">
                    @foreach($grade_distribution as $dist)
                    <div class="dist-card shadow-sm">
                        <div class="text-muted small mb-1 fw-bold">{{ $dist->grade_range }}</div>
                        <div class="h2 fw-black mb-0" style="color: #6366f1;">{{ $dist->count }}</div>
                        <div class="small">طالب</div>
                    </div>
                    @endforeach
                </div>
                <div style="height: 300px; width: 100%;">
                    <canvas id="ultraChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('ultraChart').getContext('2d');
    
    // Gradient setup
    const grad = ctx.createLinearGradient(0, 0, 0, 300);
    grad.addColorStop(0, '#6366f1');
    grad.addColorStop(1, '#a855f7');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($grade_distribution).map(d => d.grade_range),
            datasets: [{
                label: 'عدد الطلاب',
                data: @json($grade_distribution).map(d => d.count),
                backgroundColor: grad,
                borderRadius: 20,
                barThickness: 50,
                hoverBackgroundColor: '#1e293b'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 15,
                    titleFont: { size: 14, weight: 'bold' },
                    cornerRadius: 10
                }
            },
            scales: {
                y: { display: false },
                x: { grid: { display: false }, ticks: { font: { weight: 'bold' } } }
            }
        }
    });
</script>
@endsection