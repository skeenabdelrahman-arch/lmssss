@extends('front.layouts.app')
@section('title')
صفحة الدفع
@endsection
@section('content')

@if(isset($paymentHtml) && !empty($paymentHtml))
    {{-- Show Kashier iframe payment form --}}
    <section class="feature-section oh pos-rel padding-bottom-2 pb-xl-0" style="margin-top: 150px; min-height: 80vh;">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card shadow-lg" style="border-radius: 20px; border: none;">
                        <div class="card-header text-center" style="background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%); color: white; border-radius: 20px 20px 0 0; padding: 20px;">
                            <h3 style="margin: 0; font-weight: bold;">
                                <i class="fas fa-credit-card me-2"></i> صفحة الدفع الآمنة
                            </h3>
                        </div>
                        <div class="card-body p-5" style="min-height: 500px;">
                            <div id="kashier-payment-container">
                                {!! $paymentHtml !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@else
    {{-- Show redirect page if redirect URL is provided --}}
    <section class="feature-section oh pos-rel padding-bottom-2 pb-xl-0" style="margin-top: 200px; min-height: 60vh;">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card shadow-lg text-center" style="border-radius: 20px; border: none;">
                        <div class="card-body p-5">
                            <div class="mb-4">
                                <i class="fas fa-spinner fa-spin" style="font-size: 80px; color: #1976d2; margin-bottom: 20px;"></i>
                            </div>
                            <h2 style="color: #1976d2; font-weight: bold; margin-bottom: 20px;">
                                جاري التوجيه إلى صفحة الدفع...
                            </h2>
                            <p style="font-size: 18px; color: #666; margin-bottom: 30px;">
                                يرجى الانتظار، سيتم توجيهك تلقائياً إلى صفحة الدفع الآمنة
                            </p>
                            <div class="progress" style="height: 8px; border-radius: 10px; background: #e0e0e0;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     role="progressbar" 
                                     style="width: 100%; background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);"
                                     aria-valuenow="100" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ $paymentUrl ?? '#' }}" class="btn btn-lg" style="background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%); color: white; padding: 15px 40px; border-radius: 50px; font-size: 18px; font-weight: bold; text-decoration: none; box-shadow: 0 4px 15px rgba(25, 118, 210, 0.3);">
                                    <i class="fas fa-external-link-alt me-2"></i> اضغط هنا إذا لم يتم التوجيه تلقائياً
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <script>
        // Immediate redirect to Kashier payment page
        (function() {
            var paymentUrl = @json($paymentUrl ?? '');
            
            // Validate URL
            if (!paymentUrl || paymentUrl === '') {
                console.error('Payment URL is empty');
                document.querySelector('.card-body').innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>خطأ: رابط الدفع فارغ</div>';
                return;
            }
            
            // Log for debugging
            console.log('Redirecting to Kashier:', paymentUrl);
            
            // Immediate redirect without delay
            try {
                window.location.replace(paymentUrl);
            } catch(e) {
                console.error('Redirect error:', e);
                // Fallback: use href if replace fails
                window.location.href = paymentUrl;
            }
        })();
    </script>
@endif
@endsection

