@extends('back_layouts.master')

@section('title') إضافة سؤال جديد @stop

@section('css')
<style>
    /* تحسينات عامة للفورم */
    .form-label { font-weight: 700; color: #334155; margin-bottom: 8px; display: flex; align-items: center; }
    .form-control, .form-select { border-radius: 10px; border: 1px solid #e2e8f0; padding: 12px; transition: 0.3s; }
    .form-control:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }

    /* ستايل كروت الاختيارات */
    .choice-card {
        border-radius: 15px; border: 2px solid #f1f5f9; transition: 0.3s; background: #fff; position: relative;
    }
    .choice-card.active-choice { border-color: #6366f1; background: #f8faff; }
    
    /* ستايل صح وغلط المطور */
    .tf-container { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .tf-card {
        cursor: pointer; position: relative; border-radius: 20px; border: 3px solid #f1f5f9;
        padding: 30px; text-align: center; transition: 0.3s;
    }
    .tf-card i { font-size: 3rem; margin-bottom: 15px; display: block; }
    
    /* حالة الصح */
    .tf-card.true-variant:hover { border-color: #22c55e; background: #f0fdf4; }
    input[type="radio"]:checked + .tf-card.true-variant { border-color: #22c55e; background: #f0fdf4; box-shadow: 0 10px 15px -3px rgba(34, 197, 94, 0.2); }
    
    /* حالة الغلط */
    .tf-card.false-variant:hover { border-color: #ef4444; background: #fef2f2; }
    input[type="radio"]:checked + .tf-card.false-variant { border-color: #ef4444; background: #fef2f2; box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.2); }

    .hidden-section { display: none !important; }
</style>
@endsection

@section('page-header')
<div class="page-header-modern mb-4">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <div class="bg-success text-white rounded-3 p-2 me-3">
                <i class="fas fa-plus fa-lg"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-bold">إضافة سؤال جديد</h4>
                <p class="text-muted small mb-0">أنت الآن تضيف أسئلة لنموذج الامتحان</p>
            </div>
        </div>
        <a href="{{ route('exam_name.add_question', $exam_id) }}" class="btn btn-light border rounded-pill px-4">
            <i class="fas fa-arrow-right me-1"></i> رجوع للبنك
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid pb-5">
    <form action="{{ route('add_question.insert',$exam_id) }}" method="POST" enctype="multipart/form-data" id="questionForm">
        @csrf
        
        <div class="row">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h6 class="fw-bold mb-4 border-bottom pb-2">إعدادات السؤال الأساسية</h6>
                    
                    <div class="mb-4">
                        <label class="form-label text-primary"><i class="fas fa-layer-group me-2"></i>نوع السؤال</label>
                        <select class="form-select fw-bold" name="question_type" id="question_type" required>
                            <option value="multiple_choice" selected>اختيار من متعدد</option>
                            <option value="true_false">صح أو خطأ</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-primary"><i class="fas fa-copy me-2"></i>النموذج</label>
                        <select class="form-select" name="model_name" id="model_name">
                            <option value="A">نموذج (A)</option>
                            <option value="B">نموذج (B)</option>
                            <option value="C">نموذج (C)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-primary"><i class="fas fa-award me-2"></i>درجة السؤال</label>
                        <input type="number" name="Q_degree" class="form-control fw-bold text-center" step="0.5" value="1" required>
                    </div>

                    <div class="form-check form-switch bg-light p-3 rounded-3">
                        <input class="form-check-input ms-0 me-2" type="checkbox" id="is_bonus" name="is_bonus" value="1">
                        <label class="form-check-label fw-bold" for="is_bonus">سؤال إضافي (Bonus)</label>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <label class="form-label text-primary"><i class="fas fa-image me-2"></i>صورة توضيحية للسؤال</label>
                    <input type="file" name="img" class="form-control" accept="image/*" id="main_img">
                    <div id="main_preview" class="mt-3 text-center hidden-section">
                        <img src="" class="img-fluid rounded-3 border" style="max-height: 150px;">
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <div class="mb-3">
                        <label class="form-label fs-5"><i class="fas fa-pen-fancy text-primary me-2"></i>اكتب نص السؤال هنا:</label>
                        <textarea class="form-control fs-5" name="question_title" rows="3" placeholder="مثال: ما هي عاصمة مصر؟" required></textarea>
                    </div>
                </div>

                <div id="multiple_choice_section" class="question-section">
                    <div class="row g-3">
                        @foreach(['1' => 'أ', '2' => 'ب', '3' => 'ج', '4' => 'د'] as $num => $label)
                        <div class="col-md-6">
                            <div class="choice-card p-3 shadow-sm border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-primary rounded-pill px-3">اختيار ({{ $label }})</span>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="correct_answers[]" value="ch_{{$num}}" id="correct_{{$num}}">
                                        <label class="form-check-label small fw-bold" for="correct_{{$num}}">إجابة صحيحة</label>
                                    </div>
                                </div>
                                <textarea class="form-control border-0 bg-light mb-2" name="ch_{{$num}}" rows="2" placeholder="اكتب الخيار هنا..."></textarea>
                                <input type="file" name="ch_{{$num}}_img" class="form-control form-control-sm" accept="image/*">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div id="true_false_section" class="question-section hidden-section">
                    <div class="tf-container">
                        <label class="w-100">
                            <input type="radio" name="correct_answer_true_false" value="صح" class="hidden-section" id="radio_true" checked>
                            <div class="tf-card true-variant">
                                <i class="fas fa-check-circle text-success"></i>
                                <h4 class="fw-bold text-success">صواب (True)</h4>
                            </div>
                        </label>
                        
                        <label class="w-100">
                            <input type="radio" name="correct_answer_true_false" value="غلط" class="hidden-section" id="radio_false">
                            <div class="tf-card false-variant">
                                <i class="fas fa-times-circle text-danger"></i>
                                <h4 class="fw-bold text-danger">خطأ (False)</h4>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mt-5 d-flex gap-3">
                    <button type="submit" class="btn btn-success btn-lg px-5 rounded-pill shadow">
                        <i class="fas fa-save me-2"></i> حفظ السؤال الآن
                    </button>
                    <button type="reset" class="btn btn-light btn-lg px-4 rounded-pill border">تفريغ الحقول</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('question_type');
    const mcSection = document.getElementById('multiple_choice_section');
    const tfSection = document.getElementById('true_false_section');
    const mainImg = document.getElementById('main_img');
    const mainPreview = document.getElementById('main_preview');

    // تبديل نوع السؤال
    typeSelect.addEventListener('change', function() {
        if (this.value === 'true_false') {
            mcSection.classList.add('hidden-section');
            tfSection.classList.remove('hidden-section');
        } else {
            mcSection.classList.remove('hidden-section');
            tfSection.classList.add('hidden-section');
        }
    });

    // معاينة الصورة الرئيسية
    mainImg.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                mainPreview.querySelector('img').src = e.target.result;
                mainPreview.classList.remove('hidden-section');
            }
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endsection