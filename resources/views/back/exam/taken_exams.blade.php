@extends('back_layouts.master')
@section('css')
<style>
    .exam-table-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        margin-bottom: 30px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .exam-table-card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        transform: translateY(-5px);
    }
    
    .exam-header-card {
        background: linear-gradient(135deg, #7424a9 0%, #fa896b 100%);
        color: white;
        padding: 25px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .exam-header-card h4 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .exam-stats {
        display: flex;
        gap: 20px;
        align-items: center;
    }
    
    .stat-badge {
        background: rgba(255,255,255,0.2);
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.9rem;
    }
    
    .modern-table {
        width: 100%;
        margin: 0;
    }
    
    .modern-table thead {
        background: #f8f9fa;
    }
    
    .modern-table thead th {
        padding: 15px;
        font-weight: 700;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        text-align: center;
    }
    
    .modern-table tbody td {
        padding: 15px;
        text-align: center;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }
    
    .modern-table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .degree-badge {
        display: inline-block;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 1rem;
    }
    
    .degree-badge.success {
        background: #d4edda;
        color: #155724;
    }
    
    .degree-badge.warning {
        background: #fff3cd;
        color: #856404;
    }
    
    .degree-badge.danger {
        background: #f8d7da;
        color: #721c24;
    }
    
    .action-buttons {
        display: flex;
        gap: 5px;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .btn-action {
        padding: 8px 15px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .btn-view {
        background: #007bff;
        color: white;
    }
    
    .btn-edit {
        background: #ffc107;
        color: #212529;
    }
    
    .btn-delete {
        background: #dc3545;
        color: white;
    }
    
    .no-exams {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    
    .no-exams i {
        font-size: 4rem;
        margin-bottom: 20px;
        color: #dee2e6;
    }
    
    .time-spent-badge {
        background: #e7f3ff;
        color: #0066cc;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    /* Responsive Design */
    @media (max-width: 992px) {
        .exam-header-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .exam-stats {
            width: 100%;
            justify-content: space-between;
        }
        
        .d-flex.gap-2 {
            flex-direction: column;
            width: 100%;
        }
        
        .input-group {
            flex-direction: column;
            gap: 10px;
        }
        
        .input-group select,
        .input-group button {
            width: 100% !important;
            max-width: 100% !important;
        }
    }
    
    @media (max-width: 768px) {
        .exam-header-card {
            padding: 15px;
        }
        
        .exam-header-card h4 {
            font-size: 1.2rem;
        }
        
        .stat-badge {
            padding: 6px 10px;
            font-size: 0.8rem;
        }
        
        .modern-table {
            font-size: 12px;
        }
        
        .modern-table thead th,
        .modern-table tbody td {
            padding: 8px 5px;
        }
        
        .degree-badge {
            padding: 5px 10px;
            font-size: 0.85rem;
        }
        
        .action-buttons .btn {
            padding: 5px 10px;
            font-size: 11px;
        }
        
        .mb-4.d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .mb-4.d-flex > div {
            width: 100%;
        }
        
        form.d-flex {
            flex-direction: column;
        }
        
        form.d-flex input[type="search"] {
            min-width: 100% !important;
            margin-bottom: 10px;
        }
    }
    
    @media (max-width: 576px) {
        .exam-table-card {
            margin-bottom: 20px;
        }
        
        .modern-table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
        
        .time-spent-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
        }
    }
</style>
@endsection

@section('title')
    الامتحانات الممتحنة
@endsection

@section('page-header')
<div class="page-header-modern">
    <h4><i class="fas fa-clipboard-list me-2"></i> الامتحانات الممتحنة</h4>
</div>
@endsection

@section('content')
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

    <!-- Header Stats -->
    <div class="mb-4">
        <div class="row align-items-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <h5 class="mb-1"><i class="fas fa-chart-bar me-2"></i>إجمالي الامتحانات: <strong>{{ $taken_exams->count() }}</strong></h5>
                <small class="text-muted">عدد الامتحانات المختلفة: <strong>{{ $exams_grouped->count() }}</strong></small>
            </div>
            <div class="col-md-6">
                <!-- Search form -->
                <form method="get" action="{{ url('show-taken-exams') }}" class="d-flex gap-2">
                    <input type="search" name="q" value="{{ isset($q) ? e($q) : '' }}" 
                           placeholder="ابحث باسم الطالب أو الامتحان..." 
                           class="form-control" />
                    <button type="submit" class="btn btn-modern btn-modern-primary" style="white-space: nowrap;">
                        <i class="fas fa-search me-1"></i>بحث
                    </button>
                    @if(isset($q) && $q !== '')
                        <a href="{{ url('show-taken-exams') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mb-4">
        <div class="row g-3">
            <!-- إظهار الدرجات -->
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="fas fa-eye text-primary me-2"></i>إظهار الدرجات
                        </h6>
                        <form action="{{route('showAllDegrees')}}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <select name="exam_id" class="form-select" required>
                                    <option value="">-- اختر الامتحان --</option>
                                    @foreach($exams_grouped as $exam_id => $results)
                                        <option value="{{ $exam_id }}">{{ $results->first()->exam->exam_title ?? 'امتحان غير معروف' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-eye me-2"></i>عرض الدرجات
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- تصدير Excel -->
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="fas fa-file-excel text-success me-2"></i>تصدير Excel
                        </h6>
                        <form action="{{route('exportExamDegrees')}}" method="GET">
                            <div class="mb-2">
                                <select name="exam_id" class="form-select" required>
                                    <option value="">-- اختر الامتحان --</option>
                                    @foreach($exams_grouped as $exam_id => $results)
                                        <option value="{{ $exam_id }}">{{ $results->first()->exam->exam_title ?? 'امتحان غير معروف' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-download me-2"></i>تحميل Excel
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- حذف جميع النتائج -->
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="fas fa-trash-alt text-danger me-2"></i>حذف جميع النتائج
                        </h6>
                        <form action="{{route('deleteExamResults')}}" method="POST" 
                              onsubmit="return confirm('⚠️ هل أنت متأكد من حذف جميع نتائج هذا الامتحان؟\n\nهذا الإجراء لا يمكن التراجع عنه!')">
                            @csrf
                            @method('DELETE')
                            <div class="mb-2">
                                <select name="exam_id" class="form-select" required>
                                    <option value="">-- اختر الامتحان --</option>
                                    @foreach($exams_grouped as $exam_id => $results)
                                        <option value="{{ $exam_id }}">{{ $results->first()->exam->exam_title ?? 'امتحان غير معروف' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash-alt me-2"></i>حذف النتائج
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($exams_grouped->count() > 0)
        @foreach($exams_grouped as $exam_id => $exam_results)
            @php
                $exam = $exam_results->first()->exam;
                
                // تخطي الامتحانات المحذوفة
                if (!$exam) {
                    continue;
                }
                
                $totalDegree = 0;
                foreach($exam->questions as $question) {
                    $totalDegree += (float)$question->Q_degree;
                }
                $avgDegree = $exam_results->avg('degree');
                $maxDegree = $exam_results->max('degree');
                $minDegree = $exam_results->min('degree');
            @endphp
            
            <div class="exam-table-card">
                <div class="exam-header-card">
                    <div>
                        <h4><i class="fas fa-file-alt me-2"></i>{{ $exam->exam_title ?? 'امتحان غير معروف' }}</h4>
                        <small style="opacity: 0.9;">{{ $exam_results->count() }} طالب/طالبة</small>
                    </div>
                    <div class="exam-stats">
                        <div class="stat-badge">
                            <i class="fas fa-users me-1"></i>{{ $exam_results->count() }} طالب
                        </div>
                        <div class="stat-badge">
                            <i class="fas fa-chart-line me-1"></i>متوسط: {{ number_format($avgDegree, 2) }}
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم الطالب</th>
                                <th>النوع</th>
                                <th>السنة الدراسية</th>
                                <th>الدرجة</th>
                                <th>النسبة</th>
                                <th>وقت الجلوس</th>
                                <th>التاريخ</th>
                                <th>العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exam_results as $taken_exam)
                                @php
                                    $percentage = $totalDegree > 0 ? round(($taken_exam->degree / $totalDegree) * 100, 2) : 0;
                                    $badgeClass = 'success';
                                    if ($percentage < 50) {
                                        $badgeClass = 'danger';
                                    } elseif ($percentage < 70) {
                                        $badgeClass = 'warning';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $taken_exam->student->first_name }} {{ $taken_exam->student->second_name }} {{ $taken_exam->student->third_name }} {{ $taken_exam->student->forth_name }}</strong>
                                    </td>
                                    <td>
                                        @if($taken_exam->student->gender == 'ذكر')
                                            <span class="badge bg-primary">ذكر</span>
                                        @else
                                            <span class="badge bg-pink">أنثى</span>
                                        @endif
                                    </td>
                                    <td>{{ $taken_exam->student->grade ?? '-' }}</td>
                                    <td>
                                        <span class="degree-badge {{ $badgeClass }}">
                                            {{ $taken_exam->degree }} / {{ $totalDegree }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong style="color: {{ $percentage >= 70 ? '#28a745' : ($percentage >= 50 ? '#ffc107' : '#dc3545') }};">
                                            {{ $percentage }}%
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info" style="font-size: 0.9rem; padding: 6px 12px;">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $taken_exam->time_spent_formatted ?? 'غير متاح' }}
                                        </span>
                                    </td>
                                    <td>{{ $taken_exam->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('exam_review', ['exam_id' => $taken_exam->exam_id, 'student_id' => $taken_exam->student_id]) }}" 
                                               class="btn-action btn-view" title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button data-toggle="modal" data-target="#edit{{$taken_exam->id}}" 
                                                    class="btn-action btn-edit" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a onclick="return confirm('هل أنت متأكد من حذف هذا الامتحان؟')" 
                                               href="{{ url('delete-taken-exam/'. $taken_exam->id) }}" 
                                               class="btn-action btn-delete" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Edit Modal --}}
                                <div class="modal fade" id="edit{{$taken_exam->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">
                                                    <i class="fas fa-edit me-2"></i>تعديل درجة الامتحان
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{route('exam_degree.update',$taken_exam->exam_id)}}" method="POST">
                                                    @csrf
                                                    <div class="card-body">
                                                        <div class="row mb-3">
                                                            <div class="col-12">
                                                                <h5>اسم الامتحان: <span class="text-info">{{$taken_exam->exam->exam_title}}</span></h5>
                                                                <h5>اسم الطالب: <span class="text-info">{{$taken_exam->student->first_name}} {{$taken_exam->student->second_name}} {{$taken_exam->student->third_name}}</span></h5>
                                                            </div>
                                                            <div class="col-12 mb-3">
                                                                <label for="degree" class="form-label">الدرجة الجديدة:</label>
                                                                <input class="form-control" type="number" name="degree" value="{{$taken_exam->degree}}" step="0.5" min="0" max="{{$totalDegree}}" required/>
                                                                <small class="text-muted">الدرجة الكلية: {{$totalDegree}}</small>
                                                            </div>
                                                            <div class="col-12">
                                                                <label class="form-label">إظهار الدرجة للطالب:</label>
                                                                <div class="form-check">
                                                                    <input type="checkbox" class="form-check-input" name="show_degree" value="1" 
                                                                           {{ $taken_exam->show_degree == 1 ? 'checked' : '' }} 
                                                                           style="width: 20px; height: 20px;">
                                                                    <label class="form-check-label">إظهار الدرجة</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fas fa-save me-2"></i>تعديل
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @else
        <div class="no-exams">
            <i class="fas fa-clipboard-list"></i>
            <h4>لا توجد امتحانات ممتحنة</h4>
            <p class="text-muted">لم يتم إجراء أي امتحانات حتى الآن</p>
        </div>
    @endif
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Smooth scroll for exam cards
    $('.exam-table-card').each(function(index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
    });
});
</script>
@endsection
