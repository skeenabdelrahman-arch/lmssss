@extends('back_layouts.master')

@section('title')
    إضافة مستخدم جديد
@stop

@section('css')
<style>
    /* نستخدم نفس ستايل التعديل لضمان التناسق */
    .modern-card {
        background: #ffffff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        border: none;
    }
    .form-label { font-weight: 600; color: #495057; margin-bottom: 8px; }
    .form-control, .form-select {
        border-radius: 10px;
        padding: 12px 15px;
        border: 1px solid #e9ecef;
        background-color: #f8f9fa;
        transition: 0.3s;
    }
    .form-control:focus { border-color: #7424a9; background: #fff; }
    .btn-modern-primary {
        background: #7424a9 !important;
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
    }
    .btn-modern-secondary {
        background: #212529 !important;
        color: #fff !important;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
    }
    .btn-modern-secondary:hover { background: #dc3545 !important; }
</style>
@endsection

@section('page-header')
<div class="page-header-modern mb-4">
    <h4><i class="fas fa-user-plus me-2 text-primary"></i> إنشاء حساب مستخدم جديد</h4>
</div>
@endsection

@section('content')
<div class="modern-card">
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label">الاسم بالكامل <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="text" name="name" class="form-control" required placeholder="مثال: أحمد محمد">
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" required placeholder="user@domain.com">
            </div>
            
            <div class="col-md-6">
                <label class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" required minlength="8" placeholder="••••••••">
            </div>
            <div class="col-md-6">
                <label class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" required minlength="8" placeholder="••••••••">
            </div>
            
            <div class="col-md-12">
                <label class="form-label">تحديد الدور <span class="text-danger">*</span></label>
                <select name="role_id" class="form-select" required>
                    <option value="" selected disabled>-- اختر صلاحية المستخدم --</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 mt-4 border-top pt-4">
                <button type="submit" class="btn btn-modern-primary shadow-sm">
                    <i class="fas fa-save me-2"></i> تسجيل المستخدم
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-modern-secondary shadow-sm ms-2">
                    <i class="fas fa-arrow-right me-2"></i> العودة للقائمة
                </a>
            </div>
        </div>
    </form>
</div>
@endsection