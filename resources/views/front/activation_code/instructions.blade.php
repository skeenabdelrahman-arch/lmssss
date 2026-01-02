@extends('front.layouts.app')

@section('title', 'تعليمات التفعيل')

@section('content')
<div class="page-header" style="background: linear-gradient(135deg, {{ primary_color() }}, {{ secondary_color() }}); padding: 60px 0; text-align: center; color: white; margin-top: 90px;">
    <div class="container">
        <h1 style="font-size: 2.5rem; margin-bottom: 10px;">📋 تعليمات تفعيل الكود</h1>
        <p style="font-size: 1.1rem; opacity: 0.9;">اتبع الخطوات التالية لتفعيل كود الاشتراك</p>
    </div>
</div>

<div class="container" style="max-width: 800px; margin: 50px auto; padding: 20px;">
    <div class="modern-card" style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 40px;">
            @if(file_exists(public_path(logo_path())))
                <img src="{{ asset(logo_path()) }}" alt="Logo" style="max-width: 120px; margin-bottom: 20px;">
            @endif
            <h2 style="color: {{ primary_color() }}; font-size: 2rem;">{{ site_name() }}</h2>
            <p style="color: #666; font-size: 1.1rem; margin-top: 10px;">{{ teacher_full_name() ?: teacher_name() }}</p>
        </div>

        <div style="margin-bottom: 40px;">
            <h3 style="color: {{ primary_color() }}; font-size: 1.5rem; margin-bottom: 25px; text-align: center;">
                <i class="fas fa-list-ol me-2"></i>خطوات التفعيل
            </h3>

            <div class="steps">
                <!-- Step 1 -->
                <div class="step-item" style="display: flex; align-items: start; margin-bottom: 25px; padding: 20px; background: #f8f9ff; border-radius: 12px; border-right: 4px solid {{ primary_color() }};">
                    <div class="step-number" style="background: {{ primary_color() }}; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.2rem; margin-left: 15px; flex-shrink: 0;">1</div>
                    <div>
                        <h4 style="color: #333; font-size: 1.2rem; margin-bottom: 8px;">تسجيل الدخول</h4>
                        <p style="color: #666; margin: 0;">
                            قم بتسجيل الدخول إلى حسابك على المنصة. إذا لم يكن لديك حساب، 
                            <a href="{{ route('studentSignup') }}" style="color: {{ primary_color() }}; font-weight: 600;">سجل الآن</a>
                        </p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="step-item" style="display: flex; align-items: start; margin-bottom: 25px; padding: 20px; background: #f8f9ff; border-radius: 12px; border-right: 4px solid {{ secondary_color() }};">
                    <div class="step-number" style="background: {{ secondary_color() }}; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.2rem; margin-left: 15px; flex-shrink: 0;">2</div>
                    <div>
                        <h4 style="color: #333; font-size: 1.2rem; margin-bottom: 8px;">انتقل إلى صفحة التفعيل</h4>
                        <p style="color: #666; margin: 0;">
                            اذهب إلى صفحة "تفعيل الكود" من القائمة الرئيسية أو 
                            <a href="{{ route('activation_code.index') }}" style="color: {{ secondary_color() }}; font-weight: 600;">اضغط هنا</a>
                        </p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="step-item" style="display: flex; align-items: start; margin-bottom: 25px; padding: 20px; background: #f8f9ff; border-radius: 12px; border-right: 4px solid #28a745;">
                    <div class="step-number" style="background: #28a745; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.2rem; margin-left: 15px; flex-shrink: 0;">3</div>
                    <div>
                        <h4 style="color: #333; font-size: 1.2rem; margin-bottom: 8px;">أدخل الكود</h4>
                        <p style="color: #666; margin: 0;">
                            أدخل كود التفعيل الموجود في البطاقة بالضبط كما هو مكتوب (12 حرف/رقم)
                        </p>
                        <div style="margin-top: 10px; padding: 10px; background: white; border-radius: 8px; border: 2px dashed {{ primary_color() }}; text-align: center; font-family: 'Courier New', monospace; font-size: 1.1rem; color: {{ primary_color() }}; letter-spacing: 2px;">
                            XXXXXXXXXXXX
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="step-item" style="display: flex; align-items: start; margin-bottom: 25px; padding: 20px; background: #f8f9ff; border-radius: 12px; border-right: 4px solid #17a2b8;">
                    <div class="step-number" style="background: #17a2b8; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.2rem; margin-left: 15px; flex-shrink: 0;">4</div>
                    <div>
                        <h4 style="color: #333; font-size: 1.2rem; margin-bottom: 8px;">ابدأ المشاهدة</h4>
                        <p style="color: #666; margin: 0;">
                            بعد التفعيل الناجح، سيتم فتح الكورس تلقائياً ويمكنك البدء في مشاهدة الدروس مباشرة
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access Button -->
        <div style="text-align: center; margin: 40px 0;">
            <a href="{{ route('activation_code.index') }}" class="btn btn-lg" style="background: linear-gradient(135deg, {{ primary_color() }}, {{ secondary_color() }}); color: white; border: none; padding: 15px 50px; border-radius: 25px; font-size: 1.2rem; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.3s;">
                <i class="fas fa-key me-2"></i>فعّل كودك الآن
            </a>
        </div>

        <!-- Tips Section -->
        <div style="margin-top: 40px; padding: 25px; background: #fff3cd; border-radius: 12px; border-left: 4px solid #ffc107;">
            <h4 style="color: #856404; font-size: 1.1rem; margin-bottom: 15px;">
                <i class="fas fa-lightbulb me-2"></i>نصائح مهمة
            </h4>
            </h4>
            <ul style="color: #856404; line-height: 2; margin: 0;">
                <li>تأكد من إدخال الكود بشكل صحيح (حروف إنجليزية كبيرة وأرقام)</li>
                <li>كل كود يمكن استخدامه مرة واحدة فقط</li>
                <li>تحقق من تاريخ انتهاء صلاحية الكود إذا كان موجوداً</li>
                <li>احتفظ بالبطاقة في مكان آمن</li>
            </ul>
        </div>

        <!-- Contact Section -->
        @if(whatsapp_number())
        <div style="margin-top: 30px; text-align: center; padding: 25px; background: #e8f5e9; border-radius: 12px;">
            <h4 style="color: #2e7d32; margin-bottom: 15px;">
                <i class="fas fa-headset me-2"></i>هل تحتاج إلى مساعدة؟
            </h4>
            <p style="color: #2e7d32; margin-bottom: 15px;">فريق الدعم جاهز لمساعدتك في أي وقت</p>
            <a href="https://wa.me/{{ str_replace(['+', ' ', '-'], '', whatsapp_number()) }}" target="_blank" class="btn btn-success" style="background: #25D366; border: none; padding: 12px 35px; border-radius: 25px; font-weight: 600;">
                <i class="fab fa-whatsapp me-2"></i>تواصل معنا على الواتساب
            </a>
        </div>
        @endif
    </div>
</div>

<style>
    .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .step-item:hover {
        transform: translateX(-5px);
        transition: all 0.3s ease;
    }
</style>
@endsection
