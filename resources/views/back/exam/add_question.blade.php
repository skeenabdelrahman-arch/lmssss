@extends('back_layouts.master')

@section('title') بنك الأسئلة @stop

@section('css')
<style>
    /* تنسيق معاينة الصورة */
    .img-preview-container {
        position: relative; width: 55px; height: 55px; margin: auto;
    }
    .question-img-sm {
        width: 55px; height: 55px; object-fit: cover;
        border-radius: 10px; border: 2px solid #f1f5f9;
        transition: all 0.3s ease-in-out; cursor: zoom-in;
    }
    .question-img-sm:hover { 
        transform: scale(3.5); z-index: 1000; position: relative; 
        box-shadow: 0 10px 25px rgba(0,0,0,0.2); border-color: #6366f1;
    }

    /* تقسيم الاختيارات الأربعة */
    .choices-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 8px; min-width: 350px;
    }
    .choice-item {
        font-size: 0.85rem; background: #fdfdfd; padding: 6px 10px;
        border-radius: 8px; border: 1px solid #edf2f7;
        color: #475569; transition: 0.2s;
    }
    .choice-item:hover { background: #f8fafc; border-color: #cbd5e1; }
    .choice-prefix { font-weight: 800; color: #6366f1; margin-left: 5px; }
    
    .correct-ans-badge {
        background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;
        padding: 5px 12px; border-radius: 12px; font-weight: 700; font-size: 0.8rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="modern-card shadow-sm border-0 bg-white rounded-4 p-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i class="fas fa-layer-group text-primary me-2"></i> إدارة أسئلة الامتحان
                </h5>
                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold">
                    {{ App\Models\ExamName::find($exam_id)->exam_title }}
                </span>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ url('add-question/'.$exam_id.'/create') }}" class="btn btn-primary px-4 py-2 rounded-3 fw-bold">
                    <i class="fas fa-plus-circle me-1"></i> إضافة سؤال
                </a>

                <div class="dropdown">
                    <button class="btn btn-outline-danger px-4 py-2 rounded-3 fw-bold dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-file-pdf me-1"></i> تصدير الامتحان
                    </button>
                    <ul class="dropdown-menu shadow-lg border-0 rounded-3">
                        <li><a class="dropdown-item py-2 fw-bold" href="{{ route('exam.questions.pdf', $exam_id) }}" target="_blank">
                            <i class="fas fa-check-double text-success me-2"></i> نسخة (بالإجابات)
                        </a></li>
                        <li><a class="dropdown-item py-2 fw-bold" href="{{ route('exam.questions.pdf.without.answers', $exam_id) }}" target="_blank">
                            <i class="fas fa-file-alt text-primary me-2"></i> نسخة (بدون إجابات)
                        </a></li>
                    </ul>
                </div>

                <a onclick="return confirm('⚠️ هل أنت متأكد من حذف جميع الأسئلة؟ لا يمكن التراجع!')" 
                   href="{{route('deleteAllQuestions')}}" class="btn btn-light text-danger border px-4 py-2 rounded-3 fw-bold">
                    <i class="fas fa-trash-alt me-1"></i> حذف الكل
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table id="datatable" class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="text-center" style="width: 40px;">#</th>
                        <th style="width: 20%;">السؤال</th>
                        <th style="width: 40%;">الاختيارات الأربعة</th>
                        <th class="text-center">صورة</th>
                        <th class="text-center">الإجابة</th>
                        <th class="text-center">الدرجة</th>
                        <th class="text-center">العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($exam_questions as $exam_question)
                    <tr>
                        <td class="text-center fw-bold text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-bold text-dark lh-base">{{ $exam_question->question_title }}</div>
                        </td>
                        <td>
                            <div class="choices-grid">
                                <div class="choice-item"><span class="choice-prefix">أ:</span> {{ $exam_question->ch_1 }}</div>
                                <div class="choice-item"><span class="choice-prefix">ب:</span> {{ $exam_question->ch_2 }}</div>
                                <div class="choice-item"><span class="choice-prefix">ج:</span> {{ $exam_question->ch_3 }}</div>
                                <div class="choice-item"><span class="choice-prefix">د:</span> {{ $exam_question->ch_4 }}</div>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($exam_question->img)
                                <div class="img-preview-container">
                                    <img src="{{url('upload_files/'.$exam_question->img)}}" class="question-img-sm shadow-sm">
                                </div>
                            @else
                                <span class="text-muted small">---</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="correct-ans-badge">
                                <i class="fas fa-check-circle me-1 text-success"></i> {{ $exam_question->correct_answer }}
                            </span>
                        </td>
                        <td class="text-center fw-bold text-primary">{{ $exam_question->Q_degree }} د</td>
                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ url('add-question/'.$exam_id.'/edit/'.$exam_question->id) }}" class="btn btn-sm btn-outline-info rounded-2" title="تعديل">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a onclick="return confirm('حذف السؤال؟')" href="{{url('question/delete/'.$exam_question->id)}}" class="btn btn-sm btn-outline-danger rounded-2" title="حذف">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection