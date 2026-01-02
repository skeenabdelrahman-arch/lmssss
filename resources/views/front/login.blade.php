@extends('front.layouts.app')
@section('title')
تسجيل الدخول | {{ site_name() }}
@endsection

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
        --primary-light: {{ primary_color() }};
    }

    .auth-section {
        padding: 120px 0 80px;
        background: linear-gradient(135deg, {{ hexToRgba(primary_color(), 0.03) }}, {{ hexToRgba(secondary_color(), 0.03) }});
        min-height: calc(100vh - 90px);
    }

    .auth-container {
        max-width: 500px;
        margin: 0 auto;
    }

    .auth-card {
        background: white;
        border-radius: 20px;
        padding: 50px 40px;
        box-shadow: 0 10px 40px {{ hexToRgba(primary_color(), 0.1) }};
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .auth-card:hover {
        box-shadow: 0 15px 50px {{ hexToRgba(primary_color(), 0.15) }};
        border-color: var(--primary-color);
    }

    .auth-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .auth-header h2 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 10px;
    }

    .auth-header p {
        color: #6c757d;
        font-size: 1rem;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 8px;
        display: block;
    }

    .form-control {
        padding: 15px 20px;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        font-size: 16px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px {{ hexToRgba(primary_color(), 0.1) }};
        outline: none;
    }

    .input-group {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        transition: color 0.3s ease;
        z-index: 10;
    }

    .password-toggle:hover {
        color: var(--primary-color);
    }

    .input-group .form-control {
        padding-left: 50px;
    }

    .btn-submit {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 10px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px {{ hexToRgba(primary_color(), 0.3) }};
    }

    .auth-links {
        text-align: center;
        margin-top: 25px;
    }

    .auth-links a {
        color: var(--secondary-color);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-block;
        margin: 0 10px;
    }

    .auth-links a:hover {
        color: var(--primary-color);
        transform: translateX(-3px);
    }

    .alert {
        border-radius: 12px;
        padding: 15px 20px;
        margin-bottom: 25px;
        border: none;
    }

    .alert-danger {
        background: #fee;
        color: #c33;
        border-right: 4px solid #c33;
    }

    .alert-success {
        background: #efe;
        color: #3c3;
        border-right: 4px solid #3c3;
    }

    .help-text {
        font-size: 0.85rem;
        color: var(--secondary-color);
        margin-top: 5px;
    }

    .auth-image {
        text-align: center;
        margin-top: 30px;
    }

    .auth-image img {
        max-width: 200px;
        opacity: 0.8;
    }

    @media (max-width: 768px) {
        .auth-card {
            padding: 40px 30px;
        }

        .auth-header h2 {
            font-size: 1.7rem;
        }
    }
</style>

<section class="auth-section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <h2><i class="fas fa-sign-in-alt me-2"></i>مرحباً بعودتك</h2>
                    <p>سجل دخولك للوصول إلى محتواك التعليمي</p>
                </div>

                @if(session()->has('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>{{ session()->get('error') }}</strong>
                    </div>
                @elseif(session()->has('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>{{ session()->get('success') }}</strong>
                    </div>
                @endif

                <form method="POST" action="{{route('goLogin')}}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">رقم الهاتف</label>
                        <input 
                            type="number" 
                            name="student_phone" 
                            class="form-control" 
                            id="student_phone"
                            placeholder="أدخل رقم هاتفك"
                            maxlength="11"
                            required
                            autofocus
                        >
                        <span class="help-text">
                            <i class="fas fa-info-circle me-1"></i>
                            يجب أن يكون 11 رقم فقط
                        </span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">كلمة المرور</label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                name="password" 
                                class="form-control" 
                                id="password"
                                placeholder="أدخل كلمة المرور"
                                required
                            >
                            <span class="password-toggle" id="passwordToggle">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-sign-in-alt me-2"></i>تسجيل الدخول
                    </button>

                    <div class="auth-links">
                        <p style="margin-bottom: 15px;">
                            <a href="{{route('studentSignup')}}">
                                <i class="fas fa-user-plus me-1"></i>لا يوجد لديك حساب؟ سجل الآن
                            </a>
                        </p>
                        <p>
                            <a href="{{route('showresetPassword')}}">
                                <i class="fas fa-key me-1"></i>هل نسيت كلمة المرور؟
                            </a>
                        </p>
                    </div>
                </form>

                <div class="auth-image">
                    <img src="{{ asset(logo_path()) }}" alt="Logo">
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Password Toggle
    document.getElementById('passwordToggle').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    });

    // Phone Validation
    document.getElementById('student_phone').addEventListener('input', function(e) {
        if (this.value.length > 11) {
            this.value = this.value.slice(0, 11);
        }
    });

    // Form Validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const phone = document.getElementById('student_phone').value;
        if (phone.length !== 11) {
            e.preventDefault();
            alert('يجب إدخال 11 رقم بالضبط');
            return false;
        }
    });
</script>

@endsection
