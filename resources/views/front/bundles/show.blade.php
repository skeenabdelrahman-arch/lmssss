@extends('front.layouts.app')
@section('title')
{{ $bundle->name ?? $bundle->code }} - الحزمة التعليمية | {{ site_name() }}
@endsection

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
    }

    .bundle-details-section {
        padding: 120px 0 80px;
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.03), rgba(250, 137, 107, 0.03));
        min-height: calc(100vh - 90px);
    }

    .bundle-header {
        background: white;
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        text-align: center;
    }

    .bundle-image-large {
        width: 100%;
        max-width: 600px;
        height: 300px;
        object-fit: cover;
        border-radius: 15px;
        margin-bottom: 20px;
    }

    .bundle-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 15px;
    }

    .bundle-header .description {
        font-size: 1.1rem;
        color: #666;
        margin-bottom: 20px;
    }

    .bundle-info-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    .courses-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .course-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
        border-right: 4px solid var(--primary-color);
    }

    .course-item h5 {
        margin: 0;
        color: #2c3e50;
        font-weight: 600;
    }

    .course-item small {
        color: #666;
    }

    .pricing-card {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border-radius: 20px;
        padding: 40px;
        text-align: center;
    }

    .pricing-card .original-price {
        font-size: 1.2rem;
        text-decoration: line-through;
        opacity: 0.8;
        margin-bottom: 10px;
    }

    .pricing-card .bundle-price {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .savings-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 10px 20px;
        border-radius: 25px;
        display: inline-block;
        margin-top: 10px;
    }

    .purchase-btn {
        width: 100%;
        padding: 20px;
        background: white;
        color: var(--primary-color);
        border: none;
        border-radius: 15px;
        font-size: 1.3rem;
        font-weight: 700;
        text-decoration: none;
        display: block;
        text-align: center;
        margin-top: 20px;
        transition: all 0.3s ease;
    }

    .purchase-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        color: var(--primary-color);
        text-decoration: none;
    }
</style>

<section class="bundle-details-section">
    <div class="container">
        <div class="mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="{{route('bundles.index')}}">الحزم التعليمية</a></li>
                    <li class="breadcrumb-item active">{{ $bundle->name ?? $bundle->code }}</li>
                </ol>
            </nav>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
            </div>
        @endif

        @if(!$validation['valid'])
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ $validation['message'] }}
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="bundle-header">
                    @if($bundle->bundle_image)
                        <img src="{{ url('upload_files/' . $bundle->bundle_image) }}" alt="{{ $bundle->name ?? $bundle->code }}" class="bundle-image-large">
                    @else
                        <div style="display: flex; align-items: center; justify-content: center; height: 300px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 15px; color: white; font-size: 6rem;">
                            <i class="fas fa-gift"></i>
                        </div>
                    @endif
                    <h1>{{ $bundle->name ?? $bundle->code }}</h1>
                    @if($bundle->description)
                        <p class="description">{{ $bundle->description }}</p>
                    @endif
                </div>

                <div class="bundle-info-card">
                    <h4 class="mb-4"><i class="fas fa-book me-2"></i> الكورسات في الحزمة</h4>
                    <div class="courses-list">
                        @foreach($bundle->months as $month)
                            <div class="course-item">
                                <h5>{{ $month->name }}</h5>
                                <small>
                                    <i class="fas fa-graduation-cap me-1"></i>
                                    {{ $month->grade }}
                                </small>
                                <br>
                                <small>
                                    <i class="fas fa-money-bill-wave me-1"></i>
                                    {{ number_format($month->price, 2) }} ج.م
                                </small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="pricing-card">
                    @php
                        $originalPrice = $bundle->getOriginalBundlePrice();
                        $bundlePrice = $bundle->bundle_price;
                        $savings = $originalPrice - $bundlePrice;
                        $savingsPercentage = $bundle->getSavingsPercentage();
                    @endphp
                    
                    <h5 style="margin-bottom: 20px;">سعر الحزمة</h5>
                    
                    @if($originalPrice > $bundlePrice)
                        <div class="original-price">{{ number_format($originalPrice, 2) }} ج.م</div>
                    @endif
                    
                    <div class="bundle-price">{{ number_format($bundlePrice, 2) }} ج.م</div>
                    
                    @if($savingsPercentage > 0)
                        <div class="savings-badge">
                            <i class="fas fa-tag me-1"></i>
                            وفر {{ number_format($savings, 2) }} ج.م ({{ $savingsPercentage }}%)
                        </div>
                    @endif

                    @if($validation['valid'])
                        @if(is_payment_method_enabled('online_payment'))
                            <a href="{{ route('payment.bundle.show', $bundle->id) }}" class="purchase-btn">
                                <i class="fas fa-shopping-cart me-2"></i>
                                اشتري الحزمة الآن
                            </a>
                        @else
                            <div class="alert alert-info" style="background: rgba(255,255,255,0.2); color: white; border: none; margin-top: 20px; text-align: right;">
                                <h6 style="margin-bottom: 15px;"><i class="fas fa-info-circle me-2"></i> طريقة الشراء:</h6>
                                <p style="margin-bottom: 10px; line-height: 1.8;">
                                    <i class="fas fa-store me-2"></i>
                                    ادفع مبلغ <strong>{{ number_format($bundlePrice, 2) }} ج.م</strong> في السنتر واستلم كود التفعيل
                                </p>
                                <p style="margin-bottom: 15px; text-align: center; font-weight: bold;">
                                    أو
                                </p>
                                <p style="margin-bottom: 15px; line-height: 1.8;">
                                    <i class="fas fa-mobile-alt me-2"></i>
                                    حول علي فودافون كاش او انستا باي رقم <strong>01094144305</strong> وارسل الصورة علي تيلجرام الدعم الفني واستلم كود التفعيل
                                </p>
                                <hr style="border-color: rgba(255,255,255,0.3); margin: 15px 0;">
                                <a href="{{ route('bundle.activate.form', $bundle->id) }}" class="purchase-btn">
                                    <i class="fas fa-key me-2"></i>
                                    تفعيل كود الحزمة
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning" style="background: rgba(255,255,255,0.2); color: white; border: none; margin-top: 20px;">
                            {{ $validation['message'] }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

