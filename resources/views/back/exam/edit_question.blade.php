@extends('back_layouts.master')

@section('title') تعديل سؤال @endsection

@section('css')
<style>
    /* تحسينات بصرية عامة */
    .modern-card {
        border-radius: 15px; border: none; background: #fff;
        padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .form-label { font-weight: 700; color: #475569; margin-bottom: 8px; }
    .form-control, .form-select { border-radius: 10px; padding: 12px; border: 1px solid #d1d5db; }

    /* تنسيق خيارات الأسئلة */
    .choice-card {
        transition: all 0.3s ease;
        border: 2px solid #f1f5f9;
        border-radius: 12px;
    }
    .choice-card:hover { transform: translateY(-3px); border-color: #cbd5e1; }
    .correct-choice { border-color: #10b981 !important; background-color: #f0fdf4; }
    
    /* تنسيق صح وغلط */
    .true-false-card {
        border-radius: 15px;
        transition: all 0.2s;
        border: 2px solid #e2e8f0;
    }
    .true-false-card.selected-true { border: 3px solid #10b981 !important; background: #f0fdf4; }
    .true-false-card.selected-false { border: 3px solid #ef4444 !important; background: #fef2f2; }
    
    .img-preview-container {
        position: relative;
        display: inline-block;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #ddd;
    }
</style>
@endsection

@section('page-header')
<div class="page-header-modern mb-4">
    <div class="d-flex align-items-center">
        <div class="bg-warning text-dark rounded-3 p-3 me-3">
            <i class="fas fa-edit fa-lg"></i>
        </div>
        <div>
            <h4 class="mb-0 fw-bold">تعديل السؤال</h4>
            <p class="text-muted small mb-0">تحديث نص السؤال، الخيارات، أو الصور المرفقة لهذا السؤال</p>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid pb-5">
    
    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert" style="border-radius: 12px; border-right: 5px solid #dc3545;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-3 fa-lg"></i>
                <div><strong>خطأ!</strong> {{ session()->get('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert" style="border-radius: 12px; border-right: 5px solid #198754;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-3 fa-lg"></i>
                <div><strong>تم بنجاح!</strong> {{ session()->get('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{route('exam_question.update_Q',$exam_question->id)}}" method="POST" enctype="multipart/form-data" id="questionForm">
        @csrf
        <div class="row">
            
            <div class="col-lg-4 order-lg-2">
                <div class="modern-card shadow-sm">
                    <h5 class="fw-bold mb-3 border-bottom pb-2"><i class="fas fa-cog me-2"></i>إعدادات السؤال</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">نوع السؤال</label>
                        <select class="form-select" name="question_type" id="question_type" required>
                            <option value="multiple_choice" {{ ($exam_question->question_type ?? 'multiple_choice') == 'multiple_choice' ? 'selected' : '' }}>اختيار من متعدد</option>
                            <option value="true_false" {{ ($exam_question->question_type ?? '') == 'true_false' ? 'selected' : '' }}>صح / غلط</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">النموذج</label>
                        <select class="form-select" name="model_name">
                            <option value="A" {{ $exam_question->model_name == 'A' ? 'selected' : '' }}>نموذج A</option>
                            <option value="B" {{ $exam_question->model_name == 'B' ? 'selected' : '' }}>نموذج B</option>
                            <option value="C" {{ $exam_question->model_name == 'C' ? 'selected' : '' }}>نموذج C</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">درجة السؤال</label>
                        <input class="form-control" type="number" name="Q_degree" step="0.5" value="{{$exam_question->Q_degree}}" required>
                    </div>

                    <div class="form-check form-switch p-3 bg-light rounded-3 mb-3">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="is_bonus" id="is_bonus" value="1" {{ $exam_question->is_bonus ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="is_bonus">سؤال بونص الإضافي</label>
                    </div>
                </div>

                <div class="modern-card shadow-sm">
                    <h5 class="fw-bold mb-3 border-bottom pb-2"><i class="fas fa-image me-2"></i>صورة السؤال الرئيسية</h5>
                    <input class="form-control mb-2" type="file" name="img" accept="image/*">
                    @if($exam_question->img)
                        <div class="img-preview-container w-100 mt-2">
                            <img src="{{url('upload_files/'.$exam_question->img)}}" class="img-fluid rounded shadow-sm">
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-8 order-lg-1">
                <div class="modern-card shadow-sm mb-4">
                    <label class="form-label fw-bold fs-5">نص السؤال</label>
                    <textarea class="form-control fw-bold" name="question_title" rows="4" required style="font-size: 1.1rem; border-right: 5px solid #6366f1;">{{ $exam_question->question_title }}</textarea>
                </div>

                <div id="multiple_choice_section" class="question-type-section">
                    <div class="row g-3">
                        @php
                            $choices = [
                                'ch_1' => 'الاختيار الأول',
                                'ch_2' => 'الاختيار الثاني',
                                'ch_3' => 'الاختيار الثالث',
                                'ch_4' => 'الاختيار الرابع'
                            ];
                            $selectedCorrects = is_array($exam_question->correct_answers) ? $exam_question->correct_answers : [];
                        @endphp

                        @foreach($choices as $key => $label)
                        <div class="col-md-6">
                            <div class="modern-card choice-card p-3 {{ in_array($exam_question->$key, $selectedCorrects) ? 'correct-choice' : '' }}">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-secondary px-3 py-2">{{ $label }}</span>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="correct_answers[]" value="{{ $exam_question->$key }}" id="correct_{{ $key }}"
                                            {{ in_array($exam_question->$key, $selectedCorrects) ? 'checked' : '' }}>
                                        <label class="small fw-bold" for="correct_{{ $key }}">إجابة صحيحة</label>
                                    </div>
                                </div>
                                <textarea class="form-control mb-2" name="{{ $key }}" rows="2">{{ $exam_question->$key }}</textarea>
                                <input class="form-control form-control-sm" type="file" name="{{ $key }}_img" accept="image/*">
                                
                                @if($exam_question->{$key . '_img'})
                                    <div class="mt-2 text-center">
                                        <img src="{{ url('upload_files/' . $exam_question->{$key . '_img'}) }}" class="img-thumbnail" style="height: 80px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div id="true_false_section" class="question-type-section" style="display: none;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="card true-false-card p-4 text-center shadow-sm w-100" id="true_card" style="cursor: pointer;">
                                <input class="form-check-input d-none" type="radio" name="correct_answer_true_false" id="correct_true" value="صح" 
                                    {{ $exam_question->correct_answer == 'صح' ? 'checked' : '' }}>
                                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                                <h3 class="fw-bold text-success">صح</h3>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="card true-false-card p-4 text-center shadow-sm w-100" id="false_card" style="cursor: pointer;">
                                <input class="form-check-input d-none" type="radio" name="correct_answer_true_false" id="correct_false" value="غلط"
                                    {{ $exam_question->correct_answer == 'غلط' ? 'checked' : '' }}>
                                <i class="fas fa-times-circle fa-4x text-danger mb-3"></i>
                                <h3 class="fw-bold text-danger">غلط</h3>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-5 d-flex gap-3">
                    <button type="submit" class="btn btn-success btn-lg px-5 shadow rounded-pill">
                        <i class="fas fa-save me-2"></i> حفظ التعديلات
                    </button>
                    <a href="{{ route('exam_name.add_question', $exam_id ?? $exam_question->exam_name_id) }}" class="btn btn-outline-secondary btn-lg px-4 rounded-pill">
                        <i class="fas fa-times me-2"></i> إلغاء
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const questionType = document.getElementById('question_type');
    const multipleChoiceSection = document.getElementById('multiple_choice_section');
    const trueFalseSection = document.getElementById('true_false_section');
    
    function updateLayout() {
        if (questionType.value === 'true_false') {
            multipleChoiceSection.style.display = 'none';
            trueFalseSection.style.display = 'block';
            // Disable MC inputs to prevent validation issues
            multipleChoiceSection.querySelectorAll('textarea').forEach(el => el.required = false);
        } else {
            multipleChoiceSection.style.display = 'block';
            trueFalseSection.style.display = 'none';
        }
    }

    questionType.addEventListener('change', updateLayout);
    updateLayout(); // Initial state

    // مظهر بطاقات صح وغلط عند الضغط
    const trueCard = document.getElementById('true_card');
    const falseCard = document.getElementById('false_card');
    const trueInput = document.getElementById('correct_true');
    const falseInput = document.getElementById('correct_false');

    function updateTFStyles() {
        if (trueInput.checked) {
            trueCard.classList.add('selected-true');
            falseCard.classList.remove('selected-false');
        } else if (falseInput.checked) {
            falseCard.classList.add('selected-false');
            trueCard.classList.remove('selected-true');
        }
    }

    trueInput.addEventListener('change', updateTFStyles);
    falseInput.addEventListener('change', updateTFStyles);
    updateTFStyles();

    // تمييز الكرت المختار في MC
    document.querySelectorAll('input[name="correct_answers[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            this.closest('.choice-card').classList.toggle('correct-choice', this.checked);
        });
    });
});
</script>
@endsection