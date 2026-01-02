<div class="question-card shadow-sm animate__animated animate__fadeIn" data-index="{{ $index }}">
    <span class="question-badge">سؤال #{{ $index + 1 }}</span>

    <div class="text-start">
        <button type="button" class="btn btn-sm text-danger border-0 p-0 mb-3 fw-bold" onclick="removeQuestion(this)">
            <i class="fas fa-trash-alt me-1"></i> حذف السؤال
        </button>
    </div>

    <div class="row g-4">
        <div class="col-md-9">
            <label class="form-label fw-bold text-dark">نص السؤال <span class="text-danger">*</span></label>
            <textarea name="questions[{{ $index }}][question]" class="modern-input" rows="2" placeholder="اكتب السؤال هنا..." required>{{ $question->question }}</textarea>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-bold text-dark">الدرجة <span class="text-danger">*</span></label>
            <input type="number" name="questions[{{ $index }}][points]" class="modern-input text-center fw-bold" min="1" value="{{ $question->points }}" required>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold text-dark">نوع السؤال <span class="text-danger">*</span></label>
            <select name="questions[{{ $index }}][type]" class="modern-input" onchange="changeQuestionType(this)" required>
                <option value="multiple_choice" {{ $question->type == 'multiple_choice' ? 'selected' : '' }}>اختيار من متعدد</option>
                <option value="true_false" {{ $question->type == 'true_false' ? 'selected' : '' }}>صحيح / خطأ</option>
                <option value="text" {{ $question->type == 'text' ? 'selected' : '' }}>إجابة نصية</option>
            </select>
        </div>

        <div class="col-md-8">
            <label class="form-label fw-bold text-success">
                <i class="fas fa-check-circle me-1"></i> الإجابة الصحيحة <span class="text-danger">*</span>
            </label>
            <input type="text" name="questions[{{ $index }}][correct_answer]" class="modern-input border-success" value="{{ $question->correct_answer }}" required>
            <div class="mt-2 small text-muted hint-text">
                @if($question->type == 'multiple_choice')
                    للاختيار من متعدد: اكتب الخيار الصحيح كما هو بالأعلى تماماً.
                @elseif($question->type == 'true_false')
                    اكتب كلمة: صحيح أو خطأ.
                @else
                    اكتب الإجابة النموذجية للتصحيح التلقائي.
                @endif
            </div>
        </div>

        <div class="col-md-12 question-options-container" style="display: {{ $question->type == 'multiple_choice' ? 'block' : 'none' }};">
            <div class="options-box shadow-sm">
                <label class="form-label fw-bold"><i class="fas fa-list-ol me-2 text-primary"></i>خيارات الإجابة</label>
                <textarea name="questions[{{ $index }}][options]" class="form-control border-0 bg-transparent" rows="4" 
                    placeholder="ضع كل خيار في سطر منفصل...&#10;ملاحظة: السطر الأول يجب أن يكون الإجابة الصحيحة."
                    {{ $question->type == 'multiple_choice' ? 'required' : '' }}>{{ $question->options ? implode("\n", $question->options) : '' }}</textarea>
                <div class="mt-2 small text-primary fw-semibold">
                    <i class="fas fa-info-circle me-1"></i> تأكد أن الإجابة الصحيحة هي أول سطر دائماً.
                </div>
            </div>
        </div>
    </div>
</div>