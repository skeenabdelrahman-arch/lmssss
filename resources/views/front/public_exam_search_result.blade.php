<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>البحث عن النتيجة | منصة مستر سامح صلاح</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e0f2f7 0%, #c8e6c9 100%);
            direction: rtl;
            text-align: right;
            min-height: 100vh;
        }

        /* Header خاص بالصفحة */
        .exam-header-custom {
            background: linear-gradient(135deg, #7424a9 0%, #9d4edd 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .exam-header-custom .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .exam-header-custom .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .exam-header-custom a {
            color: white;
            text-decoration: none;
            transition: opacity 0.3s;
        }

        .exam-header-custom a:hover {
            opacity: 0.8;
        }

        /* محتوى الصفحة */
        .search-section {
            min-height: calc(100vh - 200px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px 20px;
        }

        .search-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
            max-width: 600px;
            width: 100%;
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .search-card h2 {
            color: #7424a9;
            margin-bottom: 10px;
            font-weight: 700;
            font-size: 28px;
        }

        .search-card p {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }

        .search-card .form-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 10px;
            text-align: right;
        }

        .search-card .form-control {
            border-radius: 10px;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 3px;
            font-family: 'Courier New', monospace;
            text-transform: uppercase;
        }

        .code-hint {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            color: #856404;
            font-size: 14px;
        }

        .code-hint i {
            color: #ffc107;
            margin-left: 8px;
        }

        .search-card .form-control:focus,
        .search-card .form-select:focus {
            border-color: #7424a9;
            box-shadow: 0 0 0 0.25rem rgba(116, 36, 169, 0.25);
        }

        .search-card .btn-primary {
            background: linear-gradient(90deg, #7424a9 0%, #9d4edd 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 20px;
        }

        .search-card .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(116, 36, 169, 0.3);
        }

        .alert {
            border-radius: 10px;
            margin-bottom: 20px;
        }

        /* Footer خاص بالصفحة */
        .exam-footer-custom {
            background: linear-gradient(135deg, #7424a9 0%, #9d4edd 100%);
            color: white;
            padding: 30px 0;
            text-align: center;
            box-shadow: 0 -4px 15px rgba(0,0,0,0.1);
        }

        .exam-footer-custom p {
            margin-bottom: 10px;
            font-size: 15px;
        }

        .exam-footer-custom .social-icons a {
            color: white;
            font-size: 20px;
            margin: 0 10px;
            transition: transform 0.3s ease;
        }

        .exam-footer-custom .social-icons a:hover {
            transform: translateY(-3px);
            color: #fa896b;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .exam-header-custom .container {
                flex-direction: column;
                text-align: center;
            }
            .search-card {
                padding: 30px 20px;
            }
            .search-card h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- Custom Header -->
    <header class="exam-header-custom">
        <div class="container">
            <div class="logo">منصة مستر سامح</div>
            <div>البحث عن النتيجة</div>
            <a href="{{ url('/') }}">الرئيسية</a>
        </div>
    </header>

    <!-- Search Section -->
    <section class="search-section">
        <div class="search-card">
            <h2><i class="fas fa-search me-2"></i> البحث عن النتيجة</h2>
            <p>أدخل كود النتيجة الذي حصلت عليه بعد إتمام الامتحان</p>

            <div class="code-hint">
                <i class="fas fa-info-circle"></i>
                <strong>أين تجد الكود؟</strong> الكود يظهر لك مباشرة بعد إتمام الامتحان في صفحة الشكر. احفظه جيداً للوصول لنتيجتك لاحقاً.
            </div>

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    @if(session('result_code'))
                        <div class="mt-3" style="background: #fff3cd; padding: 15px; border-radius: 10px; border: 2px solid #ffc107;">
                            <p style="color: #856404; margin-bottom: 10px; font-weight: 600;">
                                <i class="fas fa-key me-2"></i>احفظ هذا الكود للوصول لنتيجتك لاحقاً:
                            </p>
                            <div style="font-size: 24px; font-weight: bold; color: #7424a9; letter-spacing: 3px; font-family: 'Courier New', monospace;">
                                {{ session('result_code') }}
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <form action="{{ route('publicExam.findResult') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="result_code" class="form-label">كود النتيجة:</label>
                    <input type="text" 
                           name="result_code" 
                           class="form-control" 
                           id="result_code" 
                           placeholder="أدخل الكود المكون من 12 حرف" 
                           required
                           maxlength="12"
                           pattern="[A-Z0-9]{12}"
                           oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')">
                    <small class="form-text text-muted" style="display: block; margin-top: 5px;">
                        الكود مكون من 12 حرف (أحرف إنجليزية وأرقام فقط)
                    </small>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i> البحث عن النتيجة
                </button>
            </form>

            <div class="mt-4">
                <a href="{{ route('publicExam.index') }}" class="text-decoration-none">
                    <i class="fas fa-arrow-right me-2"></i>العودة لقائمة الامتحانات
                </a>
            </div>
        </div>
    </section>

    <!-- Custom Footer -->
    <footer class="exam-footer-custom">
        <div class="container">
            <p class="mb-2">&copy; 2025 منصة مستر سامح صلاح. جميع الحقوق محفوظة.</p>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

