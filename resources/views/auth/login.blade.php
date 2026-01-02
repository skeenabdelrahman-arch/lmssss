@extends('front.layouts.app')

@section('title')
ADMIN PANEL | تسجيل دخول الإدارة
@endsection

@section('content')
<style>
    /* الحاوية الرئيسية */
    .admin-auth-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f0f4ff, #e9edf5);
        padding: 30px 15px;
        font-family: 'Tajawal', sans-serif;
    }

    /* صندوق تسجيل الدخول */
    .auth-card {
        background: #ffffff;
        border-radius: 25px;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.1);
        display: flex;
        max-width: 900px;
        width: 100%;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .auth-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 70px rgba(0, 0, 0, 0.15);
    }

    /* الجانب الأيمن: الفورم */
    .auth-form-side {
        padding: 50px 40px;
        flex: 1;
    }

    /* الجانب الأيسر: جمالي */
    .auth-visual-side {
        background: linear-gradient(135deg, #7424a9, #4e148c);
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: #ffffff;
        padding: 50px 30px;
        text-align: center;
    }

    .visual-logo {
        max-width: 120px;
        filter: brightness(0) invert(1);
        margin-bottom: 20px;
        transition: transform 0.3s ease;
    }

    .visual-logo:hover {
        transform: scale(1.1);
    }

    .auth-header h2 {
        font-weight: 800;
        font-size: 30px;
        color: #2d3748;
        margin-bottom: 10px;
    }

    .auth-header p {
        color: #6b7280;
        font-size: 15px;
        margin-bottom: 25px;
    }

    /* الحقول */
    .form-group label {
        font-weight: 600;
        font-size: 14px;
        color: #4a5568;
        margin-bottom: 8px;
    }

    .custom-input {
        height: 55px !important;
        border-radius: 15px !important;
        border: 1.5px solid #d1d5db !important;
        padding: 12px 20px !important;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .custom-input:focus {
        border-color: #7424a9 !important;
        box-shadow: 0 0 0 4px rgba(116, 36, 169, 0.1) !important;
    }

    /* زر الدخول */
    .btn-submit {
        background: #7424a9;
        color: #fff;
        height: 55px;
        border-radius: 15px;
        width: 100%;
        border: none;
        font-weight: 700;
        font-size: 17px;
        margin-top: 20px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-submit:hover {
        background: #5e1d8a;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(116, 36, 169, 0.2);
    }

    /* أيقونة العين */
    .password-toggle-btn {
        background: #f8f9fa;
        border: 1.5px solid #d1d5db;
        border-left: none;
        border-radius: 0 15px 15px 0 !important;
        color: #718096;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .password-toggle-btn:hover {
        background: #f1f3f6;
    }

    /* استجابة الشاشات الصغيرة */
    @media (max-width: 768px) {
        .auth-card {
            flex-direction: column;
        }
        .auth-visual-side { display: none; }
        .auth-form-side { padding: 30px 20px; }
    }

</style>

<div class="admin-auth-container">
    <div class="auth-card">
        
        <div class="auth-form-side">
            <div class="auth-header">
                <h2>دخول المشرف</h2>
                <p>أهلاً بك يا مستر، يرجى تسجيل الدخول للتحكم</p>
            </div>

            @if(session()->has('error'))
                <div class="alert alert-danger border-0 rounded-pill px-4">
                    <small><b>عذراً!</b> {{ session()->get('error') }}</small>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="form-group mb-4">
                    <label>البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control custom-input @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" required placeholder="admin@example.com">
                    @error('email')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label>كلمة المرور</label>
                    <div class="input-group">
                        <input id="password" type="password" name="password" 
                               class="form-control custom-input @error('password') is-invalid @enderror" 
                               required placeholder="••••••••" style="border-left: none !important;">
                        <div class="input-group-append">
                            <span class="input-group-text password-toggle-btn" id="password-toggle">
                                <i class="fa fa-eye" id="toggle-icon"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                        <label class="custom-control-label text-muted" for="remember" style="font-size: 14px;">تذكر دخولي</label>
                    </div>
                    <a href="/" class="text-muted" style="font-size: 13px;"><i class="fa fa-home"></i> العودة للموقع</a>
                </div>

                <button type="submit" class="btn-submit">
                    <span>تسجيل الدخول</span>
                    <i class="fa fa-chevron-left"></i>
                </button>
            </form>
        </div>

        <div class="auth-visual-side">
<img src="{{ asset('storage/front/assets/images/teacher_image.jpg') }}" alt="Logo" class="visual-logo">
            <h3 class="mb-3">لوحة التحكم الذكية</h3>
            <p class="px-3" style="opacity: 0.85; font-size: 14px;">
                هذه المنطقة مخصصة للإدارة فقط. تأكد من الحفاظ على سرية بياناتك لضمان أمن المنصة.
            </p>
            <div class="mt-5">
                <i class="fa fa-shield-alt fa-3x" style="opacity: 0.5;"></i>
            </div>
        </div>

    </div>
</div>

<script>
    document.getElementById('password-toggle').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggle-icon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
</script>
@endsection
