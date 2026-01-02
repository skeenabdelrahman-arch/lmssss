@extends('front.layouts.app')
@section('title')
{{$exam_name->exam_title}} | منصة مستر حماده مراد
@endsection

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
        --primary-light: #b05ee7;
    }

    .exam-section {
        padding: 120px 0 80px;
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.03), rgba(250, 137, 107, 0.03));
        min-height: calc(100vh - 90px);
    }

    #fixedTimer {
        position: fixed;
        top: 100px;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        max-width: 600px;
        z-index: 9999;
        background: white;
        padding: 20px 25px;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(116, 36, 169, 0.3);
        border: 2px solid var(--primary-color);
    }

    #timer {
        font-size: 24px;
        font-weight: 700;
        color: var(--secondary-color);
        text-align: center;
        margin-bottom: 15px;
    }

    .progress-container {
        width: 100%;
        background: #e0e0e0;
        border-radius: 10px;
        height: 12px;
        overflow: hidden;
    }

    .progress-bar {
        width: 0%;
        height: 100%;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        border-radius: 10px;
        transition: width 0.5s ease;
    }

    .exam-container {
        max-width: 900px;
        margin: 0 auto;
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        margin-top: 100px;
    }

    .exam-header {
        text-align: center;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 2px solid #e0e0e0;
    }

    .exam-header h2 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 10px;
    }

    .question-card {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }

    .question-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 5px 20px rgba(116, 36, 169, 0.1);
    }

    .question-card h5 {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 20px;
    }

    .question-card img {
        max-width: 100%;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .question_options {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .question_options li {
        margin-bottom: 12px;
    }

    .question_options label {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    .question_options label:hover {
        border-color: var(--primary-color);
        background: rgba(116, 36, 169, 0.05);
    }

    .question_options input[type="radio"] {
        margin-left: 15px;
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: var(--primary-color);
    }

    .question_options input[type="radio"]:checked + label,
    .question_options input[type="radio"]:checked ~ label {
        border-color: var(--primary-color);
        background: rgba(116, 36, 169, 0.1);
    }

    .finish-btn {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 30px;
    }

    .finish-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(116, 36, 169, 0.3);
    }

    .file-upload {
        padding: 15px;
        border: 2px dashed var(--primary-color);
        border-radius: 10px;
        text-align: center;
        background: rgba(116, 36, 169, 0.05);
    }

    .file-upload input[type="file"] {
        width: 100%;
        padding: 10px;
        border-radius: 8px;
    }

    @media (max-width: 768px) {
        .exam-container {
            padding: 25px 20px;
            margin-top: 120px;
        }

        #fixedTimer {
            top: 90px;
            padding: 15px 20px;
        }

        #timer {
            font-size: 20px;
        }
    }
</style>

<section class="exam-section">
    <!-- Timer و Progress Bar -->
    <div id="fixedTimer">
        <div id="timer">00:00</div>
        <div class="progress-container">
            <div class="progress-bar" id="progressBar"></div>
        </div>
    </div>

    <div class="exam-container">
        <div class="exam-header">
            <h2><i class="fas fa-clipboard-list me-2"></i>{{$exam_name->exam_title}}</h2>
        </div>

        <form action="{{route('add_question_answer',$exam_name->id)}}" method="POST" enctype="multipart/form-data" id="examForm">
            @csrf
            <input type="hidden" name="time_elapsed" id="timeElapsedInput" value="0">
            @foreach ($exam_questions as $q)
                <div class="question-card">
                    @php
                        // بناء style للسؤال بناءً على التنسيق
                        $questionStyle = '';
                        if ($q->question_font_family) {
                            $questionStyle .= 'font-family: ' . $q->question_font_family . '; ';
                        }
                        if ($q->question_font_size) {
                            $questionStyle .= 'font-size: ' . $q->question_font_size . '; ';
                        }
                        if ($q->question_text_color) {
                            $questionStyle .= 'color: ' . $q->question_text_color . '; ';
                        }
                    @endphp
                    
                    @if ($q->question_title)
                        <h5 style="{{ $questionStyle }}">
                            {{$loop->iteration}}.) 
                            @if($q->question_title_formatted)
                                {!! $q->question_title_formatted !!}
                            @else
                                {{ $q->question_title }}
                            @endif
                        </h5>
                    @endif
                    
                    @if($q->img)
                        <img src="{{url('upload_files/'.$q->img)}}" alt="Question Image" style="max-width: 100%; border-radius: 10px; margin-bottom: 20px;">
                    @endif
                    
                    @php
                        $questionType = $q->question_type ?? 'multiple_choice';
                    @endphp
                    
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
                        <ul class="question_options">
                            @php
                                $choices = [
                                    'ch_1' => $q->ch_1,
                                    'ch_2' => $q->ch_2,
                                    'ch_3' => $q->ch_3,
                                    'ch_4' => $q->ch_4
                                ];
                            @endphp
                            
                            @foreach($choices as $key => $choice)
                                @if($choice)
                                <li>
                                    <input type="radio" name="answer[{{$q->id}}]" value="{{$choice}}" id="{{$key.$q->id}}">
                                    <label for="{{$key.$q->id}}">
                                        @if($q->{$key . '_img'})
                                            <img src="{{url('upload_files/'.$q->{$key . '_img'})}}" alt="Choice {{$key}}" style="max-width: 200px; max-height: 200px; border-radius: 8px; display: block; margin-bottom: 5px;">
                                        @endif
                                        <span>{{ $choice }}</span>
                                    </label>
                                </li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                    
                    @if($q->img && $questionType !== 'true_false' && !$q->ch_1 && !$q->ch_2 && !$q->ch_3 && !$q->ch_4)
                        <div class="file-upload">
                            <input type="file" name="answer[{{$q->id}}][image]" accept="image/*" required>
                        </div>
                    @endif
                </div>
            @endforeach

            <button class="finish-btn" type="submit">
                <i class="fas fa-check-circle me-2"></i>إنهاء الامتحان
            </button>
        </form>
    </div>
</section>

<script>
    // Timer مع دعم time_elapsed من السيرفر
    let examDuration = {{$exam_name->exam_time}} * 60; // بالثواني
    let examId = {{$exam_name->id}};
    let timeElapsedFromServer = {{ $time_elapsed ?? 0 }}; // الوقت المنقضي من السيرفر
    
    let startTimeKey = `startTime_${examId}`;
    let timeSpentKey = `timeSpent_${examId}`;

    // إذا كان هناك وقت منقضي من السيرفر، نستخدمه كنقطة بداية
    let currentSessionStart = Date.now();
    let timeSpent = timeElapsedFromServer;

    function startTimer(duration) {
        let timer = duration, minutes, seconds;
        const display = document.getElementById('timer');
        const progressBar = document.getElementById('progressBar');

        const countdown = setInterval(function () {
            // حساب الوقت الإجمالي المنقضي (من السيرفر + الجلسة الحالية)
            let currentSessionTime = Math.floor((Date.now() - currentSessionStart) / 1000);
            let totalTimeSpent = timeElapsedFromServer + currentSessionTime;
            
            // الوقت المتبقي
            timer = examDuration - totalTimeSpent;
            
            // حفظ الوقت المنقضي في localStorage للاستخدام عند الإرسال
            localStorage.setItem(timeSpentKey, totalTimeSpent);
            
            if (timer < 0) timer = 0;
            
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = minutes + ":" + seconds;
            
            // تحديث شريط التقدم بناءً على الوقت المنقضي
            const progressPercent = (totalTimeSpent / examDuration) * 100;
            progressBar.style.width = progressPercent + '%';
            
            // تغيير لون شريط التقدم حسب الوقت المتبقي
            if (progressPercent >= 90) {
                progressBar.style.background = 'linear-gradient(90deg, #dc3545, #c82333)';
            } else if (progressPercent >= 75) {
                progressBar.style.background = 'linear-gradient(90deg, #ffc107, #ff9800)';
            }

            if (timer <= 60) {
                display.style.color = '#dc3545';
            }

            if (timer <= 0) {
                clearInterval(countdown);
                
                // تحديث time_elapsed قبل الإرسال
                const finalTimeElapsed = localStorage.getItem(timeSpentKey) || examDuration;
                document.getElementById('timeElapsedInput').value = finalTimeElapsed;
                
                // إزالة event listener لمنع التحذير
                window.onbeforeunload = null;
                
                document.getElementById('examForm').submit();
            }
        }, 1000);
    }

    // بدء العداد
    const timeLimit = examDuration - timeElapsedFromServer;
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
        return "هل أنت متأكد من الخروج؟ سيتم فقدان إجاباتك!";
    };

    // الإعدادات للنموذج
    const totalQuestions = {{ count($exam_questions) }};
    const form = document.getElementById('examForm');
    const saveKey = `exam_answers_${examId}`;

    // Auto-save: حفظ الإجابات تلقائياً
    function saveAnswers() {
        const answers = {};
        form.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
            const questionId = radio.name.match(/\[(\d+)\]/)[1];
            answers[questionId] = radio.value;
        });
        localStorage.setItem(saveKey, JSON.stringify(answers));
        console.log('Answers saved:', answers);
    }

    // Auto-save: استعادة الإجابات المحفوظة
    function loadSavedAnswers() {
        const saved = localStorage.getItem(saveKey);
        if (saved) {
            try {
                const answers = JSON.parse(saved);
                Object.keys(answers).forEach(questionId => {
                    const radio = form.querySelector(`input[name="answer[${questionId}]"][value="${answers[questionId]}"]`);
                    if (radio) {
                        radio.checked = true;
                        // Trigger change event to update progress
                        radio.dispatchEvent(new Event('change'));
                    }
                });
                console.log('Answers loaded:', answers);
            } catch (e) {
                console.error('Error loading saved answers:', e);
            }
        }
    }

    // حفظ الإجابات عند أي تغيير (ملاحظة: شريط التقدم يعرض الوقت وليس الإجابات)
    form.addEventListener('change', function(e) {
        if (e.target.type === 'radio') {
            saveAnswers();
        }
    });

    // استعادة الإجابات عند تحميل الصفحة
    loadSavedAnswers();

    // حفظ تلقائي كل 5 ثوان
    setInterval(saveAnswers, 5000);

    // مراجعة قبل الإرسال
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // تحديث time_elapsed قبل الإرسال
        const timeElapsedValue = localStorage.getItem(timeSpentKey) || 0;
        document.getElementById('timeElapsedInput').value = timeElapsedValue;
        
        const answered = form.querySelectorAll('input[type="radio"]:checked').length;
        const unanswered = totalQuestions - answered;
        
        // إنشاء نافذة المراجعة
        const reviewModal = document.createElement('div');
        reviewModal.className = 'review-modal';
        reviewModal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 10000;
            display: flex;
            justify-content: center;
            align-items: center;
        `;
        
        reviewModal.innerHTML = `
            <div style="background: white; padding: 40px; border-radius: 20px; max-width: 600px; width: 90%; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.3);">
                <h2 style="color: #7424a9; margin-bottom: 30px;">
                    <i class="fas fa-clipboard-check me-2"></i>مراجعة الإجابات
                </h2>
                <div style="margin-bottom: 30px;">
                    <div style="display: flex; justify-content: space-around; margin-bottom: 20px;">
                        <div style="padding: 20px; background: #d4edda; border-radius: 15px; flex: 1; margin: 0 10px;">
                            <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                            <h3 style="color: #28a745; margin: 10px 0;">${answered}</h3>
                            <p style="color: #155724;">أسئلة مجابة</p>
                        </div>
                        <div style="padding: 20px; background: #f8d7da; border-radius: 15px; flex: 1; margin: 0 10px;">
                            <i class="fas fa-exclamation-circle fa-3x text-danger mb-2"></i>
                            <h3 style="color: #dc3545; margin: 10px 0;">${unanswered}</h3>
                            <p style="color: #721c24;">أسئلة غير مجابة</p>
                        </div>
                    </div>
                    <div style="padding: 15px; background: #e7f3ff; border-radius: 10px; margin-bottom: 20px;">
                        <p style="margin: 0; color: #004085;">
                            <i class="fas fa-info-circle me-2"></i>
                            إجمالي الأسئلة: <strong>${totalQuestions}</strong>
                        </p>
                    </div>
                </div>
                <div style="display: flex; gap: 15px; justify-content: center;">
                    <button id="cancelReview" style="padding: 12px 30px; background: #6c757d; color: white; border: none; border-radius: 10px; cursor: pointer; font-size: 16px;">
                        <i class="fas fa-arrow-right me-2"></i>العودة للامتحان
                    </button>
                    <button id="confirmSubmit" style="padding: 12px 30px; background: #28a745; color: white; border: none; border-radius: 10px; cursor: pointer; font-size: 16px;">
                        <i class="fas fa-paper-plane me-2"></i>تأكيد الإرسال
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(reviewModal);
        
        // إلغاء المراجعة
        document.getElementById('cancelReview').addEventListener('click', function() {
            document.body.removeChild(reviewModal);
        });
        
        // تأكيد الإرسال
        document.getElementById('confirmSubmit').addEventListener('click', function() {
            // تحديث time_elapsed مرة أخرى قبل الإرسال النهائي
            const finalTimeElapsed = localStorage.getItem(timeSpentKey) || 0;
            document.getElementById('timeElapsedInput').value = finalTimeElapsed;
            
            // مسح الإجابات المحفوظة
            localStorage.removeItem(saveKey);
            
            // إزالة event listener لمنع التكرار
            window.onbeforeunload = null;
            
            // إرسال النموذج
            form.submit();
        });
    });
</script>

@endsection
