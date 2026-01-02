<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$exam_name->exam_title}} | منصة مستر سامح صلاح</title>
    
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
            position: sticky;
            top: 0;
            z-index: 9998;
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

        .exam-header-custom .exam-title-header {
            flex: 1;
            text-align: center;
            font-size: 20px;
            font-weight: 600;
        }

        /* Timer ثابت في الأعلى */
        #fixedTimer {
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            z-index: 10000;
            background: linear-gradient(135deg, #7424a9 0%, #9d4edd 100%);
            color: white;
            padding: 15px 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        #timer {
            font-size: 24px;
            font-weight: bold;
            font-family: 'Arial', sans-serif;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #timer::before {
            content: "⏱️";
            font-size: 20px;
        }

        .progress-container {
            flex: 1;
            max-width: 300px;
            margin: 0 20px;
            background-color: rgba(255,255,255,0.3);
            border-radius: 20px;
            height: 12px;
            overflow: hidden;
        }

        .progress-bar {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, #fff 0%, #ffd700 100%);
            border-radius: 20px;
            transition: width 0.3s ease;
            box-shadow: 0 0 10px rgba(255,255,255,0.5);
        }

        .student-name-badge {
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            backdrop-filter: blur(10px);
        }

        .answered-count {
            font-size: 14px;
            color: rgba(255,255,255,0.9);
        }

        /* تصميم صفحة إدخال الاسم */
        .name-entry-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
        }

        .name-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .name-card h2 {
            color: #7424a9;
            margin-bottom: 30px;
            font-size: 28px;
            font-weight: bold;
        }

        .name-card .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px 20px;
            font-size: 16px;
            transition: all 0.3s;
            text-align: right;
        }

        .name-card .form-control:focus {
            border-color: #7424a9;
            box-shadow: 0 0 0 0.2rem rgba(116, 36, 169, 0.25);
        }

        .name-card .btn-primary {
            background: linear-gradient(135deg, #7424a9 0%, #9d4edd 100%);
            border: none;
            border-radius: 10px;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .name-card .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(116, 36, 169, 0.4);
        }

        /* تصميم أسئلة الامتحان */
        .exam-container {
            max-width: 1200px;
            margin: 180px auto 50px;
            padding: 0 20px;
        }

        .exam-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        .exam-header h1 {
            color: #7424a9;
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .exam-header p {
            color: #666;
            font-size: 16px;
            margin: 0;
        }

        .questions-wrapper {
            display: grid;
            gap: 25px;
        }

        .question-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            transition: all 0.3s;
            border: 2px solid transparent;
            text-align: right;
        }

        .question-card:hover {
            box-shadow: 0 5px 20px rgba(116, 36, 169, 0.15);
            border-color: #7424a9;
            transform: translateY(-2px);
        }

        .question-number {
            display: inline-block;
            background: linear-gradient(135deg, #7424a9 0%, #9d4edd 100%);
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            text-align: center;
            line-height: 35px;
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 16px;
            margin-left: 15px;
        }

        .question-title {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
            line-height: 1.6;
            text-align: right;
        }

        .question-image {
            width: 100%;
            max-width: 600px;
            border-radius: 10px;
            margin: 15px 0;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .options-list {
            list-style: none;
            padding: 0;
            margin: 0;
            text-align: right;
        }

        .option-item {
            margin-bottom: 12px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            transition: all 0.3s;
            border: 2px solid transparent;
            cursor: pointer;
            text-align: right;
        }

        .option-item:hover {
            background: #e9ecef;
            border-color: #7424a9;
            transform: translateX(-5px);
        }

        .option-item label {
            display: flex;
            align-items: center;
            cursor: pointer;
            margin: 0;
            font-size: 16px;
            color: #333;
            width: 100%;
            flex-direction: row-reverse;
            justify-content: flex-start;
        }

        .option-item input[type="radio"] {
            width: 20px;
            height: 20px;
            margin-right: 15px;
            cursor: pointer;
            accent-color: #7424a9;
        }

        .option-item input[type="radio"]:checked + span {
            color: #7424a9;
            font-weight: bold;
        }

        .option-item span {
            flex: 1;
            text-align: right;
        }

        .file-upload-wrapper {
            margin-top: 15px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 2px dashed #e0e0e0;
            text-align: center;
            transition: all 0.3s;
        }

        .file-upload-wrapper:hover {
            border-color: #7424a9;
            background: #f0f0f0;
        }

        .file-upload-wrapper input[type="file"] {
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .submit-button {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            border-radius: 15px;
            padding: 18px 50px;
            font-size: 20px;
            font-weight: bold;
            width: 100%;
            margin-top: 30px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .submit-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }

        .submit-button:active {
            transform: translateY(-1px);
        }

        /* Footer خاص بالصفحة */
        .exam-footer-custom {
            background: linear-gradient(135deg, #7424a9 0%, #9d4edd 100%);
            color: white;
            padding: 30px 0;
            margin-top: 50px;
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
            #fixedTimer {
                top: 60px;
                flex-direction: column;
                gap: 10px;
                padding: 12px;
            }
            
            #timer {
                font-size: 20px;
            }
            
            .progress-container {
                max-width: 100%;
                margin: 0;
            }
            
            .exam-container {
                margin-top: 200px;
            }
            
            .exam-header h1 {
                font-size: 24px;
            }
            
            .question-card {
                padding: 20px;
            }
            
            .name-card {
                padding: 30px 20px;
            }

            .exam-header-custom .exam-title-header {
                font-size: 16px;
            }
        }

        /* تحسينات إضافية */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .timer-warning {
            animation: pulse 1s infinite;
        }
    </style>
</head>
<body>

<!-- Header خاص بالصفحة -->
@if(session('public_exam_student_name'))
<div class="exam-header-custom">
    <div class="container">
        <div class="logo">
            <i class="fas fa-graduation-cap"></i> منصة مستر سامح
        </div>
        <div class="exam-title-header">
            {{$exam_name->exam_title}}
        </div>
        <div>
            <a href="{{ url('/') }}" style="color: white; text-decoration: none;">
                <i class="fas fa-home"></i> الرئيسية
            </a>
        </div>
    </div>
</div>
@endif

<!-- فورم إدخال الاسم -->
@if(!session('public_exam_student_name'))
<div class="name-entry-section">
    <div class="name-card">
        <h2>🎓 {{$exam_name->exam_title}}</h2>
        <p style="color: #666; margin-bottom: 30px;">أدخل اسمك للبدء في الامتحان</p>
        <form action="{{ route('publicExam.start', $exam_name->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <input 
                    type="text" 
                    name="student_name" 
                    class="form-control" 
                    id="student_name" 
                    placeholder="اكتب اسمك هنا..." 
                    required
                    autofocus
                    style="text-align: right;"
                >
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-play-circle"></i> ابدأ الامتحان الآن
            </button>
        </form>
    </div>
</div>
@endif

<!-- صفحة الامتحان -->
@if(session('public_exam_student_name'))
<!-- Timer و Progress Bar -->
<div id="fixedTimer">
    <div id="timer">00:00</div>
    <div class="progress-container">
        <div class="progress-bar" id="progressBar"></div>
    </div>
    <div class="student-name-badge">
        👤 {{ session('public_exam_student_name') }}
    </div>
    <div class="answered-count" id="answeredCount">0 / {{ count($exam_questions) }} إجابة</div>
</div>

<!-- الامتحان -->
<div class="exam-container">
    <div class="exam-header">
        <h1>{{$exam_name->exam_title}}</h1>
        <p>اختر الإجابة الصحيحة لكل سؤال</p>
    </div>

    <form action="{{ route('publicExam.submit', $exam_name->id) }}" method="POST" enctype="multipart/form-data" id="examForm">
        @csrf
        <div class="questions-wrapper">
            @foreach ($exam_questions as $q)
                <div class="question-card">
                    <div class="question-number">{{$loop->iteration}}</div>
                    
                    @php
                        $questionType = $q->question_type ?? 'multiple_choice';
                    @endphp
                    
                    @if ($q->question_title && !$q->img)
                        <div class="question-title">{{$q->question_title}}</div>
                        
                        @if($questionType === 'true_false')
                            {{-- أسئلة صح/غلط --}}
                            <div class="true-false-options" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 20px;">
                                <div class="true-false-card" style="border: 3px solid #28a745; border-radius: 15px; padding: 30px; text-align: center; cursor: pointer; transition: all 0.3s;" onclick="document.getElementById('true_{{$q->id}}').click();">
                                    <input type="radio" name="answer[{{$q->id}}]" value="صح" id="true_{{$q->id}}" style="width: 25px; height: 25px; margin-bottom: 15px;">
                                    <label for="true_{{$q->id}}" style="cursor: pointer; display: block;">
                                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                        <h4 class="fw-bold text-success">صح</h4>
                                    </label>
                                </div>
                                <div class="true-false-card" style="border: 3px solid #dc3545; border-radius: 15px; padding: 30px; text-align: center; cursor: pointer; transition: all 0.3s;" onclick="document.getElementById('false_{{$q->id}}').click();">
                                    <input type="radio" name="answer[{{$q->id}}]" value="غلط" id="false_{{$q->id}}" style="width: 25px; height: 25px; margin-bottom: 15px;">
                                    <label for="false_{{$q->id}}" style="cursor: pointer; display: block;">
                                        <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                                        <h4 class="fw-bold text-danger">غلط</h4>
                                    </label>
                                </div>
                            </div>
                        @else
                            {{-- أسئلة اختيار من متعدد --}}
                            <ul class="options-list">
                                @if($q->ch_1)
                                <li class="option-item">
                                    <label for="{{$q->ch_1.$q->id}}">
                                        <input type="radio" name="answer[{{$q->id}}]" value="{{$q->ch_1}}" id="{{$q->ch_1.$q->id}}">
                                        <span>{{$q->ch_1}}</span>
                                    </label>
                                </li>
                                @endif
                                @if($q->ch_2)
                                <li class="option-item">
                                    <label for="{{$q->ch_2.$q->id}}">
                                        <input type="radio" name="answer[{{$q->id}}]" value="{{$q->ch_2}}" id="{{$q->ch_2.$q->id}}">
                                        <span>{{$q->ch_2}}</span>
                                    </label>
                                </li>
                                @endif
                                @if($q->ch_3)
                                <li class="option-item">
                                    <label for="{{$q->ch_3.$q->id}}">
                                        <input type="radio" name="answer[{{$q->id}}]" value="{{$q->ch_3}}" id="{{$q->ch_3.$q->id}}">
                                        <span>{{$q->ch_3}}</span>
                                    </label>
                                </li>
                                @endif
                                @if($q->ch_4)
                                <li class="option-item">
                                    <label for="{{$q->ch_4.$q->id}}">
                                        <input type="radio" name="answer[{{$q->id}}]" value="{{$q->ch_4}}" id="{{$q->ch_4.$q->id}}">
                                        <span>{{$q->ch_4}}</span>
                                    </label>
                                </li>
                                @endif
                            </ul>
                        @endif
                    @elseif(!$q->question_title && $q->img)
                        <img src="{{url('upload_files/'.$q->img)}}" class="question-image" alt="سؤال {{$loop->iteration}}">
                        
                        @if($questionType === 'true_false')
                            {{-- أسئلة صح/غلط --}}
                            <div class="true-false-options" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 20px;">
                                <div class="true-false-card" style="border: 3px solid #28a745; border-radius: 15px; padding: 30px; text-align: center; cursor: pointer; transition: all 0.3s;" onclick="document.getElementById('true_{{$q->id}}').click();">
                                    <input type="radio" name="answer[{{$q->id}}]" value="صح" id="true_{{$q->id}}" style="width: 25px; height: 25px; margin-bottom: 15px;">
                                    <label for="true_{{$q->id}}" style="cursor: pointer; display: block;">
                                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                        <h4 class="fw-bold text-success">صح</h4>
                                    </label>
                                </div>
                                <div class="true-false-card" style="border: 3px solid #dc3545; border-radius: 15px; padding: 30px; text-align: center; cursor: pointer; transition: all 0.3s;" onclick="document.getElementById('false_{{$q->id}}').click();">
                                    <input type="radio" name="answer[{{$q->id}}]" value="غلط" id="false_{{$q->id}}" style="width: 25px; height: 25px; margin-bottom: 15px;">
                                    <label for="false_{{$q->id}}" style="cursor: pointer; display: block;">
                                        <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                                        <h4 class="fw-bold text-danger">غلط</h4>
                                    </label>
                                </div>
                            </div>
                        @elseif($q->ch_1 || $q->ch_2 || $q->ch_3 || $q->ch_4)
                            {{-- أسئلة اختيار من متعدد --}}
                            <ul class="options-list">
                                @if($q->ch_1)
                                <li class="option-item">
                                    <label for="{{$q->ch_1.$q->id}}">
                                        <input type="radio" name="answer[{{$q->id}}]" value="{{$q->ch_1}}" id="{{$q->ch_1.$q->id}}">
                                        <span>{{$q->ch_1}}</span>
                                    </label>
                                </li>
                                @endif
                                @if($q->ch_2)
                                <li class="option-item">
                                    <label for="{{$q->ch_2.$q->id}}">
                                        <input type="radio" name="answer[{{$q->id}}]" value="{{$q->ch_2}}" id="{{$q->ch_2.$q->id}}">
                                        <span>{{$q->ch_2}}</span>
                                    </label>
                                </li>
                                @endif
                                @if($q->ch_3)
                                <li class="option-item">
                                    <label for="{{$q->ch_3.$q->id}}">
                                        <input type="radio" name="answer[{{$q->id}}]" value="{{$q->ch_3}}" id="{{$q->ch_3.$q->id}}">
                                        <span>{{$q->ch_3}}</span>
                                    </label>
                                </li>
                                @endif
                                @if($q->ch_4)
                                <li class="option-item">
                                    <label for="{{$q->ch_4.$q->id}}">
                                        <input type="radio" name="answer[{{$q->id}}]" value="{{$q->ch_4}}" id="{{$q->ch_4.$q->id}}">
                                        <span>{{$q->ch_4}}</span>
                                    </label>
                                </li>
                                @endif
                            </ul>
                        @endif
                    @elseif($q->img && !$q->ch_1)
                        <img src="{{url('upload_files/'.$q->img)}}" class="question-image" alt="سؤال {{$loop->iteration}}">
                        <div class="file-upload-wrapper">
                            <label for="answer_{{$q->id}}" style="cursor: pointer;">
                                <i class="fas fa-upload"></i> اختر صورة للإجابة
                            </label>
                            <input type="file" name="answer[{{$q->id}}][image]" id="answer_{{$q->id}}" accept="image/*" style="display: none;" onchange="this.nextElementSibling.textContent = '✓ تم اختيار الملف: ' + this.files[0].name">
                            <span style="display: block; margin-top: 10px; color: #666;"></span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        
        <button type="submit" class="submit-button">
            <i class="fas fa-check-circle"></i> إنهاء الامتحان وإرسال الإجابات
        </button>
    </form>
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
@endif

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@if(session('public_exam_student_name'))
<script>
// Timer
let examDuration = {{$exam_name->exam_time}} * 60;
let examId = {{$exam_name->id}};
let startTimeKey = `startTime_${examId}`;
let timeSpentKey = `timeSpent_${examId}`;

let startTime = localStorage.getItem(startTimeKey);
let timeSpent = localStorage.getItem(timeSpentKey) ? parseInt(localStorage.getItem(timeSpentKey)) : 0;

if (!startTime) {
    startTime = Date.now();
    localStorage.setItem(startTimeKey, startTime);
}

timeSpent = Math.floor((Date.now() - startTime) / 1000);
let timeLimit = examDuration - timeSpent;

function startTimer(duration) {
    let timer = duration, minutes, seconds;
    const display = document.getElementById('timer');
    const timerElement = document.getElementById('fixedTimer');

    const countdown = setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        // تحذير عند اقتراب انتهاء الوقت
        if (timer <= 60 && timer > 0) {
            display.style.color = '#ff6b6b';
            timerElement.classList.add('timer-warning');
        }

        if (--timer < 0) {
            clearInterval(countdown);
            alert('انتهى الوقت! سيتم إرسال الإجابات تلقائياً.');
            document.getElementById('examForm').submit();
        }

        localStorage.setItem(timeSpentKey, Math.floor((examDuration - timer)));
    }, 1000);
}

if (timeLimit <= 0) {
    document.getElementById('examForm').submit();
} else {
    startTimer(timeLimit);
}

// مسح التخزين عند إرسال النموذج
document.getElementById('examForm').onsubmit = function () {
    localStorage.removeItem(startTimeKey);
    localStorage.removeItem(timeSpentKey);
};

// تحذير عند الخروج
window.onbeforeunload = function() {
    return "⚠️ تحذير: هل أنت متأكد من الخروج؟ سيتم فقدان إجاباتك!";
};

// Progress Bar حسب حل الأسئلة
const totalQuestions = {{ count($exam_questions) }};
const progressBar = document.getElementById('progressBar');
const answeredCount = document.getElementById('answeredCount');
const form = document.getElementById('examForm');

function updateProgress() {
    const answered = form.querySelectorAll('input[type="radio"]:checked').length;
    const fileInputs = form.querySelectorAll('input[type="file"]');
    let fileAnswered = 0;
    fileInputs.forEach(input => {
        if (input.files && input.files.length > 0) {
            fileAnswered++;
        }
    });
    
    const totalAnswered = answered + fileAnswered;
    const progressPercent = (totalAnswered / totalQuestions) * 100;
    progressBar.style.width = progressPercent + '%';
    answeredCount.textContent = `${totalAnswered} / ${totalQuestions} إجابة`;
    
    // تغيير لون الـ progress bar حسب النسبة
    if (progressPercent >= 80) {
        progressBar.style.background = 'linear-gradient(90deg, #28a745 0%, #20c997 100%)';
    } else if (progressPercent >= 50) {
        progressBar.style.background = 'linear-gradient(90deg, #ffc107 0%, #ff9800 100%)';
    } else {
        progressBar.style.background = 'linear-gradient(90deg, #fff 0%, #ffd700 100%)';
    }
}

form.addEventListener('change', function(e) {
    updateProgress();
});

// تحديث عند تحميل الصفحة
updateProgress();

// إضافة تأثير عند اختيار إجابة
document.querySelectorAll('input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const optionItem = this.closest('.option-item');
        document.querySelectorAll('.option-item').forEach(item => {
            if (item !== optionItem) {
                item.style.background = '#f8f9fa';
                item.style.borderColor = 'transparent';
            }
        });
        optionItem.style.background = '#e7f3ff';
        optionItem.style.borderColor = '#7424a9';
    });
});
</script>
@endif

</body>
</html>
