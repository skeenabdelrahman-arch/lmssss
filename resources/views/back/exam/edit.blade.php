@extends('back_layouts.master')

@section('title') تعديل امتحان: {{$exam_name->exam_title}} @stop

@section('css')
<style>
    .setting-card {
        border-radius: 15px; border: 1px solid #eef2f7; background: #fff;
        padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }
    .section-title {
        font-weight: 800; color: #1e293b; border-bottom: 2px solid #f1f5f9;
        padding-bottom: 10px; margin-bottom: 20px; display: flex; align-items: center;
    }
    .section-title i { color: #6366f1; margin-left: 10px; }
    .form-label { font-weight: 700; color: #475569; margin-bottom: 8px; }
    .form-control, .form-select { border-radius: 10px; padding: 12px; border: 1px solid #d1d5db; }
    
    /* ستايل السويتشات الحديثة */
    .custom-switch {
        display: flex; align-items: center; justify-content: space-between;
        padding: 15px; background: #f8fafc; border-radius: 12px; margin-bottom: 10px;
    }
    .form-check-input { width: 2.5em !important; height: 1.25em !important; cursor: pointer; }
</style>
@endsection

@section('page-header')
<div class="page-header-modern mb-4">
    <div class="d-flex align-items-center">
        <div class="bg-primary text-white rounded-3 p-3 me-3">
            <i class="fas fa-edit fa-lg"></i>
        </div>
        <div>
            <h4 class="mb-0 fw-bold">تعديل بيانات الامتحان</h4>
            <p class="text-muted small mb-0">يمكنك تغيير توقيت، حالة، أو جدولة الامتحان من هنا</p>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid pb-5">
    <form action="{{route('exam_name.update',$exam_name->id)}}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-7">
                <div class="setting-card shadow-sm">
                    <h5 class="section-title"><i class="fas fa-info-circle"></i> المعلومات الأساسية</h5>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">عنوان الامتحان</label>
                            <input type="text" name="exam_title" class="form-control fw-bold" value="{{$exam_name->exam_title}}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">وصف مختصر</label>
                            <input type="text" name="exam_description" class="form-control" value="{{$exam_name->exam_description}}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">مدة الامتحان (بالدقيقة)</label>
                            <select class="form-select" name="exam_time" required>
                                <option value="{{$exam_name->exam_time}}">{{$exam_name->exam_time}} دقيقة (الحالي)</option>
                                @foreach([5,10,15,20,30,45,60,90,120] as $time)
                                    <option value="{{$time}}">{{$time}} دقيقة</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">السنة الدراسية</label>
                            <select class="form-select" name="grade" required>
                                @foreach(signup_grades() as $grade)
                                    <option value="{{ $grade['value'] }}" {{ $exam_name->grade == $grade['value'] ? 'selected' : '' }}>
                                        {{ $grade['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">الشهر الدراسي</label>
                            <select class="form-select" name="Month" id="Month" required>
                                <option value="{{$exam_name->month_id}}">{{$exam_name->month ? $exam_name->month->name : 'اختر الشهر'}}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="setting-card shadow-sm">
                    <h5 class="section-title"><i class="fas fa-calendar-alt"></i> جدولة المواعيد</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">تاريخ الفتح</label>
                            <input type="datetime-local" class="form-control" name="opens_at" 
                                   value="{{ $exam_name->opens_at ? $exam_name->opens_at->format('Y-m-d\TH:i') : '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تاريخ الإغلاق</label>
                            <input type="datetime-local" class="form-control" name="closes_at" 
                                   value="{{ $exam_name->closes_at ? $exam_name->closes_at->format('Y-m-d\TH:i') : '' }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="setting-card shadow-sm">
                    <h5 class="section-title"><i class="fas fa-cog"></i> خيارات العرض والتحكم</h5>
                    
                    <div class="custom-switch">
                        <div>
                            <div class="fw-bold text-dark">حالة الامتحان</div>
                            <small class="text-muted">تفعيل أو تعطيل الامتحان حالياً</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" value="1" {{ $exam_name->status == 1 ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="custom-switch">
                        <div>
                            <div class="fw-bold text-dark">امتحان عام</div>
                            <small class="text-muted">متاح للجميع بدون تسجيل دخول</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="public_access" id="public_access" value="1" {{ $exam_name->public_access == 1 ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div id="hide_result_section" class="custom-switch" style="display: {{ $exam_name->public_access == 1 ? 'flex' : 'none' }}; background: #fff9db;">
                        <div>
                            <div class="fw-bold text-dark">إخفاء الدرجة فوراً</div>
                            <small class="text-muted">عدم إظهار النتيجة للطالب بعد الحل</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="hide_public_result" id="hide_public_result" value="1" {{ $exam_name->hide_public_result == 1 ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="custom-switch">
                        <div>
                            <div class="fw-bold text-dark">عشوائية الأسئلة</div>
                            <small class="text-muted">تغيير الترتيب لكل طالب لمنع الغش</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="randomize_questions" value="1" {{ $exam_name->randomize_questions == 1 ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="custom-switch">
                        <div>
                            <div class="fw-bold text-dark">إظهار النتائج آلياً</div>
                            <small class="text-muted">تظهر للكل فور إغلاق موعد الامتحان</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="auto_show_results" value="1" {{ $exam_name->auto_show_results == 1 ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg fw-bold rounded-pill">
                        <i class="fas fa-save me-2"></i> حفظ التغييرات
                    </button>
                    <a href="{{ route('exam_name.index') }}" class="btn btn-light btn-lg fw-bold rounded-pill border">
                        إلغاء
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        // إدارة ظهور خيار إخفاء النتيجة
        $('#public_access').on('change', function() {
            if ($(this).is(':checked')) {
                $('#hide_result_section').fadeIn().css('display', 'flex');
            } else {
                $('#hide_result_section').fadeOut();
                $('#hide_public_result').prop('checked', false);
            }
        });

        // جلب الشهور بناءً على السنة الدراسية
        $('select[name="grade"]').on('change', function () {
            var grade = $(this).val();
            if (grade) {
                $.ajax({
                    url: "{{ URL::to('monthes') }}/" + grade,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('select[name="Month"]').empty().append('<option value="">اختر الشهر</option>');
                        $.each(data, function (key, value) {
                            $('select[name="Month"]').append('<option value="' + key + '">' + value + '</option>');
                        });
                    },
                });
            }
        });
    });
</script>
@endsection