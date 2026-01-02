@extends('errors.layout')

@section('title', '403 - غير مصرح')

@section('content')
<div>
    <div class="error-icon">
        <i class="fas fa-lock"></i>
    </div>
    <div class="error-code">403</div>
    <div class="error-title">غير مصرح بالوصول</div>
    <div class="error-message">
        عذراً، ليس لديك صلاحية للوصول إلى هذه الصفحة. يرجى الاتصال بالمسؤول للحصول على الصلاحيات المطلوبة.
    </div>
    <div class="error-actions">
        <a href="{{ url('/') }}" class="error-btn error-btn-primary">
            <i class="fas fa-home"></i>
            <span>العودة للرئيسية</span>
        </a>
        <button onclick="window.history.back()" class="error-btn error-btn-secondary">
            <i class="fas fa-arrow-right"></i>
            <span>العودة للخلف</span>
        </button>
    </div>
</div>
@endsection
