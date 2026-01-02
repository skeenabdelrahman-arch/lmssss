@extends('front.layouts.app')
@section('title')
{{ site_name() }} | تسجيل الدخول
@endsection

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h2 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>تسجيل الدخول غير متاح</h2>
                </div>
                <div class="card-body p-4 text-center">
                    <i class="fas fa-lock fa-4x text-warning mb-3"></i>
                    <h4>عذراً، تسجيل الدخول غير متاح حالياً</h4>
                    <p class="text-muted">نعتذر عن الإزعاج. يرجى المحاولة لاحقاً.</p>
                    <a href="{{ url('/') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-arrow-right me-2"></i>العودة للصفحة الرئيسية
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


