@extends('back_layouts.master')

@section('title', 'أعلى الطلاب في الامتحانات')

@section('css')
<style>
    /* هيدر الصفحة بتأثير زجاجي وتدرج */
    .top-students-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        padding: 50px 30px;
        border-radius: 24px;
        margin-bottom: 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(79, 70, 229, 0.15);
    }
    .top-students-header::after {
        content: ''; position: absolute; top: -50px; left: -50px; width: 200px; height: 200px;
        background: rgba(255,255,255,0.1); border-radius: 50%;
    }

    /* تصميم منصة التتويج (Podium) */
    .podium-container {
        display: flex;
        align-items: flex-end;
        justify-content: center;
        gap: 20px;
        margin-bottom: 50px;
        padding-top: 40px;
    }
    .podium-card {
        background: white;
        border-radius: 20px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        position: relative;
        transition: 0.3s;
        width: 100%;
    }
    .podium-card.first { height: 320px; border: 2px solid #fbbf24; z-index: 2; }
    .podium-card.second { height: 260px; }
    .podium-card.third { height: 240px; }
    
    .podium-rank {
        width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center;
        justify-content: center; font-weight: bold; margin: -45px auto 15px;
        border: 4px solid #fff; font-size: 20px;
    }
    .rank-1 { background: #fbbf24; color: #fff; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4); }
    .rank-2 { background: #94a3b8; color: #fff; }
    .rank-3 { background: #92400e; color: #fff; }

    /* كروت الطلاب العادية */
    .modern-student-row {
        background: white;
        border-radius: 16px;
        margin-bottom: 12px;
        padding: 15px 25px;
        display: flex;
        align-items: center;
        transition: 0.2s;
        border: 1px solid #f1f5f9;
    }
    .modern-student-row:hover { transform: scale(1.01); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }

    /* فورم الفلتر المطور */
    .filter-section {
        background: #f8fafc;
        border-radius: 20px;
        padding: 25px;
        border: 1px dashed #cbd5e1;
    }
    .custom-select-multi {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 10px;
    }

    /* أيقونات الدرجات */
    .stat-badge-soft {
        padding: 8px 16px;
        border-radius: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .bg-soft-purple { background: #f3e8ff; color: #7e22ce; }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <div class="analytics-card mb-4">
        <form method="GET" action="{{ route('admin.analytics.top.students') }}">
            <div class="row align-items-end g-3">
                <div class="col-lg-6">
                    <label class="fw-bold mb-2 text-dark"><i class="fas fa-tasks me-2"></i> حدد نطاق الامتحانات</label>
                    <select name="selected_exams[]" class="form-control custom-select-multi" multiple style="height: 100px;">
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}" {{ in_array($exam->id, $selectedExams ?? []) ? 'selected' : '' }}>
                                {{ $exam->exam_title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="fw-bold mb-2 text-dark">ترتيب حسب</label>
                    <select name="sort_by" class="form-select custom-select-multi">
                        <option value="avg">متوسط الدرجات (الأدق)</option>
                        <option value="max">أعلى درجة محققة</option>
                        <option value="total">إجمالي مجموع الدرجات</option>
                    </select>
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">تطبيق التصفية</button>
                    <a href="{{ route('admin.analytics.top.students') }}" class="btn btn-light rounded-circle p-2"><i class="fas fa-sync"></i></a>
                </div>
            </div>
        </form>
    </div>

    @if($topStudents && $topStudents->count() > 0)
        <div class="row podium-container d-none d-md-flex">
            @php $top3 = $topStudents->take(3); @endphp
            
            @if($top3->count() > 1)
            <div class="col-md-3">
                <div class="podium-card second">
                    <div class="podium-rank rank-2">2</div>
                    <div class="avatar-lg bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:70px; height:70px">
                        <i class="fas fa-user text-muted fa-2x"></i>
                    </div>
                    <h6 class="fw-bold text-dark">{{ $top3[1]->full_name }}</h6>
                    <div class="badge bg-soft-purple mb-3">متوسط: {{ number_format($top3[1]->avg_degree, 1) }}</div>
                    <div class="small text-muted">عدد الاختبارات: {{ $top3[1]->total_exams }}</div>
                </div>
            </div>
            @endif

            <div class="col-md-4">
                <div class="podium-card first">
                    <div class="podium-rank rank-1">1</div>
                    <div class="avatar-lg bg-warning-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:90px; height:90px; border: 4px solid #fbbf24">
                        <i class="fas fa-crown text-warning fa-3x"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">{{ $top3[0]->full_name }}</h5>
                    <p class="text-primary fw-bold mb-2">{{ $top3[0]->grade }}</p>
                    <div class="stat-badge-soft bg-warning text-dark mb-3">
                        <i class="fas fa-star"></i> {{ number_format($top3[0]->avg_degree, 1) }}
                    </div>
                    <div class="d-flex justify-content-around mt-2">
                        <div class="small"><b>{{ $top3[0]->total_exams }}</b><br>امتحانات</div>
                        <div class="small"><b>{{ $top3[0]->max_degree }}</b><br>أعلى درجة</div>
                    </div>
                </div>
            </div>

            @if($top3->count() > 2)
            <div class="col-md-3">
                <div class="podium-card third">
                    <div class="podium-rank rank-3">3</div>
                    <div class="avatar-lg bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:60px; height:60px">
                        <i class="fas fa-user text-muted fa-xl"></i>
                    </div>
                    <h6 class="fw-bold text-dark">{{ $top3[2]->full_name }}</h6>
                    <div class="badge bg-soft-purple mb-3">{{ number_format($top3[2]->avg_degree, 1) }}</div>
                    <div class="small text-muted">الامتحانات: {{ $top3[2]->total_exams }}</div>
                </div>
            </div>
            @endif
        </div>

        <div class="analytics-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">قائمة المتصدرين الكاملة</h5>
                <div class="actions">
                    <button onclick="exportTableToExcel('topStudentsTable', 'المتصدرين')" class="btn btn-sm btn-outline-success rounded-pill"><i class="fas fa-file-excel"></i> Excel</button>
                    <button onclick="window.print()" class="btn btn-sm btn-outline-primary rounded-pill"><i class="fas fa-print"></i> طباعة</button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="topStudentsTable">
                    <thead class="text-muted small">
                        <tr>
                            <th>الترتيب</th>
                            <th>الطالب</th>
                            <th>الصف</th>
                            <th class="text-center">الامتحانات</th>
                            <th class="text-center">المتوسط</th>
                            <th class="text-center">أعلى درجة</th>
                            <th class="text-center">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topStudents as $index => $student)
                        <tr>
                            <td>
                                @if($index < 3)
                                    <span class="badge rounded-pill {{ $index == 0 ? 'bg-warning' : ($index == 1 ? 'bg-secondary' : 'bg-danger') }}">
                                        {{ $index + 1 }}
                                    </span>
                                @else
                                    <span class="text-muted fw-bold ms-2">#{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="name-initial me-2 bg-light p-2 rounded-circle" style="width:35px; height:35px; font-size:12px; text-align:center">
                                        {{ mb_substr($student->full_name, 0, 1) }}
                                    </div>
                                    <span class="fw-bold">{{ $student->full_name }}</span>
                                </div>
                            </td>
                            <td><span class="small text-muted">{{ $student->grade }}</span></td>
                            <td class="text-center"><span class="badge bg-light text-dark">{{ $student->total_exams }}</span></td>
                            <td class="text-center fw-bold text-primary">{{ number_format($student->avg_degree, 1) }}%</td>
                            <td class="text-center"><span class="text-success fw-bold">{{ $student->max_degree }}</span></td>
                            <td class="text-center text-muted small">{{ $student->total_degree }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="text-center py-5 analytics-card">
            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="100" class="mb-3 opacity-25">
            <h4 class="text-muted">لا توجد بيانات كافية لعرض لوحة الصدارة</h4>
        </div>
    @endif
</div>
@endsection