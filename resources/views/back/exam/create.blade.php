@extends('back_layouts.master')

@section('title') إضافة امتحان جديد @stop

@section('css')
<style>
    .form-section-title {
        font-size: 1.1rem; font-weight: 700; color: #1e293b;
        margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #f1f5f9;
        display: flex; align-items: center; gap: 10px;
    }
    .form-label { font-weight: 600; color: #475569; font-size: 0.9rem; }
    .form-control, .form-select {
        border-radius: 12px; padding: 12px 15px; border: 1px solid #e2e8f0; transition: 0.3s;
    }
    .form-control:focus { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }
    
    /* ستايل مخصص للسويتش (التوجل) */
    .custom-switch-card {
        background: #f8fafc; padding: 15px; border-radius: 12px;
        border: 1px solid #e2e8f0; height: 100%; transition: 0.3s;
    }
    .custom-switch-card:hover { border-color: #6366f1; background: #fff; }
    .form-check-input { width: 2.5em; height: 1.25em; cursor: pointer; }
</style>
@endsection

@section('page-header')
<div class="page-header-modern">
    <h4><i class="fas fa-plus-circle me-2 text-primary"></i> إنشاء امتحان جديد</h4>
</div>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="modern-card shadow-sm border-0 bg-white rounded-4 p-4">
        
        @if(session()->has('error'))
            <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-4">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('exam_name.store') }}" method="POST">
            @csrf
            
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle text-primary"></i> البيانات الأساسية للمذكرة
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">عنوان الامتحان</label>
                            <input class="form-control" type="text" name="exam_title" placeholder="مثلاً: امتحان شامل على الباب الأول" required />
                        </div>
                        <div class="col-12">
                            <label class="form-label">وصف الامتحان (اختياري)</label>
                            <textarea class="form-control" name="exam_description" rows="2" placeholder="اكتب تعليمات للطالب قبل البدء..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">السنة الدراسية</label>
                            <select class="form-select" name="grade" id="grade" required>
                                <option value="">اختر الصف...</option>
                                @foreach(signup_grades() as $grade)
                                    <option value="{{ $grade['value'] }}">{{ $grade['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الشهر</label>
                            <select class="form-select" name="Month" id="Month" required>
                                <option value="">اختر السنة أولاً</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">مدة الامتحان (بالدقائق)</label>
                            <select class="form-select" name="exam_time" required>
                                <option value="">حدد وقت الامتحان...</option>
                                @foreach([5,10,15,20,30,45,60,90,120] as $time)
                                    <option value="{{ $time }}">{{ $time }} دقيقة</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="form-section-title">
                        <i class="fas fa-cog text-primary"></i> إعدادات التحكم والجدولة
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">وقت الفتح</label>
                            <input type="datetime-local" class="form-control" name="opens_at">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">وقت الإغلاق</label>
                            <input type="datetime-local" class="form-control" name="closes_at">
                        </div>

                        <div class="col-12">
                            <div class="custom-switch-card">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" id="status" checked>
                                    <label class="form-check-label ms-2 fw-bold" for="status">تفعيل الامتحان الآن</label>
                                </div>
                                <small class="text-muted d-block mt-1">إذا تم التعطيل، لن يظهر الامتحان للطلاب حتى لو بدأ وقته.</small>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="custom-switch-card">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="randomize_questions" id="randomize">
                                    <label class="form-check-label ms-2 fw-bold" for="randomize">ترتيب عشوائي للأسئلة</label>
                                </div>
                                <small class="text-muted d-block mt-1">تغيير ترتيب الأسئلة لكل طالب لمنع الغش.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-section-title mt-4">
                        <i class="fas fa-users-cog text-primary"></i> الوصول والنتائج
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="custom-switch-card">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="public_access" id="public_access">
                                    <label class="form-check-label ms-2 fw-bold" for="public_access">امتحان عام (Link)</label>
                                </div>
                                <small class="text-muted d-block mt-1">متاح لأي شخص يملك الرابط بدون حساب.</small>
                            </div>
                        </div>

                        <div class="col-md-4" id="hide_result_section" style="display: none;">
                            <div class="custom-switch-card border-warning">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="hide_public_result" id="hide_public_result">
                                    <label class="form-check-label ms-2 fw-bold" for="hide_public_result">إخفاء الدرجة فوراً</label>
                                </div>
                                <small class="text-muted d-block mt-1">لن يرى الطالب نتيجته إلا عند تفعيلها يدوياً.</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="custom-switch-card">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="auto_show_results" id="auto_show">
                                    <label class="form-check-label ms-2 fw-bold" for="auto_show">إظهار تلقائي بعد الإغلاق</label>
                                </div>
                                <small class="text-muted d-block mt-1">تظهر النتائج للجميع فور انتهاء وقت الإغلاق.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-4 border-top d-flex gap-3">
                <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 fw-bold shadow-sm">
                    <i class="fas fa-save me-2"></i> حفظ الامتحان والبدء في إضافة الأسئلة
                </button>
                <a href="{{ route('exam_name.index') }}" class="btn btn-light px-5 py-2 rounded-3 border">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        // إدارة ظهور خيار إخفاء النتائج
        $('#public_access').on('change', function() {
            if ($(this).is(':checked')) {
                $('#hide_result_section').fadeIn();
            } else {
                $('#hide_result_section').fadeOut();
                $('#hide_public_result').prop('checked', false);
            }
        });

        // جلب الشهور بناءً على السنة الدراسية
        $('select[name="grade"]').on('change', function () {
            var grade = $(this).val();
            var $monthSelect = $('select[name="Month"]');
            
            if (grade) {
                $monthSelect.html('<option value="">جاري التحميل...</option>');
                $.ajax({
                    url: "{{ URL::to('monthes') }}/" + grade,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $monthSelect.empty().append('<option value="">اختر الشهر...</option>');
                        $.each(data, function (key, value) {
                            $monthSelect.append('<option value="' + key + '">' + value + '</option>');
                        });
                    },
                });
            } else {
                $monthSelect.empty().append('<option value="">اختر السنة الدراسية أولاً</option>');
            }
        });
    });
</script>
@endsection