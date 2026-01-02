@extends('back_layouts.master')

@section('title')
    استيراد الاشتراكات من Excel
@stop

@section('css')
<style>
    :root {
        --card-border-radius: 12px;
        --card-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        --primary-color: #7424a9;
        --orange-color: #ff8c00; /* درجة برتقالي واضحة وصلبة */
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

    /* صناديق الإرشاد */
    .instruction-box {
        background-color: #f0f7ff;
        border-right: 4px solid #007bff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .instruction-box-warning {
        background-color: #fff9eb;
        border-right: 4px solid #ffc107;
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
        font-weight: 700;
        transition: all 0.3s ease;
        border: none !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    /* الزر البرتقالي المحدث - يظهر برتقالي دائماً */
    .btn-orange-fixed { 
        background-color: var(--orange-color) !important; 
        color: #ffffff !important; 
        box-shadow: 0 4px 10px rgba(255, 140, 0, 0.3);
    }
    
    .btn-orange-fixed:hover { 
        background-color: #e67e00 !important; 
        color: #ffffff !important;
        transform: translateY(-2px); 
        box-shadow: 0 6px 15px rgba(255, 140, 0, 0.4);
    }

    .btn-modern-success { background: #28a745; color: white; }
    .btn-modern-success:hover { background: #218838; transform: translateY(-2px); color: white; }

    .btn-modern-primary { background: var(--primary-color); color: white; }
    .btn-modern-primary:hover { background: #5a1c83; transform: translateY(-2px); color: white; }

    .form-label { font-weight: 600; color: #444; }

    [data-theme="dark"] .modern-card { background: #1a202c !important; color: #fff; }
    [data-theme="dark"] .instruction-box { background: #2d3748; border-color: var(--primary-color); }
</style>
@endsection

@section('page-header')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold"><i class="fas fa-file-invoice-dollar me-2" style="color: #17a2b8"></i> استيراد اشتراكات الكورسات</h4>
        <span class="text-muted">تفعيل الشهور للطلاب عبر Excel أو الإدخال السريع</span>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid p-0">

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
                <h6 class="fw-bold mb-3 px-3 text-info">نتائج عملية الاستيراد:</h6>
                <div class="row text-center">
                    <div class="col-md-6 border-end">
                        <h4 class="text-success fw-bold mb-0">{{ $stats['success'] }}</h4>
                        <small class="text-muted">اشتراكات تم تفعيلها</small>
                    </div>
                    <div class="col-md-6">
                        <h4 class="text-danger fw-bold mb-0">{{ $stats['errors'] }}</h4>
                        <small class="text-muted">أخطاء في البيانات</small>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <div class="row">
        {{-- استيراد Excel --}}
        <div class="col-lg-12">
            <div class="modern-card">
                <h5 class="section-title"><i class="fas fa-file-excel me-2 text-success"></i> الاستيراد عبر ملف Excel</h5>
                
                <div class="instruction-box">
                    <p class="text-muted small mb-3">ترتيب الأعمدة المطلوبة:</p>
                    <div class="excel-column-list mb-3">
                        <div class="column-badge"><strong>A</strong> رقم الهاتف</div>
                        <div class="column-badge"><strong>B</strong> اسم الشهر أو ID</div>
                        <div class="column-badge"><strong>C</strong> الصف</div>
                    </div>
                </div>

                <form action="{{ route('admin.excel.import.subscriptions') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">ملف Excel <span class="text-danger">*</span></label>
                            <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">تخصيص شهر معين</label>
                            <select name="month_id" class="form-select select2">
                                <option value="">-- من الملف مباشرة --</option>
                                @foreach($months as $month)
                                    <option value="{{ $month->id }}">{{ $month->name }} - {{ $month->grade }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-modern btn-modern-success w-100">
                                <i class="fas fa-upload me-1"></i> رفع الملف
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- الإدخال اليدوي --}}
        <div class="col-lg-12">
            <div class="modern-card">
                <h5 class="section-title"><i class="fas fa-keyboard me-2 text-primary"></i> تفعيل سريع (أرقام الهواتف)</h5>
                
                <div class="instruction-box instruction-box-warning">
                    <ul class="small text-muted mb-0">
                        <li>ضع كل رقم هاتف في سطر مستقل.</li>
                        <li>اختيار الشهر إلزامي في هذه الطريقة.</li>
                    </ul>
                </div>

                <form action="{{ route('admin.excel.import.subscriptions.manual') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-7">
                            <label class="form-label text-primary fw-bold">أرقام الهواتف:</label>
                            <textarea name="phone_numbers" class="form-control" rows="6" placeholder="010xxxxxxx&#10;011xxxxxxx" required></textarea>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label">اختر الشهر <span class="text-danger">*</span></label>
                                <select name="manual_month_id" class="form-select select2" required>
                                    <option value="">-- اختر الشهر --</option>
                                    @foreach($months as $month)
                                        <option value="{{ $month->id }}">{{ $month->name }} - {{ $month->grade }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">الصف الدراسي (اختياري)</label>
                                <input type="text" name="manual_grade" class="form-control" placeholder="مثال: الصف الأول الثانوي">
                            </div>
                            
                            {{-- الزر البرتقالي المحدث --}}
                            <button type="submit" class="btn btn-modern btn-orange-fixed w-100 mt-2">
                                <i class="fas fa-magic me-2"></i> تفعيل الاشتراكات الآن
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-12 text-center mb-5">
            <a href="{{ route('admin.excel.import.students') }}" class="btn btn-outline-secondary px-4">
                <i class="fas fa-user-plus me-1"></i> العودة لاستيراد الطلاب
            </a>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        if ($('.select2').length > 0) {
            $('.select2').select2({ dir: "rtl", width: '100%' });
        }
    });
</script>
@endsection