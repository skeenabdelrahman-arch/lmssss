@extends('back_layouts.master')

@section('title')
    معاينة إجابة الطالب: {{ $submission->student->name }}
@stop

@section('css')
<style>
    /* تنسيقات الصفحة العصرية */
    .preview-card {
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        background: #fff;
    }
    .file-card {
        border-radius: 15px;
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
        overflow: hidden;
    }
    .file-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 20px rgba(0,0,0,0.1);
    }
    .image-preview {
        height: 220px;
        object-fit: cover;
        width: 100%;
        cursor: pointer;
    }
    .student-badge-area {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 25px;
    }
    .notes-box {
        border-right: 5px solid #17a2b8;
        background: #eef9fb;
        border-radius: 10px;
        padding: 15px;
    }
</style>
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex align-items-center">
            <h4 class="content-title mb-0 my-auto text-primary"><i class="fas fa-eye me-2"></i> معاينة الإجابة</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ $submission->assignment->title }}</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card preview-card">
        <div class="card-header bg-transparent border-bottom p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                @php
                    $student = $submission->student;
                    $fullName = $student ? trim(collect([$student->first_name, $student->second_name, $student->third_name])->filter()->join(' ')) : 'طالب غير معروف';
                @endphp
                
                <div class="d-flex align-items-center">
                    <div class="avatar-md bg-primary-transparent text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-weight: bold; font-size: 20px;">
                        {{ mb_substr($fullName, 0, 1) }}
                    </div>
                    <div class="ms-3 mr-3">
                        <h5 class="mb-0 fw-bold">{{ $fullName }}</h5>
                        <small class="text-muted"><i class="fas fa-phone-alt me-1"></i> {{ $student->student_phone ?? $student->phone }}</small>
                    </div>
                </div>

                <div class="btn-list mt-2 mt-md-0">
                    <a href="{{ route('assignments.submissions.download', $submission->id) }}" class="btn btn-info-gradient rounded-pill px-4 shadow-sm">
                        <i class="fas fa-download me-1"></i> تحميل كافة الملفات
                    </a>
                    <button type="button" class="btn btn-success-gradient rounded-pill px-4 shadow-sm open-grade" data-toggle="modal" data-target="#gradeModal{{ $submission->id }}">
                        <i class="fas fa-check-double me-1"></i> {{ $submission->status == 'graded' ? 'تعديل الدرجة' : 'بدء التصحيح' }}
                    </button>
                    <a href="{{ route('assignments.submissions', $submission->assignment_id) }}" class="btn btn-light rounded-pill px-4">
                        <i class="fas fa-arrow-right me-1"></i> رجوع
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            @if($submission->notes)
                <div class="notes-box mb-5 shadow-sm">
                    <h6 class="fw-bold text-info mb-2"><i class="fas fa-comment-dots me-1"></i> ملاحظة من الطالب:</h6>
                    <p class="mb-0 text-dark">{{ $submission->notes }}</p>
                </div>
            @endif

            <h5 class="mb-4 fw-bold"><i class="fas fa-folder-open text-warning me-2"></i> الملفات المرفقة ({{ count($files) }})</h5>
            
            <div class="row">
                @foreach($files as $index => $file)
                    @php
                        $extension = pathinfo($file, PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        $fileUrl = route('storage.file', str_replace(DIRECTORY_SEPARATOR, '/', $file));
                        $fileExists = Storage::disk('public')->exists($file);
                    @endphp

                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card file-card h-100">
                            @if(!$fileExists)
                                <div class="d-flex align-items-center justify-content-center bg-light" style="height: 220px;">
                                    <div class="text-center p-3">
                                        <i class="fas fa-exclamation-circle fa-3x text-danger mb-2"></i>
                                        <p class="small text-muted mb-0">الملف مفقود</p>
                                    </div>
                                </div>
                            @elseif($isImage)
                                <a href="{{ $fileUrl }}" target="_blank">
                                    <img src="{{ $fileUrl }}" class="image-preview" alt="إجابة الطالب">
                                </a>
                            @else
                                <div class="d-flex align-items-center justify-content-center bg-light" style="height: 220px;">
                                    <div class="text-center">
                                        <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
                                        <p class="fw-bold mb-0">ملف مستند</p>
                                    </div>
                                </div>
                            @endif

                            <div class="card-footer bg-white border-top-0 text-center pb-3">
                                <small class="text-muted d-block mb-2 text-truncate">{{ basename($file) }}</small>
                                <div class="btn-group w-100">
                                    <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill-start">
                                        <i class="fas fa-external-link-alt"></i> عرض
                                    </a>
                                    <a href="{{ $fileUrl }}" download class="btn btn-sm btn-outline-secondary rounded-pill-end">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="gradeModal{{ $submission->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: 20px;">
            <div class="modal-header bg-gradient-primary text-white border-0 p-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-pen-nib me-2"></i> رصد درجة الطالب</h5>
                <button type="button" class="close text-white opacity-100" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{ route('assignments.submissions.grade', [$submission->assignment_id, $submission->id]) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-4">
                        <label class="fw-bold text-dark mb-2">الدرجة المستحقة</label>
                        <div class="input-group input-group-lg">
                            <input type="number" name="marks" class="form-control border-primary" 
                                   min="0" max="{{ $submission->assignment->total_marks }}" step="0.5" 
                                   value="{{ $submission->marks }}" required 
                                   style="border-radius: 12px 0 0 12px;">
                            <span class="input-group-text bg-primary text-white" style="border-radius: 0 12px 12px 0;">من {{ $submission->assignment->total_marks }}</span>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="fw-bold text-dark mb-2">تعليقك على الإجابة</label>
                        <textarea name="feedback" class="form-control" rows="4" 
                                  placeholder="اكتب ملاحظاتك للطالب هنا..." 
                                  style="border-radius: 12px; border: 1px solid #ddd;">{{ $submission->feedback }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light px-4 rounded-pill" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary px-5 rounded-pill shadow">حفظ واعتماد</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // تفعيل الـ Modal في حال لم يعمل تلقائياً مع بعض نسخ الـ Bootstrap
    $(document).ready(function() {
        $('.open-grade').on('click', function() {
            var target = $(this).data('target');
            $(target).modal('show');
        });
    });
</script>
@endpush