@extends('back_layouts.master')

@section('title', 'اختيار الكورسات للشهور')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1>ربط الشهور بالكورسات</h1>
                <a href="{{ route('parent-portal.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> العودة
                </a>
            </div>
        </div>
    </div>

    <!-- رسائل التنبيه -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('errors') && count(session('errors')) > 0)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>تحذيرات:</strong>
            <ul class="mb-0 mt-2">
                @foreach(session('errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            
            @if(session('has_failed_rows') && session()->has('payment_failed_rows'))
                <div class="mt-3">
                    <strong>يمكنك تحميل ملف يحتوي على الصفوف الفاشلة لمراجعتها وإصلاحها:</strong>
                    <div class="mt-2">
                        <a href="{{ route('parent-portal.export-failed-payments') }}" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-download"></i> تحميل أخطاء الدفع
                        </a>
                    </div>
                </div>
            @endif
            
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">اختر الكورسات لكل شهر (يمكنك اختيار أكثر من كورس أو عدم اختيار أي كورس)</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('parent-portal.save-course-selections') }}" method="POST">
                @csrf
                <div class="row">
                    @forelse($months as $index => $month)
                        <div class="col-md-6 mb-4">
                            <div class="card border-primary">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">الشهر: <strong>{{ $month }}</strong></h6>
                                    <input type="hidden" name="month_labels[{{ $index }}]" value="{{ $month }}">
                                </div>
                                <div class="card-body">
                                    <div class="form-check">
                                        @foreach($courses as $course)
                                            <div class="mb-2">
                                                <input type="checkbox" 
                                                       name="courses[{{ $index }}][]" 
                                                       value="{{ $course->id }}"
                                                       id="course_{{ $index }}_{{ $course->id }}"
                                                       class="form-check-input">
                                                <label class="form-check-label" for="course_{{ $index }}_{{ $course->id }}">
                                                    {{ $course->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle"></i> اترك فارغاً إذا لم تختر أي كورس
                                    </small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-md-12">
                            <div class="alert alert-warning">
                                لا توجد شهور للمعالجة
                            </div>
                        </div>
                    @endforelse
                </div>

                @if(count($months) > 0)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check"></i> حفظ والمتابعة
                            </button>
                            <a href="{{ route('parent-portal.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
