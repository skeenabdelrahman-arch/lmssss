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
</style>
@section('title')
    الطلاب الإناث
@stop
@endsection
@section('page-header')
<div class="page-header-modern">
    <h4><i class="fas fa-venus me-2"></i> الطلاب الإناث</h4>
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
        <div class="table-header-actions">
            <div class="search-box-wrapper">
                <input type="text" id="searchInput" class="form-control" placeholder="ابحث بالاسم، الصف، أو رقم التليفون...">
                <i class="fas fa-search search-icon"></i>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{url('admin/logoutAllStudents')}}" class="btn btn-modern btn-modern-warning">
                    <i class="fas fa-sign-out-alt me-2"></i> خروج جميع الطلاب
                </a>
                <button class="btn btn-modern btn-modern-primary" onclick="exportTableToExcel('datatable', 'students_female')">
                    <i class="fas fa-file-excel me-2"></i> Excel
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table id="datatable" class="table table-hover" data-page-length="25">
                <thead>
                    <tr>
                        <th>اسم الطالب</th>
                        <th>الصف الدراسي</th>
                        <th>رقم التليفون</th>
                        <th>صورة الطالب</th>
                        <th>كلمة السر</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($femaleStudents as $femaleStudent)
                        <tr>
                            <td>
                                <a href="{{url('student-profile/'.$femaleStudent->id)}}" class="student-name-link">
                                    {{ $femaleStudent->first_name }}
                                    {{ $femaleStudent->second_name }}
                                    {{ $femaleStudent->third_name }}
                                    {{ $femaleStudent->forth_name }}
                                </a>
                            </td>
                            <td><span class="badge-modern badge-modern-primary">{{ $femaleStudent->grade }}</span></td>
                            <td>{{ $femaleStudent->student_phone }}</td>
                            <td>
                                @if($femaleStudent->image)
                                    <img src="{{ url('upload_files/' . $femaleStudent->image)}}" class="student-image" alt="صورة الطالب">
                                @else
                                    <span class="badge-modern badge-modern-danger">لا توجد صورة</span>
                                @endif
                            </td>
                            <td><span class="password-code">{{ $femaleStudent->password }}</span></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ url('students-female/edit/'.$femaleStudent->id) }}" class="btn btn-modern btn-modern-warning btn-sm" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a onclick="return confirm('هل انت متاكد من الحذف ؟ ')" href="{{url('Student/delete/'.$femaleStudent->id)}}" class="btn btn-modern btn-modern-danger btn-sm" title="حذف">
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
            // Initialize DataTable
            var table = $('#datatable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/ar.json'
                },
                pageLength: 25,
                order: [[0, 'asc']],
                dom: 'Bfrtip',
                buttons: [],
                responsive: true,
                columnDefs: [
                    { orderable: true, targets: [0, 1, 2] },
                    { orderable: false, targets: [3, 4, 5] }
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
            link.download = filename ? filename + '.xlsx' : 'students_female.xlsx';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
@endsection
