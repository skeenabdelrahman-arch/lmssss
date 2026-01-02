@extends('back_layouts.master')

@section('title')
تفاصيل كود التفعيل
@endsection

@section('content')
<div class="page-header">
    <h2><i class="fas fa-key me-2"></i> تفاصيل كود التفعيل</h2>
    <a href="{{ route('admin.activation_codes.index') }}" class="btn btn-modern-secondary">
        <i class="fas fa-arrow-right me-2"></i> العودة
    </a>
</div>

<div class="modern-card">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label"><strong>الكود:</strong></label>
            <div style="font-family: monospace; font-size: 24px; font-weight: bold; color: var(--primary-color); padding: 15px; background: #f5f5f5; border-radius: 10px; text-align: center;">
                {{ $code->code }}
            </div>
        </div>
        
        <div class="col-md-6 mb-3">
            <label class="form-label"><strong>الكورس:</strong></label>
            <div style="padding: 15px; background: #f5f5f5; border-radius: 10px;">
                {{ $code->month->name ?? '-' }} - {{ $code->month->grade ?? '-' }}
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label"><strong>الطالب:</strong></label>
            <div style="padding: 15px; background: #f5f5f5; border-radius: 10px;">
                @if($code->student)
                    {{ $code->student->first_name }} {{ $code->student->second_name }} {{ $code->student->third_name }} {{ $code->student->forth_name }}
                    <br>
                    <small class="text-muted">البريد: {{ $code->student->email }}</small>
                @else
                    <span class="text-muted">غير مستخدم</span>
                @endif
            </div>
        </div>
        
        <div class="col-md-6 mb-3">
            <label class="form-label"><strong>تاريخ الاستخدام:</strong></label>
            <div style="padding: 15px; background: #f5f5f5; border-radius: 10px;">
                @if($code->used_at)
                    <span class="badge bg-success">{{ $code->used_at->format('Y-m-d H:i:s') }}</span>
                @else
                    <span class="badge bg-secondary">غير مستخدم</span>
                @endif
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label"><strong>تاريخ الانتهاء:</strong></label>
            <div style="padding: 15px; background: #f5f5f5; border-radius: 10px;">
                @if($code->expires_at)
                    @if($code->expires_at->isPast())
                        <span class="badge bg-danger">منتهي - {{ $code->expires_at->format('Y-m-d') }}</span>
                    @else
                        <span class="badge bg-warning">{{ $code->expires_at->format('Y-m-d H:i') }}</span>
                    @endif
                @else
                    <span class="badge bg-secondary">غير محدد</span>
                @endif
            </div>
        </div>
        
        <div class="col-md-6 mb-3">
            <label class="form-label"><strong>الحالة:</strong></label>
            <div style="padding: 15px; background: #f5f5f5; border-radius: 10px;">
                @if($code->is_active)
                    <span class="badge bg-success">مفعّل</span>
                @else
                    <span class="badge bg-danger">معطّل</span>
                @endif
            </div>
        </div>
    </div>
    
    @if($code->notes)
    <div class="mb-3">
        <label class="form-label"><strong>ملاحظات:</strong></label>
        <div style="padding: 15px; background: #f5f5f5; border-radius: 10px;">
            {{ $code->notes }}
        </div>
    </div>
    @endif
    
    <div class="text-end mt-4">
        <a href="{{ route('admin.activation_codes.edit', $code->id) }}" class="btn btn-modern-warning">
            <i class="fas fa-edit me-2"></i> تعديل
        </a>
    </div>
</div>
@endsection



