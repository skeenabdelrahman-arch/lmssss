@extends('front.layouts.app')
@section('title')
{{ site_name() }} | الصفحة الرئيسية
@endsection

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
        --primary-light: {{ primary_color() }};
    }

    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.05), rgba(250, 137, 107, 0.05));
        padding: 100px 0 80px;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(116, 36, 169, 0.1), transparent);
        border-radius: 50%;
    }

    .hero-content {
        position: relative;
        z-index: 1;
        width: 100%;
    }

    .hero-content h1 {
        font-size: 3.5rem;
        font-weight: 900;
        color: var(--primary-color);
        margin-bottom: 20px;
        line-height: 1.2;
    }

    .hero-content .quote {
        font-size: 1.3rem;
        color: var(--secondary-color);
        font-weight: 600;
        margin-bottom: 15px;
        font-style: italic;
    }

    .hero-content .subtitle {
        font-size: 1.5rem;
        color: var(--primary-light);
        margin-bottom: 30px;
    }

    .hero-buttons {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        margin-top: 40px;
    }

    .btn-hero {
        padding: 15px 35px;
        border-radius: 30px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-hero.primary {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        box-shadow: 0 5px 20px rgba(116, 36, 169, 0.3);
    }

    .btn-hero.primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(116, 36, 169, 0.4);
    }

    .btn-hero.outline {
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        background: transparent;
    }

    .btn-hero.outline:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-3px);
    }

    /* Statistics Section */
    .hero-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 25px;
        margin-top: 50px;
    }

    .stat-item {
        background: white;
        border-radius: 15px;
        padding: 25px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 5px 20px rgba(116, 36, 169, 0.1);
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .stat-item:hover {
        transform: translateY(-5px);
        border-color: var(--primary-color);
        box-shadow: 0 10px 30px rgba(116, 36, 169, 0.2);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        flex-shrink: 0;
    }

    .stat-content {
        flex: 1;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 900;
        color: var(--primary-color);
        margin: 0;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #6c757d;
        margin: 5px 0 0 0;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .hero-stats {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 40px;
        }

        .stat-item {
            padding: 20px 15px;
            flex-direction: column;
            text-align: center;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }

        .stat-number {
            font-size: 1.5rem;
        }

        /* جعل الصورة تظهر أولاً على الموبايل فقط */
        .hero-section .row {
            display: flex;
            flex-direction: column;
        }

        .hero-section .col-lg-6:first-child {
            order: 2; /* النص يظهر ثانياً */
        }

        .hero-section .col-lg-6:last-child {
            order: 1; /* الصورة تظهر أولاً */
            margin-top: 0;
        }

        .hero-image {
            margin-top: 0;
            margin-bottom: 30px;
            min-height: auto;
            padding: 0;
        }

        /* تحسين شكل الصورة على الموبايل */
        .hero-image img,
        .hero-image-img,
        .hero-image .hero-image-img {
            border-radius: 0 !important;
            box-shadow: none !important;
            background: transparent !important;
            padding: 0 !important;
            width: 100% !important;
            max-height: 450px !important; /* مساحة أكبر لتجنب الاقتصاص العمودي */
            height: auto !important;
            object-fit: contain !important; /* عرض الصورة كاملة بدون قص */
            margin: 0 !important;
            display: block !important;
        }
    }

    .hero-image {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 400px;
        flex-direction: column;
        width: 100%;
        overflow: hidden;
    }
    
    /* محاذاة الصورة مع النص */
    .hero-section .row {
        align-items: center;
        display: flex;
    }
    
    /* على اللابتوب: الصورة على الشمال والكلام على اليمين */
    @media (min-width: 992px) {
        .hero-section .col-lg-6:first-child {
            order: 2 !important; /* الكلام على اليمين */
        }
        
        .hero-section .col-lg-6:last-child {
            order: 1 !important; /* الصورة على الشمال */
        }
    }
    
    /* Animation فقط إذا لم تكن هناك صورة */
    .hero-image:not(:has(img)) {
        animation: float 6s ease-in-out infinite;
    }

    .hero-image img,
    .hero-image-img {
        max-width: 100%;
        width: auto;
        height: auto;
        max-height: 500px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(116, 36, 169, 0.2);
        object-fit: contain;
        background: white;
        padding: 15px;
        margin: 0 auto;
        display: block;
        z-index: 2;
        position: relative;
    }
    
    /* التأكد من أن الأعمدة منفصلة */
    .hero-section .row {
        display: flex;
        flex-wrap: wrap;
    }
    
    .hero-section .col-lg-6 {
        position: relative;
    }
    
    .hero-section .col-lg-6:first-child {
        padding-right: 15px;
    }
    
    .hero-section .col-lg-6:last-child {
        padding-left: 15px;
    }
    
    /* إخفاء Animation إذا كانت الصورة موجودة */
    .hero-image:has(img) .atom-container,
    .hero-image:has(img) .physics-icons-container,
    .hero-image:has(img) .wave-container {
        display: none !important;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }

    /* Biology Icons Section */
    .physics-icons-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        padding: 30px;
        position: relative;
    }

    .physics-icon-item {
        text-align: center;
        padding: 20px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(116, 36, 169, 0.1);
        transition: all 0.3s ease;
        position: relative;
    }

    .physics-icon-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 30px rgba(116, 36, 169, 0.2);
    }

    .physics-icon-item i {
        font-size: 3rem;
        color: var(--primary-color);
        margin-bottom: 10px;
        display: block;
        animation: pulse 2s ease-in-out infinite;
    }

    .physics-icon-item:nth-child(1) i { animation-delay: 0s; }
    .physics-icon-item:nth-child(2) i { animation-delay: 0.3s; }
    .physics-icon-item:nth-child(3) i { animation-delay: 0.6s; }
    .physics-icon-item:nth-child(4) i { animation-delay: 0.9s; }

    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.8; }
    }

    .physics-icon-item .icon-label {
        font-size: 0.9rem;
        color: var(--primary-color);
        font-weight: 600;
        margin-top: 10px;
    }

    /* Cell Animation (Biology) */
    .atom-container {
        position: relative;
        width: 200px;
        height: 200px;
        margin: 0 auto;
    }

    .atom-nucleus {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 30px;
        height: 30px;
        background: var(--primary-color);
        border-radius: 50%;
        box-shadow: 0 0 20px rgba(116, 36, 169, 0.5);
        z-index: 3;
    }

    .atom-orbit {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        border: 2px solid rgba(116, 36, 169, 0.3);
        border-radius: 50%;
    }

    .atom-orbit-1 {
        width: 100px;
        height: 100px;
        animation: rotate 8s linear infinite;
    }

    .atom-orbit-2 {
        width: 140px;
        height: 140px;
        animation: rotate 10s linear infinite reverse;
    }

    .atom-orbit-3 {
        width: 180px;
        height: 180px;
        animation: rotate 12s linear infinite;
    }

    .atom-electron {
        position: absolute;
        width: 12px;
        height: 12px;
        background: var(--secondary-color);
        border-radius: 50%;
        box-shadow: 0 0 10px rgba(250, 137, 107, 0.8);
    }

    .atom-orbit-1 .atom-electron {
        top: -6px;
        left: 50%;
        transform: translateX(-50%);
    }

    .atom-orbit-2 .atom-electron {
        top: 50%;
        right: -6px;
        transform: translateY(-50%);
    }

    .atom-orbit-3 .atom-electron {
        bottom: -6px;
        left: 50%;
        transform: translateX(-50%);
    }

    @keyframes rotate {
        from { transform: translate(-50%, -50%) rotate(0deg); }
        to { transform: translate(-50%, -50%) rotate(360deg); }
    }

    /* Wave Animation */
    .wave-container {
        position: relative;
        width: 100%;
        height: 150px;
        overflow: hidden;
        margin: 20px 0;
    }

    .wave {
        position: absolute;
        width: 200%;
        height: 100%;
        background: linear-gradient(90deg, 
            transparent 0%, 
            rgba(116, 36, 169, 0.3) 25%, 
            rgba(116, 36, 169, 0.5) 50%, 
            rgba(116, 36, 169, 0.3) 75%, 
            transparent 100%);
        border-radius: 50%;
        animation: waveMove 3s ease-in-out infinite;
    }

    .wave-1 {
        top: 0;
        animation-delay: 0s;
    }

    .wave-2 {
        top: 30px;
        animation-delay: 0.5s;
    }

    .wave-3 {
        top: 60px;
        animation-delay: 1s;
    }

    @keyframes waveMove {
        0%, 100% { transform: translateX(-50%) translateY(0); }
        50% { transform: translateX(-50%) translateY(-10px); }
    }

    /* Biology Concepts Grid - صف واحد */
    .physics-concepts {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        gap: 15px;
        margin-top: 30px;
    }

    .concept-item {
        text-align: center;
        padding: 15px;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 12px;
        transition: all 0.3s ease;
        flex: 0 0 auto;
        min-width: 100px;
    }

    .concept-item:hover {
        background: white;
        transform: scale(1.05);
    }

    .concept-item i {
        font-size: 2rem;
        color: var(--primary-color);
        margin-bottom: 8px;
    }

    .concept-item span {
        display: block;
        font-size: 0.85rem;
        color: var(--text-dark);
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .physics-icons-container {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            padding: 20px;
        }

        .atom-container {
            width: 150px;
            height: 150px;
        }

        .physics-concepts {
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .concept-item {
            min-width: 80px;
            padding: 10px;
        }
    }

    /* Features Section */
    .features-section {
        padding: 80px 0;
        background: white;
    }

    .section-title {
        text-align: center;
        margin-bottom: 60px;
    }

    .section-title h2 {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 15px;
    }

    .section-title p {
        font-size: 1.1rem;
        color: #6c757d;
        max-width: 600px;
        margin: 0 auto;
    }

    .feature-card {
        background: white;
        border-radius: 20px;
        padding: 40px 30px;
        text-align: center;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        height: 100%;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }

    .feature-card:hover {
        transform: translateY(-10px);
        border-color: var(--primary-color);
        box-shadow: 0 10px 40px rgba(116, 36, 169, 0.15);
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 25px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 35px;
        color: white;
        transition: all 0.3s ease;
    }

    .feature-card:hover .feature-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .feature-card h4 {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 15px;
    }

    .feature-card p {
        color: #6c757d;
        line-height: 1.8;
    }

    /* Grades Section */
    .grades-section {
        padding: 80px 0;
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.03), rgba(250, 137, 107, 0.03));
    }

    .grade-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        text-decoration: none;
        display: block;
        height: 100%;
    }

    .grade-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 50px rgba(116, 36, 169, 0.2);
        text-decoration: none;
    }

    .grade-image {
        height: 200px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 80px;
        color: white;
    }

    .grade-content {
        padding: 30px;
        text-align: center;
    }

    .grade-content h4 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin: 0;
    }

    /* Public Exams Section */
    .public-exams-section {
        padding: 80px 0;
        background: white;
    }

    .exam-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        transition: all 0.3s ease;
        border: 2px solid #f0f0f0;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .exam-card:hover {
        border-color: var(--primary-color);
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(116, 36, 169, 0.15);
    }

    .exam-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        margin-bottom: 20px;
    }

    .exam-card h5 {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 15px;
        min-height: 60px;
    }

    .exam-badge {
        display: inline-block;
        padding: 8px 15px;
        background: rgba(116, 36, 169, 0.1);
        color: var(--primary-color);
        border-radius: 20px;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }

    .exam-btn {
        margin-top: auto;
        padding: 12px 25px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border: none;
        border-radius: 25px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .exam-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(116, 36, 169, 0.3);
        color: white;
    }

    /* Featured Lectures Section */
    .featured-lectures-section {
        padding: 80px 0;
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.03), rgba(250, 137, 107, 0.03));
    }

    .lecture-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .lecture-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 50px rgba(116, 36, 169, 0.2);
    }

    .lecture-image {
        width: 100%;
        height: 200px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .lecture-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .lecture-image::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.7), rgba(250, 137, 107, 0.7));
    }

    .lecture-play-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 70px;
        height: 70px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
        transition: all 0.3s ease;
    }

    .lecture-play-icon i {
        font-size: 30px;
        color: var(--primary-color);
        margin-right: 3px;
    }

    .lecture-card:hover .lecture-play-icon {
        transform: translate(-50%, -50%) scale(1.1);
        background: white;
    }

    .lecture-content {
        padding: 25px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .lecture-content h5 {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 10px;
        min-height: 60px;
    }

    .lecture-content p {
        color: #6c757d;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 15px;
        flex: 1;
    }

    .lecture-meta {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }

    .lecture-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.85rem;
        color: #6c757d;
    }

    .lecture-meta i {
        color: var(--primary-color);
    }

    .lecture-badge {
        display: inline-block;
        padding: 5px 12px;
        background: rgba(116, 36, 169, 0.1);
        color: var(--primary-color);
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .lecture-btn {
        margin-top: auto;
        padding: 12px 25px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border: none;
        border-radius: 25px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        text-align: center;
        transition: all 0.3s ease;
    }

    .lecture-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(116, 36, 169, 0.3);
        color: white;
    }

    /* Why Subscribe Section */
    .why-subscribe-section {
        padding: 100px 0;
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.03), rgba(250, 137, 107, 0.03));
        position: relative;
        overflow: hidden;
    }

    .why-subscribe-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="2" fill="rgba(116,36,169,0.05)"/></svg>');
        opacity: 0.3;
    }

    .why-subscribe-title {
        text-align: center;
        margin-bottom: 60px;
        position: relative;
        z-index: 1;
    }

    .why-subscribe-title h2 {
        font-size: 2.5rem;
        font-weight: 900;
        color: var(--primary-color);
        margin-bottom: 15px;
        position: relative;
        display: inline-block;
    }

    .why-subscribe-title h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        right: 50%;
        transform: translateX(50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 2px;
    }

    .why-subscribe-title p {
        font-size: 1.2rem;
        color: #6c757d;
        margin-top: 20px;
    }

    .benefit-card {
        background: white;
        border-radius: 20px;
        padding: 35px 25px;
        text-align: center;
        transition: all 0.4s ease;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        height: 100%;
        position: relative;
        overflow: hidden;
        border: 2px solid transparent;
    }

    .benefit-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        transform: scaleX(0);
        transition: transform 0.4s ease;
    }

    .benefit-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(116, 36, 169, 0.2);
        border-color: var(--primary-color);
    }

    .benefit-card:hover::before {
        transform: scaleX(1);
    }

    .benefit-icon {
        width: 90px;
        height: 90px;
        margin: 0 auto 25px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        color: white;
        position: relative;
        transition: all 0.4s ease;
    }

    .benefit-card:hover .benefit-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .benefit-icon::after {
        content: '';
        position: absolute;
        inset: -5px;
        border-radius: 50%;
        background: inherit;
        opacity: 0.2;
        z-index: -1;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 0.2;
        }
        50% {
            transform: scale(1.2);
            opacity: 0;
        }
    }

    .benefit-icon.icon-1 {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .benefit-icon.icon-2 {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .benefit-icon.icon-3 {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .benefit-icon.icon-4 {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    .benefit-icon.icon-5 {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .benefit-icon.icon-6 {
        background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
    }

    .benefit-icon.icon-7 {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    }

    .benefit-icon.icon-8 {
        background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
    }

    .benefit-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 15px;
        line-height: 1.4;
    }

    .benefit-description {
        font-size: 1rem;
        color: #6c757d;
        line-height: 1.7;
    }

    @media (max-width: 768px) {
        .hero-content h1 {
            font-size: 2.5rem;
        }

        .hero-content .quote {
            font-size: 1.1rem;
        }

        .section-title h2 {
            font-size: 2rem;
        }
    }
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <p class="quote">{{ hero_quote() ?: '" وما رميت إذ رميت ولكن الله رمى "' }}</p>
                    <h1>{{ hero_title() ?: (subject_name() . ' مع ' . teacher_name()) }}</h1>
                    <p class="subtitle">{{ hero_subtitle() ?: ('خبير تدريس مادة ' . subject_name()) }}</p>

                    {{-- نص إضافي في الـ Hero (دائمًا يظهر، مع افتراضي) --}}
                    @php
                        $extraText = trim((string) hero_additional_text());
                    @endphp
                    <p style="font-size: 1.2rem; color: #555; margin-top: 15px; white-space: pre-line; font-weight: 500;">
                        {{ $extraText !== '' ? $extraText : 'أهلاً بك في منصتنا – كل الشرح والامتحانات في مكان واحد' }}
                    </p>
                    
                    {{-- أزرار Call-to-Action في الـ Hero --}}
                    @php
                        $cta1Link = trim((string) cta_link_1());
                        $cta1Text = cta_text_1() ?: 'جروب التيلجرام';
                        $cta2Link = trim((string) cta_link_2());
                        $cta2Text = cta_text_2() ?: 'تعرف على المزيد';
                        $cta3Link = trim((string) cta_link_3());
                        $cta3Text = cta_text_3() ?: 'تواصل معنا';

                        // قيم افتراضية لضمان الظهور حتى لو الإعدادات فاضية
                        if ($cta1Link === '') {
                            $cta1Link = 'https://t.me/samehsalah2026';
                        }
                    @endphp

                    <div class="hero-buttons">
                        <a href="{{ $cta1Link }}" class="btn-hero primary" target="_blank" rel="noopener">
                            <i class="fas fa-arrow-right me-1"></i> {{ $cta1Text }}
                        </a>

                        {{-- زر بوابة أولياء الأمور --}}
                        <a href="{{ url('parent-portal') }}" class="btn-hero outline" target="_blank" rel="noopener" style="border-color: #667eea; color: #667eea;">
                            <i class="fas fa-user-shield me-1"></i> بوابة أولياء الأمور
                        </a>

                        @if($cta2Link !== '')
                        <a href="{{ $cta2Link }}" class="btn-hero outline" target="_blank" rel="noopener">
                            <i class="fas fa-link me-1"></i> {{ $cta2Text }}
                        </a>
                        @endif
                        @if($cta3Link !== '')
                        <a href="{{ $cta3Link }}" class="btn-hero outline" target="_blank" rel="noopener">
                            <i class="fas fa-link me-1"></i> {{ $cta3Text }}
                        </a>
                        @endif
                    </div>
                    
                    <!-- Statistics Section -->
                    <div class="hero-stats">
                        @foreach(hero_stats() as $stat)
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas {{ $stat['icon'] ?? 'fa-check' }}"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-number" data-target="{{ $stat['number'] ?? '0' }}">0</h3>
                                <p class="stat-label">{{ $stat['label'] ?? '' }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image text-center">
                    @php
                        $heroImgPath = setting('hero_image', '');
                        $heroImg = '';
                        if (!empty($heroImgPath)) {
                            // استخراج اسم الملف فقط
                            $fileName = basename($heroImgPath);
                            // استخدام upload_files كما في باقي الملفات
                            $heroImg = url('upload_files/' . $fileName);
                        }
                    @endphp
                    @if(!empty($heroImg))
                    <img src="{{ $heroImg }}" alt="{{ hero_title() }}" class="img-fluid hero-image-img">
                    @else
                    <!-- Cell Animation (Biology) -->
                    <div class="atom-container">
                        <div class="atom-nucleus" style="background: linear-gradient(135deg, #28a745, #20c997);">
                            <i class="fas fa-dna" style="color: white; font-size: 20px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"></i>
                        </div>
                        <div class="atom-orbit atom-orbit-1" style="border-color: rgba(40, 167, 69, 0.3);">
                            <div class="atom-electron" style="background: #28a745; box-shadow: 0 0 10px rgba(40, 167, 69, 0.8);">
                                <i class="fas fa-microscope" style="color: white; font-size: 8px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"></i>
                            </div>
                        </div>
                        <div class="atom-orbit atom-orbit-2" style="border-color: rgba(32, 201, 151, 0.3);">
                            <div class="atom-electron" style="background: #20c997; box-shadow: 0 0 10px rgba(32, 201, 151, 0.8);">
                                <i class="fas fa-leaf" style="color: white; font-size: 8px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"></i>
                            </div>
                        </div>
                        <div class="atom-orbit atom-orbit-3" style="border-color: rgba(40, 167, 69, 0.3);">
                            <div class="atom-electron" style="background: #28a745; box-shadow: 0 0 10px rgba(40, 167, 69, 0.8);">
                                <i class="fas fa-heartbeat" style="color: white; font-size: 8px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Biology Icons Grid -->
                    <div class="physics-icons-container">
                        <div class="physics-icon-item">
                            <i class="fas fa-microscope"></i>
                            <div class="icon-label">الخلية</div>
                        </div>
                        <div class="physics-icon-item">
                            <i class="fas fa-dna"></i>
                            <div class="icon-label">الوراثة</div>
                        </div>
                        <div class="physics-icon-item">
                            <i class="fas fa-lungs"></i>
                            <div class="icon-label">التنفس</div>
                        </div>
                        <div class="physics-icon-item">
                            <i class="fas fa-heartbeat"></i>
                            <div class="icon-label">الجهاز الدوري</div>
                        </div>
                    </div>

                    <!-- Wave Animation -->
                    <div class="wave-container">
                        <div class="wave wave-1"></div>
                        <div class="wave wave-2"></div>
                        <div class="wave wave-3"></div>
                    </div>
                    @endif

                    <!-- Subject Concepts -->
                    @php
                        $concepts = subject_concepts();
                    @endphp
                    @if(count($concepts) > 0)
                    <div class="physics-concepts">
                        @foreach($concepts as $concept)
                        <div class="concept-item">
                            <i class="fas {{ $concept['icon'] ?? 'fa-circle' }}"></i>
                            <span>{{ $concept['name'] ?? '' }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Animate Statistics Numbers
    function animateValue(element, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const isDecimal = end % 1 !== 0;
            if (isDecimal) {
                element.textContent = (progress * (end - start) + start).toFixed(1);
            } else {
                element.textContent = Math.floor(progress * (end - start) + start);
            }
            if (progress < 1) {
                window.requestAnimationFrame(step);
            } else {
                if (isDecimal) {
                    element.textContent = end.toFixed(1);
                } else {
                    element.textContent = end;
                }
            }
        };
        window.requestAnimationFrame(step);
    }

    // Intersection Observer for Statistics
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const statNumbers = entry.target.querySelectorAll('.stat-number');
                statNumbers.forEach(stat => {
                    const target = parseFloat(stat.getAttribute('data-target'));
                    if (!stat.classList.contains('animated')) {
                        stat.classList.add('animated');
                        animateValue(stat, 0, target, 2000);
                    }
                });
            }
        });
    }, observerOptions);

    // Observe statistics section when page loads
    document.addEventListener('DOMContentLoaded', function() {
        const statsSection = document.querySelector('.hero-stats');
        if (statsSection) {
            observer.observe(statsSection);
        }
    });
</script>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <div class="section-title">
            <h2>ماذا تقدم منصتنا؟</h2>
            <p>نوفر لك كل ما تحتاجه لتحقيق التفوق في مادة {{ subject_name() }}</p>
        </div>
        <div class="row g-4">
            @foreach(features_list() as $feature)
            <div class="col-lg-3 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas {{ $feature['icon'] ?? 'fa-check' }}"></i>
                    </div>
                    <h4>{{ $feature['title'] ?? '' }}</h4>
                    <p>{{ $feature['description'] ?? '' }}</p>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Biology Topics Section -->
        @if(count(topics_list()) > 0)
        <div class="row g-4 mt-4">
            @foreach(topics_list() as $topic)
            <div class="col-lg-2 col-md-4 col-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas {{ $topic['icon'] ?? 'fa-atom' }}"></i>
                    </div>
                    <h4>{{ $topic['name'] ?? '' }}</h4>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

<!-- Grades Section -->
<section class="grades-section">
    <div class="container">
        <div class="section-title">
            <h2>السنوات الدراسية</h2>
            <p>اختر صفك الدراسي وابدأ رحلتك التعليمية</p>
        </div>
        <div class="row g-4">
            @foreach(grades_list() as $grade)
            <div class="col-lg-4 col-md-6">
                <a href="{{route('studentLogin')}}" class="grade-card">
                    <div class="grade-image">
                        <i class="fas {{ $grade['icon'] ?? 'fa-graduation-cap' }}"></i>
                    </div>
                    <div class="grade-content">
                        <h4>{{ $grade['name'] ?? '' }}</h4>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Why Subscribe Section -->
<section class="why-subscribe-section">
    <div class="container">
        <div class="why-subscribe-title">
            <h2>ليه تشترك معانا؟</h2>
            <p>اكتشف المميزات التي تجعل منصتنا الخيار الأمثل لتعلم الأحياء</p>
        </div>

        <div class="row g-4">
            <!-- Benefit 1 -->
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card">
                    <div class="benefit-icon icon-1">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h4 class="benefit-title">شرح بسيط ومفهوم</h4>
                    <p class="benefit-description">
                        محتوى تعليمي واضح وسهل الفهم، مصمم خصيصاً لتبسيط المفاهيم المعقدة في الأحياء
                    </p>
                </div>
            </div>

            <!-- Benefit 2 -->
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card">
                    <div class="benefit-icon icon-2">
                        <i class="fas fa-video"></i>
                    </div>
                    <h4 class="benefit-title">فيديوهات برسومات توضيحية</h4>
                    <p class="benefit-description">
                        محاضرات فيديو عالية الجودة مع رسومات وتوضيحات تفاعلية تجعل التعلم أكثر متعة وفعالية
                    </p>
                </div>
            </div>

            <!-- Benefit 3 -->
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card">
                    <div class="benefit-icon icon-3">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h4 class="benefit-title">تمارين تفاعلية على الدروس</h4>
                    <p class="benefit-description">
                        تدريبات وتمارين تفاعلية بعد كل درس لتعزيز الفهم وتطبيق ما تم تعلمه
                    </p>
                </div>
            </div>

            <!-- Benefit 4 -->
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card">
                    <div class="benefit-icon icon-4">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h4 class="benefit-title">مرونة كاملة في المذاكرة</h4>
                    <p class="benefit-description">
                        ادرس في أي وقت ومن أي مكان، المحتوى متاح 24/7 لتناسب جدولك الدراسي
                    </p>
                </div>
            </div>

            <!-- Benefit 5 -->
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card">
                    <div class="benefit-icon icon-5">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h4 class="benefit-title">اختبارات مستمرة</h4>
                    <p class="benefit-description">
                        امتحانات دورية لتقييم مستواك ومتابعة تقدمك الدراسي بشكل مستمر
                    </p>
                </div>
            </div>

            <!-- Benefit 6 -->
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card">
                    <div class="benefit-icon icon-6">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <h4 class="benefit-title">محتوى متكامل ومنظم</h4>
                    <p class="benefit-description">
                        مناهج منظمة بشكل منطقي ومتسلسل تغطي جميع أجزاء المادة بشكل شامل
                    </p>
                </div>
            </div>

            <!-- Benefit 7 -->
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card">
                    <div class="benefit-icon icon-7">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <h4 class="benefit-title">تحديث مستمر حسب المنهج</h4>
                    <p class="benefit-description">
                        محتوى محدث باستمرار ليتوافق مع آخر التعديلات في المنهج الدراسي
                    </p>
                </div>
            </div>

            <!-- Benefit 8 -->
            <div class="col-lg-3 col-md-6">
                <div class="benefit-card">
                    <div class="benefit-icon icon-8">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4 class="benefit-title">مجتمع طلابي ضخم</h4>
                    <p class="benefit-description">
                        انضم إلى آلاف الطلاب الناجحين وشارك في مناقشات وتبادل الخبرات
                    </p>
                </div>
            </div>
        </div>

        <!-- CTA Button -->
        <div class="text-center mt-5">
            @auth('student')
                <a href="{{ route('courses.index') }}" class="btn-hero primary">
                    <i class="fas fa-graduation-cap me-2"></i> ابدأ التعلم الآن
                </a>
            @else
                <a href="{{ route('studentSignup') }}" class="btn-hero primary">
                    <i class="fas fa-user-plus me-2"></i> سجل الآن مجاناً
                </a>
            @endauth
        </div>
    </div>
</section>

<!-- Public Exams Section -->
@php
    $publicExams = \App\Models\ExamName::where('public_access', 1)
        ->where('status', 1)
        ->orderBy('created_at', 'desc')
        ->take(6)
        ->get();
@endphp

@if($publicExams->count() > 0)
<section class="public-exams-section">
    <div class="container">
        <div class="section-title">
            <h2><i class="fas fa-gift me-2" style="color: var(--secondary-color);"></i>الامتحانات المجانية</h2>
            <p>اختبر نفسك مع امتحانات مجانية متاحة للجميع</p>
        </div>
        <div class="row g-4">
            @foreach($publicExams as $exam)
            <div class="col-lg-4 col-md-6">
                <div class="exam-card">
                    <div class="exam-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h5>{{ $exam->exam_title }}</h5>
                    @if($exam->exam_description)
                    <p style="color: #6c757d; font-size: 0.95rem; margin-bottom: 20px;">
                        {{ Str::limit($exam->exam_description, 80) }}
                    </p>
                    @endif
                    <span class="exam-badge">
                        <i class="fas fa-clock me-1"></i> {{ $exam->exam_time }} دقيقة
                    </span>
                    <a href="{{ route('publicExam.take', $exam->id) }}" class="exam-btn">
                        <i class="fas fa-play me-2"></i> ابدأ الامتحان
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @if($publicExams->count() >= 6)
        <div class="text-center mt-5">
            <a href="{{ route('publicExam.index') }}" class="btn-hero primary">
                <i class="fas fa-list me-2"></i> عرض جميع الامتحانات المجانية
            </a>
        </div>
        @endif
    </div>
</section>
@endif

<!-- Featured Lectures Section -->
@if(isset($featuredLectures) && $featuredLectures->count() > 0)
<section class="featured-lectures-section">
    <div class="container">
        <div class="section-title">
            <h2><i class="fas fa-star me-2" style="color: var(--secondary-color);"></i>المحاضرات المميزة</h2>
            <p>محاضرات موصى بها من قبل الأستاذ - ابدأ رحلتك التعليمية من هنا</p>
        </div>
        <div class="row g-4">
            @foreach($featuredLectures as $lecture)
            <div class="col-lg-4 col-md-6">
                <div class="lecture-card">
                    <div class="lecture-image">
                        @php
                            $lectureImageUrl = $lecture->getImageUrl();
                        @endphp
                        @if($lectureImageUrl)
                            <img src="{{ $lectureImageUrl }}" 
                                 alt="{{ $lecture->title }}"
                                 loading="lazy"
                                 style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; z-index: 1;"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="lecture-image-fallback" style="display: none; align-items: center; justify-content: center; width: 100%; height: 100%; background: linear-gradient(135deg, #2c5f2d, #4a8f4e); position: absolute; top: 0; left: 0; z-index: 1; flex-direction: column; gap: 15px;">
                                <div style="display: flex; gap: 20px; align-items: center; justify-content: center; flex-wrap: wrap;">
                                    <i class="fas fa-dna" style="font-size: 40px; color: rgba(255, 255, 255, 0.9);"></i>
                                    <i class="fas fa-microscope" style="font-size: 40px; color: rgba(255, 255, 255, 0.9);"></i>
                                    <i class="fas fa-flask" style="font-size: 40px; color: rgba(255, 255, 255, 0.9);"></i>
                                </div>
                                <div style="font-size: 14px; color: rgba(255, 255, 255, 0.8); font-weight: 500; text-align: center; padding: 0 20px;">
                                    <i class="fas fa-leaf"></i> علم الأحياء
                                </div>
                            </div>
                        @else
                            <div class="lecture-image-fallback" style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; background: linear-gradient(135deg, #2c5f2d, #4a8f4e); position: absolute; top: 0; left: 0; z-index: 1; flex-direction: column; gap: 15px;">
                                <div style="display: flex; gap: 20px; align-items: center; justify-content: center; flex-wrap: wrap;">
                                    <i class="fas fa-dna" style="font-size: 40px; color: rgba(255, 255, 255, 0.9);"></i>
                                    <i class="fas fa-microscope" style="font-size: 40px; color: rgba(255, 255, 255, 0.9);"></i>
                                    <i class="fas fa-flask" style="font-size: 40px; color: rgba(255, 255, 255, 0.9);"></i>
                                </div>
                                <div style="font-size: 14px; color: rgba(255, 255, 255, 0.8); font-weight: 500; text-align: center; padding: 0 20px;">
                                    <i class="fas fa-leaf"></i> علم الأحياء
                                </div>
                            </div>
                        @endif
                        <div class="lecture-play-icon">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                    <div class="lecture-content">
                        <span class="lecture-badge">
                            <i class="fas fa-star me-1"></i> محاضرة مميزة
                        </span>
                        <h5>{{ $lecture->title }}</h5>
                        @if($lecture->description)
                        <p>{{ Str::limit($lecture->description, 100) }}</p>
                        @endif
                        <div class="lecture-meta">
                            <span>
                                <i class="fas fa-graduation-cap"></i>
                                {{ $lecture->grade }}
                            </span>
                            @if($lecture->month)
                            <span>
                                <i class="fas fa-calendar"></i>
                                {{ $lecture->month->name }}
                            </span>
                            @endif
                            <span>
                                <i class="fas fa-eye"></i>
                                {{ $lecture->views ?? 0 }} مشاهدة
                            </span>
                        </div>
                        @auth('student')
                            @php
                                $subscription = Auth::guard('student')->user()
                                    ->subscriptions()
                                    ->where('month_id', $lecture->month_id)
                                    ->first();
                                    
                                $hasSubscription = $subscription && $subscription->is_active == 1;
                                $deactivationReason = $subscription && $subscription->is_active == 0 ? $subscription->deactivation_reason : null;
                            @endphp
                            
                            @if($hasSubscription)
                                <a href="{{ route('lecture', ['lecture_id' => $lecture->id]) }}" class="lecture-btn">
                                    <i class="fas fa-play me-2"></i> شاهد المحاضرة
                                </a>
                            @else
                                @if($deactivationReason)
                                    <button class="lecture-btn" disabled style="opacity: 0.6; cursor: not-allowed;" title="{{ $deactivationReason }}">
                                        <i class="fas fa-lock me-2"></i> {{ $deactivationReason }}
                                    </button>
                                @else
                                    <button class="lecture-btn" disabled style="opacity: 0.6; cursor: not-allowed;" title="يجب تفعيل الاشتراك في هذا الكورس أولاً">
                                        <i class="fas fa-lock me-2"></i> غير متاح - فعّل الاشتراك
                                    </button>
                                @endif
                            @endif
                        @else
                            <a href="{{ route('studentLogin') }}" class="lecture-btn">
                                <i class="fas fa-sign-in-alt me-2"></i> سجل الدخول للمشاهدة
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection

