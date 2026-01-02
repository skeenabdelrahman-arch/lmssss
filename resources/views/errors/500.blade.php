@extends('errors.layout')

@section('title', '500 - خطأ في الخادم')

@section('content')
<div>
    <div class="error-icon">
        <i class="fas fa-exclamation-triangle"></i>
    </div>
    <div class="error-code">500</div>
    <div class="error-title">خطأ في الخادم</div>
    <div class="error-message">
        حدث خطأ غير متوقع في الخادم. يرجى المحاولة مرة أخرى لاحقاً أو الاتصال بالدعم الفني.
    </div>
    <div class="error-actions">
        <a href="{{ url('/') }}" class="error-btn error-btn-primary">
            <i class="fas fa-home"></i>
            <span>العودة للرئيسية</span>
        </a>
        <button onclick="window.location.reload()" class="error-btn error-btn-secondary">
            <i class="fas fa-redo"></i>
            <span>إعادة المحاولة</span>
        </button>
    </div>
</div>
@endsection
