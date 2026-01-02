@extends('back_layouts.master')

@section('title')
قيود المحاضرات
@endsection

@section('content')
<div class="page-header">
    <h2><i class="fas fa-ban me-2"></i> قيود المحاضرات</h2>
    <a href="{{ route('admin.lecture_restrictions.create') }}" class="btn btn-modern-primary">
        <i class="fas fa-plus me-2"></i> إضافة قيد جديد
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
    </div>
@endif

<div class="row mb-3">
    <div class="col-md-12">
        <div class="modern-card">
            <h6 class="mb-3"><i class="fas fa-filter me-2"></i> حذف جماعي</h6>
            <div class="row">
                <div class="col-md-4">
                    <form action="{{ route('admin.lecture_restrictions.destroyAll') }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف جميع القيود؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash-alt me-2"></i> حذف جميع القيود
                        </button>
                    </form>
                </div>
                <div class="col-md-4">
                    <form action="{{ route('admin.lecture_restrictions.destroyByStudent', 0) }}" method="POST" id="deleteByStudentForm">
                        @csrf
                        @method('DELETE')
                        <div class="input-group">
                            <select class="form-select" name="student_id" required>
                                <option value="">اختر طالب</option>
                                @php
                                    $students = \App\Models\Student::whereHas('restrictions')->orderBy('first_name')->get();
                                @endphp
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->second_name }} {{ $student->third_name }} {{ $student->forth_name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-warning" onclick="return confirm('حذف جميع قيود هذا الطالب؟')">
                                <i class="fas fa-user-times"></i> حذف
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <form action="{{ route('admin.lecture_restrictions.destroyByLecture', 0) }}" method="POST" id="deleteByLectureForm">
                        @csrf
                        @method('DELETE')
                        <div class="input-group">
                            <select class="form-select" name="lecture_id" required>
                                <option value="">اختر محاضرة</option>
                                @php
                                    $lectures = \App\Models\Lecture::whereHas('restrictions')->with('month')->orderBy('title')->get();
                                @endphp
                                @foreach($lectures as $lecture)
                                    <option value="{{ $lecture->id }}">{{ $lecture->title }} - {{ $lecture->month->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-info" onclick="return confirm('حذف جميع قيود هذه المحاضرة؟')">
                                <i class="fas fa-video-slash"></i> حذف
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modern-card">
    <div class="card-header-custom">
        <h5><i class="fas fa-list me-2"></i> جميع القيود</h5>
        <span class="badge bg-primary">{{ $restrictions->total() }} قيد</span>
    </div>

    @if($restrictions->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الطالب</th>
                        <th>الكورس</th>
                        <th>المحاضرة</th>
                        <th>السبب</th>
                        <th>تاريخ الإضافة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($restrictions as $restriction)
                        <tr>
                            <td>{{ $restriction->id }}</td>
                            <td>
                                <strong>{{ $restriction->student->first_name }} {{ $restriction->student->second_name }} {{ $restriction->student->third_name }} {{ $restriction->student->forth_name }}</strong>
                                <br>
                                <small class="text-muted">{{ $restriction->student->phone }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $restriction->lecture->month->name }}</span>
                            </td>
                            <td>
                                <i class="fas fa-video me-1"></i>
                                {{ $restriction->lecture->title }}
                            </td>
                            <td>
                                @if($restriction->reason)
                                    <small>{{ $restriction->reason }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $restriction->created_at->format('Y-m-d') }}</small>
                                <br>
                                <small class="text-muted">{{ $restriction->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <form action="{{ route('admin.lecture_restrictions.destroy', $restriction->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من إزالة هذا القيد؟')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $restrictions->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <p class="text-muted">لا توجد قيود حالياً</p>
            <a href="{{ route('admin.lecture_restrictions.create') }}" class="btn btn-modern-primary">
                <i class="fas fa-plus me-2"></i> إضافة قيد جديد
            </a>
        </div>
    @endif
</div>
@endsection
