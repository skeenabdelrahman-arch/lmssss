<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أسئلة الامتحان - {{ $exam->exam_title }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Tajawal', 'Arial', sans-serif;
            direction: rtl;
            text-align: right;
            background: #f5f5f5;
            padding: 20px;
            color: #333;
            line-height: 1.8;
        }
        
        .exam-header {
            background: linear-gradient(135deg, #7424a9, #fa896b);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .exam-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .exam-header .exam-info {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 15px;
            font-size: 14px;
            opacity: 0.95;
        }
        
        .exam-header .exam-info span {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .question-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            border-right: 4px solid #7424a9;
            page-break-inside: avoid;
        }
        
        .question-number {
            display: inline-block;
            background: linear-gradient(135deg, #7424a9, #fa896b);
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            text-align: center;
            line-height: 35px;
            font-weight: 700;
            font-size: 16px;
            margin-left: 15px;
            margin-bottom: 15px;
        }
        
        .question-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            line-height: 1.8;
        }
        
        .question-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .choices {
            list-style: none;
            padding: 0;
            margin: 15px 0;
        }
        
        .choice-item {
            background: #f8f9fa;
            padding: 12px 18px;
            margin-bottom: 10px;
            border-radius: 8px;
            border-right: 3px solid #dee2e6;
            transition: all 0.3s ease;
            font-size: 16px;
        }
        
        .choice-item.correct {
            background: #d4edda;
            border-right-color: #28a745;
            color: #155724;
            font-weight: 600;
        }
        
        .choice-item::before {
            content: '○';
            margin-left: 10px;
            font-size: 18px;
            color: #6c757d;
        }
        
        .choice-item.correct::before {
            content: '✓';
            color: #28a745;
        }
        
        .choice-image {
            max-width: 200px;
            height: auto;
            border-radius: 6px;
            margin-top: 8px;
            display: block;
        }
        
        .question-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px dashed #dee2e6;
        }
        
        .question-degree {
            background: #7424a9;
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .correct-answer-badge {
            background: #28a745;
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .question-card {
                page-break-inside: avoid;
            }
            
            .exam-header {
                page-break-after: avoid;
            }
        }
        
        .print-button {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: #7424a9;
            color: white;
            padding: 15px 30px;
            border-radius: 30px;
            border: none;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        
        .print-button:hover {
            background: #5a1d87;
            transform: translateY(-2px);
        }
        
        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="exam-header">
        <h1>{{ $exam->exam_title }}</h1>
        @if($exam->exam_description)
        <p style="margin-top: 10px; opacity: 0.95;">{{ $exam->exam_description }}</p>
        @endif
        <div class="exam-info">
            @if($exam->grade)
            <span><strong>الصف:</strong> {{ $exam->grade }}</span>
            @endif
            @if($exam->month)
            <span><strong>الشهر:</strong> {{ $exam->month->name }}</span>
            @endif
            @if($exam->exam_time)
            <span><strong>وقت الامتحان:</strong> {{ $exam->exam_time }} دقيقة</span>
            @endif
            <span><strong>عدد الأسئلة:</strong> {{ $questions->count() }}</span>
            <span><strong>إجمالي الدرجات:</strong> {{ $questions->sum('Q_degree') }}</span>
        </div>
    </div>
    
    @foreach($questions as $index => $question)
    <div class="question-card">
        <div class="question-number">{{ $index + 1 }}</div>
        
        <div class="question-title">
            @if($question->question_title_formatted)
                {!! $question->question_title_formatted !!}
            @else
                {{ $question->question_title }}
            @endif
        </div>
        
        @if($question->img)
        <img src="{{ url('upload_files/' . $question->img) }}" alt="صورة السؤال" class="question-image" onerror="this.style.display='none';">
        @endif
        
        <ul class="choices">
            @foreach(['ch_1', 'ch_2', 'ch_3', 'ch_4'] as $choice)
                @if($question->$choice)
                <li class="choice-item {{ $question->correct_answer == $question->$choice ? 'correct' : '' }}">
                    {{ $question->$choice }}
                    @if($question->correct_answer == $question->$choice)
                        <span class="correct-answer-badge" style="margin-right: 10px; font-size: 12px;">الإجابة الصحيحة</span>
                    @endif
                    @if($question->{$choice . '_img'})
                        <img src="{{ url('upload_files/' . $question->{$choice . '_img'}) }}" alt="صورة الاختيار" class="choice-image" onerror="this.style.display='none';">
                    @endif
                </li>
                @endif
            @endforeach
        </ul>
        
        <div class="question-footer">
            <div class="question-degree">
                <i class="fas fa-star"></i> درجة السؤال: {{ $question->Q_degree }}
            </div>
            <div class="correct-answer-badge">
                الإجابة الصحيحة: {{ $question->correct_answer }}
            </div>
        </div>
    </div>
    @endforeach
    
    <div style="text-align: center; margin-top: 40px; padding: 20px; color: #6c757d; font-size: 14px;">
        <p>تم إنشاء هذا الملف تلقائياً من نظام {{ site_name() }}</p>
        <p>تاريخ الإنشاء: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
    
    <button class="print-button" onclick="window.print()">
        <i class="fas fa-print"></i> طباعة / حفظ PDF
    </button>
    
    <script>
        // إضافة Font Awesome للطباعة
        if (!document.querySelector('link[href*="font-awesome"]')) {
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
            document.head.appendChild(link);
        }
    </script>
</body>
</html>

