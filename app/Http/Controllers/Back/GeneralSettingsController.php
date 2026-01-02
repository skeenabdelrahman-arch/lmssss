<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class GeneralSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    /**
     * عرض صفحة الإعدادات العامة
     */
    public function index()
    {
        // التحقق من أن المستخدم Super Admin
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        $settings = Setting::all()->pluck('value', 'key')->toArray();
        
        // تحويل JSON strings إلى arrays
        $jsonKeys = ['hero_stats', 'features_list', 'topics_list', 'grades_list', 'benefits_list', 'signup_grades', 'governorates_list', 'payment_methods', 'subject_concepts', 'signup_card_benefits', 'faq_list', 'about_teacher_qualifications', 'about_teacher_stats', 'about_teacher_methods', 'about_teacher_reviews', 'about_teacher_achievements'];
        foreach ($jsonKeys as $key) {
            if (isset($settings[$key]) && !empty($settings[$key])) {
                $settings[$key] = json_decode($settings[$key], true) ?? [];
            } else {
                $settings[$key] = [];
            }
        }
        
        // القيم الافتراضية لوسائل الدفع
        if (empty($settings['payment_methods'])) {
            $settings['payment_methods'] = [
                'online_payment' => true,
                'activation_codes' => true,
                'admin_activation' => true,
                'free_courses' => true,
            ];
        }
        
        return view('back.general_settings.index', compact('settings'));
    }

    /**
     * حفظ الإعدادات
     */
    public function update(Request $request)
    {
        // التحقق من أن المستخدم Super Admin
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        $request->validate([
            'site_name' => 'required|string|max:255',
            'teacher_name' => 'required|string|max:255',
            'subject_name' => 'required|string|max:255',
            'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:512',
            // Contact Info
            'whatsapp_number' => 'nullable|string|max:50',
            'phone_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            // Social Media
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            // Footer & Content
            'footer_text' => 'nullable|string|max:500',
            'footer_copyright' => 'nullable|string|max:255',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:255',
            'hero_quote' => 'nullable|string|max:255',
            'teacher_bio' => 'nullable|string|max:1000',
            // Developer Info
            'developer_text' => 'nullable|string|max:255',
            'developer_facebook' => 'nullable|url|max:255',
            'developer_whatsapp' => 'nullable|string|max:50',
            // About Teacher Page
            'teacher_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'about_teacher_bio' => 'nullable|string|max:2000',
            // Achievements (images + captions)
            'about_achievements.*.caption' => 'nullable|string|max:255',
            'about_achievements_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            'video_completion_percentage' => 'nullable|numeric|min:0|max:100',
            'video_tracking_enabled' => 'nullable|boolean',

        ]);
        Setting::set('video_completion_percentage', $request->video_completion_percentage ?? 80, 'number', 'general', 'النسبة المئوية المطلوبة لاعتبار الفيديو مكتمل');
        Setting::set('video_tracking_enabled', $request->has('video_tracking_enabled') ? 1 : 0, 'boolean', 'general', 'تفعيل تتبع مشاهدة الفيديوهات');

        // حفظ الإعدادات العامة
        Setting::set('site_name', $request->site_name, 'text', 'general', 'اسم المنصة');
        Setting::set('teacher_name', $request->teacher_name, 'text', 'general', 'اسم المدرس');
        Setting::set('teacher_full_name', $request->teacher_full_name ?? $request->teacher_name, 'text', 'general', 'الاسم الكامل للمدرس');
        Setting::set('subject_name', $request->subject_name, 'text', 'general', 'اسم المادة الدراسية');
        Setting::set('subject_description', $request->subject_description ?? '', 'text', 'general', 'وصف المادة');

        // حفظ الألوان
        Setting::set('primary_color', $request->primary_color, 'color', 'appearance', 'اللون الأساسي');
        Setting::set('secondary_color', $request->secondary_color, 'color', 'appearance', 'اللون الثانوي');

        // حفظ اللوجو
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = 'logo.' . $logo->getClientOriginalExtension();
            $logoPath = $logo->storeAs('front/assets/images', $logoName, 'public');
            Setting::set('logo_path', $logoPath, 'image', 'appearance', 'مسار اللوجو');
        }

        // حفظ Favicon
        if ($request->hasFile('favicon')) {
            $favicon = $request->file('favicon');
            $faviconName = 'favicon.' . $favicon->getClientOriginalExtension();
            $faviconPath = $favicon->storeAs('front/assets/images', $faviconName, 'public');
            Setting::set('favicon_path', $faviconPath, 'image', 'appearance', 'مسار Favicon');
        }

        // حفظ SEO
        Setting::set('site_description', $request->site_description ?? '', 'text', 'seo', 'وصف الموقع');
        Setting::set('site_keywords', $request->site_keywords ?? '', 'text', 'seo', 'الكلمات المفتاحية');

        // حفظ معلومات الاتصال
        Setting::set('whatsapp_number', $request->whatsapp_number ?? '', 'text', 'contact', 'رقم الواتساب');
        Setting::set('phone_number', $request->phone_number ?? '', 'text', 'contact', 'رقم الهاتف');
        Setting::set('contact_email', $request->email ?? '', 'text', 'contact', 'البريد الإلكتروني للاتصال');

        // حفظ روابط وسائل التواصل
        Setting::set('facebook_url', $request->facebook_url ?? '', 'url', 'social', 'رابط Facebook');
        Setting::set('twitter_url', $request->twitter_url ?? '', 'url', 'social', 'رابط Twitter');
        Setting::set('instagram_url', $request->instagram_url ?? '', 'url', 'social', 'رابط Instagram');
        Setting::set('youtube_url', $request->youtube_url ?? '', 'url', 'social', 'رابط YouTube');
        Setting::set('linkedin_url', $request->linkedin_url ?? '', 'url', 'social', 'رابط LinkedIn');

        // حفظ نصوص Footer
        Setting::set('footer_text', $request->footer_text ?? '', 'text', 'content', 'نص Footer');
        Setting::set('footer_copyright', $request->footer_copyright ?? '', 'text', 'content', 'نص حقوق النشر');
        
        // حفظ معلومات المطور
        Setting::set('developer_text', $request->developer_text ?? 'برمجة وتطوير بواسطة', 'text', 'content', 'نص برمجة وتطوير');
        Setting::set('developer_facebook', $request->developer_facebook ?? '', 'url', 'social', 'رابط الفيسبوك للمطور');
        Setting::set('developer_whatsapp', $request->developer_whatsapp ?? '', 'text', 'contact', 'رقم الواتساب للمطور');

        // حفظ نصوص Hero Section
        Setting::set('hero_title', $request->hero_title ?? '', 'text', 'content', 'عنوان Hero Section');
        Setting::set('hero_subtitle', $request->hero_subtitle ?? '', 'text', 'content', 'عنوان فرعي Hero Section');
        Setting::set('hero_quote', $request->hero_quote ?? '', 'text', 'content', 'اقتباس Hero Section');

        // حفظ معلومات المدرس
        Setting::set('teacher_bio', $request->teacher_bio ?? '', 'text', 'general', 'السيرة الذاتية للمدرس');

        // حفظ صورة المدرس
        if ($request->hasFile('teacher_image')) {
            $image = $request->file('teacher_image');
            $imageName = 'teacher_image.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('front/assets/images', $imageName, 'public');
            // نسخ إلى upload_files folder
            $uploadPath = public_path('upload_files');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            copy(storage_path('app/public/' . $imagePath), $uploadPath . '/' . $imageName);
            Setting::set('teacher_image', $imagePath, 'image', 'general', 'صورة المدرس');
        }

        // حفظ إعدادات صفحة "عن المدرس"
        Setting::set('about_teacher_bio', $request->about_teacher_bio ?? '', 'text', 'content', 'نبذة عن المدرس في صفحة عن المدرس');

        // حفظ المؤهلات
        $qualifications = [];
        if ($request->has('about_qualifications')) {
            foreach ($request->about_qualifications as $qual) {
                if (!empty($qual['text'])) {
                    $qualifications[] = [
                        'text' => $qual['text'],
                        'icon' => $qual['icon'] ?? 'fa-check-circle',
                    ];
                }
            }
        }
        Setting::set('about_teacher_qualifications', json_encode($qualifications), 'json', 'content', 'مؤهلات المدرس');

        // حفظ الأرقام
        $stats = [];
        if ($request->has('about_stats')) {
            foreach ($request->about_stats as $stat) {
                if (!empty($stat['number']) && !empty($stat['label'])) {
                    $stats[] = [
                        'number' => $stat['number'],
                        'label' => $stat['label'],
                    ];
                }
            }
        }
        Setting::set('about_teacher_stats', json_encode($stats), 'json', 'content', 'أرقام صفحة عن المدرس');

        // حفظ أسلوب التدريس
        $methods = [];
        if ($request->has('about_methods')) {
            foreach ($request->about_methods as $method) {
                if (!empty($method['title']) && !empty($method['description'])) {
                    $methods[] = [
                        'title' => $method['title'],
                        'description' => $method['description'],
                    ];
                }
            }
        }
        Setting::set('about_teacher_methods', json_encode($methods), 'json', 'content', 'أسلوب التدريس');

        // حفظ آراء الطلاب
        $reviews = [];
        if ($request->has('about_reviews')) {
            foreach ($request->about_reviews as $review) {
                if (!empty($review['text']) && !empty($review['student_name']) && !empty($review['grade'])) {
                    $reviews[] = [
                        'text' => $review['text'],
                        'student_name' => $review['student_name'],
                        'grade' => $review['grade'],
                        'initial' => $review['initial'] ?? mb_substr($review['student_name'], 0, 1, 'UTF-8'),
                    ];
                }
            }
        }
        Setting::set('about_teacher_reviews', json_encode($reviews), 'json', 'content', 'آراء الطلاب في صفحة عن المدرس');

        // حفظ إنجازات المدرس (صور + تعليق)
        $achievements = [];
        $files = $request->file('about_achievements_images') ?? [];
        if ($request->has('about_achievements')) {
            foreach ($request->about_achievements as $index => $item) {
                $caption = $item['caption'] ?? '';
                $existing = $item['existing_image'] ?? '';
                $imagePath = $existing;

                if (isset($files[$index]) && $files[$index]) {
                    $image = $files[$index];
                    $imageName = 'about_achievement_' . $index . '_' . time() . '.' . $image->getClientOriginalExtension();
                    $imagePathStored = $image->storeAs('front/assets/images', $imageName, 'public');
                    // ensure upload_files exists and copy a copy
                    $uploadPath = public_path('upload_files');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    copy(storage_path('app/public/' . $imagePathStored), $uploadPath . '/' . $imageName);
                    // Use the public upload_files path so `asset()` can load it directly
                    $imagePath = 'upload_files/' . $imageName;
                }

                if (!empty($caption) || !empty($imagePath)) {
                    $achievements[] = [
                        'image' => $imagePath,
                        'caption' => $caption,
                    ];
                }
            }
        }
        Setting::set('about_teacher_achievements', json_encode($achievements), 'json', 'content', 'انجازات المدرس');

        // حفظ إحصائيات Hero Section
        $heroStats = [
            ['number' => $request->stat_experience ?? '15', 'label' => $request->stat_experience_label ?? 'سنة خبرة', 'icon' => $request->stat_experience_icon ?? 'fa-calendar-check', 'enabled' => $request->has('stat_experience_enabled')],
            ['number' => $request->stat_success_rate ?? '95', 'label' => $request->stat_success_rate_label ?? 'نسبة النجاح %', 'icon' => $request->stat_success_rate_icon ?? 'fa-trophy', 'enabled' => $request->has('stat_success_rate_enabled')],
            ['number' => $request->stat_students ?? '5000', 'label' => $request->stat_students_label ?? 'طالب', 'icon' => $request->stat_students_icon ?? 'fa-users', 'enabled' => $request->has('stat_students_enabled')],
            ['number' => $request->stat_rating ?? '4.9', 'label' => $request->stat_rating_label ?? 'تقييم', 'icon' => $request->stat_rating_icon ?? 'fa-star', 'enabled' => $request->has('stat_rating_enabled')],
        ];
        Setting::set('hero_stats', json_encode($heroStats), 'json', 'content', 'إحصائيات Hero Section');

        // حفظ مميزات "ماذا تقدم منصتنا"
        $features = [];
        if ($request->has('features')) {
            foreach ($request->features as $index => $feature) {
                if (!empty($feature['title'])) {
                    $features[] = [
                        'title' => $feature['title'],
                        'description' => $feature['description'] ?? '',
                        'icon' => $feature['icon'] ?? 'fa-check',
                        'enabled' => isset($feature['enabled']),
                    ];
                }
            }
        }
        Setting::set('features_list', json_encode($features), 'json', 'content', 'قائمة المميزات');

        // حفظ مواضيع الأحياء
        $topics = [];
        if ($request->has('topics')) {
            foreach ($request->topics as $index => $topic) {
                if (!empty($topic['name'])) {
                    $topics[] = [
                        'name' => $topic['name'],
                        'icon' => $topic['icon'] ?? 'fa-atom',
                        'enabled' => isset($topic['enabled']),
                    ];
                }
            }
        }
        Setting::set('topics_list', json_encode($topics), 'json', 'content', 'قائمة المواضيع');

        // حفظ مفاهيم المادة (رموز Hero Section)
        $concepts = [];
        if ($request->has('concepts')) {
            foreach ($request->concepts as $index => $concept) {
                if (!empty($concept['name'])) {
                    $concepts[] = [
                        'name' => $concept['name'],
                        'icon' => $concept['icon'] ?? 'fa-circle',
                        'enabled' => isset($concept['enabled']),
                    ];
                }
            }
        }
        Setting::set('subject_concepts', json_encode($concepts), 'json', 'content', 'مفاهيم المادة (رموز Hero Section)');

        // حفظ السنوات الدراسية
        $grades = [];
        if ($request->has('grades')) {
            foreach ($request->grades as $index => $grade) {
                if (!empty($grade['name'])) {
                    $grades[] = [
                        'name' => $grade['name'],
                        'icon' => $grade['icon'] ?? 'fa-graduation-cap',
                        'enabled' => isset($grade['enabled']),
                    ];
                }
            }
        }
        Setting::set('grades_list', json_encode($grades), 'json', 'content', 'قائمة السنوات الدراسية');

        // حفظ مميزات "لماذا تشترك"
        $benefits = [];
        if ($request->has('benefits')) {
            foreach ($request->benefits as $index => $benefit) {
                if (!empty($benefit['title'])) {
                    $benefits[] = [
                        'title' => $benefit['title'],
                        'description' => $benefit['description'] ?? '',
                        'icon' => $benefit['icon'] ?? 'fa-check',
                        'enabled' => isset($benefit['enabled']),
                    ];
                }
            }
        }
        Setting::set('benefits_list', json_encode($benefits), 'json', 'content', 'قائمة مميزات الاشتراك');

        // حفظ الصفوف الدراسية في صفحة التسجيل
        $signupGrades = [];
        if ($request->has('signup_grades')) {
            foreach ($request->signup_grades as $grade) {
                if (!empty($grade['value']) && !empty($grade['label'])) {
                    $signupGrades[] = [
                        'value' => $grade['value'],
                        'label' => $grade['label'],
                        'enabled' => isset($grade['enabled']),
                    ];
                }
            }
        }
        Setting::set('signup_grades', json_encode($signupGrades), 'json', 'general', 'الصفوف الدراسية في التسجيل');

        // حفظ المحافظات
        $governorates = [];
        if ($request->has('governorates')) {
            foreach ($request->governorates as $gov) {
                if (!empty($gov['name'])) {
                    $governorates[] = [
                        'name' => $gov['name'],
                        'enabled' => isset($gov['enabled']),
                    ];
                }
            }
        }
        Setting::set('governorates_list', json_encode($governorates), 'json', 'general', 'قائمة المحافظات');

        // حفظ وسائل الدفع والتفعيل
        $paymentMethods = [
            'online_payment' => $request->has('payment_methods.online_payment'),
            'activation_codes' => $request->has('payment_methods.activation_codes'),
            'admin_activation' => $request->has('payment_methods.admin_activation'),
            'free_courses' => $request->has('payment_methods.free_courses'),
        ];
        Setting::set('payment_methods', json_encode($paymentMethods), 'json', 'payment', 'وسائل الدفع والتفعيل');

        // حفظ إعدادات صفحة التسجيل
        Setting::set('signup_card_title', $request->signup_card_title ?? 'انضم إلينا اليوم!', 'text', 'signup', 'عنوان كارت صفحة التسجيل');
        Setting::set('signup_card_subtitle', $request->signup_card_subtitle ?? 'ابدأ رحلتك نحو نهائية الاحياء', 'text', 'signup', 'العنوان الفرعي في كارت صفحة التسجيل');
        Setting::set('signup_card_teacher_name', $request->signup_card_teacher_name ?? 'مع مستر سامح صلاح', 'text', 'signup', 'اسم المدرس في كارت صفحة التسجيل');
        
        // حفظ مميزات صفحة التسجيل
        $signupBenefits = [];
        if ($request->has('signup_benefits')) {
            foreach ($request->signup_benefits as $benefit) {
                if (!empty($benefit['title'])) {
                    $signupBenefits[] = [
                        'title' => $benefit['title'],
                        'description' => $benefit['description'] ?? '',
                        'icon' => $benefit['icon'] ?? 'fa-check-circle',
                    ];
                }
            }
        }
        Setting::set('signup_card_benefits', json_encode($signupBenefits), 'json', 'signup', 'مميزات صفحة التسجيل');

        // حفظ إعدادات صفحة الأسئلة الشائعة
        Setting::set('faq_title', $request->faq_title ?? 'هل لديك سؤال؟', 'text', 'faq', 'عنوان صفحة الأسئلة الشائعة');
        Setting::set('faq_subtitle', $request->faq_subtitle ?? 'إجابات على الأسئلة الأكثر شيوعاً', 'text', 'faq', 'العنوان الفرعي في صفحة الأسئلة الشائعة');
        
        // حفظ الأسئلة الشائعة
        $faqList = [];
        if ($request->has('faq_list')) {
            foreach ($request->faq_list as $faq) {
                if (!empty($faq['question']) && !empty($faq['answer'])) {
                    $faqList[] = [
                        'question' => $faq['question'],
                        'answer' => $faq['answer'],
                    ];
                }
            }
        }
        Setting::set('faq_list', json_encode($faqList), 'json', 'faq', 'قائمة الأسئلة الشائعة');

        // حفظ الصفحات القانونية
        Setting::set('privacy_policy', $request->privacy_policy ?? '', 'text', 'legal', 'سياسة الخصوصية');
        Setting::set('terms_of_service', $request->terms_of_service ?? '', 'text', 'legal', 'شروط الاستخدام');

        // حفظ إعدادات الأمان
        // إذا كانت القيمة 0، يتم استخدام سنة واحدة (لا نهائي عملياً)
        $sessionLifetime = $request->session_lifetime ?? 0;
        if ($sessionLifetime <= 0) {
            $sessionLifetime = 0; // سيتم تفسيرها كـ "لا نهائي" في session_lifetime()
        }
        Setting::set('session_lifetime', $sessionLifetime, 'number', 'security', 'مدة انتهاء الجلسة');
        Setting::set('login_attempts', $request->login_attempts ?? 5, 'number', 'security', 'عدد محاولات تسجيل الدخول');
        Setting::set('login_enabled', $request->has('login_enabled') ? '1' : '0', 'boolean', 'security', 'تفعيل تسجيل الدخول');

        // حفظ إعدادات الألوان الإضافية
        Setting::set('text_color', $request->text_color ?? '#333333', 'color', 'appearance', 'لون النصوص');
        Setting::set('background_color', $request->background_color ?? '#ffffff', 'color', 'appearance', 'لون الخلفيات');
        Setting::set('success_color', $request->success_color ?? '#28a745', 'color', 'appearance', 'لون النجاح');
        Setting::set('warning_color', $request->warning_color ?? '#ffc107', 'color', 'appearance', 'لون التحذير');
        Setting::set('error_color', $request->error_color ?? '#dc3545', 'color', 'appearance', 'لون الخطأ');

        // حفظ إعدادات الخطوط
        Setting::set('heading_font', $request->heading_font ?? 'Tajawal', 'text', 'appearance', 'خط العنوان');
        Setting::set('body_font', $request->body_font ?? 'Tajawal', 'text', 'appearance', 'خط النص');
        Setting::set('font_size', $request->font_size ?? 16, 'number', 'appearance', 'حجم الخط');

        // حفظ الصور الافتراضية
        if ($request->hasFile('default_course_image')) {
            $image = $request->file('default_course_image');
            $imageName = 'default_course.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('front/assets/images', $imageName, 'public');
            Setting::set('default_course_image', $imagePath, 'image', 'appearance', 'صورة افتراضية للكورسات');
        }
        if ($request->hasFile('default_student_image')) {
            $image = $request->file('default_student_image');
            $imageName = 'default_student.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('front/assets/images', $imageName, 'public');
            Setting::set('default_student_image', $imagePath, 'image', 'appearance', 'صورة افتراضية للطلاب');
        }
        if ($request->hasFile('hero_image')) {
            $image = $request->file('hero_image');
            $imageName = 'hero_image.' . $image->getClientOriginalExtension();
            // حفظ في storage أولاً
            $imagePath = $image->storeAs('front/assets/images', $imageName, 'public');
            // نسخ إلى upload_files folder (كما في باقي الملفات)
            $uploadPath = public_path('upload_files');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            copy(storage_path('app/public/' . $imagePath), $uploadPath . '/' . $imageName);
            Setting::set('hero_image', $imagePath, 'image', 'appearance', 'صورة Hero Section');
        }

        // حفظ الروابط الخارجية
        Setting::set('youtube_url', $request->youtube_url ?? '', 'url', 'social', 'رابط YouTube');
        Setting::set('telegram_url', $request->telegram_url ?? '', 'url', 'social', 'رابط Telegram');
        Setting::set('other_links', $request->other_links ?? '', 'json', 'social', 'روابط أخرى');

        // حفظ إعدادات الصفحة الرئيسية
        Setting::set('hero_additional_text', $request->hero_additional_text ?? '', 'text', 'content', 'نص إضافي في Hero Section');
        Setting::set('cta_link_1', $request->cta_link_1 ?? '', 'url', 'content', 'رابط Call-to-Action الأول');
        Setting::set('cta_text_1', $request->cta_text_1 ?? 'اشترك الآن', 'text', 'content', 'نص Call-to-Action الأول');
        Setting::set('cta_link_2', $request->cta_link_2 ?? '', 'url', 'content', 'رابط Call-to-Action الثاني');
        Setting::set('cta_text_2', $request->cta_text_2 ?? 'تعرف على المزيد', 'text', 'content', 'نص Call-to-Action الثاني');
        Setting::set('cta_link_3', $request->cta_link_3 ?? '', 'url', 'content', 'رابط Call-to-Action الثالث');
        Setting::set('cta_text_3', $request->cta_text_3 ?? 'تواصل معنا', 'text', 'content', 'نص Call-to-Action الثالث');
        Setting::set('courses_per_page', $request->courses_per_page ?? 12, 'number', 'content', 'عدد الكورسات المعروضة');

        // مسح الكاش
        Setting::clearCache();
        Cache::flush();

        return redirect()->back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }

    /**
     * إعادة تعيين الإعدادات للقيم الافتراضية
     */
    public function reset()
    {
        // التحقق من أن المستخدم Super Admin
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        // القيم الافتراضية
        $defaults = [
            'site_name' => 'منصة الأستاذ سامح صلاح - الأحياء للثانوية العامة',
            'teacher_name' => 'سامح صلاح',
            'teacher_full_name' => 'سامح صلاح',
            'subject_name' => 'الأحياء',
            'subject_description' => 'مادة الأحياء للثانوية العامة',
            'primary_color' => '#7424a9',
            'secondary_color' => '#fa896b',
            'site_description' => 'منصة تعليمية متخصصة في الأحياء للثانوية العامة',
            'site_keywords' => 'أحياء, ثانوية عامة, دروس أحياء, محاضرات, امتحانات',
            'whatsapp_number' => '+201014506018',
            'phone_number' => '01014506018',
            'contact_email' => 'info@example.com',
            'facebook_url' => 'https://facebook.com',
            'twitter_url' => 'https://twitter.com',
            'instagram_url' => 'https://instagram.com',
            'youtube_url' => 'https://youtube.com',
            'hero_quote' => '" وما رميت إذ رميت ولكن الله رمى "',
            'hero_title' => 'الأحياء مع الأستاذ سامح صلاح',
            'hero_subtitle' => 'خبير تدريس مادة الأحياء',
            'footer_text' => 'منصة تعليمية متخصصة في مادة الأحياء للثانوية العامة، نسعى لتقديم أفضل تجربة تعليمية لطلابنا.',
            'footer_copyright' => 'جميع الحقوق محفوظة',
        ];

        foreach ($defaults as $key => $value) {
            Setting::set($key, $value);
        }

        Setting::clearCache();
        Cache::flush();

        return redirect()->back()->with('success', 'تم إعادة تعيين الإعدادات للقيم الافتراضية');
    }
}
