@extends('back_layouts.master')

@section('title') إضافة مذكرة جديدة @stop

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
    .form-switch .form-check-input { width: 3em; height: 1.5em; cursor: pointer; }
    .file-input-wrapper {
        background: #f8fafc; border: 2px dashed #e2e8f0;
        border-radius: 16px; padding: 20px; transition: 0.3s;
    }
</style>
@endsection

@section('page-header')
    <div class="page-header-modern">
        <h4><i class="fas fa-plus-circle me-2 text-primary"></i> إضافة مذكرة جديدة</h4>
    </div>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="modern-card shadow-sm border-0 bg-white rounded-4">
        <form action="{{ route('pdf.store') }}" method="POST" enctype="multipart/form-data" class="p-4">
            @csrf
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle text-primary"></i> البيانات الأساسية
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">عنوان المذكرة</label>
                            <input class="form-control" type="text" name="title" placeholder="مثلاً: ملخص الباب الأول" required />
                        </div>
                        <div class="col-12">
                            <label class="form-label">وصف مختصر</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="أضف تفاصيل بسيطة حول المحتوى..."></textarea>
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
                                <option value="">اختر الصف الدراسي...</option>
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
                        <div class="col-md-6">
                            <label class="form-label">الحالة</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" checked>
                                <label class="form-check-label ms-2" for="statusSwitch">مفعل للطلاب</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">ربط بمحاضرة (اختياري)</label>
                            <select class="form-select" name="lecture_id" id="lecture_id">
                                <option value="">اختر الشهر أولاً</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-section-title mt-4">
                        <i class="fas fa-file-upload text-primary"></i> ملف المذكرة
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="file-input-wrapper">
                                <label class="form-label"><i class="fas fa-link me-1"></i> رابط خارجي مباشر</label>
                                <input class="form-control mb-2" type="text" name="file_url" id="file_url" placeholder="Direct link, Google Drive, etc." />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="file-input-wrapper">
                                <label class="form-label"><i class="fas fa-folder-open me-1"></i> المكتبة</label>
                                <x-media-picker name="media_file_url" type="documents" label="فتح المكتبة" accept="application/pdf" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-4 border-top d-flex gap-3">
                <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 fw-bold shadow-sm">إضافة</button>
                <a href="{{ route('pdf.index') }}" class="btn btn-light px-5 py-2 rounded-3 border">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        // تغيير السنة الدراسية لجلب الشهور
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

        // تغيير الشهر لجلب المحاضرات
        $('select[name="Month"]').on('change', function () {
            var month_id = $(this).val();
            if (month_id) {
                $.ajax({
                    url: "{{ URL::to('lectures') }}/" + month_id,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        let $lectureSelect = $('select[name="lecture_id"]');
                        $lectureSelect.empty().append('<option value="">اختر المحاضرة (اختياري)...</option>');
                        $.each(data, function (key, value) {
                            $lectureSelect.append('<option value="' + key + '">' + value + '</option>');
                        });
                    },
                });
            }
        });

        // التحقق قبل الحفظ
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