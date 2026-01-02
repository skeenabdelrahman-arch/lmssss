@extends('back_layouts.master')

@section('title')
الطلاب المحظورين
@endsection

@section('css')
<style>
    /* تنسيقات المودرن كارد */
    .modern-card {
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }
    
    .page-header-area {
        background: #fff;
        padding: 20px;
        border-radius: 15px;
        margin-bottom: 25px;
        border-right: 5px solid #ffc107; /* لون تحذيري */
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }

    .table thead th {
        background-color: #f8f9fa;
        color: #333;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        border-top: none;
    }

    .student-avatar {
        width: 35px;
        height: 35px;
        background: #eee;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 10px;
        color: #666;
    }

    .badge-attempts {
        font-size: 14px;
        padding: 6px 12px;
        border-radius: 8px;
    }

    .btn-modern-warning {
        background: linear-gradient(45deg, #ffc107, #ff9800);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-modern-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 152, 0, 0.3);
        color: #fff;
    }

    /* تأثير عند اختيار الصف */
    tr.selected-row {
        background-color: #fff9e6 !important;
    }
</style>
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex align-items-center">
            <h4 class="content-title mb-0 my-auto text-primary"><i class="fas fa-user-lock me-2"></i> الأمان</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ الطلاب المحظورين</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    
    <div class="page-header-area d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="mb-1 fw-bold"><i class="fas fa-ban text-danger me-2"></i> قائمة الحظر المؤقت</h2>
            <p class="text-muted mb-0">إدارة الطلاب الذين تم تقييد دخولهم بسبب تجاوز محاولات تسجيل الدخول.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-modern-warning shadow-sm" onclick="unblockSelected()">
                <i class="fas fa-unlock-alt me-2"></i> فك حظر المحددين
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-15 animate__animated animate__fadeIn">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
    </div>
    @endif

    <div class="modern-card">
        <div class="card-body p-0">
            @if($blockedStudents->count() > 0)
            <form id="unblockForm" action="{{ route('admin.blocked_students.unblock_multiple') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </div>
                                </th>
                                <th>الطالب</th>
                                <th>بيانات التواصل</th>
                                <th class="text-center">المحاولات</th>
                                <th>تاريخ الحظر</th>
                                <th>ينتهي في</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($blockedStudents as $student)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox form-check-input">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="student-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $student->first_name }} {{ $student->forth_name }}</div>
                                            <small class="text-muted">ID: #{{ $student->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small"><i class="fas fa-phone-alt me-1 text-muted"></i> {{ $student->student_phone }}</div>
                                    <div class="small text-muted"><i class="fas fa-envelope me-1 text-muted"></i> {{ $student->email }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger-transparent text-danger fw-bold badge-attempts">
                                        {{ $student->failed_login_attempts ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small fw-bold">
                                        {{ $student->last_failed_login_at ? $student->last_failed_login_at->format('Y-m-d') : '-' }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $student->last_failed_login_at ? $student->last_failed_login_at->format('H:i A') : '' }}
                                    </div>
                                </td>
                                <td>
                                    @if($student->blocked_until)
                                        <span class="text-warning fw-bold small">
                                            <i class="far fa-clock me-1"></i> {{ $student->blocked_until->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark">دائم</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.blocked_students.unblock', $student->id) }}" 
                                       class="btn btn-sm btn-outline-success rounded-pill px-3"
                                       onclick="return confirm('هل أنت متأكد من إزالة الحظر؟')">
                                        <i class="fas fa-unlock me-1"></i> فك الحظر
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="p-3 border-top">
                {{ $blockedStudents->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-user-check fa-4x text-light"></i>
                </div>
                <h4 class="text-muted">نظيف تماماً! لا يوجد طلاب محظورين</h4>
                <p class="text-muted">كل الطلاب لديهم صلاحية الوصول للنظام حالياً.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // تفعيل / إلغاء تحديد الكل مع إضافة تأثير بصري للصفوف
    $('#selectAll').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.student-checkbox').prop('checked', isChecked);
        if(isChecked) {
            $('tbody tr').addClass('selected-row');
        } else {
            $('tbody tr').removeClass('selected-row');
        }
    });

    // إضافة تأثير عند تحديد صف واحد
    $('.student-checkbox').on('change', function() {
        if($(this).prop('checked')) {
            $(this).closest('tr').addClass('selected-row');
        } else {
            $(this).closest('tr').removeClass('selected-row');
            $('#selectAll').prop('checked', false);
        }
    });
});

function unblockSelected() {
    const checkedCount = $('.student-checkbox:checked').length;
    if (checkedCount === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'تنبيه',
            text: 'يرجى تحديد طالب واحد على الأقل',
            confirmButtonText: 'حسناً'
        });
        return;
    }
    
    if (confirm(`هل أنت متأكد من إزالة الحظر من ${checkedCount} طالب؟`)) {
        $('#unblockForm').submit();
    }
}
</script>
@endsection