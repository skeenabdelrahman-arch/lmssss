@extends('front.layouts.app')
@section('title')
تفعيل حزمة - {{ $bundle->name ?? $bundle->code }} | {{ site_name() }}
@endsection

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
    }

    .activate-section {
        padding: 120px 0 80px;
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.03), rgba(250, 137, 107, 0.03));
        min-height: calc(100vh - 90px);
    }

    .activate-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        max-width: 600px;
        margin: 0 auto;
    }

    .activate-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .activate-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 50px;
        margin: 0 auto 20px;
    }

    .activate-header h2 {
        color: var(--primary-color);
        font-weight: 700;
        margin-bottom: 10px;
    }

    .bundle-info {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 15px;
        margin-bottom: 30px;
        text-align: center;
    }

    .bundle-info h4 {
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .bundle-info .price {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .code-input-group {
        margin-bottom: 20px;
    }

    .code-input-group label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
        display: block;
    }

    .code-input-group input {
        width: 100%;
        padding: 15px;
        border: 2px solid #ddd;
        border-radius: 10px;
        font-size: 1.1rem;
        text-align: center;
        font-weight: 600;
        letter-spacing: 2px;
        text-transform: uppercase;
        transition: all 0.3s ease;
    }

    .code-input-group input:focus {
        border-color: var(--primary-color);
        outline: none;
        box-shadow: 0 0 0 3px rgba(116, 36, 169, 0.1);
    }

    .activate-btn {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 1.2rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .activate-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(116, 36, 169, 0.3);
    }

    .instructions {
        background: #e7f3ff;
        padding: 20px;
        border-radius: 10px;
        border-right: 4px solid #2196F3;
        margin-bottom: 20px;
    }

    .instructions h5 {
        color: #1976D2;
        margin-bottom: 10px;
    }

    .instructions ul {
        margin: 0;
        padding-right: 20px;
    }

    .instructions li {
        margin-bottom: 8px;
        color: #555;
    }

    .courses-included {
        margin-top: 30px;
        padding-top: 30px;
        border-top: 2px dashed #ddd;
    }

    .courses-included h5 {
        color: var(--primary-color);
        margin-bottom: 15px;
        text-align: center;
    }

    .course-chip {
        display: inline-block;
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.1), rgba(250, 137, 107, 0.1));
        padding: 8px 15px;
        border-radius: 20px;
        margin: 5px;
        color: var(--primary-color);
        font-weight: 600;
    }
</style>

<section class="activate-section">
    <div class="container">
        <div class="activate-card">
            <div class="activate-header">
                <div class="activate-icon">
                    <i class="fas fa-gift"></i>
                </div>
                <h2>تفعيل حزمة تعليمية</h2>
                <p class="text-muted">أدخل كود التفعيل الخاص بك</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bundle-info">
                <h4>{{ $bundle->name ?? $bundle->code }}</h4>
                @if($bundle->description)
                    <p class="text-muted">{{ $bundle->description }}</p>
                @endif
                <div class="price">{{ number_format($bundle->bundle_price, 2) }} ج.م</div>
            </div>

            <div class="instructions">
                <h5><i class="fas fa-info-circle me-2"></i> تعليمات التفعيل</h5>
                <ul>
                    <li>ادفع مبلغ الحزمة في السنتر واستلم كود التفعيل</li>
                                    <li>حول علي فودافون كاش او انستا باي رقم <strong>01094144305</strong> وارسل الصورة علي تيلجرام الدعم الفني واستلم كود التفعيل
                    <li>أدخل الكود في الحقل أدناه</li>
                    <li>سيتم تفعيل جميع الكورسات في الحزمة تلقائياً</li>
                </ul>
            </div>

            <form action="{{ route('bundle.activate', $bundle->id) }}" method="POST">
                @csrf
                <div class="code-input-group">
                    <label for="activation_code">
                        <i class="fas fa-key me-2"></i>
                        كود التفعيل
                    </label>
                    <input 
                        type="text" 
                        name="activation_code" 
                        id="activation_code" 
                        placeholder="أدخل كود التفعيل" 
                        value="{{ old('activation_code') }}"
                        required
                        maxlength="50"
                    >
                </div>

                <button type="submit" class="activate-btn">
                    <i class="fas fa-check-circle me-2"></i>
                    تفعيل الحزمة
                </button>
            </form>

            <div class="courses-included">
                <h5><i class="fas fa-book me-2"></i> الكورسات المتضمنة في الحزمة</h5>
                <div class="text-center">
                    @foreach($bundle->months as $month)
                        <span class="course-chip">
                            <i class="fas fa-check me-1"></i>
                            {{ $month->name }}
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('bundle.details', $bundle->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right me-2"></i>
                    رجوع لتفاصيل الحزمة
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
