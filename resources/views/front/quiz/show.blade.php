@extends('front.layouts.app')
@section('title')
    {{ $quiz->title }} | {{ site_name() }}
@endsection

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
    }

    .quiz-section {
        padding: 120px 0 80px;
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.03), rgba(250, 137, 107, 0.03));
        min-height: calc(100vh - 90px);
    }

    .quiz-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .quiz-header {
        background: white;
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    .quiz-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 10px;
    }

    .quiz-header p {
        color: #6c757d;
        margin-bottom: 20px;
    }

    .quiz-info {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 15px;
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.1), rgba(250, 137, 107, 0.1));
        border-radius: 10px;
        font-size: 0.9rem;
        color: var(--primary-color);
        font-weight: 600;
    }

    .timer-display {
        position: sticky;
        top: 80px;
        background: linear-gradient(135deg, #7424a9, #fa896b);
        color: white;
        padding: 15px 25px;
        border-radius: 15px;
        text-align: center;
        margin-bottom: 20px;
        z-index: 100;
        box-shadow: 0 5px 20px rgba(116, 36, 169, 0.3);
    }

    .timer-display .time {
        font-size: 1.8rem;
        font-weight: 700;
    }

    .question-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 25px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    .question-number {
        display: inline-block;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .question-text {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 20px;
    }

    .options-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .option-item {
        margin-bottom: 15px;
    }

    .option-label {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .option-label:hover {
        background: rgba(116, 36, 169, 0.05);
        border-color: var(--primary-color);
    }

    .option-label input[type="radio"],
    .option-label input[type="checkbox"] {
        margin-left: 15px;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .option-text {
        flex: 1;
        font-size: 1rem;
        color: #495057;
    }

    .text-answer {
        width: 100%;
        padding: 15px;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }

    .text-answer:focus {
        outline: none;
        border-color: var(--primary-color);
    }

    .quiz-actions {
        position: sticky;
        bottom: 0;
        background: white;
        padding: 20px;
        border-radius: 20px 20px 0 0;
        box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.1);
        margin-top: 30px;
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 15px 40px;
        border: none;
        border-radius: 25px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(116, 36, 169, 0.3);
    }

    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .start-quiz-container {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    .start-quiz-container h2 {
        color: var(--primary-color);
        margin-bottom: 20px;
    }

    .btn-start {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 15px 50px;
        border: none;
        border-radius: 25px;
        font-size: 1.2rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-start:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(116, 36, 169, 0.3);
        color: white;
    }
</style>

<section class="quiz-section">
    <div class="container">
        <div class="quiz-container">
            @if(!$attempt || $attempt->completed_at)
                <!-- شاشة البدء -->
                <div class="start-quiz-container">
                    <h2><i class="fas fa-question-circle me-2"></i>{{ $quiz->title }}</h2>
                    @if($quiz->description)
                        <p class="text-muted mb-4">{{ $quiz->description }}</p>
                    @endif
                    <div class="quiz-info justify-content-center mb-4">
                        <div class="info-item">
                            <i class="fas fa-list-ol"></i>
                            <span>{{ $quiz->questions->count() }} سؤال</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-star"></i>
                            <span>نسبة النجاح: {{ $quiz->passing_score }}%</span>
                        </div>
                        @if($quiz->time_limit)
                            <div class="info-item">
                                <i class="fas fa-clock"></i>
                                <span>{{ $quiz->time_limit }} دقيقة</span>
                            </div>
                        @endif
                    </div>
                    @if($attempt && $attempt->completed_at)
                        <a href="{{ route('quiz.result', ['quizId' => $quiz->id]) }}" class="btn-start">
                            <i class="fas fa-chart-line me-2"></i> عرض النتيجة
                        </a>
                    @else
                        <form action="{{ route('quiz.start', ['quizId' => $quiz->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-start">
                                <i class="fas fa-play me-2"></i> ابدأ الكويز
                            </button>
                        </form>
                    @endif
                </div>
            @else
                <!-- شاشة حل الكويز -->
                <form id="quizForm">
                    @csrf
                    
                    @if($quiz->time_limit)
                        <div class="timer-display" id="timerDisplay">
                            <div class="time" id="timer">{{ $quiz->time_limit }}:00</div>
                            <small>الوقت المتبقي</small>
                        </div>
                    @endif

                    <div class="quiz-header">
                        <h1><i class="fas fa-question-circle me-2"></i>{{ $quiz->title }}</h1>
                        @if($quiz->description)
                            <p>{{ $quiz->description }}</p>
                        @endif
                    </div>

                    @foreach($quiz->questions as $index => $question)
                        <div class="question-card">
                            <span class="question-number">سؤال {{ $index + 1 }} من {{ $quiz->questions->count() }}</span>
                            <div class="question-text">{{ $question->question }}</div>
                            
                            @if($question->type === 'multiple_choice')
                                <ul class="options-list">
                                    @foreach($question->options as $option)
                                        <li class="option-item">
                                            <label class="option-label">
                                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" required>
                                                <span class="option-text">{{ $option }}</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            @elseif($question->type === 'true_false')
                                <ul class="options-list">
                                    <li class="option-item">
                                        <label class="option-label">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="صحيح" required>
                                            <span class="option-text">صحيح</span>
                                        </label>
                                    </li>
                                    <li class="option-item">
                                        <label class="option-label">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="خطأ" required>
                                            <span class="option-text">خطأ</span>
                                        </label>
                                    </li>
                                </ul>
                            @else
                                <textarea name="answers[{{ $question->id }}]" class="text-answer" rows="4" required placeholder="اكتب إجابتك هنا..."></textarea>
                            @endif
                        </div>
                    @endforeach

                    <div class="quiz-actions">
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <i class="fas fa-paper-plane me-2"></i> إرسال الإجابات
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</section>

<script>
@if($attempt && !$attempt->completed_at && $quiz->time_limit)
    let timeLimit = {{ $quiz->time_limit }}; // بالدقائق
    let startedAt = new Date('{{ $attempt->started_at }}');
    let elapsedMinutes = Math.floor((new Date() - startedAt) / (1000 * 60));
    let remainingMinutes = timeLimit - elapsedMinutes;
    let remainingSeconds = 0;
    
    if (remainingMinutes <= 0) {
        remainingMinutes = 0;
        remainingSeconds = 0;
        // انتهى الوقت، إرسال الكويز تلقائياً
        submitQuiz();
    } else {
        let totalSeconds = remainingMinutes * 60;
        
        function updateTimer() {
            let minutes = Math.floor(totalSeconds / 60);
            let seconds = totalSeconds % 60;
            
            document.getElementById('timer').textContent = 
                String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            
            if (totalSeconds <= 0) {
                submitQuiz();
                return;
            }
            
            totalSeconds--;
            setTimeout(updateTimer, 1000);
        }
        
        updateTimer();
    }
@endif

document.getElementById('quizForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    submitQuiz();
});

function submitQuiz() {
    const form = document.getElementById('quizForm');
    const formData = new FormData(form);
    const submitBtn = document.getElementById('submitBtn');
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري الإرسال...';
    
    fetch('{{ route("quiz.submit", ["quizId" => $quiz->id]) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("quiz.result", ["quizId" => $quiz->id]) }}';
        } else {
            alert(data.message || 'حدث خطأ أثناء إرسال الإجابات');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> إرسال الإجابات';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء إرسال الإجابات');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> إرسال الإجابات';
    });
}
</script>
@endsection



