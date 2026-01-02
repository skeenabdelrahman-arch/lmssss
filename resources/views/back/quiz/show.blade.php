@extends('back_layouts.master')

@section('title') إدارة الكويز - {{ $lecture->title }} @stop

@section('css')
<style>
    body { background-color: #f4f7fa; }
    .main-wrapper { max-width: 1000px; margin: 0 auto; }

    /* كرت الإعدادات العلوي */
    .quiz-config-card {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.04);
        padding: 30px;
        margin-bottom: 40px;
    }

    .section-label {
        display: flex; align-items: center; gap: 10px;
        font-weight: 800; color: #1e293b; font-size: 1.1rem; margin-bottom: 25px;
    }
    .section-label i { color: #6366f1; background: #eef2ff; padding: 10px; border-radius: 12px; }

    /* كرت السؤال */
    .question-card {
        background: #fff; border-radius: 20px; border: 1px solid #f1f5f9;
        padding: 30px; margin-bottom: 30px; position: relative;
        transition: transform 0.3s ease;
    }
    .question-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.06); }
    
    .question-badge {
        position: absolute; top: -12px; right: 30px;
        background: #1e293b; color: #fff; padding: 4px 18px;
        border-radius: 50px; font-size: 13px; font-weight: 600;
    }

    .modern-input {
        border: 2px solid #f1f5f9; border-radius: 12px; padding: 12px 16px;
        transition: all 0.2s; background: #fcfdfe; width: 100%;
    }
    .modern-input:focus { border-color: #6366f1; background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }

    .options-box { background: #f8fafc; border-radius: 15px; padding: 20px; border-right: 5px solid #6366f1; }

    /* Sticky Footer */
    .action-footer {
        position: sticky; bottom: 20px; background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px); padding: 20px; border-radius: 20px;
        box-shadow: 0 -10px 30px rgba(0,0,0,0.05); z-index: 1000; border: 1px solid #eee;
    }

    .btn-submit-quiz { background: #6366f1; color: white; border: none; padding: 12px 35px; border-radius: 12px; font-weight: 700; }
    .add-q-btn { border: 2px dashed #cbd5e1; background: #fff; color: #64748b; padding: 15px; border-radius: 15px; width: 100%; font-weight: 700; margin-bottom: 40px; }
</style>
@endsection

@section('content')
<div class="main-wrapper py-5">
    
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('lecture.index') }}" class="text-decoration-none text-muted fw-bold">
            <i class="fas fa-chevron-right me-2"></i> العودة للمحاضرات
        </a>
        <span class="badge bg-white text-dark shadow-sm p-2 px-3 rounded-pill border">كود المحاضرة: #{{ $lecture->id }}</span>
    </div>

    <form action="{{ route('admin.quiz.store', $lecture->id) }}" method="POST" id="quizForm">
        @csrf

        <div class="quiz-config-card">
            <div class="section-label">
                <i class="fas fa-cog"></i>
                <span>إعدادات الاختبار ووصفه</span>
            </div>
            
            <div class="row g-4">
                <div class="col-md-8">
                    <label class="form-label fw-bold">عنوان الاختبار</label>
                    <input type="text" name="title" class="modern-input" value="{{ $lecture->quiz->title ?? '' }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">نسبة النجاح (%)</label>
                    <input type="number" name="passing_score" class="modern-input text-center" value="{{ $lecture->quiz->passing_score ?? 50 }}" required>
                </div>
                
                <div class="col-md-12">
                    <label class="form-label fw-bold">وصف الاختبار (تعليمات للطلاب)</label>
                    <textarea name="description" class="modern-input" rows="3" placeholder="اكتب هنا التعليمات التي ستظهر للطالب قبل بدء الاختبار...">{{ $lecture->quiz->description ?? '' }}</textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold">وقت الحل (بالدقائق)</label>
                    <input type="number" name="time_limit" class="modern-input" value="{{ $lecture->quiz->time_limit ?? '' }}" placeholder="اتركه فارغاً لوقت مفتوح">
                </div>

                <div class="col-md-8 d-flex align-items-end gap-3 flex-wrap">
                    <div class="form-check form-switch p-0 ms-4">
                        <label class="form-check-label fw-bold me-5" for="is_required">اختبار إجباري</label>
                        <input class="form-check-input ms-0" type="checkbox" name="is_required" id="is_required" value="1" {{ ($lecture->quiz->is_required ?? 1) ? 'checked' : '' }}>
                    </div>
                    <div class="form-check form-switch p-0 ms-4">
                        <label class="form-check-label fw-bold me-5" for="is_active">تفعيل الآن</label>
                        <input class="form-check-input ms-0" type="checkbox" name="is_active" id="is_active" value="1" {{ ($lecture->quiz->is_active ?? 1) ? 'checked' : '' }}>
                    </div>
                    <div class="form-check form-switch p-0 ms-4 text-danger">
                        <label class="form-check-label fw-bold me-5" for="exclude_excel_students">استثناء طلاب Excel</label>
                        <input class="form-check-input ms-0" type="checkbox" name="exclude_excel_students" id="exclude_excel_students" value="1" {{ ($lecture->quiz->exclude_excel_students ?? 0) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>
        </div>

        <div id="questionsContainer">
            @if(isset($lecture->quiz) && $lecture->quiz->questions->count() > 0)
                @foreach($lecture->quiz->questions as $index => $question)
                    @include('back.quiz.question_item', ['index' => $index, 'question' => $question])
                @endforeach
            @endif
        </div>

        <button type="button" class="add-q-btn" onclick="addQuestion()">
            <i class="fas fa-plus-circle me-2"></i> أضف سؤالاً جديداً
        </button>

        <div class="action-footer d-flex justify-content-between align-items-center">
            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-submit-quiz shadow-sm">
                    <i class="fas fa-save me-2"></i> حفظ كافة التعديلات
                </button>
                @if(isset($lecture->quiz))
                <a href="{{ route('admin.quiz.results', $lecture->id) }}" class="btn btn-light fw-bold border px-4 rounded-3">
                    <i class="fas fa-chart-line me-2 text-primary"></i> النتائج
                </a>
                @endif
            </div>

            @if(isset($lecture->quiz))
            <form action="{{ route('admin.quiz.destroy', $lecture->id) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف الكويز نهائياً؟');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-link text-danger fw-bold text-decoration-none">
                    <i class="fas fa-trash-alt me-2"></i> حذف الكويز
                </button>
            </form>
            @endif
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
    let questionIndex = {{ isset($lecture->quiz) && $lecture->quiz->questions ? $lecture->quiz->questions->count() : 0 }};

    function addQuestion() {
        const container = document.getElementById('questionsContainer');
        const html = `
            <div class="question-card animate__animated animate__fadeInUp" data-index="${questionIndex}">
                <span class="question-badge">سؤال #${questionIndex + 1}</span>
                <div class="d-flex justify-content-end mb-3">
                    <button type="button" class="btn btn-sm text-danger border-0 fw-bold" onclick="removeQuestion(this)">
                        <i class="fas fa-times me-1"></i> إزالة
                    </button>
                </div>
                <div class="row g-4">
                    <div class="col-md-9">
                        <label class="form-label fw-bold">نص السؤال</label>
                        <textarea name="questions[${questionIndex}][question]" class="modern-input" rows="2" required></textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">درجة السؤال</label>
                        <input type="number" name="questions[${questionIndex}][points]" class="modern-input text-center" value="1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">نوع السؤال</label>
                        <select name="questions[${questionIndex}][type]" class="modern-input" onchange="changeQuestionType(this)" required>
                            <option value="multiple_choice">اختيار من متعدد</option>
                            <option value="true_false">صحيح / خطأ</option>
                            <option value="text">إجابة نصية</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold text-success">الإجابة الصحيحة</label>
                        <input type="text" name="questions[${questionIndex}][correct_answer]" class="modern-input border-success" required>
                    </div>
                    <div class="col-md-12 question-options-container">
                        <div class="options-box">
                            <label class="form-label fw-bold"><i class="fas fa-list-ol me-2"></i>خيارات الإجابة</label>
                            <textarea name="questions[${questionIndex}][options]" class="form-control border-0 bg-transparent" rows="3" 
                                placeholder="اكتب كل خيار في سطر.. الأول هو الصحيح"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        questionIndex++;
        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
    }

    function removeQuestion(btn) {
        if(confirm('حذف هذا السؤال؟')) btn.closest('.question-card').remove();
    }

    function changeQuestionType(select) {
        const card = select.closest('.question-card');
        card.querySelector('.question-options-container').style.display = (select.value === 'multiple_choice') ? 'block' : 'none';
    }
</script>
@endsection