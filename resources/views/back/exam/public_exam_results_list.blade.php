@extends('back_layouts.master')

@section('title')
    نتائج الامتحانات العامة
@stop

@section('css')
<style>
    /* تصميم بطاقات اختيار الامتحانات */
    .exam-selector-card {
        background: #fff;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        border: 1px solid #f0f0f0;
        transition: all 0.3s ease;
        text-decoration: none !important;
        display: block;
        height: 100%;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    .exam-selector-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(116, 36, 169, 0.1);
        border-color: #7424a9;
    }
    .exam-icon {
        width: 50px;
        height: 50px;
        background: #f8f0ff;
        color: #7424a9;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 20px;
    }
    .exam-title-text {
        color: #333;
        font-weight: 700;
        font-size: 14px;
        margin-bottom: 5px;
        display: block;
    }

    /* هيدر الصفحة المودرن */
    .page-header-modern {
        background: white;
        padding: 20px;
        border-radius: 15px;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        border-right: 5px solid #7424a9;
    }

    /* تنسيق الجدول الاحترافي */
    .modern-table-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }
    .table thead th {
        background-color: #f8f9fa;
        color: #8e94a9;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        font-weight: 700;
        border: none;
    }
    .table tbody td {
        vertical-align: middle;
        padding: 15px;
        border-bottom: 1px solid #f1f1f1;
    }

    /* الـ Badges الزجاجية */
    .badge-soft-success { background: #e8f5e9; color: #2e7d32; }
    .badge-soft-danger { background: #ffebee; color: #c62828; }
    .badge-soft-warning { background: #fff3e0; color: #ef6c00; }
</style>
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex align-items-center page-header-modern">
            <h4 class="content-title mb-0 my-auto text-dark">
                <i class="fas fa-chart-bar text-primary me-2"></i> مركز النتائج
            </h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ عرض وتحليل نتائج الامتحانات العامة</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">

    @if(session()->has('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-15 mb-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="mb-5">
        <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-th-large me-2 text-primary"></i> تصفية حسب الامتحان:</h5>
        <div class="row g-3">
            @foreach($exams as $exam)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <a href="{{ route('publicExam.results.exam', $exam->id) }}" class="exam-selector-card">
                    <div class="exam-icon">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <span class="exam-title-text">{{ $exam->exam_title }}</span>
                    <small class="text-muted">عرض تفاصيل النتائج <i class="fas fa-chevron-left ms-1" style="font-size: 10px;"></i></small>
                </a>
            </div>
            @endforeach
        </div>
    </div>

    <div class="modern-table-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-history me-2 text-primary"></i> السجل العام للنتائج</h5>
            <span class="badge bg-light text-dark px-3 py-2 rounded-pill small">إجمالي السجلات: {{ count($allResults) }}</span>
        </div>

        <div class="table-responsive">
            <table id="datatable" class="table table-hover mb-0" data-page-length="50">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم المتقدم</th>
                        <th>الامتحان</th>
                        <th class="text-center">الدرجة</th>
                        <th class="text-center">النسبة</th>
                        <th>توقيت التقديم</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($allResults as $result)
                    <tr>
                        <td class="text-muted small">#{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2 bg-light rounded-circle text-center" style="width: 30px; height: 30px; line-height: 30px;">
                                    <i class="fas fa-user text-muted small"></i>
                                </div>
                                <span class="fw-bold">{{ $result->student_name }}</span>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('publicExam.results.exam', $result->exam_id) }}" class="text-primary fw-600 text-decoration-none">
                                <i class="fas fa-link small me-1"></i> {{ $result->exam_title }}
                            </a>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $result->student_degree >= ($result->total_degree / 2) ? 'badge-soft-success' : 'badge-soft-danger' }} px-3 py-2">
                                {{ $result->student_degree }} / {{ $result->total_degree }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="fw-bold {{ $result->percentage >= 50 ? 'text-success' : 'text-warning' }}">
                                {{ $result->percentage }}%
                            </div>
                        </td>
                        <td>
                            <div class="small text-dark">{{ \Carbon\Carbon::parse($result->created_at)->format('Y-m-d') }}</div>
                            <div class="text-muted" style="font-size: 11px;">{{ \Carbon\Carbon::parse($result->created_at)->format('H:i A') }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="60" class="mb-3 opacity-50">
                            <p class="text-muted fw-bold">لا يوجد أي بيانات مسجلة في السجل العام</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
@endsection