@extends('back_layouts.master')

@section('title') تعديل شهر @stop

@section('css')
<style>
    .modern-card { background: #fff; border-radius: 15px; padding: 30px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .form-label { font-weight: 600; color: #444; }
    .form-control { border-radius: 10px; padding: 12px; background: #f8f9fa; }
    .current-img-box {
        border: 2px dashed #ddd;
        border-radius: 15px;
        padding: 10px;
        display: inline-block;
        background: #fcfcfc;
    }
    .btn-update { background: #7424a9 !important; color: #fff; border-radius: 10px; padding: 12px 30px; border: none; font-weight: 600; }
    .btn-cancel { background: #212529 !important; color: #fff !important; border-radius: 10px; padding: 12px 30px; border: none; }
    .btn-cancel:hover { background: #dc3545 !important; }
</style>
@endsection

@section('page-header')
<div class="page-header-modern mb-4">
    <h4><i class="fas fa-edit me-2 text-primary"></i> تعديل بيانات الشهر: <span class="text-muted">{{ $month->name }}</span></h4>
</div>
@endsection

@section('content')
<div class="modern-card">
    <form action="{{route('month.update',$month->id)}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row g-4">
            <div class="col-md-4">
                <label class="form-label">اسم الشهر</label>
                <input class="form-control" type="text" name="name" value="{{$month->name}}" required/>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">السنة الدراسية</label>
                <select class="form-control form-select" name="grade" required>
                    @foreach(signup_grades() as $grade)
                        <option value="{{ $grade['value'] }}" {{ $month->grade == $grade['value'] ? 'selected' : '' }}>
                            {{ $grade['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">السعر (ج.م)</label>
                <input class="form-control" type="text" name="price" value="{{$month->price}}" required/>
            </div>

            <div class="col-md-12">
                <label class="form-label">صورة الغلاف الجديدة</label>
                <input class="form-control mb-3" type="file" name="image" accept="image/*"/>
                
                @if($month->image)
                    <div class="current-img-box shadow-sm">
                        <p class="small fw-bold text-muted mb-2"><i class="fas fa-image me-1"></i> الغلاف الحالي:</p>
                        <img src="{{url('upload_files/'.$month->image)}}" alt="month_img" style="width: 180px; height: 120px; border-radius: 10px; object-fit: cover;">
                    </div>
                @endif
                
                <div class="alert alert-light border-0 mt-3 small text-muted">
                    <i class="fas fa-info-circle me-1"></i> نصيحة: استخدم صوراً بمقاس 1200×800 للحصول على أفضل مظهر في تطبيق الطلاب.
                </div>
            </div>

            <div class="col-12 mt-4 border-top pt-4">
                <button type="submit" class="btn-update shadow-sm">
                    <i class="fas fa-save me-2"></i> حفظ التعديلات
                </button>
                <a href="{{ route('month.index') }}" class="btn-cancel ms-2 shadow-sm text-decoration-none d-inline-block text-center">
                    <i class="fas fa-times me-1"></i> إلغاء
                </a>
            </div>
        </div>
    </form>
</div>
@endsection