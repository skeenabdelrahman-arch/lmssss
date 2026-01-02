@component('mail::message')

<div style="direction: rtl; text-align: right; font-family: 'Tajawal', 'Arial', sans-serif;">

<!-- Logo Section -->
<div style="text-align: center; margin-bottom: 30px; padding: 20px 0; border-bottom: 2px solid #e0e0e0;">
    <img src="{{ url(logo_path()) }}" alt="{{ site_name() }}" style="max-width: 200px; height: auto; margin: 0 auto; display: block;">
</div>

<!-- Header -->
<div style="text-align: right; margin-bottom: 25px;">
    <h1 style="color: {{ primary_color() }}; font-size: 28px; font-weight: 700; margin: 0 0 15px 0; text-align: right;">
        🔐 استعادة كلمة المرور
    </h1>
    <p style="font-size: 18px; color: #333; margin: 0; text-align: right;">
        مرحباً <strong>{{ $user->first_name }} {{ $user->second_name }}</strong> 👋
    </p>
</div>

<!-- Main Content -->
<div style="background: #f8f9fa; padding: 25px; border-radius: 10px; margin: 25px 0; text-align: right; direction: rtl;">
    <p style="font-size: 16px; color: #555; line-height: 1.8; margin: 0 0 20px 0; text-align: right;">
        نحن هنا لمساعدتك! لقد تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك في <strong style="color: {{ primary_color() }};">{{ site_name() }}</strong>.
    </p>
</div>

<!-- Steps Section -->
<div style="margin: 30px 0; text-align: right; direction: rtl;">
    <h2 style="color: {{ primary_color() }}; font-size: 22px; font-weight: 600; margin: 0 0 15px 0; text-align: right;">
        📝 خطوات استعادة كلمة المرور:
    </h2>
    <ol style="padding-right: 20px; margin: 0; text-align: right; direction: rtl;">
        <li style="margin-bottom: 10px; font-size: 16px; color: #555; line-height: 1.8;">
            اضغط على الزر أدناه لإعادة تعيين كلمة المرور
        </li>
        <li style="margin-bottom: 10px; font-size: 16px; color: #555; line-height: 1.8;">
            سيتم توجيهك إلى صفحة آمنة لإدخال كلمة مرور جديدة
        </li>
        <li style="margin-bottom: 10px; font-size: 16px; color: #555; line-height: 1.8;">
            تأكد من اختيار كلمة مرور قوية وسهلة التذكر
        </li>
    </ol>
</div>

<!-- Button -->
<div style="text-align: center; margin: 35px 0;">
    @component('mail::button', ['url' => url('reset/'.$user->remember_token), 'color' => 'primary'])
        🔑 إعادة تعيين كلمة المرور
    @endcomponent
</div>

<!-- Important Notes -->
<div style="background: #fff3cd; border-right: 4px solid #ffc107; padding: 20px; border-radius: 8px; margin: 30px 0; text-align: right; direction: rtl;">
    <h3 style="color: #856404; font-size: 18px; font-weight: 600; margin: 0 0 15px 0; text-align: right;">
        ⚠️ ملاحظات مهمة:
    </h3>
    <ul style="padding-right: 20px; margin: 0; text-align: right; direction: rtl;">
        <li style="margin-bottom: 8px; font-size: 15px; color: #856404; line-height: 1.7;">
            <strong>الرابط صالح لمدة 60 دقيقة فقط</strong> - يرجى استخدامه في أقرب وقت ممكن
        </li>
        <li style="margin-bottom: 8px; font-size: 15px; color: #856404; line-height: 1.7;">
            إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذا البريد بأمان
        </li>
        <li style="margin-bottom: 8px; font-size: 15px; color: #856404; line-height: 1.7;">
            <strong>لا تشارك هذا الرابط مع أي شخص</strong> - إنه خاص بحسابك فقط
        </li>
    </ul>
</div>

<!-- Contact Section -->
<div style="background: #e7f3ff; border-right: 4px solid {{ primary_color() }}; padding: 20px; border-radius: 8px; margin: 30px 0; text-align: right; direction: rtl;">
    <h3 style="color: {{ primary_color() }}; font-size: 18px; font-weight: 600; margin: 0 0 15px 0; text-align: right;">
        🆘 تحتاج مساعدة؟
    </h3>
    <p style="font-size: 15px; color: #555; margin: 0 0 10px 0; text-align: right;">
        إذا واجهت أي مشاكل أو لديك استفسارات، لا تتردد في التواصل معنا:
    </p>
    <ul style="padding-right: 20px; margin: 0; text-align: right; direction: rtl;">
        @if(whatsapp_number())
        <li style="margin-bottom: 8px; font-size: 15px; color: #555; line-height: 1.7;">
            <strong>واتساب:</strong> {{ whatsapp_number() }}
        </li>
        @endif
        @if(phone_number())
        <li style="margin-bottom: 8px; font-size: 15px; color: #555; line-height: 1.7;">
            <strong>هاتف:</strong> {{ phone_number() }}
        </li>
        @endif
        @if(contact_email())
        <li style="margin-bottom: 8px; font-size: 15px; color: #555; line-height: 1.7;">
            <strong>بريد إلكتروني:</strong> {{ contact_email() }}
        </li>
        @endif
    </ul>
</div>

<!-- Footer -->
<div style="text-align: center; margin: 40px 0 20px 0; padding-top: 30px; border-top: 2px solid #e0e0e0;">
    <p style="font-size: 16px; color: #555; margin: 0 0 10px 0; text-align: center;">
        🙏 شكراً لثقتك بنا
    </p>
    <p style="font-size: 15px; color: #777; margin: 0 0 15px 0; text-align: center; line-height: 1.8;">
        نتمنى لك تجربة تعليمية ممتعة مع <strong style="color: {{ primary_color() }};">{{ teacher_name() }}</strong> في مادة <strong style="color: {{ primary_color() }};">{{ subject_name() }}</strong>
    </p>
    <p style="font-size: 16px; color: {{ primary_color() }}; font-weight: 600; margin: 0 0 5px 0; text-align: center;">
        {{ site_name() }}
    </p>
    <p style="font-size: 14px; color: #999; margin: 0; text-align: center; font-style: italic;">
        {{ teacher_name() }}
    </p>
</div>

<!-- Alternative Link -->
<div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 30px; text-align: right; direction: rtl;">
    <p style="font-size: 13px; color: #6c757d; margin: 0 0 10px 0; text-align: right;">
        <strong>إذا لم يعمل الزر أعلاه،</strong> يمكنك نسخ الرابط التالي ولصقه في المتصفح:
    </p>
    <p style="font-size: 12px; color: {{ primary_color() }}; word-break: break-all; margin: 0; text-align: right; direction: ltr; text-align: left;">
        {{ url('reset/'.$user->remember_token) }}
    </p>
</div>

</div>

@endcomponent
