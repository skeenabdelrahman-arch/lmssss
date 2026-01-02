@extends('back_layouts.master')

@section('title')
إنشاء أكواد تفعيل
@endsection

@section('content')
<div class="page-header">
    <h2><i class="fas fa-plus me-2"></i> إنشاء أكواد تفعيل</h2>
    <a href="{{ route('admin.activation_codes.index') }}" class="btn btn-modern-secondary">
        <i class="fas fa-arrow-right me-2"></i> العودة
    </a>
</div>

<div class="modern-card">
    <form method="POST" action="{{ route('admin.activation_codes.store') }}">
        @csrf
        
        <div class="mb-4">
            <label class="form-label"><strong>نوع الكود</strong></label>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="code_type" id="code_type_course" value="course" checked onchange="toggleCodeType()">
                        <label class="form-check-label" for="code_type_course">
                            <i class="fas fa-book me-1"></i> كود كورس واحد
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="code_type" id="code_type_bundle" value="bundle" onchange="toggleCodeType()">
                        <label class="form-check-label" for="code_type_bundle">
                            <i class="fas fa-gift me-1"></i> كود حزمة
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row" id="course_field">
            <div class="col-md-6 mb-3">
                <label class="form-label">الكورس <span class="text-danger">*</span></label>
                <select class="form-select" name="month_id" id="month_id">
                    <option value="">اختر الكورس</option>
                    @foreach($months as $month)
                        <option value="{{ $month->id }}" {{ old('month_id') == $month->id ? 'selected' : '' }}>
                            {{ $month->name }} - {{ $month->grade }}
                        </option>
                    @endforeach
                </select>
                @error('month_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="row" id="bundle_field" style="display: none;">
            <div class="col-md-6 mb-3">
                <label class="form-label">الحزمة <span class="text-danger">*</span></label>
                <select class="form-select" name="bundle_id" id="bundle_id">
                    <option value="">اختر الحزمة</option>
                    @forelse($bundles as $bundle)
                        <option value="{{ $bundle->id }}" {{ old('bundle_id') == $bundle->id ? 'selected' : '' }}>
                            {{ $bundle->name ?? $bundle->code }} - {{ number_format($bundle->bundle_price, 2) }} ج.م
                        </option>
                    @empty
                        <option value="" disabled>لا توجد حزم متاحة</option>
                    @endforelse
                </select>
                @if($bundles->isEmpty())
                    <div class="alert alert-warning mt-2">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        لا توجد حزم متاحة حالياً. يرجى إنشاء حزمة أولاً من 
                        <a href="{{ route('admin.discount_codes.create') }}" target="_blank">أكواد الخصم والحزم</a>
                    </div>
                @endif
                @error('bundle_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">عدد الأكواد <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="count" value="{{ old('count', 1) }}" min="1" max="100" required>
                <small class="text-muted">يمكنك إنشاء من 1 إلى 100 كود في المرة الواحدة</small>
                @error('count')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">تاريخ الانتهاء (اختياري)</label>
                <input type="datetime-local" class="form-control" name="expires_at" value="{{ old('expires_at') }}">
                <small class="text-muted">اتركه فارغاً إذا لم يكن هناك تاريخ انتهاء</small>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">ملاحظات (اختياري)</label>
            <textarea class="form-control" name="notes" rows="3" placeholder="مثل: أكواد خاصة بالمعرض">{{ old('notes') }}</textarea>
        </div>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            سيتم إنشاء أكواد تفعيل تلقائية عشوائية. يمكنك تصديرها بعد الإنشاء.
        </div>
        
        <div class="text-end">
            <button type="submit" class="btn btn-modern-primary">
                <i class="fas fa-save me-2"></i> إنشاء الأكواد
            </button>
        </div>
    </form>
</div>

<script>
function toggleCodeType() {
    const codeType = document.querySelector('input[name="code_type"]:checked').value;
    const courseField = document.getElementById('course_field');
    const bundleField = document.getElementById('bundle_field');
    const monthSelect = document.getElementById('month_id');
    const bundleSelect = document.getElementById('bundle_id');
    
    if (codeType === 'course') {
        courseField.style.display = 'block';
        bundleField.style.display = 'none';
        monthSelect.required = true;
        bundleSelect.required = false;
        bundleSelect.value = '';
    } else {
        courseField.style.display = 'none';
        bundleField.style.display = 'block';
        monthSelect.required = false;
        bundleSelect.required = true;
        monthSelect.value = '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleCodeType();
});
</script>
@endsection



