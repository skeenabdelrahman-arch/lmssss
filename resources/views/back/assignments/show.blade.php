@extends('back_layouts.master')

@section('title')
    تفاصيل الواجب: {{ $assignment->title }}
@stop

@section('css')
<style>
    /* تنسيقات البطاقة الرئيسية */
    .modern-card {
        background: #fff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border: none;
    }
    
    .detail-item {
        margin-bottom: 25px;
    }
    
    .detail-label {
        color: #888;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 8px;
        display: block;
        text-transform: uppercase;
    }
    
    .detail-value {
        color: #333;
        font-size: 16px;
        font-weight: 500;
    }

    /* تنسيق قسم الإحصائيات */
    .stat-box {
        border-radius: 15px;
        padding: 20px;
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
    }
    .stat-box:hover {
        background: #f8f9fa;
        transform: translateY(-5px);
    }
    .stat-box h3 {
        font-weight: 800;
        margin-bottom: 5px;
        color: #7424a9;
    }
    .stat-box small {
        color: #6c757d;
        font-weight: 500;
    }

    /* أزرار مودرن */
    .btn-modern-group {
        display: flex;
        gap: 10px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #f0f0f0;
    }
</style>
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex align-items-center">
            <h4 class="content-title mb-0 my-auto text-primary">
                <i class="fas fa-info-circle me-2"></i> تفاصيل الواجب
            </h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ $assignment->title }}</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="modern-card">
        <div class="row border-bottom pb-3 mb-4">
            <div class="col-md-8">
                <span class="detail-label">عنوان الواجب</span>
                <h3 class="text-dark fw-bold">{{ $assignment->title }}</h3>
            </div>
            <div class="col-md-4 text-md-left">
                <span class="detail-label">الحالة</span>
                @if($assignment->status == 'active')
                    <span class="badge badge-success-light p-2 px-3 rounded-pill">
                        <i class="fas fa-check-circle me-1"></i> نشط للطلاب
                    </span>
                @else
                    <span class="badge badge-secondary-light p-2 px-3 rounded-pill">
                        <i class="fas fa-eye-slash me-1"></i> مخفي حالياً
                    </span>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-md-6 detail-item">
                        <span class="detail-label">الشهر الدراسي</span>
                        <div class="detail-value text-primary">
                            <i class="fas fa-calendar-alt me-1"></i> {{ $assignment->month->name ?? 'غير محدد' }}
                        </div>
                    </div>
                    <div class="col-md-6 detail-item">
                        <span class="detail-label">المحاضرة المرتبطة</span>
                        <div class="detail-value">
                            <i class="fas fa-video me-1 text-muted"></i> {{ $assignment->lecture->name ?? 'واجب عام للشهر' }}
                        </div>
                    </div>
                    <div class="col-md-6 detail-item">
                        <span class="detail-label">الموعد النهائي (Deadline)</span>
                        <div class="detail-value {{ $assignment->deadline && $assignment->deadline->isPast() ? 'text-danger' : 'text-dark' }}">
                            <i class="fas fa-clock me-1"></i> 
                            {{ $assignment->deadline ? $assignment->deadline->format('Y-m-d | h:i A') : 'مفتوح' }}
                        </div>
                    </div>
                    <div class="col-md-6 detail-item">
                        <span class="detail-label">الدرجة الكلية</span>
                        <div class="detail-value">
                            <span class="badge bg-primary-transparent text-primary fw-bold fs-14">
                                {{ $assignment->total_marks }} درجة
                            </span>
                        </div>
                    </div>
                </div>

                @if($assignment->description)
                <div class="detail-item mt-2">
                    <span class="detail-label">وصف الواجب وتعليمات</span>
                    <div class="p-3 bg-light rounded" style="border-right: 4px solid #7424a9;">
                        <p class="mb-0 text-muted" style="line-height: 1.8;">{{ $assignment->description }}</p>
                    </div>
                </div>
                @endif

                @if($assignment->file_path)
                <div class="detail-item">
                    <span class="detail-label">مرفق الواجب</span>
                    <a href="{{ Storage::url($assignment->file_path) }}" target="_blank" class="btn btn-outline-primary rounded-pill shadow-sm">
                        <i class="fas fa-file-download me-2"></i> تحميل ملف الأسئلة
                    </a>
                </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="bg-light p-4 rounded-20 shadow-sm border">
                    <h6 class="fw-bold mb-4 text-dark border-bottom pb-2">نبذة عن التفاعل</h6>
                    <div class="row g-3">
                        <div class="col-6 mb-3">
                            <div class="stat-box text-center bg-white shadow-sm">
                                <h3>{{ $assignment->submittedCount() }}</h3>
                                <small>تم التسليم</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stat-box text-center bg-white shadow-sm">
                                <h3>{{ $assignment->gradedCount() }}</h3>
                                <small>تم التصحيح</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3 text-center">
                            <div class="stat-box text-center bg-white shadow-sm">
                                <h3>{{ number_format($assignment->averageMarks() ?? 0, 1) }}</h3>
                                <small>المتوسط</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3 text-center">
                            <div class="stat-box text-center bg-white shadow-sm">
                                <h3>#{{ $assignment->display_order }}</h3>
                                <small>الترتيب</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="btn-modern-group">
            <a href="{{ route('assignments.submissions', $assignment->id) }}" class="btn btn-primary rounded-pill px-4 shadow">
                <i class="fas fa-tasks me-2"></i> إدارة الإجابات
            </a>
            <a href="{{ route('assignments.edit', $assignment->id) }}" class="btn btn-outline-dark rounded-pill px-4">
                <i class="fas fa-edit me-2"></i> تعديل البيانات
            </a>
            <a href="{{ route('assignments.index') }}" class="btn btn-light rounded-pill px-4 text-muted">
                رجوع للقائمة
            </a>
        </div>
    </div>
</div>
@endsection