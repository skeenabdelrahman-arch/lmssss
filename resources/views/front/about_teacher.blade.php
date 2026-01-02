@extends('front.layouts.app')

@section('title', 'عن المدرس - ' . site_name())

@section('content')
<div style="background: linear-gradient(135deg, rgba(116,36,169,0.06), rgba(250,137,107,0.06)); padding: 40px 0 60px 0;">
    <div class="container" style="max-width: 1200px;">
        <style>
            .about-hero .teacher-card { display:inline-block; padding:28px; border-radius:28px; background:rgba(255,255,255,0.98); box-shadow:0 16px 40px rgba(0,0,0,0.08); }
            .about-hero .teacher-img { width: 260px; height:260px; border-radius:50%; overflow:hidden; border:8px solid var(--primary-color); transition: transform .35s ease; }
            .about-hero .teacher-img img { width:100%; height:100%; object-fit:cover; display:block; }
            .about-hero .teacher-img:hover { transform: scale(1.03); }
            .achievement-card { border-radius:12px; background:#fff; box-shadow:0 8px 30px rgba(0,0,0,0.06); transition: transform .25s ease, box-shadow .25s ease; }
            .achievement-card:hover { transform: translateY(-6px); box-shadow:0 18px 40px rgba(0,0,0,0.09); }
            .achievement-card .img-wrap { height:240px; overflow:hidden; border-radius:8px; }
            .achievement-card .caption { font-size:14px; color:var(--text-dark); margin-top:8px; font-weight:600; }
            .method-card, .review-card { border-radius:16px; background:#fff; box-shadow:0 8px 26px rgba(0,0,0,0.04); }
            .stat-box { border-radius: 12px; background: #f8f9ff; padding: 12px; }
            @media (max-width:767px) { .about-hero .teacher-img { width: 180px; height:180px; } .achievement-card .img-wrap{height:160px;} }
        </style>
        {{-- هيرو عن المدرس --}}
        <div class="row align-items-center g-4 mb-5">
            <div class="col-md-6">
                <span class="badge rounded-pill mb-3" style="background: #fff; color: var(--primary-color); padding: 7px 16px; font-size: 13px; box-shadow: 0 4px 15px rgba(0,0,0,0.04);">
                    تعرف على المدرس وأسلوبه في التدريس
                </span>
                <h1 style="font-weight: 800; font-size: 34px; color: var(--primary-color); margin-bottom: 12px;">
                    {{ teacher_full_name() ?: 'مستر حماده مراد' }}
                </h1>
                @php
                    $bio = about_teacher_bio();
                @endphp
                @if($bio)
                <p style="color: var(--text-light); font-size: 16px; line-height: 1.95; margin-bottom: 18px;">
                    {{ $bio }}
                </p>
                @else
                <p style="color: var(--text-light); font-size: 15px; line-height: 1.9; margin-bottom: 15px;">
                    مدرس {{ subject_name() }} متخصص في تدريس الثانوية العامة بخبرة تمتد لأكثر من 15 عاماً في مجال التعليم،
                    مع التركيز على جعل التعلم ممتعاً وسهلاً وتحقيق أعلى الدرجات للطلاب.
                </p>
                @endif
                @php
                    $qualifications = about_teacher_qualifications();
                @endphp
                @if(count($qualifications) > 0)
                <ul style="list-style: none; padding: 0; margin: 0 0 15px 0; font-size: 14px; color: var(--text-dark);">
                    @foreach($qualifications as $qual)
                    <li class="mb-1"><i class="fas {{ $qual['icon'] ?? 'fa-check-circle' }} ms-2" style="color: var(--secondary-color);"></i> {{ $qual['text'] }}</li>
                    @endforeach
                </ul>
                @endif
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <a href="{{ route('courses.index') }}" class="btn" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: #fff; border-radius: 30px; padding: 9px 22px; font-size: 14px; font-weight: 600; text-decoration: none;">
                        <i class="fas fa-book-open ms-2"></i> تصفح الكورسات
                    </a>
                    <a href="{{ route('studentSignup') }}" class="btn" style="background: #fff; color: var(--primary-color); border-radius: 30px; padding: 9px 22px; font-size: 14px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 15px rgba(0,0,0,0.04);">
                        <i class="fas fa-user-plus ms-2"></i> اشترك في المنصة
                    </a>
                </div>
            </div>
            <div class="col-md-6 text-center about-hero">
                <div class="teacher-card">
                    <div class="teacher-img mx-auto mb-3">
                        <img src="{{ asset(teacher_image_path()) }}" alt="{{ teacher_full_name() ?: teacher_name() }}">
                    </div>
                    @php
                        $stats = about_teacher_stats();
                    @endphp
                    @if(count($stats) > 0)
                    <div class="row g-2" style="max-width: 340px; margin: 0 auto;">
                        @foreach($stats as $stat)
                        <div class="col-6">
                            <div class="text-center stat-box">
                                <div style="font-size: 18px; font-weight: 800; color: var(--primary-color);">{{ $stat['number'] ?? '' }}</div>
                                <div style="font-size: 12px; color: var(--text-light);">{{ $stat['label'] ?? '' }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="row g-2" style="max-width: 320px; margin: 0 auto;">
                        <div class="col-6">
                            <div class="text-center stat-box">
                                <div style="font-size: 16px; font-weight: 800; color: var(--primary-color);">15+</div>
                                <div style="font-size: 11px; color: var(--text-light);">سنة خبرة</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center stat-box">
                                <div style="font-size: 16px; font-weight: 800; color: var(--primary-color);">5000+</div>
                                <div style="font-size: 11px; color: var(--text-light);">طالب مشترك</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center stat-box">
                                <div style="font-size: 16px; font-weight: 800; color: var(--primary-color);">250+</div>
                                <div style="font-size: 11px; color: var(--text-light);">فيديو تعليمي</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center stat-box">
                                <div style="font-size: 16px; font-weight: 800; color: var(--primary-color);">98%</div>
                                <div style="font-size: 11px; color: var(--text-light);">نسبة النجاح</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- إنجازات المدرس --}}
        @php
            $achievements = about_teacher_achievements();
        @endphp
        @if(count($achievements) > 0)
        <div class="mb-5">
            <div class="text-center mb-4">
                <h3 style="font-weight: 700; color: var(--primary-color); font-size: 22px;">إنجازات المدرس</h3>
                <p style="color: var(--text-light); font-size: 14px; margin: 0;">أبرز الإنجازات والجوائز مع صور توضيحية</p>
            </div>
            <div class="row g-3">
                @foreach($achievements as $ach)
                <div class="col-md-4">
                    <div class="h-100 p-3 achievement-card text-center">
                        @if(!empty($ach['image']))
                        <div class="img-wrap mb-3">
                            <img src="{{ asset($ach['image']) }}" alt="achievement" style="width:100%; height:100%; object-fit:cover; display:block;">
                        </div>
                        @endif
                        <div class="caption">{{ $ach['caption'] ?? '' }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- أسلوب التدريس --}}
        <div class="mb-5">
            <div class="text-center mb-4">
                <h3 style="font-weight: 700; color: var(--primary-color); font-size: 22px;">أسلوب التدريس</h3>
                <p style="color: var(--text-light); font-size: 14px; margin: 0;">
                    منهجية تدريس فعالة ومجربة لتحقيق أفضل النتائج في اللغة الإنجليزية
                </p>
            </div>
            @php
                $methods = about_teacher_methods();
            @endphp
            @if(count($methods) > 0)
            <div class="row g-3">
                @foreach($methods as $index => $method)
                <div class="col-md-4">
                    <div class="h-100 p-4 method-card">
                        <div class="d-flex align-items-center mb-2">
                            <span style="width: 26px; height: 26px; border-radius: 50%; background: rgba(116,36,169,0.08); display: flex; align-items: center; justify-content: center; font-size: 13px; color: var(--primary-color); margin-left: 8px;">{{ $index + 1 }}</span>
                            <h5 style="font-size: 15px; font-weight: 700; margin: 0;">{{ $method['title'] ?? '' }}</h5>
                        </div>
                        <p style="font-size: 13px; color: var(--text-light); margin: 0; line-height: 1.8;">
                            {{ $method['description'] ?? '' }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="h-100 p-4" style="border-radius: 18px; background: #ffffff; box-shadow: 0 4px 18px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.02);">
                        <div class="d-flex align-items-center mb-2">
                            <span style="width: 26px; height: 26px; border-radius: 50%; background: rgba(116,36,169,0.08); display: flex; align-items: center; justify-content: center; font-size: 13px; color: var(--primary-color); margin-left: 8px;">1</span>
                            <h5 style="font-size: 15px; font-weight: 700; margin: 0;">شرح مبسط وواضح</h5>
                        </div>
                        <p style="font-size: 13px; color: var(--text-light); margin: 0; line-height: 1.8;">
                            تبسيط القواعد والمفاهيم المعقدة مع أمثلة عملية من الامتحانات السابقة حتى تثبت المعلومة في دماغك.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="h-100 p-4" style="border-radius: 18px; background: #ffffff; box-shadow: 0 4px 18px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.02);">
                        <div class="d-flex align-items-center mb-2">
                            <span style="width: 26px; height: 26px; border-radius: 50%; background: rgba(116,36,169,0.08); display: flex; align-items: center; justify-content: center; font-size: 13px; color: var(--primary-color); margin-left: 8px;">2</span>
                            <h5 style="font-size: 15px; font-weight: 700; margin: 0;">تدريبات متدرجة</h5>
                        </div>
                        <p style="font-size: 13px; color: var(--text-light); margin: 0; line-height: 1.8;">
                            تمارين من السهل للصعب على كل درس ووحدة، عشان تبني ثقتك في حل أي شكل من أشكال الأسئلة.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="h-100 p-4" style="border-radius: 18px; background: #ffffff; box-shadow: 0 4px 18px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.02);">
                        <div class="d-flex align-items-center mb-2">
                            <span style="width: 26px; height: 26px; border-radius: 50%; background: rgba(116,36,169,0.08); display: flex; align-items: center; justify-content: center; font-size: 13px; color: var(--primary-color); margin-left: 8px;">3</span>
                            <h5 style="font-size: 15px; font-weight: 700; margin: 0;">مراجعة مستمرة</h5>
                        </div>
                        <p style="font-size: 13px; color: var(--text-light); margin: 0; line-height: 1.8;">
                            اختبارات دورية ومراجعات شاملة على المنصة لضمان ثبات المعلومات واستعداد كامل قبل الامتحان.
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- آراء الطلاب --}}
        <div class="mb-3">
            <div class="text-center mb-4">
                <h3 style="font-weight: 700; color: var(--primary-color); font-size: 22px;">آراء الطلاب</h3>
                <p style="color: var(--text-light); font-size: 14px; margin: 0;">
                    تجارب حقيقية من طلاب حققوا التفوق معنا
                </p>
            </div>
            @php
                $reviews = about_teacher_reviews();
            @endphp
            @if(count($reviews) > 0)
            <div class="row g-3">
                @foreach($reviews as $review)
                @php
                    $initial = $review['initial'] ?? mb_substr($review['student_name'], 0, 1, 'UTF-8');
                @endphp
                <div class="col-md-4">
                    <div class="h-100 p-4 review-card">
                        <p style="font-size: 13px; color: var(--text-dark); line-height: 1.9; margin: 0;">
                            {{ $review['text'] ?? '' }}
                        </p>
                        <div class="d-flex align-items-center mt-3">
                            <div style="width: 34px; height: 34px; border-radius: 50%; background: #f1f3ff; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--primary-color); margin-left: 8px;">
                                {{ $initial }}
                            </div>
                            <div>
                                <div style="font-size: 13px; font-weight: 700;">{{ $review['student_name'] ?? '' }}</div>
                                <div style="font-size: 11px; color: var(--text-light);">{{ $review['grade'] ?? '' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="h-100 p-4" style="border-radius: 18px; background: #ffffff; box-shadow: 0 4px 18px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.02);">
                        <p style="font-size: 13px; color: var(--text-dark); line-height: 1.9; margin: 0;">
                            شرح مستر {{ teacher_name() }} أكتر من رائع! فهمت {{ subject_name() }} بطريقة لم أفهمها من قبل.
                            حصلت على 49 من 50 في الامتحان النهائي.
                        </p>
                        <div class="d-flex align-items-center mt-3">
                            <div style="width: 34px; height: 34px; border-radius: 50%; background: #f1f3ff; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--primary-color); margin-left: 8px;">
                                أ
                            </div>
                            <div>
                                <div style="font-size: 13px; font-weight: 700;">أحمد محمد</div>
                                <div style="font-size: 11px; color: var(--text-light);">الصف الثالث الثانوي</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="h-100 p-4" style="border-radius: 18px; background: #ffffff; box-shadow: 0 4px 18px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.02);">
                        <p style="font-size: 13px; color: var(--text-dark); line-height: 1.9; margin: 0;">
                            أسلوب الشرح مبسط جداً والتمارين متنوعة. المنصة ساعدتني أذاكر في أي وقت وأراجع اللي محتاجاه
                            قبل الامتحان.
                        </p>
                        <div class="d-flex align-items-center mt-3">
                            <div style="width: 34px; height: 34px; border-radius: 50%; background: #f1f3ff; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--primary-color); margin-left: 8px;">
                                ن
                            </div>
                            <div>
                                <div style="font-size: 13px; font-weight: 700;">نورا أحمد</div>
                                <div style="font-size: 11px; color: var(--text-light);">الصف الثاني الثانوي</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="h-100 p-4" style="border-radius: 18px; background: #ffffff; box-shadow: 0 4px 18px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.02);">
                        <p style="font-size: 13px; color: var(--text-dark); line-height: 1.9; margin: 0;">
                            من أفضل المدرسين اللي قابلتهم. الشرح واضح والامتحانات على المنصة بتجهزك كويس جداً
                            للامتحان الفعلي.
                        </p>
                        <div class="d-flex align-items-center mt-3">
                            <div style="width: 34px; height: 34px; border-radius: 50%; background: #f1f3ff; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--primary-color); margin-left: 8px;">
                                م
                            </div>
                            <div>
                                <div style="font-size: 13px; font-weight: 700;">محمد سعيد</div>
                                <div style="font-size: 11px; color: var(--text-light);">الصف الثالث الثانوي</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection


