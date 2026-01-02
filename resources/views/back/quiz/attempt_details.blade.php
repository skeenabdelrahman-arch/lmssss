@extends('back_layouts.master')

@section('title') تفاصيل محاولة - {{ $attempt->student->first_name }} @stop

@section('css')
<style>
    body { background-color: #f4f7fa; }
    .main-wrapper { max-width: 900px; margin: 0 auto; }

    /* كروت المعلومات */
    .detail-card {
        background: #fff; border-radius: 24px; padding: 30px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.04); border: none; margin-bottom: 30px;
    }

    /* هيدر النتيجة */
    .result-header-card {
        background: #fff; border-radius: 24px; overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.04); margin-bottom: 30px;
    }
    .result-banner {
        padding: 40px; text-align: center; color: white;
    }
    .banner-passed { background: linear-gradient(135deg, #10b981, #059669); }
    .banner-failed { background: linear-gradient(135deg, #ef4444, #dc2626); }

    .score-circle {
        width: 100px; height: 100px; background: rgba(255,255,255,0.2);
        border-radius: 50%; display: flex; flex-direction: column;
        align-items: center; justify-content: center; margin: 0 auto 15px;
        border: 4px solid rgba(255,255,255,0.3);
    }

    /* شبكة المعلومات */
    .info-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; }
    .info-box { background: #f8fafc; padding: 15px; border-radius: 15px; border: 1px solid #f1f5f9; }
    .info-box label { color: #64748b; font-size: 12px; display: block; margin-bottom: 5px; font-weight: 700; }
    .info-box span { color: #1e293b; font-weight: 800; font-size: 14px; }

    /* تصميم السؤال */
    .q-detail-item {
        background: #fff; border-radius: 20px; padding: 25px;
        margin-bottom: 20px; border: 1px solid #f1f5f9; position: relative;
    }
    .q-status-icon {
        position: absolute; top: 25px; left: 25px; font-size: 24px;
    }
    .text-correct { color: #10b981; }
    .text-incorrect { color: #ef4444; }

    .answer-pill {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 8px 16px; border-radius: 10px; font-weight: 700; font-size: 13px; margin-top: 10px;
    }
    .pill-student { background: #f1f5f9; color: #475569; }
    .pill-correct { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }

    .q-number-badge {
        background: #eef2ff; color: #6366f1; padding: 4px 12px;
        border-radius: 8px; font-weight: 800; font-size: 12px; margin-bottom: 10px; display: inline-block;
    }
</style>
@endsection

@section('content')
<div class="main-wrapper py-5">
    
    <div class="mb-4">
        <a href="{{ route('admin.quiz.results', $attempt->quiz->lecture_id) }}" class="btn btn-white shadow-sm rounded-3 fw-bold border">
            <i class="fas fa-arrow-right me-2 text-primary"></i> العودة لقائمة النتائج
        </a>
    </div>

    <div class="result-header-card">
        <div class="result-banner {{ $attempt->is_passed ? 'banner-passed' : 'banner-failed' }}">
            <div class="score-circle">
                <span class="fs-3 fw-bold">{{ number_format($attempt->percentage, 0) }}%</span>
            </div>
            <h3 class="fw-bold mb-1">{{ $attempt->is_passed ? 'تهانينا، اجتاز الاختبار!' : 'للأسف، لم يجتز الاختبار' }}</h3>
            <p class="mb-0 opacity-75">تم إكمال المحاولة في {{ $attempt->completed_at ? $attempt->completed_at->format('Y-m-d h:i A') : '-' }}</p>
        </div>
        <div class="p-4 bg-white">
            <div class="info-row">
                <div class="info-box text-center">
                    <label>النقاط المحصلة</label>
                    <span>{{ $attempt->score }} من {{ $attempt->total_score }}</span>
                </div>
                <div class="info-box text-center">
                    <label>الحد الأدنى للنجاح</label>
                    <span>{{ $attempt->quiz->passing_score }}%</span>
                </div>
                <div class="info-box text-center">
                    <label>الوقت المستغرق</label>
                    <span>
                        @if($attempt->started_at && $attempt->completed_at)
                            {{ $attempt->started_at->diffInMinutes($attempt->completed_at) }} دقيقة
                        @else
                            -
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="detail-card">
        <h5 class="fw-bold mb-4"><i class="fas fa-user-graduate me-2 text-primary"></i> بيانات الطالب</h5>
        <div class="info-row">
            <div>
                <label class="text-muted small fw-bold">اسم الطالب</label>
                <div class="fw-bold text-dark">{{ $attempt->student->first_name }} {{ $attempt->student->second_name }} {{ $attempt->student->forth_name }}</div>
            </div>
            <div>
                <label class="text-muted small fw-bold">رقم الهاتف</label>
                <div class="fw-bold text-dark">{{ $attempt->student->student_phone ?? '-' }}</div>
            </div>
            <div>
                <label class="text-muted small fw-bold">تاريخ البدء</label>
                <div class="fw-bold text-dark">{{ $attempt->started_at ? $attempt->started_at->format('h:i A') : '-' }}</div>
            </div>
        </div>
    </div>

    <h5 class="fw-bold mb-4 mt-5"><i class="fas fa-tasks me-2 text-primary"></i> مراجعة الإجابات التفصيلية</h5>
    
    @foreach($attempt->quiz->questions as $index => $question)
        @php
            $studentAnswer = is_array($attempt->answers) ? ($attempt->answers[$question->id] ?? null) : null;
            $isCorrect = $studentAnswer && $question->isCorrectAnswer($studentAnswer);
        @endphp
        
        <div class="q-detail-item shadow-sm">
            <span class="q-number-badge">السؤال رقم {{ $index + 1 }}</span>
            
            <div class="q-status-icon">
                @if($isCorrect)
                    <i class="fas fa-check-circle text-correct"></i>
                @else
                    <i class="fas fa-times-circle text-incorrect"></i>
                @endif
            </div>

            <div class="pe-5">
                <p class="fw-bold text-dark fs-6 mb-3">{{ $question->question }}</p>
                
                <div class="d-flex flex-column gap-2">
                    <div>
                        <span class="small text-muted d-block mb-1">إجابة الطالب:</span>
                        <div class="answer-pill pill-student">
                            <i class="fas fa-user-edit"></i> {{ $studentAnswer ?: 'لم يتم الإجابة' }}
                        </div>
                    </div>

                    @if(!$isCorrect)
                    <div>
                        <span class="small text-muted d-block mb-1">الإجابة الصحيحة:</span>
                        <div class="answer-pill pill-correct">
                            <i class="fas fa-check"></i> {{ $question->correct_answer }}
                        </div>
                    </div>
                    @endif
                </div>

                <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="small fw-bold {{ $isCorrect ? 'text-success' : 'text-danger' }}">
                         الدرجة المستحقة: {{ $isCorrect ? $question->points : 0 }} من {{ $question->points }}
                    </span>
                    <span class="badge bg-light text-muted border px-3 rounded-pill" style="font-size: 10px;">{{ strtoupper($question->type) }}</span>
                </div>
            </div>
        </div>
    @endforeach

</div>
@endsection