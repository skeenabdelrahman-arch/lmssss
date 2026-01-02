@extends('back_layouts.master')

@section('title')
    إلغاء تفعيل الاشتراكات جماعياً
@stop

@section('css')
<style>
    :root {
        --card-border-radius: 12px;
        --card-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        --primary-color: #7424a9;
        --danger-color: #dc3545;
        --bg-light: #f8f9fa;
        --text-muted: #6c757d;
    }

    .modern-card {
        background: #ffffff;
        border-radius: var(--card-border-radius);
        box-shadow: var(--card-shadow);
        border: none;
        padding: 25px;
        margin-bottom: 25px;
    }

    .section-title {
        font-weight: 700;
        color: var(--danger-color); /* أحمر للتنبيه */
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }

    .instruction-box {
        background-color: #fff5f5;
        border-right: 4px solid var(--danger-color);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .alert-modern {
        border-radius: 10px;
        border: none;
        padding: 15px 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .btn-modern {
        border-radius: 8px;
        padding: 12px 25px;
        font-weight: 600;
        transition: all 0.3s;
        border: none;
    }

    .btn-modern-danger { background: var(--danger-color); color: white; }
    .btn-modern-danger:hover { background: #bb2d3b; transform: translateY(-2px); color: white; }

    .btn-modern-success { background: #28a745; color: white; }
    .btn-modern-success:hover { background: #218838; transform: translateY(-2px); color: white; }

    .table-custom thead th {
        background: var(--bg-light);
        color: var(--text-muted);
        font-weight: 600;
        border-bottom: 2px solid #eee;
    }

    .table-custom tbody td {
        vertical-align: middle;
        border-bottom: 1px solid #eee;
    }

    .form-label { font-weight: 600; color: #444; }

    [data-theme="dark"] .modern-card { background: #1a202c !important; color: #fff; }
    [data-theme="dark"] .instruction-box { background: #2d3035; border-color: var(--danger-color); }
</style>
@endsection

@section('page-header')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-danger"><i class="fas fa-user-times me-2"></i> إلغاء تفعيل الاشتراكات جماعياً</h4>
        <span class="text-muted">إيقاف وصول الطلاب للكورسات بناءً على أرقام الهواتف</span>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid p-0">

    {{-- رسائل الحالة --}}
    @if(session()->has('error'))
        <div class="alert alert-modern alert-danger mb-4">
            <i class="fas fa-exclamation-circle me-2"></i><strong>{{ session()->get('error') }}</strong>
        </div>
    @elseif(session()->has('success'))
        <div class="alert alert-modern alert-success mb-3">
            <i class="fas fa-check-circle me-2"></i><strong>{{ session()->get('success') }}</strong>
        </div>
        
        @if(session()->has('deactivate_stats'))
            @php $stats = session('deactivate_stats'); @endphp
            <div class="modern-card border-start border-danger border-4 py-3 mb-4">
                <h6 class="fw-bold mb-3 px-3 text-danger">ملخص عملية الإلغاء:</h6>
                <div class="row text-center">
                    <div class="col-md-4 border-end">
                        <h4 class="text-danger fw-bold mb-0">{{ $stats['success'] }}</h4>
                        <small class="text-muted">اشتراك تم إيقافه</small>
                    </div>
                    <div class="col-md-4 border-end">
                        <h4 class="text-warning fw-bold mb-0">{{ $stats['not_found'] }}</h4>
                        <small class="text-muted">أرقام غير موجودة</small>
                    </div>
                    <div class="col-md-4">
                        <h4 class="text-muted fw-bold mb-0">{{ $stats['errors'] }}</h4>
                        <small class="text-muted">أخطاء تقنية</small>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <div class="row g-4">
        {{-- الطريقة الأولى: رفع ملف Excel --}}
        <div class="col-lg-6">
            <div class="modern-card h-100">
                <h5 class="section-title"><i class="fas fa-file-excel me-2"></i> الإلغاء عبر ملف Excel</h5>
                
                <div class="instruction-box mb-4">
                    <h6 class="fw-bold text-danger"><i class="fas fa-exclamation-triangle me-2"></i> تحذير:</h6>
                    <p class="small mb-0">سيتم إلغاء تفعيل <strong>جميع الاشتراكات</strong> لكل رقم هاتف بالملف. تأكد أن الملف يحتوي على أرقام الهواتف في <strong>العمود A</strong>.</p>
                </div>

                <form action="{{ route('admin.excel.deactivate.subscriptions') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('هل أنت متأكد من إلغاء تفعيل جميع الاشتراكات؟');">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">ملف Excel (.xlsx, .xls)</label>
                        <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">سبب الإلغاء (يظهر للطالب)</label>
                        <textarea name="deactivation_reason" class="form-control" rows="2" required>تم إلغاء التفعيل بسبب خروجك من السنتر</textarea>
                    </div>
                    <button type="submit" class="btn btn-modern btn-modern-danger w-100">
                        <i class="fas fa-user-slash me-2"></i> تنفيذ الإيقاف الجماعي
                    </button>
                </form>
            </div>
        </div>

        {{-- الطريقة الثانية: إدخال يدوي --}}
        <div class="col-lg-6">
            <div class="modern-card h-100">
                <h5 class="section-title text-primary"><i class="fas fa-keyboard me-2"></i> إدخال يدوي سريع</h5>
                
                <p class="text-muted small mb-3">أدخل أرقام الهواتف (رقم واحد في كل سطر):</p>

                <form action="{{ route('admin.excel.deactivate.subscriptions.manual') }}" method="POST" onsubmit="return confirm('تأكيد الإيقاف اليدوي لهؤلاء الطلاب؟');">
                    @csrf
                    <div class="mb-3">
                        <textarea name="phone_numbers" class="form-control" rows="5" placeholder="010xxxxxxx&#10;011xxxxxxx" required></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">سبب الإلغاء</label>
                        <textarea name="deactivation_reason" class="form-control" rows="2" required>تم إلغاء التفعيل بسبب خروجك من السنتر</textarea>
                    </div>
                    <button type="submit" class="btn btn-modern btn-modern-danger w-100">
                        <i class="fas fa-bolt me-2"></i> إيقاف فوري للأرقام المدخلة
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- جدول الطلاب الملغي تفعيلهم حالياً --}}
    <div class="modern-card mt-4">
        <h5 class="section-title text-dark"><i class="fas fa-list-ul me-2 text-muted"></i> قائمة الطلاب الموقوفين حالياً</h5>
        
        @php
            $deactivatedSubscriptions = \App\Models\StudentSubscriptions::with(['student', 'month'])
                ->where('is_active', 0)
                ->whereNotNull('deactivation_reason')
                ->orderBy('updated_at', 'desc')
                ->get()
                ->groupBy('student_id');
        @endphp
        
        @if($deactivatedSubscriptions->count() > 0)
            <div class="table-responsive">
                <table class="table table-custom table-hover">
                    <thead>
                        <tr>
                            <th>الطالب</th>
                            <th>رقم الهاتف</th>
                            <th>الكورسات المتأثرة</th>
                            <th>السبب والتاريخ</th>
                            <th width="120px">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deactivatedSubscriptions as $studentId => $subscriptions)
                            @php
                                $student = $subscriptions->first()->student;
                                $firstSub = $subscriptions->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $student->first_name }} {{ $student->forth_name }}</div>
                                    <small class="text-muted">ID: {{ $student->id }}</small>
                                </td>
                                <td><code class="text-dark">{{ $student->student_phone }}</code></td>
                                <td>
                                    <span class="badge bg-danger mb-1">{{ $subscriptions->count() }} كورس</span><br>
                                    <small class="text-muted">
                                        {{ implode('، ', $subscriptions->pluck('month.name')->filter()->toArray()) }}
                                    </small>
                                </td>
                                <td>
                                    <div class="small text-danger">{{ $firstSub->deactivation_reason }}</div>
                                    <div class="small text-muted">{{ $firstSub->updated_at->format('Y-m-d') }}</div>
                                </td>
                                <td>
                                    <form action="{{ route('admin.excel.reactivate.student') }}" method="POST" onsubmit="return confirm('إعادة تفعيل كافة الاشتراكات لهذا الطالب؟');">
                                        @csrf
                                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                                        <button type="submit" class="btn btn-sm btn-modern btn-modern-success w-100 py-1">
                                            <i class="fas fa-redo"></i> تفعيل
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-user-check fa-3x text-light mb-3"></i>
                <p class="text-muted">لا يوجد طلاب ملغي تفعيلهم حالياً بالسبب المحدد.</p>
            </div>
        @endif
    </div>

    {{-- أزرار التنقل --}}
    <div class="d-flex gap-3 justify-content-center mb-5">
        <a href="{{ route('admin.excel.import.subscriptions') }}" class="btn btn-outline-success px-4">
            <i class="fas fa-plus me-1"></i> العودة لشاشة تفعيل الاشتراكات
        </a>
        <a href="{{ url('/admin') }}" class="btn btn-outline-secondary px-4">
            <i class="fas fa-home me-1"></i> الرئيسية
        </a>
    </div>
</div>
@endsection