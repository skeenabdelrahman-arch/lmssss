@extends('back_layouts.master')

@section('title')
    استيراد الطلاب من Excel
@stop

@section('css')
<style>
    :root {
        --card-border-radius: 12px;
        --card-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        --primary-color: #7424a9;
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
        color: var(--primary-color);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }

    /* تنسيق التعليمات */
    .instruction-box {
        background-color: #f0f7ff;
        border-right: 4px solid #007bff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .instruction-box h5 {
        color: #0056b3;
        font-weight: 700;
        font-size: 16px;
    }

    .excel-column-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
    }

    .column-badge {
        background: #fff;
        border: 1px solid #dee2e6;
        padding: 8px 15px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .column-badge strong { color: var(--primary-color); }

    /* تنبيهات الحالة */
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

    .btn-modern-success { background: #28a745; color: white; }
    .btn-modern-success:hover { background: #218838; transform: translateY(-2px); color: white; }

    .btn-modern-primary { background: var(--primary-color); color: white; }
    .btn-modern-primary:hover { background: #5a1c83; transform: translateY(-2px); color: white; }

    .form-label { font-weight: 600; color: #444; }
    
    [data-theme="dark"] .modern-card { background: #1a202c !important; color: #fff; }
    [data-theme="dark"] .instruction-box { background: #2d3748; border-color: var(--primary-color); }
    [data-theme="dark"] .column-badge { background: #1a202c; color: #fff; border-color: #4a5568; }
</style>
@endsection

@section('page-header')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold"><i class="fas fa-file-excel me-2" style="color: #28a745"></i> استيراد الطلاب من Excel</h4>
        <span class="text-muted">إضافة وتحديث بيانات الطلاب دفعة واحدة</span>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid p-0">

    {{-- رسائل التنبيه والنتائج --}}
    @if(session()->has('error'))
        <div class="alert alert-modern alert-danger mb-4">
            <i class="fas fa-exclamation-circle me-2"></i><strong>{{ session()->get('error') }}</strong>
        </div>
    @elseif(session()->has('success'))
        <div class="alert alert-modern alert-success mb-3">
            <i class="fas fa-check-circle me-2"></i><strong>{{ session()->get('success') }}</strong>
        </div>
        
        @if(session()->has('import_stats'))
            @php $stats = session('import_stats'); @endphp
            <div class="modern-card border-start border-info border-4 py-3 mb-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-list-check me-2 text-info"></i> ملخص عملية الاستيراد:</h6>
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="p-2">
                            <h4 class="text-success fw-bold">{{ $stats['success'] }}</h4>
                            <small class="text-muted">تم استيرادهم بنجاح</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2">
                            <h4 class="text-danger fw-bold">{{ $stats['errors'] }}</h4>
                            <small class="text-muted">قيود تحتوي على أخطاء</small>
                        </div>
                    </div>
                </div>
                
                @if(!empty($stats['error_details']))
                    <div class="mt-3 px-3">
                        <small class="text-danger fw-bold">الأخطاء التي حدثت:</small>
                        <ul class="small text-danger mt-1">
                            @foreach($stats['error_details'] as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif
    @endif

    <div class="modern-card">
        {{-- تعليمات الملف --}}
        <div class="instruction-box">
            <h5><i class="fas fa-lightbulb me-2"></i> كيف تجهز ملف الـ Excel؟</h5>
            <p class="text-muted small mb-0">تأكد أن الملف يبدأ من الصف الثاني (الصف الأول للعناوين) وبالترتيب التالي للأعمدة:</p>
            
            <div class="excel-column-list">
                <div class="column-badge"><strong>A</strong> الاسم بالكامل</div>
                <div class="column-badge"><strong>B</strong> رقم الموبايل</div>
                <div class="column-badge"><strong>C</strong> رقم ولي الأمر</div>
                <div class="column-badge"><strong>D</strong> كلمة المرور</div>
            </div>

            <div class="mt-4 pt-3 border-top border-light">
                <div class="row g-2">
                    <div class="col-md-6 small text-muted"><i class="fas fa-check me-1 text-success"></i> سيتم تقسيم الاسم تلقائياً لأربعة أسماء.</div>
                    <div class="col-md-6 small text-muted"><i class="fas fa-check me-1 text-success"></i> سيتم تحديث البيانات إذا كان الطالب مسجلاً مسبقاً.</div>
                </div>
            </div>
        </div>

        {{-- نموذج الرفع --}}
        <h5 class="section-title"><i class="fas fa-upload me-2"></i> بيانات الاستيراد</h5>
        <form action="{{ route('admin.excel.import.students') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">الصف الدراسي الموحد <span class="text-danger">*</span></label>
                    <select name="grade" class="form-select form-select-lg" required>
                        <option value="">-- اختر الصف الدراسي --</option>
                        @foreach(signup_grades() as $grade)
                            <option value="{{ $grade['value'] }}" {{ old('grade') == $grade['value'] ? 'selected' : '' }}>
                                {{ $grade['label'] }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">هذا الصف سيُطبق على جميع الطلاب الموجودين بالملف.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">ملف الـ Excel المطلوب <span class="text-danger">*</span></label>
                    <input type="file" name="excel_file" class="form-control form-control-lg" accept=".xlsx,.xls" required>
                    <div class="form-text">الصيغ المقبولة: .xlsx أو .xls فقط.</div>
                </div>

                <div class="col-12 mt-5 d-flex gap-3">
                    <button type="submit" class="btn btn-modern btn-modern-success">
                        <i class="fas fa-cloud-upload-alt me-2"></i> بدء استيراد الطلاب الآن
                    </button>
                    <a href="{{ route('admin.excel.import.subscriptions') }}" class="btn btn-modern btn-modern-primary">
                        <i class="fas fa-tags me-2"></i> استيراد اشتراكات فقط
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection