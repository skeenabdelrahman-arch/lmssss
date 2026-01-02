@extends('back_layouts.master')

@section('title')
    تعديل اشتراك: {{ $subscription->student->name ?? 'طالب' }}
@stop

@section('css')
<style>
    .edit-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        border: none;
    }
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
    }
    .form-control, .form-select {
        border-radius: 12px;
        padding: 12px 15px;
        border: 1px solid #dee2e6;
        transition: all 0.3s;
    }
    .form-control:focus {
        border-color: #7424a9;
        box-shadow: 0 0 0 0.2rem rgba(116, 36, 169, 0.1);
    }
    .status-box {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 12px;
        border: 1px dashed #ced4da;
    }
</style>
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex align-items-center">
            <h4 class="content-title mb-0 my-auto text-primary"><i class="fas fa-edit me-2"></i> تعديل الاشتراك</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ إدارة اشتراكات الطلاب</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm rounded-15">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                </div>
            @endif

            <div class="edit-card border-top border-primary border-4">
                <form action="{{ route('student_subscription.update', $subscription->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">كود الطالب <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-id-badge text-primary"></i></span>
                                <input type="text" class="form-control" name="student_id" required value="{{$subscription->student_id}}" placeholder="أدخل كود الطالب">
                            </div>
                            <small class="text-muted">اسم الطالب الحالي: <strong>{{ $subscription->student->name ?? 'غير متوفر' }}</strong></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">المرحلة الدراسية <span class="text-danger">*</span></label>
                            <select class="form-control form-select" name="grade" id="gradeSelectEdit" required>
                                @foreach(signup_grades() as $grade)
                                    <option value="{{ $grade['value'] }}" {{ $subscription->grade == $grade['value'] ? 'selected' : '' }}>
                                        {{ $grade['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">الشهر <span class="text-danger">*</span></label>
                            <select class="form-control form-select" name="month_id" id="monthSelectEdit" required>
                                @if($subscription->month)
                                    <option value="{{$subscription->month_id}}">{{$subscription->month->name}}</option>
                                @else
                                    <option value="">-- اختر الشهر --</option>
                                @endif
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">حالة الاشتراك</label>
                            <div class="status-box d-flex align-items-center justify-content-between">
                                <span class="fw-bold"><i class="fas fa-toggle-on me-2 text-success"></i> تفعيل الوصول للمحتوى</span>
                                <div class="form-check form-switch m-0">
                                    <input type="checkbox" class="form-check-input" name="is_active" value="1" 
                                           {{ $subscription->is_active == 1 ? 'checked' : '' }} 
                                           style="width: 50px; height: 25px; cursor: pointer;">
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-5 border-top pt-4">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow">
                                    <i class="fas fa-save me-2"></i> حفظ التغييرات
                                </button>
                                <a href="{{ route('student_subscription.index') }}" class="btn btn-light btn-lg rounded-pill px-4 text-muted">
                                    <i class="fas fa-times me-2"></i> إلغاء
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // كود لتحديث الشهور عند تغيير المرحلة الدراسية في صفحة التعديل
    $('#gradeSelectEdit').on('change', function() {
        const grade = $(this).val();
        const monthSelect = $('#monthSelectEdit');
        
        if (grade) {
            monthSelect.prop('disabled', true).html('<option>جاري التحميل...</option>');
            $.get("{{ route('subscription.months', '') }}/" + grade, function(data) {
                monthSelect.prop('disabled', false).empty();
                monthSelect.append('<option value="">-- اختر الشهر الجديد --</option>');
                $.each(data, function(key, value) {
                    monthSelect.append(`<option value="${key}">${value}</option>`);
                });
            });
        }
    });
});
</script>
@endsection