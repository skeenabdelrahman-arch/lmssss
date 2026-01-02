@extends('back_layouts.master')

@section('title') إضافة شهر جديد @stop

@section('css')
<style>
    .modern-card { background: #fff; border-radius: 15px; padding: 25px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .repeater-item { 
        background: #fcfaff; 
        padding: 20px; 
        border-radius: 12px; 
        border: 1px solid #efe5f7; 
        margin-bottom: 15px;
        position: relative;
    }
    .form-label { font-weight: 600; color: #444; font-size: 0.9rem; }
    .form-control { border-radius: 10px; padding: 10px; border: 1px solid #ddd; }
    .form-control:focus { border-color: #7424a9; box-shadow: 0 0 0 0.2rem rgba(116, 36, 169, 0.1); }
    
    /* الأزرار */
    .btn-add-row { background: #f0e6f7; color: #7424a9; border: none; border-radius: 10px; font-weight: 700; padding: 10px 20px; transition: 0.3s; }
    .btn-add-row:hover { background: #7424a9; color: #fff; }
    
    .btn-delete-row { 
        background: #212529 !important; 
        color: #fff !important; 
        border-radius: 8px; 
        border: none;
        padding: 10px;
        transition: 0.3s;
    }
    .btn-delete-row:hover { background: #dc3545 !important; }
    
    .btn-save { background: #7424a9 !important; color: #fff; border-radius: 10px; padding: 12px 30px; font-weight: 600; border: none; }
</style>
@endsection

@section('page-header')
<div class="page-header-modern mb-4">
    <h4><i class="fas fa-plus-circle me-2 text-primary"></i> إضافة مجموعة أشهر جديدة</h4>
</div>
@endsection

@section('content')
<div class="modern-card">
    {{-- تنبيهات النظام --}}
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div>
    @endif

    <form action="{{ route('month.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="repeater">
            <div data-repeater-list="List_Monthes">
                <div data-repeater-item class="repeater-item">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">اسم الشهر <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="name" placeholder="مثال: شهر أكتوبر" required/>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">السعر (ج.م) <span class="text-danger">*</span></label>
                            <input class="form-control" type="number" name="price" placeholder="00.00" required/>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">السنة الدراسية <span class="text-danger">*</span></label>
                            <select class="form-control form-select" name="grade" required>
                                <option value="">اختر الصف الدراسي</option>
                                @foreach(signup_grades() as $grade)
                                    <option value="{{ $grade['value'] }}">{{ $grade['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">صورة الغلاف</label>
                            <input class="form-control" type="file" name="image" accept="image/*"/>
                        </div>
                        <div class="col-md-1 d-flex align-items-end justify-content-center">
                            <button data-repeater-delete type="button" class="btn-delete-row w-100" title="حذف هذا الصف">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button data-repeater-create type="button" class="btn-add-row">
                    <i class="fas fa-plus me-1"></i> إضافة صف جديد
                </button>
            </div>

            <div class="mt-5 border-top pt-4">
                <button type="submit" class="btn-save shadow-sm">
                    <i class="fas fa-cloud-upload-alt me-2"></i> تنفيذ الحفظ
                </button>
                <a href="{{ route('month.index') }}" class="btn btn-light ms-2 px-4 py-2" style="border-radius: 10px;">
                    إلغاء
                </a>
            </div>
        </div>
    </form>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js"></script>
<script>
    $(document).ready(function () {
        $('.repeater').repeater({
            initEmpty: false,
            show: function () { $(this).slideDown(); },
            hide: function (deleteElement) { 
                if(confirm('هل تريد حذف هذا الصف؟')) { $(this).slideUp(deleteElement); }
            }
        });
    });
</script>
@endsection