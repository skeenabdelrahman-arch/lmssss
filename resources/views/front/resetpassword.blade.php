@extends('front.layouts.app')
@section('title')
استعادة كلمة المرور | {{ site_name() }}
@endsection

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
        --primary-light: #b05ee7;
    }

    .auth-section {
        padding: 120px 0 80px;
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.03), rgba(250, 137, 107, 0.03));
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
        box-shadow: 0 10px 40px rgba(116, 36, 169, 0.1);
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .auth-card:hover {
        box-shadow: 0 15px 50px rgba(116, 36, 169, 0.15);
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
        box-shadow: 0 0 0 4px rgba(116, 36, 169, 0.1);
        outline: none;
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
        box-shadow: 0 8px 25px rgba(116, 36, 169, 0.3);
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
    }

    .auth-links a:hover {
        color: var(--primary-color);
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
                    <h2><i class="fas fa-key me-2"></i>هل نسيت كلمة المرور؟</h2>
                    <p>أدخل بريدك الإلكتروني لاستعادة كلمة المرور</p>
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

                <form method="POST" action="{{url('reset-password')}}" id="resetPasswordForm">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input 
                            type="email" 
                            name="email" 
                            class="form-control" 
                            placeholder="أدخل بريدك الإلكتروني"
                            required
                            autofocus
                            id="resetEmail"
                        >
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-paper-plane me-2"></i>إرسال رابط الاستعادة
                    </button>

                    <div class="auth-links">
                        <p>
                            <a href="{{route('studentLogin')}}">
                                <i class="fas fa-sign-in-alt me-1"></i>تذكرت كلمة المرور؟ تسجيل الدخول
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
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('resetPasswordForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            // التأكد من أن CSRF token موجود
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token not found');
                e.preventDefault();
                alert('حدث خطأ. يرجى تحديث الصفحة والمحاولة مرة أخرى.');
                return false;
            }
            
            // تعطيل الزر لمنع الإرسال المتكرر
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الإرسال...';
            }
        });
    }
    
    // إعادة تفعيل الزر إذا فشل الإرسال
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            // الصفحة تم تحميلها من cache
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>إرسال رابط الاستعادة';
            }
        }
    });
});
</script>

@endsection
