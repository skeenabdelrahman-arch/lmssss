@extends('back_layouts.master')

@section('title')
إضافة طالب جديد
@stop

@section('page-header')
<div class="page-header-modern">
    <h4><i class="fas fa-user-plus me-2"></i> إضافة طالب جديد</h4>
</div>
@endsection

@section('content')

<style>
/* Wizard Layout */
.wizard-container {
    background: #fff;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 15px 30px rgba(0,0,0,.06);
}

/* Progress Bar */
.progress {
    height: 10px;
    border-radius: 20px;
    overflow: hidden;
    background: #eee;
    margin-bottom: 30px;
}
.progress-bar {
    transition: width .4s ease;
}

/* Steps */
.wizard-step {
    display: none;
    animation: fadeSlide .4s ease;
}
.wizard-step.active {
    display: block;
}

@keyframes fadeSlide {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.step-title {
    font-weight: 700;
    margin-bottom: 20px;
    color: #444;
}

.form-label {
    font-weight: 600;
}
</style>

<div class="wizard-container">

    {{-- Progress Bar --}}
    <div class="progress">
        <div class="progress-bar bg-primary" id="progressBar" style="width: 33%"></div>
    </div>

    <form method="POST" action="{{ route('admin.student.store') }}" enctype="multipart/form-data">
    @csrf

    {{-- STEP 1 --}}
    <div class="wizard-step active" data-step="1">
        <h5 class="step-title"><i class="fas fa-id-card me-2"></i> البيانات الأساسية</h5>

        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">الاسم الأول</label>
                <input type="text" name="first_name" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">الاسم الثاني</label>
                <input type="text" name="second_name" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">الاسم الثالث</label>
                <input type="text" name="third_name" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">الاسم الرابع</label>
                <input type="text" name="forth_name" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">رقم تليفون الطالب <span class="text-danger">*</span></label>
                <input type="text" name="student_phone" class="form-control" required>
            </div>
        </div>

        <div class="text-end mt-4">
            <button type="button" class="btn btn-primary next-step">
                التالي <i class="fas fa-arrow-left ms-1"></i>
            </button>
        </div>
    </div>

    {{-- STEP 2 --}}
    <div class="wizard-step" data-step="2">
        <h5 class="step-title"><i class="fas fa-layer-group me-2"></i> بيانات الاشتراك</h5>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">الصف</label>
                <select name="grade" class="form-select">
                    <option value="">اختر الصف</option>
                    @foreach(signup_grades() as $grade)
                        <option value="{{ $grade['value'] }}">{{ $grade['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">مصدر التسجيل</label>
                <select name="registration_source" class="form-select">
                    <option value="admin">لوحة التحكم</option>
                    <option value="online">أونلاين</option>
                    <option value="excel_import">Excel</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">رقم التسجيل</label>
                <input type="text" name="register" class="form-control">
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary prev-step">
                <i class="fas fa-arrow-right me-1"></i> السابق
            </button>
            <button type="button" class="btn btn-primary next-step">
                التالي <i class="fas fa-arrow-left ms-1"></i>
            </button>
        </div>
    </div>

    {{-- STEP 3 --}}
    <div class="wizard-step" data-step="3">
        <h5 class="step-title"><i class="fas fa-lock me-2"></i> الحساب والصورة</h5>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">كلمة المرور</label>
                <input type="password" name="password" class="form-control" required minlength="8">
            </div>

            <div class="col-md-6">
                <label class="form-label">تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" class="form-control" required minlength="8">
            </div>

            <div class="col-md-12">
                <label class="form-label">صورة الطالب</label>
                <input type="file" name="image" class="form-control mb-2" accept="image/*">
                <x-media-picker name="image_url" type="images" label="اختر من المكتبة" />
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary prev-step">
                <i class="fas fa-arrow-right me-1"></i> السابق
            </button>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save me-1"></i> حفظ الطالب
            </button>
        </div>
    </div>

    </form>
</div>

@endsection

@section('js')
<script>
let currentStep = 1;
const totalSteps = 3;

function updateWizard() {
    document.querySelectorAll('.wizard-step').forEach(step => step.classList.remove('active'));
    document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.add('active');

    const percent = (currentStep / totalSteps) * 100;
    document.getElementById('progressBar').style.width = percent + '%';
}

document.querySelectorAll('.next-step').forEach(btn => {
    btn.addEventListener('click', () => {
        if (currentStep < totalSteps) {
            currentStep++;
            updateWizard();
        }
    });
});

document.querySelectorAll('.prev-step').forEach(btn => {
    btn.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            updateWizard();
        }
    });
});
</script>
@endsection
