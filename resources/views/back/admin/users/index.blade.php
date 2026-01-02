@extends('back_layouts.master')

@section('title')
    إدارة المستخدمين
@stop

@section('css')
<style>
    /* تحسين الكارت الرئيسي */
    .modern-card {
        background: #ffffff;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        border: none;
        margin-bottom: 30px;
    }

    /* عنوان الصفحة */
    .page-header-modern h4 {
        font-weight: 700;
        color: #333;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
    }

    /* تنسيق الجدول */
    .modern-table thead th {
        background-color: #f8f9fa;
        color: #6c757d;
        font-weight: 600;
        border-top: none;
        padding: 15px;
        text-transform: uppercase;
        font-size: 0.85rem;
    }

    .modern-table tbody td {
        padding: 15px;
        vertical-align: middle;
        color: #444;
        border-bottom: 1px solid #f1f1f1;
    }

    .user-name {
        font-weight: 700;
        color: #212529;
        display: block;
    }

    .user-email {
        font-size: 0.85rem;
        color: #888;
    }

    /* الأزرار العامة */
    .btn-modern-primary {
        background-color: #7424a9 !important;
        color: #fff !important;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        border: none;
        transition: 0.3s;
    }

    .btn-modern-primary:hover {
        background-color: #5a1a85 !important;
        transform: translateY(-2px);
    }

    /* زر الحذف - أسود يتحول لأحمر ثابت */
    .btn-delete-fixed {
        background-color: #212529 !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 8px;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease !important;
    }

    .btn-delete-fixed:hover {
        background-color: #dc3545 !important; /* أحمر عند الـ Hover */
        color: #ffffff !important;
        transform: scale(1.1);
    }

    /* زر التعديل - سماوي هادئ */
    .btn-edit-modern {
        background-color: #e3f2fd !important;
        color: #0d6efd !important;
        border: none !important;
        border-radius: 8px;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.3s;
    }

    .btn-edit-modern:hover {
        background-color: #0d6efd !important;
        color: #fff !important;
    }

    /* Badge الدور */
    .badge-role {
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .badge-admin { background-color: #f0e6f7; color: #7424a9; }
    .badge-none { background-color: #eee; color: #777; }

</style>
@endsection

@section('page-header')
<div class="page-header-modern">
    <h4><i class="fas fa-users-cog me-2 text-primary"></i> إدارة مستخدمي النظام</h4>
</div>
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="modern-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-0 fw-bold">قائمة المستخدمين</h5>
                <small class="text-muted">يمكنك إدارة صلاحيات وأدوار الموظفين من هنا</small>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-modern-primary shadow-sm">
                <i class="fas fa-user-plus me-2"></i> إضافة مستخدم جديد
            </a>
        </div>

        <div class="table-responsive">
            <table class="table modern-table table-hover">
                <thead>
                    <tr>
                        <th class="text-center" width="60">#</th>
                        <th>المستخدم</th>
                        <th>الدور الصلاحي</th>
                        <th>تاريخ الانضمام</th>
                        <th class="text-center" width="150">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-secondary"></i>
                                </div>
                                <div>
                                    <span class="user-name">{{ $user->name }}</span>
                                    <span class="user-email">{{ $user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($user->role)
                                <span class="badge-role badge-admin">
                                    <i class="fas fa-shield-alt me-1"></i> {{ $user->role->name }}
                                </span>
                            @else
                                <span class="badge-role badge-none">بدون دور</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted small"><i class="far fa-calendar-alt me-1"></i> {{ $user->created_at->format('Y-m-d') }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                {{-- زر التعديل --}}
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-edit-modern" title="تعديل البيانات">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- زر الحذف - أسود يتحول لأحمر --}}
                                @if($user->id != Auth::guard('web')->id())
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم نهائياً؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete-fixed" title="حذف المستخدم">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @else
                                    <span class="badge bg-light text-muted small py-2">حسابك الحالي</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="fas fa-users-slash fa-3x text-light mb-3 d-block"></i>
                            <span class="text-muted">لا يوجد مستخدمين مضافين حالياً</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
@endsection