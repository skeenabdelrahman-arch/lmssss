@extends('back_layouts.master')

@section('title') تعديل المذكرة @stop

@section('css')
{{-- نفس استايلات صفحة الإضافة --}}
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
    .form-switch .form-check-input { width: 3em; height: 1.5em; cursor: pointer; }
    .file-input-wrapper {
        background: #f8fafc; border: 2px dashed #e2e8f0;
        border-radius: 16px; padding: 20px;
    }
</style>
@endsection

@section('page-header')
    <div class="page-header-modern">
        <h4><i class="fas fa-edit me-2 text-primary"></i> تعديل المذكرة</h4>
    </div>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="modern-card shadow-sm border-0 bg-white rounded-4">
        <form action="{{route('pdf.update', $pdf->id)}}" method="POST" enctype="multipart/form-data" class="p-4">
            @csrf
            @method('PUT')
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle text-primary"></i> البيانات الأساسية
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">عنوان المذكرة</label>
                            <input class="form-control" type="text" name="title" value="{{$pdf->title}}" required />
                        </div>
                        <div class="col-12">
                            <label class="form-label">الوصف</label>
                            <textarea class="form-control" name="description" rows="3">{{$pdf->description}}</textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="form-section-title">
                        <i class="fas fa-layer-group text-primary"></i> التنظيم
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">السنة الدراسية</label>
                            <select class="form-select" name="grade" id="grade" required>
                                @foreach(signup_grades() as $grade)
                                    <option value="{{ $grade['value'] }}" {{ $pdf->grade == $grade['value'] ? 'selected' : '' }}>
                                        {{ $grade['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الشهر</label>
                            <select class="form-select" name="Month" id="Month" required>
                                <option value="{{$pdf->month_id}}">{{$pdf->month ? $pdf->month->name : 'شهر محذوف'}}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">الحالة</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" {{ $pdf->status == 1 ? 'checked' : '' }}>
                                <label class="form-check-label ms-2" for="statusSwitch">مفعل</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">محاضرة مرتبطة</label>
                            <select class="form-select" name="lecture_id" id="lecture_id">
                                <option value="">جاري التحميل...</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-section-title mt-4">
                        <i class="fas fa-file-upload text-primary"></i> الملف الحالي
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="file-input-wrapper">
                                <label class="form-label">تحديث الرابط الخارجي</label>
                                <input class="form-control" type="text" name="file_url" id="file_url" value="{{$pdf->file_url}}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="file-input-wrapper">
                                <label class="form-label">تغيير من المكتبة</label>
                                <x-media-picker name="media_file_url" type="documents" value="{{ $pdf->file_url }}" label="اختر ملف" accept="application/pdf" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-4 border-top d-flex gap-3">
                <button type="submit" class="btn btn-success px-5 py-2 rounded-3 fw-bold">تحديث</button>
                <a href="{{ route('pdf.index') }}" class="btn btn-light px-5 py-2 rounded-3 border">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        var selectedLectureId = "{{ $pdf->lecture_id }}";

        // دالة جلب المحاضرات (تستخدم عند تغيير الشهر وعند التحميل الأول)
        function getLectures(month_id, selectedId = null) {
            if (month_id) {
                $.ajax({
                    url: "{{ URL::to('lectures') }}/" + month_id,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        let $lectureSelect = $('select[name="lecture_id"]');
                        $lectureSelect.empty().append('<option value="">اختر المحاضرة (اختياري)...</option>');
                        $.each(data, function (key, value) {
                            var selected = (selectedId && selectedId == key) ? 'selected' : '';
                            $lectureSelect.append('<option value="' + key + '" ' + selected + '>' + value + '</option>');
                        });
                    },
                });
            }
        }

        // تحميل المحاضرات لأول مرة بناءً على الشهر الموجود
        if ($('select[name="Month"]').val()) {
            getLectures($('select[name="Month"]').val(), selectedLectureId);
        }

        // تغيير السنة
        $('select[name="grade"]').on('change', function () {
            var grade = $(this).val();
            if (grade) {
                $.ajax({
                    url: "{{ URL::to('monthes') }}/" + grade,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        let $monthSelect = $('select[name="Month"]');
                        $monthSelect.empty().append('<option value="">اختر الشهر...</option>');
                        $.each(data, function (key, value) {
                            $monthSelect.append('<option value="' + key + '">' + value + '</option>');
                        });
                        $('select[name="lecture_id"]').empty().append('<option value="">اختر الشهر أولاً</option>');
                    },
                });
            }
        });

        // تغيير الشهر
        $('select[name="Month"]').on('change', function () {
            getLectures($(this).val());
        });

        // التحقق قبل الإرسال
        $('form').on('submit', function (e) {
            var fileUrl = $('#file_url').val().trim();
            var mediaFileUrl = $('#media_file_url').val() ? $('#media_file_url').val().trim() : '';

            if (!fileUrl && mediaFileUrl) {
                $('#file_url').val(mediaFileUrl);
                fileUrl = mediaFileUrl;
            }

            if (!fileUrl) {
                e.preventDefault();
                alert('يرجى إدخال رابط الملف أو اختيار ملف من المكتبة');
                return false;
            }
        });
    });
</script>
@endsection