@extends('back_layouts.master')

@section('title')
إضافة كود خصم جديد
@endsection

@section('content')
<div class="page-header">
    <h2><i class="fas fa-plus me-2"></i> إضافة كود خصم جديد</h2>
    <a href="{{ route('admin.discount_codes.index') }}" class="btn btn-modern-secondary">
        <i class="fas fa-arrow-right me-2"></i> العودة
    </a>
</div>

<div class="modern-card">
    <form method="POST" action="{{ route('admin.discount_codes.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">الكود <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="code" value="{{ old('code') }}" required style="text-transform: uppercase;">
                <small class="text-muted">سيتم تحويله تلقائياً إلى أحرف كبيرة</small>
                @error('code')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label">الاسم (اختياري)</label>
                <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="مثل: خصم الصيف">
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">نوع الخصم <span class="text-danger">*</span></label>
                <select class="form-select" name="type" required>
                    <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>نسبة مئوية (%)</option>
                    <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>مبلغ ثابت (ج.م)</option>
                </select>
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label">قيمة الخصم <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="value" value="{{ old('value') }}" step="0.01" min="0" required>
                <small class="text-muted" id="valueHint">إذا كان النوع نسبة مئوية، أدخل الرقم فقط (مثل: 20 لخصم 20%)</small>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">الحد الأدنى للطلب (ج.م)</label>
                <input type="number" class="form-control" name="min_amount" value="{{ old('min_amount') }}" step="0.01" min="0">
                <small class="text-muted">اتركه فارغاً إذا لم يكن هناك حد أدنى</small>
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label">عدد الاستخدامات الأقصى</label>
                <input type="number" class="form-control" name="max_uses" value="{{ old('max_uses') }}" min="1">
                <small class="text-muted">اتركه فارغاً للاستخدام غير المحدود</small>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">تاريخ البدء</label>
                <input type="datetime-local" class="form-control" name="starts_at" value="{{ old('starts_at') }}">
            </div>
            
            <div class="col-md-6 mb-3">
                <label class="form-label">تاريخ الانتهاء</label>
                <input type="datetime-local" class="form-control" name="expires_at" value="{{ old('expires_at') }}">
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">الوصف</label>
            <textarea class="form-control" name="description" rows="3">{{ old('description') }}</textarea>
        </div>
        
        <!-- خيار الحزمة -->
        <div class="mb-4">
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="is_bundle" id="is_bundle" value="1" {{ old('is_bundle') ? 'checked' : '' }} onchange="toggleBundleFields()">
                <label class="form-check-label" for="is_bundle">
                    <strong>حزمة خصم</strong> (يظهر كمنتج منفصل للطلاب للشراء)
                </label>
            </div>
            
            <div id="bundleFields" style="display: {{ old('is_bundle') ? 'block' : 'none' }};">
                <div class="card-modern p-3 mb-3" style="background: #f8f9fa;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">سعر الحزمة (ج.م) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="bundle_price" value="{{ old('bundle_price') }}" step="0.01" min="0">
                            <small class="text-muted">السعر النهائي للحزمة التي سيدفعها الطالب</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">صورة الحزمة</label>
                            <input type="file" class="form-control" name="bundle_image" accept="image/*">
                            <small class="text-muted">صورة عرض الحزمة للطلاب</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- اختيار الكورسات -->
        <div class="mb-4">
            <h5 class="mb-3"><i class="fas fa-book me-2"></i> الكورسات في الحزمة <span class="text-danger">*</span></h5>
            <p class="text-muted mb-3">
                <small>
                    <i class="fas fa-info-circle me-1"></i>
                    <span id="bundleInfo">اختر الكورسات التي ستكون في الحزمة. عند شراء الحزمة، سيتم تفعيل جميع هذه الكورسات.</span>
                </small>
            </p>
            
            <div class="card-modern" style="max-height: 400px; overflow-y: auto;">
                <div class="mb-3">
                    <input type="text" id="courseSearch" class="form-control" placeholder="بحث في الكورسات...">
                </div>
                <div class="row g-2">
                    @foreach($months ?? [] as $month)
                        <div class="col-md-6 course-item" data-name="{{ strtolower($month->name) }}">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="months[]" value="{{ $month->id }}" id="month_{{ $month->id }}" {{ in_array($month->id, old('months', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="month_{{ $month->id }}">
                                    <strong>{{ $month->name }}</strong>
                                    <small class="text-muted d-block">الصف: {{ $month->grade }} | السعر: {{ number_format($month->price, 2) }} ج.م</small>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if(empty($months) || count($months) == 0)
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        لا توجد كورسات متاحة حالياً
                    </div>
                @endif
            </div>
        </div>
        
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                    تفعيل الكود
                </label>
            </div>
        </div>
        
        <div class="text-end">
            <button type="submit" class="btn btn-modern-primary">
                <i class="fas fa-save me-2"></i> حفظ
            </button>
        </div>
    </form>
</div>

<script>
    document.querySelector('select[name="type"]').addEventListener('change', function() {
        const hint = document.getElementById('valueHint');
        if (this.value === 'percentage') {
            hint.textContent = 'إذا كان النوع نسبة مئوية، أدخل الرقم فقط (مثل: 20 لخصم 20%)';
        } else {
            hint.textContent = 'أدخل المبلغ بالجنيه المصري (مثل: 50 لخصم 50 ج.م)';
        }
    });

    function toggleBundleFields() {
        const isBundle = document.getElementById('is_bundle').checked;
        const bundleFields = document.getElementById('bundleFields');
        const bundlePriceInput = bundleFields.querySelector('input[name="bundle_price"]');
        const bundleInfo = document.getElementById('bundleInfo');
        
        if (isBundle) {
            bundleFields.style.display = 'block';
            bundlePriceInput.required = true;
            bundleInfo.textContent = 'اختر الكورسات التي ستكون في الحزمة. عند شراء الحزمة، سيتم تفعيل جميع هذه الكورسات.';
        } else {
            bundleFields.style.display = 'none';
            bundlePriceInput.required = false;
            bundleInfo.textContent = 'اختر الكورسات المرتبطة بالكود. عند استخدام الكود في الدفع، سيتم تفعيل جميع الكورسات المحددة تلقائياً للطالب.';
        }
    }

    // البحث في الكورسات
    document.getElementById('courseSearch')?.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const courseItems = document.querySelectorAll('.course-item');
        
        courseItems.forEach(item => {
            const courseName = item.getAttribute('data-name');
            if (courseName.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
@endsection




