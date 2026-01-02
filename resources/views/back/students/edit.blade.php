@extends('back_layouts.master')

@section('title')
تعديل بيانات الطالب
@stop

@section('css')
<style>
/* ===== Wizard Layout ===== */
.wizard-wrapper {
    background: #fff;
    border-radius: 14px;
    padding: 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,.06);
}

/* Progress Bar */
.wizard-progress {
    height: 6px;
    background: #eee;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 25px;
}
.wizard-progress-bar {
    height: 100%;
    width: 33%;
    background: linear-gradient(90deg,#7424a9,#fa896b);
    transition: width .4s ease;
}

/* Steps Nav */
.wizard-steps {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
}
.wizard-step-title {
    flex: 1;
    text-align: center;
    font-weight: 600;
    color: #aaa;
    position: relative;
}
.wizard-step-title.active {
    color: #7424a9;
}
.wizard-step-title::after {
    content: '';
    width: 10px;
    height: 10px;
    background: currentColor;
    border-radius: 50%;
    display: block;
    margin: 8px auto 0;
}

/* Step Content */
.wizard-step {
    display: none;
    animation: fadeSlide .4s ease;
}
.wizard-step.active {
    display: block;
}

@keyframes fadeSlide {
    from {opacity: 0; transform: translateY(15px);}
    to {opacity: 1; transform: translateY(0);}
}

/* Buttons */
.wizard-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 30px;
}
</style>
@endsection

@section('page-header')
<div class="page-header-modern">
    <h4><i class="fas fa-edit me-2"></i> تعديل بيانات الطالب</h4>
</div>
@endsection

@section('content')

<div class="wizard-wrapper">

    {{-- Progress --}}
    <div class="wizard-progress">
        <div class="wizard-progress-bar" id="progressBar"></div>
    </div>

    {{-- Steps --}}
    <div class="wizard-steps">
        <div class="wizard-step-title active">البيانات الأساسية</div>
        <div class="wizard-step-title">الاشتراك</div>
        <div class="wizard-step-title">الحساب</div>
    </div>

    <form action="{{route('Student_update',$student->id)}}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- STEP 1 --}}
    <div class="wizard-step active">
        <div class="row g-3">
            @foreach(['first'=>'الأول','second'=>'الثاني','third'=>'الثالث','forth'=>'الرابع'] as $key => $label)
            <div class="col-md-6">
                <label class="form-label">الاسم {{ $label }}</label>
                <input class="form-control" name="{{ $key }}_name" value="{{ $student[$key.'_name'] }}" required>
            </div>
            @endforeach

            <div class="col-md-6">
                <label class="form-label">هاتف الطالب</label>
                <input class="form-control" name="student_phone" value="{{ $student->student_phone }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">هاتف ولي الأمر</label>
                <input class="form-control" name="parent_phone" value="{{ $student->parent_phone }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">المحافظة</label>
                <select name="city" class="form-select">
                    @foreach(governorates_list() as $gov)
                        <option value="{{ $gov['name'] }}" {{ $student->city == $gov['name'] ? 'selected' : '' }}>
                            {{ $gov['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">النوع</label>
                <select name="gender" class="form-select">
                    <option value="ذكر" {{ $student->gender=='ذكر'?'selected':'' }}>ذكر</option>
                    <option value="انثي" {{ $student->gender=='انثي'?'selected':'' }}>أنثى</option>
                </select>
            </div>
        </div>
    </div>

    {{-- STEP 2 --}}
    <div class="wizard-step">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">المرحلة</label>
                <select name="grade" class="form-select">
                    @foreach(signup_grades() as $grade)
                        <option value="{{ $grade['value'] }}" {{ $student->grade==$grade['value']?'selected':'' }}>
                            {{ $grade['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">نوع التسجيل</label>
                <select name="register" class="form-select">
                    <option value="اونلاين">أونلاين</option>
                </select>
            </div>

            <div class="col-12 mt-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="has_all_access" value="1" {{ $student->has_all_access?'checked':'' }}>
                    <label class="form-check-label">اشتراك شامل لكل الكورسات</label>
                </div>
            </div>
        </div>
    </div>

    {{-- STEP 3 --}}
    <div class="wizard-step">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">كلمة السر</label>
                <input class="form-control" name="password" value="{{ $student->password }}">
            </div>

            <div class="col-md-6">
                <label class="form-label">الصورة</label>
                <input type="file" class="form-control mb-2" name="image">
                <x-media-picker name="image_url" type="images" />
                @if($student->image)
                    <img src="{{url('upload_files/'.$student->image)}}" class="mt-2 rounded" width="100">
                @endif
            </div>
        </div>
    </div>

    {{-- Buttons --}}
    <div class="wizard-actions">
        <button type="button" class="btn btn-modern btn-modern-secondary" id="prevBtn">السابق</button>
        <button type="button" class="btn btn-modern btn-modern-primary" id="nextBtn">التالي</button>
        <button type="submit" class="btn btn-modern btn-modern-success d-none" id="submitBtn">
            <i class="fas fa-save me-2"></i> حفظ التعديلات
        </button>
    </div>

    </form>
</div>

@endsection

@section('js')
<script>
let current = 0;
const steps = document.querySelectorAll('.wizard-step');
const titles = document.querySelectorAll('.wizard-step-title');
const progress = document.getElementById('progressBar');

function updateWizard() {
    steps.forEach((s,i)=>s.classList.toggle('active',i===current));
    titles.forEach((t,i)=>t.classList.toggle('active',i===current));

    progress.style.width = ((current+1)/steps.length)*100 + '%';

    document.getElementById('prevBtn').style.display = current === 0 ? 'none' : 'inline-block';
    document.getElementById('nextBtn').classList.toggle('d-none', current === steps.length-1);
    document.getElementById('submitBtn').classList.toggle('d-none', current !== steps.length-1);
}

document.getElementById('nextBtn').onclick = ()=>{ if(current < steps.length-1){ current++; updateWizard(); } }
document.getElementById('prevBtn').onclick = ()=>{ if(current > 0){ current--; updateWizard(); } }

updateWizard();
</script>
@endsection
