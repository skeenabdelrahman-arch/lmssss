@extends('back_layouts.master')
@section('title')
    مراجعة الامتحان
@endsection
@section('page-header')
<div class="page-header-modern">
    <h4><i class="fas fa-clipboard-check me-2"></i> مراجعة إجابات الطالب</h4>
</div>
@endsection

@section('content')
<style>
    .exam-review-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        padding: 30px;
        margin-bottom: 30px;
    }
    
    .exam-header-card {
        background: linear-gradient(135deg, #7424a9 0%, #fa896b 100%);
        color: white;
        padding: 25px 30px;
        border-radius: 15px;
        margin-bottom: 30px;
    }
    
    .exam-header-card h2 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 700;
    }
    
    .result-badge {
        display: inline-block;
        background: rgba(255,255,255,0.2);
        padding: 10px 20px;
        border-radius: 25px;
        margin-top: 15px;
        font-size: 1.2rem;
        font-weight: 700;
    }
    
    .question-card {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .question-card:hover {
        border-color: #7424a9;
        box-shadow: 0 5px 15px rgba(116, 36, 169, 0.1);
    }
    
    .question-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #dee2e6;
    }
    
    .question-number {
        background: #7424a9;
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
    
    .question-title {
        flex: 1;
        margin: 0 15px;
        font-size: 1.1rem;
        font-weight: 600;
        color: #495057;
    }
    
    .question-degree {
        background: #ffc107;
        color: #212529;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: 700;
    }
    
    .question-options {
        list-style: none;
        padding: 0;
        margin: 20px 0;
    }
    
    .question-options li {
        padding: 12px 15px;
        margin-bottom: 10px;
        background: white;
        border-radius: 8px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .answer-section {
        margin-top: 20px;
        padding: 15px;
        background: white;
        border-radius: 10px;
        border-right: 4px solid #007bff;
    }
    
    .student-answer {
        color: #dc3545;
        font-weight: 700;
        font-size: 1.1rem;
    }
    
    .correct-answer {
        color: #28a745;
        font-weight: 700;
        font-size: 1.1rem;
    }
    
    .answer-status {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 700;
        margin-right: 10px;
    }
    
    .answer-status.correct {
        background: #d4edda;
        color: #155724;
    }
    
    .answer-status.wrong {
        background: #f8d7da;
        color: #721c24;
    }
    
    .edit-degree-modal .modal-content {
        border-radius: 15px;
    }
    
    .edit-degree-modal .modal-header {
        background: linear-gradient(135deg, #7424a9 0%, #fa896b 100%);
        color: white;
        border-radius: 15px 15px 0 0;
    }
</style>

<div class="modern-card">
    @if(session()->has('error'))
        <div class="alert alert-modern alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><strong>{{ session()->get('error') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif(session()->has('success'))
        <div class="alert alert-modern alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><strong>{{ session()->get('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="exam-review-container">
        <div class="exam-header-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h2><i class="fas fa-file-alt me-2"></i>{{ $exam_name->exam_title }}</h2>
                    <p class="mb-0" style="opacity: 0.9;">
                        <i class="fas fa-user me-2"></i>
                        {{ $f_name }} {{ $s_name }} {{ $t_name }}
                    </p>
                </div>
                <div class="text-end">
                    <div class="result-badge">
                        <i class="fas fa-star me-2"></i>
                        النتيجة: {{ $exam_result->degree ?? 0 }} / {{ $exam_degree }}
                        @if($exam_degree > 0)
                            <span style="font-size: 0.9rem; opacity: 0.9;">
                                ({{ number_format((($exam_result->degree ?? 0) / $exam_degree) * 100, 2) }}%)
                            </span>
                        @endif
                    </div>
                    @if($exam_result)
                    <div class="mt-3">
                        <button type="button" class="btn btn-modern btn-modern-warning" data-bs-toggle="modal" data-bs-target="#editDegreeModal">
                            <i class="fas fa-edit me-2"></i>تعديل الدرجة
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @foreach ($exam_questions as $q)
            @php
                $student_answer = App\Models\ExamAnswer::where('student_id', $student_id)
                                    ->where('exam_id', $exam_name->id)
                                    ->where('question_id', $q->id)
                                    ->first();
                $questionType = $q->question_type ?? 'multiple_choice';
                $isCorrect = $student_answer && $student_answer->student_answer == $q->correct_answer;
            @endphp
            
            <div class="question-card">
                <div class="question-header">
                    <div class="question-number">{{ $loop->iteration }}</div>
                    <div class="question-title">
                        @if($q->question_title)
                            {{ $q->question_title }}
                        @elseif($q->img)
                            <img src="{{ url('upload_files/'.$q->img) }}" style="max-width: 100%; border-radius: 10px;">
                        @endif
                    </div>
                    <div class="question-degree">
                        <i class="fas fa-star me-1"></i>{{ $q->Q_degree }} درجة
                    </div>
                </div>

                @if($questionType === 'true_false')
                    {{-- أسئلة صح/غلط --}}
                    <div class="answer-section">
                        <div class="mb-3">
                            <span class="answer-status {{ $isCorrect ? 'correct' : 'wrong' }}">
                                <i class="fas {{ $isCorrect ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                {{ $isCorrect ? 'إجابة صحيحة' : 'إجابة خاطئة' }}
                            </span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>إجابة الطالب:</strong>
                                <span class="student-answer">
                                    {{ $student_answer->student_answer ?? 'لم يُجب' }}
                                </span>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>الإجابة الصحيحة:</strong>
                                <span class="correct-answer">{{ $q->correct_answer }}</span>
                            </div>
                        </div>
                    </div>
                @elseif($q->ch_1 || $q->ch_2 || $q->ch_3 || $q->ch_4)
                    {{-- أسئلة اختيار من متعدد --}}
                    <ul class="question-options">
                        @foreach(['ch_1', 'ch_2', 'ch_3', 'ch_4'] as $choice)
                            @if($q->$choice)
                                @php
                                    $isSelected = $student_answer && $student_answer->student_answer == $q->$choice;
                                    $isCorrectChoice = $q->$choice == $q->correct_answer;
                                    $optionClass = '';
                                    if ($isCorrectChoice) {
                                        $optionClass = 'border-success bg-light';
                                    } elseif ($isSelected && !$isCorrectChoice) {
                                        $optionClass = 'border-danger bg-light';
                                    }
                                @endphp
                                <li class="{{ $optionClass }}" style="border: 2px solid {{ $isCorrectChoice ? '#28a745' : ($isSelected ? '#dc3545' : '#e9ecef') }};">
                                    @if($isCorrectChoice)
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                    @endif
                                    @if($isSelected && !$isCorrectChoice)
                                        <i class="fas fa-times-circle text-danger me-2"></i>
                                    @endif
                                    {{ $q->$choice }}
                                    @if($isCorrectChoice)
                                        <span class="badge bg-success ms-2">الإجابة الصحيحة</span>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    </ul>
                    
                    <div class="answer-section">
                        <div class="mb-3">
                            <span class="answer-status {{ $isCorrect ? 'correct' : 'wrong' }}">
                                <i class="fas {{ $isCorrect ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                {{ $isCorrect ? 'إجابة صحيحة' : 'إجابة خاطئة' }}
                            </span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>إجابة الطالب:</strong>
                                <span class="student-answer">
                                    {{ $student_answer->student_answer ?? 'لم يُجب' }}
                                </span>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>الإجابة الصحيحة:</strong>
                                <span class="correct-answer">{{ $q->correct_answer }}</span>
                            </div>
                        </div>
                    </div>
                @elseif($q->img && !$q->ch_1)
                    {{-- أسئلة بصور --}}
                    <div class="answer-section">
                        <p><strong>إجابة الطالب:</strong></p>
                        @if($student_answer && $student_answer->student_answer)
                            <img src="{{ url('upload_files/student_answer/'.$f_name.'_'.$s_name.'_'.$t_name.'/'.$student_answer->student_answer) }}" 
                                 style="max-width: 100%; border-radius: 10px; border: 2px solid #dee2e6;">
                        @else
                            <p class="text-muted">لم يُجب الطالب</p>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

@if($exam_result)
<!-- Modal تعديل الدرجة -->
<div class="modal fade edit-degree-modal" id="editDegreeModal" tabindex="-1" role="dialog" aria-labelledby="editDegreeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDegreeModalLabel">
                    <i class="fas fa-edit me-2"></i>تعديل درجة الامتحان
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('exam_degree.update', $exam_result->id) }}" method="POST" id="editDegreeForm">
                    @csrf
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12">
                                <h5>اسم الامتحان: <span class="text-info">{{ $exam_name->exam_title }}</span></h5>
                                <h5>اسم الطالب: <span class="text-info">{{ $f_name }} {{ $s_name }} {{ $t_name }}</span></h5>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="degree" class="form-label">الدرجة الجديدة:</label>
                                <input class="form-control form-control-lg" type="number" name="degree" id="degree" 
                                       value="{{ $exam_result->degree }}" step="0.5" min="0" max="{{ $exam_degree }}" required/>
                                <small class="text-muted">الدرجة الكلية: {{ $exam_degree }}</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">إظهار الدرجة للطالب:</label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="show_degree" value="1" id="show_degree"
                                           {{ $exam_result->show_degree == 1 ? 'checked' : '' }} 
                                           style="width: 20px; height: 20px;">
                                    <label class="form-check-label" for="show_degree">إظهار الدرجة للطالب</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-modern btn-modern-success">
                            <i class="fas fa-save me-2"></i>حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endif

@section('js')
<script>
$(document).ready(function() {
    // التأكد من أن الـ modal يعمل
    $('#editDegreeModal').on('show.bs.modal', function (event) {
        console.log('Modal opening');
    });
    
    // معالجة إرسال النموذج
    $('#editDegreeForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = form.serialize();
        var url = form.attr('action');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                // إغلاق الـ modal
                $('#editDegreeModal').modal('hide');
                // إعادة تحميل الصفحة لعرض الرسالة
                location.reload();
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                alert('حدث خطأ أثناء حفظ التعديلات. يرجى المحاولة مرة أخرى.');
            }
        });
    });
});
</script>
@endsection

@endsection
