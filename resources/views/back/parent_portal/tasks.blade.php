@extends('back_layouts.master')

@section('title', 'الواجبات والامتحانات')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1>الواجبات والامتحانات</h1>
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
                            <th>العنوان</th>
                            <th>النوع</th>
                            <th>تاريخ الاستحقاق</th>
                            <th>الحالة</th>
                            <th>الدرجة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td><strong>{{ $record->student->student_code }}</strong></td>
                                <td>{{ $record->student->first_name }} {{ $record->student->second_name }}</td>
                                <td>{{ $record->title }}</td>
                                <td>
                                    @if($record->task_type == 'homework')
                                        <span class="badge bg-primary">واجب</span>
                                    @else
                                        <span class="badge bg-danger">امتحان</span>
                                    @endif
                                </td>
                                <td>{{ $record->due_date?->format('Y-m-d') ?? '-' }}</td>
                                <td>
                                    @if($record->status == 'completed')
                                        <span class="badge bg-success">منجز</span>
                                    @elseif($record->status == 'overdue')
                                        <span class="badge bg-danger">متأخر</span>
                                    @else
                                        <span class="badge bg-warning">قيد الانتظار</span>
                                    @endif
                                </td>
                                <td>{{ $record->grade ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    لا توجد واجبات أو امتحانات
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
