@extends('front.layouts.app')
@section('title')
تغيير كلمة المرور | {{ site_name() }}
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

    .input-group-password {
        position: relative;
    }

    .form-control {
        padding: 15px 20px;
        padding-left: 50px;
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
                    <h2><i class="fas fa-lock me-2"></i>تغيير كلمة المرور</h2>
                    <p>أدخل كلمة المرور الجديدة</p>
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

                <form method="POST" action="{{url('reset/'.$token)}}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">كلمة المرور الجديدة</label>
                        <div class="input-group-password">
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                class="form-control" 
                                placeholder="أدخل كلمة المرور الجديدة"
                                required
                                autofocus
                            >
                            <span class="password-toggle" id="passwordToggle">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save me-2"></i>إعادة التعيين
                    </button>
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
</script>

@endsection
