@extends('back_layouts.master')
@section('css')
<style>
    .students-table-wrapper {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .table-header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .search-box-wrapper {
        position: relative;
        max-width: 400px;
        flex: 1;
        min-width: 250px;
    }
    
    .search-box-wrapper input {
        padding-right: 45px;
        border-radius: 8px;
        border: 2px solid #e0e0e0;
        transition: all 0.3s ease;
    }
    
    .search-box-wrapper input:focus {
        border-color: #7424a9;
        box-shadow: 0 0 0 3px rgba(116, 36, 169, 0.1);
    }
    
    .search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        pointer-events: none;
    }
    
    #datatable_wrapper .dataTables_filter {
        display: none;
    }
    
    .student-name-link {
        color: #7424a9;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-block;
    }
    
    .student-name-link:hover {
        color: #fa896b;
        text-decoration: underline;
        transform: translateX(-3px);
    }
    
    .table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        padding: 15px;
    }
    
    .table td {
        padding: 15px;
        vertical-align: middle;
    }
    
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
    }
    
    .action-buttons .btn {
        padding: 8px 12px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
    
    .action-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .student-image {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        object-fit: cover;
        border: 2px solid #e0e0e0;
        transition: all 0.3s ease;
    }
    
    .student-image:hover {
        border-color: #7424a9;
        transform: scale(1.1);
    }
    
    .password-code {
        font-family: 'Courier New', monospace;
        background: #f8f9fa;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.9rem;
        color: #495057;
    }
    
    .gender-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    
    .gender-badge.male {
        background: #e3f2fd;
        color: #1976d2;
    }
    
    .gender-badge.female {
        background: #fce4ec;
        color: #c2185b;
    }
</style>
@section('title')
    جميع الطلاب
@stop
@endsection

@section('page-header')
<div class="page-header-modern">
    <h4><i class="fas fa-users me-2"></i> جميع الطلاب</h4>
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

    <div class="students-table-wrapper">
        <!-- الفلاتر -->
        <div class="mb-4 p-3" style="background: #f8f9fa; border-radius: 10px; border: 1px solid #e0e0e0;">
            <form method="GET" action="{{ route('students.all') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">الصف الدراسي:</label>
                    <select name="grade" class="form-control form-select">
                        <option value="">-- جميع الصفوف --</option>
                        @forelse($grades as $grade)
                            <option value="{{ $grade }}" {{ $selectedGrade === $grade ? 'selected' : '' }}>
                                {{ $grade }}
                            </option>
                        @empty
                            <option disabled>لا توجد صفوف</option>
                        @endforelse
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">المحافظة:</label>
                    <select name="city" class="form-control form-select">
                        <option value="">-- جميع المحافظات --</option>
                        @forelse($cities as $city)
                            <option value="{{ $city }}" {{ $selectedCity === $city ? 'selected' : '' }}>
                                {{ $city }}
                            </option>
                        @empty
                            <option disabled>لا توجد محافظات</option>
                        @endforelse
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">نوع التسجيل:</label>
                    <select name="register" class="form-control form-select">
                        <option value="">-- جميع الأنواع --</option>
                        @forelse($registerTypes as $registerType)
                            <option value="{{ $registerType }}" {{ $selectedRegister === $registerType ? 'selected' : '' }}>
                                {{ $registerType === 'اونلاين' ? 'أونلاين' : ($registerType === 'اكسيل' ? 'إكسيل' : $registerType) }}
                            </option>
                        @empty
                            <option disabled>لا توجد أنواع</option>
                        @endforelse
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-modern btn-modern-primary w-100">
                        <i class="fas fa-filter me-2"></i> تطبيق الفلاتر
                    </button>
                </div>
                @if(!empty($selectedGrade) || !empty($selectedCity) || !empty($selectedRegister))
                <div class="col-12">
                    <a href="{{ route('students.all') }}" class="btn btn-modern btn-modern-secondary btn-sm">
                        <i class="fas fa-redo me-2"></i> إعادة تعيين الفلاتر
                    </a>
                    <span class="badge badge-modern badge-modern-info ms-2">
                        <i class="fas fa-filter me-1"></i> 
                        @if($selectedGrade) الصف: {{ $selectedGrade }} @endif
                        @if($selectedCity) | المحافظة: {{ $selectedCity }} @endif
                        @if($selectedRegister) | النوع: {{ $selectedRegister === 'اونلاين' ? 'أونلاين' : ($selectedRegister === 'اكسيل' ? 'إكسيل' : $selectedRegister) }} @endif
                    </span>
                </div>
                @endif
            </form>
        </div>

        <div class="table-header-actions">
            <div class="search-box-wrapper">
                <input type="text" id="searchInput" class="form-control" placeholder="ابحث بالاسم، الصف، أو رقم التليفون...">
                <i class="fas fa-search search-icon"></i>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{url('admin/logoutAllStudents')}}" class="btn btn-modern btn-modern-warning">
                    <i class="fas fa-sign-out-alt me-2"></i> خروج جميع الطلاب
                </a>
                <button class="btn btn-modern btn-modern-primary" onclick="exportTableToExcel('datatable', 'students')">
                    <i class="fas fa-file-excel me-2"></i> Excel
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="datatable" class="table table-hover" data-page-length="25" data-custom-init="true">
                <thead>
                    <tr>
                        <th>اسم الطالب</th>
                        <th>الجنس</th>
                        <th>الصف الدراسي</th>
                        <th>رقم التليفون</th>
                        <th>صورة الطالب</th>
                        <th>كلمة السر</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                        <tr>
                            <td>
                                <a href="{{url('student-profile/'.$student->id)}}" class="student-name-link">
                                    {{ $student->first_name }}
                                    {{ $student->second_name }}
                                    {{ $student->third_name }}
                                    {{ $student->forth_name }}
                                </a>
                            </td>
                            <td>
                                @if($student->gender == 'ذكر')
                                    <span class="gender-badge male">
                                        <i class="fas fa-mars"></i> ذكر
                                    </span>
                                @elseif($student->gender == 'انثي')
                                    <span class="gender-badge female">
                                        <i class="fas fa-venus"></i> أنثى
                                    </span>
                                @else
                                    <span class="badge-modern badge-modern-secondary">{{ $student->gender ?? 'غير محدد' }}</span>
                                @endif
                            </td>
                            <td><span class="badge-modern badge-modern-primary">{{ $student->grade }}</span></td>
                            <td>{{ $student->student_phone }}</td>
                            <td>
                                @if($student->image)
                                    <img src="{{ url('upload_files/' . $student->image)}}" class="student-image" alt="صورة الطالب">
                                @else
                                    <span class="badge-modern badge-modern-danger">لا توجد صورة</span>
                                @endif
                            </td>
                            <td><span class="password-code">{{ $student->password }}</span></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ url('students-male/edit/'.$student->id) }}" class="btn btn-modern btn-modern-warning btn-sm" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a onclick="return confirm('هل انت متاكد من الحذف ؟ ')" href="{{url('Student/delete/'.$student->id)}}" class="btn btn-modern btn-modern-danger btn-sm" title="حذف">
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
<!-- row closed -->
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
    <script>
        $(document).ready(function() {
            // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable('#datatable')) {
                $('#datatable').DataTable().destroy();
            }
            
            // Initialize DataTable
            var table = $('#datatable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/ar.json'
                },
                pageLength: 25,
                order: [[1, 'asc'], [0, 'asc']], // ترتيب حسب الجنس ثم الاسم
                dom: 'Bfrtip',
                buttons: [],
                responsive: true,
                columnDefs: [
                    { orderable: true, targets: [0, 1, 2, 3] },
                    { orderable: false, targets: [4, 5, 6] }
                ]
            });

            // Custom search input
            $('#searchInput').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Clear search on escape
            $('#searchInput').on('keydown', function(e) {
                if (e.key === 'Escape') {
                    $(this).val('');
                    table.search('').draw();
                }
            });
        });

        //////////////////////////  EXCEL /////////////////////////////////
        function exportTableToExcel(tableID, filename = ''){
            var table = document.getElementById(tableID);
            var wb = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
            var wbout = XLSX.write(wb, {bookType: 'xlsx', type: 'binary'});

            function s2ab(s) {
                var buf = new ArrayBuffer(s.length);
                var view = new Uint8Array(buf);
                for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
                return buf;
            }

            var blob = new Blob([s2ab(wbout)], {type: "application/octet-stream"});

            var link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = filename ? filename + '.xlsx' : 'students.xlsx';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
@endsection


