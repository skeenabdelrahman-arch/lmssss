@extends('front.layouts.app')
@section('title')
إنشاء حساب | {{ site_name() }}
@endsection

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
        --primary-light: {{ primary_color() }};
    }

    .auth-section {
        padding: 100px 0 80px;
        background: linear-gradient(135deg, {{ hexToRgba(primary_color(), 0.03) }}, {{ hexToRgba(secondary_color(), 0.03) }});
        min-height: calc(100vh - 90px);
    }

    .auth-container {
        max-width: 1200px;
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

    .signup-benefits-card {
        background: linear-gradient(135deg, {{ primary_color() }} 0%, {{ secondary_color() }} 100%);
        border-radius: 20px;
        padding: 50px 40px;
        color: white;
        box-shadow: 0 10px 40px {{ hexToRgba(primary_color(), 0.2) }};
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .signup-benefits-card h3 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 30px;
        color: white;
    }

    .signup-benefits-card .subtitle {
        font-size: 1.3rem;
        margin-bottom: 40px;
        opacity: 0.95;
        font-weight: 500;
    }

    .signup-benefits-card .benefit-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 25px;
        padding: 15px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .signup-benefits-card .benefit-item:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateX(-5px);
    }

    .signup-benefits-card .benefit-item i {
        font-size: 1.5rem;
        margin-left: 15px;
        margin-top: 5px;
        color: #ffd700;
    }

    .signup-benefits-card .benefit-item .benefit-text {
        flex: 1;
    }

    .signup-benefits-card .benefit-item .benefit-text strong {
        display: block;
        font-size: 1.1rem;
        margin-bottom: 5px;
    }

    .signup-benefits-card .benefit-item .benefit-text p {
        margin: 0;
        opacity: 0.9;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    @media (max-width: 992px) {
        .signup-benefits-card {
            margin-top: 30px;
        }
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
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 8px;
        display: block;
        font-size: 0.95rem;
    }

    .form-control,
    .form-select {
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px {{ hexToRgba(primary_color(), 0.1) }};
        outline: none;
    }

    .input-group-password {
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

    .input-group-password .form-control {
        padding-left: 45px;
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
        margin-top: 20px;
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

    .img-preview {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 10px;
        margin-top: 10px;
        border: 2px solid var(--primary-color);
        display: none;
    }

    .img-preview.show {
        display: block;
    }

    @media (max-width: 768px) {
        .auth-card {
            padding: 40px 25px;
        }

        .auth-header h2 {
            font-size: 1.7rem;
        }
    }
</style>

<section class="auth-section">
    <div class="container">
        <div class="auth-container">
            <div class="row g-4">
                <!-- Form Card -->
                <div class="col-lg-7">
                    <div class="auth-card">
                <div class="auth-header">
                    <h2><i class="fas fa-user-plus me-2"></i>أهلاً بك في عالم {{ subject_name() }}</h2>
                    <p>أنشئ حسابك الآن وابدأ رحلتك التعليمية</p>
                </div>

                @if ($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>يرجى تصحيح الأخطاء التالية:</strong>
        <ul style="margin-top: 10px; padding-right: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>{{ session('error') }}</strong>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle me-2"></i>
        <strong>{{ session('success') }}</strong>
    </div>
@endif

                <form method="POST" action="{{route('student.store')}}" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="row">
                        <!-- الأسماء -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">الاسم الأول</label>
                                <input type="text" name="first_name" class="form-control" placeholder="الاسم الأول" required onkeypress="validateInput(event)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">الاسم الثاني</label>
                                <input type="text" name="second_name" class="form-control" placeholder="الاسم الثاني" required onkeypress="validateInput(event)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">الاسم الثالث</label>
                                <input type="text" name="third_name" class="form-control" placeholder="الاسم الثالث" required onkeypress="validateInput(event)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">الاسم الرابع</label>
                                <input type="text" name="forth_name" class="form-control" placeholder="الاسم الرابع" required onkeypress="validateInput(event)">
                            </div>
                        </div>

                        <!-- الهاتف والبريد -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">رقم هاتف الطالب</label>
                                <input type="number" name="student_phone" class="form-control" placeholder="رقم الهاتف" maxlength="11" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">رقم هاتف ولي الأمر</label>
                                <input type="number" name="parent_phone" class="form-control" placeholder="رقم هاتف ولي الأمر" maxlength="11" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">البريد الإلكتروني</label>
                                <input type="email" name="email" class="form-control" placeholder="البريد الإلكتروني" required>
                            </div>
                        </div>

                        <!-- المحافظة والنوع -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">المحافظة</label>
                                <select name="city" class="form-select" required>
                                    <option value="">اختر المحافظة...</option>
                                    @foreach(governorates_list() as $gov)
                                    <option value="{{ $gov['name'] }}">{{ $gov['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">النوع</label>
                                <select name="gender" class="form-select" required>
                                    <option value="">اختر النوع...</option>
                                    <option value="ذكر">ذكر</option>
                                    <option value="انثى">انثى</option>
                                </select>
                            </div>
                        </div>

                        <!-- الصف ونوع التسجيل -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">الصف الدراسي</label>
                                <select name="grade" class="form-select" required>
                                    <option value="">اختر الصف...</option>
                                    @foreach(signup_grades() as $grade)
                                    <option value="{{ $grade['value'] }}">{{ $grade['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">نوع التسجيل</label>
                                <select name="register" class="form-select" required>
                                    <option value="اونلاين">اونلاين</option>
                                </select>
                            </div>
                        </div>

                        <!-- كلمة المرور -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">كلمة المرور</label>
                                <div class="input-group-password">
                                    <input id="password" type="password" name="password" class="form-control" placeholder="كلمة المرور" required autocomplete="new-password">
                                    <span class="password-toggle" id="passwordToggle">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">تأكيد كلمة المرور</label>
                                <div class="input-group-password">
                                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="تأكيد كلمة المرور" required autocomplete="new-password">
                                    <span class="password-toggle" id="passwordToggleConfirm">
                                        <i class="fas fa-eye" id="toggleIconConfirm"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- صورة الطالب -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">صورة الطالب</label>
                                <input type="file" name="image" class="form-control" accept="image/*" required onchange="previewImage(this)">
                                <img id="imgPreview" class="img-preview" src="#" alt="معاينة الصورة">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-user-plus me-2"></i>إنشاء الحساب
                    </button>

                    <div class="auth-links">
                        <p>
                            <a href="{{route('studentLogin')}}">
                                <i class="fas fa-sign-in-alt me-1"></i>يوجد لديك حساب؟ تسجيل الدخول
                            </a>
                        </p>
                    </div>
                </form>
                    </div>
                </div>

                <!-- Benefits Card -->
                <div class="col-lg-5">
                    <div class="signup-benefits-card">
                        <h3>
                            <i class="fas fa-rocket me-2"></i>
                            {{ setting('signup_card_title', 'انضم إلينا اليوم!') }}
                        </h3>
                        <p class="subtitle">{{ setting('signup_card_subtitle', 'ابدأ رحلتك نحو نهائية الاحياء') }}</p>
                        <p class="subtitle" style="font-size: 1.1rem; margin-bottom: 30px;">
                            <strong>{{ setting('signup_card_teacher_name', 'مع مستر سامح صلاح') }}</strong>
                        </p>
                        
                        @php
                            $benefits = json_decode(setting('signup_card_benefits', '[]'), true);
                            if (empty($benefits)) {
                                $benefits = [
                                    ['title' => 'شرح مبسط وواضح', 'description' => 'لجميع النقاط', 'icon' => 'fa-book'],
                                    ['title' => 'فيديوهات عالية الجودة', 'description' => 'HD', 'icon' => 'fa-video'],
                                    ['title' => 'تمارين تفاعلية', 'description' => 'ومتنوعة', 'icon' => 'fa-tasks'],
                                    ['title' => 'امتحانات', 'description' => 'لقياس مستواك', 'icon' => 'fa-clipboard-check'],
                                    ['title' => 'متاح 24/7', 'description' => 'من أي مكان', 'icon' => 'fa-clock'],
                                ];
                            }
                        @endphp

                        @foreach($benefits as $benefit)
                            <div class="benefit-item">
                                <i class="fas {{ $benefit['icon'] ?? 'fa-check-circle' }}"></i>
                                <div class="benefit-text">
                                    <strong>{{ $benefit['title'] ?? '' }}</strong>
                                    <p>{{ $benefit['description'] ?? '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // تحقق من كتابة الأسماء بالعربية
    function validateInput(event) {
        var arabicRegex = /[\u0600-\u06FF\s]/;
        if(!arabicRegex.test(event.key)){
            event.preventDefault();
        }
    }

    // Toggle كلمة المرور
    document.getElementById('passwordToggle').addEventListener('click', function() {
        const input = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        if(input.type === 'password'){
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    document.getElementById('passwordToggleConfirm').addEventListener('click', function() {
        const input = document.getElementById('password_confirmation');
        const icon = document.getElementById('toggleIconConfirm');
        if(input.type === 'password'){
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });


    // معاينة الصورة قبل الرفع
    function previewImage(input) {
        const preview = document.getElementById('imgPreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.add('show');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

@endsection
