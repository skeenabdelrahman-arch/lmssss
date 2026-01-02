@extends('back_layouts.master')

@section('title', 'إضافة واجب جديد')

@section('css')
<style>
    /* تحسينات عامة للنموذج */
    .form-card { border-radius: 15px; border: none; box-shadow: 0 0 20px rgba(0,0,0,0.05); }
    .form-label { font-weight: 600; color: #495057; margin-bottom: 8px; }
    .section-title { 
        background: #f8f9fa; 
        padding: 10px 15px; 
        border-right: 4px solid #7424a9; 
        border-radius: 5px; 
        margin-bottom: 20px; 
        font-weight: 700;
    }
    .custom-switch .custom-control-label::before { height: 1.5rem; width: 2.75rem; border-radius: 1rem; }
    .custom-switch .custom-control-label::after { width: calc(1.5rem - 4px); height: calc(1.5rem - 4px); border-radius: 1rem; }
    
    /* تنسيق كروت الأسئلة */
    .question-card {
        border: 2px solid #e9ecef;
        border-radius: 15px;
        transition: all 0.3s ease;
        background: #fff;
    }
    .question-card:hover { border-color: #7424a9; box-shadow: 0 5px 15px rgba(116, 36, 169, 0.1); }
    .option-row {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #eee;
    }
</style>
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex align-items-center">
            <h4 class="content-title mb-0 my-auto text-primary"><i class="fas fa-plus-circle me-2"></i> الواجبات</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ إضافة واجب جديد</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        
        {{-- عرض الأخطاء --}}
        @if($errors->any())
            <div class="alert alert-danger shadow-sm border-0 alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                <ul class="mb-0">
                    @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('assignments.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card form-card">
                <div class="card-body">
                    
                    <div class="section-title text-primary">البيانات الأساسية</div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="form-label">عنوان الواجب <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       name="title" value="{{ old('title') }}" placeholder="مثلاً: واجب الدرس الأول - الكيمياء العضوية" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">الدرجة الكلية <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="total_marks" value="{{ old('total_marks', 10) }}" min="1">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">الوصف والتعليمات</label>
                                <textarea class="form-control" name="description" rows="3" placeholder="اكتب تعليمات للطلاب هنا...">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">الشهر <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="month_id" name="month_id" required>
                                    <option value="">-- اختر الشهر --</option>
                                    @foreach($months as $month)
                                        <option value="{{ $month->id }}">{{ $month->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">المحاضرة (اختياري)</label>
                                <select class="form-control" id="lecture_id" name="lecture_id">
                                    <option value="">-- اختر الشهر أولاً --</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">الموعد النهائي</label>
                                <input type="datetime-local" class="form-control" name="deadline" value="{{ old('deadline') }}">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">ملف الواجب (PDF أو صور)</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="file_path" name="file_path">
                                    <label class="custom-file-label" for="file_path">اختر الملف...</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-title text-primary mt-4">الإعدادات المتقدمة</div>
                    <div class="row bg-light p-3 rounded mb-4">
                        <div class="col-md-4">
                            <div class="custom-control custom-switch mt-2">
                                <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" checked>
                                <label class="custom-control-label fw-bold" for="status">نشر الواجب فوراً</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="custom-control custom-switch mt-2">
                                <input type="checkbox" class="custom-control-input" id="auto_grade_all" name="auto_grade_all" value="1">
                                <label class="custom-control-label fw-bold" for="auto_grade_all">تصحيح تلقائي كامل</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label class="small fw-bold">ترتيب العرض</label>
                                <input type="number" class="form-control form-control-sm" name="display_order" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="section-title text-primary d-flex justify-content-between align-items-center">
                        <span>الأسئلة التفاعلية</span>
                        <button type="button" id="add-question" class="btn btn-sm btn-success rounded-pill">
                            <i class="fas fa-plus-circle"></i> إضافة سؤال جديد
                        </button>
                    </div>

                    <div id="questions-wrapper" class="mt-3">
                        {{-- الأسئلة ستظهر هنا عبر الـ JS --}}
                    </div>

                    <div class="mt-5 border-top pt-4 text-center">
                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                            <i class="fas fa-save me-2"></i> حفظ الواجب بالكامل
                        </button>
                        <a href="{{ route('assignments.index') }}" class="btn btn-light btn-lg px-5">إلغاء</a>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // تحديث اسم الملف عند الاختيار
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // AJAX تحميل المحاضرات
    $('#month_id').on('change', function() {
        var monthId = $(this).val();
        var lectureSelect = $('#lecture_id');
        lectureSelect.html('<option>جاري التحميل...</option>');
        
        if(monthId) {
            $.get('/admin/lecture-restrictions/api/lectures/' + monthId, function(data) {
                lectureSelect.html('<option value="">-- عام للشهر --</option>');
                $.each(data, function(i, v) {
                    lectureSelect.append(`<option value="${v.id}">${v.name || v.title}</option>`);
                });
            });
        }
    });
});

// مولد الأسئلة الديناميكي
(function() {
    const wrapper = document.getElementById('questions-wrapper');
    const addBtn = document.getElementById('add-question');
    let qIndex = 0;

    function createOptionRow(qIdx, oIdx) {
        const div = document.createElement('div');
        div.className = 'option-row d-flex align-items-center';
        div.innerHTML = `
            <div class="flex-grow-1">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white fw-bold">${String.fromCharCode(65 + oIdx)}</span>
                    </div>
                    <input type="text" class="form-control border-0" name="questions[${qIdx}][options][${oIdx}][option_text]" placeholder="نص الاختيار">
                </div>
            </div>
            <div class="mx-3">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="q${qIdx}o${oIdx}" name="questions[${qIdx}][options][${oIdx}][is_correct]" value="1">
                    <label class="custom-control-label text-success fw-bold" for="q${qIdx}o${oIdx}">صحيح</label>
                </div>
            </div>
            <button type="button" class="btn btn-sm text-danger remove-option"><i class="fas fa-times"></i></button>
        `;
        return div;
    }

    function buildQuestionCard(idx) {
        const card = document.createElement('div');
        card.className = 'question-card card mb-4 shadow-sm';
        card.innerHTML = `
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-edit text-primary me-2"></i> السؤال رقم #${idx + 1}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-question"><i class="fas fa-trash"></i> حذف</button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="small fw-bold">نوع السؤال</label>
                        <select class="form-control form-control-sm question-type" name="questions[${idx}][type]">
                            <option value="mcq_single">اختيار من متعدد (إجابة واحدة)</option>
                            <option value="mcq_multi">اختيار من متعدد (إجابات متعددة)</option>
                            <option value="essay">سؤال مقالي / رفع ملف</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="small fw-bold">الدرجة</label>
                        <input type="number" class="form-control form-control-sm" name="questions[${idx}][max_marks]" value="1" step="0.5">
                    </div>
                    <div class="col-md-3 form-group">
                        <label class="small fw-bold">الترتيب</label>
                        <input type="number" class="form-control form-control-sm" name="questions[${idx}][display_order]" value="${idx}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="small fw-bold">نص السؤال</label>
                    <textarea class="form-control" name="questions[${idx}][question_text]" rows="2" placeholder="اكتب نص السؤال هنا..."></textarea>
                </div>

                <div class="bg-light p-2 rounded mb-3 d-flex" style="gap: 20px">
                    <div class="custom-control custom-checkbox small">
                        <input type="checkbox" class="custom-control-input" id="req${idx}" name="questions[${idx}][is_required]" value="1" checked>
                        <label class="custom-control-label" for="req${idx}">إلزامي الحل</label>
                    </div>
                    <div class="mcq-only-ui custom-control custom-checkbox small">
                        <input type="checkbox" class="custom-control-input" id="auto${idx}" name="questions[${idx}][auto_grade]" value="1" checked>
                        <label class="custom-control-label" for="auto${idx}">تصحيح تلقائي</label>
                    </div>
                </div>

                <div class="mcq-section">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="small fw-bold text-primary">الاختيارات المتاحة:</label>
                        <button type="button" class="btn btn-xs btn-primary add-option">إضافة اختيار</button>
                    </div>
                    <div class="option-list"></div>
                </div>

                <div class="essay-section d-none">
                    <div class="alert alert-info py-2 small">
                        <i class="fas fa-info-circle"></i> الأسئلة المقالية تتطلب تصحيحاً يدوياً من المعلم لاحقاً.
                    </div>
                </div>
            </div>
        `;

        // منطق الخيارات داخل السؤال
        const optionList = card.querySelector('.option-list');
        const addOptionBtn = card.querySelector('.add-option');
        let optIdx = 0;

        const addOpt = () => optionList.appendChild(createOptionRow(idx, optIdx++));
        addOpt(); addOpt(); // إضافة خيارين تلقائياً

        addOptionBtn.onclick = addOpt;
        optionList.onclick = (e) => e.target.closest('.remove-option') && e.target.closest('.option-row').remove();

        // التبديل بين مقالي واختياري
        const typeSelect = card.querySelector('.question-type');
        typeSelect.onchange = () => {
            const isEssay = typeSelect.value === 'essay';
            card.querySelector('.mcq-section').classList.toggle('d-none', isEssay);
            card.querySelector('.mcq-only-ui').classList.toggle('d-none', isEssay);
            card.querySelector('.essay-section').classList.toggle('d-none', !isEssay);
        };

        card.querySelector('.remove-question').onclick = () => card.remove();
        return card;
    }

    addBtn.onclick = () => wrapper.appendChild(buildQuestionCard(qIndex++));
})();
</script>
@endsection