@extends('front.layouts.app')
@section('title')
الحزم التعليمية | {{ site_name() }}
@endsection

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
    }

    .bundles-section {
        padding: 120px 0 80px;
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.03), rgba(250, 137, 107, 0.03));
        min-height: calc(100vh - 90px);
    }

    .page-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 15px;
    }

    .bundles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 30px;
    }

    .bundle-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        border: 2px solid transparent;
    }

    .bundle-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 50px rgba(116, 36, 169, 0.2);
        border-color: var(--primary-color);
    }

    .bundle-image {
        height: 200px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        position: relative;
        overflow: hidden;
    }

    .bundle-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .bundle-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: rgba(76, 175, 80, 0.95);
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .bundle-content {
        padding: 25px;
    }

    .bundle-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .bundle-description {
        color: #666;
        margin-bottom: 20px;
        min-height: 60px;
    }

    .bundle-courses {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .bundle-courses-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
        font-size: 0.95rem;
    }

    .course-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 5px 0;
        font-size: 0.9rem;
        color: #555;
    }

    .bundle-pricing {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding: 15px;
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.05), rgba(250, 137, 107, 0.05));
        border-radius: 10px;
    }

    .original-price {
        color: #999;
        text-decoration: line-through;
        font-size: 0.9rem;
    }

    .bundle-price {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .savings {
        background: #4caf50;
        color: white;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .bundle-btn {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 1.1rem;
        font-weight: 600;
        text-decoration: none;
        display: block;
        text-align: center;
        transition: all 0.3s ease;
    }

    .bundle-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 20px rgba(116, 36, 169, 0.3);
        color: white;
        text-decoration: none;
    }
</style>

<section class="bundles-section">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-gift me-2"></i> الحزم التعليمية</h1>
            <p class="text-muted" style="font-size: 1.1rem;">احصل على عدة كورسات بسعر مميز</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                {{ session('info') }}
            </div>
        @endif

        @if($bundles->count() > 0)
            <div class="bundles-grid">
                @foreach($bundles as $bundle)
                    @php
                        $originalPrice = $bundle->getOriginalBundlePrice();
                        $bundlePrice = $bundle->bundle_price;
                        $savings = $originalPrice - $bundlePrice;
                        $savingsPercentage = $bundle->getSavingsPercentage();
                    @endphp
                    <div class="bundle-card">
                        <div class="bundle-image">
                            @if($bundle->bundle_image)
                                <img src="{{ url('upload_files/' . $bundle->bundle_image) }}" alt="{{ $bundle->name ?? $bundle->code }}">
                            @else
                                <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: white; font-size: 4rem;">
                                    <i class="fas fa-gift"></i>
                                </div>
                            @endif
                            @if($savingsPercentage > 0)
                                <span class="bundle-badge">
                                    <i class="fas fa-tag me-1"></i> وفر {{ $savingsPercentage }}%
                                </span>
                            @endif
                        </div>
                        <div class="bundle-content">
                            <h3 class="bundle-title">{{ $bundle->name ?? $bundle->code }}</h3>
                            @if($bundle->description)
                                <p class="bundle-description">{{ Str::limit($bundle->description, 100) }}</p>
                            @endif
                            
                            <div class="bundle-courses">
                                <div class="bundle-courses-title">
                                    <i class="fas fa-book me-2"></i>
                                    الكورسات في الحزمة ({{ $bundle->months->count() }})
                                </div>
                                @foreach($bundle->months->take(3) as $month)
                                    <div class="course-item">
                                        <i class="fas fa-check-circle text-success"></i>
                                        <span>{{ $month->name }}</span>
                                    </div>
                                @endforeach
                                @if($bundle->months->count() > 3)
                                    <div class="course-item">
                                        <i class="fas fa-plus-circle text-primary"></i>
                                        <span>و {{ $bundle->months->count() - 3 }} كورسات أخرى</span>
                                    </div>
                                @endif
                            </div>

                            <div class="bundle-pricing">
                                <div>
                                    @if($originalPrice > $bundlePrice)
                                        <div class="original-price">{{ number_format($originalPrice, 2) }} ج.م</div>
                                    @endif
                                    <div class="bundle-price">{{ number_format($bundlePrice, 2) }} ج.م</div>
                                </div>
                                @if($savingsPercentage > 0)
                                    <span class="savings">وفر {{ number_format($savings, 2) }} ج.م</span>
                                @endif
                            </div>

                            <a href="{{ route('bundle.details', $bundle->id) }}" class="bundle-btn">
                                <i class="fas fa-shopping-cart me-2"></i>
                                عرض التفاصيل
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-gift" style="font-size: 80px; color: #ccc; margin-bottom: 20px;"></i>
                <h4 class="text-muted">لا توجد حزم متاحة حالياً</h4>
            </div>
        @endif
    </div>
</section>
@endsection

