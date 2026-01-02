@extends('back_layouts.master')

@section('title', 'سجلات الدفع')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1>سجلات الدفع</h1>
                <a href="{{ route('parent-portal.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> العودة
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>رقم الطالب</th>
                            <th>اسم الطالب</th>
                            <th>الشهر</th>
                            <th>المبلغ</th>
                            <th>تاريخ الدفع</th>
                            <th>الطريقة</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td><strong>{{ $record->student->student_code }}</strong></td>
                                <td>{{ $record->student->first_name }} {{ $record->student->second_name }}</td>
                                <td>
                                    @php
                                        // استخراج الشهر من الملاحظات
                                        if ($record->notes && str_starts_with($record->notes, 'الشهر: ')) {
                                            echo str_replace('الشهر: ', '', $record->notes);
                                        } elseif ($record->month) {
                                            echo $record->month->name;
                                        } else {
                                            echo 'غير محدد';
                                        }
                                    @endphp
                                </td>
                                <td>{{ $record->amount }} ريال</td>
                                <td>{{ $record->payment_date->format('Y-m-d') }}</td>
                                <td>{{ $record->payment_method }}</td>
                                <td>
                                    @if($record->is_confirmed)
                                        <span class="badge bg-success">تم التأكيد</span>
                                    @else
                                        <span class="badge bg-warning">قيد المراجعة</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    لا توجد سجلات دفع
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($records->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $records->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
