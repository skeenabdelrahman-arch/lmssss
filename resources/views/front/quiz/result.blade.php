@extends('front.layouts.app')
@section('title')
    نتيجة الكويز | {{ site_name() }}
@endsection

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
    }

    .result-section {
        padding: 120px 0 80px;
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.03), rgba(250, 137, 107, 0.03));
        min-height: calc(100vh - 90px);
    }

    .result-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .result-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        text-align: center;
    }

    .result-icon {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 60px;
    }

    .result-icon.passed {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .result-icon.failed {
        background: linear-gradient(135deg, #dc3545, #fd7e14);
        color: white;
    }

    .result-card h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .result-card.passed h1 {
        color: #28a745;
    }

    .result-card.failed h1 {
        color: #dc3545;
    }

    .score-display {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin: 30px 0;
        flex-wrap: wrap;
    }

    .score-item {
        text-align: center;
    }

    .score-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-color);
        display: block;
    }

    .score-label {
        color: #6c757d;
        font-size: 0.9rem;
        margin-top: 5px;
    }

    .percentage-circle {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 2rem;
        font-weight: 700;
        position: relative;
    }

    .percentage-circle.passed {
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(32, 201, 151, 0.1));
        color: #28a745;
        border: 5px solid #28a745;
    }

    .percentage-circle.failed {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(253, 126, 20, 0.1));
        color: #dc3545;
        border: 5px solid #dc3545;
    }

    .actions {
        margin-top: 40px;
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 12px 30px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(116, 36, 169, 0.3);
        color: white;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
        color: white;
    }

    .btn-warning {
        background: linear-gradient(135deg, #ffc107, #ff9800);
        color: white;
    }

    .btn-warning:hover {
        background: linear-gradient(135deg, #ffb300, #f57c00);
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(255, 152, 0, 0.3);
        color: white;
    }

    .answers-review {
        margin-top: 20px;
    }

    .question-card {
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .question-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .question-card.correct {
        border: 2px solid #28a745;
        background: rgba(40, 167, 69, 0.05);
    }

    .question-card.incorrect {
        border: 2px solid #dc3545;
        background: rgba(220, 53, 69, 0.05);
    }

    .question-header {
        display: flex;
        align-items: start;
        gap: 15px;
        margin-bottom: 15px;
    }

    .question-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
    }

    .question-icon.correct {
        background: #28a745;
    }

    .question-icon.incorrect {
        background: #dc3545;
    }

    .question-content {
        flex: 1;
    }

    .question-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }

    .question-points {
        float: left;
        font-weight: 600;
    }

    .question-points.correct {
        color: #28a745;
    }

    .question-points.incorrect {
        color: #dc3545;
    }

    .question-text {
        color: #555;
        line-height: 1.8;
        margin: 10px 0;
    }

    .answer-section {
        margin-right: 55px;
        margin-top: 15px;
    }

    .student-answer {
        margin-bottom: 10px;
        font-size: 14px;
        color: #666;
    }

    .student-answer strong {
        display: inline-block;
        margin-left: 5px;
    }

    .student-answer-text {
        font-weight: 600;
        margin-right: 5px;
    }

    .student-answer-text.correct {
        color: #28a745;
    }

    .student-answer-text.incorrect {
        color: #dc3545;
    }

    .correct-answer {
        font-size: 14px;
        color: #28a745;
        font-weight: 600;
    }

    .answer-box {
        background: white;
        padding: 15px;
        border-radius: 8px;
        color: #333;
        line-height: 1.6;
    }

    .answer-box.student {
        border: 1px solid #ddd;
    }

    .answer-box.correct {
        background: rgba(40, 167, 69, 0.1);
        border: 1px solid #28a745;
    }

    @media (max-width: 768px) {
        .question-header {
            flex-direction: column;
        }

        .answer-section {
            margin-right: 0;
        }

        .score-display {
            flex-direction: column;
        }
    }
</style>

<section class="result-section">
    <div class="container">
        <div class="result-container">
            <div class="result-card {{ $attempt->is_passed ? 'passed' : 'failed' }}">
                <div class="result-icon {{ $attempt->is_passed ? 'passed' : 'failed' }}">
                    <i class="fas {{ $attempt->is_passed ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                </div>
                
                <h1>{{ $attempt->is_passed ? 'مبروك! لقد نجحت' : 'لم تنجح هذه المرة' }}</h1>
                <p class="text-muted">{{ $quiz->title }}</p>

                <div class="score-display">
                    <div class="score-item">
                        <span class="score-value">{{ $attempt->score }}</span>
                        <span class="score-label">من {{ $attempt->total_score }}</span>
                    </div>
                    <div class="score-item">
                        <div class="percentage-circle {{ $attempt->is_passed ? 'passed' : 'failed' }}">
                            {{ number_format($attempt->percentage, 1) }}%
                        </div>
                        <span class="score-label">النسبة المئوية</span>
                    </div>
                </div>

                <div class="alert alert-{{ $attempt->is_passed ? 'success' : 'danger' }}" style="margin-top: 20px;">
                    <strong>
                        @if($attempt->is_passed)
                            <i class="fas fa-check-circle me-2"></i>
                            لقد حصلت على {{ number_format($attempt->percentage, 1) }}% وهذا أكثر من الحد الأدنى للنجاح ({{ $quiz->passing_score }}%)
                        @else
                            <i class="fas fa-times-circle me-2"></i>
                            لقد حصلت على {{ number_format($attempt->percentage, 1) }}% وهذا أقل من الحد الأدنى للنجاح ({{ $quiz->passing_score }}%)
                            <br><br>
                            <i class="fas fa-redo me-2"></i>
                            يمكنك إعادة المحاولة مرة أخرى حتى تنجح!
                        @endif
                    </strong>
                </div>

                @if($attempt->completed_at)
                    <div class="d-flex justify-content-center gap-4 mt-3 text-muted flex-wrap">
                        <span>
                            <i class="fas fa-calendar-check me-2"></i>
                            تم الانتهاء: {{ $attempt->completed_at->format('Y-m-d H:i') }}
                        </span>
                        @if($timeSpent !== null)
                            <span>
                                <i class="fas fa-hourglass-half me-2"></i>
                                الوقت المستغرق: {{ $timeSpent }} دقيقة
                            </span>
                        @endif
                    </div>
                @endif

                <div class="actions">
                    @if(!$attempt->is_passed)
                        <form action="{{ route('quiz.start', ['quizId' => $quiz->id]) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-action btn-warning" style="border: none; cursor: pointer;">
                                <i class="fas fa-redo me-2"></i> إعادة المحاولة
                            </button>
                        </form>
                    @endif
                    @if($nextLecture)
                        <a href="{{ route('lecture', ['lecture_id' => $nextLecture->id]) }}" class="btn-action btn-primary">
                            <i class="fas fa-arrow-left me-2"></i> المحاضرة التالية
                        </a>
                    @endif
                    <a href="{{ route('videos', ['month_id' => $quiz->lecture->month_id]) }}" class="btn-action btn-secondary">
                        <i class="fas fa-list me-2"></i> قائمة المحاضرات
                    </a>
                </div>
            </div>

            <!-- قسم الإجابات التفصيلية -->
            <div class="result-card" style="text-align: right;">
                <h3 style="color: var(--primary-color); margin-bottom: 25px; text-align: center;">
                    <i class="fas fa-tasks me-2"></i>
                    مراجعة الإجابات
                </h3>

                <div class="answers-review">
                    @php
                        $studentAnswers = is_array($attempt->answers) ? $attempt->answers : json_decode($attempt->answers, true);
                    @endphp

                    @foreach($quiz->questions as $index => $question)
                        @php
                            $studentAnswer = $studentAnswers[$question->id] ?? null;
                            $isCorrect = $question->isCorrectAnswer($studentAnswer);
                        @endphp

                        <div class="question-card {{ $isCorrect ? 'correct' : 'incorrect' }}">
                            <div class="question-header">
                                <div class="question-icon {{ $isCorrect ? 'correct' : 'incorrect' }}">
                                    <i class="fas {{ $isCorrect ? 'fa-check' : 'fa-times' }}"></i>
                                </div>
                                <div class="question-content">
                                    <div class="question-title">
                                        السؤال {{ $index + 1 }}
                                        <span class="question-points {{ $isCorrect ? 'correct' : 'incorrect' }}">
                                            {{ $isCorrect ? $question->points : 0 }} / {{ $question->points }} نقطة
                                        </span>
                                    </div>
                                    <p class="question-text">{{ $question->question }}</p>
                                </div>
                            </div>

                            <div class="answer-section">
                                @if($question->type === 'multiple_choice' || $question->type === 'true_false')
                                    <div class="student-answer">
                                        <strong>إجابتك:</strong>
                                        <span class="student-answer-text {{ $isCorrect ? 'correct' : 'incorrect' }}">
                                            {{ $studentAnswer ?? 'لم تجب' }}
                                        </span>
                                    </div>
                                    @if(!$isCorrect)
                                        <div class="correct-answer">
                                            <i class="fas fa-check-circle me-1"></i>
                                            الإجابة الصحيحة: {{ $question->correct_answer }}
                                        </div>
                                    @endif

                                @elseif($question->type === 'text')
                                    <div style="margin-bottom: 15px;">
                                        <strong style="color: #666; font-size: 14px; display: block; margin-bottom: 8px;">إجابتك:</strong>
                                        <div class="answer-box student" style="border-color: {{ $isCorrect ? '#28a745' : '#dc3545' }};">
                                            {{ $studentAnswer ?? 'لم تجب' }}
                                        </div>
                                    </div>
                                    @if(!$isCorrect && $question->correct_answer)
                                        <div style="margin-top: 15px;">
                                            <strong style="color: #28a745; font-size: 14px; display: block; margin-bottom: 8px;">
                                                <i class="fas fa-check-circle me-1"></i>
                                                الإجابة النموذجية:
                                            </strong>
                                            <div class="answer-box correct">
                                                {{ $question->correct_answer }}
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endsection



