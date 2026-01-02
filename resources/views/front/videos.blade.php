@extends('front.layouts.app')
@section('title')
    المحاضرات | {{ site_name() }}
@endsection
@section('content')
    <style>
        :root {
            --primary-color:
                {{ primary_color() }}
            ;
            --secondary-color:
                {{ secondary_color() }}
            ;
            --primary-light: #b05ee7;
        }

        .videos-section {
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

        .page-header .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
            justify-content: center;
        }

        .page-header .breadcrumb-item a {
            color: var(--secondary-color);
            text-decoration: none;
        }

        .videos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }

        .video-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            text-decoration: none;
            display: block;
            border: 2px solid transparent;
        }

        .video-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 50px rgba(116, 36, 169, 0.2);
            border-color: var(--primary-color);
            text-decoration: none;
        }

        .video-thumbnail {
            height: 200px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            position: relative;
            overflow: hidden;
        }

        .video-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .video-thumbnail::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(116, 36, 169, 0.3);
            transition: all 0.3s ease;
        }

        .video-card:hover .video-thumbnail::after {
            background: rgba(116, 36, 169, 0.1);
        }

        .play-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: var(--primary-color);
            z-index: 2;
            transition: all 0.3s ease;
        }

        .video-card:hover .play-icon {
            transform: translate(-50%, -50%) scale(1.1);
            background: white;
        }

        .video-content {
            padding: 25px;
        }

        .video-content h5 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
            transition: color 0.3s ease;
        }

        .video-card:hover .video-content h5 {
            color: var(--secondary-color);
        }

        .video-content p {
            color: #6c757d;
            font-size: 0.95rem;
            margin: 0;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .empty-state i {
            font-size: 100px;
            color: var(--primary-light);
            margin-bottom: 30px;
        }

        .empty-state h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1.8rem;
        }

        .empty-state p {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .btn-home {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(116, 36, 169, 0.3);
            color: white;
        }

        .video-card-wrapper {
            position: relative;
        }

        .video-card.disabled-lecture {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
        }

        .badge-row {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 8px;
            z-index: 10;
            flex-wrap: wrap;
        }

        .pill-badge {
            padding: 7px 12px;
            border-radius: 18px;
            font-size: 0.82rem;
            font-weight: 600;
            color: white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-block;
        }

        .pill-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.25);
            color: white;
        }

        .pill-quiz {
            background: linear-gradient(135deg, #7424a9, #fa896b);
        }

        .pill-assignment {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .pill-completed {
            background: linear-gradient(135deg, #28a745, #5ddc74);
        }

        @media (max-width: 768px) {
            .videos-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .page-header h1 {
                font-size: 2rem;
            }
        }
    </style>

    <section class="videos-section">
        <div class="container">
            <div class="page-header">
                <h1><i class="fas fa-video me-2"></i>المحاضرات</h1>

            </div>

            @if($videos->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-video-slash"></i>
                    <h3>لا توجد محاضرات حالياً</h3>
                    <p>نعمل دائماً على إضافة المزيد من المحاضرات. يرجى التحقق لاحقاً.</p>
                    <a href="{{url('/')}}" class="btn-home">
                        <i class="fas fa-home me-2"></i>العودة إلى الرئيسية
                    </a>
                </div>
            @else
                @if(session()->has('error'))
                    <div class="alert alert-modern alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><strong>{{ session()->get('error') }}</strong>
                        @if(session()->has('quiz_id'))
                            <a href="{{ route('quiz.show', session()->get('quiz_id')) }}"
                                class="btn btn-sm btn-modern btn-modern-primary ms-2">
                                <i class="fas fa-question-circle me-1"></i> حل الكويز الآن
                            </a>
                        @endif
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="videos-grid">
                    @foreach($videos as $index => $video)
                        @php
                            // Check if quiz exists, regardless of active status
                            $hasQuiz = $video->quiz ? true : false;
                            // But for access control, check if it's active
                            $canAccessQuiz = $video->quiz && $video->quiz->canStudentAccess($student);
                            $canAccessLecture = true;
                            $restrictionReason = null;

                            $studentView = $video->lectureViews->first();
                            $isViewed = $studentView ? true : false;
                            $isCompleted = $studentView ? (bool) $studentView->completed : false;

                            // التحقق من قيود المحاضرات
                            $isRestricted = \App\Models\LectureRestriction::isRestricted($student->id, $video->id);
                            if ($isRestricted) {
                                $canAccessLecture = false;
                                $restrictionReason = 'محاضرة غير متاحة';
                            }

                            // التحقق من جميع المحاضرات السابقة - إذا كان أي منها لديه كويز إجباري ولم يتم حله، منع الوصول
                            if ($canAccessLecture) {
                                for ($i = 0; $i < $index; $i++) {
                                    $prevVideo = $videos[$i];
                                    if ($prevVideo->quiz && $prevVideo->quiz->canStudentAccess($student) && $prevVideo->quiz->isRequiredForStudent($student)) {
                                        $attempt = $prevVideo->quiz->getStudentAttempt($student->id);
                                        if (!$attempt || !$attempt->is_passed) {
                                            $canAccessLecture = false;
                                            $restrictionReason = 'يجب إكمال الكويزات السابقة';
                                            break; // منع الوصول عند أول كويز غير مكتمل
                                        }
                                    }
                                }
                            }

                            // تحديد إذا كان الكويز إجباري على الطالب الحالي (يستخدم $canAccessQuiz بدلاً من $hasQuiz)
                            $isQuizRequired = $canAccessQuiz && $video->quiz->isRequiredForStudent($student);
                        @endphp

                        <div class="video-card-wrapper">
                            <a href="{{route('lecture', ['lecture_id' => $video->id])}}"
                                class="video-card {{ !$canAccessLecture ? 'disabled-lecture' : '' }}" @if(!$canAccessLecture)
                                    onclick="event.preventDefault(); @if($isRestricted) alert('{{ $restrictionReason }}'); @else showQuizWarning(); @endif"
                                @endif>
                                <div class="video-thumbnail">
                                    @php
                                        $videoImageUrl = $video->getImageUrl();
                                    @endphp
                                    @if($videoImageUrl)
                                        <img src="{{ $videoImageUrl }}" alt="{{$video->title}}"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="biology-placeholder"
                                            style="display: none; align-items: center; justify-content: center; width: 100%; height: 100%; background: linear-gradient(135deg, #2c5f2d, #4a8f4e); position: absolute; top: 0; left: 0; flex-direction: column; gap: 12px; z-index: 1;">
                                            <div
                                                style="display: flex; gap: 15px; align-items: center; justify-content: center; flex-wrap: wrap;">
                                                <i class="fas fa-dna" style="font-size: 35px; color: rgba(255, 255, 255, 0.9);"></i>
                                                <i class="fas fa-microscope"
                                                    style="font-size: 35px; color: rgba(255, 255, 255, 0.9);"></i>
                                                <i class="fas fa-flask" style="font-size: 35px; color: rgba(255, 255, 255, 0.9);"></i>
                                            </div>
                                            <div
                                                style="font-size: 12px; color: rgba(255, 255, 255, 0.8); font-weight: 500; text-align: center; padding: 0 15px;">
                                                <i class="fas fa-leaf"></i> علم الاللغة الانجليزية
                                            </div>
                                        </div>
                                    @else
                                        <div class="biology-placeholder"
                                            style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; background: linear-gradient(135deg, #2c5f2d, #4a8f4e); position: absolute; top: 0; left: 0; flex-direction: column; gap: 12px; z-index: 1;">
                                            <div
                                                style="display: flex; gap: 15px; align-items: center; justify-content: center; flex-wrap: wrap;">
                                                <i class="fas fa-dna" style="font-size: 35px; color: rgba(255, 255, 255, 0.9);"></i>
                                                <i class="fas fa-microscope"
                                                    style="font-size: 35px; color: rgba(255, 255, 255, 0.9);"></i>
                                                <i class="fas fa-flask" style="font-size: 35px; color: rgba(255, 255, 255, 0.9);"></i>
                                            </div>
                                            <div
                                                style="font-size: 12px; color: rgba(255, 255, 255, 0.8); font-weight: 500; text-align: center; padding: 0 15px;">
                                                <i class="fas fa-leaf"></i> علم الاللغة الانجليزية
                                            </div>
                                        </div>
                                    @endif
                                    <div class="play-icon">
                                        <i class="fas fa-play"></i>
                                    </div>
                                </div>
                                <div class="video-content">
                                    <h5>{{$video->title}}</h5>
                                    @if($video->description)
                                        <p>{{ Str::limit($video->description, 80) }}</p>
                                    @endif
                                </div>
                            </a>

                            @php
                                $hasAssignments = isset($video->assignments) && $video->assignments->count();
                                $firstAssignment = $hasAssignments ? $video->assignments->first() : null;
                                $firstPdf = isset($video->pdfs) ? $video->pdfs->where('status', 1)->first() : null;
                            @endphp
                            @if($hasQuiz || $hasAssignments || $firstPdf || $isCompleted)
                                <div class="badge-row">
                                    @if($isCompleted)
                                        <div class="pill-badge pill-completed">
                                            <i class="fas fa-check-circle"></i> تمت المشاهدة
                                        </div>
                                    @endif
                                    @if($hasQuiz)
                                        <a class="pill-badge pill-quiz" href="{{ route('quiz.show', $video->quiz->id) }}"
                                            onclick="event.stopPropagation();">
                                            <i class="fas fa-question-circle"></i> {{ $isQuizRequired ? 'كويز إجباري' : 'كويز' }}
                                        </a>
                                    @endif
                                    @if($firstPdf)
                                        <a class="pill-badge pill-pdf" href="{{ route('pdf.view', $firstPdf->id) }}"
                                            onclick="event.stopPropagation();"
                                            style="background: linear-gradient(135deg, #dc3545, #ff6b6b);">
                                            <i class="fas fa-file-pdf"></i> مذكرة
                                        </a>
                                    @endif
                                    @if($hasAssignments)
                                        <a class="pill-badge pill-assignment"
                                            href="{{ $firstAssignment ? route('student.assignments.show', $firstAssignment->id) : '#' }}"
                                            onclick="event.stopPropagation();">
                                            <i class="fas fa-clipboard-check"></i> واجب
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <script>
        function showQuizWarning() {
            alert('يجب إكمال الكويز الخاص بالمحاضرة السابقة أولاً للوصول لهذه المحاضرة');
        }
    </script>
@endsection