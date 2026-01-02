@extends('back_layouts.master')

@section('title')
تعديل كود التفعيل
@endsection

@section('content')
<div class="page-header">
    <h2><i class="fas fa-edit me-2"></i> تعديل كود التفعيل</h2>
    <a href="{{ route('admin.activation_codes.index') }}" class="btn btn-modern-secondary">
        <i class="fas fa-arrow-right me-2"></i> العودة
    </a>
</div>

<div class="modern-card">
    <form method="POST" action="{{ route('admin.activation_codes.update', $code->id) }}">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">الكورس <span class="text-danger">*</span></label>
                <select class="form-select" name="month_id" required>
                    <option value="">اختر الكورس</option>
                    @foreach($months as $month)
                        <option value="{{ $month->id }}" {{ old('month_id', $code->month_id) == $month->id ? 'selected' : '' }}>
                            {{ $month->name }} - {{ $month->grade }}
                        </option>
                    @endforeach
                </select>
                @error('month_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label">الكود <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="code" value="{{ old('code', $code->code) }}" required style="text-transform: uppercase; font-family: monospace; font-size: 18px; font-weight: bold;">
                <small class="text-muted">سيتم تحويله تلقائياً إلى أحرف كبيرة</small>
                @error('code')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">تاريخ الانتهاء (اختياري)</label>
                <input type="datetime-local" class="form-control" name="expires_at" value="{{ old('expires_at', $code->expires_at ? $code->expires_at->format('Y-m-d\TH:i') : '') }}">
                <small class="text-muted">اتركه فارغاً إذا لم يكن هناك تاريخ انتهاء</small>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">ملاحظات (اختياري)</label>
            <textarea class="form-control" name="notes" rows="3">{{ old('notes', $code->notes) }}</textarea>
        </div>
        
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $code->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                    تفعيل الكود
                </label>
            </div>
        </div>
        
        @if($code->used_at)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            هذا الكود مستخدم بالفعل من قبل: {{ $code->student->first_name ?? 'طالب' }} في {{ $code->used_at->format('Y-m-d H:i') }}
        </div>
        @endif
        
        <div class="text-end">
            <button type="submit" class="btn btn-modern-primary">
                <i class="fas fa-save me-2"></i> حفظ التغييرات
            </button>
        </div>
    </form>
</div>
@endsection



