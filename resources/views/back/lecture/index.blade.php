@extends('back_layouts.master')

@section('title') إدارة المحاضرات @stop

@section('css')
<style>
    /* التنسيقات العامة للوحة المحاضرات */
    .page-header-modern { background: #fff; padding: 20px; border-radius: 15px; margin-bottom: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); border: 1px solid #f1f5f9; }
    .modern-card { background: #fff; border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: none; }
    
    /* تنسيق الجدول */
    .table-modern thead th { background: #f8fafc; color: #64748b; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; padding: 18px 15px; border-bottom: 2px solid #edf2f7; }
    .table-modern tbody td { padding: 15px; vertical-align: middle; color: #334155; font-size: 14px; border-bottom: 1px solid #f1f5f9; }
    .table-hover tbody tr:hover { background-color: #fcfcfd; }

    /* الأزرار والبادجات */
    .btn-action { width: 35px; height: 35px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; transition: 0.3s; border: none; }
    .btn-action:hover { transform: translateY(-3px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    
    .status-badge { padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; }
    .status-active { background: #dcfce7; color: #15803d; }
    .status-inactive { background: #fee2e2; color: #b91c1c; }

    /* شريط العمليات الجماعية العائم */
    .bulk-toolbar {
        position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%);
        background: #1e293b; color: #fff; padding: 15px 30px; border-radius: 50px;
        display: flex; align-items: center; gap: 15px; z-index: 1000;
        box-shadow: 0 15px 35px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1);
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    
    <div class="page-header-modern d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-chalkboard-teacher text-primary me-2"></i> المحاضرات الدراسية</h4>
            <p class="text-muted small mb-0">إدارة ورفع المحاضرات، الكويزات، ومتابعة الطلاب</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('lecture.create') }}" class="btn btn-primary fw-bold px-4 rounded-pill">
                <i class="fas fa-plus me-2"></i> إضافة محاضرة جديدة
            </a>
            <a onclick="return confirm('هل انت متأكد من حذف جميع المحاضرات ؟ ')" href="{{route('deleteAllLectures')}}" class="btn btn-outline-danger fw-bold px-4 rounded-pill">
                <i class="fas fa-trash-alt me-2"></i> حذف الكل
            </a>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i> {{ session()->get('success') }}
        </div>
    @endif

    <div class="modern-card">
        <div class="table-responsive">
            <table id="datatable" class="table table-modern table-hover mb-0">
                <thead>
                    <tr>
                        <th width="40">
                            <input type="checkbox" class="form-check-input" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th>المحاضرة</th>
                        <th>التصنيف</th>
                        <th class="text-center">المشاهدات</th>
                        <th class="text-center">الترتيب</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lectures as $lecture)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input lecture-checkbox" value="{{ $lecture->id }}" onchange="updateBulkActions()">
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $lecture->title }}</div>
                            <div class="text-muted small text-truncate" style="max-width: 200px;">{{ $lecture->description ?: 'لا يوجد وصف' }}</div>
                        </td>
                        <td>
                            <div class="mb-1"><span class="badge bg-soft-primary text-primary px-2 py-1 rounded">{{ $lecture->grade }}</span></div>
                            <div><span class="text-muted small"><i class="far fa-calendar-alt me-1"></i> {{ $lecture->month ? $lecture->month->name : 'غير محدد' }}</span></div>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold text-dark"><i class="far fa-eye text-info me-1"></i> {{ $lecture->views }}</span>
                        </td>
                        <td class="text-center">
                            <input type="number" class="form-control form-control-sm mx-auto text-center" 
                                   style="width: 70px; border-radius: 8px;" 
                                   value="{{ $lecture->display_order ?? 0 }}" 
                                   onchange="updateDisplayOrder('lecture', {{ $lecture->id }}, this.value)">
                        </td>
                        <td class="text-center">
                            @if($lecture->status == 1)
                                <span class="status-badge status-active">مفعلة</span>
                            @else
                                <span class="status-badge status-inactive">معطلة</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('lecture.edit', $lecture->id) }}" class="btn-action bg-light text-primary" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.quiz.show', $lecture->id) }}" class="btn-action bg-light text-warning" title="الكويز">
                                    <i class="fas fa-question-circle"></i>
                                </a>
                                <a href="{{ route('admin.views.lecture.students', $lecture->id) }}" class="btn-action bg-light text-success" title="المشاهدات">
                                    <i class="fas fa-users"></i>
                                </a>
                                <a onclick="return confirm('هل انت متأكد من الحذف؟')" href="{{url('lecture/delete/'.$lecture->id)}}" class="btn-action bg-light text-danger" title="حذف">
                                    <i class="fas fa-trash"></i>
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

<div id="bulkActions" class="bulk-toolbar" style="display: none;">
    <span class="small border-end pe-3 text-white-50"><span id="selectedCount">0</span> محاضرات مختارة</span>
    <select class="form-select form-select-sm bg-dark text-white border-secondary" id="bulkActionSelect" style="width: 160px;">
        <option value="">اختر العملية...</option>
        <option value="publish">نشر (تفعيل)</option>
        <option value="unpublish">إلغاء النشر</option>
        <option value="feature">تمييز</option>
        <option value="delete">حذف نهائي</option>
    </select>
    <button class="btn btn-primary btn-sm rounded-pill px-4" onclick="performBulkAction()">
        تطبيق
    </button>
</div>

@endsection

@section('js')
<script>
    // نظام التحديد الجماعي
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.lecture-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateBulkActions();
    }
    
    function updateBulkActions() {
        const checked = document.querySelectorAll('.lecture-checkbox:checked');
        const bulkActions = document.getElementById('bulkActions');
        const countSpan = document.getElementById('selectedCount');
        
        if (checked.length > 0) {
            $(bulkActions).fadeIn();
            countSpan.innerText = checked.length;
        } else {
            $(bulkActions).fadeOut();
        }
    }
    
    // تنفيذ العمليات الجماعية
    function performBulkAction() {
        const action = document.getElementById('bulkActionSelect').value;
        const checked = Array.from(document.querySelectorAll('.lecture-checkbox:checked')).map(cb => cb.value);
        
        if (!action) { alert('يرجى اختيار عملية'); return; }
        if (!confirm(`هل أنت متأكد من تنفيذ العملية على ${checked.length} محاضرة؟`)) return;
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.bulk.update") }}';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden'; actionInput.name = 'action'; actionInput.value = action;
        form.appendChild(actionInput);
        
        checked.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden'; input.name = 'ids[]'; input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
    
    // تحديث الترتيب عبر AJAX
    function updateDisplayOrder(type, id, order) {
        $.ajax({
            url: "{{ route('admin.content.updateOrder') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                type: type, id: id, display_order: order
            },
            success: function(response) {
                // إشعار صغير (اختياري)
            }
        });
    }
</script>
@endsection