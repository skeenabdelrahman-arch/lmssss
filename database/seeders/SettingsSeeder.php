<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'منصة الأستاذ سامح صلاح - الأحياء للثانوية العامة',
                'type' => 'text',
                'group' => 'general',
                'description' => 'اسم المنصة',
            ],
            [
                'key' => 'teacher_name',
                'value' => 'سامح صلاح',
                'type' => 'text',
                'group' => 'general',
                'description' => 'اسم المدرس',
            ],
            [
                'key' => 'teacher_full_name',
                'value' => 'سامح صلاح',
                'type' => 'text',
                'group' => 'general',
                'description' => 'الاسم الكامل للمدرس',
            ],
            [
                'key' => 'subject_name',
                'value' => 'الأحياء',
                'type' => 'text',
                'group' => 'general',
                'description' => 'اسم المادة الدراسية',
            ],
            [
                'key' => 'subject_description',
                'value' => 'مادة الأحياء للثانوية العامة',
                'type' => 'text',
                'group' => 'general',
                'description' => 'وصف المادة',
            ],

            // Appearance Settings
            [
                'key' => 'primary_color',
                'value' => '#7424a9',
                'type' => 'color',
                'group' => 'appearance',
                'description' => 'اللون الأساسي',
            ],
            [
                'key' => 'secondary_color',
                'value' => '#fa896b',
                'type' => 'color',
                'group' => 'appearance',
                'description' => 'اللون الثانوي',
            ],
            [
                'key' => 'logo_path',
                'value' => 'front/assets/images/logo.png',
                'type' => 'image',
                'group' => 'appearance',
                'description' => 'مسار اللوجو',
            ],
            [
                'key' => 'favicon_path',
                'value' => 'front/assets/images/logo.png',
                'type' => 'image',
                'group' => 'appearance',
                'description' => 'مسار Favicon',
            ],

            // SEO Settings
            [
                'key' => 'site_description',
                'value' => 'منصة تعليمية متخصصة في الأحياء للثانوية العامة - دروس، محاضرات، امتحانات',
                'type' => 'text',
                'group' => 'seo',
                'description' => 'وصف الموقع',
            ],
            [
                'key' => 'site_keywords',
                'value' => 'أحياء, ثانوية عامة, دروس أحياء, محاضرات, امتحانات, أونلاين',
                'type' => 'text',
                'group' => 'seo',
                'description' => 'الكلمات المفتاحية',
            ],

            // Contact Settings
            [
                'key' => 'whatsapp_number',
                'value' => '+201014506018',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'رقم الواتساب',
            ],
            [
                'key' => 'phone_number',
                'value' => '01014506018',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'رقم الهاتف',
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@example.com',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'البريد الإلكتروني للاتصال',
            ],

            // Social Media Settings
            [
                'key' => 'facebook_url',
                'value' => 'https://facebook.com',
                'type' => 'url',
                'group' => 'social',
                'description' => 'رابط Facebook',
            ],
            [
                'key' => 'twitter_url',
                'value' => 'https://twitter.com',
                'type' => 'url',
                'group' => 'social',
                'description' => 'رابط Twitter',
            ],
            [
                'key' => 'instagram_url',
                'value' => 'https://instagram.com',
                'type' => 'url',
                'group' => 'social',
                'description' => 'رابط Instagram',
            ],
            [
                'key' => 'youtube_url',
                'value' => 'https://youtube.com',
                'type' => 'url',
                'group' => 'social',
                'description' => 'رابط YouTube',
            ],
            [
                'key' => 'linkedin_url',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'description' => 'رابط LinkedIn',
            ],

            // Content Settings
            [
                'key' => 'hero_quote',
                'value' => '" وما رميت إذ رميت ولكن الله رمى "',
                'type' => 'text',
                'group' => 'content',
                'description' => 'اقتباس Hero Section',
            ],
            [
                'key' => 'hero_title',
                'value' => 'الأحياء مع الأستاذ سامح صلاح',
                'type' => 'text',
                'group' => 'content',
                'description' => 'عنوان Hero Section',
            ],
            [
                'key' => 'hero_subtitle',
                'value' => 'خبير تدريس مادة الأحياء',
                'type' => 'text',
                'group' => 'content',
                'description' => 'عنوان فرعي Hero Section',
            ],
            [
                'key' => 'teacher_bio',
                'value' => '',
                'type' => 'text',
                'group' => 'general',
                'description' => 'السيرة الذاتية للمدرس',
            ],
            [
                'key' => 'footer_text',
                'value' => 'منصة تعليمية متخصصة في مادة الأحياء للثانوية العامة، نسعى لتقديم أفضل تجربة تعليمية لطلابنا.',
                'type' => 'text',
                'group' => 'content',
                'description' => 'نص Footer',
            ],
            [
                'key' => 'footer_copyright',
                'value' => 'جميع الحقوق محفوظة',
                'type' => 'text',
                'group' => 'content',
                'description' => 'نص حقوق النشر',
            ],

            // Hero Statistics
            [
                'key' => 'hero_stats',
                'value' => json_encode([
                    ['number' => '15', 'label' => 'سنة خبرة', 'icon' => 'fa-calendar-check', 'enabled' => true],
                    ['number' => '95', 'label' => 'نسبة النجاح %', 'icon' => 'fa-trophy', 'enabled' => true],
                    ['number' => '5000', 'label' => 'طالب', 'icon' => 'fa-users', 'enabled' => true],
                    ['number' => '4.9', 'label' => 'تقييم', 'icon' => 'fa-star', 'enabled' => true],
                ]),
                'type' => 'json',
                'group' => 'content',
                'description' => 'إحصائيات Hero Section',
            ],

            // Features List
            [
                'key' => 'features_list',
                'value' => json_encode([
                    ['title' => 'محاضرات', 'description' => 'محاضرات فيديو عالية الجودة تغطي جميع أجزاء المنهج', 'icon' => 'fa-video', 'enabled' => true],
                    ['title' => 'المذكرات', 'description' => 'مذكرات شاملة ومنظمة يمكنك تحميلها والاستفادة منها', 'icon' => 'fa-file-pdf', 'enabled' => true],
                    ['title' => 'الامتحانات', 'description' => 'امتحانات تفاعلية لتقييم مستواك والتحضير للامتحانات', 'icon' => 'fa-clipboard-check', 'enabled' => true],
                    ['title' => 'بنك الأسئلة', 'description' => 'مجموعة ضخمة من الأسئلة والتدريبات المتنوعة', 'icon' => 'fa-database', 'enabled' => true],
                ]),
                'type' => 'json',
                'group' => 'content',
                'description' => 'قائمة المميزات',
            ],

            // Topics List
            [
                'key' => 'topics_list',
                'value' => json_encode([
                    ['name' => 'الخلية', 'icon' => 'fa-microscope', 'enabled' => true],
                    ['name' => 'الوراثة', 'icon' => 'fa-dna', 'enabled' => true],
                    ['name' => 'التنفس', 'icon' => 'fa-lungs', 'enabled' => true],
                    ['name' => 'الجهاز الهضمي', 'icon' => 'fa-stomach', 'enabled' => true],
                    ['name' => 'الجهاز الدوري', 'icon' => 'fa-heartbeat', 'enabled' => true],
                    ['name' => 'النبات', 'icon' => 'fa-leaf', 'enabled' => true],
                ]),
                'type' => 'json',
                'group' => 'content',
                'description' => 'قائمة المواضيع',
            ],

            // Subject Concepts (رموز Hero Section)
            [
                'key' => 'subject_concepts',
                'value' => json_encode([
                    ['name' => 'الخلية', 'icon' => 'fa-microscope', 'enabled' => true],
                    ['name' => 'الوراثة', 'icon' => 'fa-dna', 'enabled' => true],
                    ['name' => 'التنفس', 'icon' => 'fa-lungs', 'enabled' => true],
                    ['name' => 'الجهاز الهضمي', 'icon' => 'fa-stomach', 'enabled' => true],
                    ['name' => 'الجهاز الدوري', 'icon' => 'fa-heartbeat', 'enabled' => true],
                    ['name' => 'النبات', 'icon' => 'fa-leaf', 'enabled' => true],
                ]),
                'type' => 'json',
                'group' => 'content',
                'description' => 'مفاهيم المادة (رموز Hero Section)',
            ],

            // Grades List
            [
                'key' => 'grades_list',
                'value' => json_encode([
                    ['name' => 'الصف الأول الثانوي', 'icon' => 'fa-atom', 'enabled' => true],
                    ['name' => 'الصف الثاني الثانوي', 'icon' => 'fa-bolt', 'enabled' => true],
                    ['name' => 'الصف الثالث الثانوي', 'icon' => 'fa-rocket', 'enabled' => true],
                ]),
                'type' => 'json',
                'group' => 'content',
                'description' => 'قائمة السنوات الدراسية',
            ],

            // Benefits List
            [
                'key' => 'benefits_list',
                'value' => json_encode([
                    ['title' => 'شرح بسيط ومفهوم', 'description' => 'محتوى تعليمي واضح وسهل الفهم، مصمم خصيصاً لتبسيط المفاهيم المعقدة', 'icon' => 'fa-book-open', 'enabled' => true],
                    ['title' => 'فيديوهات برسومات توضيحية', 'description' => 'محاضرات فيديو عالية الجودة مع رسومات وتوضيحات تفاعلية', 'icon' => 'fa-video', 'enabled' => true],
                    ['title' => 'تمارين تفاعلية على الدروس', 'description' => 'تدريبات وتمارين تفاعلية بعد كل درس لتعزيز الفهم', 'icon' => 'fa-tasks', 'enabled' => true],
                    ['title' => 'مرونة كاملة في المذاكرة', 'description' => 'ادرس في أي وقت ومن أي مكان، المحتوى متاح 24/7', 'icon' => 'fa-clock', 'enabled' => true],
                    ['title' => 'اختبارات مستمرة', 'description' => 'امتحانات دورية لتقييم مستواك ومتابعة تقدمك الدراسي', 'icon' => 'fa-clipboard-check', 'enabled' => true],
                    ['title' => 'محتوى متكامل ومنظم', 'description' => 'مناهج منظمة بشكل منطقي ومتسلسل تغطي جميع أجزاء المادة', 'icon' => 'fa-layer-group', 'enabled' => true],
                    ['title' => 'تحديث مستمر حسب المنهج', 'description' => 'محتوى محدث باستمرار ليتوافق مع آخر التعديلات في المنهج', 'icon' => 'fa-sync-alt', 'enabled' => true],
                    ['title' => 'مجتمع طلابي ضخم', 'description' => 'انضم إلى آلاف الطلاب الناجحين في رحلتهم التعليمية', 'icon' => 'fa-users', 'enabled' => true],
                ]),
                'type' => 'json',
                'group' => 'content',
                'description' => 'قائمة مميزات الاشتراك',
            ],

            // Signup Grades
            [
                'key' => 'signup_grades',
                'value' => json_encode([
                    ['value' => 'أولي', 'label' => 'الصف الأول الثانوي', 'enabled' => true],
                    ['value' => 'تانية', 'label' => 'الصف الثاني الثانوي', 'enabled' => true],
                    ['value' => 'ثالثة', 'label' => 'الصف الثالث الثانوي', 'enabled' => true],
                ]),
                'type' => 'json',
                'group' => 'general',
                'description' => 'الصفوف الدراسية في التسجيل',
            ],

            // Governorates List
            [
                'key' => 'governorates_list',
                'value' => json_encode([
                    ['name' => 'القاهرة', 'enabled' => true],
                    ['name' => 'الجيزة', 'enabled' => true],
                    ['name' => 'الإسكندرية', 'enabled' => true],
                    ['name' => 'أسيوط', 'enabled' => true],
                    ['name' => 'سوهاج', 'enabled' => true],
                    ['name' => 'قنا', 'enabled' => true],
                    ['name' => 'أسوان', 'enabled' => true],
                ]),
                'type' => 'json',
                'group' => 'general',
                'description' => 'قائمة المحافظات',
            ],

            // Payment Methods
            [
                'key' => 'payment_methods',
                'value' => json_encode([
                    'online_payment' => true,
                    'activation_codes' => true,
                    'admin_activation' => true,
                    'free_courses' => true,
                ]),
                'type' => 'json',
                'group' => 'payment',
                'description' => 'وسائل الدفع والتفعيل',
            ],

            // Legal Pages
            [
                'key' => 'privacy_policy',
                'value' => '',
                'type' => 'text',
                'group' => 'legal',
                'description' => 'سياسة الخصوصية',
            ],
            [
                'key' => 'terms_of_service',
                'value' => '',
                'type' => 'text',
                'group' => 'legal',
                'description' => 'شروط الاستخدام',
            ],

            // Security Settings
            [
                'key' => 'session_lifetime',
                'value' => '120',
                'type' => 'number',
                'group' => 'security',
                'description' => 'مدة انتهاء الجلسة (بالدقائق)',
            ],
            [
                'key' => 'login_attempts',
                'value' => '5',
                'type' => 'number',
                'group' => 'security',
                'description' => 'عدد محاولات تسجيل الدخول المسموحة',
            ],
            [
                'key' => 'login_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'security',
                'description' => 'تفعيل تسجيل الدخول',
            ],

            // Additional Colors
            [
                'key' => 'text_color',
                'value' => '#333333',
                'type' => 'color',
                'group' => 'appearance',
                'description' => 'لون النصوص الأساسي',
            ],
            [
                'key' => 'background_color',
                'value' => '#ffffff',
                'type' => 'color',
                'group' => 'appearance',
                'description' => 'لون الخلفيات الأساسي',
            ],
            [
                'key' => 'success_color',
                'value' => '#28a745',
                'type' => 'color',
                'group' => 'appearance',
                'description' => 'لون النجاح',
            ],
            [
                'key' => 'warning_color',
                'value' => '#ffc107',
                'type' => 'color',
                'group' => 'appearance',
                'description' => 'لون التحذير',
            ],
            [
                'key' => 'error_color',
                'value' => '#dc3545',
                'type' => 'color',
                'group' => 'appearance',
                'description' => 'لون الخطأ',
            ],

            // Font Settings
            [
                'key' => 'heading_font',
                'value' => 'Tajawal',
                'type' => 'text',
                'group' => 'appearance',
                'description' => 'خط العنوان',
            ],
            [
                'key' => 'body_font',
                'value' => 'Tajawal',
                'type' => 'text',
                'group' => 'appearance',
                'description' => 'خط النص',
            ],
            [
                'key' => 'font_size',
                'value' => '16',
                'type' => 'number',
                'group' => 'appearance',
                'description' => 'حجم الخط الأساسي',
            ],

            // Default Images
            [
                'key' => 'default_course_image',
                'value' => '',
                'type' => 'image',
                'group' => 'appearance',
                'description' => 'صورة افتراضية للكورسات',
            ],
            [
                'key' => 'default_student_image',
                'value' => '',
                'type' => 'image',
                'group' => 'appearance',
                'description' => 'صورة افتراضية للطلاب',
            ],
            [
                'key' => 'hero_image',
                'value' => '',
                'type' => 'image',
                'group' => 'appearance',
                'description' => 'صورة Hero Section',
            ],

            // External Links
            [
                'key' => 'telegram_url',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'description' => 'رابط Telegram',
            ],
            [
                'key' => 'other_links',
                'value' => '',
                'type' => 'json',
                'group' => 'social',
                'description' => 'روابط أخرى',
            ],

            // Homepage Settings
            [
                'key' => 'hero_additional_text',
                'value' => '',
                'type' => 'text',
                'group' => 'content',
                'description' => 'نص إضافي في Hero Section',
            ],
            [
                'key' => 'cta_link_1',
                'value' => '',
                'type' => 'url',
                'group' => 'content',
                'description' => 'رابط Call-to-Action الأول',
            ],
            [
                'key' => 'cta_text_1',
                'value' => 'اشترك الآن',
                'type' => 'text',
                'group' => 'content',
                'description' => 'نص Call-to-Action الأول',
            ],
            [
                'key' => 'cta_link_2',
                'value' => '',
                'type' => 'url',
                'group' => 'content',
                'description' => 'رابط Call-to-Action الثاني',
            ],
            [
                'key' => 'cta_text_2',
                'value' => 'تعرف على المزيد',
                'type' => 'text',
                'group' => 'content',
                'description' => 'نص Call-to-Action الثاني',
            ],
            [
                'key' => 'courses_per_page',
                'value' => '12',
                'type' => 'number',
                'group' => 'content',
                'description' => 'عدد الكورسات المعروضة في الصفحة الرئيسية',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
