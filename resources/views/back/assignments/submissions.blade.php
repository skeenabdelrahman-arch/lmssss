@extends('back_layouts.master')

@section('title')
    إجابات الطلاب: {{ $assignment->title }}
@stop

@section('css')
<style>
    /* تنسيقات البطاقات الإحصائية */
    .stat-card {
        border: none;
        border-radius: 15px;
        transition: transform 0.3s;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .stat-card:hover { transform: translateY(-5px); }
    .bg-gradient-primary { background: linear-gradient(45deg, #7424a9, #a347e3); color: white; }
    .bg-gradient-info { background: linear-gradient(45deg, #17a2b8, #36d1dc); color: white; }
    .bg-gradient-success { background: linear-gradient(45deg, #28a745, #5dd479); color: white; }
    .bg-gradient-warning { background: linear-gradient(45deg, #ffc107, #ffdb6e); color: white; }
    
    /* تنسيقات الجدول والصفحة */
    .table-container { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .student-info { display: flex; align-items: center; }
    .student-avatar {
        width: 40px; height: 40px; border-radius: 10px; 
        background: #f3e5f5; display: flex; align-items: center; 
        justify-content: center; margin-left: 12px; font-weight: bold; color: #7424a9;
    }
    .badge-modern { padding: 8px 12px; border-radius: 8px; font-weight: 500; font-size: 11px; text-transform: uppercase; }
    
    /* تنسيق المودال */
    .modal-content { border-radius: 20px; border: none; overflow: hidden; }
    .modal-header { border-bottom: none; }
    .form-control-modern { border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px; }
    .form-control-modern:focus { border-color: #7424a9; box-shadow: 0 0 0 0.2rem rgba(116, 36, 169, 0.1); }
</style>
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex align-items-center">
            <h4 class="content-title mb-0 my-auto text-primary"><i class="fas fa-file-invoice me-2"></i> الواجبات</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ إجابات: {{ $assignment->title }}</span>
        </div>
    </div>
    <div class="d-flex my-xl-auto right-content">
        <a href="{{ route('assignments.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-right"></i> رجوع
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card bg-gradient-primary">
                <div class="card-body py-3">
                    <p class="mb-1 opacity-80">الدرجة الكلية</p>
                    <h3 class="mb-0 fw-bold">{{ $assignment->total_marks }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card bg-gradient-info">
                <div class="card-body py-3">
                    <p class="mb-1 opacity-80">عدد التسليمات</p>
                    <h3 class="mb-0 fw-bold">{{ $submissions->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card bg-gradient-success">
                <div class="card-body py-3">
                    <p class="mb-1 opacity-80">تم تصحيحه</p>
                    <h3 class="mb-0 fw-bold">{{ $submissions->where('status', 'graded')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card stat-card bg-gradient-warning text-dark">
                <div class="card-body py-3">
                    <p class="mb-1 opacity-80">متوسط الدرجات</p>
                    <h3 class="mb-0 fw-bold">{{ number_format($assignment->averageMarks() ?? 0, 1) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="table-container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif

        @if($submissions->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-folder-open fa-4x text-light mb-3"></i>
                <h5 class="text-muted">لا توجد إجابات مرسلة لهذا الواجب حتى الآن.</h5>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">الطالب</th>
                            <th class="border-0 text-center">التوقيت</th>
                            <th class="border-0 text-center">الحالة</th>
                            <th class="border-0 text-center">الدرجة</th>
                            <th class="border-0 text-center">الملفات</th>
                            <th class="border-0 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submissions as $submission)
                            @php
                                $student = $submission->student;
                                $fullName = $student ? trim(collect([$student->first_name, $student->second_name, $student->third_name])->filter()->join(' ')) : 'طالب مجهول';
                            @endphp
                            <tr>
                                <td>
                                    <div class="student-info">
                                        <div class="student-avatar">{{ mb_substr($fullName, 0, 1) }}</div>
                                        <div>
                                            <div class="fw-bold">{{ $fullName }}</div>
                                            <small class="text-muted">{{ $student->student_phone ?? $student->phone }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="d-block small fw-bold">{{ $submission->submitted_at->format('Y-m-d') }}</span>
                                    <span class="text-muted small">{{ $submission->submitted_at->format('h:i A') }}</span>
                                    @if($submission->isLate())
                                        <br><span class="badge bg-danger-transparent text-danger" style="font-size: 10px;">متأخر</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($submission->status == 'graded')
                                        <span class="badge badge-success-light badge-modern">تم التصحيح</span>
                                    @else
                                        <span class="badge badge-secondary-light badge-modern">قيد الانتظار</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($submission->status == 'graded')
                                        <span class="text-primary fw-bold">{{ $submission->marks }}</span> <small class="text-muted">/ {{ $assignment->total_marks }}</small>
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($submission->file_path)
                                        <div class="btn-group">
                                            <a href="{{ route('assignments.submissions.preview', $submission->id) }}" target="_blank" class="btn btn-sm btn-light text-primary" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('assignments.submissions.download', $submission->id) }}" class="btn btn-sm btn-light text-info" title="تحميل">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-muted small">لا يوجد</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-toggle="modal" data-target="#gradeModal{{ $submission->id }}">
                                        <i class="fas fa-edit me-1"></i> رصد الدرجة
                                    </button>
                                </td>
                            </tr>

                            <div class="modal fade" id="gradeModal{{ $submission->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content shadow-lg">
                                        <div class="modal-header bg-gradient-primary text-white">
                                            <h6 class="modal-title">تصحيح إجابة الطالب</h6>
                                            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <form action="{{ route('assignments.submissions.grade', [$assignment->id, $submission->id]) }}" method="POST">
                                            @csrf
                                            <div class="modal-body p-4">
                                                <div class="mb-3 p-2 bg-light rounded d-flex align-items-center">
                                                    <div class="student-avatar shadow-sm">{{ mb_substr($fullName, 0, 1) }}</div>
                                                    <div class="mr-2 ms-2">
                                                        <p class="mb-0 fw-bold">{{ $fullName }}</p>
                                                        <small class="text-muted">الدرجة الكلية للواجب: {{ $assignment->total_marks }}</small>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-3">
                                                    <label class="fw-bold mb-1">الدرجة المستحقة <span class="text-danger">*</span></label>
                                                    <input type="number" name="marks" class="form-control form-control-modern" min="0" max="{{ $assignment->total_marks }}" step="0.5" value="{{ $submission->marks }}" required>
                                                </div>

                                                <div class="form-group mb-0">
                                                    <label class="fw-bold mb-1">ملاحظات المعلم</label>
                                                    <textarea name="feedback" class="form-control form-control-modern" rows="3" placeholder="أحسنت، استمر في التقدم...">{{ $submission->feedback }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 p-3">
                                                <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">إلغاء</button>
                                                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">حفظ الدرجة</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

@section('js')
<script>
    $(function() {
        // أي كود إضافي للتحكم في المودال أو الجداول
        console.log('Submissions page ready.');
    });
</script>
@endsection