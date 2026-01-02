@extends('front.layouts.app')

@section('title', 'بوابة ولي الأمر')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0 d-flex align-items-center gap-2">
                        <i class="fas fa-user-tie"></i>
                        بوابة ولي الأمر
                    </h4>
                </div>

                <div class="card-body p-4">
                    <p class="text-muted mb-4">
                        أدخل أرقام الهواتف للبحث مباشرة عن تقرير الطالب. (لا حاجة للكود)
                    </p>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>خطأ في البحث!</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('parent-portal.search') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="parent_id" class="form-label fw-semibold">رقم هاتف ولي الأمر</label>
                            <input
                                type="text"
                                id="parent_id"
                                name="parent_id"
                                class="form-control @error('parent_id') is-invalid @enderror"
                                placeholder="مثال: 0100xxxxxxx أو +20100xxxxxxx"
                                value="{{ old('parent_id') }}"
                                required
                            >
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="student_id" class="form-label fw-semibold">رقم هاتف الطالب</label>
                            <input
                                type="text"
                                id="student_id"
                                name="student_id"
                                class="form-control @error('student_id') is-invalid @enderror"
                                placeholder="رقم هاتف الطالب (لا حاجة للكود)"
                                value="{{ old('student_id') }}"
                                required
                            >
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">يمكن استخدام الكود بدلاً من الهاتف في حال معرفته.</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> البحث عن التقرير
                        </button>
                    </form>

                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="mb-2">إرشادات سريعة</h6>
                        <ul class="mb-0 small ps-3">
                            <li>اكتب رقم هاتف ولي الأمر كما هو مسجّل.</li>
                            <li>اكتب رقم هاتف الطالب (أو الكود إذا توافر).</li>
                            <li>استخدم نفس صيغة التخزين (مثلاً مع كود الدولة أو بدونه).</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card { border-radius: 12px; }
    .card-header { border-radius: 12px 12px 0 0; }
    .form-control { height: 48px; }
</style>
@endsection
