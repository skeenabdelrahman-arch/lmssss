@extends('back_layouts.master')

@section('title', 'الواجبات الدراسية')

@section('css')
<style>
    .assignment-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        border: none;
        overflow: hidden;
    }
    .stat-box {
        padding: 20px;
        border-radius: 12px;
        background: #f8f9fa;
        border-right: 4px solid #7424a9;
        transition: transform 0.3s;
    }
    .stat-box:hover { transform: translateY(-5px); }
    
    .table-modern thead th {
        background: #7424a9;
        color: white;
        border: none;
        padding: 15px;
        font-weight: 600;
        text-align: center;
    }
    .table-modern tbody td {
        vertical-align: middle;
        text-align: center;
        padding: 15px;
        border-bottom: 1px solid #eee;
    }
    .badge-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
    }
    .btn-action {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        margin: 0 2px;
        transition: 0.3s;
    }
    .btn-action:hover { transform: scale(1.1); }
    
    /* Responsive labels for mobile */
    @media (max-width: 768px) {
        .table-modern thead { display: none; }
        .table-modern tbody td {
            display: block;
            text-align: right !important;
            padding-left: 50%;
            position: relative;
        }
        .table-modern tbody td::before {
            content: attr(data-label);
            position: absolute;
            left: 15px;
            font-weight: bold;
            color: #7424a9;
        }
    }
</style>
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex align-items-center">
            <h4 class="content-title mb-0 my-auto text-primary"><i class="fas fa-book-open me-2"></i> إدارة الواجبات</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ القائمة العامة</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <a href="{{ route('assignments.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus-circle me-1"></i> إضافة واجب جديد
        </a>
    </div>
</div>
@endsection

@section('content')

{{-- إحصائيات سريعة --}}
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-box shadow-sm mb-3">
            <small class="text-muted d-block mb-1">إجمالي الواجبات</small>
            <h3 class="mb-0 fw-bold">{{ $assignments->total() }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box shadow-sm mb-3" style="border-right-color: #28a745;">
            <small class="text-muted d-block mb-1">الواجبات النشطة</small>
            <h3 class="mb-0 fw-bold text-success">{{ $assignments->where('status', 'active')->count() }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box shadow-sm mb-3" style="border-right-color: #ffc107;">
            <small class="text-muted d-block mb-1">موعد التسليم القادم</small>
            <h5 class="mb-0">{{ $assignments->where('deadline', '>', now())->first()->deadline ?? 'لا يوجد' }}</h5>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card assignment-card">
            <div class="card-body p-0">
                
                {{-- تنبيهات --}}
                @if(session('success') || session('error'))
                    <div class="p-3">
                        @if(session('success'))
                            <div class="alert alert-success border-0 shadow-sm mb-0">
                                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger border-0 shadow-sm mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                            </div>
                        @endif
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>العنوان</th>
                                <th>المحاضرة / الشهر</th>
                                <th>الدرجة</th>
                                <th>الموعد النهائي</th>
                                <th>الحالة</th>
                                <th>التسليمات</th>
                                <th>العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments as $assignment)
                                <tr>
                                    <td data-label="#">{{ $loop->iteration }}</td>
                                    <td data-label="العنوان">
                                        <div class="fw-bold text-dark">{{ $assignment->title }}</div>
                                    </td>
                                    <td data-label="المحاضرة">
                                        <span class="text-muted small">
                                            <i class="fas fa-calendar-alt me-1"></i> {{ $assignment->month->name ?? '-' }}
                                        </span>
                                        <div class="mt-1 badge bg-light text-dark">
                                            {{ $assignment->lecture->title ?? 'واجب عام' }}
                                        </div>
                                    </td>
                                    <td data-label="الدرجة">
                                        <span class="badge bg-primary-transparent text-primary fw-bold" style="font-size: 14px;">
                                            {{ $assignment->total_marks }}
                                        </span>
                                    </td>
                                    <td data-label="الموعد النهائي">
                                        @if($assignment->deadline)
                                            <div class="{{ $assignment->isOverdue() ? 'text-danger fw-bold' : 'text-muted' }}">
                                                {{ $assignment->deadline->format('Y-m-d') }}
                                                <br><small>{{ $assignment->deadline->format('H:i') }}</small>
                                            </div>
                                            @if($assignment->isOverdue())
                                                <span class="badge bg-danger-transparent text-danger small">انتهى الوقت</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td data-label="الحالة">
                                        @if($assignment->status == 'active')
                                            <span class="badge badge-status bg-success-transparent text-success border border-success">
                                                <i class="fas fa-check me-1"></i> نشط
                                            </span>
                                        @else
                                            <span class="badge badge-status bg-secondary-transparent text-secondary border border-secondary">
                                                <i class="fas fa-eye-slash me-1"></i> مخفي
                                            </span>
                                        @endif
                                    </td>
                                    <td data-label="الإجابات">
                                        <a href="{{ route('assignments.submissions', $assignment->id) }}" class="btn btn-sm btn-outline-info rounded-pill fw-bold">
                                            <i class="fas fa-users me-1"></i> {{ $assignment->submittedCount() }}
                                        </a>
                                    </td>
                                    <td data-label="العمليات">
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ route('assignments.show', $assignment->id) }}" class="btn-action bg-info text-white" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('assignments.edit', $assignment->id) }}" class="btn-action bg-warning text-white" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('assignments.destroy', $assignment->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action bg-danger text-white border-0" onclick="return confirm('هل أنت متأكد من الحذف؟')" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <img src="{{ asset('assets/img/nodata.png') }}" alt="" style="width: 80px; opacity: 0.5;">
                                        <p class="text-muted mt-2">لا توجد واجبات مضافة حالياً</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-3 d-flex justify-content-center">
                    {{ $assignments->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection