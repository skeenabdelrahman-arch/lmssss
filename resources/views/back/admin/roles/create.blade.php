@extends('back_layouts.master')
@section('css')

@section('title')
    إضافة دور جديد
@stop
@endsection
@section('page-header')
<div class="page-header-modern">
    <h4><i class="fas fa-plus me-2"></i> إضافة دور جديد</h4>
</div>
@endsection
@section('content')
<div class="modern-card">
    <form method="POST" action="{{ route('admin.roles.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">اسم الدور <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required placeholder="مثال: مدير، إداري، محرر">
            </div>
            <div class="col-md-12">
                <label class="form-label">الوصف</label>
                <textarea name="description" class="form-control" rows="3" placeholder="وصف مختصر للدور"></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-modern btn-modern-primary">
                    <i class="fas fa-save me-2"></i> حفظ
                </button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-modern btn-modern-secondary">
                    <i class="fas fa-times me-2"></i> إلغاء
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
@section('js')

@endsection

