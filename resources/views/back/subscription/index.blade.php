@extends('back_layouts.master')

@section('title')
    اشتراكات الطلاب
@stop

@section('css')
<style>
    /* تنسيقات عامة للمودرن كارد */
    .filter-card {
        background: #ffffff;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }
    
    .students-section { display: none; }
    .students-section.active { display: block; animation: fadeIn 0.5s ease; }
    
    .table-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 25px;
        overflow: hidden;
        border: none;
    }
    
    .table-card-header {
        padding: 15px 20px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .table-card-header.success { background: linear-gradient(45deg, #28a745, #5dd479); }
    .table-card-header.danger { background: linear-gradient(45deg, #dc3545, #ff5f6d); }
    
    .badge-modern {
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }
    .badge-modern-success { background: #e8f5e9; color: #2e7d32; }
    .badge-modern-danger { background: #ffebee; color: #c62828; }
    .badge-modern-primary { background: #e3f2fd; color: #1565c0; }

    .loading-spinner { text-align: center; padding: 50px; }
    .loading-spinner i { font-size: 50px; color: #7424a9; animation: spin 1s linear infinite; }
    
    @keyframes spin { 100% { transform: rotate(360deg); } }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .student-table thead th { 
        background: #f8f9fa; 
        border-bottom: 2px solid #eee; 
        font-size: 13px; 
        padding: 15px;
    }
    
    .table-responsive { max-height: 600px; overflow-y: auto; }
</style>
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex align-items-center">
            <h4 class="content-title mb-0 my-auto text-primary"><i class="fas fa-user-graduate me-2"></i> الاشتراكات</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ إدارة تفعيل الشهور للطلاب</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-15">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="filter-card border-top border-primary border-3">
        <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-filter me-2 text-primary"></i> تصفية واختيار الطلاب</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold small text-muted">الصف الدراسي</label>
                <select class="form-control form-select shadow-none" id="gradeSelect">
                    <option value="">-- اختر الصف --</option>
                    @foreach(signup_grades() as $grade)
                        <option value="{{ $grade['value'] }}">{{ $grade['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold small text-muted">الشهر الدراسي</label>
                <select class="form-control form-select shadow-none" id="monthSelect" disabled>
                    <option value="">-- اختر الشهر --</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="button" class="btn btn-primary w-100 rounded-pill shadow-sm py-2 fw-bold" id="loadStudentsBtn" disabled>
                    <i class="fas fa-sync-alt me-1"></i> عرض قوائم الطلاب
                </button>
            </div>
        </div>
    </div>

    <div id="loadingState" class="loading-spinner" style="display: none;">
        <i class="fas fa-circle-notch"></i>
        <p class="mt-3 text-muted fw-bold">جاري جلب البيانات من القاعدة...</p>
    </div>

    <div id="studentsSection" class="students-section">
        <div class="row">
            <div class="col-xl-6">
                <div class="table-card">
                    <div class="table-card-header success">
                        <span class="fw-bold"><i class="fas fa-user-check me-2"></i> طلاب لديهم اشتراك مسبق</span>
                        <span class="badge bg-white text-success rounded-pill px-3" id="subscribedCount">0</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table student-table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th width="40"><input type="checkbox" id="selectAllSubscribed"></th>
                                    <th>كود الطالب</th>
                                    <th>الاسم</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody id="subscribedTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="table-card">
                    <div class="table-card-header danger">
                        <span class="fw-bold"><i class="fas fa-user-times me-2"></i> طلاب غير مشتركين في هذا الشهر</span>
                        <span class="badge bg-white text-danger rounded-pill px-3" id="notSubscribedCount">0</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table student-table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th width="40"><input type="checkbox" id="selectAllNotSubscribed"></th>
                                    <th>كود الطالب</th>
                                    <th>الاسم</th>
                                    <th>الاشتراك</th>
                                </tr>
                            </thead>
                            <tbody id="notSubscribedTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 mb-5">
            <form id="activateForm" action="{{ route('subscription.activateMultiple') }}" method="POST">
                @csrf
                <input type="hidden" name="grade" id="formGrade">
                <input type="hidden" name="month_id" id="formMonthId">
                <div id="selectedStudentsContainer"></div>
                
                <button type="submit" class="btn btn-success btn-lg rounded-pill px-5 shadow-lg fw-bold" id="activateBtn" disabled>
                    <i class="fas fa-bolt me-2"></i> تفعيل المحددين الآن
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    
    // 1. جلب الشهور بناءً على الصف
    $('#gradeSelect').on('change', function() {
        const grade = $(this).val();
        const monthSelect = $('#monthSelect');
        
        if (grade) {
            monthSelect.prop('disabled', false).html('<option>جاري التحميل...</option>');
            $.get("{{ route('subscription.months', '') }}/" + grade, function(data) {
                monthSelect.empty().append('<option value="">-- اختر الشهر --</option>');
                $.each(data, function(key, value) {
                    monthSelect.append(`<option value="${key}">${value}</option>`);
                });
            });
        } else {
            monthSelect.prop('disabled', true).empty().append('<option value="">-- اختر الشهر --</option>');
            $('#loadStudentsBtn').prop('disabled', true);
        }
    });

    // 2. تفعيل زر العرض
    $('#monthSelect').on('change', function() {
        $('#loadStudentsBtn').prop('disabled', !$(this).val());
    });

    // 3. جلب الطلاب AJAX
    $('#loadStudentsBtn').on('click', function() {
        const grade = $('#gradeSelect').val();
        const monthId = $('#monthSelect').val();
        
        $('#formGrade').val(grade);
        $('#formMonthId').val(monthId);
        $('#loadingState').show();
        $('#studentsSection').removeClass('active');

        $.ajax({
            url: "{{ route('subscription.loadStudents') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}", grade: grade, month_id: monthId },
            success: function(response) {
                renderTable('subscribed', response.subscribedStudents);
                renderTable('notSubscribed', response.notSubscribedStudents);
                
                $('#subscribedCount').text(response.subscribedStudents.length);
                $('#notSubscribedCount').text(response.notSubscribedStudents.length);
                
                $('#loadingState').hide();
                $('#studentsSection').addClass('active');
                
                // ربط أحداث الـ Checkbox (مهم جداً هنا)
                bindCheckboxEvents();
            }
        });
    });

    // دالة بناء الجداول
// ابحث عن هذا الجزء داخل دالة renderTable وقم بتحديثه
function renderTable(type, students) {
    const tbody = $('#' + type + 'TableBody');
    tbody.empty();
    
    if (students.length === 0) {
        tbody.append('<tr><td colspan="4" class="text-center p-4 text-muted small">لا توجد بيانات لهذا الاختيار</td></tr>');
        return;
    }

    students.forEach(student => {
        const statusLabel = (type === 'subscribed') 
            ? (student.is_active == 1 ? 'success">مفعل' : 'danger">معطل') 
            : 'danger">غير مشترك';
            
        // التعديل هنا: تحويل نوع الكلاس ليتوافق مع الـ JQuery (notSubscribed تصبح not-subscribed)
        const checkClass = type === 'subscribed' ? 'subscribed-checkbox' : 'not-subscribed-checkbox';
            
        tbody.append(`
            <tr>
                <td><input type="checkbox" class="${checkClass}" value="${student.id}"></td>
                <td><span class="badge-modern badge-modern-primary">${student.id}</span></td>
                <td class="fw-bold">${student.name}</td>
                <td><span class="badge-modern badge-modern-${statusLabel}</span></td>
            </tr>
        `);
    });
}

    // دالة ربط الأحداث (Event Delegation)
    function bindCheckboxEvents() {
        // فك الارتباط القديم لمنع تكرار التنفيذ
        $(document).off('change', '#selectAllSubscribed');
        $(document).off('change', '#selectAllNotSubscribed');
        $(document).off('change', '.subscribed-checkbox, .not-subscribed-checkbox');

        // اختيار الكل - مشتركين
        $(document).on('change', '#selectAllSubscribed', function() {
            $('.subscribed-checkbox').prop('checked', $(this).prop('checked'));
            updateActivateBtn();
        });

        // اختيار الكل - غير مشتركين
        $(document).on('change', '#selectAllNotSubscribed', function() {
            $('.not-subscribed-checkbox').prop('checked', $(this).prop('checked'));
            updateActivateBtn();
        });

        // الاختيار الفردي (من أي جدول)
        $(document).on('change', '.subscribed-checkbox, .not-subscribed-checkbox', function() {
            updateActivateBtn();
        });
    }

    // دالة تحديث زر التفعيل وحمل البيانات
    function updateActivateBtn() {
        const selectedIds = [];
        $('.subscribed-checkbox:checked, .not-subscribed-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        // تعبئة الـ Container بالطلاب المختارين
        $('#selectedStudentsContainer').empty();
        selectedIds.forEach(id => {
            $('#selectedStudentsContainer').append(`<input type="hidden" name="student_ids[]" value="${id}">`);
        });

        // تحديث شكل الزر
        const btn = $('#activateBtn');
        if (selectedIds.length > 0) {
            btn.prop('disabled', false)
               .html(`<i class="fas fa-bolt me-2"></i> تفعيل (${selectedIds.length}) طلاب مختارين`);
        } else {
            btn.prop('disabled', true)
               .html(`<i class="fas fa-bolt me-2"></i> تفعيل المحددين الآن`);
        }
    }
});
</script>
@endsection