@extends('back_layouts.master')

@section('title') إضافة محاضرة @stop

@section('css')
<style>
    .modern-card { background: #fff; border-radius: 15px; padding: 25px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .lecture-item { 
        background: #fdfcfe; 
        border: 1px solid #eef2f7; 
        border-radius: 15px; 
        padding: 25px; 
        margin-bottom: 20px; 
        transition: 0.3s;
        position: relative;
    }
    .lecture-item:hover { border-color: #7424a9; box-shadow: 0 5px 15px rgba(116, 36, 169, 0.05); }
    .lecture-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f1f1; margin-bottom: 20px; padding-bottom: 10px; }
    .form-label { font-weight: 600; color: #444; font-size: 0.9rem; margin-bottom: 8px; }
    .form-control, .form-select { border-radius: 10px; padding: 12px; border: 1px solid #e2e8f0; }
    
    /* Toggle Switch Style */
    .form-check-input { width: 2.5em !important; height: 1.25em !important; cursor: pointer; }
    .form-check-input:checked { background-color: #7424a9; border-color: #7424a9; }

    .btn-add-more { background: #eef2ff; color: #4338ca; border: none; border-radius: 10px; padding: 12px 25px; font-weight: 700; transition: 0.3s; }
    .btn-add-more:hover { background: #4338ca; color: #fff; }
</style>
@endsection

@section('page-header')
<div class="page-header-modern mb-4">
    <h4><i class="fas fa-video me-2 text-primary"></i> إدارة المحاضرات</h4>
    <p class="text-muted small">يمكنك إضافة محاضرة واحدة أو أكثر وربطها بالصف والشهر</p>
</div>
@endsection

@section('content')
<div class="modern-card">
    <form action="{{ route('lecture.store') }}" method="POST" enctype="multipart/form-data" id="lectureForm">
        @csrf
        
        {{-- الحقول المشتركة في الأعلى لتسهيل الإدخال --}}
        <div class="row g-3 mb-4 bg-light p-3 rounded-3 border">
            <div class="col-md-6">
                <label class="form-label">السنة الدراسية:</label>
                <select class="form-select" name="grade" id="grade" required>
                    <option value="">اختار السنة الدراسية ...</option>
                    @foreach(signup_grades() as $grade)
                        <option value="{{ $grade['value'] }}">{{ $grade['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">الشهر الدراسي:</label>
                <select class="form-select" name="Month" id="Month" required>
                    <option value="">اختر السنة الدراسية أولاً</option>
                </select>
            </div>
        </div>

        <div id="lecturesContainer">
            <div class="lecture-item" data-index="0">
                <div class="lecture-header">
                    <h5 class="text-primary mb-0"><i class="fas fa-play-circle me-1"></i> محاضرة #<span class="lecture-number">1</span></h5>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-lecture" style="display: none; border-radius: 8px;">
                        <i class="fas fa-trash-alt me-1"></i> حذف المحاضرة
                    </button>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">عنوان المحاضرة:</label>
                        <input class="form-control" type="text" name="lectures[0][title]" placeholder="مثال: مقدمة في علم النحو" required/>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">وصف مختصر:</label>
                        <input class="form-control" type="text" name="lectures[0][description]" placeholder="شرح مبسط للمحتوى" required/>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-danger fw-bold"><i class="fab fa-youtube me-1"></i> لينك فيديو خارجي (Vimeo/YouTube):</label>
                        <input class="form-control border-danger-subtle" type="text" name="lectures[0][video_url]" placeholder="https://..."/>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-primary fw-bold"><i class="fas fa-server me-1"></i> رفع فيديو للسيرفر (اختياري):</label>
                        <input class="form-control border-primary-subtle" type="file" name="lectures[0][video_server]"/>
                    </div>
                    <div class="col-12">
                        <label class="form-label">صورة المحاضرة (Thumbnail):</label>
                        <div class="p-3 border rounded-3 bg-white">
                            <input class="form-control mb-2" type="file" name="lectures[0][image]" accept="image/*"/>
                            <x-media-picker name="lectures[0][image_url]" type="images" label="أو اختر من مكتبة الميديا" accept="image/*" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="lectures[0][status]" id="status0" checked>
                            <label class="form-check-label ms-2 fw-bold" for="status0">تفعيل المحاضرة فوراً</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="lectures[0][is_featured]" id="feat0">
                            <label class="form-check-label ms-2 fw-bold text-warning" for="feat0"><i class="fas fa-star me-1"></i> تمييز في الصفحة الرئيسية</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-4">
            <button type="button" class="btn-add-more" id="addLectureBtn">
                <i class="fas fa-plus-circle me-2"></i> إضافة محاضرة أخرى
            </button>
            
            <div class="actions">
                <button type="submit" class="btn btn-modern btn-modern-success px-5 shadow-sm">
                    <i class="fas fa-save me-2"></i> حفظ الكل
                </button>
                <a href="{{ route('lecture.index') }}" class="btn btn-modern btn-modern-secondary px-4 ms-2">إلغاء</a>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
    let lectureIndex = 1;
    $(document).ready(function () {
        // Ajax لربط الصفوف بالشهور
        $('select[name="grade"]').on('change', function () {
            var grade = $(this).val();
            if (grade) {
                $.ajax({
                    url: "{{ URL::to('monthes') }}/" + grade,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('select[name="Month"]').empty().append('<option value="">اختر الشهر...</option>');
                        $.each(data, function (key, value) {
                            $('select[name="Month"]').append('<option value="' + key + '">' + value + '</option>');
                        });
                    },
                });
            }
        });

        $('#addLectureBtn').on('click', function() {
            let html = `
                <div class="lecture-item shadow-sm" data-index="${lectureIndex}" style="display:none">
                    <div class="lecture-header">
                        <h5 class="text-primary mb-0"><i class="fas fa-play-circle me-1"></i> محاضرة #<span class="lecture-number">${lectureIndex + 1}</span></h5>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-lecture" style="border-radius: 8px;">
                            <i class="fas fa-trash-alt me-1"></i> حذف
                        </button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">العنوان:</label><input class="form-control" type="text" name="lectures[${lectureIndex}][title]" required/></div>
                        <div class="col-md-6"><label class="form-label">الوصف:</label><input class="form-control" type="text" name="lectures[${lectureIndex}][description]" required/></div>
                        <div class="col-md-6"><label class="form-label text-danger">لينك فيديو خارجي:</label><input class="form-control border-danger-subtle" type="text" name="lectures[${lectureIndex}][video_url]"/></div>
                        <div class="col-md-6"><label class="form-label text-primary">رفع فيديو:</label><input class="form-control border-primary-subtle" type="file" name="lectures[${lectureIndex}][video_server]"/></div>
                        <div class="col-12"><label class="form-label">الصورة:</label><input class="form-control" type="file" name="lectures[${lectureIndex}][image]" accept="image/*"/></div>
                        <div class="col-md-6"><div class="form-check form-switch mt-3"><input class="form-check-input" type="checkbox" name="lectures[${lectureIndex}][status]" checked><label class="form-check-label ms-2 fw-bold">تفعيل</label></div></div>
                    </div>
                </div>`;
            $('#lecturesContainer').append(html);
            $(`.lecture-item[data-index="${lectureIndex}"]`).slideDown();
            lectureIndex++;
            updateLectureNumbers();
        });

        $(document).on('click', '.remove-lecture', function() {
            $(this).closest('.lecture-item').slideUp(function() { $(this).remove(); updateLectureNumbers(); });
        });

        function updateLectureNumbers() {
            $('.lecture-item').each(function(i) {
                $(this).find('.lecture-number').text(i + 1);
                $('.remove-lecture').toggle($('.lecture-item').length > 1);
            });
        }
    });
</script>
@endsection