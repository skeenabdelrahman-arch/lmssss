@extends('back_layouts.master')

@section('title')
أكواد التفعيل
@endsection

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h2><i class="fas fa-key me-2"></i> أكواد التفعيل</h2>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.activation_codes.create') }}" class="btn btn-modern-primary">
                <i class="fas fa-plus me-2"></i> إنشاء أكواد
            </a>
            
            <!-- Dropdown for PDF Export -->
            <div class="btn-group">
                <button type="button" class="btn btn-modern-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-file-pdf me-2"></i> تصدير PDF
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.activation_codes.export_pdf', array_merge(request()->query(), ['size' => 'a4'])) }}" target="_blank">
                            <i class="fas fa-file-pdf me-2"></i> حجم A4 (للطباعة العادية)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.activation_codes.export_pdf', array_merge(request()->query(), ['size' => 'receipt'])) }}" target="_blank">
                            <i class="fas fa-receipt me-2"></i> حجم الكاشير (80mm)
                        </a>
                    </li>
                </ul>
            </div>
            
            <a href="{{ route('admin.activation_codes.export', request()->query()) }}" class="btn btn-modern-success">
                <i class="fas fa-file-excel me-2"></i> تصدير Excel
            </a>
        </div>
    </div>
</div>

@if(session()->has('success'))
    <div class="alert alert-modern alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><strong>{{ session()->get('success') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Filters -->
<div class="modern-card mb-4">
    <form method="GET" action="{{ route('admin.activation_codes.index') }}" class="row g-3">
        <div class="col-md-2">
            <label class="form-label">النوع:</label>
            <select name="type" class="form-select">
                <option value="">الكل</option>
                <option value="course" {{ request('type') == 'course' ? 'selected' : '' }}>كورس</option>
                <option value="bundle" {{ request('type') == 'bundle' ? 'selected' : '' }}>حزمة</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">الكورس:</label>
            <select name="month_id" class="form-select">
                <option value="">جميع الكورسات</option>
                @foreach($months as $month)
                    <option value="{{ $month->id }}" {{ request('month_id') == $month->id ? 'selected' : '' }}>
                        {{ $month->name }} - {{ $month->grade }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">الحالة:</label>
            <select name="status" class="form-select">
                <option value="">الكل</option>
                <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>مستخدم</option>
                <option value="unused" {{ request('status') == 'unused' ? 'selected' : '' }}>غير مستخدم</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">التفعيل:</label>
            <select name="is_active" class="form-select">
                <option value="">الكل</option>
                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>مفعّل</option>
                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>معطّل</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">البحث:</label>
            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="كود، كورس، طالب...">
        </div>
        <div class="col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-modern-primary w-100">
                <i class="fas fa-search me-2"></i> بحث
            </button>
            <a href="{{ route('admin.activation_codes.index') }}" class="btn btn-modern-secondary">
                <i class="fas fa-redo"></i>
            </a>
        </div>
    </form>
</div>

<!-- Statistics Cards -->
@php
    $totalCodes = \App\Models\ActivationCode::count();
    $usedCodes = \App\Models\ActivationCode::whereNotNull('used_at')->count();
    $unusedCodes = \App\Models\ActivationCode::whereNull('used_at')->where('is_active', true)->count();
    $expiredCodes = \App\Models\ActivationCode::where('expires_at', '<', now())->whereNull('used_at')->count();
@endphp

<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-key"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $totalCodes }}</div>
                <div class="stat-label">إجمالي الأكواد</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $usedCodes }}</div>
                <div class="stat-label">مستخدم</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon info">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $unusedCodes }}</div>
                <div class="stat-label">متاح</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">{{ $expiredCodes }}</div>
                <div class="stat-label">منتهي</div>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="modern-card">
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th style="width: 12%;">الكود</th>
                    <th style="width: 8%;">النوع</th>
                    <th style="width: 15%;">الكورس/الحزمة</th>
                    <th style="width: 15%;">الطالب</th>
                    <th style="width: 12%;">تاريخ الاستخدام</th>
                    <th style="width: 12%;">تاريخ الانتهاء</th>
                    <th style="width: 8%;">الحالة</th>
                    <th style="width: 18%;">العمليات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($codes as $code)
                <tr>
                    <td>
                        <strong style="font-family: 'Courier New', monospace; font-size: 16px; color: var(--primary-color);">
                            {{ $code->code }}
                        </strong>
                    </td>
                    <td>
                        @if($code->bundle_id)
                            <span class="badge bg-gradient-purple">
                                <i class="fas fa-gift me-1"></i> حزمة
                            </span>
                        @else
                            <span class="badge bg-gradient-primary">
                                <i class="fas fa-book me-1"></i> كورس
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($code->bundle_id)
                            <div>
                                <strong><i class="fas fa-gift me-1"></i> {{ $code->bundle->name ?? $code->bundle->code }}</strong>
                                @if($code->bundle)
                                    <br><small class="text-muted">{{ number_format($code->bundle->bundle_price, 2) }} ج.م</small>
                                @endif
                            </div>
                        @elseif($code->month)
                            <div>
                                <strong>{{ $code->month->name }}</strong>
                                <br><small class="text-muted">{{ $code->month->grade }}</small>
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($code->student)
                            <div>
                                <strong>{{ $code->student->first_name }} {{ $code->student->second_name }}</strong>
                                <br><small class="text-muted">{{ $code->student->email }}</small>
                            </div>
                        @else
                            <span class="badge bg-secondary">غير مستخدم</span>
                        @endif
                    </td>
                    <td>
                        @if($code->used_at)
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i>
                                {{ $code->used_at->format('Y-m-d') }}
                                <br><small>{{ $code->used_at->format('H:i') }}</small>
                            </span>
                        @else
                            <span class="badge bg-secondary">غير مستخدم</span>
                        @endif
                    </td>
                    <td>
                        @if($code->expires_at)
                            @if($code->expires_at->isPast())
                                <span class="badge bg-danger">
                                    <i class="fas fa-times me-1"></i> منتهي
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    {{ $code->expires_at->format('Y-m-d') }}
                                </span>
                            @endif
                        @else
                            <span class="badge bg-secondary">غير محدد</span>
                        @endif
                    </td>
                    <td>
                        @if($code->is_active)
                            <span class="badge bg-success">مفعّل</span>
                        @else
                            <span class="badge bg-danger">معطّل</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.activation_codes.show', $code->id) }}" class="btn btn-sm btn-modern-info" title="عرض التفاصيل">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.activation_codes.edit', $code->id) }}" class="btn btn-sm btn-modern-warning" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.activation_codes.destroy', $code->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-modern-danger" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-key" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
                            <h4 style="color: #666; margin-bottom: 10px;">لا توجد أكواد تفعيل</h4>
                            <p style="color: #999;">ابدأ بإنشاء أكواد تفعيل جديدة</p>
                            <a href="{{ route('admin.activation_codes.create') }}" class="btn btn-modern-primary mt-3">
                                <i class="fas fa-plus me-2"></i> إنشاء أكواد تفعيل
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($codes->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $codes->links() }}
    </div>
    @endif
</div>
@endsection

