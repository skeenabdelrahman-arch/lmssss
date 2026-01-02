@extends('back_layouts.master')

@section('title')
    تفعيل اشتراك شامل لمجموعة من الطلبة
@stop

@section('css')
<style>
    :root {
        --card-border-radius: 12px;
        --card-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        --primary-color: #7424a9;
        --bg-light: #f8f9fa;
        --text-muted: #6c757d;
    }

    .modern-card {
        background: #ffffff;
        border-radius: var(--card-border-radius);
        box-shadow: var(--card-shadow);
        border: none;
        padding: 25px;
        margin-bottom: 25px;
    }

    .section-title {
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }

    .form-label {
        font-weight: 600;
        color: #444;
        font-size: 14px;
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding: 10px 15px;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(116, 36, 169, 0.1);
    }

    .btn-modern {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-modern-primary { background: var(--primary-color); color: white; border: none; }
    .btn-modern-primary:hover { background: #5a1c83; color: white; transform: translateY(-2px); }
    
    .btn-modern-success { background: #28a745; color: white; border: none; }
    .btn-modern-success:hover { background: #218838; color: white; transform: translateY(-2px); }

    .search-box {
        background: var(--bg-light);
        border-radius: 10px;
        border: 1px solid #eee;
        padding: 15px;
    }

    /* Table Styles */
    .table-custom thead th {
        background: var(--bg-light);
        color: var(--text-muted);
        font-weight: 600;
        border-bottom: 2px solid #eee;
        padding: 15px;
    }

    .table-custom tbody td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #eee;
    }

    /* Alerts */
    .alert-modern {
        border-radius: 10px;
        border: none;
        padding: 15px 20px;
    }

    [data-theme="dark"] .modern-card, [data-theme="dark"] .search-box {
        background: #1a202c !important;
        color: #fff;
    }
    [data-theme="dark"] .form-control, [data-theme="dark"] .form-select {
        background: #2d3748;
        border-color: #4a5568;
        color: #fff;
    }
</style>
@endsection

@section('page-header')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold"><i class="fas fa-unlock-alt me-2" style="color: var(--primary-color)"></i> تفعيل الاشتراك الشامل</h4>
        <span class="text-muted">إدارة الوصول الكامل للكورسات لمجموعة طلاب</span>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid p-0">
    
    {{-- رسائل التنبيه --}}
    @if(session()->has('error'))
        <div class="alert alert-modern alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><strong>{{ session()->get('error') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif(session()->has('success'))
        <div class="alert alert-modern alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i><strong>{{ session()->get('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- قسم ملف Excel --}}
        <div class="col-lg-6">
            <div class="modern-card h-100">
                <h5 class="section-title"><i class="fas fa-file-excel me-2 text-success"></i> تفعيل من ملف Excel</h5>
                <p class="text-muted small mb-4">ارفع ملف Excel يحتوي على أرقام هواتف الطلاب في <strong>العمود الثالث</strong> لتفعيل كل الكورسات لهم دفعة واحدة.</p>
                
                <form action="{{ route('admin.students.all_access.excel') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label">اختر ملف Excel (.xlsx, .xls)</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                    </div>
                    <div class="mb-4 p-3 border rounded bg-light">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="include_imported" name="include_imported" value="1">
                            <label class="form-check-label fw-bold" for="include_imported">تفعيل لطلاب الـ Excel المستوردين مؤخراً</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-modern btn-modern-success w-100">
                        <i class="fas fa-upload me-2"></i> بدء معالجة الملف وتفعيل الطلاب
                    </button>
                </form>
            </div>
        </div>

        {{-- قسم الإدخال اليدوي --}}
        <div class="col-lg-6">
            <div class="modern-card h-100">
                <h5 class="section-title"><i class="fas fa-keyboard me-2 text-primary"></i> تفعيل باختيار يدوي</h5>
                
                <div class="search-box mb-4">
                    <label class="form-label mb-2">بحث سريع عن طالب لإضافته:</label>
                    <div class="input-group">
                        <input type="text" id="student_search_input" class="form-control" placeholder="الاسم، الموبايل، أو الكود...">
                        <button type="button" id="student_search_btn" class="btn btn-dark">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <div id="student_search_results" class="mt-2" style="max-height: 150px; overflow-y: auto;"></div>
                </div>

                <form action="{{ route('admin.students.all_access.manual') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">نوع المعرّف:</label>
                            <select name="identifier_type" id="identifier_type" class="form-select" required>
                                <option value="id">Database ID</option>
                                <option value="phone">رقم الموبايل</option>
                                <option value="code">كود الطالب</option>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">القائمة (كل معرّف في سطر):</label>
                            <textarea name="identifiers" id="identifiers" rows="5" class="form-control" placeholder="1020&#10;1021&#10;1022" required></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-modern btn-modern-primary w-100 mt-4">
                        <i class="fas fa-check-circle me-2"></i> تفعيل للطلاب المحددين أعلاه
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- جدول الطلاب الحاليين --}}
    <div class="modern-card mt-4">
        <h5 class="section-title"><i class="fas fa-users me-2"></i> طلاب لديهم اشتراك شامل حالياً</h5>
        @if(isset($allAccessStudents) && $allAccessStudents->count())
            <div class="table-responsive">
                <table class="table table-custom table-hover">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>اسم الطالب</th>
                            <th>الصف الدراسي</th>
                            <th>رقم الموبايل</th>
                            <th>كود الطالب</th>
                            <th width="15%">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allAccessStudents as $index => $s)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">{{ trim($s->first_name . ' ' . $s->second_name . ' ' . $s->third_name . ' ' . $s->forth_name) }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $s->grade }}</span></td>
                                <td dir="ltr">{{ $s->student_phone }}</td>
                                <td><code>{{ $s->student_code }}</code></td>
                                <td>
                                    <form action="{{ route('admin.students.remove_all_access', $s->id) }}" method="POST" onsubmit="return confirm('إلغاء الاشتراك الشامل سيؤدي لمنع الوصول للكورسات غير المدفوعة، هل أنت متأكد؟')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                            <i class="fas fa-user-slash me-1"></i> إلغاء التفعيل
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-user-shield fa-3x mb-3 text-light"></i>
                <p class="text-muted">لا يوجد طلاب مشتركين في النظام الشامل حالياً.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('js')
<script>
    (function() {
        const searchInput = document.getElementById('student_search_input');
        const searchBtn = document.getElementById('student_search_btn');
        const resultsBox = document.getElementById('student_search_results');
        const identifiersTextarea = document.getElementById('identifiers');
        const identifierTypeSelect = document.getElementById('identifier_type');

        function renderResults(students) {
            if (!students || students.length === 0) {
                resultsBox.innerHTML = '<div class="alert alert-light small p-2">لا توجد نتائج.</div>';
                return;
            }

            let html = '<div class="list-group shadow-sm">';
            students.forEach(function(s) {
                html += `
                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-2">
                        <div style="font-size: 13px">
                            <span class="fw-bold">${s.name || ''}</span><br>
                            <small class="text-muted">${s.phone || ''} | ${s.code || ''}</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary add-student-btn"
                            data-id="${s.id}" data-phone="${s.phone || ''}" data-code="${s.code || ''}">
                            إضافة
                        </button>
                    </div>
                `;
            });
            html += '</div>';
            resultsBox.innerHTML = html;

            resultsBox.querySelectorAll('.add-student-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const type = identifierTypeSelect.value;
                    let value = (type === 'id') ? this.getAttribute('data-id') : 
                                (type === 'phone') ? this.getAttribute('data-phone') : 
                                this.getAttribute('data-code');

                    if (!value) return alert('هذا المعرف غير متوفر لهذا الطالب');

                    const current = identifiersTextarea.value.trim();
                    identifiersTextarea.value = current === '' ? value : current + '\n' + value;
                    this.innerHTML = '<i class="fas fa-check"></i>';
                    this.className = 'btn btn-sm btn-success disabled';
                });
            });
        }

        function performSearch() {
            const q = searchInput.value.trim();
            if (q.length < 2) return;
            resultsBox.innerHTML = '<small class="text-muted">جاري البحث...</small>';
            
            fetch(`{{ route('admin.students.search') }}?q=${encodeURIComponent(q)}`, {
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
            .then(res => res.json())
            .then(data => renderResults(data.students || []))
            .catch(() => resultsBox.innerHTML = '<small class="text-danger">خطأ في الاتصال</small>');
        }

        searchBtn.addEventListener('click', performSearch);
        searchInput.addEventListener('keypress', (e) => { if(e.key === 'Enter') { e.preventDefault(); performSearch(); } });
    })();
</script>
@endsection