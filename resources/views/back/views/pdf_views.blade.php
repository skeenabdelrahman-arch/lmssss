@extends('back_layouts.master')
@section('title')
    مشاهدات المذكرة: {{ $pdf->title }}
@stop
@section('content')
<div class="modern-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-eye me-2"></i>
                مشاهدات المذكرة: {{ $pdf->title }}
            </h4>
            <small class="text-muted">
                الكورس: {{ $pdf->month ? $pdf->month->name : 'غير محدد' }}
            </small>
        </div>
        <a href="{{ route('pdf.index') }}" class="btn btn-modern btn-modern-secondary">
            <i class="fas fa-arrow-right me-2"></i> العودة
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ count($studentsData) }}</h3>
                    <p>إجمالي الطلاب المشتركين</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ collect($studentsData)->where('viewed', true)->count() }}</h3>
                    <p>الطلاب الذين شاهدوا</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ collect($studentsData)->where('viewed', false)->count() }}</h3>
                    <p>الطلاب الذين لم يشاهدوا</p>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول الطلاب الذين شاهدوا -->
    <div class="modern-card mb-4">
        <div class="card-header-modern bg-success text-white">
            <h5 class="mb-0">
                <i class="fas fa-check-circle me-2"></i>
                الطلاب الذين شاهدوا المذكرة ({{ collect($studentsData)->where('viewed', true)->count() }})
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم الطالب</th>
                        <th>البريد الإلكتروني</th>
                        <th>رقم الهاتف</th>
                        <th>الصف الدراسي</th>
                        <th>تاريخ المشاهدة</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $viewedStudents = collect($studentsData)->where('viewed', true);
                    @endphp
                    @forelse($viewedStudents as $index => $data)
                        <tr>
                            <td><strong>{{ $loop->iteration }}</strong></td>
                            <td>
                                <strong>
                                    {{ $data['student']->first_name }} 
                                    {{ $data['student']->second_name }} 
                                    {{ $data['student']->third_name }} 
                                    {{ $data['student']->forth_name }}
                                </strong>
                            </td>
                            <td>{{ $data['student']->email ?: '-' }}</td>
                            <td>{{ $data['student']->student_phone ?: '-' }}</td>
                            <td>
                                <span class="badge-modern badge-modern-primary">
                                    {{ $data['student']->grade ?: 'غير محدد' }}
                                </span>
                            </td>
                            <td>
                                @if($data['viewed_at'])
                                    <span class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $data['viewed_at']->format('Y-m-d H:i') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                <p class="text-muted">لا يوجد طلاب شاهدوا هذه المذكرة</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- جدول الطلاب الذين لم يشاهدوا -->
    <div class="modern-card">
        <div class="card-header-modern bg-danger text-white">
            <h5 class="mb-0">
                <i class="fas fa-times-circle me-2"></i>
                الطلاب الذين لم يشاهدوا المذكرة ({{ collect($studentsData)->where('viewed', false)->count() }})
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم الطالب</th>
                        <th>البريد الإلكتروني</th>
                        <th>رقم الهاتف</th>
                        <th>الصف الدراسي</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $notViewedStudents = collect($studentsData)->where('viewed', false);
                    @endphp
                    @forelse($notViewedStudents as $index => $data)
                        <tr>
                            <td><strong>{{ $loop->iteration }}</strong></td>
                            <td>
                                <strong>
                                    {{ $data['student']->first_name }} 
                                    {{ $data['student']->second_name }} 
                                    {{ $data['student']->third_name }} 
                                    {{ $data['student']->forth_name }}
                                </strong>
                            </td>
                            <td>{{ $data['student']->email ?: '-' }}</td>
                            <td>{{ $data['student']->student_phone ?: '-' }}</td>
                            <td>
                                <span class="badge-modern badge-modern-primary">
                                    {{ $data['student']->grade ?: 'غير محدد' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ url('student-profile/' . $data['student']->id) }}" class="btn btn-modern btn-modern-primary btn-sm" title="عرض الملف الشخصي">
                                    <i class="fas fa-user"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <p class="text-success">جميع الطلاب شاهدوا هذه المذكرة</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.stat-card {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}
.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #fff;
}
.stat-icon.bg-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-icon.bg-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.stat-icon.bg-danger { background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%); }
.stat-content h3 {
    margin: 0;
    font-size: 28px;
    font-weight: bold;
    color: #333;
}
.stat-content p {
    margin: 5px 0 0 0;
    color: #666;
    font-size: 14px;
}
.card-header-modern {
    padding: 15px 20px;
    border-radius: 10px 10px 0 0;
}
.card-header-modern.bg-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
}
.card-header-modern.bg-danger {
    background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%) !important;
}
</style>
@endsection

