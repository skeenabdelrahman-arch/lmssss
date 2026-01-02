@extends('front.layouts.app')
@section('title')
    الأسئلة الشائعة | {{ site_name() }}
@endsection

@section('content')
<style>
    .faq-section {
        padding: 120px 0 80px;
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.03), rgba(250, 137, 107, 0.03));
        min-height: calc(100vh - 90px);
    }

    .faq-header {
        text-align: center;
        margin-bottom: 60px;
    }

    .faq-header h1 {
        font-size: 3rem;
        font-weight: 700;
        color: #7424a9;
        margin-bottom: 20px;
    }

    .faq-header p {
        font-size: 1.3rem;
        color: #6c757d;
        margin-bottom: 0;
    }

    .faq-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .faq-item {
        background: white;
        border-radius: 15px;
        margin-bottom: 25px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .faq-item:hover {
        box-shadow: 0 8px 30px rgba(116, 36, 169, 0.15);
        transform: translateY(-3px);
    }

    .faq-question {
        padding: 25px 30px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-bottom: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .faq-question:hover {
        background: linear-gradient(135deg, #7424a9 0%, #fa896b 100%);
        color: white;
    }

    .faq-question.active {
        background: linear-gradient(135deg, #7424a9 0%, #fa896b 100%);
        color: white;
        border-bottom-color: transparent;
    }

    .faq-question h3 {
        font-size: 1.2rem;
        font-weight: 600;
        margin: 0;
        flex: 1;
        padding-left: 20px;
    }

    .faq-question .icon {
        font-size: 1.5rem;
        transition: transform 0.3s ease;
    }

    .faq-question.active .icon {
        transform: rotate(180deg);
    }

    .faq-answer {
        padding: 0 30px;
        max-height: 0;
        overflow: hidden;
        transition: all 0.4s ease;
        background: white;
    }

    .faq-answer.active {
        padding: 25px 30px;
        max-height: 1000px;
    }

    .faq-answer p {
        font-size: 1.05rem;
        line-height: 1.8;
        color: #495057;
        margin: 0;
    }

    @media (max-width: 768px) {
        .faq-header h1 {
            font-size: 2rem;
        }

        .faq-header p {
            font-size: 1.1rem;
        }

        .faq-question h3 {
            font-size: 1rem;
        }

        .faq-question {
            padding: 20px;
        }

        .faq-answer.active {
            padding: 20px;
        }
    }
</style>

<section class="faq-section">
    <div class="container">
        <div class="faq-header">
            <h1>
                <i class="fas fa-question-circle me-2"></i>
                {{ setting('faq_title', 'هل لديك سؤال؟') }}
            </h1>
            <p>{{ setting('faq_subtitle', 'إجابات على الأسئلة الأكثر شيوعاً') }}</p>
        </div>

        <div class="faq-container">
            @php
                $faqs = json_decode(setting('faq_list', '[]'), true);
                if (empty($faqs)) {
                    $faqs = [
                        [
                            'question' => 'كيف يمكنني الاشتراك في الكورسات؟',
                            'answer' => 'يمكنك الاشتراك بسهولة عن طريق إنشاء حساب جديد ثم اختيار الكورس المناسب والدفع عبر الطرق المتاحة (فودافون كاش - فوري - إنستا باي).'
                        ],
                        [
                            'question' => 'هل الكورسات متاحة طوال العام؟',
                            'answer' => 'نعم، جميع الكورسات متاحة 24/7 طوال فترة الاشتراك. يمكنك الدراسة في أي وقت يناسبك.'
                        ],
                        [
                            'question' => 'هل يمكنني مشاهدة الفيديوهات أكثر من مرة؟',
                            'answer' => 'نعم، يمكنك مشاهدة الفيديوهات عدد غير محدود من المرات خلال فترة الاشتراك.'
                        ],
                        [
                            'question' => 'كيف يمكنني التواصل إذا واجهت مشكلة؟',
                            'answer' => 'يمكنك التواصل معنا عبر تيلجرام في أي وقت وسنرد عليك في أسرع وقت ممكن'
                        ]
                    ];
                }
            @endphp

            @foreach($faqs as $index => $faq)
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ({{ $index }})">
                        <h3>{{ $faq['question'] ?? '' }}</h3>
                        <i class="fas fa-chevron-down icon"></i>
                    </div>
                    <div class="faq-answer" id="faq-answer-{{ $index }}">
                        <p>{{ $faq['answer'] ?? '' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<script>
    function toggleFAQ(index) {
        const answer = document.getElementById('faq-answer-' + index);
        const question = answer.previousElementSibling;
        
        // إغلاق جميع الأسئلة الأخرى
        document.querySelectorAll('.faq-answer').forEach(function(item, i) {
            if (i !== index) {
                item.classList.remove('active');
                item.previousElementSibling.classList.remove('active');
            }
        });
        
        // تبديل السؤال الحالي
        answer.classList.toggle('active');
        question.classList.toggle('active');
    }
</script>
@endsection

