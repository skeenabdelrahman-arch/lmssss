<?php

if (!function_exists('setting')) {
    /**
     * Get setting value by key
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        return \App\Models\Setting::get($key, $default);
    }
}

if (!function_exists('site_name')) {
    /**
     * Get site name
     * 
     * @return string
     */
    function site_name()
    {
        return setting('site_name', config('seo.site_title', 'منصة التعليم'));
    }
}

if (!function_exists('teacher_name')) {
    /**
     * Get teacher name
     * 
     * @return string
     */
    function teacher_name()
    {
        return setting('teacher_name', 'سامح صلاح');
    }
}

if (!function_exists('subject_name')) {
    /**
     * Get subject name
     * 
     * @return string
     */
    function subject_name()
    {
        return setting('subject_name', 'الأحياء');
    }
}

if (!function_exists('primary_color')) {
    /**
     * Get primary color
     * 
     * @return string
     */
    function primary_color()
    {
        return setting('primary_color', '#7424a9');
    }
}

if (!function_exists('secondary_color')) {
    /**
     * Get secondary color
     * 
     * @return string
     */
    function secondary_color()
    {
        return setting('secondary_color', '#fa896b');
    }
}

if (!function_exists('logo_path')) {
    /**
     * Get logo path
     * 
     * @return string
     */
    function logo_path()
    {
        return setting('logo_path', 'front/assets/images/logo.png');
    }
}

if (!function_exists('whatsapp_number')) {
    /**
     * Get WhatsApp number
     * 
     * @return string
     */
    function whatsapp_number()
    {
        return setting('whatsapp_number', '');
    }
}

if (!function_exists('phone_number')) {
    /**
     * Get phone number
     * 
     * @return string
     */
    function phone_number()
    {
        return setting('phone_number', '');
    }
}

if (!function_exists('contact_email')) {
    /**
     * Get contact email
     * 
     * @return string
     */
    function contact_email()
    {
        return setting('contact_email', '');
    }
}

if (!function_exists('social_url')) {
    /**
     * Get social media URL
     * 
     * @param string $platform
     * @return string
     */
    function social_url($platform)
    {
        return setting($platform . '_url', '');
    }
}

if (!function_exists('hero_quote')) {
    /**
     * Get hero quote
     * 
     * @return string
     */
    function hero_quote()
    {
        return setting('hero_quote', '');
    }
}

if (!function_exists('hero_title')) {
    /**
     * Get hero title
     * 
     * @return string
     */
    function hero_title()
    {
        return setting('hero_title', '');
    }
}

if (!function_exists('hero_subtitle')) {
    /**
     * Get hero subtitle
     * 
     * @return string
     */
    function hero_subtitle()
    {
        return setting('hero_subtitle', '');
    }
}

if (!function_exists('footer_text')) {
    /**
     * Get footer text
     * 
     * @return string
     */
    function footer_text()
    {
        return setting('footer_text', '');
    }
}

if (!function_exists('footer_copyright')) {
    /**
     * Get footer copyright text
     * 
     * @return string
     */
    function footer_copyright()
    {
        return setting('footer_copyright', 'جميع الحقوق محفوظة') . ' © ' . date('Y');
    }
}

if (!function_exists('teacher_bio')) {
    /**
     * Get teacher biography
     * 
     * @return string
     */
    function teacher_bio()
    {
        return setting('teacher_bio', '');
    }
}

if (!function_exists('teacher_full_name')) {
    /**
     * Get teacher full name
     * 
     * @return string
     */
    function teacher_full_name()
    {
        return setting('teacher_full_name', teacher_name());
    }
}

if (!function_exists('hero_stats')) {
    /**
     * Get hero statistics
     * 
     * @return array
     */
    function hero_stats()
    {
        $stats = setting('hero_stats', '[]');
        $decoded = json_decode($stats, true);
        return is_array($decoded) ? array_filter($decoded, function($stat) {
            return isset($stat['enabled']) && $stat['enabled'];
        }) : [];
    }
}

if (!function_exists('features_list')) {
    /**
     * Get features list
     * 
     * @return array
     */
    function features_list()
    {
        $features = setting('features_list', '[]');
        $decoded = json_decode($features, true);
        return is_array($decoded) ? array_filter($decoded, function($feature) {
            return isset($feature['enabled']) && $feature['enabled'];
        }) : [];
    }
}

if (!function_exists('topics_list')) {
    /**
     * Get topics list
     * 
     * @return array
     */
    function topics_list()
    {
        $topics = setting('topics_list', '[]');
        $decoded = json_decode($topics, true);
        return is_array($decoded) ? array_filter($decoded, function($topic) {
            return isset($topic['enabled']) && $topic['enabled'];
        }) : [];
    }
}

if (!function_exists('subject_concepts')) {
    /**
     * Get subject concepts (رموز Hero Section)
     * 
     * @return array
     */
    function subject_concepts()
    {
        $concepts = setting('subject_concepts', '[]');
        $decoded = json_decode($concepts, true);
        return is_array($decoded) ? array_filter($decoded, function($concept) {
            return isset($concept['enabled']) && $concept['enabled'];
        }) : [];
    }
}

if (!function_exists('grades_list')) {
    /**
     * Get grades list
     * 
     * @return array
     */
    function grades_list()
    {
        $grades = setting('grades_list', '[]');
        $decoded = json_decode($grades, true);
        return is_array($decoded) ? array_filter($decoded, function($grade) {
            return isset($grade['enabled']) && $grade['enabled'];
        }) : [];
    }
}

if (!function_exists('benefits_list')) {
    /**
     * Get benefits list
     * 
     * @return array
     */
    function benefits_list()
    {
        $benefits = setting('benefits_list', '[]');
        $decoded = json_decode($benefits, true);
        return is_array($decoded) ? array_filter($decoded, function($benefit) {
            return isset($benefit['enabled']) && $benefit['enabled'];
        }) : [];
    }
}

if (!function_exists('signup_grades')) {
    /**
     * Get signup grades list
     * 
     * @return array
     */
    function signup_grades()
    {
        $grades = setting('signup_grades', '[]');
        $decoded = json_decode($grades, true);
        return is_array($decoded) ? array_filter($decoded, function($grade) {
            return isset($grade['enabled']) && $grade['enabled'];
        }) : [];
    }
}

if (!function_exists('governorates_list')) {
    /**
     * Get governorates list
     * 
     * @return array
     */
    function governorates_list()
    {
        $governorates = setting('governorates_list', '[]');
        $decoded = json_decode($governorates, true);
        return is_array($decoded) ? array_filter($decoded, function($gov) {
            return isset($gov['enabled']) && $gov['enabled'];
        }) : [];
    }
}

if (!function_exists('payment_methods')) {
    /**
     * Get payment methods settings
     * 
     * @return array
     */
    function payment_methods()
    {
        $methods = setting('payment_methods', '[]');
        $decoded = json_decode($methods, true);
        if (is_array($decoded) && !empty($decoded)) {
            return $decoded;
        }
        // Default values
        return [
            'online_payment' => true,
            'activation_codes' => true,
            'admin_activation' => true,
            'free_courses' => true,
        ];
    }
}

if (!function_exists('is_payment_method_enabled')) {
    /**
     * Check if a payment method is enabled
     * 
     * @param string $method
     * @return bool
     */
    function is_payment_method_enabled($method)
    {
        $methods = payment_methods();
        return isset($methods[$method]) && $methods[$method] === true;
    }
}

if (!function_exists('privacy_policy')) {
    /**
     * Get privacy policy text
     * 
     * @return string
     */
    function privacy_policy()
    {
        return setting('privacy_policy', '');
    }
}

if (!function_exists('terms_of_service')) {
    /**
     * Get terms of service text
     * 
     * @return string
     */
    function terms_of_service()
    {
        return setting('terms_of_service', '');
    }
}

if (!function_exists('session_lifetime')) {
    /**
     * Get session lifetime in minutes
     * Returns a very large value (1 year = 525600 minutes) for infinite sessions
     * 
     * @return int
     */
    function session_lifetime()
    {
        $lifetime = (int) setting('session_lifetime', 525600);
        // إذا كانت القيمة 0 أو أقل من 60 دقيقة، استخدم سنة واحدة (لا نهائي عملياً)
        if ($lifetime <= 0 || $lifetime < 60) {
            return 525600; // سنة واحدة = 525600 دقيقة
        }
        return $lifetime;
    }
}

if (!function_exists('login_attempts')) {
    /**
     * Get max login attempts
     * 
     * @return int
     */
    function login_attempts()
    {
        return (int) setting('login_attempts', 5);
    }
}

if (!function_exists('is_login_enabled')) {
    /**
     * Check if login is enabled
     * 
     * @return bool
     */
    function is_login_enabled()
    {
        return setting('login_enabled', '1') === '1';
    }
}

if (!function_exists('text_color')) {
    /**
     * Get text color
     * 
     * @return string
     */
    function text_color()
    {
        return setting('text_color', '#333333');
    }
}

if (!function_exists('background_color')) {
    /**
     * Get background color
     * 
     * @return string
     */
    function background_color()
    {
        return setting('background_color', '#ffffff');
    }
}

if (!function_exists('success_color')) {
    /**
     * Get success color
     * 
     * @return string
     */
    function success_color()
    {
        return setting('success_color', '#28a745');
    }
}

if (!function_exists('warning_color')) {
    /**
     * Get warning color
     * 
     * @return string
     */
    function warning_color()
    {
        return setting('warning_color', '#ffc107');
    }
}

if (!function_exists('error_color')) {
    /**
     * Get error color
     * 
     * @return string
     */
    function error_color()
    {
        return setting('error_color', '#dc3545');
    }
}

if (!function_exists('heading_font')) {
    /**
     * Get heading font
     * 
     * @return string
     */
    function heading_font()
    {
        return setting('heading_font', 'Tajawal');
    }
}

if (!function_exists('body_font')) {
    /**
     * Get body font
     * 
     * @return string
     */
    function body_font()
    {
        return setting('body_font', 'Tajawal');
    }
}

if (!function_exists('font_size')) {
    /**
     * Get font size
     * 
     * @return int
     */
    function font_size()
    {
        return (int) setting('font_size', 16);
    }
}

if (!function_exists('default_course_image')) {
    /**
     * Get default course image path
     * 
     * @return string
     */
    function default_course_image()
    {
        return setting('default_course_image', '');
    }
}

if (!function_exists('default_student_image')) {
    /**
     * Get default student image path
     * 
     * @return string
     */
    function default_student_image()
    {
        return setting('default_student_image', '');
    }
}

if (!function_exists('hero_image')) {
    /**
     * Get hero image path
     * 
     * @return string
     */
    function hero_image()
    {
        $path = setting('hero_image', '');
        if (empty($path)) {
            return '';
        }
        // إرجاع المسار الكامل للصورة
        // الصورة محفوظة في storage/app/public/front/assets/images/
        // والـ URL يجب أن يكون storage/front/assets/images/...
        return asset('storage/' . $path);
    }
}

if (!function_exists('telegram_url')) {
    /**
     * Get Telegram URL
     * 
     * @return string
     */
    function telegram_url()
    {
        return setting('telegram_url', '');
    }
}

if (!function_exists('other_links')) {
    /**
     * Get other links as array
     * 
     * @return array
     */
    function other_links()
    {
        $links = setting('other_links', '{}');
        $decoded = json_decode($links, true);
        return is_array($decoded) ? $decoded : [];
    }
}

if (!function_exists('hero_additional_text')) {
    /**
     * Get hero additional text
     * 
     * @return string
     */
    function hero_additional_text()
    {
        return setting('hero_additional_text', '');
    }
}

if (!function_exists('cta_link_1')) {
    /**
     * Get first CTA link
     * 
     * @return string
     */
    function cta_link_1()
    {
        return setting('cta_link_1', '');
    }
}

if (!function_exists('cta_text_1')) {
    /**
     * Get first CTA text
     * 
     * @return string
     */
    function cta_text_1()
    {
        return setting('cta_text_1', 'اشترك الآن');
    }
}

if (!function_exists('cta_link_2')) {
    /**
     * Get second CTA link
     * 
     * @return string
     */
    function cta_link_2()
    {
        return setting('cta_link_2', '');
    }
}

if (!function_exists('cta_text_2')) {
    /**
     * Get second CTA text
     * 
     * @return string
     */
    function cta_text_2()
    {
        return setting('cta_text_2', 'تعرف على المزيد');
    }
}

if (!function_exists('cta_link_3')) {
    /**
     * Get third CTA link
     * 
     * @return string
     */
    function cta_link_3()
    {
        return setting('cta_link_3', '');
    }
}

if (!function_exists('cta_text_3')) {
    /**
     * Get third CTA text
     * 
     * @return string
     */
    function cta_text_3()
    {
        return setting('cta_text_3', 'تواصل معنا');
    }
}


if (!function_exists('courses_per_page')) {
    /**
     * Get courses per page
     * 
     * @return int
     */
    function courses_per_page()
    {
        return (int) setting('courses_per_page', 12);
    }
}

if (!function_exists('hexToRgba')) {
    /**
     * Convert HEX color to RGBA
     * 
     * @param string $hex
     * @param float $alpha
     * @return string
     */
    function hexToRgba($hex, $alpha = 1.0)
    {
        $hex = str_replace('#', '', $hex);
        $length = strlen($hex);
        
        // Convert short hex to full hex (e.g., #fff to #ffffff)
        if ($length == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return "rgba($r, $g, $b, $alpha)";
    }
}

if (!function_exists('teacher_image_path')) {
    /**
     * Get teacher image path
     * 
     * @return string
     */
    function teacher_image_path()
    {
        $path = setting('teacher_image', '');
        if ($path) {
            // Check if file exists in upload_files
            $uploadPath = 'upload_files/' . basename($path);
            if (file_exists(public_path($uploadPath))) {
                return $uploadPath;
            }
            // Return original path
            return $path;
        }
        // Fallback to logo
        return logo_path();
    }
}

if (!function_exists('about_teacher_bio')) {
    /**
     * Get about teacher bio text
     * 
     * @return string
     */
    function about_teacher_bio()
    {
        return setting('about_teacher_bio', '');
    }
}

if (!function_exists('about_teacher_qualifications')) {
    /**
     * Get about teacher qualifications
     * 
     * @return array
     */
    function about_teacher_qualifications()
    {
        $qualifications = setting('about_teacher_qualifications', '[]');
        $decoded = json_decode($qualifications, true);
        return is_array($decoded) ? $decoded : [];
    }
}

if (!function_exists('about_teacher_stats')) {
    /**
     * Get about teacher stats
     * 
     * @return array
     */
    function about_teacher_stats()
    {
        $stats = setting('about_teacher_stats', '[]');
        $decoded = json_decode($stats, true);
        return is_array($decoded) ? $decoded : [];
    }
}

if (!function_exists('about_teacher_methods')) {
    /**
     * Get about teacher teaching methods
     * 
     * @return array
     */
    function about_teacher_methods()
    {
        $methods = setting('about_teacher_methods', '[]');
        $decoded = json_decode($methods, true);
        return is_array($decoded) ? $decoded : [];
    }
}

if (!function_exists('about_teacher_reviews')) {
    /**
     * Get about teacher student reviews
     * 
     * @return array
     */
    function about_teacher_reviews()
    {
        $reviews = setting('about_teacher_reviews', '[]');
        $decoded = json_decode($reviews, true);
        return is_array($decoded) ? $decoded : [];
    }
}

if (!function_exists('about_teacher_achievements')) {
    /**
     * Get about teacher achievements (image + caption)
     *
     * @return array
     */
    function about_teacher_achievements()
    {
        $items = setting('about_teacher_achievements', '[]');
        $decoded = json_decode($items, true);
        return is_array($decoded) ? $decoded : [];
    }
}

