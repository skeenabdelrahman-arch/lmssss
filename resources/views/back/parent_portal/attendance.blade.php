@extends('back_layouts.master')

@section('title', 'سجلات الحضور')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1>سجلات الحضور</h1>
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
                            <th>التاريخ</th>
                            <th>الحالة</th>
                            <th>ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td><strong>{{ $record->student->student_code }}</strong></td>
                                <td>{{ $record->student->first_name }} {{ $record->student->second_name }}</td>
                                <td>{{ $record->attendance_date->format('Y-m-d') }}</td>
                                <td>
                                    @if($record->is_present)
                                        <span class="badge bg-success">حاضر</span>
                                    @else
                                        <span class="badge bg-danger">غائب</span>
                                    @endif
                                </td>
                                <td>{{ $record->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    لا توجد سجلات حضور
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
