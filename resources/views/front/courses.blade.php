@extends('front.layouts.app')
@section('title')
{{ site_name() }} | الكورسات
@endsection

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
        --primary-light: {{ primary_color() }};
    }

    .courses-section {
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

    .courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
    }

    .course-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        text-decoration: none;
        display: block;
        border: 2px solid transparent;
    }

    .course-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 50px rgba(116, 36, 169, 0.2);
        border-color: var(--primary-color);
        text-decoration: none;
    }

    .course-card.no-access {
        cursor: default;
        opacity: 0.95;
    }

    .course-card.no-access:hover {
        transform: none;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        border-color: transparent;
    }
    
    .course-card.no-access:hover .course-content h3 {
        color: var(--primary-color);
    }

    .course-icon {
        height: 200px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 80px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .course-icon.has-image {
        background: transparent;
        padding: 0;
    }

    .course-icon img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .course-icon::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.2), transparent);
        animation: rotate 10s linear infinite;
        z-index: 0;
    }

    .course-icon.has-image::before {
        display: none;
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .course-icon i {
        position: relative;
        z-index: 1;
    }

    .course-icon.has-image i {
        display: none;
    }

    .course-content {
        padding: 30px;
        text-align: center;
    }

    .course-content h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin: 0;
        transition: color 0.3s ease;
    }

    .course-card:hover .course-content h3 {
        color: var(--secondary-color);
    }

    .alert {
        border-radius: 12px;
        padding: 15px 20px;
        margin-bottom: 30px;
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

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    .empty-state i {
        font-size: 80px;
        color: var(--primary-light);
        margin-bottom: 20px;
    }

    .empty-state h3 {
        color: var(--primary-color);
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #6c757d;
    }

    @media (max-width: 768px) {
        .courses-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .page-header h1 {
            font-size: 2rem;
        }
    }
</style>

@php
    $student = auth()->guard('student')->user();
@endphp

@if(empty($student->image))
    <script>
        window.location.href = "{{ url('student-profile') }}?student_id={{ $student->id }}&force=1";
    </script>
@endif

<section class="courses-section">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-book me-2"></i>كورساتنا</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">الرئيسية</a></li>
                    <li class="breadcrumb-item active">الكورسات</li>
                </ol>
            </nav>
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

        @php
            // التأكد من أن $monthes موجودة ومجموعة
            $monthes = $monthes ?? collect();
            $studentGrade = $studentGrade ?? ($student->grade ?? 'غير محدد');
        @endphp
        
        {{-- Debug Info (يمكن إزالتها لاحقاً) --}}
        @if(config('app.debug'))
            <div class="alert alert-info mb-3">
                <small>
                    <strong>Debug Info:</strong> 
                    الصف الدراسي: {{ $studentGrade }}, 
                    عدد الكورسات: {{ $monthes->count() ?? 0 }}
                </small>
            </div>
        @endif
        
        @if(isset($monthes) && $monthes->count() > 0)
            <div class="courses-grid">
                @foreach ($monthes as $month)
                    @php
                        $student = Auth::guard('student')->user();
                        // استخدام StudentSubscriptionPolicy للتحقق من الوصول
                        $canAccess = \App\Policies\StudentSubscriptionPolicy::canAccessMonth($month->id);
                        $isFree = (float)$month->price == 0;

                        // تحقق صريح من شروط الوصول
                        $subscription = \App\Models\StudentSubscriptions::where('student_id', $student->id)
                            ->where('month_id', $month->id)
                            ->first();
                        
                        $hasActiveSubscription = $subscription && $subscription->is_active == 1;
                        
                        // استخراج سبب إلغاء التفعيل إذا كان الاشتراك موجود وغير مفعّل
                        $deactivationReason = null;
                        if ($subscription && $subscription->is_active == 0 && !empty($subscription->deactivation_reason)) {
                            $deactivationReason = $subscription->deactivation_reason;
                        }

                        $hasUsedActivationCode = \App\Models\ActivationCode::where('student_id', $student->id)
                            ->where('month_id', $month->id)
                            ->whereNotNull('used_at')
                            ->exists();

                        $explicitAccess = ($student->has_all_access || $isFree || $hasActiveSubscription || $hasUsedActivationCode);
                    @endphp
                    <div class="course-card {{ !$canAccess ? 'no-access' : '' }}" style="position: relative;">
                        @if($canAccess)
                            <a href="{{route('month.content', ['month_id' => $month->id])}}" style="text-decoration: none; color: inherit; display: block;">
                                <div class="course-icon {{ !empty($month->image) ? 'has-image' : '' }}">
                                    @if(!empty($month->image))
                                        <img src="{{ url('upload_files/' . $month->image) }}" alt="{{ $month->name }}" 
                                             onerror="this.onerror=null; this.style.display='none'; this.parentElement.classList.remove('has-image'); var icon = this.parentElement.querySelector('i'); if(icon) icon.style.display='flex';">
                                        <i class="fas fa-calendar-alt" style="display: none;"></i>
                                    @else
                                        <i class="fas fa-calendar-alt"></i>
                                    @endif
                                </div>
                                <div class="course-content">
                                    <h3>{{$month->name}}</h3>
                                    @if($deactivationReason)
                                        <p style="color: #dc3545; font-size: 13px; margin-top: 8px; margin-bottom: 5px; font-weight: 600;">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            {{ $deactivationReason }}
                                        </p>
                                    @endif
                                    @if($month->price)
                                        <p style="color: var(--primary-color); font-size: 18px; font-weight: bold; margin-top: 10px;">
                                            {{ number_format($month->price, 2) }} ج.م
                                        </p>
                                    @endif
                                </div>
                            </a>
                        @else
                            <div class="course-icon {{ !empty($month->image) ? 'has-image' : '' }}">
                                @if(!empty($month->image))
                                    <img src="{{ url('upload_files/' . $month->image) }}" alt="{{ $month->name }}" 
                                         onerror="this.onerror=null; this.style.display='none'; this.parentElement.classList.remove('has-image'); var icon = this.parentElement.querySelector('i'); if(icon) icon.style.display='flex';">
                                    <i class="fas fa-calendar-alt" style="display: none;"></i>
                                @else
                                    <i class="fas fa-calendar-alt"></i>
                                @endif
                            </div>
                            <div class="course-content">
                                <h3>{{$month->name}}</h3>
                                @if($deactivationReason)
                                    <p style="color: #dc3545; font-size: 13px; margin-top: 8px; margin-bottom: 5px; font-weight: 600;">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $deactivationReason }}
                                    </p>
                                @endif
                                @if($month->price)
                                    <p style="color: var(--primary-color); font-size: 18px; font-weight: bold; margin-top: 10px;">
                                        {{ number_format($month->price, 2) }} ج.م
                                    </p>
                                @endif
                            </div>
                        @endif
                        @php
                            $paymentMethods = payment_methods();
                            $showActivationCode = is_payment_method_enabled('activation_codes');
                            $showOnlinePayment = is_payment_method_enabled('online_payment') && !$isFree;
                        @endphp
                        
                        @if($explicitAccess)
                            <div style="padding: 15px; border-top: 1px solid #eee;">
                                <span class="badge bg-success w-100" style="padding: 10px; font-size: 14px;">
                                    <i class="fas fa-check-circle me-2"></i> مشترك
                                </span>
                            </div>
                            
                        @elseif(!$isFree)
                            <div style="padding: 15px; border-top: 1px solid #eee;">
                                <div class="d-grid gap-2">
                                    @if($showOnlinePayment)
                                    <a href="{{ route('payment.show', $month->id) }}" class="btn w-100" style="background: var(--primary-color); color: white; border-radius: 10px; font-weight: bold; margin-bottom: 8px;">
                                        <i class="fas fa-credit-card me-2"></i> ادفع الآن
                                    </a>
                                    @endif
                                    @if($showActivationCode)
                                    <button type="button" class="btn w-100" style="background: #4caf50; color: white; border-radius: 10px; font-weight: bold;" data-bs-toggle="modal" data-bs-target="#activationModal{{ $month->id }}">
                                        <i class="fas fa-key me-2"></i> أدخل الكود
                                    </button>
                                    @endif
                                    @if(!$showOnlinePayment && !$showActivationCode)
                                    <div class="alert alert-warning mb-0" style="padding: 10px; font-size: 12px;">
                                        <i class="fas fa-info-circle me-1"></i> لا توجد وسائل دفع متاحة
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>لا توجد كورسات متاحة</h3>
                <p>
                    @if(!empty($studentGrade) && $studentGrade != 'غير محدد')
                        لا توجد كورسات متاحة حالياً لصفك الدراسي (<strong>{{ $studentGrade }}</strong>). 
                    @else
                        لا توجد كورسات متاحة حالياً.
                    @endif
                    سيتم إضافة كورسات جديدة قريباً.
                </p>
                @if(!empty($studentGrade) && $studentGrade != 'غير محدد')
                    <p class="text-muted" style="font-size: 14px; margin-top: 10px;">
                        <i class="fas fa-info-circle me-1"></i> 
                        إذا كنت تعتقد أن هذا خطأ، يرجى التحقق من أن صفك الدراسي (<strong>{{ $studentGrade }}</strong>) صحيح في صفحة الملف الشخصي.
                    </p>
                @else
                    <p class="text-muted" style="font-size: 14px; margin-top: 10px;">
                        <i class="fas fa-info-circle me-1"></i> 
                        يرجى تحديث صفك الدراسي في صفحة الملف الشخصي لعرض الكورسات المتاحة لك.
                    </p>
                @endif
                
                {{-- معلومات إضافية للتشخيص --}}
                @php
                    $allMonths = \App\Models\Month::count();
                @endphp
                @if($allMonths > 0)
                    <p class="text-muted" style="font-size: 12px; margin-top: 10px; font-style: italic;">
                        <i class="fas fa-database me-1"></i> 
                        يوجد {{ $allMonths }} كورس في النظام، لكن لا يوجد كورسات متطابقة مع صفك الدراسي.
                    </p>
                @endif
            </div>
        @endif
    </div>
</section>

{{-- Activation Code Modals --}}
@foreach ($monthes as $month)
    @php
        $isFree = (float)$month->price == 0;
        $isSubscribed = \App\Models\StudentSubscriptions::where('student_id', $student->id)
            ->where('month_id', $month->id)
            ->where('is_active', 1)
            ->exists();
    @endphp
    
    @if(!$isSubscribed && !$isFree)
    <!-- Activation Modal for {{ $month->name }} -->
    <div class="modal fade" id="activationModal{{ $month->id }}" tabindex="-1" aria-labelledby="activationModalLabel{{ $month->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; border: none;">
                <div class="modal-header" style="background: linear-gradient(135deg, #4caf50 0%, #2e7d32 100%); color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title" id="activationModalLabel{{ $month->id }}">
                        <i class="fas fa-key me-2"></i> تفعيل الكورس: {{ $month->name }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 30px;">
                    <form method="POST" action="{{ route('activation.activate') }}" id="activationForm{{ $month->id }}">
                        @csrf
                        <input type="hidden" name="month_id" value="{{ $month->id }}">
                        
                        <div class="mb-3">
                            <label for="activationCode{{ $month->id }}" class="form-label">
                                <i class="fas fa-key me-2"></i> أدخل كود التفعيل
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   name="code" 
                                   id="activationCode{{ $month->id }}" 
                                   placeholder="أدخل كود التفعيل" 
                                   style="text-align: center; font-size: 18px; font-weight: bold; letter-spacing: 3px; text-transform: uppercase;"
                                   required>
                            <small class="text-muted">سيتم تحويل الكود تلقائياً إلى أحرف كبيرة</small>
                        </div>
                        
                        <div id="activationMessage{{ $month->id }}" class="mt-2" style="display: none;"></div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-lg" id="activateBtn{{ $month->id }}" style="background: linear-gradient(135deg, #4caf50 0%, #2e7d32 100%); color: white; border-radius: 10px; font-weight: bold;">
                                <i class="fas fa-check me-2"></i> تفعيل الكورس
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i> إلغاء
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Activation Code Form Handling for {{ $month->id }}
        (function() {
            const form = document.getElementById('activationForm{{ $month->id }}');
            const codeInput = document.getElementById('activationCode{{ $month->id }}');
            const activateBtn = document.getElementById('activateBtn{{ $month->id }}');
            const messageDiv = document.getElementById('activationMessage{{ $month->id }}');
            
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
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
                            month_id: {{ $month->id }}
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
                            activateBtn.innerHTML = '<i class="fas fa-check me-2"></i> تفعيل الكورس';
                        }
                    })
                    .catch(error => {
                        messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i> حدث خطأ أثناء التحقق من الكود</div>';
                        messageDiv.style.display = 'block';
                        activateBtn.disabled = false;
                        activateBtn.innerHTML = '<i class="fas fa-check me-2"></i> تفعيل الكورس';
                    });
                });
                
                // Auto uppercase code input
                codeInput.addEventListener('input', function(e) {
                    this.value = this.value.toUpperCase();
                });
            }
        })();
    </script>
    @endif
@endforeach

@endsection
