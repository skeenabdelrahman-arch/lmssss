@extends('front.layouts.app')
@section('title')
{{ site_name() }} | شروط الاستخدام
@endsection

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0"><i class="fas fa-gavel me-2"></i>شروط الاستخدام</h2>
                </div>
                <div class="card-body p-4">
                    <div class="content">
                        @if(terms_of_service())
                            {!! nl2br(e(terms_of_service())) !!}
                        @else
                            <div class="alert alert-info">
                                <p>شروط الاستخدام قيد التحديث...</p>
                            </div>
                        @endif
                    </div>
                    <div class="mt-4">
                        <a href="{{ url('/') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-right me-2"></i>العودة للصفحة الرئيسية
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


