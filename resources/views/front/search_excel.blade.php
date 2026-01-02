@extends('front.layouts.app')
@section('title')
كود السنتر | {{ site_name() }}
@endsection

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
        --primary-light: #b05ee7;
    }

    .search-section {
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

    .page-header p {
        color: #6c757d;
        font-size: 1.1rem;
    }

    .search-card {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(116, 36, 169, 0.1);
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 10px;
        display: block;
    }

    .form-control {
        padding: 15px 20px;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        font-size: 16px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(116, 36, 169, 0.1);
        outline: none;
    }

    .btn-submit {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 10px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(116, 36, 169, 0.3);
    }

    .result-card {
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.05), rgba(250, 137, 107, 0.05));
        border-radius: 15px;
        padding: 30px;
        margin-top: 30px;
        border: 2px solid var(--primary-color);
    }

    .result-card h3 {
        color: var(--primary-color);
        font-weight: 700;
        margin-bottom: 20px;
        font-size: 1.5rem;
    }

    .result-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background: white;
        border-radius: 10px;
        margin-bottom: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .result-item:last-child {
        margin-bottom: 0;
    }

    .result-label {
        font-weight: 600;
        color: var(--primary-color);
    }

    .result-value {
        color: #333;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .alert {
        border-radius: 12px;
        padding: 15px 20px;
        margin-bottom: 25px;
        border: none;
    }

    .alert-danger {
        background: #fee;
        color: #c33;
        border-right: 4px solid #c33;
    }

    .info-box {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
        border-right: 4px solid var(--primary-color);
    }

    .info-box i {
        color: var(--primary-color);
        margin-left: 10px;
    }

    @media (max-width: 768px) {
        .search-card {
            padding: 30px 20px;
        }

        .page-header h1 {
            font-size: 2rem;
        }
    }
</style>

<section class="search-section">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-search me-2"></i>معرفة كود السنتر للطالب</h1>
            <p>ابحث عن كود السنتر باستخدام رقم الهاتف المسجل</p>
        </div>

        <div class="search-card">
            <form action="{{ route('students.find.excel') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">رقم الهاتف</label>
                    <input 
                        type="text" 
                        name="phone" 
                        class="form-control" 
                        placeholder="أدخل رقم الهاتف المسجل بالسنتر"
                        required
                        autofocus
                    >
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-search me-2"></i>بحث
                </button>
            </form>

            @if(isset($foundStudent))
                <div class="result-card">
                    <h3><i class="fas fa-user-check me-2"></i>بيانات الطالب</h3>
                    <div class="result-item">
                        <span class="result-label">الاسم:</span>
                        <span class="result-value">{{ $foundStudent['name'] }}</span>
                    </div>
                    <div class="result-item">
                        <span class="result-label">الكود:</span>
                        <span class="result-value" style="color: var(--primary-color); font-size: 1.3rem;">{{ $foundStudent['code'] }}</span>
                    </div>
                    <div class="result-item">
                        <span class="result-label">رقم الهاتف:</span>
                        <span class="result-value">{{ $foundStudent['phone'] }}</span>
                    </div>
                </div>
            @elseif(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>{{ session('error') }}</strong>
                </div>
            @endif

            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                <strong>ملاحظة:</strong> أدخل رقم الهاتف المسجل في السنتر للحصول على كود الطالب.
            </div>
        </div>
    </div>
</section>

@endsection
