@extends('front.layouts.app')
@section('title', 'الامتحانات العامة | {{ site_name() }}')

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
        --primary-light: #b05ee7;
    }

    .public-exams-section {
        padding: 120px 0 80px;
        background: linear-gradient(135deg, {{ hexToRgba(primary_color(), 0.03) }}, {{ hexToRgba(secondary_color(), 0.03) }});
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

    .page-header p {
        color: #6c757d;
        font-size: 1.1rem;
    }

    .exams-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 30px;
    }

    .exam-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .exam-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 50px rgba(116, 36, 169, 0.2);
        border-color: var(--primary-color);
    }

    .exam-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        padding: 8px 15px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .exam-counter {
        position: absolute;
        top: 15px;
        left: 15px;
        background: var(--primary-color);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .exam-icon {
        text-align: center;
        margin: 20px 0;
    }

    .exam-icon i {
        font-size: 50px;
        color: var(--primary-color);
    }

    .exam-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 15px;
        text-align: center;
    }

    .exam-info {
        display: flex;
        justify-content: space-around;
        margin: 20px 0;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 10px;
    }

    .exam-info-item {
        text-align: center;
    }

    .exam-info-item i {
        color: var(--secondary-color);
        margin-bottom: 5px;
    }

    .exam-info-item span {
        display: block;
        color: var(--primary-color);
        font-weight: 600;
        font-size: 0.9rem;
    }

    .exam-btn {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        display: block;
        text-align: center;
        transition: all 0.3s ease;
    }

    .exam-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(116, 36, 169, 0.3);
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    .empty-state i {
        font-size: 100px;
        color: var(--primary-light);
        margin-bottom: 30px;
    }

    .empty-state h3 {
        color: var(--primary-color);
        margin-bottom: 15px;
        font-size: 1.8rem;
    }

    .empty-state p {
        color: #6c757d;
        font-size: 1.1rem;
        margin-bottom: 30px;
    }

    .btn-home {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 12px 30px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .btn-home:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(116, 36, 169, 0.3);
        color: white;
    }

    @media (max-width: 768px) {
        .exams-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .page-header h1 {
            font-size: 2rem;
        }
    }
</style>

<section class="public-exams-section">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-gift me-2"></i>الامتحانات العامة</h1>
            <p>امتحانات مجانية متاحة للجميع - اختبر نفسك الآن</p>
        </div>

        @forelse($exams as $exam)
            <div class="exams-grid">
                <div class="exam-card">
                    <span class="exam-counter">#{{ $loop->iteration }}</span>
                    <span class="exam-badge">جديد</span>

                    <div class="exam-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>

                    <h5 class="exam-title">{{$exam->exam_title}}</h5>

                    <div class="exam-info">
                        <div class="exam-info-item">
                            <i class="fas fa-clock"></i>
                            <span>{{$exam->exam_time}} دقيقة</span>
                        </div>
                        <div class="exam-info-item">
                            <i class="fas fa-question-circle"></i>
                            <span>{{$exam->questions->count()}} سؤال</span>
                        </div>
                    </div>

                    <a href="{{ route('publicExam.take', $exam->id) }}" class="exam-btn">
                        <i class="fas fa-play me-2"></i>ابدأ الامتحان
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-file-alt"></i>
                <h3>لا توجد امتحانات عامة حالياً</h3>
                <p>نقوم بتحديث قائمتنا باستمرار. يرجى التحقق لاحقاً.</p>
                <a href="{{url('/')}}" class="btn-home">
                    <i class="fas fa-home me-2"></i>العودة إلى الرئيسية
                </a>
            </div>
        @endforelse
    </div>
</section>

@endsection
