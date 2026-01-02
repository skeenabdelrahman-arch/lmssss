<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>شكراً لإتمام الامتحان | منصة مستر سامح صلاح</title>
    
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            direction: rtl;
            text-align: right;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
        .thanks-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .thanks-card {
            background: white;
            border-radius: 20px;
            padding: 50px 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            text-align: center;
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

        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
            animation: scaleIn 0.5s ease 0.3s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .thanks-card h1 {
            color: #7424a9;
            font-size: 36px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .thanks-card p {
            color: #666;
            font-size: 18px;
            margin-bottom: 30px;
            line-height: 1.8;
        }

        .result-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            border: 2px solid #e0e0e0;
        }

        .result-box h3 {
            color: #7424a9;
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .score-display {
            display: flex;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            margin: 20px 0;
        }

        .score-item {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            min-width: 150px;
        }

        .score-item .label {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .score-item .value {
            color: #7424a9;
            font-size: 32px;
            font-weight: bold;
        }

        .percentage-badge {
            display: inline-block;
            padding: 10px 25px;
            border-radius: 25px;
            font-size: 20px;
            font-weight: bold;
            margin-top: 15px;
        }

        .percentage-excellent {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .percentage-good {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: white;
        }

        .percentage-fair {
            background: linear-gradient(135deg, #ff9800 0%, #ff5722 100%);
            color: white;
        }

        .percentage-poor {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .btn-custom {
            padding: 15px 40px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #7424a9 0%, #9d4edd 100%);
            color: white;
            box-shadow: 0 5px 15px rgba(116, 36, 169, 0.3);
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(116, 36, 169, 0.4);
            color: white;
        }

        .btn-secondary-custom {
            background: white;
            color: #7424a9;
            border: 2px solid #7424a9;
        }

        .btn-secondary-custom:hover {
            background: #7424a9;
            color: white;
            transform: translateY(-3px);
        }

        /* Footer خاص بالصفحة */
        .exam-footer-custom {
            background: linear-gradient(135deg, #7424a9 0%, #9d4edd 100%);
            color: white;
            padding: 30px 0;
            text-align: center;
        }

        .exam-footer-custom p {
            margin: 10px 0;
            font-size: 16px;
        }

        .exam-footer-custom a {
            color: #fa896b;
            text-decoration: none;
            margin: 0 10px;
        }

        .exam-footer-custom a:hover {
            text-decoration: underline;
        }

        /* استجابة للهواتف */
        @media (max-width: 768px) {
            .thanks-card {
                padding: 40px 25px;
            }

            .thanks-card h1 {
                font-size: 28px;
            }

            .score-display {
                flex-direction: column;
            }

            .score-item {
                width: 100%;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-custom {
                width: 100%;
                justify-content: center;
            }
        }

        /* تأثيرات إضافية */
        .celebration {
            font-size: 40px;
            margin: 20px 0;
            animation: bounce 1s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>

<!-- Header خاص بالصفحة -->
<div class="exam-header-custom">
    <div class="container">
        <div class="logo">
            <i class="fas fa-graduation-cap"></i> منصة مستر سامح
        </div>
        <div>
            <a href="{{ url('/') }}">
                <i class="fas fa-home"></i> الرئيسية
            </a>
        </div>
    </div>
</div>

<!-- محتوى الصفحة -->
<div class="thanks-container">
    <div class="thanks-card">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1>شكراً لإتمام الامتحان! 🎉</h1>
        
        <p>تم استلام إجاباتك بنجاح وسيتم مراجعتها قريباً</p>

        @if(session('student_degree') && session('total_degree'))
        <div class="result-box">
            <h3>نتيجتك</h3>
            <div class="score-display">
                <div class="score-item">
                    <div class="label">درجتك</div>
                    <div class="value" style="color: #28a745;">{{ session('student_degree') }}</div>
                </div>
                <div class="score-item">
                    <div class="label">الدرجة الكلية</div>
                    <div class="value" style="color: #7424a9;">{{ session('total_degree') }}</div>
                </div>
            </div>
            
            @php
                $percentage = session('total_degree') > 0 ? round((session('student_degree') / session('total_degree')) * 100, 2) : 0;
            @endphp
            
            <div style="margin-top: 20px;">
                <span class="percentage-badge 
                    @if($percentage >= 85) percentage-excellent
                    @elseif($percentage >= 70) percentage-good
                    @elseif($percentage >= 50) percentage-fair
                    @else percentage-poor
                    @endif">
                    النسبة: {{ $percentage }}%
                </span>
            </div>

            @if($percentage >= 85)
                <div class="celebration">🎊 ممتاز! أداء رائع 🎊</div>
            @elseif($percentage >= 70)
                <div class="celebration">👍 جيد جداً! 👍</div>
            @elseif($percentage >= 50)
                <div class="celebration">💪 مقبول، استمر في المحاولة 💪</div>
            @else
                <div class="celebration">📚 لا تيأس، استمر في المذاكرة 📚</div>
            @endif
        </div>
        @endif

        @if(session('result_code') && session('result_id'))
        <div class="result-box" style="background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%); border: 2px solid #ffc107;">
            <h3 style="color: #856404; margin-bottom: 20px;">
                <i class="fas fa-key me-2"></i> كود النتيجة الخاص بك
            </h3>
            <div style="text-align: center; margin: 20px 0;">
                <div style="background: white; padding: 20px; border-radius: 10px; display: inline-block; border: 3px solid #ffc107;">
                    <div style="font-size: 14px; color: #666; margin-bottom: 10px;">احفظ هذا الكود للوصول لنتيجتك لاحقاً</div>
                    <div style="font-size: 32px; font-weight: bold; color: #7424a9; letter-spacing: 3px; font-family: 'Courier New', monospace;">
                        {{ session('result_code') }}
                    </div>
                </div>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="{{ route('publicExam.showResult', session('result_id')) }}" class="btn-custom btn-primary-custom" style="display: inline-flex;">
                    <i class="fas fa-eye"></i> عرض النتيجة التفصيلية الآن
                </a>
            </div>
            <div style="margin-top: 15px; padding: 15px; background: rgba(255,255,255,0.7); border-radius: 10px;">
                <p style="color: #856404; margin: 0; font-size: 14px;">
                    <i class="fas fa-shield-alt me-2"></i>
                    <strong>ملاحظة أمنية:</strong> هذا الكود خاص بك فقط. لا تشاركه مع أحد للوصول لنتيجتك لاحقاً.
                </p>
            </div>
        </div>
        @endif

        <div class="action-buttons">
            @if(session('result_code'))
            <a href="{{ route('publicExam.showResult', session('result_id')) }}" class="btn-custom btn-primary-custom">
                <i class="fas fa-eye"></i> عرض النتيجة التفصيلية
            </a>
            @endif
            <a href="{{ route('publicExam.searchResult') }}" class="btn-custom btn-secondary-custom">
                <i class="fas fa-search"></i> البحث عن النتيجة لاحقاً
            </a>
            <a href="{{ url('/') }}" class="btn-custom btn-secondary-custom">
                <i class="fas fa-home"></i> العودة للرئيسية
            </a>
            <a href="{{ route('publicExam.index') }}" class="btn-custom btn-secondary-custom">
                <i class="fas fa-list"></i> عرض المزيد من الامتحانات
            </a>
</div>
        
        @if(session('result_code'))
        <div class="mt-4 text-center">
            <p class="text-muted">
                <i class="fas fa-info-circle me-2"></i>
                يمكنك البحث عن نتيجتك لاحقاً باستخدام الكود: <strong>{{ session('result_code') }}</strong>
            </p>
        </div>
        @endif
    </div>
</div>

<!-- Footer خاص بالصفحة -->
<footer class="exam-footer-custom">
    <div class="container">
        <p><strong>منصة مستر سامح صلاح</strong></p>
        <p>© 2025 - جميع الحقوق محفوظة</p>
        <p>
            <a href="{{ url('/') }}">الرئيسية</a> |
            <a href="https://wa.me/+201014506018" target="_blank">تواصل معنا</a>
        </p>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
