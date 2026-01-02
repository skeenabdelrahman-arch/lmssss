@extends('back_layouts.master')

@section('title') نتائج الكويز - {{ $lecture->quiz->title }} @stop

@section('css')
<style>
    body { background-color: #f4f7fa; }
    .main-wrapper { max-width: 1200px; margin: 0 auto; }
    
    /* كروت الإحصائيات */
    .stat-card {
        background: #fff; border-radius: 20px; padding: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid #f1f5f9;
        display: flex; align-items: center; gap: 15px; height: 100%;
    }
    .stat-icon {
        width: 50px; height: 50px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; font-size: 20px;
    }
    .icon-blue { background: #eef2ff; color: #6366f1; }
    .icon-green { background: #ecfdf5; color: #10b981; }
    .icon-red { background: #fef2f2; color: #ef4444; }

    /* تحسين الجدول */
    .results-card {
        background: #fff; border-radius: 24px; padding: 30px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.04); border: none;
    }
    .table thead th {
        background-color: #f8fafc; color: #64748b; font-weight: 700;
        text-transform: uppercase; font-size: 12px; border: none; padding: 15px;
    }
    .table tbody td { vertical-align: middle; padding: 15px; border-bottom: 1px solid #f1f5f9; color: #1e293b; }
    
    /* البادجات الحديثة */
    .badge-status {
        padding: 6px 14px; border-radius: 10px; font-weight: 700; font-size: 12px;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .status-pass { background: #dcfce7; color: #15803d; }
    .status-fail { background: #fee2e2; color: #b91c1c; }
    .status-info { background: #e0e7ff; color: #4338ca; }

    .student-avatar {
        width: 38px; height: 38px; background: #6366f1; color: white;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-size: 14px;
    }
</style>
@endsection

@section('content')
<div class="main-wrapper py-5">
    
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="fw-bold mb-1">تقرير نتائج الاختبار</h3>
            <p class="text-muted mb-0"><i class="fas fa-file-alt me-1"></i> {{ $lecture->quiz->title }}</p>
        </div>
        <a href="{{ route('admin.quiz.show', $lecture->id) }}" class="btn btn-white shadow-sm rounded-3 fw-bold px-4 border">
            <i class="fas fa-arrow-right me-2 text-primary"></i> العودة لإدارة الكويز
        </a>
    </div>

    @if($attempts->isEmpty())
        <div class="results-card text-center py-5">
            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="120" class="mb-4 opacity-50">
            <h4 class="fw-bold text-secondary">لا توجد محاولات حتى الآن</h4>
            <p class="text-muted">بمجرد قيام الطلاب بحل الاختبار، ستظهر نتائجهم هنا بشكل تلقائي.</p>
        </div>
    @else
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
                    <div>
                        <div class="text-muted small">إجمالي الممتحنين</div>
                        <div class="fs-4 fw-bold">{{ $attempts->total() }} طالب</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon icon-green"><i class="fas fa-user-check"></i></div>
                    <div>
                        <div class="text-muted small">عدد الناجحين</div>
                        <div class="fs-4 fw-bold text-success">{{ $attempts->where('is_passed', true)->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon icon-red"><i class="fas fa-user-times"></i></div>
                    <div>
                        <div class="text-muted small">عدد الراسبين</div>
                        <div class="fs-4 fw-bold text-danger">{{ $attempts->where('is_passed', false)->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="results-card">
            <div class="table-responsive">
                <table id="datatable" class="table">
                    <thead>
                        <tr>
                            <th>الطالب</th>
                            <th class="text-center">الدرجة</th>
                            <th class="text-center">النسبة</th>
                            <th class="text-center">الحالة</th>
                            <th>تاريخ المحاولة</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attempts as $attempt)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="student-avatar">
                                            {{ mb_substr($attempt->student->first_name ?? 'S', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $attempt->student->first_name }} {{ $attempt->student->second_name }}</div>
                                            <div class="small text-muted"><i class="fas fa-phone-alt me-1"></i> {{ $attempt->student->student_phone ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge-status status-info">
                                        {{ $attempt->score }} / {{ $attempt->total_score }}
                                    </span>
                                </td>
                                <td class="text-center fw-bold">
                                    <span class="{{ $attempt->is_passed ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($attempt->percentage, 1) }}%
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($attempt->is_passed)
                                        <span class="badge-status status-pass">
                                            <i class="fas fa-check-circle"></i> ناجح
                                        </span>
                                    @else
                                        <span class="badge-status status-fail">
                                            <i class="fas fa-times-circle"></i> رسب
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">{{ $attempt->completed_at ? $attempt->completed_at->format('Y-m-d') : '-' }}</div>
                                    <div class="small text-muted">{{ $attempt->completed_at ? $attempt->completed_at->format('h:i A') : '' }}</div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.quiz.attempt.details', ['attemptId' => $attempt->id]) }}" 
                                       class="btn btn-light btn-sm rounded-pill px-3 shadow-sm border">
                                        <i class="fas fa-eye me-1 text-primary"></i> تفاصيل
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $attempts->links() }}
            </div>
        </div>
    @endif
</div>
@endsection