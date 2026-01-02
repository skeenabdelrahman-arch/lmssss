@extends('front.layouts.app')
@section('title')
الامتحانات | {{ site_name() }}
@endsection

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
        --primary-light: #b05ee7;
    }

    .exams-section {
        padding: 120px 0 80px;
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.03), rgba(250, 137, 107, 0.03));
        min-height: calc(100vh - 90px);
    }

    .page-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 15px;
    }

    .filter-section {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    .filter-select {
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 12px 20px;
        font-size: 16px;
        color: var(--primary-color);
        transition: all 0.3s ease;
        width: 100%;
    }

    .filter-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(116, 36, 169, 0.1);
        outline: none;
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

    .exam-card[data-status="taken"] {
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.05), rgba(250, 137, 107, 0.05));
    }

    .exam-card[data-status="new"] {
        background: white;
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
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .exam-badge.new {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
    }

    .exam-badge.taken {
        background: #28a745;
        color: white;
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
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        display: block;
        text-align: center;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .exam-btn.start {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
    }

    .exam-btn.start:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(116, 36, 169, 0.3);
        color: white;
    }

    .exam-btn.review {
        background: var(--primary-color);
        color: white;
    }

    .exam-btn.review:hover {
        background: var(--secondary-color);
        color: white;
        transform: translateY(-2px);
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

<section class="exams-section">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-clipboard-check me-2"></i>الامتحانات</h1>
        </div>

        <div class="filter-section">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <select id="examFilter" class="filter-select">
                        <option value="all">جميع الامتحانات</option>
                        <option value="new">الامتحانات الجديدة</option>
                        <option value="taken">الامتحانات التي تم حلها</option>
                    </select>
                </div>
            </div>
        </div>

        @if($exams->isEmpty())
            <div class="empty-state">
                <i class="fas fa-file-alt"></i>
                <h3>لا توجد امتحانات حالياً</h3>
                <p>نقوم بتحديث قائمتنا باستمرار. يرجى التحقق لاحقاً.</p>
                <a href="{{url('/')}}" class="btn-home">
                    <i class="fas fa-home me-2"></i>العودة إلى الرئيسية
                </a>
            </div>
        @else
            <div class="exams-grid">
                @foreach($exams as $index => $exam)
                    @php
                        $student_id = Auth::guard('student')->user()->id;
                        $result = App\Models\ExamResult::where('student_id',$student_id)->where('exam_id',$exam->id)->first();
                        $is_taken = !is_null($result);
                    @endphp

                    <div class="exam-card" data-status="{{$is_taken ? 'taken' : 'new'}}">
                        <span class="exam-counter">#{{ $index + 1 }}</span>
                        
                        @if($exam->isUpcoming())
                            <span class="exam-badge" style="background: linear-gradient(135deg, #6c757d, #495057);">
                                قريباً
                            </span>
                        @elseif($exam->isClosed())
                            <span class="exam-badge" style="background: linear-gradient(135deg, #dc3545, #c82333);">
                                مغلق
                            </span>
                        @else
                            <span class="exam-badge {{$is_taken ? 'taken' : 'new'}}">
                                {{$is_taken ? 'تم الحل' : 'جديد'}}
                            </span>
                        @endif

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
                            
                            @if($exam->opens_at || $exam->closes_at)
                                <div class="exam-info-item" style="grid-column: 1 / -1; margin-top: 10px; border-top: 1px solid #e9ecef; padding-top: 10px;">
                                    @if($exam->isUpcoming())
                                        <i class="fas fa-calendar-alt" style="color: #6c757d;"></i>
                                        <span style="color: #6c757d;">سوف يتم فتح الامتحان الساعة {{ $exam->opens_at->format('h:i A') }} - {{ $exam->opens_at->format('Y/m/d') }}</span>
                                    @elseif($exam->isClosed())
                                        <i class="fas fa-lock" style="color: #dc3545;"></i>
                                        <span style="color: #dc3545;">تم غلق الامتحان الساعة {{ $exam->closes_at->format('h:i A') }} - {{ $exam->closes_at->format('Y/m/d') }}</span>
                                    @elseif($exam->closes_at)
                                        <i class="fas fa-clock" style="color: #ffc107;"></i>
                                        <span style="color: #ffc107;">آخر موعد للامتحان الساعة {{ $exam->closes_at->format('h:i A') }} - {{ $exam->closes_at->format('Y/m/d') }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="mt-3">
                            @if($exam->isUpcoming())
                                <button class="exam-btn" style="background: #6c757d; cursor: not-allowed;" disabled>
                                    <i class="fas fa-hourglass-start me-2"></i>لم يفتح بعد
                                </button>
                            @elseif($exam->isClosed())
                                @if($is_taken && $result->show_degree)
                                    <a href="{{route('exam_review',['exam_id'=>$exam->id])}}" class="exam-btn review">
                                        <i class="fas fa-eye me-2"></i>عرض النتيجة
                                    </a>
                                @else
                                    <button class="exam-btn" style="background: #dc3545; cursor: not-allowed;" disabled>
                                        <i class="fas fa-lock me-2"></i>مغلق
                                    </button>
                                @endif
                            @elseif($is_taken)
                                @if($result->show_degree)
                                    <a href="{{route('exam_review',['exam_id'=>$exam->id])}}" class="exam-btn review">
                                        <i class="fas fa-eye me-2"></i>عرض النتيجة
                                    </a>
                                @endif
                            @else
                                <a href="{{route('exam_questions',['exam_id'=>$exam->id])}}" class="exam-btn start">
                                    <i class="fas fa-play me-2"></i>ابدأ الامتحان
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<script>
    document.getElementById('examFilter').addEventListener('change', function () {
        const value = this.value;
        const cards = document.querySelectorAll('.exam-card');

        cards.forEach(card => {
            const status = card.getAttribute('data-status');
            if (value === 'all' || value === status) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>

@endsection
