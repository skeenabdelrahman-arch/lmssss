@extends('front.layouts.app')
@section('title')
{{ site_name() }} | سياسة الخصوصية
@endsection

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0"><i class="fas fa-shield-alt me-2"></i>سياسة الخصوصية</h2>
                </div>
                <div class="card-body p-4">
                    <div class="content">
                        @if(privacy_policy())
                            {!! nl2br(e(privacy_policy())) !!}
                        @else
                            <div class="alert alert-info">
                                <p>سياسة الخصوصية قيد التحديث...</p>
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


