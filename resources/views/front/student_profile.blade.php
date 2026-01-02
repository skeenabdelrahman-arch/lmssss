@extends('front.layouts.app')
@section('title')
بيانات الطالب | {{ site_name() }}
@endsection

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
        --primary-light: #b05ee7;
    }

    .profile-section {
        padding: 120px 0 80px;
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.03), rgba(250, 137, 107, 0.03));
        min-height: calc(100vh - 90px);
    }

    .profile-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 20px;
        padding: 40px;
        text-align: center;
        color: white;
        margin-bottom: 40px;
        box-shadow: 0 10px 40px rgba(116, 36, 169, 0.2);
    }

    .profile-img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 5px solid white;
        object-fit: cover;
        margin-bottom: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
    }

    .profile-header h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .profile-header p {
        font-size: 1.2rem;
        opacity: 0.9;
    }

    .profile-tabs {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .nav-tabs {
        border: none;
        margin-bottom: 30px;
    }

    .nav-tabs .nav-link {
        border: none;
        color: var(--primary-color);
        font-weight: 600;
        padding: 15px 25px;
        margin: 0 5px;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link:hover {
        background: rgba(116, 36, 169, 0.1);
    }

    .nav-tabs .nav-link.active {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
    }

    .tab-content {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
    }

    .info-table th {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 15px;
        text-align: right;
        font-weight: 600;
        border-radius: 8px;
    }

    .info-table td {
        padding: 15px;
        background: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
    }

    .info-table tr:last-child td {
        border-bottom: none;
    }

    .month-card {
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
        margin-bottom: 20px;
    }

    .month-card:hover {
        border-color: var(--primary-color);
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(116, 36, 169, 0.15);
        text-decoration: none;
    }

    .month-card h5 {
        color: var(--primary-color);
        font-weight: 700;
        margin: 0;
    }

    .exam-result-card {
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .exam-result-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 5px 20px rgba(116, 36, 169, 0.1);
    }

    .exam-result-card a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
    }

    .exam-result-card a:hover {
        color: var(--secondary-color);
    }

    .progress {
        height: 25px;
        background: #e0e0e0;
        border-radius: 15px;
        overflow: hidden;
    }

    .progress-bar {
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        transition: width 1s ease;
    }

    .password-section {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 30px;
        margin-top: 30px;
    }

    .password-section h5 {
        color: var(--primary-color);
        margin-bottom: 20px;
        font-weight: 700;
    }

    .form-control {
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(116, 36, 169, 0.1);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(116, 36, 169, 0.3);
    }

    .image-upload-section {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        padding: 20px;
        margin-top: 20px;
    }

    .alert {
        border-radius: 12px;
        padding: 15px 20px;
        margin-bottom: 20px;
        border: none;
    }

    .alert-info {
        background: #e3f2fd;
        color: #1976d2;
        border-right: 4px solid #1976d2;
    }
</style>

<section class="profile-section">
    <div class="container">
        <div class="profile-header">
            @if ($student->image)
                <img src="{{url('upload_files/'.$student->image)}}" alt="Profile Picture" class="profile-img">
            @else
                <img src="{{url('front/assets/images/avatar.jpg')}}" alt="Profile Picture" class="profile-img">
            @endif
            <h2>{{$student->first_name}} {{$student->second_name}} {{$student->third_name}} {{$student->forth_name}}</h2>
            @if($student->grade)
                @php
                    $gradeLabel = $student->grade;
                    foreach(signup_grades() as $grade) {
                        if($grade['value'] == $student->grade) {
                            $gradeLabel = $grade['label'];
                            break;
                        }
                    }
                @endphp
                <p>{{ $gradeLabel }}</p>
            @endif

            <form method="POST" action="{{route('updateImage')}}" enctype="multipart/form-data" autocomplete="off" class="image-upload-section">
                @csrf
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label text-white" style="font-weight: 600;">
                                <i class="fas fa-camera me-2"></i>صورة شخصية
                            </label>
                            <input type="file" name="image" class="form-control" accept="image/*" {{ empty($student->image) ? 'required' : '' }}>
                            <small class="text-white-50 d-block mt-1">يجب أن تكون الصورة واضحة وحديثة</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white" style="font-weight: 600;">
                                <i class="fas fa-envelope me-2"></i>البريد الإلكتروني
                            </label>
                            <input type="email" name="email" class="form-control" value="{{ $student->email }}" placeholder="example@email.com" required>
                            <small class="text-white-50 d-block mt-1">سيتم استخدام هذا الإيميل للتواصل معك</small>
                        </div>
                        <button type="submit" class="btn btn-light w-100" style="font-weight: 600;">
                            <i class="fas fa-save me-2"></i>{{ empty($student->image) ? 'حفظ الصورة والإيميل والمتابعة' : 'تحديث الصورة والإيميل' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="profile-tabs">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#info">
                        <i class="fas fa-user me-2"></i>معلومات تعريفية
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#months">
                        <i class="fas fa-calendar me-2"></i>الشهور المشتركة
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#grades">
                        <i class="fas fa-graduation-cap me-2"></i>درجات الامتحانات
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- معلومات الطالب -->
                <div class="tab-pane fade show active" id="info">
                    <table class="info-table">
                        <tbody>
                            <tr>
                                <th>الاسم الكامل</th>
                                <td>{{$student->first_name}} {{$student->second_name}} {{$student->third_name}} {{$student->forth_name}}</td>
                            </tr>
                            <tr>
                                <th>الصف</th>
                                <td>
                                    @php
                                        $gradeLabel = $student->grade;
                                        foreach(signup_grades() as $grade) {
                                            if($grade['value'] == $student->grade) {
                                                $gradeLabel = $grade['label'];
                                                break;
                                            }
                                        }
                                    @endphp
                                    {{ $gradeLabel }}
                                </td>
                            </tr>
                            <tr>
                                <th>البريد الإلكتروني</th>
                                <td>{{$student->email ?? 'غير محدد'}}</td>
                            </tr>
                            <tr>
                                <th>نوع التسجيل</th>
                                <td>{{$student->register}}</td>
                            </tr>
                            <tr>
                                <th>رقم التليفون</th>
                                <td>{{$student->student_phone}}</td>
                            </tr>
                            <tr>
                                <th>رقم ولي الأمر</th>
                                <td>{{$student->parent_phone}}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="password-section">
                        <h5><i class="fas fa-key me-2"></i>تغيير كلمة المرور</h5>
                        <form method="POST" action="{{ route('updatePassword') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">كلمة المرور الحالية</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">كلمة المرور الجديدة</label>
                                    <input type="password" name="new_password" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">تأكيد كلمة المرور الجديدة</label>
                                    <input type="password" name="new_password_confirmation" class="form-control" required>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>تغيير كلمة المرور
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- الشهور المشتركة -->
                <div class="tab-pane fade" id="months">
                    @php
                        $activeMonths = $months->filter(function($subscription) {
                            return $subscription->month && $subscription->is_active == 1;
                        });
                    @endphp
                    
                    @if($activeMonths->count() > 0)
                        <div class="row">
                            @foreach ($activeMonths as $subscription)
                                <div class="col-md-4 mb-3">
                                    <a href="{{route('course_details', $subscription->month_id)}}" class="month-card">
                                        <i class="fas fa-calendar-alt fa-3x mb-3" style="color: var(--primary-color);"></i>
                                        <h5>{{ $subscription->month->name }}</h5>
                                        <p class="text-muted mb-0">
                                            <span class="badge bg-success">مفعل</span>
                                        </p>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>لا توجد كورسات مفعلة حالياً</strong>
                            <p class="mb-0 mt-2">لم يتم تفعيل أي كورسات لك حتى الآن. يرجى التواصل مع الإدارة.</p>
                        </div>
                    @endif
                </div>

                <!-- درجات الامتحانات -->
                <div class="tab-pane fade" id="grades">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        اضغط على اسم الامتحان لمراجعة الإجابات والأسئلة.
                    </div>
                    @if(isset($exam_results) && $exam_results->count() > 0)
                        @foreach ($exam_results as $result)
                            @if($result->exam && $result->exam->questions)
                                @php
                                    $exam_degree = $result->exam->questions->sum('Q_degree');
                                    $percentage = $exam_degree ? round(($result->degree / $exam_degree) * 100) : 0;
                                @endphp
                                <div class="exam-result-card">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h5><a href="{{ route('exam_review', ['exam_id' => $result->exam->id]) }}">{{$result->exam->exam_title}}</a></h5>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <strong style="color: var(--primary-color); font-size: 1.2rem;">{{$result->degree}} / {{$exam_degree}}</strong>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="progress">
                                                <div class="progress-bar" style="width: {{$percentage}}%">{{$percentage}}%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-clipboard-list" style="font-size: 3rem; opacity: 0.3; display: block; margin-bottom: 15px;"></i>
                            <p>لا توجد نتائج امتحانات حتى الآن</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    @if(request('force') == 1 && empty($student->image))
        // إظهار رسالة توضيحية عند التوجيه القسري
        document.addEventListener('DOMContentLoaded', function() {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-warning';
            alertDiv.style.cssText = 'margin: 20px auto; max-width: 600px; text-align: center; border-radius: 15px; padding: 20px;';
            alertDiv.innerHTML = `
                <i class="fas fa-exclamation-triangle fa-2x mb-3" style="color: #ff9800;"></i>
                <h4 style="color: #ff9800; margin-bottom: 10px;">إكمال ملفك الشخصي</h4>
                <p>من فضلك قم برفع صورة شخصية وإدخال بريدك الإلكتروني لإكمال ملفك الشخصي والبدء في تصفح الكورسات.</p>
            `;
            document.querySelector('.profile-section .container').insertBefore(alertDiv, document.querySelector('.profile-header'));
        });
    @endif
</script>

@endsection
