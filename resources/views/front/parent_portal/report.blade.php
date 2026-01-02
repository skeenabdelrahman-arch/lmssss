@extends('front.layouts.app')

@section('title', 'تقرير الطالب الشامل')

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2">
                        <i class="fas fa-file-alt"></i> تقرير الطالب الشامل
                    </h1>
                    <p class="text-muted">{{ $student->first_name }} {{ $student->second_name }} {{ $student->third_name }} {{ $student->forth_name }}</p>
                </div>
                <div>
                    <a href="{{ url('parent-portal') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> العودة
                    </a>
                    <button onclick="window.print()" class="btn btn-info">
                        <i class="fas fa-print"></i> طباعة
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات الطالب الأساسية -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> معلومات الطالب</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center mb-3 mb-md-0">
                            @php
                                $studentImage = $student->image ? url('upload_files/' . $student->image) : asset('images/default-avatar.png');
                            @endphp
                            <img src="{{ $studentImage }}" 
                                 alt="{{ $student->first_name }}" 
                                 class="img-fluid rounded-circle shadow"
                                 style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #007bff;"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($student->first_name . ' ' . $student->second_name) }}&size=120&background=007bff&color=fff&bold=true'">
                        </div>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>الاسم الكامل:</strong></p>
                                    <p>{{ $student->first_name }} {{ $student->second_name }} {{ $student->third_name }} {{ $student->forth_name }}</p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>رقم الطالب:</strong></p>
                                    <p>{{ $student->student_code }}</p>
                                </div>
                                <div class="col-md-3">
                                    <p><strong>الصف:</strong></p>
                                    <p>{{ $student->grade }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- كارت الأداء العام -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <h6 class="text-muted">المعدل العام</h6>
                    <h2 class="text-primary">{{ $performance['average_grade'] }}</h2>
                    <p class="badge bg-info">{{ $performance['level'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h6 class="text-muted">الحضور</h6>
                    <h2 class="text-success">{{ $performance['attendance_percentage'] }}%</h2>
                    <p class="badge bg-success">{{ $attendanceStats['present'] }}/{{ $attendanceStats['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <h6 class="text-muted">الامتحانات</h6>
                    <h2 class="text-warning">{{ $performance['exams_taken'] }}/{{ $performance['total_exams'] }}</h2>
                    <p class="badge bg-warning">مأخوذ / متاح</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <h6 class="text-muted">المحاضرات</h6>
                    <h2 class="text-info">{{ $performance['lectures_watched'] }}/{{ $performance['total_lectures'] }}</h2>
                    <p class="badge bg-info">مشاهد / إجمالي</p>
                </div>
            </div>
        </div>
    </div>

    <!-- الاشتراكات الشهرية -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar"></i> الشهور المشترك فيها</h5>
                </div>
                <div class="card-body">
                    @if($subscriptions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr class="table-light">
                                        <th>اسم الشهر</th>
                                        <th>الحالة</th>
                                        <th>تاريخ الاشتراك</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subscriptions as $subscription)
                                        <tr>
                                            <td>{{ $subscription->month->name ?? 'N/A' }}</td>
                                            <td>
                                                @if($subscription->is_active)
                                                    <span class="badge bg-success">نشط</span>
                                                @else
                                                    <span class="badge bg-danger">معطل</span>
                                                @endif
                                            </td>
                                            <td>{{ $subscription->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">لا توجد اشتراكات حالياً</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- درجات الامتحانات -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> درجات الامتحانات</h5>
                </div>
                <div class="card-body">
                    @if($examResults->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr class="table-light">
                                        <th>اسم الامتحان</th>
                                        <th>الدرجة</th>
                                        <th>تاريخ الامتحان</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($examResults as $result)
                                        <tr>
                                            <td>
                                                {{ $result->exam->exam_title ?? 'غير مسمى' }}
                                            </td>
                                            <td>
                                                <strong>{{ $result->degree ?? 'لم يتم التقييم' }}</strong>
                                            </td>
                                            <td>{{ $result->completed_at?->format('Y-m-d H:i') ?? 'قيد الانتظار' }}</td>
                                            <td>
                                                @if($result->is_marked)
                                                    <span class="badge bg-success">مصحح</span>
                                                @else
                                                    <span class="badge bg-warning">قيد التصحيح</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">لم يأخذ الطالب أي امتحانات حتى الآن</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- سجلات الحضور -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-check-circle"></i> سجلات الحضور ( الخاصة بالسنتر )</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded">
                                <p class="text-muted mb-1">إجمالي الحضور</p>
                                <h5>{{ $attendanceStats['present'] }} / {{ $attendanceStats['total'] }}</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded">
                                <p class="text-muted mb-1">نسبة الحضور</p>
                                <h5>{{ $attendanceStats['percentage'] }}%</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded">
                                <p class="text-muted mb-1">الغيابات</p>
                                <h5>{{ $attendanceStats['absent'] }}</h5>
                            </div>
                        </div>
                    </div>

                    @if($attendance->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr class="table-light">
                                        <th>التاريخ</th>
                                        <th>الحالة</th>
                                        <th>ملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendance->take(20) as $record)
                                        <tr>
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
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">لا توجد سجلات حضور</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- سجلات الدفع -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-credit-card"></i> سجلات الدفع ( الخاصة بالسنتر )</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="bg-light p-3 rounded">
                                <p class="text-muted mb-1">المبلغ المدفوع</p>
                                <h5>{{ $paymentStats['total_paid'] }} جنيه مصري</h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-light p-3 rounded">
                                <p class="text-muted mb-1">المبلغ المعلق</p>
                                <h5>{{ $paymentStats['pending'] }} جنيه مصري</h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <p class="text-muted mb-1">عدد الدفعات</p>
                                <h5>{{ $paymentStats['payment_count'] }} دفعة</h5>
                            </div>
                        </div>
                    </div>

                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr class="table-light">
                                        <th>الشهر</th>
                                        <th>المبلغ</th>
                                        <th>تاريخ الدفع</th>
                                        <th>طريقة الدفع</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>
                                                @php
                                                    // استخراج الشهر من الملاحظات
                                                    if ($payment->notes && str_starts_with($payment->notes, 'الشهر: ')) {
                                                        echo str_replace('الشهر: ', '', $payment->notes);
                                                    } elseif ($payment->month) {
                                                        echo $payment->month->name;
                                                    } else {
                                                        echo 'غير محدد';
                                                    }
                                                @endphp
                                            </td>
                                            <td>{{ $payment->amount }} جنيه مصري</td>
                                            <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                            <td>{{ $payment->payment_method }}</td>
                                            <td>
                                                @if($payment->is_confirmed)
                                                    <span class="badge bg-success">تم التأكيد</span>
                                                @else
                                                    <span class="badge bg-warning">قيد المراجعة</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">لا توجد سجلات دفع</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- الواجبات والامتحانات -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-tasks"></i> الواجبات والامتحانات ( الخاصة بالسنتر )</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="bg-light p-3 rounded">
                                <p class="text-muted mb-1">إجمالي</p>
                                <h5>{{ $taskStats['total'] }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-light p-3 rounded">
                                <p class="text-muted mb-1">منجزة</p>
                                <h5 class="text-success">{{ $taskStats['completed'] }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-light p-3 rounded">
                                <p class="text-muted mb-1">قيد الانتظار</p>
                                <h5 class="text-warning">{{ $taskStats['pending'] }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-light p-3 rounded">
                                <p class="text-muted mb-1">متأخرة</p>
                                <h5 class="text-danger">{{ $taskStats['overdue'] }}</h5>
                            </div>
                        </div>
                    </div>

                    @if($tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr class="table-light">
                                        <th>العنوان</th>
                                        <th>النوع</th>
                                        <th>تاريخ الاستحقاق</th>
                                        <th>الحالة</th>
                                        <th>الدرجة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $task)
                                        <tr>
                                            <td>{{ $task->title }}</td>
                                            <td>
                                                @if($task->task_type == 'homework')
                                                    <span class="badge bg-primary">واجب</span>
                                                @else
                                                    <span class="badge bg-danger">امتحان</span>
                                                @endif
                                            </td>
                                            <td>{{ $task->due_date?->format('Y-m-d') ?? '-' }}</td>
                                            <td>
                                                @if($task->status == 'completed')
                                                    <span class="badge bg-success">منجز</span>
                                                @elseif($task->status == 'overdue')
                                                    <span class="badge bg-danger">متأخر</span>
                                                @else
                                                    <span class="badge bg-warning">قيد الانتظار</span>
                                                @endif
                                            </td>
                                            <td>{{ $task->grade ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">لا توجد واجبات أو امتحانات</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- آخر المحاضرات المشاهدة -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-video"></i> آخر المحاضرات المشاهدة</h5>
                </div>
                <div class="card-body">
                    @if($lectureViews->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr class="table-light">
                                        <th>اسم المحاضرة</th>
                                        <th>تاريخ المشاهدة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lectureViews as $view)
                                        <tr>
                                            <td>
                                                {{ $view->lecture->title ?? 'غير مسمى' }}
                                            </td>
                                            <td>{{ $view->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">لم يشاهد الطالب أي محاضرات بعد</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- التقرير الشامل -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-star"></i> التقرير الشامل والتوصيات</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6>ملخص الأداء:</h6>
                        <ul class="mb-0">
                            <li>المعدل العام: <strong>{{ $performance['average_grade'] }}</strong> ({{ $performance['level'] }})</li>
                            <li>نسبة الحضور: <strong>{{ $performance['attendance_percentage'] }}%</strong></li>
                            <li>الامتحانات: <strong>{{ $performance['exams_taken'] }}/{{ $performance['total_exams'] }}</strong></li>
                            <li>المحاضرات: <strong>{{ $performance['lectures_watched'] }}/{{ $performance['total_lectures'] }}</strong></li>
                            <li>الاشتراكات النشطة: <strong>{{ $performance['active_subscriptions'] }}/{{ $performance['total_subscriptions'] }}</strong></li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <h6>التوصيات:</h6>
                        <ul class="mb-0">
                            @if($performance['average_grade'] < 60)
                                <li>⚠️ يحتاج الطالب لدعم أكاديمي إضافي</li>
                            @endif
                            @if($performance['attendance_percentage'] < 80)
                                <li>⚠️ نسبة الحضور أقل من المتوقع، يرجى تحسينها</li>
                            @endif
                            @if($taskStats['overdue'] > 0)
                                <li>⚠️ هناك {{ $taskStats['overdue'] }} واجب/امتحان متأخر</li>
                            @endif
                        </ul>
                    </div>

                    <p class="text-muted">
                        <i class="fas fa-info-circle"></i> تم إنشاء هذا التقرير في: {{ now()->format('Y-m-d H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn, .d-flex {
            display: none !important;
        }
        body {
            background: white;
        }
    }

    .card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        border-radius: 8px 8px 0 0;
        padding: 1rem 1.5rem;
    }

    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }

    .badge {
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
    }
</style>
@endsection
