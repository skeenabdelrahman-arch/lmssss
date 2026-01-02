@extends('front.layouts.app')
@section('title')
{{$exam_name->exam_title}} | {{ site_name() }}
@endsection

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
        --primary-light: #b05ee7;
    }

    .exam-review-section {
        padding: 120px 0 80px;
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.03), rgba(250, 137, 107, 0.03));
        min-height: calc(100vh - 90px);
    }

    .result-card {
        max-width: 800px;
        margin: 0 auto 40px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 20px;
        padding: 40px;
        text-align: center;
        color: white;
        box-shadow: 0 10px 40px rgba(116, 36, 169, 0.3);
    }

    .result-card h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .result-score {
        font-size: 3rem;
        font-weight: 900;
        margin: 20px 0;
    }

    .exam-container {
        max-width: 900px;
        margin: 0 auto;
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .exam-header {
        text-align: center;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 2px solid #e0e0e0;
    }

    .exam-header h2 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .question-card {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        border: 2px solid transparent;
    }

    .question-card h5 {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 20px;
    }

    .question-card img {
        max-width: 100%;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .question_options {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .question_options li {
        margin-bottom: 12px;
        padding: 12px 15px;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .correct-answer {
        background: #d4edda;
        border: 2px solid #28a745;
        color: #155724;
        font-weight: 600;
    }

    .wrong-answer {
        background: #f8d7da;
        border: 2px solid #dc3545;
        color: #721c24;
        font-weight: 600;
    }

    .normal-answer {
        background: white;
        border: 2px solid #e0e0e0;
        color: #6c757d;
    }

    .unanswered {
        background: #fff3cd;
        border: 2px solid #ffc107;
        color: #856404;
        font-style: italic;
        padding: 10px 15px;
        border-radius: 10px;
        margin-top: 10px;
    }

    .answer-icon {
        margin-left: 10px;
        font-size: 1.2rem;
    }

    @media (max-width: 768px) {
        .exam-container {
            padding: 25px 20px;
        }

        .result-card {
            padding: 30px 20px;
        }

        .result-score {
            font-size: 2rem;
        }
    }
</style>

<section class="exam-review-section">
    <div class="container">
        <div class="result-card">
            <h2><i class="fas fa-trophy me-2"></i>نتيجة الامتحان</h2>
            <div class="result-score">{{$exam_result->degree}} / {{$exam_degree}}</div>
            <p style="font-size: 1.2rem; opacity: 0.9;">
                النسبة المئوية: {{ $exam_degree > 0 ? round(($exam_result->degree / $exam_degree) * 100) : 0 }}%
            </p>
        </div>

        <div class="exam-container">
            <div class="exam-header">
                <h2><i class="fas fa-clipboard-check me-2"></i>{{$exam_name->exam_title}}</h2>
            </div>

            @foreach ($exam_questions as $q)
                @php
                    $student_answer = App\Models\ExamAnswer::where('student_id', Auth::guard('student')->user()->id)
                                        ->where('exam_id', $exam_name->id)
                                        ->where('question_id', $q->id)
                                        ->first();
                    $answered = $student_answer && $student_answer->student_answer;
                @endphp

                <div class="question-card">
                    <h5>{{$loop->iteration}}.) {{$q->question_title ?? ''}}</h5>
                    @if($q->img)
                        <img src="{{url('upload_files/'.$q->img)}}" alt="Question Image">
                    @endif

                    @php
                        $questionType = $q->question_type ?? 'multiple_choice';
                    @endphp
                    
                    @if($questionType === 'true_false')
                        {{-- أسئلة صح/غلط --}}
                        <div class="true-false-review" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 20px;">
                            @php
                                $trueIsCorrect = $q->correct_answer == 'صح';
                                $falseIsCorrect = $q->correct_answer == 'غلط';
                                $trueIsStudentAnswer = $answered && $student_answer->student_answer == 'صح';
                                $falseIsStudentAnswer = $answered && $student_answer->student_answer == 'غلط';
                            @endphp
                            
                            <div class="true-false-card-review {{ $trueIsCorrect ? 'correct-answer' : ($trueIsStudentAnswer ? 'wrong-answer' : 'normal-answer') }}" 
                                 style="border: 3px solid {{ $trueIsCorrect ? '#28a745' : ($trueIsStudentAnswer ? '#dc3545' : '#6c757d') }}; border-radius: 15px; padding: 30px; text-align: center;">
                                <i class="fas fa-check-circle fa-3x {{ $trueIsCorrect ? 'text-success' : ($trueIsStudentAnswer ? 'text-danger' : 'text-muted') }} mb-3"></i>
                                <h4 class="fw-bold {{ $trueIsCorrect ? 'text-success' : ($trueIsStudentAnswer ? 'text-danger' : 'text-muted') }}">صح</h4>
                                @if($trueIsCorrect)
                                    <span class="badge bg-success mt-2">الإجابة الصحيحة</span>
                                @elseif($trueIsStudentAnswer)
                                    <span class="badge bg-danger mt-2">إجابتك</span>
                                @endif
                            </div>
                            
                            <div class="true-false-card-review {{ $falseIsCorrect ? 'correct-answer' : ($falseIsStudentAnswer ? 'wrong-answer' : 'normal-answer') }}" 
                                 style="border: 3px solid {{ $falseIsCorrect ? '#28a745' : ($falseIsStudentAnswer ? '#dc3545' : '#6c757d') }}; border-radius: 15px; padding: 30px; text-align: center;">
                                <i class="fas fa-times-circle fa-3x {{ $falseIsCorrect ? 'text-success' : ($falseIsStudentAnswer ? 'text-danger' : 'text-muted') }} mb-3"></i>
                                <h4 class="fw-bold {{ $falseIsCorrect ? 'text-success' : ($falseIsStudentAnswer ? 'text-danger' : 'text-muted') }}">غلط</h4>
                                @if($falseIsCorrect)
                                    <span class="badge bg-success mt-2">الإجابة الصحيحة</span>
                                @elseif($falseIsStudentAnswer)
                                    <span class="badge bg-danger mt-2">إجابتك</span>
                                @endif
                            </div>
                        </div>
                    @else
                        {{-- أسئلة اختيار من متعدد --}}
                        <ul class="question_options">
                            @foreach (['ch_1', 'ch_2', 'ch_3', 'ch_4'] as $choice)
                                @if($q->$choice)
                                    @php
                                        $isCorrect = $q->$choice == $q->correct_answer;
                                        $isStudentAnswer = $answered && $student_answer->student_answer == $q->$choice;
                                        $class = 'normal-answer';
                                        $icon = '';

                                        if($isCorrect) {
                                            $class = 'correct-answer';
                                            $icon = '<i class="fas fa-check-circle answer-icon"></i>';
                                        } elseif($isStudentAnswer) {
                                            $class = 'wrong-answer';
                                            $icon = '<i class="fas fa-times-circle answer-icon"></i>';
                                        }
                                    @endphp
                                    <li class="{{ $class }}">
                                        {!! $icon !!}
                                        {{$q->$choice}}
                                        @if($isCorrect)
                                            <span style="margin-right: 10px; font-weight: 700;">(الإجابة الصحيحة)</span>
                                        @endif
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @endif

                        @if(!$answered)
                            <li class="unanswered">
                                <i class="fas fa-exclamation-circle me-2"></i>لم يتم الإجابة على هذا السؤال
                            </li>
                        @endif
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
