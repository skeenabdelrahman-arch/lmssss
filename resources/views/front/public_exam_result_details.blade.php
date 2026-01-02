<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>نتيجة الامتحان | منصة مستر سامح صلاح</title>
    
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
            background: #f5f7fa;
            direction: rtl;
            text-align: right;
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
        .result-section {
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .result-summary-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
            margin-bottom: 30px;
            text-align: center;
        }

        .result-summary-card h2 {
            color: #7424a9;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .result-summary-card h3 {
            color: #555;
            margin-bottom: 30px;
            font-size: 20px;
        }

        .score-display {
            display: flex;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            margin: 30px 0;
        }

        .score-item {
            text-align: center;
            padding: 25px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            min-width: 180px;
        }

        .score-item .label {
            color: #666;
            font-size: 16px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .score-item .value {
            color: #7424a9;
            font-size: 36px;
            font-weight: bold;
        }

        .percentage-badge {
            display: inline-block;
            padding: 15px 30px;
            border-radius: 25px;
            font-size: 24px;
            font-weight: bold;
            margin-top: 20px;
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

        .answers-section {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
        }

        .answers-section h3 {
            color: #7424a9;
            margin-bottom: 30px;
            font-weight: 700;
            text-align: center;
        }

        .answer-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .answer-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .answer-card.correct {
            border-color: #28a745;
            background: #d4edda;
        }

        .answer-card.incorrect {
            border-color: #dc3545;
            background: #f8d7da;
        }

        .question-header {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 15px;
        }

        .question-number {
            background: #7424a9;
            color: white;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .question-title {
            flex: 1;
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .question-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .answer-details {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
        }

        .answer-details .detail-item {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .answer-details .detail-label {
            font-weight: 600;
            color: #555;
            min-width: 120px;
        }

        .answer-details .detail-value {
            color: #333;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }

        .status-correct {
            background: #28a745;
            color: white;
        }

        .status-incorrect {
            background: #dc3545;
            color: white;
        }

        .action-buttons {
            text-align: center;
            margin-top: 30px;
        }

        .btn-custom {
            padding: 12px 30px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin: 0 10px;
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
            box-shadow: 0 -4px 15px rgba(0,0,0,0.1);
            margin-top: 50px;
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
            .result-summary-card,
            .answers-section {
                padding: 25px 15px;
            }
            .score-display {
                flex-direction: column;
            }
            .score-item {
                width: 100%;
            }
            .action-buttons {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            .btn-custom {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Custom Header -->
    <header class="exam-header-custom">
        <div class="container">
            <div class="logo">منصة مستر سامح</div>
            <div>نتيجة الامتحان</div>
            <a href="{{ url('/') }}">الرئيسية</a>
        </div>
    </header>

    <!-- Result Section -->
    <section class="result-section">
        <!-- Result Summary -->
        <div class="result-summary-card">
            <h2><i class="fas fa-trophy me-2"></i> نتيجتك في الامتحان</h2>
            <h3>{{ $result->exam_title }}</h3>
            <p class="text-muted">الطالب: <strong>{{ $result->student_name }}</strong></p>
            
            @if(($result->hide_public_result ?? 0) == 1)
                {{-- إخفاء الدرجة --}}
                <div style="text-align: center; padding: 40px 20px;">
                    <i class="fas fa-clock fa-4x mb-4" style="color: #ffc107;"></i>
                    <h3 style="color: #856404; margin-bottom: 20px;">سيتم مراجعة إجاباتك قريباً</h3>
                    <p style="color: #666; font-size: 18px; margin-bottom: 30px;">
                        تم استلام إجاباتك بنجاح وسيتم مراجعتها من قبل الفريق المختص.<br>
                        سيتم إعلان النتائج لاحقاً.
                    </p>
                    @if(isset($result->result_code))
                    <div style="background: #fff3cd; padding: 20px; border-radius: 15px; margin-top: 30px; border: 2px solid #ffc107;">
                        <p style="color: #856404; margin-bottom: 15px; font-weight: 600;">
                            <i class="fas fa-key me-2"></i>احفظ هذا الكود للوصول لنتيجتك لاحقاً:
                        </p>
                        <div style="font-size: 28px; font-weight: bold; color: #7424a9; letter-spacing: 3px; font-family: 'Courier New', monospace;">
                            {{ $result->result_code }}
                        </div>
                        <a href="{{ route('publicExam.searchResult') }}" class="btn-custom btn-primary-custom mt-3" style="display: inline-flex;">
                            <i class="fas fa-search me-2"></i>البحث عن النتيجة لاحقاً
                        </a>
                    </div>
                    @endif
                </div>
            @else
                {{-- عرض الدرجة --}}
                <div class="score-display">
                    <div class="score-item">
                        <div class="label">درجتك</div>
                        <div class="value" style="color: #28a745;">{{ $result->student_degree }}</div>
                    </div>
                    <div class="score-item">
                        <div class="label">الدرجة الكلية</div>
                        <div class="value" style="color: #7424a9;">{{ $result->total_degree }}</div>
                    </div>
                </div>
                
                <div>
                    <span class="percentage-badge 
                        @if($result->percentage >= 85) percentage-excellent
                        @elseif($result->percentage >= 70) percentage-good
                        @elseif($result->percentage >= 50) percentage-fair
                        @else percentage-poor
                        @endif">
                        النسبة: {{ $result->percentage }}%
                    </span>
                </div>

                @if($result->percentage >= 85)
                    <div style="margin-top: 20px; font-size: 24px;">🎊 ممتاز! أداء رائع 🎊</div>
                @elseif($result->percentage >= 70)
                    <div style="margin-top: 20px; font-size: 24px;">👍 جيد جداً! 👍</div>
                @elseif($result->percentage >= 50)
                    <div style="margin-top: 20px; font-size: 24px;">💪 مقبول، استمر في المحاولة 💪</div>
                @else
                    <div style="margin-top: 20px; font-size: 24px;">📚 لا تيأس، استمر في المذاكرة 📚</div>
                @endif
            @endif
        </div>

        <!-- Detailed Answers -->
        @if(($result->hide_public_result ?? 0) == 0)
        <div class="answers-section">
            <h3><i class="fas fa-list-alt me-2"></i> الإجابات التفصيلية</h3>
            
            @foreach($answers as $answer)
                @php
                    $questionType = $answer->question_type ?? 'multiple_choice';
                @endphp
                <div class="answer-card {{ $answer->is_correct ? 'correct' : 'incorrect' }}">
                    <div class="question-header">
                        <div class="question-number">{{ $loop->iteration }}</div>
                        <div class="question-title">{{ $answer->question_title }}</div>
                    </div>

                    @if($answer->img)
                        <img src="{{ url('upload_files/'.$answer->img) }}" class="question-image" alt="Question Image">
                    @endif

                    <div class="answer-details">
                        <div class="detail-item">
                            <span class="detail-label">إجابتك:</span>
                            <span class="detail-value">
                                <strong>{{ $answer->answer ?: 'لم تجب' }}</strong>
                                @if($answer->is_correct)
                                    <span class="status-badge status-correct ms-2">
                                        <i class="fas fa-check"></i> صحيح
                                    </span>
                                @else
                                    <span class="status-badge status-incorrect ms-2">
                                        <i class="fas fa-times"></i> خطأ
                                    </span>
                                @endif
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">الإجابة الصحيحة:</span>
                            <span class="detail-value">
                                <strong style="color: #28a745;">{{ $answer->correct_answer }}</strong>
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">درجة السؤال:</span>
                            <span class="detail-value">
                                <strong>{{ $answer->Q_degree }}</strong>
                            </span>
                        </div>
                        @php
                            $questionType = $answer->question_type ?? 'multiple_choice';
                        @endphp
                        @if($questionType === 'multiple_choice' && ($answer->ch_1 || $answer->ch_2 || $answer->ch_3 || $answer->ch_4))
                        <div class="detail-item">
                            <span class="detail-label">الخيارات:</span>
                            <span class="detail-value">
                                @if($answer->ch_1) {{ $answer->ch_1 }} @endif
                                @if($answer->ch_2) | {{ $answer->ch_2 }} @endif
                                @if($answer->ch_3) | {{ $answer->ch_3 }} @endif
                                @if($answer->ch_4) | {{ $answer->ch_4 }} @endif
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('publicExam.searchResult') }}" class="btn-custom btn-secondary-custom">
                <i class="fas fa-search"></i> البحث عن نتيجة أخرى
            </a>
            <a href="{{ route('publicExam.index') }}" class="btn-custom btn-primary-custom">
                <i class="fas fa-list"></i> عرض المزيد من الامتحانات
            </a>
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

