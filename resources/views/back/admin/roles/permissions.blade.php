@extends('back_layouts.master')
@section('css')

@section('title')
    إدارة صلاحيات الدور: {{ $role->name }}
@stop
@endsection
@section('page-header')
<div class="page-header-modern">
    <h4><i class="fas fa-key me-2"></i> إدارة صلاحيات الدور: {{ $role->name }}</h4>
</div>
@endsection
@section('content')
<div class="modern-card">
    <form method="POST" action="{{ route('admin.roles.permissions.update', $role->id) }}">
        @csrf
        @method('PUT')
        
        @foreach($permissions as $group => $groupPermissions)
        <div class="mb-4">
            <h5 class="mb-3" style="color: #7424a9; border-bottom: 2px solid #7424a9; padding-bottom: 10px;">
                <i class="fas fa-folder me-2"></i>{{ $group }}
            </h5>
            <div class="row g-3">
                @foreach($groupPermissions as $permission)
                <div class="col-md-4">
                    <div class="form-check" style="padding: 15px; background: #f8f9fa; border-radius: 8px; border: 2px solid #e9ecef;">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="permissions[]" 
                               value="{{ $permission->id }}" 
                               id="permission_{{ $permission->id }}"
                               {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                        <label class="form-check-label" for="permission_{{ $permission->id }}" style="cursor: pointer;">
                            <strong>{{ $permission->name }}</strong>
                            @if($permission->description)
                            <br><small class="text-muted">{{ $permission->description }}</small>
                            @endif
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-modern btn-modern-primary">
                <i class="fas fa-save me-2"></i> حفظ الصلاحيات
            </button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-modern btn-modern-secondary">
                <i class="fas fa-times me-2"></i> إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
@section('js')

@endsection

