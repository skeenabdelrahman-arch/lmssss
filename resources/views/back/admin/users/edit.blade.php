@extends('back_layouts.master')

@section('title')
    تعديل مستخدم
@stop

@section('css')
<style>
    .modern-card {
        background: #ffffff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        border: none;
    }
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
    }
    .form-control, .form-select {
        border-radius: 10px;
        padding: 12px 15px;
        border: 1px solid #e9ecef;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        background-color: #fff;
        border-color: #7424a9;
        box-shadow: 0 0 0 0.2rem rgba(116, 36, 169, 0.1);
    }
    .btn-modern-primary {
        background: #7424a9 !important;
        border: none;
        border-radius: 10px;
        padding: 12px 25px;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-modern-primary:hover {
        background: #5a1a85 !important;
        transform: translateY(-2px);
    }
    .btn-modern-secondary {
        background: #212529 !important;
        color: #fff !important;
        border-radius: 10px;
        padding: 12px 25px;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-modern-secondary:hover {
        background: #dc3545 !important; /* يتحول للأحمر عند الإلغاء */
        transform: translateY(-2px);
    }
    .input-group-text {
        border-radius: 10px 0 0 10px;
        background: #f8f9fa;
    }
</style>
@endsection

@section('page-header')
<div class="page-header-modern mb-4">
    <h4><i class="fas fa-user-edit me-2 text-primary"></i> تعديل بيانات المستخدم: <span class="text-muted">{{ $user->name }}</span></h4>
</div>
@endsection

@section('content')
<div class="modern-card">
    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required placeholder="أدخل اسم المستخدم">
            </div>
            <div class="col-md-6">
                <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required placeholder="example@mail.com">
            </div>
            
            <div class="col-md-12">
                <div class="alert alert-light border-0 py-2 small text-muted" style="background: #fdfcfe;">
                    <i class="fas fa-info-circle me-1"></i> اترك حقول كلمة المرور فارغة إذا كنت لا ترغب في تغييرها.
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">كلمة المرور الجديدة</label>
                <input type="password" name="password" class="form-control" minlength="8" placeholder="••••••••">
            </div>
            <div class="col-md-6">
                <label class="form-label">تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" class="form-control" minlength="8" placeholder="••••••••">
            </div>
            
            <div class="col-md-12">
                <label class="form-label">الدور / الصلاحية <span class="text-danger">*</span></label>
                <select name="role_id" class="form-select" required>
                    <option value="" disabled>-- اختر الدور الوظيفي --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 mt-4 border-top pt-4">
                <button type="submit" class="btn btn-modern-primary shadow-sm">
                    <i class="fas fa-check-circle me-2"></i> حفظ التغييرات
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-modern-secondary shadow-sm ms-2">
                    <i class="fas fa-times-circle me-2"></i> إلغاء
                </a>
            </div>
        </div>
    </form>
</div>
@endsection