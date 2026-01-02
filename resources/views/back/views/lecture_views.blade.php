@extends('back_layouts.master')

@section('title') متابعة المشاهدات: {{ $lecture->title }} @stop

@section('css')
<style>
    :root { 
        --primary-grad: linear-gradient(135deg, #6e8efb, #a777e3);
        --success-grad: linear-gradient(135deg, #2ecc71, #1abc9c);
        --danger-grad: linear-gradient(135deg, #ff7675, #d63031);
    }

    /* ستايل الكروت العلوية */
    .modern-stat-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 25px;
        position: relative;
        overflow: hidden;
        border: none;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        display: flex;
        align-items: center;
        height: 100%;
    }
    .modern-stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    }
    .stat-icon-wrapper {
        width: 65px;
        height: 65px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        margin-left: 20px;
        flex-shrink: 0;
        z-index: 1;
    }
    .stat-data { z-index: 1; }
    .stat-data h3 { font-size: 1.8rem; font-weight: 800; margin-bottom: 2px; color: #2d3436; }
    .stat-data p { font-size: 0.85rem; font-weight: 600; color: #636e72; margin: 0; }
    
    .card-wave {
        position: absolute;
        bottom: -20px;
        left: -20px;
        opacity: 0.05;
        font-size: 80px;
        transform: rotate(-20deg);
        z-index: 0;
    }

    /* ستايل الجداول والتبويبات */
    .main-container { background: #fff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); overflow: hidden; border: 1px solid #f1f5f9; }
    .nav-tabs-modern { border: none; background: #f1f5f9; padding: 6px; border-radius: 15px; display: inline-flex; margin-bottom: 20px; }
    .nav-tabs-modern .nav-link { border: none; color: #64748b; font-weight: 700; padding: 10px 25px; border-radius: 12px; transition: 0.3s; }
    .nav-tabs-modern .nav-link.active { background: #fff; color: #764ba2; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    
    .search-box-wrapper { position: relative; max-width: 350px; width: 100%; }
    .search-box-wrapper input { border-radius: 12px; border: 1px solid #e2e8f0; padding: 12px 45px 12px 15px; width: 100%; outline: none; transition: 0.3s; background: #f8fafc; }
    .search-box-wrapper i { position: absolute; right: 15px; top: 15px; color: #94a3b8; }
    .search-box-wrapper input:focus { border-color: #764ba2; background: #fff; box-shadow: 0 0 0 4px rgba(118, 75, 162, 0.1); }

    .table thead th { background: #fcfcfd; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; padding: 15px; border-top: none; }
    .avatar-char { width: 40px; height: 40px; border-radius: 12px; background: #f1f5f9; color: #475569; display: flex; align-items: center; justify-content: center; font-weight: 800; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-1">لوحة متابعة المحاضرة</h2>
            <p class="text-muted mb-0">
                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                    <i class="fas fa-play-circle me-1 text-primary"></i> {{ $lecture->title }}
                </span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-dark fw-bold px-4 rounded-pill">
                <i class="fas fa-print me-2"></i> طباعة التقرير
            </button>
            <a href="{{ route('lecture.index') }}" class="btn btn-dark fw-bold px-4 rounded-pill shadow-sm">
                <i class="fas fa-arrow-left me-2"></i> العودة
            </a>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-xl-4 col-md-6">
            <div class="modern-stat-card">
                <div class="stat-icon-wrapper bg-soft-blue shadow-sm">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-data">
                    <h3>{{ count($studentsData) }}</h3>
                    <p>إجمالي المشتركين</p>
                </div>
                <i class="fas fa-users card-wave"></i>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="modern-stat-card">
                <div class="stat-icon-wrapper bg-soft-green shadow-sm">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-data">
                    <h3>{{ collect($studentsData)->where('viewed', true)->count() }}</h3>
                    <p>طلاب شاهدوا الدرس</p>
                </div>
                <i class="fas fa-play-circle card-wave"></i>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="modern-stat-card">
                <div class="stat-icon-wrapper bg-soft-red shadow-sm">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="stat-data">
                    <h3>{{ collect($studentsData)->where('viewed', false)->count() }}</h3>
                    <p>طلاب لم يشاهدوا بعد</p>
                </div>
                <i class="fas fa-exclamation-triangle card-wave"></i>
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="p-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 bg-white">
            <ul class="nav nav-tabs-modern" id="viewTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#viewed" type="button">
                        شاهدوا المحاضرة
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#notViewed" type="button">
                        لم يشاهدوا بعد
                    </button>
                </li>
            </ul>
            
            <div class="search-box-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" id="mainSearch" placeholder="ابحث عن اسم طالب بالكامل...">
            </div>
        </div>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="viewed">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">اسم الطالب</th>
                                <th>بيانات التواصل</th>
                                <th>الصف</th>
                                <th>تاريخ المشاهدة</th>
                            </tr>
                        </thead>
                        <tbody class="student-table-body">
                            @forelse(collect($studentsData)->where('viewed', true) as $data)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-char">{{ mb_substr($data['student']->first_name, 0, 1) }}</div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $data['student']->first_name }} {{ $data['student']->second_name }} {{ $data['student']->third_name }}</div>
                                            <small class="text-muted">ID: #{{ $data['student']->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-semibold"><i class="fas fa-phone-alt me-1 text-muted"></i> {{ $data['student']->student_phone ?: '-' }}</div>
                                    <div class="small text-muted"><i class="fas fa-envelope me-1 text-muted"></i> {{ $data['student']->email ?: '-' }}</div>
                                </td>
                                <td><span class="badge bg-soft-primary text-primary px-3 py-2">{{ $data['student']->grade }}</span></td>
                                <td>
                                    <span class="text-success fw-bold small">
                                        <i class="fas fa-calendar-check me-1"></i>
                                        {{ $data['viewed_at'] ? $data['viewed_at']->format('Y-m-d | h:i A') : '-' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted">لا يوجد بيانات لعرضها</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="notViewed">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">اسم الطالب</th>
                                <th>بيانات التواصل</th>
                                <th>الصف</th>
                                <th class="text-center">الملف الشخصي</th>
                            </tr>
                        </thead>
                        <tbody class="student-table-body">
                            @forelse(collect($studentsData)->where('viewed', false) as $data)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-char" style="background:#fff1f2; color:#e11d48;">{{ mb_substr($data['student']->first_name, 0, 1) }}</div>
                                        <div class="fw-bold text-dark">{{ $data['student']->first_name }} {{ $data['student']->second_name }} {{ $data['student']->third_name }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-semibold"><i class="fas fa-phone-alt me-1 text-muted"></i> {{ $data['student']->student_phone ?: '-' }}</div>
                                    <div class="small text-muted"><i class="fas fa-envelope me-1 text-muted"></i> {{ $data['student']->email ?: '-' }}</div>
                                </td>
                                <td><span class="badge bg-light text-dark px-3 py-2">{{ $data['student']->grade }}</span></td>
                                <td class="text-center">
                                    <a href="{{ url('student-profile/' . $data['student']->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        عرض <i class="fas fa-external-link-alt ms-1 small"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-5 text-success fw-bold">ممتاز! جميع الطلاب المسجلين شاهدوا المحاضرة</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function(){
        // نظام البحث السريع
        $("#mainSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".student-table-body tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
@endsection