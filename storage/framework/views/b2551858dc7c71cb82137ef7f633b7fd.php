<?php $__env->startSection('title'); ?>
لوحة التحكم | الادمن
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&family=Tajawal:wght@400;500;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
            --success-gradient: linear-gradient(135deg, #34d399 0%, #059669 100%);
            --warning-gradient: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
            --info-gradient: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%);
            --glass: rgba(255, 255, 255, 0.9);
        }

        body {
            font-family: 'Tajawal', 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        /* إصلاح تداخل الطبقات */
        .side-menu-fixed,
        .overlay {
            position: fixed !important;
            z-index: 999;
        }

        /* الكروت الذكية */
        .smart-card {
            border: none;
            border-radius: 24px;
            background: var(--glass);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .smart-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        /* تصميم الأيقونات */
        .icon-shape {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 20px;
            transition: 0.3s;
        }

        .icon-primary {
            background: #eef2ff;
            color: #4338ca;
        }

        .icon-success {
            background: #ecfdf5;
            color: #059669;
        }

        .icon-warning {
            background: #fffbeb;
            color: #d97706;
        }

        .icon-info {
            background: #f0f9ff;
            color: #0284c7;
        }

        /* الأرقام والخطوط */
        .stat-val {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 2px;
        }

        .stat-lab {
            font-size: 0.9rem;
            font-weight: 600;
            color: #64748b;
        }

        /* الأزرار الحديثة */
        .btn-pill {
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-pill:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }

        /* كرت بوابة أولياء الأمور (Premium) */
        .parent-banner {
            background: #1e293b;
            background-image: radial-gradient(circle at 0% 0%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(168, 85, 247, 0.15) 0%, transparent 50%);
            border-radius: 28px;
            color: white;
            padding: 40px;
            border: none;
        }

        /* كروت الامتحانات */
        .exam-glass-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #f1f5f9;
            padding: 20px;
            transition: 0.3s;
        }

        .exam-glass-card:hover {
            background: #fdfdff;
            border-color: #e2e8f0;
        }

        /* الترويسة */
        .welcome-section {
            padding: 30px 0;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 40px;
        }
        body {
    font-family: 'Tajawal', 'Plus Jakarta Sans', sans-serif;
    background-color: #f8fafc;
    color: #1e293b;
    position: relative;
}

body {
    font-family: 'Tajawal', 'Plus Jakarta Sans', sans-serif;
    color: #1e293b;

    /* لون الخلفية الأساسي */
    background-color: #f8fafc;

    /* Watermark متكرر + شفافية */
    background-image:
        linear-gradient(
            rgba(248, 250, 252, 0.96),
            rgba(248, 250, 252, 0.96)
        ),
        url('<?php echo e(asset("front/assets/images/logo.PNG")); ?>');

    background-repeat: repeat;
    background-position: center;
    background-size: 150px;
}


    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-header'); ?>
    <div class="welcome-section">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="fw-bold h3 mb-1"> مستر سامح <span class="ms-2">👋</span></h1>
                    <p class="text-muted mb-0 font-weight-medium">إليك ما يحدث في منصتك اليوم، <?php echo e(date('l, d M Y')); ?></p>
                </div>
                <div class="col-auto">
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row g-4">
            <div class="col-xl-3 col-lg-6">
                <div class="card smart-card p-4">
                    <div class="icon-shape icon-primary">
                        <i class="fas fa-users-viewfinder"></i>
                    </div>
                    <div class="stat-val text-indigo"><?php echo e($students); ?></div>
                    <div class="stat-lab mb-4">إجمالي الطلاب المسجلين</div>
                    <div class="d-flex gap-2">
                        <a href="<?php echo e(url('students-all')); ?>" class="btn btn-pill bg-primary text-white w-100 shadow-sm">
                            <i class="fas "></i> جميع الطلاب
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6">
                <div class="card smart-card p-4">
                    <div class="icon-shape icon-warning">
                        <i class="fas fa-medal"></i>
                    </div>
                    <div class="stat-val"><?php echo e($taken_exams); ?></div>
                    <div class="stat-lab mb-4">اختبارات تم إكمالها</div>
                    <a href="<?php echo e(url('show-taken-exams')); ?>" class="btn btn-pill bg-light text-dark w-100 border">
                        تحليل النتائج <i class="fas fa-arrow-left ms-2"></i>
                    </a>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6">
                <div class="card smart-card p-4">
                    <div class="icon-shape icon-info">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="stat-val"><?php echo e($public_exams); ?></div>
                    <div class="stat-lab mb-4">الامتحانات العامة المتاحة</div>
                    <a href="<?php echo e(route('publicExam.results')); ?>" class="btn btn-pill bg-info text-white w-100 shadow-sm">
                        إدارة الامتحانات
                    </a>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6">
                <div class="card smart-card p-4">
                    <div class="icon-shape icon-success">
                        <i class="fas fa-clapperboard"></i>
                    </div>
                    <div class="stat-val"><?php echo e($lectures); ?></div>
                    <div class="stat-lab mb-4">محاضرات تعليمية</div>
                    <a href="<?php echo e(route('lecture.index')); ?>" class="btn btn-pill bg-success text-white w-100 shadow-sm">
                        إدارة المحتوى
                    </a>
                </div>
            </div>

            <div class="col-12 mt-4">
                <div class="card parent-banner shadow-lg">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-primary px-3 py-2 me-3 rounded-pill">جديد</span>
                                <h4 class="mb-0 fw-bold text-white">مركز متابعة أولياء الأمور</h4>
                            </div>
                            <p class="text-white-50 fs-5 mb-4">نظام متكامل يربط بين الطالب، المعلم، وولي الأمر لمتابعة
                                التحصيل الدراسي بشكل لحظي.</p>
                            <div class="d-flex gap-3">
                                <a href="<?php echo e(url('parent-portal')); ?>"
                                    class="btn btn-pill btn-light px-5 py-2 fw-bold shadow">دخول المنصة</a>
                                <a href="<?php echo e(url('admin/parent-portal')); ?>"
                                    class="btn btn-pill btn-outline-light px-4">الإعدادات الفنية</a>
                            </div>
                        </div>
                        <div class="col-lg-4 d-none d-lg-block text-center">
                            <i class="fas fa-user-shield opacity-25" style="font-size: 150px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if(isset($public_exams_list) && $public_exams_list->count() > 0): ?>
            <div class="row mt-5 pt-4">
                <div class="col-12 d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="fw-bold mb-1">الامتحانات المجانية الحالية</h4>
                        <p class="text-muted small">آخر الامتحانات التي تم طرحها للجمهور</p>
                    </div>
                    <a href="<?php echo e(route('publicExam.results')); ?>" class="btn btn-link text-decoration-none fw-bold">عرض الكل</a>
                </div>

                <?php $__currentLoopData = $public_exams_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="exam-glass-card h-100 d-flex flex-column shadow-sm">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1"><?php echo e($exam->exam_title); ?></h6>
                                    <span class="text-muted x-small" style="font-size: 12px;">
                                        <i class="far fa-clock me-1"></i> المدة: <?php echo e($exam->exam_time); ?> دقيقة
                                    </span>
                                </div>
                                <div class="ms-2">
                                    <span class="badge bg-soft-info text-info rounded-pill px-3">مجاني</span>
                                </div>
                            </div>

                            <p class="text-muted small flex-grow-1">
                                <?php echo e(Str::limit($exam->exam_description, 90)); ?>

                            </p>

                            <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                                <div class="avatar-group d-flex align-items-center">
                                    <i class="fas fa-users me-2 text-muted"></i>
                                    <span class="text-muted small">نشط الآن</span>
                                </div>
                                <a href="<?php echo e(route('publicExam.take', $exam->id)); ?>"
                                    class="btn btn-sm btn-pill bg-indigo text-white px-4 shadow-sm" target="_blank">
                                    بدأ الامتحان
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('back_layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\A-Tech\Downloads\archiveGzNa7\resources\views/home.blade.php ENDPATH**/ ?>