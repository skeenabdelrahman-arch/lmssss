@extends('back_layouts.master')
@section('css')

@section('title')
    إدارة الأدوار
@stop
@endsection
@section('page-header')
<div class="page-header-modern">
    <h4><i class="fas fa-user-shield me-2"></i> إدارة الأدوار</h4>
</div>
@endsection
@section('content')
<div class="modern-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">قائمة الأدوار</h5>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-modern btn-modern-primary">
            <i class="fas fa-plus me-2"></i> إضافة دور جديد
        </a>
    </div>

    <div class="modern-table">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>اسم الدور</th>
                    <th>الوصف</th>
                    <th>عدد الصلاحيات</th>
                    <th>عدد المستخدمين</th>
                    <th>تاريخ الإنشاء</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><strong>{{ $role->name }}</strong></td>
                    <td>{{ $role->description ?? '-' }}</td>
                    <td>
                        <span class="badge-modern badge-modern-primary">{{ $role->permissions()->count() }}</span>
                    </td>
                    <td>
                        <span class="badge-modern badge-modern-info">{{ $role->users_count }}</span>
                    </td>
                    <td>{{ $role->created_at->format('Y-m-d') }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.roles.permissions', $role->id) }}" class="btn btn-sm btn-modern btn-modern-warning" title="إدارة الصلاحيات">
                                <i class="fas fa-key"></i>
                            </a>
                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-modern btn-modern-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($role->users_count == 0)
                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-modern btn-modern-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">لا يوجد أدوار</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
@section('js')

@endsection

