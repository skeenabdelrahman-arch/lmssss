@extends('errors.layout')

@section('title', '404 - الصفحة غير موجودة')

@section('content')
<div>
    <div class="error-icon">
        <i class="fas fa-search"></i>
    </div>
    <div class="error-code">404</div>
    <div class="error-title">الصفحة غير موجودة</div>
    <div class="error-message">
        عذراً، الصفحة التي تبحث عنها غير موجودة أو تم نقلها إلى مكان آخر.
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
