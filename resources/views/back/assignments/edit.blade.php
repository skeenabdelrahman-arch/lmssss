@extends('back_layouts.master')

@section('title')
    تعديل واجب: {{ $assignment->title }}
@stop

@section('css')
<style>
    .modern-card { background: #fff; border-radius: 15px; padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: none; }
    .page-header-modern { margin-bottom: 25px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0; }
    .form-label { font-weight: 600; color: #495057; margin-bottom: 8px; }
    .form-control:focus { border-color: #7424a9; box-shadow: 0 0 0 0.2rem rgba(116, 36, 169, 0.25); }
    .btn-modern { border-radius: 10px; padding: 10px 25px; font-weight: 600; transition: all 0.3s; }
    .btn-modern-success { background-color: #7424a9; border-color: #7424a9; color: white; }
    .btn-modern-success:hover { background-color: #5a1c84; transform: translateY(-2px); color: white; }
    .btn-modern-secondary { background-color: #e9ecef; color: #495057; border: none; }
    .btn-modern-secondary:hover { background-color: #dee2e6; }
    .current-file-box { background: #f8f9fa; border-radius: 10px; padding: 12px; border: 1px dashed #ced4da; margin-bottom: 10px; }
</style>
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex align-items-center">
            <h4 class="content-title mb-0 my-auto text-primary"><i class="fas fa-edit me-2"></i> الواجبات</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تعديل واجب</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    {{-- تنبيهات النظام --}}
    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session()->get('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session()->get('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="modern-card">
        <form action="{{ route('assignments.update', $assignment->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                {{-- عنوان الواجب --}}
                <div class="col-md-12 mb-3">
                    <label for="title" class="form-label">عنوان الواجب <span class="text-danger">*</span></label>
                    <input class="form-control @error('title') is-invalid @enderror" type="text" name="title" id="title" value="{{ old('title', $assignment->title) }}" required />
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- الوصف --}}
                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">الوصف والتعليمات</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description" rows="3">{{ old('description', $assignment->description) }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- السنة الدراسية --}}
                <div class="col-md-6 mb-3">
                    <label for="grade" class="form-label">السنة الدراسية <span class="text-danger">*</span></label>
                    <select class="form-control select2 @error('grade') is-invalid @enderror" name="grade" id="grade" required>
                        <option value="">اختر السنة الدراسية...</option>
                        @foreach(signup_grades() as $grade)
                            <option value="{{ $grade['value'] }}" {{ old('grade', ($assignment->month->grade ?? '')) == $grade['value'] ? 'selected' : '' }}>
                                {{ $grade['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- الشهر --}}
                <div class="col-md-6 mb-3">
                    <label for="month_id" class="form-label">الشهر <span class="text-danger">*</span></label>
                    <select class="form-control @error('month_id') is-invalid @enderror" name="month_id" id="month_id" required>
                        <option value="{{ $assignment->month_id }}">{{ $assignment->month->name ?? 'الشهر الحالي' }}</option>
                    </select>
                </div>

                {{-- المحاضرة --}}
                <div class="col-md-12 mb-3">
                    <label for="lecture_id" class="form-label">المحاضرة (اختياري)</label>
                    <select class="form-control @error('lecture_id') is-invalid @enderror" name="lecture_id" id="lecture_id">
                        <option value="">عام للشهر (بدون محاضرة محددة)</option>
                        @if($assignment->lecture_id)
                            <option value="{{ $assignment->lecture_id }}" selected>{{ $assignment->lecture->name ?? 'المحاضرة الحالية' }}</option>
                        @endif
                    </select>
                </div>

                {{-- الدرجة والموعد --}}
                <div class="col-md-6 mb-3">
                    <label for="total_marks" class="form-label">الدرجة الكلية <span class="text-danger">*</span></label>
                    <input class="form-control" type="number" name="total_marks" value="{{ old('total_marks', $assignment->total_marks) }}" min="1" required />
                </div>

                <div class="col-md-6 mb-3">
                    <label for="deadline" class="form-label">الموعد النهائي</label>
                    <input class="form-control" type="datetime-local" name="deadline" id="deadline" value="{{ old('deadline', $assignment->deadline ? $assignment->deadline->format('Y-m-d\TH:i') : '') }}" />
                </div>

                {{-- إدارة الملف --}}
                <div class="col-md-12 mb-3">
                    <label class="form-label">ملف الواجب</label>
                    @if($assignment->file_path)
                        <div class="current-file-box d-flex align-items-center justify-content-between">
                            <span><i class="fas fa-file-pdf text-danger me-2"></i> ملف مرفق حالياً</span>
                            <a href="{{ Storage::url($assignment->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">عرض الملف</a>
                        </div>
                    @endif
                    <input class="form-control @error('file_path') is-invalid @enderror" type="file" name="file_path" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip" />
                    <small class="text-muted">ارفع ملفاً جديداً لاستبدال الحالي أو اتركه فارغاً للإبقاء عليه.</small>
                </div>

                {{-- الحالة والترتيب --}}
                <div class="col-md-6 mb-3">
                    <label for="display_order" class="form-label">ترتيب الظهور</label>
                    <input class="form-control" type="number" name="display_order" value="{{ old('display_order', $assignment->display_order) }}" min="0" />
                </div>

                <div class="col-md-6 mb-3 d-flex align-items-center">
                    <div class="form-check form-switch pt-4">
                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', $assignment->status == 'active') ? 'checked' : '' }} style="width: 45px; height: 22px;">
                        <label class="form-check-label ms-2 fw-bold" for="status">تفعيل الواجب للطلاب</label>
                    </div>
                </div>

                {{-- أزرار التحكم --}}
                <div class="col-12 mt-4 border-top pt-4 text-center">
                    <button type="submit" class="btn btn-modern btn-modern-success shadow-sm">
                        <i class="fas fa-save me-2"></i> حفظ التعديلات
                    </button>
                    <a href="{{ route('assignments.index') }}" class="btn btn-modern btn-modern-secondary ms-2">
                        إلغاء
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // منطق جلب الأشهر بناءً على السنة الدراسية
    $('#grade').on('change', function() {
        var grade = $(this).val();
        var monthSelect = $('#month_id');
        var lectureSelect = $('#lecture_id');

        monthSelect.html('<option>جاري التحميل...</option>');
        
        if(grade) {
            $.get('/monthes/' + grade, function(data) {
                monthSelect.empty().append('<option value="">اختر الشهر...</option>');
                $.each(data, function(key, value) {
                    monthSelect.append('<option value="'+ value.id +'">'+ value.name +'</option>');
                });
                lectureSelect.empty().append('<option value="">اختر الشهر أولاً</option>');
            });
        }
    });

    // منطق جلب المحاضرات بناءً على الشهر
    $('#month_id').on('change', function() {
        var monthId = $(this).val();
        var lectureSelect = $('#lecture_id');

        lectureSelect.html('<option>جاري التحميل...</option>');
        
        if(monthId) {
            $.get('/admin/lecture-restrictions/api/lectures/' + monthId, function(data) {
                lectureSelect.empty().append('<option value="">عام للشهر (بدون محاضرة محددة)</option>');
                $.each(data, function(key, value) {
                    lectureSelect.append('<option value="'+ value.id +'">'+ value.name +'</option>');
                });
            });
        }
    });
});
</script>
@endsection