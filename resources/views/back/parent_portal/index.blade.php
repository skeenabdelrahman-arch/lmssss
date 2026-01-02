@extends('back_layouts.master')

@section('title', 'إدارة بيانات ولي الأمر')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-4">
                <i class="fas fa-database"></i> إدارة بيانات بوابة ولي الأمر
            </h1>
        </div>
    </div>

    <!-- رسائل التنبيه -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('errors') && count(session('errors')) > 0)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>تحذيرات:</strong>
            <ul class="mb-0 mt-2">
                @foreach(session('errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            
            @if(session('has_failed_rows'))
                <div class="mt-3">
                    <strong>يمكنك تحميل ملف يحتوي على الصفوف الفاشلة لمراجعتها وإصلاحها:</strong>
                    <div class="btn-group mt-2" role="group">
                        @if(session()->has('attendance_failed_rows'))
                            <a href="{{ route('parent-portal.export-failed-attendance') }}" class="btn btn-sm btn-outline-warning">
                                <i class="fas fa-download"></i> تحميل أخطاء الحضور
                            </a>
                        @endif
                        @if(session()->has('payment_failed_rows'))
                            <a href="{{ route('parent-portal.export-failed-payments') }}" class="btn btn-sm btn-outline-warning">
                                <i class="fas fa-download"></i> تحميل أخطاء الدفع
                            </a>
                        @endif
                        @if(session()->has('task_failed_rows'))
                            <a href="{{ route('parent-portal.export-failed-tasks') }}" class="btn btn-sm btn-outline-warning">
                                <i class="fas fa-download"></i> تحميل أخطاء الواجبات
                            </a>
                        @endif
                    </div>
                </div>
            @endif
            
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- التبويبات -->
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="attendance-tab" data-bs-toggle="tab" href="#attendance" role="tab">
                        <i class="fas fa-check-circle"></i> الحضور
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="payments-tab" data-bs-toggle="tab" href="#payments" role="tab">
                        <i class="fas fa-credit-card"></i> الدفع
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tasks-tab" data-bs-toggle="tab" href="#tasks" role="tab">
                        <i class="fas fa-tasks"></i> الواجبات والامتحانات
                    </a>
                </li>
            </ul>

            <!-- محتوى التبويبات -->
            <div class="tab-content mt-4">
                <!-- تبويب الحضور -->
                <div class="tab-pane fade show active" id="attendance" role="tabpanel">
                    <h5 class="mb-4">استيراد سجلات الحضور من Excel</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">تحميل ملف الحضور</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('parent-portal.import-attendance') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="attendance_file" class="form-label">اختر ملف Excel</label>
                                            <input 
                                                type="file" 
                                                id="attendance_file" 
                                                name="file" 
                                                class="form-control"
                                                accept=".xlsx,.xls,.csv"
                                                required
                                            >
                                            <small class="text-muted d-block mt-2">
                                                الصيغ المدعومة: Excel (.xlsx, .xls) أو CSV
                                            </small>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-upload"></i> تحميل
                                        </button>
                                    </form>

                                    <hr class="my-3">

                                    <p class="mb-3"><strong>تنسيق الملف المتوقع:</strong></p>
                                    <table class="table table-sm table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>رقم الطالب</th>
                                                <th>التاريخ</th>
                                                <th>حاضر؟</th>
                                                <th>ملاحظات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-muted">
                                                <td>12345</td>
                                                <td>2024-01-15</td>
                                                <td>نعم</td>
                                                <td>-</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <a href="{{ route('parent-portal.export-attendance-template') }}" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-download"></i> تحميل النموذج
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">معلومات إضافية</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info mb-0">
                                        <strong>📋 التعليمات:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>تأكد من أن رقم الطالب موجود في النظام</li>
                                            <li>استخدم صيغة التاريخ: YYYY-MM-DD (مثال: 2024-01-15)</li>
                                            <li>استخدم "نعم" أو "لا" في حقل الحضور</li>
                                            <li>يمكنك تحميل أكثر من ملف، ستتم إضافتها تدريجياً</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب الدفع -->
                <div class="tab-pane fade" id="payments" role="tabpanel">
                    <h5 class="mb-4">استيراد سجلات الدفع من Excel</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">تحميل ملف الدفع</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('parent-portal.import-payments') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="payment_file" class="form-label">اختر ملف Excel</label>
                                            <input 
                                                type="file" 
                                                id="payment_file" 
                                                name="file" 
                                                class="form-control"
                                                accept=".xlsx,.xls,.csv"
                                                required
                                            >
                                            <small class="text-muted d-block mt-2">
                                                الصيغ المدعومة: Excel (.xlsx, .xls) أو CSV
                                            </small>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-upload"></i> تحميل
                                        </button>
                                    </form>

                                    <hr class="my-3">

                                    <p class="mb-3"><strong>تنسيق الملف المتوقع:</strong></p>
                                    <table class="table table-sm table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>رقم الطالب</th>
                                                <th>الشهر</th>
                                                <th>المبلغ</th>
                                                <th>التاريخ</th>
                                                <th>الطريقة</th>
                                                <th>تم التأكيد</th>
                                                <th>ملاحظات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-muted">
                                                <td>12345</td>
                                                <td>يناير 2024</td>
                                                <td>500</td>
                                                <td>2024-01-15</td>
                                                <td>نقداً</td>
                                                <td>نعم</td>
                                                <td>-</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <a href="{{ route('parent-portal.export-payment-template') }}" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-download"></i> تحميل النموذج
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">معلومات إضافية</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info mb-0">
                                        <strong>📋 التعليمات:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>تأكد من أن رقم الطالب موجود في النظام</li>
                                            <li>اسم الشهر يجب أن يكون مطابقاً لأسماء الشهور في النظام</li>
                                            <li>استخدم صيغة التاريخ: YYYY-MM-DD</li>
                                            <li>استخدم "نعم" أو "لا" في حقل التأكيد</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب الواجبات -->
                <div class="tab-pane fade" id="tasks" role="tabpanel">
                    <h5 class="mb-4">استيراد الواجبات والامتحانات من Excel</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">تحميل ملف الواجبات</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('parent-portal.import-tasks') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="task_file" class="form-label">اختر ملف Excel</label>
                                            <input 
                                                type="file" 
                                                id="task_file" 
                                                name="file" 
                                                class="form-control"
                                                accept=".xlsx,.xls,.csv"
                                                required
                                            >
                                            <small class="text-muted d-block mt-2">
                                                الصيغ المدعومة: Excel (.xlsx, .xls) أو CSV
                                            </small>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-upload"></i> تحميل
                                        </button>
                                    </form>

                                    <hr class="my-3">

                                    <p class="mb-3"><strong>تنسيق الملف المتوقع:</strong></p>
                                    <table class="table table-sm table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>رقم الطالب</th>
                                                <th>العنوان</th>
                                                <th>النوع</th>
                                                <th>التاريخ</th>
                                                <th>الحالة</th>
                                                <th>الدرجة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="text-muted">
                                                <td>12345</td>
                                                <td>واجب الفصل 1</td>
                                                <td>واجب</td>
                                                <td>2024-01-20</td>
                                                <td>completed</td>
                                                <td>95</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <a href="{{ route('parent-portal.export-task-template') }}" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-download"></i> تحميل النموذج
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">معلومات إضافية</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info mb-0">
                                        <strong>📋 التعليمات:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>تأكد من أن رقم الطالب موجود في النظام</li>
                                            <li>النوع: "واجب" أو "امتحان"</li>
                                            <li>الحالة: pending (قيد الانتظار) / completed (منجز) / overdue (متأخر)</li>
                                            <li>استخدم صيغة التاريخ: YYYY-MM-DD</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- عرض البيانات المحملة -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4 class="mb-4">البيانات المحملة</h4>
        </div>
        <div class="col-md-4">
            <a href="{{ route('parent-portal.view-attendance') }}" class="text-decoration-none">
                <div class="card text-center border-primary">
                    <div class="card-body">
                        <h5 class="card-title">سجلات الحضور</h5>
                        <p class="card-text text-muted">عرض جميع السجلات المحملة</p>
                        <i class="fas fa-chevron-left"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('parent-portal.view-payments') }}" class="text-decoration-none">
                <div class="card text-center border-success">
                    <div class="card-body">
                        <h5 class="card-title">سجلات الدفع</h5>
                        <p class="card-text text-muted">عرض جميع السجلات المحملة</p>
                        <i class="fas fa-chevron-left"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('parent-portal.view-tasks') }}" class="text-decoration-none">
                <div class="card text-center border-warning">
                    <div class="card-body">
                        <h5 class="card-title">الواجبات</h5>
                        <p class="card-text text-muted">عرض جميع السجلات المحملة</p>
                        <i class="fas fa-chevron-left"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
    }

    .nav-tabs .nav-link {
        color: #666;
        border: none;
        border-bottom: 3px solid transparent;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        border-bottom-color: #0d6efd;
        background: none;
    }

    .nav-tabs .nav-link:hover {
        border-bottom-color: #0d6efd;
    }

    .alert {
        border-radius: 8px;
    }
</style>
@endsection
