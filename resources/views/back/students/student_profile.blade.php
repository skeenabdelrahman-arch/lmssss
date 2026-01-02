@extends('back_layouts.master')

@section('title')
    بروفايل الطالب
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

    /* تنسيقات عامة للبطاقات */
    .modern-card {
        background: #ffffff;
        border-radius: var(--card-border-radius);
        box-shadow: var(--card-shadow);
        border: none;
        margin-bottom: 25px;
        transition: all 0.3s ease;
    }

    .card-header-custom {
        background: transparent;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 20px 25px;
        font-weight: 700;
        color: var(--primary-color);
        display: flex;
        align-items: center;
    }

    .card-body-custom {
        padding: 25px;
    }

    /* التنبيهات */
    .alert-custom {
        border-radius: 10px;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 15px 20px;
        display: flex;
        align-items: center;
    }
    .alert-custom-success { background-color: #d4edda; color: #155724; }
    .alert-custom-danger { background-color: #f8d7da; color: #721c24; }

    /* قسم رأس الصفحة (بيانات الطالب) */
    .student-profile-header {
        display: flex;
        align-items: center;
        gap: 30px;
    }

    .student-avatar-container {
        position: relative;
    }

    .student-avatar {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        border: 4px solid rgba(116, 36, 169, 0.1);
        padding: 3px;
        object-fit: cover;
        background: #fff;
    }

    .student-basic-info h2 {
        font-weight: 800;
        color: #333;
        margin-bottom: 10px;
        font-size: 1.8rem;
    }

    .grade-badge {
        background: rgba(116, 36, 169, 0.1);
        color: var(--primary-color);
        padding: 8px 15px;
        border-radius: 30px;
        font-weight: 600;
        display: inline-block;
    }

    /* شبكة المعلومات التفصيلية */
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
        margin-top: 25px;
        padding-top: 25px;
        border-top: 1px solid #eee;
    }

    .detail-item {
        display: flex;
        align-items: center;
    }

    .detail-icon {
        width: 45px;
        height: 45px;
        background: var(--bg-light);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        font-size: 18px;
        margin-left: 15px;
    }

    .detail-content label {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 3px;
        display: block;
    }

    .detail-content span {
        font-weight: 600;
        color: #333;
        font-size: 15px;
    }

    /* بطاقات الإحصائيات */
    .stats-box {
        background: #fff;
        padding: 25px;
        border-radius: var(--card-border-radius);
        box-shadow: var(--card-shadow);
        display: flex;
        align-items: center;
        height: 100%;
    }

    .stats-icon-circle {
        width: 65px;
        height: 65px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-left: 20px;
        flex-shrink: 0;
    }
    
    .stats-icon-circle.success { background: rgba(40, 167, 69, 0.1); color: #28a745; }
    .stats-icon-circle.info { background: rgba(23, 162, 184, 0.1); color: #17a2b8; }

    .stats-info h3 {
        font-weight: 800;
        font-size: 2rem;
        margin-bottom: 5px;
        color: #333;
    }

    .stats-info p {
        margin: 0;
        color: var(--text-muted);
        font-weight: 600;
    }

    /* قائمة الكورسات */
    .course-list-item {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s;
    }

    .course-list-item:hover {
        border-color: var(--primary-color);
        transform: translateX(-3px);
    }

    .course-title {
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 5px;
        color: #333;
    }

    .course-date {
        font-size: 13px;
        color: var(--text-muted);
    }

    /* جدول النتائج */
    .table-custom thead th {
        background: var(--bg-light);
        color: var(--text-muted);
        font-weight: 600;
        font-size: 14px;
        border-bottom: 2px solid #eee;
        padding: 15px;
    }

    .table-custom tbody td {
        padding: 15px;
        vertical-align: middle;
        color: #333;
        border-bottom: 1px solid #eee;
    }

    .degree-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 14px;
    }

    /* تحسينات الوضع الليلي */
    [data-theme="dark"] .modern-card,
    [data-theme="dark"] .stats-box,
    [data-theme="dark"] .course-list-item,
    [data-theme="dark"] .student-avatar {
        background: #1a202c !important;
        color: #e2e8f0;
    }

    [data-theme="dark"] .card-header-custom,
    [data-theme="dark"] .detail-grid,
    [data-theme="dark"] .course-list-item,
    [data-theme="dark"] .table-custom tbody td,
    [data-theme="dark"] .table-custom thead th {
        border-color: #2d3748;
    }

    [data-theme="dark"] .student-basic-info h2,
    [data-theme="dark"] .detail-content span,
    [data-theme="dark"] .stats-info h3,
    [data-theme="dark"] .course-title,
    [data-theme="dark"] .table-custom tbody td {
        color: #fff !important;
    }

    [data-theme="dark"] .detail-icon,
    [data-theme="dark"] .table-custom thead th {
        background: #2d3748;
        color: #a0aec0;
    }
    
    [data-theme="dark"] .student-avatar {
         border-color: rgba(255,255,255,0.1);
    }
</style>
@endsection

@section('page-header')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold"><i class="fas fa-user-graduate me-2" style="color: var(--primary-color)"></i> بروفايل الطالب</h4>
        <span class="text-muted">عرض البيانات الشاملة والنشاط</span>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid p-0">
    
    {{-- Alerts Area --}}
    @if(session()->has('error'))
        <div class="alert alert-custom alert-custom-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><strong>{{ session()->get('error') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif(session()->has('success'))
        <div class="alert alert-custom alert-custom-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i><strong>{{ session()->get('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="modern-card">
        <div class="card-body-custom">
            <div class="student-profile-header">
                <div class="student-avatar-container">
                    @if($student->image)
                        <img src="{{ url('upload_files/' . $student->image) }}" alt="صورة الطالب" class="student-avatar shadow-sm">
                    @else
                        <div class="student-avatar shadow-sm d-flex align-items-center justify-content-center" style="font-size: 50px; color: var(--primary-color); background: var(--bg-light);">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                </div>
                <div class="student-basic-info">
                    <h2>{{ $student->first_name }} {{ $student->second_name }} {{ $student->third_name }} {{ $student->forth_name }}</h2>
                    @php
                        $gradeLabel = $student->grade;
                        foreach(signup_grades() as $grade) {
                            if($grade['value'] == $student->grade) {
                                $gradeLabel = $grade['label'];
                                break;
                            }
                        }
                    @endphp
                    <span class="grade-badge">
                        <i class="fas fa-graduation-cap me-2"></i> {{ $gradeLabel }}
                    </span>
                </div>
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-icon"><i class="fas fa-phone-alt"></i></div>
                    <div class="detail-content">
                        <label>هاتف الطالب</label>
                        <span dir="ltr">{{ $student->student_phone }}</span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-icon"><i class="fas fa-users"></i></div>
                    <div class="detail-content">
                        <label>هاتف ولي الأمر</label>
                        <span dir="ltr">{{ $student->parent_phone }}</span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-icon"><i class="fas fa-envelope"></i></div>
                    <div class="detail-content">
                        <label>البريد الإلكتروني</label>
                        <span>{{ $student->email ?? 'غير متوفر' }}</span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="detail-content">
                        <label>المدينة</label>
                        <span>{{ $student->city ?? 'غير محدد' }}</span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-icon"><i class="fas fa-info-circle"></i></div>
                    <div class="detail-content">
                        <label>نوع التسجيل</label>
                        <span>{{ $student->register ?? 'اونلاين' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-lg-3 col-md-6">
            <div class="stats-box">
                <div class="stats-icon-circle success">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ $exam_results->filter(function($er) { return $er->exam !== null; })->count() }}</h3>
                    <p>امتحان تم اجتيازه</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stats-box">
                <div class="stats-icon-circle info">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stats-info">
                    <h3>{{ $months->where('is_active', 1)->count() }}</h3>
                    <p>كورس مفعل حالياً</p>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="modern-card h-100 mb-0">
                <div class="card-header-custom">
                    <i class="fas fa-plus-circle me-2"></i> إضافة الطالب إلى كورس جديد
                </div>
                <div class="card-body-custom d-flex align-items-center">
                    <form action="{{ route('admin.student.addCourse', $student->id) }}" method="POST" class="w-100">
                        @csrf
                        <div class="input-group">
                            <select class="form-control form-select-lg" name="month_id" required>
                                <option value="">اختر الكورس من القائمة...</option>
                                @if($availableMonths && $availableMonths->count() > 0)
                                    @foreach($availableMonths as $month)
                                        @if(!in_array($month->id, $subscribedMonthIds))
                                            <option value="{{ $month->id }}">{{ $month->name }} ({{ $month->grade }})</option>
                                        @endif
                                    @endforeach
                                @else
                                    <option value="" disabled>لا توجد كورسات متاحة</option>
                                @endif
                            </select>
                            <button type="submit" class="btn btn-primary px-4">
                                إضافة <i class="fas fa-arrow-left ms-1"></i>
                            </button>
                        </div>
                         @if($availableMonths && $availableMonths->whereNotIn('id', $subscribedMonthIds)->count() == 0)
                            <small class="text-success mt-2 d-block">
                                <i class="fas fa-check-circle me-1"></i> الطالب مشترك في جميع الكورسات المتاحة لهذا الصف.
                            </small>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="modern-card h-100">
                <div class="card-header-custom">
                    <i class="fas fa-book-open me-2"></i> الكورسات المشترك فيها
                </div>
                <div class="card-body-custom p-3">
                    @if($months->count() > 0)
                        @foreach($months as $subscription)
                        <div class="course-list-item">
                            <div>
                                <div class="course-title">
                                    @if($subscription->is_active)
                                        <i class="fas fa-circle text-success me-2" style="font-size: 10px;"></i>
                                    @else
                                        <i class="fas fa-circle text-danger me-2" style="font-size: 10px;"></i>
                                    @endif
                                    {{ $subscription->month ? $subscription->month->name : 'شهر محذوف' }}
                                </div>
                                <div class="course-date">
                                    <i class="far fa-calendar-alt me-1"></i> {{ $subscription->created_at->format('Y-m-d') }}
                                    <span class="badge ms-2 {{ $subscription->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $subscription->is_active ? 'مفعل' : 'غير مفعل' }}
                                    </span>
                                </div>
                            </div>
                            <div class="ms-3">
                                @if($subscription->is_active == 1)
                                    <form action="{{ route('admin.student.removeCourse', ['id' => $student->id, 'subscription_id' => $subscription->id]) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء تفعيل هذا الاشتراك؟')">
                                        @csrf @method('POST')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="إلغاء التفعيل">
                                            <i class="fas fa-times"></i> إلغاء
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.student.addCourse', $student->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="month_id" value="{{ $subscription->month_id }}">
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="تفعيل الاشتراك">
                                            <i class="fas fa-check"></i> تفعيل
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-folder-open fa-3x mb-3" style="opacity: 0.3"></i>
                            <p>لا توجد اشتراكات حالياً</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="modern-card h-100">
                <div class="card-header-custom">
                    <i class="fas fa-file-signature me-2"></i> سجل نتائج الامتحانات
                </div>
                <div class="card-body-custom p-0 table-responsive">
                    @php
                        $validExamResults = $exam_results->filter(function($er) { return $er->exam !== null; });
                    @endphp
                    
                    @if($validExamResults->count() > 0)
                        <table class="table table-custom table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">#</th>
                                    <th width="40%">اسم الامتحان</th>
                                    <th class="text-center" width="30%">الدرجة / النسبة</th>
                                    <th width="25%">التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($validExamResults as $exam_result)
                                    @php
                                        $exam_degree = $exam_result->exam->questions->sum('Q_degree');
                                        $percentage = $exam_degree > 0 ? round(($exam_result->degree / $exam_degree) * 100, 1) : 0;
                                        $badgeClass = $percentage >= 85 ? 'bg-success' : ($percentage >= 65 ? 'bg-info' : ($percentage >= 50 ? 'bg-warning text-dark' : 'bg-danger'));
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="fw-bold">{{ $exam_result->exam->exam_title }}</td>
                                        <td class="text-center">
                                            <span style="font-weight: 700; font-size: 16px;">{{ $exam_result->degree }}</span> 
                                            <span class="text-muted small">/ {{ $exam_degree }}</span>
                                            <div class="mt-1">
                                                <span class="badge {{ $badgeClass }}">{{ $percentage }}%</span>
                                            </div>
                                        </td>
                                        <td class="text-muted">
                                            <i class="far fa-clock me-1"></i> {{ $exam_result->created_at->format('Y-m-d') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-clipboard-list fa-3x mb-3" style="opacity: 0.3"></i>
                            <p>لم يقم الطالب بأداء أي امتحانات بعد</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // إخفاء رسائل التنبيه تلقائياً
    $(document).ready(function() {
        setTimeout(function() {
            $('.alert').slideUp(500);
        }, 4000);
    });
</script>
@endsection