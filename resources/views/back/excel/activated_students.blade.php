@extends('back_layouts.master')

@section('title')
    الطلاب المفعلين من Excel
@stop

@section('css')
<style>
    /* التنسيقات العامة للحاويات */
    .modern-card {
        background: #ffffff;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        border: none;
    }

    /* كروت الإحصائيات */
    .stats-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        border: 1px solid #f1f1f1;
        transition: all 0.3s ease;
        height: 100%;
    }
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(116, 36, 169, 0.1);
    }
    .stats-card .number {
        font-size: 2rem;
        font-weight: 800;
        color: #7424a9;
        display: block;
    }
    .stats-card .label {
        color: #6c757d;
        font-size: 0.9rem;
        font-weight: 600;
    }

    /* كرت الفلترة */
    .filter-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
        border-right: 5px solid #7424a9;
    }

    /* بطاقة الطالب */
    .student-card {
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
        border: 1px solid #eee;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .student-card:hover {
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .student-header {
        background: linear-gradient(45deg, #7424a9, #9d50bb);
        padding: 15px;
        color: white;
    }
    .student-header.not-activated {
        background: linear-gradient(45deg, #495057, #6c757d);
    }

    .student-avatar {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        border: 2px solid rgba(255,255,255,0.8);
        object-fit: cover;
    }

    .student-body {
        padding: 18px;
        flex-grow: 1;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 1px dashed #f0f0f0;
    }
    .info-row:last-child { border: none; }
    .info-label { color: #888; font-size: 0.85rem; }
    .info-value { color: #333; font-weight: 600; font-size: 0.9rem; }

    /* الاشتراكات */
    .subscription-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 10px;
    }
    .sub-pill {
        background: #f0e6f7;
        color: #7424a9;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        border: 1px solid #e1d0ed;
    }

    /* الأزرار */
    .btn-modern-action {
        border-radius: 8px;
        font-weight: 600;
        padding: 8px 15px;
        transition: 0.3s;
    }
    .btn-view { background: #212529; color: #fff; }
    .btn-view:hover { background: #7424a9; color: #fff; }

    /* Pagination */
    .pagination-wrapper .pagination { gap: 5px; }
    .pagination-wrapper .page-link {
        border-radius: 8px !important;
        border: none;
        background: #f8f9fa;
        color: #333;
    }
    .pagination-wrapper .page-item.active .page-link {
        background: #7424a9;
    }
</style>
@endsection

@section('page-header')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="fas fa-users-cog text-primary me-2"></i> إدارة طلاب Excel</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/admin') }}">الرئيسية</a></li>
                <li class="breadcrumb-item active">الطلاب المستوردين</li>
            </ol>
        </nav>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid p-0">
    
    {{-- الإحصائيات --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stats-card">
                <span class="number">{{ $students->total() }}</span>
                <span class="label">إجمالي المفعلين</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stats-card">
                <span class="number">{{ isset($notActivatedCount) ? $notActivatedCount : 0 }}</span>
                <span class="label">غير المفعلين</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stats-card">
                <span class="number">{{ $students->count() }}</span>
                <span class="label">طلاب الصفحة</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stats-card">
                <span class="number">{{ $students->lastPage() }}</span>
                <span class="label">إجمالي الصفحات</span>
            </div>
        </div>
    </div>

    {{-- الفلترة --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.excel.activated.students') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small fw-bold">الصف الدراسي</label>
                <select name="grade" class="form-select border-0 shadow-sm">
                    <option value="">كل الصفوف</option>
                    @foreach($grades as $grade)
                        <option value="{{ $grade['value'] }}" {{ request('grade') == $grade['value'] ? 'selected' : '' }}>{{ $grade['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">الشهر</label>
                <select name="month_id" class="form-select border-0 shadow-sm">
                    <option value="">كل الأشهر</option>
                    @foreach($months as $month)
                        <option value="{{ $month->id }}" {{ request('month_id') == $month->id ? 'selected' : '' }}>{{ $month->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">بحث سريع</label>
                <input type="text" name="search" class="form-control border-0 shadow-sm" placeholder="الاسم، الكود، الهاتف..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold border-0" style="background: #7424a9;">
                    <i class="fas fa-search"></i>
                </button>
                @if(request()->hasAny(['grade', 'month_id', 'search']))
                    <a href="{{ route('admin.excel.activated.students') }}" class="btn btn-light shadow-sm">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- قائمة الطلاب المفعلين --}}
    <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">الطلاب الذين لديهم اشتراكات حالية</h5>
    
    @if($students->count() > 0)
        <div class="row g-4">
            @foreach($students as $student)
                <div class="col-xl-4 col-lg-6">
                    <div class="student-card">
                        <div class="student-header d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                @if($student->image)
                                    <img src="{{ url('upload_files/' . $student->image) }}" class="student-avatar">
                                @else
                                    <div class="student-avatar d-flex align-items-center justify-content-center bg-white bg-opacity-25">
                                        <i class="fas fa-user fa-lg"></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $student->first_name }} {{ $student->second_name }} {{ $student->third_name }} {{ $student->forth_name }}</h6>
                                    <small class="text-white-50">ID: {{ $student->id }}</small>
                                </div>
                            </div>
                            <div class="badge bg-white text-dark rounded-pill px-3">{{ $student->grade }}</div>
                        </div>
                        
                        <div class="student-body">
                            <div class="info-row">
                                <span class="info-label"><i class="fas fa-phone-alt me-1"></i> الهاتف</span>
                                <span class="info-value">{{ $student->student_phone }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label"><i class="fas fa-key me-1"></i> كلمة السر</span>
                                <span class="info-value"><code>{{ $student->password }}</code></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label"><i class="fas fa-calendar-check me-1"></i> الكورسات</span>
                                <div class="subscription-pills">
                                    @forelse($student->subscriptions as $sub)
                                        <span class="sub-pill">{{ $sub->month->name ?? 'مجهول' }}</span>
                                    @empty
                                        <span class="text-muted small">لا يوجد</span>
                                    @endforelse
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2 mt-4">
                                <a href="{{ url('student-profile/'.$student->id) }}" class="btn btn-deactivate-action flex-fill btn-sm" style="background:#212529 !important; color:#fff !important; transition:0.3s;">
                                    <i class="fas fa-eye me-1"></i> البروفايل
                                </a>
                                <a href="{{ url('students-male/edit/'.$student->id) }}" class="btn btn-light btn-sm shadow-sm border">
                                    <i class="fas fa-edit text-warning"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pagination-wrapper d-flex justify-content-center mt-5">
            {{ $students->links() }}
        </div>
    @else
        <div class="text-center py-5 modern-card">
            <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" style="width: 80px; opacity: 0.3">
            <p class="mt-3 text-muted">لم يتم العثور على طلاب مطابقين للبحث.</p>
        </div>
    @endif

    {{-- الطلاب غير المفعلين --}}
    @if(isset($notActivatedStudents) && $notActivatedStudents->count() > 0)
        <div class="section-divider mt-5"></div>
        <h5 class="fw-bold mb-4 text-secondary mt-4"><i class="fas fa-user-slash me-2"></i> طلاب مستوردين بدون اشتراك ({{ $notActivatedStudents->count() }})</h5>
        
        <div class="row g-4 mb-5">
            @foreach($notActivatedStudents as $student)
                <div class="col-xl-4 col-lg-6">
                    <div class="student-card shadow-sm border-0">
                        <div class="student-header not-activated d-flex justify-content-between align-items-center">
                            <span class="fw-bold small">{{ $student->first_name }} {{ $student->second_name }}</span>
                            <span class="badge bg-danger rounded-pill">غير مفعل</span>
                        </div>
                        <div class="student-body py-2 px-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">هاتف: {{ $student->student_phone }}</small>
                                <a href="{{ url('student-profile/'.$student->id) }}" class="text-primary small fw-bold">عرض <i class="fas fa-arrow-left ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection