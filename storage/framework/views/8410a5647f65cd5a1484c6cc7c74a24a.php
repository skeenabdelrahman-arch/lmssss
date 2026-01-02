<?php $__env->startSection('title'); ?>
الإعدادات العامة
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h2><i class="fas fa-cog me-2"></i> الإعدادات العامة</h2>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-modern-warning" onclick="resetSettings()">
                <i class="fas fa-undo me-2"></i> إعادة تعيين
            </button>
        </div>
    </div>
    <p class="text-muted">قم بتعديل إعدادات المنصة الأساسية (متاح فقط لـ Super Admin)</p>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i> <?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if($errors->any()): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>
    <ul class="mb-0">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form action="<?php echo e(route('admin.general_settings.update')); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <hr class="my-4">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">

            <h5 class="mb-4 d-flex align-items-center">
                <i class="fas fa-video me-2 text-primary"></i>
                إعدادات تتبع مشاهدة الفيديوهات
            </h5>

            
            <div class="form-check form-switch mb-4">
                <input
                    class="form-check-input"
                    type="checkbox"
                    name="video_tracking_enabled"
                    id="video_tracking_enabled"
                    value="1"
                    <?php echo e(($settings['video_tracking_enabled'] ?? '1') == '1' ? 'checked' : ''); ?>

                >
                <label class="form-check-label fw-bold ms-2" for="video_tracking_enabled">
                    تفعيل نظام تتبع المشاهدة بالوقت والنسبة
                </label>
                <small class="text-muted d-block mt-1">
                    سيقوم النظام بتتبع مدى مشاهدة الطالب لكل فيديو بدقة.
                </small>
            </div>

<div id="videoCompletionWrapper"
     class="fade-toggle mt-3 <?php echo e(($settings['video_tracking_enabled'] ?? '1') == '1' ? 'visible' : 'hidden'); ?>">

    <label for="video_completion_percentage" class="form-label fw-semibold">
        النسبة المئوية لاعتبار الفيديو مكتمل
    </label>

    <div class="input-group">
        <span class="input-group-text bg-light">
            <i class="fas fa-percent"></i>
        </span>
        <input
            type="number"
            class="form-control"
            name="video_completion_percentage"
            id="video_completion_percentage"
            value="<?php echo e($settings['video_completion_percentage'] ?? '80'); ?>"
            min="0"
            max="100"
            step="1"
        >
    </div>

    <small class="text-muted">
        عند وصول الطالب لهذه النسبة سيتم تسجيل أن الفيديو تمت مشاهدته.
    </small>
</div>


        </div>
    </div>

    <div class="row">

        <!-- الإعدادات العامة -->
        <div class="col-lg-6">
            <div class="modern-card mb-4">
                <div class="card-header-modern">
                    <h4><i class="fas fa-info-circle me-2"></i> الإعدادات العامة</h4>
                </div>
                <div class="card-body-modern">
                    <div class="mb-3">
                        <label for="site_name" class="form-label">اسم المنصة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="site_name" name="site_name" 
                               value="<?php echo e($settings['site_name'] ?? ''); ?>" required>
                        <small class="text-muted">اسم المنصة الذي سيظهر في جميع الصفحات</small>
                    </div>

                    <div class="mb-3">
                        <label for="teacher_name" class="form-label">اسم المدرس <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="teacher_name" name="teacher_name" 
                               value="<?php echo e($settings['teacher_name'] ?? ''); ?>" required>
                        <small class="text-muted">اسم المدرس (مثل: مستر سامح)</small>
                    </div>

                    <div class="mb-3">
                        <label for="teacher_full_name" class="form-label">الاسم الكامل للمدرس</label>
                        <input type="text" class="form-control" id="teacher_full_name" name="teacher_full_name" 
                               value="<?php echo e($settings['teacher_full_name'] ?? ''); ?>">
                        <small class="text-muted">الاسم الكامل (مثل: سامح صلاح)</small>
                    </div>

                    <div class="mb-3">
                        <label for="subject_name" class="form-label">اسم المادة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="subject_name" name="subject_name" 
                               value="<?php echo e($settings['subject_name'] ?? ''); ?>" required>
                        <small class="text-muted">اسم المادة الدراسية (مثل: الأحياء، الكيمياء)</small>
                    </div>

                    <div class="mb-3">
                        <label for="subject_description" class="form-label">وصف المادة</label>
                        <textarea class="form-control" id="subject_description" name="subject_description" rows="2"><?php echo e($settings['subject_description'] ?? ''); ?></textarea>
                        <small class="text-muted">وصف مختصر للمادة</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- الألوان والمظهر -->
        <div class="col-lg-6">
            <div class="modern-card mb-4">
                <div class="card-header-modern">
                    <h4><i class="fas fa-palette me-2"></i> الألوان والمظهر</h4>
                </div>
                <div class="card-body-modern">
                    <div class="mb-3">
                        <label for="primary_color" class="form-label">اللون الأساسي <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="primary_color" 
                                   name="primary_color" value="<?php echo e($settings['primary_color'] ?? '#7424a9'); ?>" required>
                            <input type="text" class="form-control" id="primary_color_text" 
                                   value="<?php echo e($settings['primary_color'] ?? '#7424a9'); ?>" readonly>
                        </div>
                        <small class="text-muted">اللون الأساسي المستخدم في التصميم</small>
                    </div>

                    <div class="mb-3">
                        <label for="secondary_color" class="form-label">اللون الثانوي <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="secondary_color" 
                                   name="secondary_color" value="<?php echo e($settings['secondary_color'] ?? '#fa896b'); ?>" required>
                            <input type="text" class="form-control" id="secondary_color_text" 
                                   value="<?php echo e($settings['secondary_color'] ?? '#fa896b'); ?>" readonly>
                        </div>
                        <small class="text-muted">اللون الثانوي المستخدم في التصميم</small>
                    </div>

                    <div class="mb-3">
                        <label for="logo" class="form-label">اللوجو</label>
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                        <?php if(isset($settings['logo_path']) && $settings['logo_path']): ?>
                        <div class="mt-2">
                            <img src="<?php echo e(asset($settings['logo_path'])); ?>" alt="Logo" style="max-height: 100px; border-radius: 10px;">
                            <small class="d-block text-muted mt-1">اللوجو الحالي</small>
                        </div>
                        <?php endif; ?>
                        <small class="text-muted">رفع لوجو جديد (PNG, JPG, GIF - حد أقصى 2MB)</small>
                    </div>

                    <div class="mb-3">
                        <label for="favicon" class="form-label">Favicon</label>
                        <input type="file" class="form-control" id="favicon" name="favicon" accept="image/*">
                        <?php if(isset($settings['favicon_path']) && $settings['favicon_path']): ?>
                        <div class="mt-2">
                            <img src="<?php echo e(asset($settings['favicon_path'])); ?>" alt="Favicon" style="max-height: 32px; border-radius: 5px;">
                            <small class="d-block text-muted mt-1">Favicon الحالي</small>
                        </div>
                        <?php endif; ?>
                        <small class="text-muted">رفع Favicon جديد (ICO, PNG - حد أقصى 512KB)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEO Settings -->
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h4><i class="fas fa-search me-2"></i> إعدادات SEO</h4>
        </div>
        <div class="card-body-modern">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="site_description" class="form-label">وصف الموقع</label>
                        <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php echo e($settings['site_description'] ?? ''); ?></textarea>
                        <small class="text-muted">وصف الموقع لمحركات البحث</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="site_keywords" class="form-label">الكلمات المفتاحية</label>
                        <input type="text" class="form-control" id="site_keywords" name="site_keywords" 
                               value="<?php echo e($settings['site_keywords'] ?? ''); ?>" placeholder="مثال: أحياء, ثانوية عامة, دروس">
                        <small class="text-muted">الكلمات المفتاحية مفصولة بفواصل</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات الاتصال -->
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h4><i class="fas fa-phone me-2"></i> معلومات الاتصال</h4>
        </div>
        <div class="card-body-modern">
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="whatsapp_number" class="form-label">رقم الواتساب</label>
                        <input type="text" class="form-control" id="whatsapp_number" name="whatsapp_number" 
                               value="<?php echo e($settings['whatsapp_number'] ?? ''); ?>" placeholder="+201014506018">
                        <small class="text-muted">رقم الواتساب (مع رمز الدولة)</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">رقم الهاتف</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" 
                               value="<?php echo e($settings['phone_number'] ?? ''); ?>" placeholder="01014506018">
                        <small class="text-muted">رقم الهاتف للاتصال</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo e($settings['contact_email'] ?? ''); ?>" placeholder="info@example.com">
                        <small class="text-muted">البريد الإلكتروني للاتصال</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- وسائل التواصل الاجتماعي -->
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h4><i class="fas fa-share-alt me-2"></i> وسائل التواصل الاجتماعي</h4>
        </div>
        <div class="card-body-modern">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="facebook_url" class="form-label"><i class="fab fa-facebook me-2"></i> Facebook</label>
                        <input type="url" class="form-control" id="facebook_url" name="facebook_url" 
                               value="<?php echo e($settings['facebook_url'] ?? ''); ?>" placeholder="https://facebook.com/username">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="twitter_url" class="form-label"><i class="fab fa-twitter me-2"></i> Twitter</label>
                        <input type="url" class="form-control" id="twitter_url" name="twitter_url" 
                               value="<?php echo e($settings['twitter_url'] ?? ''); ?>" placeholder="https://twitter.com/username">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="instagram_url" class="form-label"><i class="fab fa-instagram me-2"></i> Instagram</label>
                        <input type="url" class="form-control" id="instagram_url" name="instagram_url" 
                               value="<?php echo e($settings['instagram_url'] ?? ''); ?>" placeholder="https://instagram.com/username">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="youtube_url" class="form-label"><i class="fab fa-youtube me-2"></i> YouTube</label>
                        <input type="url" class="form-control" id="youtube_url" name="youtube_url" 
                               value="<?php echo e($settings['youtube_url'] ?? ''); ?>" placeholder="https://youtube.com/channel/...">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="linkedin_url" class="form-label"><i class="fab fa-linkedin me-2"></i> LinkedIn</label>
                        <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                               value="<?php echo e($settings['linkedin_url'] ?? ''); ?>" placeholder="https://linkedin.com/in/username">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- نصوص الصفحة الرئيسية -->
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h4><i class="fas fa-home me-2"></i> نصوص الصفحة الرئيسية</h4>
        </div>
        <div class="card-body-modern">
            <div class="mb-3">
                <label for="hero_quote" class="form-label">الاقتباس</label>
                <input type="text" class="form-control" id="hero_quote" name="hero_quote" 
                       value="<?php echo e($settings['hero_quote'] ?? ''); ?>" placeholder='" وما رميت إذ رميت ولكن الله رمى "'>
                <small class="text-muted">الاقتباس الذي يظهر في أعلى الصفحة الرئيسية</small>
            </div>
            <div class="mb-3">
                <label for="hero_title" class="form-label">العنوان الرئيسي</label>
                <input type="text" class="form-control" id="hero_title" name="hero_title" 
                       value="<?php echo e($settings['hero_title'] ?? ''); ?>" placeholder="الأحياء مع الأستاذ سامح صلاح">
                <small class="text-muted">العنوان الرئيسي في Hero Section</small>
            </div>
            <div class="mb-3">
                <label for="hero_subtitle" class="form-label">العنوان الفرعي</label>
                <input type="text" class="form-control" id="hero_subtitle" name="hero_subtitle" 
                       value="<?php echo e($settings['hero_subtitle'] ?? ''); ?>" placeholder="خبير تدريس مادة الأحياء">
                <small class="text-muted">العنوان الفرعي تحت العنوان الرئيسي</small>
            </div>
            <div class="mb-3">
                <label for="hero_additional_text" class="form-label">النص الإضافي</label>
                <textarea class="form-control" id="hero_additional_text" name="hero_additional_text" rows="3" 
                          placeholder="أهلاً بك في منصتنا – كل الشرح والامتحانات في مكان واحد"><?php echo e($settings['hero_additional_text'] ?? ''); ?></textarea>
                <small class="text-muted">نص إضافي يظهر تحت العنوان الفرعي في Hero Section</small>
            </div>
            
            <hr class="my-4">
            <h5 class="mb-3"><i class="fas fa-mouse-pointer me-2"></i> أزرار Call-to-Action</h5>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="cta_text_1" class="form-label">نص الزر الأول</label>
                        <input type="text" class="form-control" id="cta_text_1" name="cta_text_1" 
                               value="<?php echo e($settings['cta_text_1'] ?? 'جروب التيلجرام'); ?>" placeholder="جروب التيلجرام">
                        <small class="text-muted">النص الذي يظهر على الزر الأول</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="cta_link_1" class="form-label">رابط الزر الأول</label>
                        <input type="url" class="form-control" id="cta_link_1" name="cta_link_1" 
                               value="<?php echo e($settings['cta_link_1'] ?? ''); ?>" placeholder="https://t.me/...">
                        <small class="text-muted">الرابط الذي يفتح عند الضغط على الزر الأول</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="cta_text_2" class="form-label">نص الزر الثاني (اختياري)</label>
                        <input type="text" class="form-control" id="cta_text_2" name="cta_text_2" 
                               value="<?php echo e($settings['cta_text_2'] ?? 'تعرف على المزيد'); ?>" placeholder="تعرف على المزيد">
                        <small class="text-muted">النص الذي يظهر على الزر الثاني (سيختفي إذا كان الرابط فارغاً)</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="cta_link_2" class="form-label">رابط الزر الثاني (اختياري)</label>
                        <input type="url" class="form-control" id="cta_link_2" name="cta_link_2" 
                               value="<?php echo e($settings['cta_link_2'] ?? ''); ?>" placeholder="https://...">
                        <small class="text-muted">الرابط الذي يفتح عند الضغط على الزر الثاني (اتركه فارغاً لإخفاء الزر)</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="cta_text_3" class="form-label">نص الزر الثالث (اختياري)</label>
                        <input type="text" class="form-control" id="cta_text_3" name="cta_text_3" 
                               value="<?php echo e($settings['cta_text_3'] ?? 'تواصل معنا'); ?>" placeholder="تواصل معنا">
                        <small class="text-muted">النص الذي يظهر على الزر الثالث (سيختفي إذا كان الرابط فارغاً)</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="cta_link_3" class="form-label">رابط الزر الثالث (اختياري)</label>
                        <input type="url" class="form-control" id="cta_link_3" name="cta_link_3" 
                               value="<?php echo e($settings['cta_link_3'] ?? ''); ?>" placeholder="https://...">
                        <small class="text-muted">الرابط الذي يفتح عند الضغط على الزر الثالث (اتركه فارغاً لإخفاء الزر)</small>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- معلومات المدرس -->
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h4><i class="fas fa-user-tie me-2"></i> معلومات المدرس</h4>
        </div>
        <div class="card-body-modern">
            <div class="mb-3">
                <label for="teacher_bio" class="form-label">السيرة الذاتية</label>
                <textarea class="form-control" id="teacher_bio" name="teacher_bio" rows="4" 
                          placeholder="اكتب سيرة ذاتية مختصرة عن المدرس..."><?php echo e($settings['teacher_bio'] ?? ''); ?></textarea>
                <small class="text-muted">سيرة ذاتية مختصرة عن المدرس (اختياري)</small>
            </div>
        </div>
    </div>

    <!-- صفحة عن المدرس -->
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h4><i class="fas fa-user-graduate me-2"></i> صفحة "عن المدرس"</h4>
        </div>
        <div class="card-body-modern">
            <div class="mb-3">
                <label for="teacher_image" class="form-label">صورة المدرس</label>
                <input type="file" class="form-control" id="teacher_image" name="teacher_image" accept="image/*">
                <?php
                    $teacherImagePath = $settings['teacher_image'] ?? '';
                    if ($teacherImagePath && strpos($teacherImagePath, 'upload_files/') === false) {
                        $teacherImagePath = 'upload_files/' . basename($teacherImagePath);
                    }
                ?>
                <?php if($teacherImagePath && file_exists(public_path($teacherImagePath))): ?>
                <div class="mt-2">
                    <img src="<?php echo e(asset($teacherImagePath)); ?>" alt="صورة المدرس" style="max-height: 150px; border-radius: 10px; border: 2px solid var(--primary-color);">
                    <small class="d-block text-muted mt-1">الصورة الحالية</small>
                </div>
                <?php elseif(isset($settings['teacher_image']) && $settings['teacher_image']): ?>
                <div class="mt-2">
                    <img src="<?php echo e(asset($settings['teacher_image'])); ?>" alt="صورة المدرس" style="max-height: 150px; border-radius: 10px; border: 2px solid var(--primary-color);">
                    <small class="d-block text-muted mt-1">الصورة الحالية</small>
                </div>
                <?php endif; ?>
                <small class="text-muted">رفع صورة المدرس (PNG, JPG, GIF - حد أقصى 2MB)</small>
            </div>

            <div class="mb-3">
                <label for="about_teacher_bio" class="form-label">نبذة عن المدرس</label>
                <textarea class="form-control" id="about_teacher_bio" name="about_teacher_bio" rows="5" 
                          placeholder="مدرس لغة إنجليزية متخصص في تدريس الثانوية العامة بخبرة تمتد لأكثر من 15 عاماً..."><?php echo e($settings['about_teacher_bio'] ?? ''); ?></textarea>
                <small class="text-muted">النص الذي يظهر في قسم "نبذة عني" في صفحة عن المدرس</small>
            </div>

            <hr class="my-4">

            <h5 class="mb-3"><i class="fas fa-certificate me-2"></i> المؤهلات</h5>
            <div id="about-qualifications-container">
                <?php
                    $qualificationsData = $settings['about_teacher_qualifications'] ?? [];
                    if (is_string($qualificationsData)) {
                        $qualificationsData = json_decode($qualificationsData, true) ?? [];
                    }
                    if (!is_array($qualificationsData)) {
                        $qualificationsData = [];
                    }
                ?>
                <?php $__currentLoopData = $qualificationsData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $qual): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="about-qualification-item mb-3 p-3 border rounded">
                    <div class="row g-3">
                        <div class="col-md-10">
                            <label class="form-label">النص</label>
                            <input type="text" name="about_qualifications[<?php echo e($index); ?>][text]" 
                                   class="form-control" value="<?php echo e($qual['text'] ?? ''); ?>" 
                                   placeholder="مثال: بكالوريوس آداب قسم اللغة الإنجليزية" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">الأيقونة</label>
                            <input type="text" name="about_qualifications[<?php echo e($index); ?>][icon]" 
                                   class="form-control" value="<?php echo e($qual['icon'] ?? 'fa-check-circle'); ?>" 
                                   placeholder="fa-check-circle" required>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeAboutQualification(this)">
                        <i class="fas fa-trash"></i> حذف
                    </button>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <button type="button" class="btn btn-modern-secondary" onclick="addAboutQualification()">
                <i class="fas fa-plus me-2"></i> إضافة مؤهل
            </button>

            <hr class="my-4">

            <h5 class="mb-3"><i class="fas fa-chart-bar me-2"></i> الأرقام والإحصائيات</h5>
            <div id="about-stats-container">
                <?php
                    $statsData = $settings['about_teacher_stats'] ?? [];
                    if (is_string($statsData)) {
                        $statsData = json_decode($statsData, true) ?? [];
                    }
                    if (!is_array($statsData)) {
                        $statsData = [];
                    }
                ?>
                <?php $__currentLoopData = $statsData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="about-stat-item mb-3 p-3 border rounded">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">الرقم</label>
                            <input type="text" name="about_stats[<?php echo e($index); ?>][number]" 
                                   class="form-control" value="<?php echo e($stat['number'] ?? ''); ?>" 
                                   placeholder="مثال: 15+" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">التسمية</label>
                            <input type="text" name="about_stats[<?php echo e($index); ?>][label]" 
                                   class="form-control" value="<?php echo e($stat['label'] ?? ''); ?>" 
                                   placeholder="مثال: سنة خبرة" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-sm btn-danger w-100" onclick="removeAboutStat(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <button type="button" class="btn btn-modern-secondary" onclick="addAboutStat()">
                <i class="fas fa-plus me-2"></i> إضافة رقم
            </button>

            <hr class="my-4">

            <h5 class="mb-3"><i class="fas fa-chalkboard-teacher me-2"></i> أسلوب التدريس</h5>
            <div id="about-methods-container">
                <?php
                    $methodsData = $settings['about_teacher_methods'] ?? [];
                    if (is_string($methodsData)) {
                        $methodsData = json_decode($methodsData, true) ?? [];
                    }
                    if (!is_array($methodsData)) {
                        $methodsData = [];
                    }
                ?>
                <?php $__currentLoopData = $methodsData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="about-method-item mb-3 p-3 border rounded">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">العنوان</label>
                            <input type="text" name="about_methods[<?php echo e($index); ?>][title]" 
                                   class="form-control" value="<?php echo e($method['title'] ?? ''); ?>" 
                                   placeholder="مثال: شرح مبسط وواضح" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">الوصف</label>
                            <textarea name="about_methods[<?php echo e($index); ?>][description]" 
                                      class="form-control" rows="2" 
                                      placeholder="مثال: تبسيط القواعد والمفاهيم المعقدة..." required><?php echo e($method['description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeAboutMethod(this)">
                        <i class="fas fa-trash"></i> حذف
                    </button>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <button type="button" class="btn btn-modern-secondary" onclick="addAboutMethod()">
                <i class="fas fa-plus me-2"></i> إضافة طريقة تدريس
            </button>

            <hr class="my-4">

            <h5 class="mb-3"><i class="fas fa-comments me-2"></i> آراء الطلاب</h5>
            <div id="about-reviews-container">
                <?php
                    $reviewsData = $settings['about_teacher_reviews'] ?? [];
                    if (is_string($reviewsData)) {
                        $reviewsData = json_decode($reviewsData, true) ?? [];
                    }
                    if (!is_array($reviewsData)) {
                        $reviewsData = [];
                    }
                ?>
                <?php $__currentLoopData = $reviewsData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="about-review-item mb-3 p-3 border rounded">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">نص الرأي</label>
                            <textarea name="about_reviews[<?php echo e($index); ?>][text]" 
                                      class="form-control" rows="2" 
                                      placeholder="مثال: شرح مستر حماده أكتر من رائع!..." required><?php echo e($review['text'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">اسم الطالب</label>
                            <input type="text" name="about_reviews[<?php echo e($index); ?>][student_name]" 
                                   class="form-control" value="<?php echo e($review['student_name'] ?? ''); ?>" 
                                   placeholder="مثال: أحمد محمد" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">الصف</label>
                            <input type="text" name="about_reviews[<?php echo e($index); ?>][grade]" 
                                   class="form-control" value="<?php echo e($review['grade'] ?? ''); ?>" 
                                   placeholder="مثال: الصف الثالث الثانوي" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">الحرف الأول</label>
                            <input type="text" name="about_reviews[<?php echo e($index); ?>][initial]" 
                                   class="form-control" value="<?php echo e($review['initial'] ?? ''); ?>" 
                                   placeholder="مثال: أ" maxlength="1">
                            <small class="text-muted">سيتم توليده تلقائياً إذا تركت فارغاً</small>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-sm btn-danger w-100" onclick="removeAboutReview(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <button type="button" class="btn btn-modern-secondary" onclick="addAboutReview()">
                <i class="fas fa-plus me-2"></i> إضافة رأي طالب
            </button>
                        
            <hr class="my-4">

            <h5 class="mb-3"><i class="fas fa-trophy me-2"></i> إنجازات المدرس</h5>
            <div id="about-achievements-container">
                <?php
                    $achievementsData = $settings['about_teacher_achievements'] ?? [];
                    if (is_string($achievementsData)) {
                        $achievementsData = json_decode($achievementsData, true) ?? [];
                    }
                    if (!is_array($achievementsData)) {
                        $achievementsData = [];
                    }
                ?>
                <?php $__currentLoopData = $achievementsData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $ach): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="about-achievement-item mb-3 p-3 border rounded">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-3 text-center">
                            <?php $imgPath = $ach['image'] ?? ''; ?>
                            <?php if($imgPath): ?>
                                <img src="<?php echo e(asset($imgPath)); ?>" alt="achievement" style="max-height:100px; border-radius:8px;">
                            <?php else: ?>
                                <div style="width:100px; height:70px; background:#f1f3ff; border-radius:8px; display:inline-block;"></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">التعليق</label>
                            <input type="text" name="about_achievements[<?php echo e($index); ?>][caption]" class="form-control" value="<?php echo e($ach['caption'] ?? ''); ?>" placeholder="مثال: فاز بجائزة أفضل مدرس 2023">
                            <input type="hidden" name="about_achievements[<?php echo e($index); ?>][existing_image]" value="<?php echo e($ach['image'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">صورة (اختياري)</label>
                            <input type="file" name="about_achievements_images[<?php echo e($index); ?>]" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-sm btn-danger w-100" onclick="removeAboutAchievement(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <button type="button" class="btn btn-modern-secondary" onclick="addAboutAchievement()">
                <i class="fas fa-plus me-2"></i> إضافة إنجاز
            </button>

        </div>
    </div>

    <!-- نصوص Footer -->
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h4><i class="fas fa-file-alt me-2"></i> نصوص Footer</h4>
        </div>
        <div class="card-body-modern">
            <div class="mb-3">
                <label for="footer_text" class="form-label">نص Footer</label>
                <textarea class="form-control" id="footer_text" name="footer_text" rows="3" 
                          placeholder="منصة تعليمية متخصصة في مادة الأحياء..."><?php echo e($settings['footer_text'] ?? ''); ?></textarea>
                <small class="text-muted">النص الذي يظهر في Footer</small>
            </div>
            <div class="mb-3">
                <label for="footer_copyright" class="form-label">نص حقوق النشر</label>
                <input type="text" class="form-control" id="footer_copyright" name="footer_copyright" 
                       value="<?php echo e($settings['footer_copyright'] ?? ''); ?>" placeholder="جميع الحقوق محفوظة © <?php echo e(date('Y')); ?>">
                <small class="text-muted">نص حقوق النشر (سيتم إضافة السنة تلقائياً)</small>
            </div>
            
            <hr class="my-4">
            
            <h5 class="mb-3"><i class="fas fa-code me-2"></i> برمجة وتطوير</h5>
            
            <div class="mb-3">
                <label for="developer_text" class="form-label">نص برمجة وتطوير</label>
                <input type="text" class="form-control" id="developer_text" name="developer_text" 
                       value="<?php echo e($settings['developer_text'] ?? 'برمجة وتطوير بواسطة'); ?>" placeholder="برمجة وتطوير بواسطة">
                <small class="text-muted">النص الذي يظهر قبل رابط المطور</small>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="developer_facebook" class="form-label"><i class="fab fa-facebook me-2"></i> رابط الفيسبوك للمطور</label>
                    <input type="url" class="form-control" id="developer_facebook" name="developer_facebook" 
                           value="<?php echo e($settings['developer_facebook'] ?? ''); ?>" placeholder="https://facebook.com/...">
                    <small class="text-muted">رابط الفيسبوك (اختياري)</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="developer_whatsapp" class="form-label"><i class="fab fa-whatsapp me-2"></i> رقم الواتساب للمطور</label>
                    <input type="text" class="form-control" id="developer_whatsapp" name="developer_whatsapp" 
                           value="<?php echo e($settings['developer_whatsapp'] ?? ''); ?>" placeholder="01234567890">
                    <small class="text-muted">رقم الواتساب (اختياري)</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Section -->
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h4><i class="fas fa-eye me-2"></i> معاينة الألوان</h4>
        </div>
        <div class="card-body-modern">
            <div class="row">
                <div class="col-md-6">
                    <div class="preview-box" style="background: linear-gradient(135deg, <?php echo e($settings['primary_color'] ?? '#7424a9'); ?>, <?php echo e($settings['secondary_color'] ?? '#fa896b'); ?>); padding: 30px; border-radius: 15px; color: white; text-align: center;">
                        <h3 style="color: white; margin-bottom: 10px;"><?php echo e($settings['site_name'] ?? 'اسم المنصة'); ?></h3>
                        <p style="color: white; margin: 0;"><?php echo e($settings['teacher_name'] ?? 'اسم المدرس'); ?> - <?php echo e($settings['subject_name'] ?? 'المادة'); ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="preview-box" style="border: 2px solid <?php echo e($settings['primary_color'] ?? '#7424a9'); ?>; padding: 30px; border-radius: 15px;">
                        <h4 style="color: <?php echo e($settings['primary_color'] ?? '#7424a9'); ?>;">عنوان باللون الأساسي</h4>
                        <p style="color: <?php echo e($settings['secondary_color'] ?? '#fa896b'); ?>;">نص باللون الثانوي</p>
                        <button type="button" class="btn" style="background: <?php echo e($settings['primary_color'] ?? '#7424a9'); ?>; color: white; border-radius: 10px; padding: 10px 20px;">
                            زر تجريبي
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات Hero Section -->
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h4><i class="fas fa-chart-bar me-2"></i> إحصائيات الصفحة الرئيسية</h4>
        </div>
        <div class="card-body-modern">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <input type="checkbox" name="stat_experience_enabled" id="stat_experience_enabled" 
                               <?php echo e(isset($settings['hero_stats'][0]['enabled']) && $settings['hero_stats'][0]['enabled'] ? 'checked' : ''); ?> class="me-2">
                        <label for="stat_experience_enabled" class="form-label mb-0"><strong>سنة الخبرة</strong></label>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <input type="text" name="stat_experience" class="form-control" 
                                   value="<?php echo e($settings['hero_stats'][0]['number'] ?? '15'); ?>" placeholder="15">
                        </div>
                        <div class="col-4">
                            <input type="text" name="stat_experience_label" class="form-control" 
                                   value="<?php echo e($settings['hero_stats'][0]['label'] ?? 'سنة خبرة'); ?>" placeholder="سنة خبرة">
                        </div>
                        <div class="col-4">
                            <input type="text" name="stat_experience_icon" class="form-control" 
                                   value="<?php echo e($settings['hero_stats'][0]['icon'] ?? 'fa-calendar-check'); ?>" placeholder="fa-calendar-check">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <input type="checkbox" name="stat_success_rate_enabled" id="stat_success_rate_enabled" 
                               <?php echo e(isset($settings['hero_stats'][1]['enabled']) && $settings['hero_stats'][1]['enabled'] ? 'checked' : ''); ?> class="me-2">
                        <label for="stat_success_rate_enabled" class="form-label mb-0"><strong>نسبة النجاح</strong></label>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <input type="text" name="stat_success_rate" class="form-control" 
                                   value="<?php echo e($settings['hero_stats'][1]['number'] ?? '95'); ?>" placeholder="95">
                        </div>
                        <div class="col-4">
                            <input type="text" name="stat_success_rate_label" class="form-control" 
                                   value="<?php echo e($settings['hero_stats'][1]['label'] ?? 'نسبة النجاح %'); ?>" placeholder="نسبة النجاح %">
                        </div>
                        <div class="col-4">
                            <input type="text" name="stat_success_rate_icon" class="form-control" 
                                   value="<?php echo e($settings['hero_stats'][1]['icon'] ?? 'fa-trophy'); ?>" placeholder="fa-trophy">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <input type="checkbox" name="stat_students_enabled" id="stat_students_enabled" 
                               <?php echo e(isset($settings['hero_stats'][2]['enabled']) && $settings['hero_stats'][2]['enabled'] ? 'checked' : ''); ?> class="me-2">
                        <label for="stat_students_enabled" class="form-label mb-0"><strong>عدد الطلاب</strong></label>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <input type="text" name="stat_students" class="form-control" 
                                   value="<?php echo e($settings['hero_stats'][2]['number'] ?? '5000'); ?>" placeholder="5000">
                        </div>
                        <div class="col-4">
                            <input type="text" name="stat_students_label" class="form-control" 
                                   value="<?php echo e($settings['hero_stats'][2]['label'] ?? 'طالب'); ?>" placeholder="طالب">
                        </div>
                        <div class="col-4">
                            <input type="text" name="stat_students_icon" class="form-control" 
                                   value="<?php echo e($settings['hero_stats'][2]['icon'] ?? 'fa-users'); ?>" placeholder="fa-users">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <input type="checkbox" name="stat_rating_enabled" id="stat_rating_enabled" 
                               <?php echo e(isset($settings['hero_stats'][3]['enabled']) && $settings['hero_stats'][3]['enabled'] ? 'checked' : ''); ?> class="me-2">
                        <label for="stat_rating_enabled" class="form-label mb-0"><strong>التقييم</strong></label>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <input type="text" name="stat_rating" class="form-control" 
                                   value="<?php echo e($settings['hero_stats'][3]['number'] ?? '4.9'); ?>" placeholder="4.9">
                        </div>
                        <div class="col-4">
                            <input type="text" name="stat_rating_label" class="form-control" 
                                   value="<?php echo e($settings['hero_stats'][3]['label'] ?? 'تقييم'); ?>" placeholder="تقييم">
                        </div>
                        <div class="col-4">
                            <input type="text" name="stat_rating_icon" class="form-control" 
                                   value="<?php echo e($settings['hero_stats'][3]['icon'] ?? 'fa-star'); ?>" placeholder="fa-star">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- مميزات "ماذا تقدم منصتنا" -->
    <div class="modern-card mb-4">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h4><i class="fas fa-star me-2"></i> مميزات "ماذا تقدم منصتنا"</h4>
            <button type="button" class="btn btn-sm btn-modern-primary" onclick="addFeature()">
                <i class="fas fa-plus me-1"></i> إضافة مميزة
            </button>
        </div>
        <div class="card-body-modern">
            <?php
                $currentFeatures = $settings['features_list'] ?? [];
            ?>
            <?php if(count($currentFeatures) > 0): ?>
            <div class="alert alert-info mb-3">
                <strong><i class="fas fa-info-circle me-2"></i>القيم الحالية:</strong>
                <ul class="mb-0 mt-2">
                    <?php $__currentLoopData = $currentFeatures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <strong><?php echo e($feat['title'] ?? 'بدون عنوان'); ?></strong>
                        <?php if(isset($feat['description']) && !empty($feat['description'])): ?>
                        - <?php echo e(Str::limit($feat['description'], 50)); ?>

                        <?php endif; ?>
                        <span class="badge bg-<?php echo e(isset($feat['enabled']) && $feat['enabled'] ? 'success' : 'secondary'); ?> ms-2">
                            <?php echo e(isset($feat['enabled']) && $feat['enabled'] ? 'مفعّل' : 'معطّل'); ?>

                        </span>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>
            <div id="features-container">
                <?php
                    $defaultFeatures = [
                        ['title' => 'محاضرات', 'description' => 'محاضرات فيديو عالية الجودة تغطي جميع أجزاء المنهج', 'icon' => 'fa-video'],
                        ['title' => 'المذكرات', 'description' => 'مذكرات شاملة ومنظمة يمكنك تحميلها والاستفادة منها', 'icon' => 'fa-file-pdf'],
                        ['title' => 'الامتحانات', 'description' => 'امتحانات تفاعلية لتقييم مستواك والتحضير للامتحانات', 'icon' => 'fa-clipboard-check'],
                        ['title' => 'بنك الأسئلة', 'description' => 'مجموعة ضخمة من الأسئلة والتدريبات المتنوعة', 'icon' => 'fa-database'],
                    ];
                    $features = $settings['features_list'] ?? $defaultFeatures;
                ?>
                <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="feature-item mb-3 p-3 border rounded">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="form-check">
                            <input type="checkbox" name="features[<?php echo e($index); ?>][enabled]" class="form-check-input" 
                                   <?php echo e(isset($feature['enabled']) && $feature['enabled'] ? 'checked' : 'checked'); ?> id="feature_enabled_<?php echo e($index); ?>">
                            <label class="form-check-label" for="feature_enabled_<?php echo e($index); ?>">مفعّل</label>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeFeature(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <input type="text" name="features[<?php echo e($index); ?>][title]" class="form-control" 
                                   value="<?php echo e($feature['title'] ?? ''); ?>" placeholder="العنوان" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="text" name="features[<?php echo e($index); ?>][icon]" class="form-control" 
                                   value="<?php echo e($feature['icon'] ?? 'fa-check'); ?>" placeholder="fa-video">
                        </div>
                        <div class="col-md-12 mb-2">
                            <textarea name="features[<?php echo e($index); ?>][description]" class="form-control" rows="2" 
                                      placeholder="الوصف"><?php echo e($feature['description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <!-- مفاهيم المادة (الرموز في Hero Section) -->
    <div class="modern-card mb-4">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h4><i class="fas fa-icons me-2"></i> مفاهيم المادة (رموز Hero Section)</h4>
            <button type="button" class="btn btn-sm btn-modern-primary" onclick="addConcept()">
                <i class="fas fa-plus me-1"></i> إضافة مفهوم
            </button>
        </div>
        <div class="card-body-modern">
            <p class="text-muted mb-3">
                <i class="fas fa-info-circle me-2"></i>
                هذه المفاهيم تظهر في الصفحة الرئيسية تحت صورة Hero Section (مثل: الخلية، الوراثة، التنفس، الجهاز الهضمي، الجهاز الدوري، النبات للأحياء)
            </p>
            <?php
                $currentConcepts = $settings['subject_concepts'] ?? [];
            ?>
            <?php if(count($currentConcepts) > 0): ?>
            <div class="alert alert-info mb-3">
                <strong><i class="fas fa-info-circle me-2"></i>القيم الحالية:</strong>
                <ul class="mb-0 mt-2">
                    <?php $__currentLoopData = $currentConcepts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $concept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <i class="fas <?php echo e($concept['icon'] ?? 'fa-circle'); ?> me-2"></i>
                        <strong><?php echo e($concept['name'] ?? 'بدون اسم'); ?></strong>
                        <span class="badge bg-<?php echo e(isset($concept['enabled']) && $concept['enabled'] ? 'success' : 'secondary'); ?> ms-2">
                            <?php echo e(isset($concept['enabled']) && $concept['enabled'] ? 'مفعّل' : 'معطّل'); ?>

                        </span>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>
            <div id="concepts-container">
                <?php
                    $defaultConcepts = [
                        ['name' => 'الضوء', 'icon' => 'fa-lightbulb', 'enabled' => true],
                        ['name' => 'الطاقة', 'icon' => 'fa-fire', 'enabled' => true],
                        ['name' => 'القوة', 'icon' => 'fa-weight', 'enabled' => true],
                        ['name' => 'الحركة', 'icon' => 'fa-rocket', 'enabled' => true],
                        ['name' => 'الحرارة', 'icon' => 'fa-thermometer-half', 'enabled' => true],
                        ['name' => 'الفضاء', 'icon' => 'fa-satellite', 'enabled' => true],
                    ];
                    $concepts = $settings['subject_concepts'] ?? $defaultConcepts;
                ?>
                <?php $__currentLoopData = $concepts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $concept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="concept-item mb-3 p-3 border rounded">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="form-check">
                            <input type="checkbox" name="concepts[<?php echo e($index); ?>][enabled]" class="form-check-input" 
                                   <?php echo e(isset($concept['enabled']) && $concept['enabled'] ? 'checked' : 'checked'); ?> id="concept_enabled_<?php echo e($index); ?>">
                            <label class="form-check-label" for="concept_enabled_<?php echo e($index); ?>">مفعّل</label>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeConcept(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">اسم المفهوم</label>
                            <input type="text" name="concepts[<?php echo e($index); ?>][name]" class="form-control" 
                                   value="<?php echo e($concept['name'] ?? ''); ?>" placeholder="مثال: الضوء" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">الأيقونة (Font Awesome)</label>
                            <input type="text" name="concepts[<?php echo e($index); ?>][icon]" class="form-control" 
                                   value="<?php echo e($concept['icon'] ?? 'fa-circle'); ?>" placeholder="fa-lightbulb">
                            <small class="text-muted">مثال: fa-lightbulb, fa-fire, fa-weight, fa-rocket</small>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <!-- مواضيع الأحياء -->
    <div class="modern-card mb-4">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h4><i class="fas fa-atom me-2"></i> مواضيع المادة</h4>
            <button type="button" class="btn btn-sm btn-modern-primary" onclick="addTopic()">
                <i class="fas fa-plus me-1"></i> إضافة موضوع
            </button>
        </div>
        <div class="card-body-modern">
            <?php
                $currentTopics = $settings['topics_list'] ?? [];
            ?>
            <?php if(count($currentTopics) > 0): ?>
            <div class="alert alert-info mb-3">
                <strong><i class="fas fa-info-circle me-2"></i>القيم الحالية:</strong>
                <ul class="mb-0 mt-2">
                    <?php $__currentLoopData = $currentTopics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <strong><?php echo e($topic['name'] ?? 'بدون اسم'); ?></strong>
                        <span class="badge bg-<?php echo e(isset($topic['enabled']) && $topic['enabled'] ? 'success' : 'secondary'); ?> ms-2">
                            <?php echo e(isset($topic['enabled']) && $topic['enabled'] ? 'مفعّل' : 'معطّل'); ?>

                        </span>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>
            <div id="topics-container">
                <?php
                    $defaultTopics = [
                        ['name' => 'الخلية', 'icon' => 'fa-microscope'],
                        ['name' => 'الوراثة', 'icon' => 'fa-dna'],
                        ['name' => 'التنفس', 'icon' => 'fa-lungs'],
                        ['name' => 'الجهاز الهضمي', 'icon' => 'fa-stomach'],
                        ['name' => 'الجهاز الدوري', 'icon' => 'fa-heartbeat'],
                        ['name' => 'النبات', 'icon' => 'fa-leaf'],
                    ];
                    $topics = $settings['topics_list'] ?? $defaultTopics;
                ?>
                <?php $__currentLoopData = $topics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="topic-item mb-3 p-3 border rounded">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="form-check">
                            <input type="checkbox" name="topics[<?php echo e($index); ?>][enabled]" class="form-check-input" 
                                   <?php echo e(isset($topic['enabled']) && $topic['enabled'] ? 'checked' : 'checked'); ?> id="topic_enabled_<?php echo e($index); ?>">
                            <label class="form-check-label" for="topic_enabled_<?php echo e($index); ?>">مفعّل</label>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeTopic(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <input type="text" name="topics[<?php echo e($index); ?>][name]" class="form-control" 
                                   value="<?php echo e($topic['name'] ?? ''); ?>" placeholder="اسم الموضوع" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <input type="text" name="topics[<?php echo e($index); ?>][icon]" class="form-control" 
                                   value="<?php echo e($topic['icon'] ?? 'fa-atom'); ?>" placeholder="fa-atom">
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <!-- السنوات الدراسية -->
    <div class="modern-card mb-4">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h4><i class="fas fa-graduation-cap me-2"></i> السنوات الدراسية</h4>
            <button type="button" class="btn btn-sm btn-modern-primary" onclick="addGrade()">
                <i class="fas fa-plus me-1"></i> إضافة سنة
            </button>
        </div>
        <div class="card-body-modern">
            <?php
                $currentGrades = $settings['grades_list'] ?? [];
            ?>
            <?php if(count($currentGrades) > 0): ?>
            <div class="alert alert-info mb-3">
                <strong><i class="fas fa-info-circle me-2"></i>القيم الحالية:</strong>
                <ul class="mb-0 mt-2">
                    <?php $__currentLoopData = $currentGrades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <strong><?php echo e($grade['name'] ?? 'بدون اسم'); ?></strong>
                        <span class="badge bg-<?php echo e(isset($grade['enabled']) && $grade['enabled'] ? 'success' : 'secondary'); ?> ms-2">
                            <?php echo e(isset($grade['enabled']) && $grade['enabled'] ? 'مفعّل' : 'معطّل'); ?>

                        </span>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>
            <div id="grades-container">
                <?php
                    $defaultGrades = [
                        ['name' => 'الصف الأول الثانوي', 'icon' => 'fa-atom'],
                        ['name' => 'الصف الثاني الثانوي', 'icon' => 'fa-bolt'],
                        ['name' => 'الصف الثالث الثانوي', 'icon' => 'fa-rocket'],
                    ];
                    $grades = $settings['grades_list'] ?? $defaultGrades;
                ?>
                <?php $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="grade-item mb-3 p-3 border rounded">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="form-check">
                            <input type="checkbox" name="grades[<?php echo e($index); ?>][enabled]" class="form-check-input" 
                                   <?php echo e(isset($grade['enabled']) && $grade['enabled'] ? 'checked' : 'checked'); ?> id="grade_enabled_<?php echo e($index); ?>">
                            <label class="form-check-label" for="grade_enabled_<?php echo e($index); ?>">مفعّل</label>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeGrade(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <input type="text" name="grades[<?php echo e($index); ?>][name]" class="form-control" 
                                   value="<?php echo e($grade['name'] ?? ''); ?>" placeholder="اسم السنة الدراسية" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <input type="text" name="grades[<?php echo e($index); ?>][icon]" class="form-control" 
                                   value="<?php echo e($grade['icon'] ?? 'fa-graduation-cap'); ?>" placeholder="fa-graduation-cap">
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <!-- مميزات "لماذا تشترك" -->
    <div class="modern-card mb-4">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h4><i class="fas fa-heart me-2"></i> مميزات "لماذا تشترك معنا"</h4>
            <button type="button" class="btn btn-sm btn-modern-primary" onclick="addBenefit()">
                <i class="fas fa-plus me-1"></i> إضافة مميزة
            </button>
        </div>
        <div class="card-body-modern">
            <?php
                $currentBenefits = $settings['benefits_list'] ?? [];
            ?>
            <?php if(count($currentBenefits) > 0): ?>
            <div class="alert alert-info mb-3">
                <strong><i class="fas fa-info-circle me-2"></i>القيم الحالية (<?php echo e(count($currentBenefits)); ?> مميزة):</strong>
                <ul class="mb-0 mt-2">
                    <?php $__currentLoopData = $currentBenefits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $benefit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <strong><?php echo e($benefit['title'] ?? 'بدون عنوان'); ?></strong>
                        <?php if(isset($benefit['description']) && !empty($benefit['description'])): ?>
                        - <?php echo e(Str::limit($benefit['description'], 50)); ?>

                        <?php endif; ?>
                        <span class="badge bg-<?php echo e(isset($benefit['enabled']) && $benefit['enabled'] ? 'success' : 'secondary'); ?> ms-2">
                            <?php echo e(isset($benefit['enabled']) && $benefit['enabled'] ? 'مفعّل' : 'معطّل'); ?>

                        </span>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php else: ?>
            <div class="alert alert-warning mb-3">
                <strong><i class="fas fa-exclamation-triangle me-2"></i>لا توجد مميزات حالياً. سيتم استخدام القيم الافتراضية.</strong>
            </div>
            <?php endif; ?>
            <div id="benefits-container">
                <?php
                    $defaultBenefits = [
                        ['title' => 'شرح بسيط ومفهوم', 'description' => 'محتوى تعليمي واضح وسهل الفهم، مصمم خصيصاً لتبسيط المفاهيم المعقدة', 'icon' => 'fa-book-open'],
                        ['title' => 'فيديوهات برسومات توضيحية', 'description' => 'محاضرات فيديو عالية الجودة مع رسومات وتوضيحات تفاعلية', 'icon' => 'fa-video'],
                        ['title' => 'تمارين تفاعلية على الدروس', 'description' => 'تدريبات وتمارين تفاعلية بعد كل درس لتعزيز الفهم', 'icon' => 'fa-tasks'],
                        ['title' => 'مرونة كاملة في المذاكرة', 'description' => 'ادرس في أي وقت ومن أي مكان، المحتوى متاح 24/7', 'icon' => 'fa-clock'],
                        ['title' => 'اختبارات مستمرة', 'description' => 'امتحانات دورية لتقييم مستواك ومتابعة تقدمك الدراسي', 'icon' => 'fa-clipboard-check'],
                        ['title' => 'محتوى متكامل ومنظم', 'description' => 'مناهج منظمة بشكل منطقي ومتسلسل تغطي جميع أجزاء المادة', 'icon' => 'fa-layer-group'],
                        ['title' => 'تحديث مستمر حسب المنهج', 'description' => 'محتوى محدث باستمرار ليتوافق مع آخر التعديلات في المنهج', 'icon' => 'fa-sync-alt'],
                        ['title' => 'مجتمع طلابي ضخم', 'description' => 'انضم إلى آلاف الطلاب الناجحين في رحلتهم التعليمية', 'icon' => 'fa-users'],
                    ];
                    $benefits = $settings['benefits_list'] ?? $defaultBenefits;
                ?>
                <?php $__currentLoopData = $benefits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $benefit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="benefit-item mb-3 p-3 border rounded">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="form-check">
                            <input type="checkbox" name="benefits[<?php echo e($index); ?>][enabled]" class="form-check-input" 
                                   <?php echo e(isset($benefit['enabled']) && $benefit['enabled'] ? 'checked' : 'checked'); ?> id="benefit_enabled_<?php echo e($index); ?>">
                            <label class="form-check-label" for="benefit_enabled_<?php echo e($index); ?>">مفعّل</label>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeBenefit(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <input type="text" name="benefits[<?php echo e($index); ?>][title]" class="form-control" 
                                   value="<?php echo e($benefit['title'] ?? ''); ?>" placeholder="العنوان" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="text" name="benefits[<?php echo e($index); ?>][icon]" class="form-control" 
                                   value="<?php echo e($benefit['icon'] ?? 'fa-check'); ?>" placeholder="fa-book-open">
                        </div>
                        <div class="col-md-12 mb-2">
                            <textarea name="benefits[<?php echo e($index); ?>][description]" class="form-control" rows="2" 
                                      placeholder="الوصف"><?php echo e($benefit['description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <!-- الصفوف الدراسية في صفحة التسجيل -->
    <div class="modern-card mb-4">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h4><i class="fas fa-user-graduate me-2"></i> الصفوف الدراسية (صفحة التسجيل)</h4>
            <button type="button" class="btn btn-sm btn-modern-primary" onclick="addSignupGrade()">
                <i class="fas fa-plus me-1"></i> إضافة صف
            </button>
        </div>
        <div class="card-body-modern">
            <?php
                $currentSignupGrades = $settings['signup_grades'] ?? [];
            ?>
            <?php if(count($currentSignupGrades) > 0): ?>
            <div class="alert alert-info mb-3">
                <strong><i class="fas fa-info-circle me-2"></i>القيم الحالية:</strong>
                <ul class="mb-0 mt-2">
                    <?php $__currentLoopData = $currentSignupGrades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <strong><?php echo e($grade['label'] ?? 'بدون تسمية'); ?></strong> 
                        <span class="text-muted">(<?php echo e($grade['value'] ?? 'بدون قيمة'); ?>)</span>
                        <span class="badge bg-<?php echo e(isset($grade['enabled']) && $grade['enabled'] ? 'success' : 'secondary'); ?> ms-2">
                            <?php echo e(isset($grade['enabled']) && $grade['enabled'] ? 'مفعّل' : 'معطّل'); ?>

                        </span>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>
            <div id="signup-grades-container">
                <?php
                    $defaultSignupGrades = [
                        ['value' => 'أولي', 'label' => 'الصف الأول الثانوي'],
                        ['value' => 'تانية', 'label' => 'الصف الثاني الثانوي'],
                        ['value' => 'ثالثة', 'label' => 'الصف الثالث الثانوي'],
                    ];
                    $signupGrades = $settings['signup_grades'] ?? $defaultSignupGrades;
                ?>
                <?php $__currentLoopData = $signupGrades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="signup-grade-item mb-3 p-3 border rounded">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="form-check">
                            <input type="checkbox" name="signup_grades[<?php echo e($index); ?>][enabled]" class="form-check-input" 
                                   <?php echo e(isset($grade['enabled']) && $grade['enabled'] ? 'checked' : 'checked'); ?> id="signup_grade_enabled_<?php echo e($index); ?>">
                            <label class="form-check-label" for="signup_grade_enabled_<?php echo e($index); ?>">مفعّل</label>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeSignupGrade(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <input type="text" name="signup_grades[<?php echo e($index); ?>][value]" class="form-control" 
                                   value="<?php echo e($grade['value'] ?? ''); ?>" placeholder="القيمة (أولي، تانية، إلخ)" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <input type="text" name="signup_grades[<?php echo e($index); ?>][label]" class="form-control" 
                                   value="<?php echo e($grade['label'] ?? ''); ?>" placeholder="التسمية (الصف الأول الثانوي)" required>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <!-- وسائل الدفع والتفعيل -->
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h4><i class="fas fa-money-bill-wave me-2"></i> وسائل الدفع والتفعيل</h4>
        </div>
        <div class="card-body-modern">
            <p class="text-muted mb-4">حدد وسائل الدفع والتفعيل المتاحة للكورسات</p>
            
            <?php
                $currentPaymentMethods = $settings['payment_methods'] ?? [
                    'online_payment' => true,
                    'activation_codes' => true,
                    'admin_activation' => true,
                    'free_courses' => true,
                ];
            ?>
            
            <?php if(!empty($currentPaymentMethods)): ?>
            <div class="alert alert-info mb-3">
                <strong><i class="fas fa-info-circle me-2"></i>الإعدادات الحالية:</strong>
                <ul class="mb-0 mt-2">
                    <li>
                        <strong>الدفع الأونلاين:</strong> 
                        <span class="badge bg-<?php echo e($currentPaymentMethods['online_payment'] ?? false ? 'success' : 'secondary'); ?>">
                            <?php echo e($currentPaymentMethods['online_payment'] ?? false ? 'مفعّل' : 'معطّل'); ?>

                        </span>
                    </li>
                    <li>
                        <strong>أكواد التفعيل:</strong> 
                        <span class="badge bg-<?php echo e($currentPaymentMethods['activation_codes'] ?? false ? 'success' : 'secondary'); ?>">
                            <?php echo e($currentPaymentMethods['activation_codes'] ?? false ? 'مفعّل' : 'معطّل'); ?>

                        </span>
                    </li>
                    <li>
                        <strong>تفعيل من الأدمن:</strong> 
                        <span class="badge bg-<?php echo e($currentPaymentMethods['admin_activation'] ?? false ? 'success' : 'secondary'); ?>">
                            <?php echo e($currentPaymentMethods['admin_activation'] ?? false ? 'مفعّل' : 'معطّل'); ?>

                        </span>
                    </li>
                    <li>
                        <strong>الكورسات المجانية:</strong> 
                        <span class="badge bg-<?php echo e($currentPaymentMethods['free_courses'] ?? false ? 'success' : 'secondary'); ?>">
                            <?php echo e($currentPaymentMethods['free_courses'] ?? false ? 'مفعّل' : 'معطّل'); ?>

                        </span>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="payment_methods[online_payment]" 
                               id="online_payment" value="1" 
                               <?php echo e(($currentPaymentMethods['online_payment'] ?? true) ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="online_payment">
                            <strong><i class="fas fa-credit-card me-2"></i> الدفع الأونلاين</strong>
                            <small class="d-block text-muted">السماح للطلاب بالدفع أونلاين عبر Kashier</small>
                        </label>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="payment_methods[activation_codes]" 
                               id="activation_codes" value="1" 
                               <?php echo e(($currentPaymentMethods['activation_codes'] ?? true) ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="activation_codes">
                            <strong><i class="fas fa-key me-2"></i> أكواد التفعيل</strong>
                            <small class="d-block text-muted">السماح للطلاب بإدخال أكواد تفعيل</small>
                        </label>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="payment_methods[admin_activation]" 
                               id="admin_activation" value="1" 
                               <?php echo e(($currentPaymentMethods['admin_activation'] ?? true) ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="admin_activation">
                            <strong><i class="fas fa-user-shield me-2"></i> تفعيل من الأدمن</strong>
                            <small class="d-block text-muted">السماح للأدمن بتفعيل الكورسات يدوياً</small>
                        </label>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="payment_methods[free_courses]" 
                               id="free_courses" value="1" 
                               <?php echo e(($currentPaymentMethods['free_courses'] ?? true) ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="free_courses">
                            <strong><i class="fas fa-gift me-2"></i> الكورسات المجانية</strong>
                            <small class="d-block text-muted">السماح بإنشاء كورسات مجانية (سعر = 0)</small>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info mt-3">
                <strong><i class="fas fa-info-circle me-2"></i>ملاحظة:</strong>
                <ul class="mb-0 mt-2">
                    <li>يمكن تفعيل أكثر من وسيلة في نفس الوقت</li>
                    <li>إذا كان الكورس مجاني (سعر = 0)، سيظهر تلقائياً كمجاني بغض النظر عن هذه الإعدادات</li>
                    <li>إذا تم تعطيل جميع الوسائل، لن يتمكن الطلاب من الاشتراك في الكورسات المدفوعة</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- المحافظات -->
    <div class="modern-card mb-4">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h4><i class="fas fa-map-marker-alt me-2"></i> المحافظات</h4>
            <button type="button" class="btn btn-sm btn-modern-primary" onclick="addGovernorate()">
                <i class="fas fa-plus me-1"></i> إضافة محافظة
            </button>
        </div>
        <div class="card-body-modern">
            <?php
                $currentGovernorates = $settings['governorates_list'] ?? [];
            ?>
            <?php if(count($currentGovernorates) > 0): ?>
            <div class="alert alert-info mb-3">
                <strong><i class="fas fa-info-circle me-2"></i>القيم الحالية:</strong>
                <div class="mt-2">
                    <?php $__currentLoopData = $currentGovernorates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="badge bg-<?php echo e(isset($gov['enabled']) && $gov['enabled'] ? 'success' : 'secondary'); ?> me-2 mb-2" style="font-size: 14px; padding: 8px 12px;">
                        <?php echo e($gov['name'] ?? 'بدون اسم'); ?>

                        <i class="fas fa-<?php echo e(isset($gov['enabled']) && $gov['enabled'] ? 'check' : 'times'); ?> ms-1"></i>
                    </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>
            <div id="governorates-container">
                <?php
                    $defaultGovernorates = ['القاهرة', 'الجيزة', 'الإسكندرية', 'أسيوط', 'سوهاج', 'قنا', 'أسوان'];
                    $governorates = $settings['governorates_list'] ?? array_map(function($name) {
                        return ['name' => $name, 'enabled' => true];
                    }, $defaultGovernorates);
                ?>
                <?php $__currentLoopData = $governorates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $gov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="governorate-item mb-2 p-2 border rounded d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input type="checkbox" name="governorates[<?php echo e($index); ?>][enabled]" class="form-check-input" 
                               <?php echo e(isset($gov['enabled']) && $gov['enabled'] ? 'checked' : 'checked'); ?> id="gov_enabled_<?php echo e($index); ?>">
                        <input type="text" name="governorates[<?php echo e($index); ?>][name]" class="form-control d-inline-block ms-2" 
                               value="<?php echo e($gov['name'] ?? ''); ?>" placeholder="اسم المحافظة" style="width: 200px;" required>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeGovernorate(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <!-- إعدادات صفحة الأسئلة الشائعة -->
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h4><i class="fas fa-question-circle me-2"></i> إعدادات صفحة الأسئلة الشائعة</h4>
        </div>
        <div class="card-body-modern">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">العنوان الرئيسي</label>
                    <input type="text" name="faq_title" class="form-control" 
                           value="<?php echo e($settings['faq_title'] ?? 'هل لديك سؤال؟'); ?>" 
                           placeholder="هل لديك سؤال؟">
                    <small class="text-muted">العنوان الرئيسي في صفحة الأسئلة الشائعة</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">العنوان الفرعي</label>
                    <input type="text" name="faq_subtitle" class="form-control" 
                           value="<?php echo e($settings['faq_subtitle'] ?? 'إجابات على الأسئلة الأكثر شيوعاً'); ?>" 
                           placeholder="إجابات على الأسئلة الأكثر شيوعاً">
                    <small class="text-muted">العنوان الفرعي تحت العنوان الرئيسي</small>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">الأسئلة والإجابات</label>
                    <div id="faq-list-container">
                        <?php
                            $defaultFAQs = [
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
                            $faqData = $settings['faq_list'] ?? [];
                            if (is_string($faqData)) {
                            $faqs = json_decode($faqData, true);
                            if (!is_array($faqs)) {
                                $faqs = [];
                            }
                        } else {
                            $faqs = is_array($faqData) ? $faqData : [];
                        }
                        if (empty($faqs)) {
                            $faqs = $defaultFAQs;
                        }
                        ?>
                        <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="faq-item-settings mb-3 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="text-primary">سؤال <?php echo e($index + 1); ?></strong>
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeFAQItem(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">السؤال</label>
                                    <input type="text" name="faq_list[<?php echo e($index); ?>][question]" 
                                           class="form-control" value="<?php echo e($faq['question'] ?? ''); ?>" 
                                           placeholder="مثال: كيف يمكنني الاشتراك في الكورسات؟" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">الإجابة</label>
                                    <textarea name="faq_list[<?php echo e($index); ?>][answer]" 
                                              class="form-control" rows="3" 
                                              placeholder="مثال: يمكنك الاشتراك بسهولة عن طريق..." required><?php echo e($faq['answer'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <button type="button" class="btn btn-modern btn-modern-primary btn-sm" onclick="addFAQItem()">
                        <i class="fas fa-plus me-2"></i> إضافة سؤال جديد
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- إعدادات صفحة التسجيل -->
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h4><i class="fas fa-user-plus me-2"></i> إعدادات صفحة التسجيل</h4>
        </div>
        <div class="card-body-modern">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">عنوان الكارت</label>
                    <input type="text" name="signup_card_title" class="form-control" 
                           value="<?php echo e($settings['signup_card_title'] ?? 'انضم إلينا اليوم!'); ?>" 
                           placeholder="انضم إلينا اليوم!">
                    <small class="text-muted">العنوان الرئيسي في كارت المميزات</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">العنوان الفرعي</label>
                    <input type="text" name="signup_card_subtitle" class="form-control" 
                           value="<?php echo e($settings['signup_card_subtitle'] ?? 'ابدأ رحلتك نحو نهائية الاحياء'); ?>" 
                           placeholder="ابدأ رحلتك نحو نهائية الاحياء">
                    <small class="text-muted">العنوان الفرعي تحت العنوان الرئيسي</small>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">اسم المدرس</label>
                    <input type="text" name="signup_card_teacher_name" class="form-control" 
                           value="<?php echo e($settings['signup_card_teacher_name'] ?? 'مع مستر سامح صلاح'); ?>" 
                           placeholder="مع مستر سامح صلاح">
                    <small class="text-muted">النص الذي يظهر تحت العنوان الفرعي</small>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">مميزات الاشتراك</label>
                    <div id="signup-benefits-container">
                        <?php
                            $defaultBenefits = [
                                ['title' => 'شرح مبسط وواضح', 'description' => 'لجميع النقاط', 'icon' => 'fa-book'],
                                ['title' => 'فيديوهات عالية الجودة', 'description' => 'HD', 'icon' => 'fa-video'],
                                ['title' => 'تمارين تفاعلية', 'description' => 'ومتنوعة', 'icon' => 'fa-tasks'],
                                ['title' => 'امتحانات', 'description' => 'لقياس مستواك', 'icon' => 'fa-clipboard-check'],
                                ['title' => 'متاح 24/7', 'description' => 'من أي مكان', 'icon' => 'fa-clock'],
                            ];
                            // التحقق من نوع البيانات
                            $signupBenefitsData = $settings['signup_card_benefits'] ?? [];
                            if (is_string($signupBenefitsData)) {
                                $signupBenefits = json_decode($signupBenefitsData, true) ?? [];
                            } else {
                                $signupBenefits = is_array($signupBenefitsData) ? $signupBenefitsData : [];
                            }
                            if (empty($signupBenefits)) {
                                $signupBenefits = $defaultBenefits;
                            }
                        ?>
                        <?php $__currentLoopData = $signupBenefits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $benefit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="signup-benefit-item mb-3 p-3 border rounded">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">العنوان</label>
                                    <input type="text" name="signup_benefits[<?php echo e($index); ?>][title]" 
                                           class="form-control" value="<?php echo e($benefit['title'] ?? ''); ?>" 
                                           placeholder="مثال: شرح مبسط وواضح" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">الوصف</label>
                                    <input type="text" name="signup_benefits[<?php echo e($index); ?>][description]" 
                                           class="form-control" value="<?php echo e($benefit['description'] ?? ''); ?>" 
                                           placeholder="مثال: لجميع النقاط" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">الأيقونة</label>
                                    <input type="text" name="signup_benefits[<?php echo e($index); ?>][icon]" 
                                           class="form-control" value="<?php echo e($benefit['icon'] ?? 'fa-check-circle'); ?>" 
                                           placeholder="fa-book" required>
                                    <small class="text-muted">Font Awesome icon class</small>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-sm btn-danger w-100" onclick="removeSignupBenefit(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <button type="button" class="btn btn-modern btn-modern-primary btn-sm" onclick="addSignupBenefit()">
                        <i class="fas fa-plus me-2"></i> إضافة ميزة جديدة
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- الصفحات القانونية -->
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h4><i class="fas fa-gavel me-2"></i> الصفحات القانونية</h4>
        </div>
        <div class="card-body-modern">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">سياسة الخصوصية</label>
                    <textarea name="privacy_policy" class="form-control" rows="10" placeholder="أدخل نص سياسة الخصوصية..."><?php echo e($settings['privacy_policy'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">شروط الاستخدام</label>
                    <textarea name="terms_of_service" class="form-control" rows="10" placeholder="أدخل نص شروط الاستخدام..."><?php echo e($settings['terms_of_service'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- إعدادات الأمان -->
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h4><i class="fas fa-shield-alt me-2"></i> إعدادات الأمان</h4>
        </div>
        <div class="card-body-modern">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">مدة انتهاء الجلسة (بالدقائق)</label>
                    <input type="number" name="session_lifetime" class="form-control" 
                           value="<?php echo e($settings['session_lifetime'] ?? 0); ?>" min="0" max="525600">
                    <small class="text-muted">
                        <strong>0 = لا نهائي</strong> (الجلسات لا تنتهي أبداً)<br>
                        القيمة الافتراضية: 0 (لا نهائي)
                    </small>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">عدد محاولات تسجيل الدخول المسموحة</label>
                    <input type="number" name="login_attempts" class="form-control" 
                           value="<?php echo e($settings['login_attempts'] ?? 5); ?>" min="3" max="10">
                    <small class="text-muted">بعد تجاوز هذا العدد سيتم حظر الحساب مؤقتاً</small>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">تفعيل/تعطيل تسجيل الدخول</label>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="login_enabled" 
                               id="login_enabled" value="1" 
                               <?php echo e(($settings['login_enabled'] ?? true) ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="login_enabled">
                            تفعيل تسجيل الدخول
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إعدادات الألوان الإضافية -->
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h4><i class="fas fa-palette me-2"></i> إعدادات الألوان الإضافية</h4>
        </div>
        <div class="card-body-modern">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">لون النصوص الأساسي</label>
                    <input type="color" name="text_color" class="form-control form-control-color" 
                           value="<?php echo e($settings['text_color'] ?? '#333333'); ?>" title="اختر لون النصوص">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">لون الخلفيات الأساسي</label>
                    <input type="color" name="background_color" class="form-control form-control-color" 
                           value="<?php echo e($settings['background_color'] ?? '#ffffff'); ?>" title="اختر لون الخلفيات">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">لون النجاح</label>
                    <input type="color" name="success_color" class="form-control form-control-color" 
                           value="<?php echo e($settings['success_color'] ?? '#28a745'); ?>" title="اختر لون النجاح">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">لون التحذير</label>
                    <input type="color" name="warning_color" class="form-control form-control-color" 
                           value="<?php echo e($settings['warning_color'] ?? '#ffc107'); ?>" title="اختر لون التحذير">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">لون الخطأ</label>
                    <input type="color" name="error_color" class="form-control form-control-color" 
                           value="<?php echo e($settings['error_color'] ?? '#dc3545'); ?>" title="اختر لون الخطأ">
                </div>
            </div>
        </div>
    </div>

    <!-- إعدادات الخطوط -->
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h4><i class="fas fa-font me-2"></i> إعدادات الخطوط</h4>
        </div>
        <div class="card-body-modern">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">خط العنوان</label>
                    <select name="heading_font" class="form-select">
                        <option value="Tajawal" <?php echo e(($settings['heading_font'] ?? 'Tajawal') == 'Tajawal' ? 'selected' : ''); ?>>Tajawal</option>
                        <option value="Cairo" <?php echo e(($settings['heading_font'] ?? '') == 'Cairo' ? 'selected' : ''); ?>>Cairo</option>
                        <option value="Almarai" <?php echo e(($settings['heading_font'] ?? '') == 'Almarai' ? 'selected' : ''); ?>>Almarai</option>
                        <option value="Amiri" <?php echo e(($settings['heading_font'] ?? '') == 'Amiri' ? 'selected' : ''); ?>>Amiri</option>
                        <option value="Arial" <?php echo e(($settings['heading_font'] ?? '') == 'Arial' ? 'selected' : ''); ?>>Arial</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">خط النص</label>
                    <select name="body_font" class="form-select">
                        <option value="Tajawal" <?php echo e(($settings['body_font'] ?? 'Tajawal') == 'Tajawal' ? 'selected' : ''); ?>>Tajawal</option>
                        <option value="Cairo" <?php echo e(($settings['body_font'] ?? '') == 'Cairo' ? 'selected' : ''); ?>>Cairo</option>
                        <option value="Almarai" <?php echo e(($settings['body_font'] ?? '') == 'Almarai' ? 'selected' : ''); ?>>Almarai</option>
                        <option value="Amiri" <?php echo e(($settings['body_font'] ?? '') == 'Amiri' ? 'selected' : ''); ?>>Amiri</option>
                        <option value="Arial" <?php echo e(($settings['body_font'] ?? '') == 'Arial' ? 'selected' : ''); ?>>Arial</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">حجم الخط الأساسي (px)</label>
                    <input type="number" name="font_size" class="form-control" 
                           value="<?php echo e($settings['font_size'] ?? 16); ?>" min="12" max="24">
                </div>
            </div>
        </div>
    </div>

    <!-- إعدادات الصور الافتراضية -->
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h4><i class="fas fa-images me-2"></i> إعدادات الصور الافتراضية</h4>
        </div>
        <div class="card-body-modern">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">صورة افتراضية للكورسات</label>
                    <input type="file" name="default_course_image" class="form-control" accept="image/*">
                    <?php if(isset($settings['default_course_image']) && !empty($settings['default_course_image'])): ?>
                    <small class="text-muted">الصورة الحالية: <?php echo e($settings['default_course_image']); ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">صورة افتراضية للطلاب</label>
                    <input type="file" name="default_student_image" class="form-control" accept="image/*">
                    <?php if(isset($settings['default_student_image']) && !empty($settings['default_student_image'])): ?>
                    <small class="text-muted">الصورة الحالية: <?php echo e($settings['default_student_image']); ?></small>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">صورة Hero Section</label>
                    <input type="file" name="hero_image" class="form-control" accept="image/*">
                    <?php if(isset($settings['hero_image']) && !empty($settings['hero_image'])): ?>
                    <small class="text-muted">الصورة الحالية: <?php echo e($settings['hero_image']); ?></small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- إعدادات الروابط الخارجية -->
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h4><i class="fas fa-link me-2"></i> إعدادات الروابط الخارجية</h4>
        </div>
        <div class="card-body-modern">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">رابط YouTube</label>
                    <input type="url" name="youtube_url" class="form-control" 
                           value="<?php echo e($settings['youtube_url'] ?? ''); ?>" placeholder="https://youtube.com/...">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">رابط Telegram</label>
                    <input type="url" name="telegram_url" class="form-control" 
                           value="<?php echo e($settings['telegram_url'] ?? ''); ?>" placeholder="https://t.me/...">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">روابط أخرى (JSON)</label>
                    <textarea name="other_links" class="form-control" rows="5" 
                              placeholder='{"link_name": "https://example.com", ...}'><?php echo e($settings['other_links'] ?? ''); ?></textarea>
                    <small class="text-muted">صيغة JSON: {"اسم الرابط": "الرابط"}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- إعدادات Webhook (ربط النظام الخارجي) -->
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h4><i class="fas fa-plug me-2"></i> إعدادات Webhook (ربط النظام الخارجي)</h4>
        </div>
        <div class="card-body-modern">
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>معلومات:</strong> استخدم هذه الروابط في النظام الخارجي (السنتر) لإرسال البيانات إلى المنصة.
                <br>
                <small>فقط أرسل HTTP POST request إلى الرابط المطلوب بدون أي مصادقة.</small>
            </div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-money-bill-wave me-2"></i> رابط الدفع (مُوصى به) - يسجل + يفعّل تلقائياً
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label"><strong>رابط Webhook للدفع:</strong></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" 
                                           value="<?php echo e(url('/webhook/payment')); ?>" readonly>
                                    <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard('<?php echo e(url('/webhook/payment')); ?>')">
                                        <i class="fas fa-copy"></i> نسخ
                                    </button>
                                </div>
                                <small class="text-muted">
                                    <strong>استخدم هذا الرابط في النظام الخارجي:</strong> هذا الرابط يعمل تلقائياً - إذا كان الطالب موجود يفعّل الاشتراك مباشرة، وإذا كان غير موجود يسجله أولاً ثم يفعّل الاشتراك.
                                    <br>
                                    <strong>البيانات المطلوبة:</strong> <code>phone</code>, <code>month_number</code>, <code>student_code</code> (مطلوب فقط إذا كان الطالب غير موجود)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-link me-2"></i> روابط Webhook الأخرى (اختيارية)
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label"><strong>تسجيل طالب جديد:</strong></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" 
                                           value="<?php echo e(url('/webhook/register-student')); ?>" readonly>
                                    <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard('<?php echo e(url('/webhook/register-student')); ?>')">
                                        <i class="fas fa-copy"></i> نسخ
                                    </button>
                                </div>
                                <small class="text-muted">أرسل POST request إلى هذا الرابط لتسجيل طالب جديد</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>تفعيل اشتراك (بعد الدفع):</strong></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" 
                                           value="<?php echo e(url('/webhook/activate-subscription')); ?>" readonly>
                                    <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard('<?php echo e(url('/webhook/activate-subscription')); ?>')">
                                        <i class="fas fa-copy"></i> نسخ
                                    </button>
                                </div>
                                <small class="text-muted">أرسل POST request إلى هذا الرابط لتفعيل الكورس بعد الدفع</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>ملاحظات مهمة:</strong>
                        <ul class="mb-0 mt-2">
                            <li>استخدم HTTPS في الإنتاج لضمان الأمان</li>
                            <li>تأكد من أن الكورسات تم إنشاؤها في الشهر الصحيح (النظام يربط الكورس بالشهر بناءً على تاريخ الإنشاء)</li>
                            <li>جميع الطلبات يتم تسجيلها في Logs للمراجعة</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إعدادات الصفحة الرئيسية -->
    <div class="modern-card mb-4">
        <div class="card-header-modern">
            <h4><i class="fas fa-home me-2"></i> إعدادات الصفحة الرئيسية</h4>
        </div>
        <div class="card-body-modern">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">نص إضافي في Hero Section</label>
                    <textarea name="hero_additional_text" class="form-control" rows="3" 
                              placeholder="نص إضافي يظهر في Hero Section..."><?php echo e($settings['hero_additional_text'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">رابط Call-to-Action الأول</label>
                    <input type="url" name="cta_link_1" class="form-control" 
                           value="<?php echo e($settings['cta_link_1'] ?? ''); ?>" placeholder="https://...">
                    <input type="text" name="cta_text_1" class="form-control mt-2" 
                           value="<?php echo e($settings['cta_text_1'] ?? 'اشترك الآن'); ?>" placeholder="نص الزر">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">رابط Call-to-Action الثاني</label>
                    <input type="url" name="cta_link_2" class="form-control" 
                           value="<?php echo e($settings['cta_link_2'] ?? ''); ?>" placeholder="https://...">
                    <input type="text" name="cta_text_2" class="form-control mt-2" 
                           value="<?php echo e($settings['cta_text_2'] ?? 'تعرف على المزيد'); ?>" placeholder="نص الزر">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">عدد الكورسات المعروضة في الصفحة الرئيسية</label>
                    <input type="number" name="courses_per_page" class="form-control" 
                           value="<?php echo e($settings['courses_per_page'] ?? 12); ?>" min="6" max="24">
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="text-end mb-4">
        <button type="submit" class="btn btn-modern-primary btn-lg">
            <i class="fas fa-save me-2"></i> حفظ الإعدادات
        </button>
    </div>
</form>

<!-- Reset Confirmation Modal -->
<div class="modal fade" id="resetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد إعادة التعيين</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من إعادة تعيين جميع الإعدادات للقيم الافتراضية؟</p>
                <p class="text-danger"><strong>تحذير:</strong> سيتم فقدان جميع التغييرات الحالية!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="<?php echo e(route('admin.general_settings.reset')); ?>" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('POST'); ?>
                    <button type="submit" class="btn btn-warning">نعم، إعادة التعيين</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Update color text inputs when color picker changes
document.getElementById('primary_color').addEventListener('input', function(e) {
    document.getElementById('primary_color_text').value = e.target.value;
    updatePreview();
});

document.getElementById('secondary_color').addEventListener('input', function(e) {
    document.getElementById('secondary_color_text').value = e.target.value;
    updatePreview();
});

// Update preview in real-time
function updatePreview() {
    const primaryColor = document.getElementById('primary_color').value;
    const secondaryColor = document.getElementById('secondary_color').value;
    const siteName = document.getElementById('site_name').value || 'اسم المنصة';
    const teacherName = document.getElementById('teacher_name').value || 'اسم المدرس';
    const subjectName = document.getElementById('subject_name').value || 'المادة';
    
    // Update preview boxes
    const previewBoxes = document.querySelectorAll('.preview-box');
    if (previewBoxes[0]) {
        previewBoxes[0].style.background = `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})`;
        previewBoxes[0].querySelector('h3').textContent = siteName;
        previewBoxes[0].querySelector('p').textContent = `${teacherName} - ${subjectName}`;
    }
    if (previewBoxes[1]) {
        previewBoxes[1].style.borderColor = primaryColor;
        previewBoxes[1].querySelector('h4').style.color = primaryColor;
        previewBoxes[1].querySelector('p').style.color = secondaryColor;
        previewBoxes[1].querySelector('button').style.background = primaryColor;
    }
}

// Add event listeners for text inputs
['site_name', 'teacher_name', 'subject_name'].forEach(id => {
    const element = document.getElementById(id);
    if (element) {
        element.addEventListener('input', updatePreview);
    }
});

// Reset confirmation
function resetSettings() {
    const modal = new bootstrap.Modal(document.getElementById('resetModal'));
    modal.show();
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('تم نسخ الرابط!');
    }, function(err) {
        console.error('Failed to copy: ', err);
    });
}

// Add/Remove Functions
let conceptIndex = <?php echo e(count($settings['subject_concepts'] ?? [])); ?>;
function addConcept() {
    const container = document.getElementById('concepts-container');
    const html = `
        <div class="concept-item mb-3 p-3 border rounded">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="form-check">
                    <input type="checkbox" name="concepts[${conceptIndex}][enabled]" class="form-check-input" checked id="concept_enabled_${conceptIndex}">
                    <label class="form-check-label" for="concept_enabled_${conceptIndex}">مفعّل</label>
                </div>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeConcept(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label">اسم المفهوم</label>
                    <input type="text" name="concepts[${conceptIndex}][name]" class="form-control" placeholder="مثال: الضوء" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">الأيقونة (Font Awesome)</label>
                    <input type="text" name="concepts[${conceptIndex}][icon]" class="form-control" placeholder="fa-lightbulb" value="fa-circle">
                    <small class="text-muted">مثال: fa-lightbulb, fa-fire, fa-weight, fa-rocket</small>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    conceptIndex++;
}
function removeConcept(btn) {
    if (confirm('هل أنت متأكد من حذف هذا المفهوم؟')) {
        btn.closest('.concept-item').remove();
    }
}

let featureIndex = <?php echo e(count($settings['features_list'] ?? [])); ?>;
function addFeature() {
    const container = document.getElementById('features-container');
    const html = `
        <div class="feature-item mb-3 p-3 border rounded">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="form-check">
                    <input type="checkbox" name="features[${featureIndex}][enabled]" class="form-check-input" checked id="feature_enabled_${featureIndex}">
                    <label class="form-check-label" for="feature_enabled_${featureIndex}">مفعّل</label>
                </div>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeFeature(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-4 mb-2">
                    <input type="text" name="features[${featureIndex}][title]" class="form-control" placeholder="العنوان" required>
                </div>
                <div class="col-md-4 mb-2">
                    <input type="text" name="features[${featureIndex}][icon]" class="form-control" placeholder="fa-video" value="fa-check">
                </div>
                <div class="col-md-12 mb-2">
                    <textarea name="features[${featureIndex}][description]" class="form-control" rows="2" placeholder="الوصف"></textarea>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    featureIndex++;
}
function removeFeature(btn) {
    btn.closest('.feature-item').remove();
}

let topicIndex = <?php echo e(count($settings['topics_list'] ?? [])); ?>;
function addTopic() {
    const container = document.getElementById('topics-container');
    const html = `
        <div class="topic-item mb-3 p-3 border rounded">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="form-check">
                    <input type="checkbox" name="topics[${topicIndex}][enabled]" class="form-check-input" checked id="topic_enabled_${topicIndex}">
                    <label class="form-check-label" for="topic_enabled_${topicIndex}">مفعّل</label>
                </div>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeTopic(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <input type="text" name="topics[${topicIndex}][name]" class="form-control" placeholder="اسم الموضوع" required>
                </div>
                <div class="col-md-6 mb-2">
                    <input type="text" name="topics[${topicIndex}][icon]" class="form-control" placeholder="fa-atom" value="fa-atom">
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    topicIndex++;
}
function removeTopic(btn) {
    btn.closest('.topic-item').remove();
}

let gradeIndex = <?php echo e(count($settings['grades_list'] ?? [])); ?>;
function addGrade() {
    const container = document.getElementById('grades-container');
    const html = `
        <div class="grade-item mb-3 p-3 border rounded">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="form-check">
                    <input type="checkbox" name="grades[${gradeIndex}][enabled]" class="form-check-input" checked id="grade_enabled_${gradeIndex}">
                    <label class="form-check-label" for="grade_enabled_${gradeIndex}">مفعّل</label>
                </div>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeGrade(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <input type="text" name="grades[${gradeIndex}][name]" class="form-control" placeholder="اسم السنة الدراسية" required>
                </div>
                <div class="col-md-6 mb-2">
                    <input type="text" name="grades[${gradeIndex}][icon]" class="form-control" placeholder="fa-graduation-cap" value="fa-graduation-cap">
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    gradeIndex++;
}
function removeGrade(btn) {
    btn.closest('.grade-item').remove();
}

let benefitIndex = <?php echo e(count($settings['benefits_list'] ?? [])); ?>;
function addBenefit() {
    const container = document.getElementById('benefits-container');
    const html = `
        <div class="benefit-item mb-3 p-3 border rounded">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="form-check">
                    <input type="checkbox" name="benefits[${benefitIndex}][enabled]" class="form-check-input" checked id="benefit_enabled_${benefitIndex}">
                    <label class="form-check-label" for="benefit_enabled_${benefitIndex}">مفعّل</label>
                </div>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeBenefit(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-4 mb-2">
                    <input type="text" name="benefits[${benefitIndex}][title]" class="form-control" placeholder="العنوان" required>
                </div>
                <div class="col-md-4 mb-2">
                    <input type="text" name="benefits[${benefitIndex}][icon]" class="form-control" placeholder="fa-book-open" value="fa-check">
                </div>
                <div class="col-md-12 mb-2">
                    <textarea name="benefits[${benefitIndex}][description]" class="form-control" rows="2" placeholder="الوصف"></textarea>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    benefitIndex++;
}
function removeBenefit(btn) {
    btn.closest('.benefit-item').remove();
}


let achievementIndex = <?php echo e(count($settings['about_teacher_achievements'] ?? [])); ?>;
function addAboutAchievement() {
    const container = document.getElementById('about-achievements-container');
    const idx = achievementIndex;
    const html = `
        <div class="about-achievement-item mb-3 p-3 border rounded">
            <div class="row g-3 align-items-center">
                <div class="col-md-3 text-center">
                    <div style="width:100px; height:70px; background:#f1f3ff; border-radius:8px; display:inline-block;"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">التعليق</label>
                    <input type="text" name="about_achievements[${idx}][caption]" class="form-control" value="" placeholder="مثال: فاز بجائزة أفضل مدرس 2023">
                    <input type="hidden" name="about_achievements[${idx}][existing_image]" value="">
                </div>
                <div class="col-md-2">
                    <label class="form-label">صورة (اختياري)</label>
                    <input type="file" name="about_achievements_images[${idx}]" class="form-control" accept="image/*">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-danger w-100" onclick="removeAboutAchievement(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    achievementIndex++;
}
function removeAboutAchievement(btn) {
    if (confirm('هل أنت متأكد من حذف هذا الانجاز؟')) {
        btn.closest('.about-achievement-item').remove();
    }
}


let signupGradeIndex = <?php echo e(count($settings['signup_grades'] ?? [])); ?>;
function addSignupGrade() {
    const container = document.getElementById('signup-grades-container');
    const html = `
        <div class="signup-grade-item mb-3 p-3 border rounded">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="form-check">
                    <input type="checkbox" name="signup_grades[${signupGradeIndex}][enabled]" class="form-check-input" checked id="signup_grade_enabled_${signupGradeIndex}">
                    <label class="form-check-label" for="signup_grade_enabled_${signupGradeIndex}">مفعّل</label>
                </div>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeSignupGrade(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <input type="text" name="signup_grades[${signupGradeIndex}][value]" class="form-control" placeholder="القيمة (أولي، تانية، إلخ)" required>
                </div>
                <div class="col-md-6 mb-2">
                    <input type="text" name="signup_grades[${signupGradeIndex}][label]" class="form-control" placeholder="التسمية (الصف الأول الثانوي)" required>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    signupGradeIndex++;
}
function removeSignupGrade(btn) {
    btn.closest('.signup-grade-item').remove();
}

<?php
    $signupBenefitsData = $settings['signup_card_benefits'] ?? [];
    if (is_string($signupBenefitsData)) {
        $signupBenefitsForCount = json_decode($signupBenefitsData, true) ?? [];
    } else {
        $signupBenefitsForCount = is_array($signupBenefitsData) ? $signupBenefitsData : [];
    }
?>
let signupBenefitIndex = <?php echo e(count($signupBenefitsForCount)); ?>;
function addSignupBenefit() {
    const container = document.getElementById('signup-benefits-container');
    const html = `
        <div class="signup-benefit-item mb-3 p-3 border rounded">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">العنوان</label>
                    <input type="text" name="signup_benefits[${signupBenefitIndex}][title]" 
                           class="form-control" placeholder="مثال: شرح مبسط وواضح" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">الوصف</label>
                    <input type="text" name="signup_benefits[${signupBenefitIndex}][description]" 
                           class="form-control" placeholder="مثال: لجميع النقاط" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">الأيقونة</label>
                    <input type="text" name="signup_benefits[${signupBenefitIndex}][icon]" 
                           class="form-control" value="fa-check-circle" 
                           placeholder="fa-book" required>
                    <small class="text-muted">Font Awesome icon class</small>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-danger w-100" onclick="removeSignupBenefit(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    signupBenefitIndex++;
}
function removeSignupBenefit(btn) {
    btn.closest('.signup-benefit-item').remove();
}

<?php
    $faqListForJS = $settings['faq_list'] ?? [];
    if (is_string($faqListForJS)) {
        $faqListForJS = json_decode($faqListForJS, true) ?? [];
    }
    if (!is_array($faqListForJS)) {
        $faqListForJS = [];
    }
?>
let faqIndex = <?php echo e(count($faqListForJS)); ?>;
function addFAQItem() {
    const container = document.getElementById('faq-list-container');
    const html = `
        <div class="faq-item-settings mb-3 p-3 border rounded">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong class="text-primary">سؤال ${faqIndex + 1}</strong>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeFAQItem(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">السؤال</label>
                    <input type="text" name="faq_list[${faqIndex}][question]" 
                           class="form-control" 
                           placeholder="مثال: كيف يمكنني الاشتراك في الكورسات؟" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">الإجابة</label>
                    <textarea name="faq_list[${faqIndex}][answer]" 
                              class="form-control" rows="3" 
                              placeholder="مثال: يمكنك الاشتراك بسهولة عن طريق..." required></textarea>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    faqIndex++;
}
function removeFAQItem(btn) {
    btn.closest('.faq-item-settings').remove();
}

let governorateIndex = <?php echo e(count($settings['governorates_list'] ?? [])); ?>;
function addGovernorate() {
    const container = document.getElementById('governorates-container');
    const html = `
        <div class="governorate-item mb-2 p-2 border rounded d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input type="checkbox" name="governorates[${governorateIndex}][enabled]" class="form-check-input" checked id="gov_enabled_${governorateIndex}">
                <input type="text" name="governorates[${governorateIndex}][name]" class="form-control d-inline-block ms-2" placeholder="اسم المحافظة" style="width: 200px;" required>
            </div>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeGovernorate(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    governorateIndex++;
}
function removeGovernorate(btn) {
    btn.closest('.governorate-item').remove();
}

// About Teacher Page - Qualifications
<?php
    $qualificationsForJS = $settings['about_teacher_qualifications'] ?? [];
    if (is_string($qualificationsForJS)) {
        $qualificationsForJS = json_decode($qualificationsForJS, true) ?? [];
    }
    if (!is_array($qualificationsForJS)) {
        $qualificationsForJS = [];
    }
?>
let aboutQualificationIndex = <?php echo e(count($qualificationsForJS)); ?>;
function addAboutQualification() {
    const container = document.getElementById('about-qualifications-container');
    const html = `
        <div class="about-qualification-item mb-3 p-3 border rounded">
            <div class="row g-3">
                <div class="col-md-10">
                    <label class="form-label">النص</label>
                    <input type="text" name="about_qualifications[${aboutQualificationIndex}][text]" 
                           class="form-control" placeholder="مثال: بكالوريوس آداب قسم اللغة الإنجليزية" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">الأيقونة</label>
                    <input type="text" name="about_qualifications[${aboutQualificationIndex}][icon]" 
                           class="form-control" value="fa-check-circle" placeholder="fa-check-circle" required>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeAboutQualification(this)">
                <i class="fas fa-trash"></i> حذف
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    aboutQualificationIndex++;
}
function removeAboutQualification(btn) {
    btn.closest('.about-qualification-item').remove();
}

// About Teacher Page - Stats
<?php
    $statsForJS = $settings['about_teacher_stats'] ?? [];
    if (is_string($statsForJS)) {
        $statsForJS = json_decode($statsForJS, true) ?? [];
    }
    if (!is_array($statsForJS)) {
        $statsForJS = [];
    }
?>
let aboutStatIndex = <?php echo e(count($statsForJS)); ?>;
function addAboutStat() {
    const container = document.getElementById('about-stats-container');
    const html = `
        <div class="about-stat-item mb-3 p-3 border rounded">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">الرقم</label>
                    <input type="text" name="about_stats[${aboutStatIndex}][number]" 
                           class="form-control" placeholder="مثال: 15+" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label">التسمية</label>
                    <input type="text" name="about_stats[${aboutStatIndex}][label]" 
                           class="form-control" placeholder="مثال: سنة خبرة" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-danger w-100" onclick="removeAboutStat(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    aboutStatIndex++;
}
function removeAboutStat(btn) {
    btn.closest('.about-stat-item').remove();
}

// About Teacher Page - Methods
<?php
    $methodsForJS = $settings['about_teacher_methods'] ?? [];
    if (is_string($methodsForJS)) {
        $methodsForJS = json_decode($methodsForJS, true) ?? [];
    }
    if (!is_array($methodsForJS)) {
        $methodsForJS = [];
    }
?>
let aboutMethodIndex = <?php echo e(count($methodsForJS)); ?>;
function addAboutMethod() {
    const container = document.getElementById('about-methods-container');
    const html = `
        <div class="about-method-item mb-3 p-3 border rounded">
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">العنوان</label>
                    <input type="text" name="about_methods[${aboutMethodIndex}][title]" 
                           class="form-control" placeholder="مثال: شرح مبسط وواضح" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">الوصف</label>
                    <textarea name="about_methods[${aboutMethodIndex}][description]" 
                              class="form-control" rows="2" 
                              placeholder="مثال: تبسيط القواعد والمفاهيم المعقدة..." required></textarea>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeAboutMethod(this)">
                <i class="fas fa-trash"></i> حذف
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    aboutMethodIndex++;
}
function removeAboutMethod(btn) {
    btn.closest('.about-method-item').remove();
}

// About Teacher Page - Reviews
<?php
    $reviewsForJS = $settings['about_teacher_reviews'] ?? [];
    if (is_string($reviewsForJS)) {
        $reviewsForJS = json_decode($reviewsForJS, true) ?? [];
    }
    if (!is_array($reviewsForJS)) {
        $reviewsForJS = [];
    }
?>
let aboutReviewIndex = <?php echo e(count($reviewsForJS)); ?>;
function addAboutReview() {
    const container = document.getElementById('about-reviews-container');
    const html = `
        <div class="about-review-item mb-3 p-3 border rounded">
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">نص الرأي</label>
                    <textarea name="about_reviews[${aboutReviewIndex}][text]" 
                              class="form-control" rows="2" 
                              placeholder="مثال: شرح مستر حماده أكتر من رائع!..." required></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">اسم الطالب</label>
                    <input type="text" name="about_reviews[${aboutReviewIndex}][student_name]" 
                           class="form-control" placeholder="مثال: أحمد محمد" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">الصف</label>
                    <input type="text" name="about_reviews[${aboutReviewIndex}][grade]" 
                           class="form-control" placeholder="مثال: الصف الثالث الثانوي" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">الحرف الأول</label>
                    <input type="text" name="about_reviews[${aboutReviewIndex}][initial]" 
                           class="form-control" placeholder="مثال: أ" maxlength="1">
                    <small class="text-muted">سيتم توليده تلقائياً إذا تركت فارغاً</small>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-sm btn-danger w-100" onclick="removeAboutReview(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    aboutReviewIndex++;
}
function removeAboutReview(btn) {
    btn.closest('.about-review-item').remove();
}

    document.addEventListener('DOMContentLoaded', function () {

        const toggle = document.getElementById('video_tracking_enabled');
        const wrapper = document.getElementById('videoCompletionWrapper');
        const input = document.getElementById('video_completion_percentage');

        function updateState() {
            if (toggle.checked) {
                wrapper.classList.remove('hidden');
                wrapper.classList.add('visible');
                input.disabled = false;
            } else {
                wrapper.classList.remove('visible');
                wrapper.classList.add('hidden');
                input.disabled = true;
            }
        }

        toggle.addEventListener('change', updateState);
        updateState(); // عند تحميل الصفحة
    });
</script>

<style>
.form-control-color {
    width: 80px;
    height: 50px;
    cursor: pointer;
}

.preview-box {
    transition: all 0.3s ease;
}
    .fade-toggle {
        transition: all 0.35s ease;
        overflow: hidden;
    }

    .fade-toggle.hidden {
        opacity: 0;
        max-height: 0;
        margin-top: 0 !important;
        pointer-events: none;
    }

    .fade-toggle.visible {
        opacity: 1;
        max-height: 200px;
    }
</style>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('back_layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\A-Tech\Downloads\archiveGzNa7\resources\views/back/general_settings/index.blade.php ENDPATH**/ ?>