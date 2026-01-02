@extends('front.layouts.app')
@section('title')
    تفاصيل المحاضرة | {{ site_name() }}
@endsection

@section('content')
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

    <style>
        :root {
            --primary-color:
                {{ primary_color() }}
            ;
            --secondary-color:
                {{ secondary_color() }}
            ;
            --plyr-color-main: #7424a9;
            /* جعل لون المشغل مثل لون المنصة */
            --primary-light: #b05ee7;
        }

        .lecture-section {
            padding: 120px 0 80px;
            background: linear-gradient(135deg, rgba(116, 36, 169, 0.03), rgba(250, 137, 107, 0.03));
            min-height: calc(100vh - 90px);
        }

        .video-wrapper {
            max-width: 1200px;
            margin: 0 auto 40px;
            background: white;
            border-radius: 20px;
            padding: 10px;
            /* تقليل الحواف */
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        /* تنسيقات المشغل */
        .plyr {
            border-radius: 15px;
            overflow: hidden;
            --plyr-control-icon-size: 18px;
        }

        /* =========================================
                               الخدعة السحرية لإخفاء يوتيوب
                               ========================================= */

        /* 1. تكبير الفيديو قليلاً لإخفاء الحواف السوداء إن وجدت */
        .plyr__video-embed iframe {
            top: -50%;
            height: 200%;
            transform: scale(1.01);
            /* تكبير بنسبة 1% لإخفاء أي حدود */
            pointer-events: none !important;
            /* منع الضغط على أي شيء داخل فيديو يوتيوب (يمنع فتح القناة) */
        }

        /* 2. طبقة حماية تمنع النقر اليمين أو النقر على العنوان */
        .plyr__poster {
            background-size: cover;
            z-index: 2;
            /* التأكد من أن البوستر يغطي الفيديو قبل التشغيل */
        }

        /* إخفاء عناصر التحكم الأصلية ليوتيوب تماماً */
        .ytp-chrome-top,
        .ytp-show-cards-title {
            display: none !important;
        }

        /* واجبات المحاضرة */
        .assignments-block {
            margin-top: 40px;
        }

        .assignments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
        }

        .assignment-card {
            background: white;
            border-radius: 18px;
            padding: 22px;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
            border: 2px solid transparent;
            position: relative;
            transition: all 0.25s ease;
        }

        .assignment-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(116, 36, 169, 0.18);
            border-color: var(--primary-color);
        }

        .assignment-card[data-status="graded"] {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.07), rgba(255, 255, 255, 0.96));
        }

        .assignment-card[data-status="late"],
        .assignment-card[data-status="pending"] {
            background: linear-gradient(135deg, rgba(23, 162, 184, 0.06), rgba(255, 255, 255, 0.96));
        }

        .assignment-card[data-status="overdue"] {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.07), rgba(255, 255, 255, 0.96));
        }

        .assignment-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 6px 12px;
            border-radius: 14px;
            font-size: .85rem;
            font-weight: 600;
            color: white;
        }

        .badge-graded {
            background: #28a745;
        }

        .badge-late {
            background: #ffc107;
            color: #212529;
        }

        .badge-pending {
            background: #17a2b8;
        }

        .badge-overdue {
            background: #dc3545;
        }

        .badge-new {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .assignment-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .assignment-meta {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .assignment-btn {
            width: 100%;
            padding: 10px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: all 0.25s ease;
            border: none;
            cursor: pointer;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .assignment-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 16px rgba(116, 36, 169, 0.28);
            color: white;
        }
    </style>

    <section class="lecture-section">
        <div class="container">

            <div class="page-header text-center mb-4">
                <h1 style="color: var(--primary-color); font-weight: 700;">
                    <i class="fas fa-video me-2"></i>{{ $lecture->title }}
                </h1>
            </div>

            {{-- ===========================
            YouTube Video Player
            ============================ --}}
            @if ($lecture->video_url)
                <div class="video-wrapper">
                    {{-- قمنا بإضافة data-plyr-provider و data-plyr-embed-id ليعمل Plyr بشكل أفضل مع يوتيوب --}}
                    <div id="player" data-plyr-provider="youtube" data-plyr-embed-id="{{ $lecture->video_url }}">
                    </div>
                </div>
                {{-- LOCAL SERVER VIDEO - يظهر فقط إذا لم يكن هناك فيديو يوتيوب --}}
            @elseif ($lecture->video_server)
                <div class="video-wrapper">
                    <video id="localPlayer" playsinline controls>
                        <source src="{{ url('upload_files/' . $lecture->video_server) }}" type="video/mp4" />
                    </video>
                </div>
            @endif

            <div class="lecture-info bg-white p-4 rounded-3 shadow-sm mt-4">
                <h4><i class="fas fa-info-circle me-2"></i>الوصف</h4>
                <p>{{ $lecture->description ?? 'لا يوجد وصف متاح' }}</p>
                <div class="mt-3">
                    <i class="fas fa-calendar-alt text-primary"></i>
                    <span class="text-dark fw-bold">تاريخ النشر: {{ date('Y-m-d', strtotime($lecture->created_at)) }}</span>
                </div>

                @if(isset($lecture->pdfs) && $lecture->pdfs->count())
                    <hr class="my-3">
                    <h5 class="mb-3" style="color: var(--primary-color); font-weight: 600;">
                        <i class="fas fa-paperclip me-2"></i> المرفقات:
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($lecture->pdfs as $pdf)
                            <a href="{{ route('pdf.view', $pdf->id) }}" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                <i class="fas fa-file-pdf me-1"></i> {{ $pdf->title }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>



            @if(isset($assignments) && $assignments->count())
                <div class="assignments-block">
                    <div class="page-header text-center mb-4">
                        <h2 style="color: var(--primary-color); font-weight: 700; font-size: 1.8rem;">
                            <i class="fas fa-clipboard-check me-2"></i> واجبات المحاضرة
                        </h2>
                    </div>

                    <div class="assignments-grid">
                        @foreach($assignments as $assignment)
                            @php
                                $submission = $assignment->getSubmissionForStudent($student->id);
                                $status = $submission ? $submission->status : ($assignment->isOverdue() ? 'overdue' : 'new');
                                $statusLabel = [
                                    'graded' => 'تم التصحيح',
                                    'late' => 'متأخر',
                                    'pending' => 'تم الإرسال',
                                    'overdue' => 'انتهى الموعد',
                                    'new' => 'جديد',
                                ][$status] ?? 'جديد';
                                $badgeClass = [
                                    'graded' => 'badge-graded',
                                    'late' => 'badge-late',
                                    'pending' => 'badge-pending',
                                    'overdue' => 'badge-overdue',
                                    'new' => 'badge-new',
                                ][$status] ?? 'badge-new';
                            @endphp
                            <div class="assignment-card" data-status="{{ $status }}">
                                <div class="assignment-badge {{ $badgeClass }}">{{ $statusLabel }}</div>
                                <div class="assignment-title" style="padding-inline-end: 72px;">{{ $assignment->title }}</div>
                                @if($assignment->description)
                                    <p class="text-muted mb-2">{{ \Illuminate\Support\Str::limit($assignment->description, 100) }}</p>
                                @endif
                                <div class="assignment-meta"><i class="fas fa-star"></i> {{ $assignment->total_marks }} درجة</div>
                                <div class="assignment-meta"><i class="fas fa-calendar"></i>
                                    {{ $assignment->deadline ? $assignment->deadline->format('Y-m-d H:i') : 'بدون موعد' }}</div>
                                <div class="assignment-meta"><i class="fas fa-flag-checkered"></i> {{ $statusLabel }}</div>
                                <a href="{{ route('student.assignments.show', $assignment->id) }}" class="assignment-btn mt-2">
                                    <i class="fas fa-eye"></i> عرض الواجب
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </section>

    <script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            // إعدادات مشتركة لإخفاء كل شيء
            const commonOptions = {
                controls: [
                    'play-large', // الزر الكبير في المنتصف
                    'play', // زر التشغيل الصغير
                    'progress', // شريط التقدم
                    'current-time',
                    'mute',
                    'volume',
                    'settings',
                    'fullscreen'
                ],
                settings: ['speed'], // إزالة الجودة لأن يوتيوب يتحكم بها أحياناً تلقائياً، ويمكن إضافتها عند الحاجة
                hideControls: true, // إخفاء التحكم عند عدم تحريك الماوس
                resetOnEnd: true, // مهم جداً: عند انتهاء الفيديو يعود للبداية فوراً (لمنع ظهور الفيديوهات المقترحة)
                clickToPlay: true,
                keyboard: { focused: true, global: true },
                tooltips: { controls: true, seek: true }
            };

            // 1. إعداد مشغل يوتيوب
            if (document.querySelector('#player')) {
                const player = new Plyr('#player', {
                    ...commonOptions,
                    youtube: {
                        noCookie: true, // استخدام وضع الخصوصية
                        rel: 0, // عدم إظهار فيديوهات ذات صلة من قنوات أخرى
                        showinfo: 0, // إخفاء المعلومات
                        iv_load_policy: 3, // إخفاء التعليقات التوضيحية
                        modestbranding: 1, // محاولة تقليل شعار يوتيوب
                        controls: 0, // إخفاء تحكم يوتيوب الأصلي
                        disablekb: 1, // تعطيل كيبورد يوتيوب
                        playsinline: 1 // التشغيل داخل الصفحة في الموبايل
                    }
                });



                // خدعة إضافية: عند إيقاف الفيديو (Pause) تأكد من عدم ظهور اقتراحات
                player.on('pause', event => {
                    // يمكن هنا إضافة كود لإظهار بوستر مخصص إذا أردت تغطية الشاشة
                });

                // عند الانتهاء، قم بإعادة تعيين الفيديو فوراً لمنع ظهور شاشة الاقتراحات السوداء
                player.on('ended', event => {
                    player.stop();
                });
            }

            // 2. إعداد المشغل المحلي
            if (document.querySelector('#localPlayer')) {
                const player = new Plyr('#localPlayer', commonOptions);
            }
        });
    </script>
@endsection