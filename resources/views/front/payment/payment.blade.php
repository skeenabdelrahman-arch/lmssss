@extends('front.layouts.app')
@section('title')
دفع الكورس - {{ $month->name }}
@endsection
@section('content')
<section class="feature-section oh pos-rel padding-bottom-2 pb-xl-0" style="margin-top: 200px;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg" style="border-radius: 20px; border: none;">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-credit-card" style="font-size: 60px; color: #1976d2; margin-bottom: 20px;"></i>
                            <h2 style="color: #1976d2; font-weight: bold;">دفع الكورس</h2>
                            <p class="text-muted">
                                <i class="fas fa-shield-alt me-2"></i>
                                الدفع آمن ومشفر عبر بوابة Kashier
                            </p>
                        </div>

                        <div class="payment-details mb-4" style="background: #f5f5f5; padding: 25px; border-radius: 15px;">
                            <h4 style="color: #424242; margin-bottom: 20px;">
                                <i class="fas fa-book me-2"></i> تفاصيل الكورس
                            </h4>
                            <div class="row">
                                <div class="col-6">
                                    <strong>اسم الكورس:</strong>
                                </div>
                                <div class="col-6 text-end">
                                    {{ $month->name }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <strong>الصف:</strong>
                                </div>
                                <div class="col-6 text-end">
                                    {{ $month->grade }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <strong>المبلغ الأصلي:</strong>
                                </div>
                                <div class="col-6 text-end">
                                    <span style="font-size: 24px; color: #666; text-decoration: line-through;">
                                        {{ number_format($month->price, 2) }} ج.م
                                    </span>
                                </div>
                            </div>
                            <hr>
                            <div class="row" id="discountRow" style="display: none;">
                                <div class="col-6">
                                    <strong style="color: #4caf50;">الخصم:</strong>
                                </div>
                                <div class="col-6 text-end">
                                    <span style="font-size: 20px; color: #4caf50; font-weight: bold;" id="discountAmount">
                                        0.00 ج.م
                                    </span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <strong>المبلغ النهائي:</strong>
                                </div>
                                <div class="col-6 text-end">
                                    <span style="font-size: 28px; color: #1976d2; font-weight: bold;" id="finalAmount">
                                        {{ number_format($month->price, 2) }} ج.م
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if($pendingPayment)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            لديك عملية دفع معلقة. يرجى إكمال الدفع أو إلغاؤها أولاً.
                        </div>
                        @endif

                        <form method="POST" action="{{ route('payment.initiate', $month->id) }}" id="paymentForm">
                            @csrf
                            <input type="hidden" name="amount" id="amountInput" value="{{ $month->price }}">
                            <input type="hidden" name="discount_code" id="discountCodeInput" value="">
                            
                            {{-- Discount Code Section --}}
                            <div class="mb-4" style="background: #f8f9fa; padding: 20px; border-radius: 15px;">
                                <h5 style="color: #424242; margin-bottom: 15px;">
                                    <i class="fas fa-tag me-2"></i> كود الخصم
                                </h5>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="discountCode" placeholder="أدخل كود الخصم" style="border-radius: 10px 0 0 10px;">
                                    <button type="button" class="btn btn-success" id="applyDiscountBtn" style="border-radius: 0 10px 10px 0;">
                                        <i class="fas fa-check me-2"></i> تطبيق
                                    </button>
                                </div>
                                <div id="discountMessage" class="mt-2" style="display: none;"></div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-lg" id="payButton" style="background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%); color: white; padding: 15px 50px; border-radius: 50px; font-size: 20px; font-weight: bold; box-shadow: 0 4px 15px rgba(25, 118, 210, 0.3);">
                                    <i class="fas fa-lock me-2"></i> <span id="payButtonText">ادفع الآن عبر Kashier</span>
                                </button>
                            </div>
                        </form>

                        <script>
                            document.getElementById('paymentForm').addEventListener('submit', function(e) {
                                const button = document.getElementById('payButton');
                                const buttonText = document.getElementById('payButtonText');
                                
                                button.disabled = true;
                                buttonText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري التوجيه...';
                                
                                // Allow form to submit normally
                            });
                        </script>

                        <div class="text-center mt-4">
                            <a href="{{ route('courses.index') }}" style="color: #666; text-decoration: none;">
                                <i class="fas fa-arrow-right me-2"></i> العودة للكورسات
                            </a>
                        </div>

                        <div class="mt-4" style="background: #e3f2fd; padding: 15px; border-radius: 10px; text-align: center;">
                            <small style="color: #1976d2;">
                                <i class="fas fa-shield-alt me-2"></i>
                                الدفع آمن ومشفر عبر بوابة Kashier
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.getElementById('applyDiscountBtn').addEventListener('click', function() {
        const code = document.getElementById('discountCode').value.trim();
        const originalAmount = {{ $month->price }};
        const messageDiv = document.getElementById('discountMessage');
        const discountRow = document.getElementById('discountRow');
        const discountAmountSpan = document.getElementById('discountAmount');
        const finalAmountSpan = document.getElementById('finalAmount');
        const amountInput = document.getElementById('amountInput');
        const discountCodeInput = document.getElementById('discountCodeInput');
        
        if (!code) {
            messageDiv.innerHTML = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i> يرجى إدخال كود الخصم</div>';
            messageDiv.style.display = 'block';
            return;
        }
        
        fetch('{{ route("discount.validate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                code: code,
                amount: originalAmount
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.valid) {
                messageDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i> ' + data.message + '</div>';
                messageDiv.style.display = 'block';
                
                discountRow.style.display = 'flex';
                discountAmountSpan.textContent = data.discount.toFixed(2) + ' ج.م';
                finalAmountSpan.textContent = data.final_amount.toFixed(2) + ' ج.م';
                amountInput.value = data.final_amount;
                discountCodeInput.value = code;
            } else {
                messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i> ' + data.message + '</div>';
                messageDiv.style.display = 'block';
                
                discountRow.style.display = 'none';
                discountAmountSpan.textContent = '0.00 ج.م';
                finalAmountSpan.textContent = originalAmount.toFixed(2) + ' ج.م';
                amountInput.value = originalAmount;
                discountCodeInput.value = '';
            }
        })
        .catch(error => {
            messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i> حدث خطأ أثناء التحقق من كود الخصم</div>';
            messageDiv.style.display = 'block';
        });
    });
</script>
@endsection

