@extends('front.layouts.app')
@section('title')
{{ site_name() }} | تفاصيل الكورس
@endsection

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
        --primary-light: {{ primary_color() }};
    }

    .course-details-section {
        padding: 120px 0 80px;
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.03), rgba(250, 137, 107, 0.03));
        min-height: calc(100vh - 90px);
    }

    .page-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 15px;
    }

    .page-header .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
        justify-content: center;
    }

    .page-header .breadcrumb-item a {
        color: var(--secondary-color);
        text-decoration: none;
    }

    .page-header .breadcrumb-item.active {
        color: var(--primary-color);
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
    }

    .feature-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        text-decoration: none;
        display: block;
        border: 2px solid transparent;
        position: relative;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 50px rgba(116, 36, 169, 0.2);
        border-color: var(--primary-color);
        text-decoration: none;
    }

    .feature-icon-wrapper {
        height: 200px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .feature-icon-wrapper::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.2), transparent);
        transition: transform 0.5s ease;
    }

    .feature-card:hover .feature-icon-wrapper::before {
        transform: rotate(180deg);
    }

    .feature-icon {
        font-size: 70px;
        color: white;
        position: relative;
        z-index: 1;
        transition: transform 0.3s ease;
    }

    .feature-card:hover .feature-icon {
        transform: scale(1.2) rotate(5deg);
    }

    .feature-content {
        padding: 30px;
        text-align: center;
    }

    .feature-content h3 {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--primary-color);
        margin: 0;
        transition: color 0.3s ease;
    }

    .feature-card:hover .feature-content h3 {
        color: var(--secondary-color);
    }

    @media (max-width: 768px) {
        .features-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .page-header h1 {
            font-size: 2rem;
        }
    }
</style>

<section class="course-details-section">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-info-circle me-2"></i>تفاصيل الكورس</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="{{route('courses.index')}}">الكورسات</a></li>
                    <li class="breadcrumb-item active">تفاصيل الكورس</li>
                </ol>
            </nav>
        </div>

        @php
            $month = \App\Models\Month::find($month_id);
            $student = Auth::guard('student')->user();
            
            // Check if course is free
            $isFree = $month && (float)$month->price == 0;
            
            // Check if subscribed
            $isSubscribed = \App\Models\StudentSubscriptions::where('student_id', $student->id)
                ->where('month_id', $month_id)
                ->where('is_active', 1)
                ->exists();
            
            // Check if has activation code
            $hasActivationCode = \App\Models\ActivationCode::where('student_id', $student->id)
                ->where('month_id', $month_id)
                ->whereNotNull('used_at')
                ->exists();
            
            // Final check: use the central policy (includes has_all_access) to ensure consistency
            $canAccess = \App\Policies\StudentSubscriptionPolicy::canAccessMonth($month_id);
        @endphp

        @if(!$canAccess && $month)
        @php
            $paymentMethods = payment_methods();
            $showActivationCode = is_payment_method_enabled('activation_codes');
            $showOnlinePayment = is_payment_method_enabled('online_payment') && (float)$month->price > 0;
        @endphp
        
        @if($showActivationCode)
        <!-- Activation Code Card -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-8">
                <div class="card shadow-lg" style="border-radius: 20px; border: 2px solid #4caf50; background: linear-gradient(135deg, #f1f8e9 0%, #e8f5e9 100%);">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-key" style="font-size: 50px; color: #4caf50; margin-bottom: 15px;"></i>
                        <h4 style="color: #2e7d32; font-weight: bold; margin-bottom: 15px;">
                            لديك كود تفعيل؟
                        </h4>
                        <form method="POST" action="{{ route('activation.activate') }}" id="activationForm" class="mb-3">
                            @csrf
                            <input type="hidden" name="month_id" value="{{ $month_id }}">
                            <div class="input-group mb-3" style="max-width: 400px; margin: 0 auto;">
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       name="code" 
                                       id="activationCode" 
                                       placeholder="أدخل كود التفعيل" 
                                       style="border-radius: 10px 0 0 10px; text-align: center; font-size: 18px; font-weight: bold; letter-spacing: 2px;"
                                       required>
                                <button type="submit" 
                                        class="btn btn-success btn-lg" 
                                        id="activateBtn"
                                        style="border-radius: 0 10px 10px 0; padding: 12px 30px;">
                                    <i class="fas fa-check me-2"></i> تفعيل
                                </button>
                            </div>
                            <div id="activationMessage" class="mt-2" style="display: none;"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        @if($showOnlinePayment)
        <!-- Payment Card -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <div class="card shadow-lg" style="border-radius: 20px; border: 2px solid var(--primary-color);">
                    <div class="card-body p-4 text-center">
                        <i class="fas fa-lock" style="font-size: 60px; color: var(--primary-color); margin-bottom: 20px;"></i>
                        <h3 style="color: var(--primary-color); font-weight: bold; margin-bottom: 15px;">
                            اشترك الآن للوصول إلى المحتوى
                        </h3>
                        <p style="font-size: 18px; color: #666; margin-bottom: 25px;">
                            سعر الكورس: <strong style="color: var(--primary-color); font-size: 24px;">{{ number_format($month->price, 2) }} ج.م</strong>
                        </p>
                        <a href="{{ route('payment.show', $month_id) }}" class="btn btn-lg" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); color: white; padding: 15px 50px; border-radius: 50px; font-size: 20px; font-weight: bold; text-decoration: none; box-shadow: 0 4px 15px rgba(116, 36, 169, 0.3);">
                            <i class="fas fa-credit-card me-2"></i> ادفع الآن
                        </a>
                        <div class="mt-3">
                            <a href="{{ route('payment.history') }}" style="color: #666; text-decoration: none;">
                                <i class="fas fa-history me-2"></i> عرض سجل المدفوعات
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        @if(!$showActivationCode && !$showOnlinePayment && (float)$month->price > 0)
        <!-- No Payment Methods Available -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <div class="alert alert-warning text-center" style="border-radius: 20px; padding: 40px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 60px; color: #ff9800; margin-bottom: 20px;"></i>
                    <h3 style="color: #ff9800; font-weight: bold; margin-bottom: 15px;">
                        لا توجد وسائل دفع متاحة حالياً
                    </h3>
                    <p style="font-size: 18px; color: #666;">
                        يرجى التواصل مع الإدارة للاشتراك في هذا الكورس
                    </p>
                </div>
            </div>
        </div>
        @endif
        @endif

        @if($canAccess)
        <div class="features-grid">
            <a href="{{route('videos', ['month_id' => $month_id])}}" class="feature-card">
                <div class="feature-icon-wrapper">
                    <i class="fab fa-youtube feature-icon"></i>
                </div>
                <div class="feature-content">
                    <h3>دروس وفيديوهات</h3>
                </div>
            </a>

            <a href="{{route('exams', ['month_id' => $month_id])}}" class="feature-card">
                <div class="feature-icon-wrapper">
                    <i class="fas fa-clipboard-check feature-icon"></i>
                </div>
                <div class="feature-content">
                    <h3>امتحانات</h3>
                </div>
            </a>

            <a href="{{route('pdfs', ['month_id' => $month_id])}}" class="feature-card">
                <div class="feature-icon-wrapper">
                    <i class="fas fa-file-pdf feature-icon"></i>
                </div>
                <div class="feature-content">
                    <h3>مذكرات</h3>
                </div>
            </a>
            <a href="{{route('student.assignments.index', ['month_id' => $month_id])}}" class="feature-card">
                <div class="feature-icon-wrapper">
                    <i class="fas fa-tasks feature-icon"></i>
                </div>
                <div class="feature-content">
                    <h3>الواجبات</h3>
                </div>
            </a>
        </div>
        @endif
    </div>
</section>

<script>
    // Activation Code Form Handling
    document.getElementById('activationForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const codeInput = document.getElementById('activationCode');
        const activateBtn = document.getElementById('activateBtn');
        const messageDiv = document.getElementById('activationMessage');
        const code = codeInput.value.trim().toUpperCase();
        
        if (!code) {
            messageDiv.innerHTML = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i> يرجى إدخال كود التفعيل</div>';
            messageDiv.style.display = 'block';
            return;
        }
        
        // Disable button
        activateBtn.disabled = true;
        activateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري التفعيل...';
        messageDiv.style.display = 'none';
        
        // Validate code first
        fetch('{{ route("activation.validate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                code: code,
                month_id: {{ $month_id }}
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.valid) {
                // Submit form if valid
                form.submit();
            } else {
                messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i> ' + data.message + '</div>';
                messageDiv.style.display = 'block';
                activateBtn.disabled = false;
                activateBtn.innerHTML = '<i class="fas fa-check me-2"></i> تفعيل';
            }
        })
        .catch(error => {
            messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i> حدث خطأ أثناء التحقق من الكود</div>';
            messageDiv.style.display = 'block';
            activateBtn.disabled = false;
            activateBtn.innerHTML = '<i class="fas fa-check me-2"></i> تفعيل';
        });
    });
    
    // Auto uppercase code input
    document.getElementById('activationCode')?.addEventListener('input', function(e) {
        this.value = this.value.toUpperCase();
    });
</script>

@endsection
