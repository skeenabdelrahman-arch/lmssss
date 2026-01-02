@extends('back_layouts.master')

@section('title') تعديل محاضرة | {{$lecture->title}} @stop

@section('css')
<style>
    :root { --primary-color: #7424a9; --secondary-bg: #f8fafc; }
    
    .main-content-card { background: transparent; border: none; }
    .card-modern { 
        background: #fff; border-radius: 16px; border: 1px solid #edf2f7; 
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px; overflow: hidden;
    }
    .card-header-custom { 
        padding: 20px 25px; border-bottom: 1px solid #f1f5f9; background: #fff;
        display: flex; align-items: center; gap: 12px;
    }
    .card-header-custom h5 { margin: 0; font-weight: 700; color: #1e293b; font-size: 1.1rem; }
    .card-body-custom { padding: 25px; }

    /* تحسين الحقول */
    .input-group-modern { position: relative; }
    .input-group-modern i { 
        position: absolute; right: 15px; top: 50%; transform: translateY(-50%); 
        color: #94a3b8; z-index: 5; 
    }
    .form-control-modern { 
        padding-right: 45px !important; border-radius: 12px !important; border: 1px solid #e2e8f0;
        background: #fdfdfd; transition: all 0.2s;
    }
    .form-control-modern:focus { 
        border-color: var(--primary-color); background: #fff;
        box-shadow: 0 0 0 4px rgba(116, 36, 169, 0.1); 
    }

    /* ميديا وقسم الفيديو */
    .video-preview-wrapper { 
        background: #0f172a; border-radius: 12px; overflow: hidden; 
        aspect-ratio: 16/9; position: relative; border: 4px solid #f1f5f9;
    }
    .img-preview-wrapper {
        width: 150px; height: 100px; border-radius: 10px; object-fit: cover;
        border: 2px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* التبديل والحالات */
    .status-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 16px; border-radius: 12px; background: #f8fafc; margin-bottom: 10px;
    }
    .btn-submit-modern {
        background: var(--primary-color); color: white; padding: 14px 28px;
        border-radius: 12px; border: none; font-weight: 700; width: 100%;
        transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-submit-modern:hover { opacity: 0.9; transform: translateY(-2px); }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">تعديل المحاضرة</h3>
            <p class="text-muted small">تحديث محتوى الفيديو، الصور، والإعدادات الخاصة بالدرس</p>
        </div>
        <a href="{{ route('lecture.index') }}" class="btn btn-outline-secondary px-4 border-0 fw-bold">
            <i class="fas fa-arrow-left me-2"></i> العودة للقائمة
        </a>
    </div>

    <form action="{{route('lecture.update',$lecture->id)}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- العمود الأساسي --}}
            <div class="col-lg-8">
                <div class="card-modern">
                    <div class="card-header-custom">
                        <i class="fas fa-file-alt text-primary"></i>
                        <h5>البيانات الأساسية</h5>
                    </div>
                    <div class="card-body-custom">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label fw-bold">عنوان المحاضرة</label>
                                <div class="input-group-modern">
                                    <i class="fas fa-heading"></i>
                                    <input type="text" name="title" class="form-control form-control-modern" value="{{$lecture->title}}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">وصف المحاضرة</label>
                                <textarea name="description" class="form-control" rows="3" style="border-radius:12px; background:#fdfdfd;" required>{{$lecture->description}}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-modern">
                    <div class="card-header-custom">
                        <i class="fas fa-video text-danger"></i>
                        <h5>محتوى الفيديو والميديا</h5>
                    </div>
                    <div class="card-body-custom">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-danger">رابط YouTube / Vimeo</label>
                                <div class="input-group-modern">
                                    <i class="fab fa-youtube"></i>
                                    <input type="text" name="video_url" class="form-control form-control-modern" value="{{$lecture->video_url}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-primary">ملف فيديو (رفع للسيرفر)</label>
                                <input type="file" name="video_server" class="form-control" style="border-radius:12px;">
                            </div>
                            
                            @if($lecture->video_server)
                            <div class="col-12">
                                <p class="small text-muted mb-2 fw-bold">مشغل الفيديو الحالي:</p>
                                <div class="video-preview-wrapper">
                                    <video src="{{url('upload_files/'.$lecture->video_server)}}" controls style="width:100%; height:100%;"></video>
                                </div>
                            </div>
                            @endif

                            <div class="col-12 border-top pt-4 mt-4">
                                <label class="form-label fw-bold">صورة الغلاف (Thumbnail)</label>
                                <div class="d-flex align-items-center gap-4">
                                    @if($lecture->image)
                                        <img src="{{url('upload_files/'.$lecture->image)}}" class="img-preview-wrapper">
                                    @endif
                                    <div class="flex-grow-1">
                                        <input type="file" name="image" class="form-control mb-2" style="border-radius:12px;">
                                        <x-media-picker name="image_url" type="images" value="{{ $lecture->image ? url('upload_files/'.$lecture->image) : '' }}" label="أو اختر من المكتبة" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- العمود الجانبي --}}
            <div class="col-lg-4">
                <div class="card-modern shadow-sm">
                    <div class="card-header-custom bg-light">
                        <i class="fas fa-tasks text-success"></i>
                        <h5>الإعدادات والنشر</h5>
                    </div>
                    <div class="card-body-custom">
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-muted">الصف الدراسي</label>
                            <select name="grade" id="grade" class="form-select form-control-modern px-3" style="padding-right:15px !important;" required>
                                @foreach(signup_grades() as $grade)
                                    <option value="{{ $grade['value'] }}" {{ $lecture->grade == $grade['value'] ? 'selected' : '' }}>
                                        {{ $grade['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-muted">الشهر الدراسي</label>
                            <select name="Month" id="Month" class="form-select form-control-modern px-3" style="padding-right:15px !important;" required>
                                <option value="{{$lecture->month_id}}">{{$lecture->month ? $lecture->month->name : 'اختر الشهر'}}</option>
                            </select>
                        </div>

                        <hr>

                        <div class="status-item">
                            <div>
                                <span class="d-block fw-bold">الحالة العامة</span>
                                <small class="text-muted small">ظهور المحاضرة للطلاب</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" style="width: 40px; height: 20px;" {{ $lecture->status == 1 ? 'checked' : '' }}>
                            </div>
                        </div>

                        <div class="status-item">
                            <div>
                                <span class="d-block fw-bold text-warning">محاضرة مميزة</span>
                                <small class="text-muted small">تثبيت في الرئيسية</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_featured" style="width: 40px; height: 20px;" {{ $lecture->is_featured == 1 ? 'checked' : '' }}>
                            </div>
                        </div>

                        <div class="mt-4 pt-2">
                            <button type="submit" class="btn-submit-modern">
                                <i class="fas fa-save"></i> حفظ التغييرات الآن
                            </button>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info border-0 shadow-sm" style="border-radius:16px;">
                    <div class="d-flex gap-3">
                        <i class="fas fa-lightbulb mt-1"></i>
                        <div class="small">
                            <strong>نصيحة:</strong> عند رفع فيديو على السيرفر، يفضل ألا يتجاوز الحجم 500 ميجا لضمان سرعة التحميل للطلاب.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        // تحديث قائمة الشهور AJAX بشكل سلس
        $('select[name="grade"]').on('change', function () {
            var grade = $(this).val();
            var $monthSelect = $('select[name="Month"]');
            if (grade) {
                $monthSelect.addClass('opacity-50'); // تأثير بصري أثناء التحميل
                $.ajax({
                    url: "{{ URL::to('monthes') }}/" + grade,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $monthSelect.empty().removeClass('opacity-50');
                        $.each(data, function (key, value) {
                            $monthSelect.append('<option value="' + key + '">' + value + '</option>');
                        });
                    },
                });
            }
        });
    });
</script>
@endsection