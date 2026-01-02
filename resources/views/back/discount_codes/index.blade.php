@extends('back_layouts.master')

@section('title')
أكواد الخصم
@endsection

@section('content')
<div class="page-header">
    <h2><i class="fas fa-tag me-2"></i> أكواد الخصم</h2>
    <a href="{{ route('admin.discount_codes.create') }}" class="btn btn-modern-primary">
        <i class="fas fa-plus me-2"></i> إضافة كود خصم جديد
    </a>
</div>

<div class="modern-card">
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>الكود</th>
                    <th>الاسم</th>
                    <th>النوع</th>
                    <th>القيمة</th>
                    <th>المستخدم</th>
                    <th>الحالة</th>
                    <th>الصلاحية</th>
                    <th>العمليات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($codes as $code)
                <tr>
                    <td><strong>{{ $code->code }}</strong></td>
                    <td>{{ $code->name ?? '-' }}</td>
                    <td>
                        @if($code->type === 'percentage')
                            <span class="badge bg-info">نسبة مئوية</span>
                        @else
                            <span class="badge bg-success">مبلغ ثابت</span>
                        @endif
                    </td>
                    <td>
                        @if($code->type === 'percentage')
                            {{ $code->value }}%
                        @else
                            {{ number_format($code->value, 2) }} ج.م
                        @endif
                    </td>
                    <td>
                        {{ $code->used_count }} / {{ $code->max_uses ?? '∞' }}
                    </td>
                    <td>
                        @if($code->is_active)
                            <span class="badge bg-success">مفعّل</span>
                        @else
                            <span class="badge bg-danger">معطّل</span>
                        @endif
                    </td>
                    <td>
                        @if($code->expires_at)
                            @if($code->expires_at->isPast())
                                <span class="badge bg-danger">منتهي</span>
                            @else
                                <small>{{ $code->expires_at->format('Y-m-d') }}</small>
                            @endif
                        @else
                            <span class="badge bg-secondary">غير محدد</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.discount_codes.edit', $code->id) }}" class="btn btn-sm btn-modern-info">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.discount_codes.destroy', $code->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-modern-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">
                        <div class="empty-state">
                            <i class="fas fa-tag" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                            <p>لا توجد أكواد خصم</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection




