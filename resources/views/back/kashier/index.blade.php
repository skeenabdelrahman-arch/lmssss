@extends('back_layouts.master')

@section('title')
إعدادات Kashier
@endsection

@section('content')
<div class="page-header">
    <h2><i class="fas fa-credit-card me-2"></i> إعدادات Kashier</h2>
</div>

@if(!$config['is_configured'])
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>تحذير:</strong> Kashier غير مضبوط. يرجى إضافة الإعدادات في ملف `.env`
</div>
@else
<div class="alert alert-success">
    <i class="fas fa-check-circle me-2"></i>
    <strong>تم الإعداد بنجاح!</strong> Kashier جاهز للاستخدام
</div>
@endif

<div class="modern-card mb-4">
    <h5 class="mb-4"><i class="fas fa-cog me-2"></i> الإعدادات الحالية</h5>
    
    <div class="row mb-3">
        <div class="col-md-4"><strong>API Key:</strong></div>
        <div class="col-md-8">
            <code>{{ $config['api_key'] }}</code>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4"><strong>Secret Key:</strong></div>
        <div class="col-md-8">
            <code>{{ $config['secret_key'] }}</code>
            <small class="text-muted d-block mt-1">(يُستخدم لتوقيع الـ hash)</small>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4"><strong>Merchant ID:</strong></div>
        <div class="col-md-8">
            <code>{{ $config['merchant_id'] }}</code>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4"><strong>الوضع:</strong></div>
        <div class="col-md-8">
            @if($config['mode'] === 'test')
                <span class="badge bg-warning">اختبار (Test)</span>
            @else
                <span class="badge bg-success">إنتاج (Live)</span>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-4"><strong>الحالة:</strong></div>
        <div class="col-md-8">
            @if($config['is_configured'])
                <span class="badge bg-success">
                    <i class="fas fa-check"></i> مضبوط
                </span>
            @else
                <span class="badge bg-danger">
                    <i class="fas fa-times"></i> غير مضبوط
                </span>
            @endif
        </div>
    </div>
</div>

<div class="modern-card mb-4">
    <h5 class="mb-4"><i class="fas fa-plug me-2"></i> اختبار الاتصال</h5>
    <p class="mb-3">اضغط على الزر أدناه للتحقق من أن الإعدادات صحيحة:</p>
    <button type="button" class="btn btn-modern-primary" id="testConnectionBtn">
        <i class="fas fa-network-wired me-2"></i> اختبار الاتصال
    </button>
    <div id="testResult" class="mt-3" style="display: none;"></div>
</div>

<div class="modern-card">
    <h5 class="mb-4"><i class="fas fa-info-circle me-2"></i> كيفية الإعداد</h5>
    
    <div class="alert alert-info">
        <h6><i class="fas fa-list-ol me-2"></i> الخطوات:</h6>
        <ol>
            <li>افتح ملف <code>.env</code> في جذر المشروع</li>
            <li>أضف المتغيرات التالية:
                <pre class="mt-2" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">KASHIER_API_KEY=your_api_key_here
KASHIER_SECRET_KEY=your_secret_key_here
KASHIER_MERCHANT_ID=your_merchant_id_here
KASHIER_MODE=test</pre>
                <small class="text-muted d-block mt-2">
                    <strong>ملاحظة:</strong> 
                    <ul class="mb-0 mt-2">
                        <li><code>KASHIER_SECRET_KEY</code> مطلوب (يُستخدم لتوقيع الـ hash)</li>
                        <li><code>KASHIER_API_KEY</code> اختياري (قد يُستخدم في بعض العمليات)</li>
                        <li><code>KASHIER_MERCHANT_ID</code> مطلوب (بصيغة MID-XXX-XXX)</li>
                    </ul>
                </small>
            </li>
            <li>احصل على البيانات من <a href="https://www.kashier.io/ar/" target="_blank">Kashier Dashboard</a></li>
            <li>امسح Cache بعد التعديل:
                <code class="d-block mt-2">php artisan config:clear</code>
            </li>
        </ol>
    </div>

    <div class="alert alert-warning">
        <h6><i class="fas fa-exclamation-triangle me-2"></i> ملاحظات مهمة:</h6>
        <ul class="mb-0">
            <li>لا تشارك API Key أو Secret Key مع أي شخص</li>
            <li>Secret Key مطلوب لتوقيع الـ hash في عمليات الدفع</li>
            <li>استخدم <code>KASHIER_MODE=test</code> أثناء التطوير</li>
            <li>غير إلى <code>KASHIER_MODE=live</code> في الإنتاج</li>
            <li>تأكد من إعداد Webhook URL في Kashier Dashboard</li>
        </ul>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.payments.index') }}" class="btn btn-modern-secondary">
            <i class="fas fa-arrow-right me-2"></i> العودة للمدفوعات
        </a>
        <a href="https://docs.kashier.io/" target="_blank" class="btn btn-modern-info">
            <i class="fas fa-book me-2"></i> وثائق Kashier
        </a>
    </div>
</div>

<script>
    document.getElementById('testConnectionBtn').addEventListener('click', function() {
        const btn = this;
        const resultDiv = document.getElementById('testResult');
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري الاختبار...';
        resultDiv.style.display = 'none';
        
        fetch('{{ route("admin.kashier.test.connection") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-network-wired me-2"></i> اختبار الاتصال';
            
            resultDiv.style.display = 'block';
            if (data.success) {
                resultDiv.className = 'alert alert-success mt-3';
                resultDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i> ' + data.message;
            } else {
                resultDiv.className = 'alert alert-danger mt-3';
                resultDiv.innerHTML = '<i class="fas fa-times-circle me-2"></i> ' + data.message;
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-network-wired me-2"></i> اختبار الاتصال';
            
            resultDiv.style.display = 'block';
            resultDiv.className = 'alert alert-danger mt-3';
            resultDiv.innerHTML = '<i class="fas fa-times-circle me-2"></i> حدث خطأ أثناء الاختبار';
        });
    });
</script>
@endsection




