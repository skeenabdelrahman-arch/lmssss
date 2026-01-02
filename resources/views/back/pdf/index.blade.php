@extends('back_layouts.master')

@section('title') المذكرات @stop

@section('css')
<style>
    body { background-color: #f4f7fa; }
    .main-wrapper { max-width: 1200px; margin: 0 auto; }
    
    /* الكرت الرئيسي */
    .modern-card {
        background: #fff; border-radius: 24px; padding: 30px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.04); border: none;
    }

    /* أزرار العمليات */
    .btn-action {
        width: 35px; height: 35px; border-radius: 10px;
        display: inline-flex; align-items: center; justify-content: center;
        transition: 0.3s; border: none; margin: 0 2px;
    }
    .btn-edit { background: #eef2ff; color: #6366f1; }
    .btn-view { background: #ecfdf5; color: #10b981; }
    .btn-delete { background: #fef2f2; color: #ef4444; }
    .btn-action:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }

    /* البادجات */
    .status-badge {
        padding: 5px 12px; border-radius: 8px; font-weight: 700; font-size: 12px;
    }
    .status-active { background: #dcfce7; color: #15803d; }
    .status-inactive { background: #fee2e2; color: #b91c1c; }

    /* تحسين الجدول */
    .table thead th {
        background-color: #f8fafc; color: #64748b; font-weight: 700;
        font-size: 13px; border: none; padding: 18px;
    }
    .table tbody td { vertical-align: middle; padding: 18px; border-bottom: 1px solid #f1f5f9; }

    .order-input {
        border: 2px solid #f1f5f9; border-radius: 8px; padding: 5px;
        text-align: center; font-weight: bold; color: #6366f1; transition: 0.3s;
    }
    .order-input:focus { border-color: #6366f1; outline: none; }
</style>
@endsection

@section('content')
<div class="main-wrapper py-5">
    
    <div class="page-header-modern mb-4">
        <h4 class="fw-bold"><i class="fas fa-file-pdf me-2 text-primary"></i> إدارة المذكرات والملفات</h4>
    </div>

    <div class="modern-card">
        @if(session()->has('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session()->get('error') }}
            </div>
        @elseif(session()->has('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                <i class="fas fa-check-circle me-2"></i> {{ session()->get('success') }}
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div class="d-flex gap-2">
                <a href="{{ route('pdf.create') }}" class="btn btn-primary rounded-3 fw-bold px-4 py-2 shadow-sm">
                    <i class="fas fa-plus me-2"></i> إضافة مذكرة جديدة
                </a>
                <a onclick="return confirm('هل انت متأكد من حذف جميع المذكرات ؟ ')" href="{{route('deleteAllPdfs')}}" class="btn btn-outline-danger rounded-3 fw-bold px-4 py-2">
                    <i class="fas fa-trash-alt me-2"></i> مسح الكل
                </a>
            </div>
            <div class="text-muted small">إجمالي المذكرات: <span class="fw-bold text-dark">{{ $pdfs->count() }}</span></div>
        </div>

        <div class="table-responsive">
            <table id="datatable" class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المذكرة</th>
                        <th>الشهر</th>
                        <th>السنة الدراسية</th>
                        <th class="text-center">الترتيب</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pdfs as $pdf)
                        <tr>
                            <td><span class="text-muted fw-bold">{{ $loop->iteration }}</span></td>
                            <td>
                                <div class="fw-bold text-dark">{{ $pdf->title }}</div>
                                <div class="small text-muted text-truncate" style="max-width: 200px;">{{ $pdf->description ?: 'لا يوجد وصف' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-3 rounded-pill">{{ $pdf->month ? $pdf->month->name : 'غير محدد' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border-primary-subtle px-3 rounded-pill">{{ $pdf->grade }}</span>
                            </td>
                            <td class="text-center">
                                <input type="number" class="order-input w-75" 
                                       value="{{ $pdf->display_order ?? 0 }}" 
                                       onchange="updateDisplayOrder('pdf', {{ $pdf->id }}, this.value)"
                                       min="0">
                            </td>
                            <td class="text-center">
                                @if($pdf->status == 1)
                                    <span class="status-badge status-active">مفعل</span>
                                @else
                                    <span class="status-badge status-inactive">معطل</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('pdf.edit', $pdf->id) }}" class="btn-action btn-edit" title="تعديل">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.views.pdf.students', $pdf->id) }}" class="btn-action btn-view" title="مشاهدات الطلاب">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a onclick="return confirm('هل انت متاكد من الحذف ؟ ')" href="{{url('pdf/delete/'.$pdf->id)}}" class="btn-action btn-delete" title="حذف">
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

@section('js')
<script>
    // كود تحديث الترتيب عبر AJAX
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
                if (response.success) {
                    // إشعار نجاح خفيف يمكن إضافته هنا (Toast)
                }
            },
            error: function() {
                alert('حدث خطأ أثناء تحديث الترتيب');
            }
        });
    }

    // كود جلب الشهور بناءً على السنة الدراسية
    $(document).ready(function () {
        $('select[name="grade"]').on('change', function () {
            var grade = $(this).val();
            if (grade) {
                $.ajax({
                    url: "{{ URL::to('monthes') }}/" + grade,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('select[name="Month"]').empty();
                        $.each(data, function (key, value) {
                            $('select[name="Month"]').append('<option value="' + key + '">' + value + '</option>');
                        });
                    },
                });
            }
        });
    });
</script>
@endsection