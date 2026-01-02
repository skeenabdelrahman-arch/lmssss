@extends('back_layouts.master')

@section('title') إدارة الامتحانات @stop

@section('css')
<style>
    /* كروت الإحصائيات */
    .exam-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .stat-item {
        background: #fff; padding: 1.25rem; border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 15px;
    }
    .stat-icon {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
    }
    
    /* ستايل الجدول */
    .modern-table thead th {
        background: #f8fafc; color: #64748b; text-transform: uppercase;
        font-size: 0.75rem; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0;
    }
    .exam-title-cell { min-width: 200px; }
    .exam-main-title { font-weight: 700; color: #1e293b; display: block; margin-bottom: 2px; }
    .exam-subtitle { font-size: 0.8rem; color: #94a3b8; }
    
    .badge-time { 
        background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5;
        padding: 4px 8px; border-radius: 6px; font-weight: 600; font-size: 0.8rem;
    }

    /* أزرار العمليات */
    .action-btn-group .btn {
        width: 35px; height: 35px; border-radius: 10px;
        display: inline-flex; align-items: center; justify-content: center;
        transition: all 0.2s; border: none; padding: 0;
    }
    .btn-view-public { background: #ecfdf5; color: #059669; }
    .btn-chart { background: #eff6ff; color: #2563eb; }
    .btn-edit-exam { background: #f5f3ff; color: #7c3aed; }
    .btn-delete-exam { background: #fff1f2; color: #e11d48; }
    .btn-add-ques { background: #fffbeb; color: #d97706; }
    
    .action-btn-group .btn:hover { transform: translateY(-3px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
</style>
@endsection

@section('page-header')
<div class="page-header-modern">
    <h4><i class="fas fa-book-open me-2 text-primary"></i> بنك الامتحانات</h4>
</div>
@endsection

@section('content')
<div class="container-fluid">
    
    <div class="exam-stats">
        <div class="stat-item">
            <div class="stat-icon bg-primary-subtle text-primary"><i class="fas fa-file-signature"></i></div>
            <div>
                <span class="text-muted small d-block">إجمالي الامتحانات</span>
                <span class="fw-bold fs-5">{{ $exam_names->count() }}</span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon bg-success-subtle text-success"><i class="fas fa-check-double"></i></div>
            <div>
                <span class="text-muted small d-block">امتحانات مفعلة</span>
                <span class="fw-bold fs-5">{{ $exam_names->where('status', 1)->count() }}</span>
            </div>
        </div>
    </div>

    <div class="modern-card shadow-sm border-0 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div class="d-flex gap-2">
                <a href="{{ route('exam_name.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm fw-bold">
                    <i class="fas fa-plus me-2"></i> إضافة امتحان جديد
                </a>
                <a onclick="return confirm('هل انت متأكد من حذف جميع الامتحانات؟ لا يمكن التراجع عن هذا الإجراء!')" 
                   href="{{route('deleteAllExams')}}" class="btn btn-outline-danger px-4 py-2 rounded-3 fw-bold">
                    <i class="fas fa-trash-alt me-2"></i> تصفير الامتحانات
                </a>
            </div>
            
            @if(session()->has('success') || session()->has('error'))
                <div class="ms-auto">
                    @if(session()->has('success'))
                        <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> {{ session('success') }}</span>
                    @else
                        <span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}</span>
                    @endif
                </div>
            @endif
        </div>

        <div class="table-responsive">
            <table id="datatable" class="table table-hover align-middle modern-table">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>تفاصيل الامتحان</th>
                        <th class="text-center">التصنيف الدراسي</th>
                        <th class="text-center">الوقت</th>
                        <th class="text-center">الترتيب</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">العمليات الإدارية</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($exam_names as $exam_name)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                            <td class="exam-title-cell">
                                <span class="exam-main-title">{{ $exam_name->exam_title }}</span>
                                <span class="exam-subtitle">{{ Str::limit($exam_name->exam_description, 40) ?: 'لا يوجد وصف' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small fw-bold">
                                    {{ $exam_name->grade }}
                                </span>
                                <div class="small text-muted mt-1">{{ $exam_name->month ? $exam_name->month->name : 'شهر محذوف' }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge-time">
                                    <i class="far fa-clock me-1"></i> {{ $exam_name->exam_time }} دقيقة
                                </span>
                            </td>
                            <td class="text-center">
                                <input type="number" class="form-control form-control-sm mx-auto text-center fw-bold shadow-none" 
                                       style="width: 65px; border-radius: 8px; border: 2px solid #f1f5f9;" 
                                       value="{{ $exam_name->display_order ?? 0 }}" 
                                       onchange="updateDisplayOrder('exam', {{ $exam_name->id }}, this.value)"
                                       min="0">
                            </td>
                            <td class="text-center">
                                @if($exam_name->status == 1)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill small">مفعل</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill small">معطل</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="action-btn-group d-flex gap-1 justify-content-center">
                                    @if($exam_name->public_access == 1)
                                        <a href="{{ route('publicExam.take', $exam_name->id) }}" target="_blank" class="btn btn-view-public" title="رابط الامتحان العام">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <a href="{{ route('publicExam.results.exam', $exam_name->id) }}" class="btn btn-chart" title="نتائج الإحصائيات">
                                            <i class="fas fa-chart-pie"></i>
                                        </a>
                                    @endif
                                    <a href="{{url('add-question/'.$exam_name->id)}}" class="btn btn-add-ques" title="إضافة أسئلة">
                                        <i class="fas fa-plus-circle"></i>
                                    </a>
                                    <a href="{{ route('exam_name.edit', $exam_name->id) }}" class="btn btn-edit-exam" title="تعديل الإعدادات">
                                        <i class="fas fa-cog"></i>
                                    </a>
                                    <a onclick="return confirm('حذف الامتحان سيؤدي لحذف الأسئلة والنتائج المرتبطة به. هل أنت متأكد؟')" 
                                       href="{{url('exam/delete/'.$exam_name->id)}}" class="btn btn-delete-exam" title="حذف">
                                        <i class="fas fa-trash-alt"></i>
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

@section('js')
<script>
    function updateDisplayOrder(type, id, order) {
        $.ajax({
            url: "{{ route('admin.content.updateOrder') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                type: type,
                id: id,
                display_order: order
            },
            success: function(response) {
                // استخدام Toastr أو إشعار بسيط بدلاً من الـ alert التقليدي
                console.log('Order updated successfully');
            },
            error: function() {
                alert('حدث خطأ أثناء تحديث الترتيب');
            }
        });
    }
</script>
@endsection