@extends('back_layouts.master')

@section('title')
    الأشهر الدراسية
@stop

@section('css')
<style>
    /* تحسين الكارت الرئيسي */
    .modern-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        border: none;
    }

    /* تنسيق الجدول الاحترافي */
    .modern-table-wrapper {
        border-radius: 15px;
        overflow: hidden;
        border: 1px solid #f1f1f1;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background-color: #fcfaff; /* لون فاتح جداً مائل للبنفسجي */
        color: #495057;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border: none;
        padding: 18px 15px;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #fdfcfe !important;
        transform: scale(1.002);
    }

    .table tbody td {
        padding: 20px 15px;
        vertical-align: middle;
        color: #555;
        border-top: 1px solid #f8f9fa;
    }

    /* تنسيق صورة الشهر */
    .month-thumb {
        width: 55px;
        height: 55px;
        border-radius: 12px;
        object-fit: cover;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border: 2px solid #fff;
    }

    /* Badges مطورة */
    .badge-soft {
        padding: 8px 15px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .badge-soft-primary { background: #eef2ff; color: #4338ca; }
    .badge-soft-success { background: #ecfdf5; color: #059669; }
    .badge-soft-warning { background: #fffbeb; color: #d97706; }

    /* حقل السعر */
    .price-tag {
        font-weight: 800;
        color: #2d3748;
        background: #f7fafc;
        padding: 5px 10px;
        border-radius: 8px;
        border: 1px solid #edf2f7;
    }

    /* حقل الترتيب - شكل مودرن */
    .order-input {
        width: 70px;
        text-align: center;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 5px;
        font-weight: 600;
        color: #7424a9;
        transition: 0.3s;
    }
    .order-input:focus {
        border-color: #7424a9;
        box-shadow: 0 0 0 3px rgba(116, 36, 169, 0.1);
        outline: none;
    }

    /* أزرار الإجراءات */
    .btn-circle {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: 0.3s;
        border: none;
    }
    
    .btn-edit-soft {
        background-color: #f0f7ff;
        color: #007bff;
    }
    .btn-edit-soft:hover {
        background-color: #007bff;
        color: #fff;
    }

    /* زر الحذف - أسود يتحول لأحمر */
    .btn-delete-black {
        background-color: #1a202c;
        color: #fff;
    }
    .btn-delete-black:hover {
        background-color: #e53e3e !important;
        transform: rotate(8deg);
    }
</style>
@endsection

@section('page-header')
<div class="page-header-modern mb-4">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-calendar-alt me-2 text-primary"></i> تنظيم الأشهر</h4>
            <p class="text-muted small mb-0">إدارة الاشتراكات الشهرية وتوزيعها على الصفوف</p>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="modern-card">
    
    {{-- Header Actions --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div class="d-flex gap-2">
            <a href="{{ route('month.create') }}" class="btn btn-modern-primary px-4">
                <i class="fas fa-plus-circle me-2"></i> إضافة شهر جديد
            </a>
            <a onclick="return confirm('⚠️ تحذير: سيتم حذف كافة الأشهر، هل أنت متأكد؟')" href="{{route('deleteAllMonthes')}}" class="btn btn-outline-danger btn-sm px-3 rounded-3">
                <i class="fas fa-trash-alt me-2"></i> مسح شامل
            </a>
        </div>

        <div class="filter-box">
            <form method="GET" action="{{ route('month.index') }}" class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-filter text-muted"></i></span>
                    <select name="grade" class="form-select border-start-0 ps-0" onchange="this.form.submit()" style="min-width: 180px;">
                        <option value="">كل الصفوف الدراسية</option>
                        @foreach(signup_grades() as $grade)
                            <option value="{{ $grade['value'] }}" {{ request('grade') == $grade['value'] ? 'selected' : '' }}>
                                {{ $grade['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if(request('grade'))
                    <a href="{{ route('month.index') }}" class="btn btn-sm btn-light text-danger"><i class="fas fa-times"></i></a>
                @endif
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="modern-table-wrapper">
        <div class="table-responsive">
            <table id="datatable" class="table">
                <thead>
                    <tr>
                        <th class="text-center" width="50">#</th>
                        <th>المحتوى والشهر</th>
                        <th>الصف الدراسي</th>
                        <th>قيمة الاشتراك</th>
                        <th class="text-center">الترتيب</th>
                        <th class="text-center">التحكم</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($months as $month)
                    <tr>
                        <td class="text-center fw-bold text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if($month->image)
                                    <img src="{{ url('upload_files/' . $month->image) }}" class="month-thumb">
                                @else
                                    <div class="month-thumb d-flex align-items-center justify-content-center bg-light text-muted small">لا صورة</div>
                                @endif
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark fs-6">{{ $month->name }}</span>
                                    <span class="text-muted extra-small" style="font-size: 0.75rem;">تعديل أخير: {{ $month->updated_at->format('Y/m/d') }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            @php
                                $status = [
                                    'أولي' => ['class' => 'badge-soft-primary', 'icon' => '1'],
                                    'تانية' => ['class' => 'badge-soft-success', 'icon' => '2'],
                                    'ثالثة' => ['class' => 'badge-soft-warning', 'icon' => '3'],
                                ];
                                $current = $status[$month->grade] ?? ['class' => 'badge-soft-secondary', 'icon' => ''];
                            @endphp
                            <span class="badge-soft {{ $current['class'] }}">
                                <i class="fas fa-graduation-cap"></i> الصف {{ $month->grade }}
                            </span>
                        </td>
                        <td>
                            <span class="price-tag">{{ number_format($month->price, 0) }} <small>ج.م</small></span>
                        </td>
                        <td class="text-center">
                            <input type="number" class="order-input" 
                                   value="{{ $month->display_order ?? 0 }}" 
                                   onchange="updateDisplayOrder('month', {{ $month->id }}, this.value)">
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('month.edit', $month->id) }}" class="btn-circle btn-edit-soft" title="تعديل">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <a onclick="return confirm('حذف الشهر؟')" href="{{url('month/delete/'.$month->id)}}" class="btn-circle btn-delete-black" title="حذف">
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