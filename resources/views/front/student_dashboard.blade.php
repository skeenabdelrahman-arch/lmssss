@extends('front.layouts.app')

@section('title', 'لوحة التحكم - التعليمية')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- خط الرقعة -->
<link href="https://fonts.googleapis.com/css2?family=Aref+Ruqaa:wght@400;700&display=swap" rel="stylesheet">

<style>
    :root {
        --primary: {{ primary_color() }};
        --secondary: {{ secondary_color() }};
        --bg-light: #f8fafe;
        --card-shadow: 0 10px 25px rgba(0,0,0,0.05);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        background-color: var(--bg-light);
    }

    .dashboard-wrapper {
        padding: 40px 0;
        direction: rtl;
    }

    /* Hero Banner */
    .hero-banner {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 24px;
        padding: 50px;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 40px;
        box-shadow: 0 15px 35px rgba(116, 36, 169, 0.2);
    }

    .hero-banner::after {
        content: '';
        position: absolute;
        top: -50px;
        left: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.12);
        border-radius: 50%;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    /* Welcome Animation */
    .welcome-animate h2,
    .welcome-animate p {
        opacity: 0;
        transform: translateY(25px);
        animation: fadeUp 0.8s ease-out forwards;
    }

    .welcome-animate h2 {
        animation-delay: 0.2s;
        font-size: 2.2rem;
        font-weight: 800;
    }

    .welcome-animate p:first-of-type {
        animation-delay: 0.5s;
        font-size: 1.2rem;
        margin-top: 10px;
    }

    .welcome-animate p:last-of-type {
        animation-delay: 0.8s;
        font-size: 1rem;
        opacity: 0.85;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(25px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* اسم الطالب بخط الرقعة */
    .student-name {
        font-family: 'Aref Ruqaa', serif;
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
    }

    /* Stats Cards */
    .stat-box {
        background: white;
        border-radius: 20px;
        padding: 25px;
        display: flex;
        align-items: center;
        gap: 20px;
        transition: var(--transition);
        border: 1px solid rgba(0,0,0,0.03);
        box-shadow: var(--card-shadow);
        height: 100%;
    }

    .stat-box:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .stat-icon {
        width: 65px;
        height: 65px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
    }

    .icon-1 { background: rgba(116, 36, 169, 0.1); color: var(--primary); }
    .icon-2 { background: rgba(250, 137, 107, 0.1); color: var(--secondary); }
    .icon-3 { background: rgba(40, 167, 69, 0.1); color: #28a745; }
    .icon-4 { background: rgba(255, 193, 7, 0.1); color: #ffc107; }

    .stat-details h3 {
        font-size: 1.8rem;
        font-weight: 800;
        margin: 0;
        color: #2d3436;
    }

    .stat-details p {
        color: #636e72;
        margin: 0;
        font-size: 0.9rem;
        font-weight: 600;
    }

    /* Modern Course Card */
    .modern-course-card {
        background: white;
        border-radius: 20px;
        padding: 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        transition: var(--transition);
        border: 1px solid transparent;
        box-shadow: var(--card-shadow);
    }

    .modern-course-card:hover {
        border-color: var(--primary);
        background: #fff;
    }

    .course-img-placeholder {
        width: 100px;
        height: 80px;
        border-radius: 12px;
        background: #f0f2f5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: var(--primary);
        margin-left: 20px;
    }

    .progress-container { flex-grow: 1; }

    .custom-progress {
        height: 8px;
        background-color: #eee;
        border-radius: 10px;
        margin-top: 10px;
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary), var(--secondary));
        border-radius: 10px;
    }

    /* Sidebar/Quick Actions */
    .action-btn {
        background: white;
        border-radius: 15px;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        text-decoration: none;
        color: #2d3436;
        margin-bottom: 12px;
        transition: var(--transition);
        font-weight: 600;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }

    .action-btn i { color: var(--primary); font-size: 1.2rem; }
    .action-btn:hover { background: var(--primary); color: white; }
    .action-btn:hover i { color: white; }

    .section-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .section-head h4 {
        font-weight: 800;
        color: #2d3436;
        position: relative;
        padding-right: 15px;
    }

    .section-head h4::before {
        content: '';
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 20px;
        background: var(--primary);
        border-radius: 10px;
    }

    @media (max-width: 768px) {
        .hero-banner { padding: 30px; }
        .welcome-animate h2 { font-size: 1.5rem; }
        .student-name { font-size: 1.2rem; }
    }
    /* Emoji small bounce */
.emoji {
    display: inline-block;
    animation: bounce 1.2s ease infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-8px);
    }
    60% {
        transform: translateY(-4px);
    }
}

</style>

<div class="dashboard-wrapper">
    <div class="container">
<div class="hero-banner">
    <div class="hero-content">
        @php
            $hour = now()->hour;
            if ($hour < 12) {
                $greeting = 'صباح الفل';
                $emoji = '☀️';
            } elseif ($hour < 18) {
                $greeting = 'نهارك الفل';
                $emoji = '🌤️';
            } else {
                $greeting = 'مساء الفل';
                $emoji = '🌙';
            }
        @endphp

        <div class="welcome-animate">
            <h2>
                {{ $greeting }} 
                <span class="emoji">{{ $emoji }}</span>
            </h2>
            <p>نورتنا يا <span class="student-name">{{ Auth::guard('student')->user()->first_name }}</span> 👋</p>
            <p>يلا شوف اللي وراك ومتكسلش 💪</p>
        </div>
    </div>
</div>

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="stat-icon icon-1"><i class="fas fa-graduation-cap"></i></div>
                    <div class="stat-details">
                        <h3>{{ $subscribedCoursesCount }}</h3>
                        <p>دورة مسجلة</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="stat-icon icon-2"><i class="fas fa-file-signature"></i></div>
                    <div class="stat-details">
                        <h3>{{ $examsTakenCount }}</h3>
                        <p>اختبار مكتمل</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="stat-icon icon-3"><i class="fas fa-play-circle"></i></div>
                    <div class="stat-details">
                        <h3>{{ $lecturesViewedCount }}</h3>
                        <p>درس شاهدته</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <div class="stat-icon icon-4"><i class="fas fa-trophy"></i></div>
                    <div class="stat-details">
                        <h3>{{ number_format($averageGrade, 1) }}%</h3>
                        <p>معدل الدرجات</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="section-head">
                    <h4>متابعة التعلم</h4>
                    <a href="{{ route('courses.index') }}" class="btn btn-sm text-primary fw-bold">عرض الكل</a>
                </div>

                @if($subscribedCourses->count() > 0)
                    @foreach($subscribedCourses as $course)
                        <a href="{{ route('course_details', ['id' => $course->id]) }}" class="text-decoration-none">
                            <div class="modern-course-card">
                                <div class="course-img-placeholder">
                                    <i class="fas fa-book-reader"></i>
                                </div>
                                <div class="progress-container">
                                    <div class="d-flex justify-content-between mb-1">
                                        <h5 class="mb-0 fw-bold" style="color:#2d3436">{{ $course->name }}</h5>
                                        <span class="text-muted small">{{ $course->grade ?? 'المستوى العام' }}</span>
                                    </div>
                                    <div class="custom-progress">
                                        <div class="progress-bar-fill" style="width: 65%"></div>
                                    </div>
                                    <div class="mt-2 small text-muted">
                                        <i class="far fa-clock me-1"></i> آخر نشاط: منذ يومين
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <i class="fas fa-chevron-left text-muted"></i>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @else
                    <div class="text-center p-5 bg-white rounded-4 shadow-sm">
                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="120" class="mb-3" alt="">
                        <h5>لا توجد كورسات نشطة حالياً</h5>
                        <p class="text-muted">ابدأ باكتشاف أفضل الكورسات وابدأ رحلة نجاحك</p>
                        <a href="{{ route('courses.index') }}" class="btn btn-primary px-4 rounded-pill">تصفح الدورات الآن</a>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="section-head">
                    <h4>وصول سريع</h4>
                </div>
                
                <div class="quick-actions-sidebar">
                    <a href="{{ route('student_profile', ['student_id' => Auth::guard('student')->user()->id]) }}" class="action-btn">
                        <i class="fas fa-user-edit"></i> تعديل البروفايل
                    </a>
                    <a href="{{ route('publicExam.index') }}" class="action-btn">
                        <i class="fas fa-laptop-code"></i> بنك الأسئلة المجاني
                    </a>
                    @if(is_payment_method_enabled('activation_codes'))
                        <a href="{{ route('activation_code.index') }}" class="action-btn">
                            <i class="fas fa-ticket-alt"></i> شحن كود تفعيل
                        </a>
                    @endif
                    @if(is_payment_method_enabled('online_payment'))
                        <a href="{{ route('payment.history') }}" class="action-btn">
                            <i class="fas fa-history"></i> سجل العمليات المالي
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
