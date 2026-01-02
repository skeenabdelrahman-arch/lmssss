@extends('front.layouts.app')

@section('title', 'تفعيل الكود')

@section('content')
<div class="page-header" style="background: linear-gradient(135deg, {{ primary_color() }}, {{ secondary_color() }}); padding: 60px 0; text-align: center; color: white; margin-top: 90px;">
    <div class="container">
        <h1 style="font-size: 2.5rem; margin-bottom: 10px;">🔑 تفعيل كود الاشتراك</h1>
        <p style="font-size: 1.1rem; opacity: 0.9;">أدخل الكود للوصول إلى الكورس الخاص بك</p>
    </div>
</div>

<div class="container" style="max-width: 600px; margin: 50px auto; padding: 20px;">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; border-left: 4px solid #28a745;">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px; border-left: 4px solid #dc3545;">
            <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="modern-card" style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 30px;">
            @if(file_exists(public_path(logo_path())))
                <img src="{{ asset(logo_path()) }}" alt="Logo" style="max-width: 100px; margin-bottom: 20px;">
            @endif
            <h2 style="color: {{ primary_color() }}; font-size: 1.8rem; margin-bottom: 10px;">تفعيل الكود</h2>
            <p style="color: #666; font-size: 1rem;">أدخل كود التفعيل الموجود في البطاقة</p>
        </div>

        <form action="{{ route('activation_code.activate') }}" method="POST" id="activationForm">
            @csrf
            
            <div class="mb-4">
                <label for="code" class="form-label" style="font-weight: 600; color: #333; font-size: 1.1rem;">
                    <i class="fas fa-key me-2" style="color: {{ primary_color() }};"></i>كود التفعيل
                </label>
                <input 
                    type="text" 
                    class="form-control @error('code') is-invalid @enderror" 
                    id="code" 
                    name="code" 
                    placeholder="XXXXXXXXXXXX" 
                    required
                    style="font-family: 'Courier New', monospace; font-size: 1.3rem; text-align: center; padding: 15px; border: 2px solid #ddd; border-radius: 12px; letter-spacing: 2px;"
                    oninput="this.value = this.value.toUpperCase()"
                >
                @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted" style="display: block; margin-top: 8px; text-align: center;">
                    <i class="fas fa-info-circle me-1"></i>أدخل الكود المكون من 12 حرف/رقم
                </small>
            </div>

            <button type="submit" class="btn btn-lg w-100" style="background: linear-gradient(135deg, {{ primary_color() }}, {{ secondary_color() }}); color: white; border: none; padding: 15px; border-radius: 12px; font-size: 1.2rem; font-weight: 600; transition: all 0.3s;">
                <i class="fas fa-unlock me-2"></i>تفعيل الكود
            </button>
        </form>

        <div style="margin-top: 30px; padding-top: 30px; border-top: 1px dashed #ddd;">
            <h5 style="color: {{ primary_color() }}; margin-bottom: 15px; font-size: 1.1rem;">
                <i class="fas fa-question-circle me-2"></i>أين أجد الكود؟
            </h5>
            <ul style="list-style: none; padding: 0; color: #666; line-height: 2;">
                <li><i class="fas fa-check text-success me-2"></i>الكود موجود في البطاقة التي حصلت عليها</li>
                <li><i class="fas fa-check text-success me-2"></i>يمكنك مسح الـ QR Code الموجود في البطاقة</li>
                <li><i class="fas fa-check text-success me-2"></i>تأكد من إدخال الكود بشكل صحيح</li>
            </ul>
        </div>

        <div style="margin-top: 25px; text-align: center; padding: 20px; background: #f0f7ff; border-radius: 12px; border: 2px dashed {{ primary_color() }};">
            <h5 style="color: {{ primary_color() }}; margin-bottom: 15px;">
                <i class="fas fa-book-open me-2"></i>هل تحتاج مساعدة؟
            </h5>
            <a href="{{ route('student.activate.instructions') }}" class="btn" style="background: linear-gradient(135deg, {{ primary_color() }}, {{ secondary_color() }}); color: white; border: none; padding: 12px 30px; border-radius: 25px; font-weight: 600; margin: 5px;">
                <i class="fas fa-info-circle me-2"></i>شرح طريقة التفعيل
            </a>
            @if(whatsapp_number())
            <a href="https://wa.me/{{ str_replace(['+', ' ', '-'], '', whatsapp_number()) }}" target="_blank" class="btn btn-success" style="background: #25D366; border: none; padding: 12px 30px; border-radius: 25px; margin: 5px;">
                <i class="fab fa-whatsapp me-2"></i>تواصل معنا
            </a>
            @endif
        </div>
    </div>
</div>

<style>
    .form-control:focus {
        border-color: {{ primary_color() }};
        box-shadow: 0 0 0 0.2rem rgba(116, 36, 169, 0.25);
    }
    
    button[type="submit"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(116, 36, 169, 0.3);
    }
</style>
@endsection
