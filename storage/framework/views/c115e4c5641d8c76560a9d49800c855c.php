<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="index,follow">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta property="og:url" content="<?php echo e(url('/')); ?>" />
    <meta property="og:image" content="<?php echo e(asset(logo_path())); ?>" />
    <link rel="shortcut icon" href="<?php echo e(asset(logo_path())); ?>" type="image/x-icon">
    
    
    <title><?php echo $__env->yieldContent('title', site_name()); ?></title>
    
    <!-- Dynamic Fonts -->
    <?php if(heading_font() == 'Cairo'): ?>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <?php elseif(heading_font() == 'Almarai'): ?>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet">
    <?php elseif(heading_font() == 'Amiri'): ?>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <?php endif; ?>
    
    <?php if(body_font() != heading_font()): ?>
        <?php if(body_font() == 'Cairo'): ?>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
        <?php elseif(body_font() == 'Almarai'): ?>
        <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet">
        <?php elseif(body_font() == 'Amiri'): ?>
        <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
        <?php endif; ?>
    <?php endif; ?>
    
    <style>
        body {
            font-family: var(--body-font);
            font-size: var(--font-size);
            color: var(--text-color);
            background-color: var(--background-color);
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--heading-font);
        }
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }
        .btn-danger {
            background-color: var(--error-color);
            border-color: var(--error-color);
        }
        .alert-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        .alert-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }
        .alert-danger {
            background-color: var(--error-color);
            border-color: var(--error-color);
        }
    </style>
    <meta name="description" content="<?php echo $__env->yieldContent('description', setting('site_description', config('seo.site_description', 'منصة تعليمية متخصصة'))); ?>" />
    <meta name="keywords" content="<?php echo $__env->yieldContent('keywords', setting('site_keywords', config('seo.site_keywords', 'تعليم, أونلاين'))); ?>" />
    
    
    <meta property="og:title" content="<?php echo $__env->yieldContent('title', config('seo.site_title')); ?>" />
    <meta property="og:description" content="<?php echo $__env->yieldContent('description', config('seo.site_description')); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:locale" content="ar_EG" />
    
    
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?php echo $__env->yieldContent('title', site_name()); ?>" />
    <meta name="twitter:description" content="<?php echo $__env->yieldContent('description', setting('site_description', config('seo.site_description', ''))); ?>" />
    
    
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "EducationalOrganization",
        "name": "<?php echo e(site_name()); ?>",
        "description": "<?php echo e(setting('site_description', config('seo.site_description', ''))); ?>",
        "url": "<?php echo e(url('/')); ?>",
        "logo": "<?php echo e(asset(logo_path())); ?>"
    }
    </script>
    
    
    <?php if(config('seo.google_analytics')): ?>
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo e(config('seo.google_analytics')); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo e(config('seo.google_analytics')); ?>');
    </script>
    <?php endif; ?>
    
    
    <?php if(config('seo.facebook_pixel')): ?>
    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?php echo e(config('seo.facebook_pixel')); ?>');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=<?php echo e(config('seo.facebook_pixel')); ?>&ev=PageView&noscript=1"
    /></noscript>
    <?php endif; ?>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Admin Enhancements CSS (for dark mode and other features) -->
    <link rel="stylesheet" href="<?php echo e(URL::asset('css/admin-enhancements.css')); ?>">
    <!-- Modern Sidebar CSS -->
    <link rel="stylesheet" href="<?php echo e(URL::asset('css/modern-sidebar.css')); ?>">
    
    <!-- Lazy Loading JS -->
    <script src="<?php echo e(URL::asset('js/lazy-loading.js')); ?>" defer></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* ============================================
           CSS Variables - Dynamic Colors
           ============================================ */
        :root {
            --primary-color: <?php echo e(primary_color()); ?>;
            --secondary-color: <?php echo e(secondary_color()); ?>;
            --text-color: <?php echo e(text_color()); ?>;
            --background-color: <?php echo e(background_color()); ?>;
            --success-color: <?php echo e(success_color()); ?>;
            --warning-color: <?php echo e(warning_color()); ?>;
            --error-color: <?php echo e(error_color()); ?>;
            --heading-font: '<?php echo e(heading_font()); ?>', sans-serif;
            --body-font: '<?php echo e(body_font()); ?>', sans-serif;
            --font-size: <?php echo e(font_size()); ?>px;
        }

        /* ============================================
           Dark Mode - New Design (Front-end)
           خلفية: أسود | كروت: رمادي | عناوين: أبيض | نصوص: بنفسجي
           ============================================ */
        
        /* CSS Variables for Dark Mode */
        [data-theme="dark"],
        html[data-theme="dark"],
        body[data-theme="dark"] {
            --primary-color: <?php echo e(primary_color()); ?> !important;
            --secondary-color: <?php echo e(secondary_color()); ?> !important;
            --primary-light: #7A35FF !important;
            --bg-primary: #000000 !important;        /* خلفية أسود */
            --bg-secondary: #2a2a2a !important;     /* كروت رمادي */
            --bg-tertiary: #3a3a3a !important;      /* كروت رمادي فاتح */
            --text-primary: #9B5FFF !important;     /* نصوص بنفسجي */
            --text-heading: #FFFFFF !important;     /* عناوين أبيض */
            --border-color: #404040 !important;
        }
        
        /* Body - Black Background */
        [data-theme="dark"] body,
        body[data-theme="dark"],
        html[data-theme="dark"] body,
        html[data-theme="dark"] {
            background: #000000 !important;
            color: #9B5FFF !important;
        }
        
        /* All Text - Purple */
        [data-theme="dark"] p,
        [data-theme="dark"] span:not(.badge):not(.btn):not(.icon),
        [data-theme="dark"] div:not(.btn):not(.badge):not(.alert):not(.card):not(.modern-card):not(.stats-card):not(.notification-card):not(.course-card):not(.feature-card):not(.lecture-card):not(.reason-card),
        [data-theme="dark"] td,
        [data-theme="dark"] th,
        [data-theme="dark"] li,
        [data-theme="dark"] label,
        [data-theme="dark"] .text-primary,
        [data-theme="dark"] .text-muted,
        [data-theme="dark"] .text-secondary {
            color: #9B5FFF !important;
        }
        
        /* All Headings - White */
        [data-theme="dark"] h1,
        [data-theme="dark"] h2,
        [data-theme="dark"] h3,
        [data-theme="dark"] h4,
        [data-theme="dark"] h5,
        [data-theme="dark"] h6,
        [data-theme="dark"] .page-header h1,
        [data-theme="dark"] .hero-content h1 {
            color: #FFFFFF !important;
        }
        
        [data-theme="dark"] .main-header,
        [data-theme="dark"] .main-header {
            background: var(--bg-secondary) !important;
            border-bottom: 1px solid var(--border-color) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3) !important;
        }

        /* Sidebar Dark Mode Styles */
        [data-theme="dark"] .modern-sidebar {
            background: linear-gradient(180deg, #1a1a1f 0%, #25252d 100%) !important;
            box-shadow: -2px 0 20px rgba(0, 0, 0, 0.5) !important;
        }

        [data-theme="dark"] .sidebar-logo {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
        }

        [data-theme="dark"] .sidebar-profile {
            background: rgba(155, 95, 255, 0.05) !important;
            border-bottom: 1px solid var(--border-color) !important;
        }

        [data-theme="dark"] .sidebar-profile-name {
            color: var(--text-heading) !important;
        }

        [data-theme="dark"] .sidebar-profile-role {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .sidebar-nav-link {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .sidebar-nav-link:hover {
            background: linear-gradient(135deg, rgba(155, 95, 255, 0.12), rgba(250, 137, 107, 0.12)) !important;
            color: var(--text-heading) !important;
        }

        [data-theme="dark"] .sidebar-nav-link.active {
            background: linear-gradient(135deg, rgba(155, 95, 255, 0.18), rgba(250, 137, 107, 0.18)) !important;
            color: var(--text-heading) !important;
        }

        [data-theme="dark"] .sidebar-dropdown-toggle {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .sidebar-dropdown-toggle:hover,
        [data-theme="dark"] .sidebar-dropdown-toggle.active {
            background: linear-gradient(135deg, rgba(155, 95, 255, 0.12), rgba(250, 137, 107, 0.12)) !important;
            color: var(--text-heading) !important;
        }

        [data-theme="dark"] .sidebar-dropdown-link {
            color: var(--text-primary) !important;
        }

        [data-theme="dark"] .sidebar-dropdown-link:hover {
            background: rgba(155, 95, 255, 0.08) !important;
            color: var(--text-heading) !important;
        }

        [data-theme="dark"] .sidebar-footer {
            background: rgba(155, 95, 255, 0.05) !important;
            border-top: 1px solid var(--border-color) !important;
        }

        [data-theme="dark"] .sidebar-dark-mode {
            background: rgba(155, 95, 255, 0.08) !important;
        }

        [data-theme="dark"] .sidebar-dark-mode span {
            color: var(--text-heading) !important;
        }

        [data-theme="dark"] .top-mini-header {
            background: var(--bg-secondary) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3) !important;
        }

        [data-theme="dark"] .sidebar-footer-btn.secondary {
            background: rgba(155, 95, 255, 0.1) !important;
            color: var(--text-heading) !important;
            border-color: var(--primary-color) !important;
        }

        [data-theme="dark"] .sidebar-footer-btn.secondary:hover {
            background: var(--primary-color) !important;
        }
        
        [data-theme="dark"] .main-header.scrolled {
            background: var(--bg-secondary) !important;
        }
        
        [data-theme="dark"] .main-header .nav-menu a,
        [data-theme="dark"] .main-header .header-buttons a,
        [data-theme="dark"] .header-buttons a,
        [data-theme="dark"] .nav-menu li a {
            color: var(--text-primary) !important;
        }
        
        [data-theme="dark"] .main-header .nav-menu a:hover,
        [data-theme="dark"] .nav-menu li a:hover {
            color: var(--primary-light) !important;
        }
        
        [data-theme="dark"] .main-header .nav-menu a.active,
        [data-theme="dark"] .nav-menu a.active {
            color: var(--primary-light) !important;
        }
        
        [data-theme="dark"] .main-header .nav-menu a.active::after,
        [data-theme="dark"] .nav-menu a.active::after {
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color)) !important;
        }
        
        [data-theme="dark"] .mobile-menu {
            background: var(--bg-secondary) !important;
            border-top: 1px solid var(--border-color) !important;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3) !important;
        }
        
        [data-theme="dark"] .mobile-menu ul li a {
            color: var(--text-primary) !important;
        }
        
        [data-theme="dark"] .mobile-menu ul li a:hover {
            background: var(--bg-tertiary) !important;
            color: var(--primary-light) !important;
        }
                [data-theme="dark"] .dropdown-menu-custom {
            background: var(--bg-secondary) !important;
            border: 1px solid var(--border-color) !important;
        }
        
        [data-theme="dark"] .dropdown-menu-custom li a {
            color: var(--text-primary) !important;
        }
        
        [data-theme="dark"] .dropdown-menu-custom li a:hover {
            background: var(--bg-tertiary) !important;
            color: var(--primary-light) !important;
        }

        [data-theme="dark"] .btn-header {
            background: var(--bg-tertiary) !important;
            color: var(--text-primary) !important;
            border-color: var(--border-color) !important;
        }
        
        [data-theme="dark"] .btn-header:hover {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
            border-color: transparent !important;
            color: white !important;
        }
        
        [data-theme="dark"] .btn-header.primary {
            background: linear-gradient(135deg, #9B5FFF, #7A35FF) !important;
            color: white !important;
        }
        
        [data-theme="dark"] .btn-header.primary:hover {
            background: linear-gradient(135deg, #7A35FF, #9B5FFF) !important;
            box-shadow: 0 5px 20px rgba(122, 53, 255, 0.4) !important;
        }
        
        [data-theme="dark"] .profile-img {
            border-color: var(--primary-color) !important;
        }
        
        [data-theme="dark"] .profile-img:hover {
            border-color: var(--primary-light) !important;
        }
        
        [data-theme="dark"] main {
            background: var(--bg-primary) !important;
            color: var(--text-primary) !important;
        }
        
        [data-theme="dark"] .modern-card,
        [data-theme="dark"] .profile-section,
        [data-theme="dark"] .courses-section,
        [data-theme="dark"] .course-details-section,
        [data-theme="dark"] .profile-tabs,
        [data-theme="dark"] .tab-content,
        [data-theme="dark"] .hero-section {
            background: #1a1a1f !important;
            color: #FFFFFF !important;
        }
        
        [data-theme="dark"] .course-card,
        [data-theme="dark"] .feature-card,
        [data-theme="dark"] .lecture-card,
        [data-theme="dark"] .reason-card {
            background: #FFFFFF !important;
            color: #9B5FFF !important;
            border-color: #2a2a35 !important;
        }
        
        [data-theme="dark"] .course-card *,
        [data-theme="dark"] .feature-card *,
        [data-theme="dark"] .lecture-card *,
        [data-theme="dark"] .reason-card *,
        [data-theme="dark"] .course-card p,
        [data-theme="dark"] .feature-card p,
        [data-theme="dark"] .lecture-card p,
        [data-theme="dark"] .reason-card p,
        [data-theme="dark"] .course-card span,
        [data-theme="dark"] .feature-card span,
        [data-theme="dark"] .lecture-card span,
        [data-theme="dark"] .reason-card span,
        [data-theme="dark"] .course-card div,
        [data-theme="dark"] .feature-card div,
        [data-theme="dark"] .lecture-card div,
        [data-theme="dark"] .reason-card div {
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .course-card h1,
        [data-theme="dark"] .course-card h2,
        [data-theme="dark"] .course-card h3,
        [data-theme="dark"] .course-card h4,
        [data-theme="dark"] .course-card h5,
        [data-theme="dark"] .course-card h6,
        [data-theme="dark"] .feature-card h1,
        [data-theme="dark"] .feature-card h2,
        [data-theme="dark"] .feature-card h3,
        [data-theme="dark"] .feature-card h4,
        [data-theme="dark"] .feature-card h5,
        [data-theme="dark"] .feature-card h6,
        [data-theme="dark"] .lecture-card h1,
        [data-theme="dark"] .lecture-card h2,
        [data-theme="dark"] .lecture-card h3,
        [data-theme="dark"] .lecture-card h4,
        [data-theme="dark"] .lecture-card h5,
        [data-theme="dark"] .lecture-card h6,
        [data-theme="dark"] .reason-card h1,
        [data-theme="dark"] .reason-card h2,
        [data-theme="dark"] .reason-card h3,
        [data-theme="dark"] .reason-card h4,
        [data-theme="dark"] .reason-card h5,
        [data-theme="dark"] .reason-card h6 {
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .course-card:hover,
        [data-theme="dark"] .feature-card:hover,
        [data-theme="dark"] .lecture-card:hover {
            border-color: #9B5FFF !important;
            box-shadow: 0 15px 50px rgba(122, 53, 255, 0.3) !important;
        }
        
        /* Cards with white background - text color #9B5FFF */
        [data-theme="dark"] .card,
        [data-theme="dark"] .card-body {
            background: #FFFFFF !important;
        }
        
        [data-theme="dark"] .card *,
        [data-theme="dark"] .card-body *,
        [data-theme="dark"] .card p,
        [data-theme="dark"] .card span,
        [data-theme="dark"] .card div,
        [data-theme="dark"] .card td,
        [data-theme="dark"] .card th,
        [data-theme="dark"] .card-body p,
        [data-theme="dark"] .card-body span,
        [data-theme="dark"] .card-body div,
        [data-theme="dark"] .card-body td,
        [data-theme="dark"] .card-body th {
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .card h1,
        [data-theme="dark"] .card h2,
        [data-theme="dark"] .card h3,
        [data-theme="dark"] .card h4,
        [data-theme="dark"] .card h5,
        [data-theme="dark"] .card h6,
        [data-theme="dark"] .card-body h1,
        [data-theme="dark"] .card-body h2,
        [data-theme="dark"] .card-body h3,
        [data-theme="dark"] .card-body h4,
        [data-theme="dark"] .card-body h5,
        [data-theme="dark"] .card-body h6 {
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .course-card:hover,
        [data-theme="dark"] .feature-card:hover,
        [data-theme="dark"] .lecture-card:hover {
            border-color: var(--primary-color) !important;
            box-shadow: 0 15px 50px rgba(157, 78, 221, 0.3) !important;
        }
        
        [data-theme="dark"] .course-icon {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
        }
        
        [data-theme="dark"] .footer-content,
        [data-theme="dark"] .main-footer {
            background: linear-gradient(135deg, #1a1a1f 0%, #25252d 100%) !important;
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .footer-links a {
            color: rgba(228, 230, 235, 0.8) !important;
        }
        
        [data-theme="dark"] .footer-links a:hover {
            color: var(--secondary-color) !important;
        }
        
        [data-theme="dark"] .social-icons a {
            background: rgba(157, 78, 221, 0.2) !important;
        }
        
        [data-theme="dark"] .social-icons a:hover {
            background: var(--secondary-color) !important;
        }
        
        [data-theme="dark"] .preloader {
            background: var(--bg-primary) !important;
        }
        
        [data-theme="dark"] h1,
        [data-theme="dark"] h2,
        [data-theme="dark"] h3,
        [data-theme="dark"] h4,
        [data-theme="dark"] h5,
        [data-theme="dark"] h6,
        [data-theme="dark"] .page-header h1,
        [data-theme="dark"] .hero-content h1 {
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .hero-content .quote {
            color: var(--secondary-color) !important;
        }
        
        [data-theme="dark"] .hero-content .subtitle {
            color: var(--primary-light) !important;
        }
        
        [data-theme="dark"] .btn-hero.primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
            box-shadow: 0 5px 20px rgba(157, 78, 221, 0.4) !important;
        }
        
        [data-theme="dark"] .btn-hero.primary:hover {
            box-shadow: 0 8px 30px rgba(157, 78, 221, 0.5) !important;
        }
        
        [data-theme="dark"] .btn-hero.outline {
            border-color: var(--primary-color) !important;
            color: var(--primary-color) !important;
        }
        
        [data-theme="dark"] .btn-hero.outline:hover {
            background: var(--primary-color) !important;
            color: white !important;
        }
        
        [data-theme="dark"] p,
        [data-theme="dark"] span,
        [data-theme="dark"] div {
            color: #c8ccd1 !important;
        }
        
        [data-theme="dark"] .text-muted {
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .text-secondary {
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .dark-mode-toggle {
            background: rgba(155, 95, 255, 0.2) !important;
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .dark-mode-toggle:hover {
            background: rgba(155, 95, 255, 0.3) !important;
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
        }
        
        [data-theme="dark"] .nav-tabs .nav-link {
            color: var(--text-primary) !important;
        }
        
        [data-theme="dark"] .nav-tabs .nav-link:hover {
            background: rgba(157, 78, 221, 0.1) !important;
        }
        
        [data-theme="dark"] .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
            color: white !important;
        }

        :root {
            --primary-color: <?php echo e(primary_color()); ?>;
            --secondary-color: <?php echo e(secondary_color()); ?>;
            --primary-light: #b05ee7;
            --text-dark: #2c3e50;
            --text-light: #6c757d;
            --bg-light: #f8f9fa;
            --white: #ffffff;
            --shadow: 0 4px 20px rgba(116, 36, 169, 0.1);
            --shadow-hover: 0 8px 30px rgba(116, 36, 169, 0.2);
        }

        body {
            font-family: 'Tajawal', sans-serif;
            color: var(--text-dark);
            background: var(--white);
            overflow-x: hidden;
        }

        /* ==================== MODERN SIDEBAR ==================== */
        .modern-sidebar {
            position: fixed;
            right: 0;
            top: 0;
            height: 100vh;
            width: 280px;
            background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
            box-shadow: -2px 0 20px rgba(0, 0, 0, 0.08);
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .modern-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .modern-sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .modern-sidebar::-webkit-scrollbar-thumb {
            background: rgba(116, 36, 169, 0.3);
            border-radius: 10px;
        }

        .modern-sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(116, 36, 169, 0.5);
        }

        /* Sidebar Logo */
        .sidebar-logo {
            padding: 25px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .sidebar-logo img {
            height: 70px;
            width: auto;
            filter: none;
            transition: all 0.3s ease;
        }

        .sidebar-logo:hover img {
            transform: scale(1.05);
        }
        /* Desktop Toggle Button */
        .sidebar-toggle-desktop {
            position: absolute;
            top: 10px;
            left: 10px; /* RTL: inside left edge of sidebar */
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 10px;
            padding: 6px 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: none; /* Hidden on mobile */
        }

        .sidebar-toggle-desktop:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: translateY(-1px);
        }

        /* Sidebar Profile */
        .sidebar-profile {
            padding: 20px;
            text-align: center;
            background: rgba(116, 36, 169, 0.03);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .sidebar-profile-img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
            margin-bottom: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .sidebar-profile-img:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 20px rgba(116, 36, 169, 0.3);
        }

        .sidebar-profile-name {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
        }

        .sidebar-profile-role {
            font-size: 13px;
            color: var(--text-light);
            margin: 0;
        }

        /* Sidebar Navigation */
        .sidebar-nav {
            padding: 20px 15px;
        }

        .sidebar-nav-item {
            margin-bottom: 8px;
        }

        .sidebar-nav-link {
            display: flex;
            align-items: center;
            padding: 14px 18px;
            color: #495057;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
            font-size: 15px;
            position: relative;
            overflow: hidden;
        }

        .sidebar-nav-link::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(180deg, var(--primary-color), var(--secondary-color));
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .sidebar-nav-link:hover::before,
        .sidebar-nav-link.active::before {
            transform: scaleY(1);
        }

        .sidebar-nav-link:hover {
            background: linear-gradient(135deg, rgba(116, 36, 169, 0.08), rgba(250, 137, 107, 0.08));
            color: var(--primary-color);
            transform: translateX(-3px);
        }

        .sidebar-nav-link.active {
            background: linear-gradient(135deg, rgba(116, 36, 169, 0.12), rgba(250, 137, 107, 0.12));
            color: var(--primary-color);
            font-weight: 600;
        }

        .sidebar-nav-icon {
            width: 24px;
            height: 24px;
            margin-left: 15px;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-nav-text {
            flex: 1;
        }

        .sidebar-nav-badge {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 10px;
            font-weight: 600;
        }

        /* Sidebar Dropdown */
        .sidebar-dropdown {
            margin-bottom: 8px;
        }

        .sidebar-dropdown-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            color: #495057;
            background: transparent;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 15px;
            width: 100%;
            text-align: right;
        }

        .sidebar-dropdown-toggle:hover {
            background: linear-gradient(135deg, rgba(116, 36, 169, 0.08), rgba(250, 137, 107, 0.08));
            color: var(--primary-color);
        }

        .sidebar-dropdown-toggle.active {
            background: linear-gradient(135deg, rgba(116, 36, 169, 0.12), rgba(250, 137, 107, 0.12));
            color: var(--primary-color);
        }

        .sidebar-dropdown-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .sidebar-dropdown-content.active {
            max-height: 500px;
        }

        .sidebar-dropdown-link {
            display: flex;
            align-items: center;
            padding: 12px 18px 12px 55px;
            color: #6c757d;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 14px;
            border-radius: 8px;
            margin: 3px 0;
        }

        .sidebar-dropdown-link:hover {
            background: rgba(116, 36, 169, 0.05);
            color: var(--primary-color);
            transform: translateX(-3px);
        }

        .sidebar-dropdown-link i {
            margin-left: 12px;
            font-size: 14px;
        }

        /* Sidebar Footer */
        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            background: rgba(116, 36, 169, 0.03);
        }

        .sidebar-footer-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 20px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 10px;
            width: 100%;
        }

        .sidebar-footer-btn.primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
        }

        .sidebar-footer-btn.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(116, 36, 169, 0.3);
        }

        .sidebar-footer-btn.secondary {
            background: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .sidebar-footer-btn.secondary:hover {
            background: var(--primary-color);
            color: white;
        }

        /* Dark Mode Toggle in Sidebar */
        .sidebar-dark-mode {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            background: rgba(116, 36, 169, 0.05);
            border-radius: 12px;
            margin-bottom: 10px;
        }

        .sidebar-dark-mode span {
            font-weight: 500;
            color: var(--primary-color);
            font-size: 15px;
        }

        .dark-mode-toggle-btn {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            min-height: 40px;
        }

        .dark-mode-toggle-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(116, 36, 169, 0.3);
        }

        /* Mini Header (Top Toggle + Actions) */
        .mobile-top-toggle {
            display: flex;
            position: sticky;
            top: 0;
            z-index: 997;
            background: #fff;
            border-bottom: 1px solid #eee;
            padding: 10px 15px;
            justify-content: space-between;
        }

        .mobile-top-toggle .btn-toggle {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            font-weight: 600;
        }

        /* Sidebar Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        /* Content Wrapper */
        .content-wrapper {
            margin-right: 280px;
            transition: margin-right 0.3s ease;
        }

        /* Collapsed state on desktop */
        body.sidebar-collapsed .modern-sidebar {
            transform: translateX(100%);
        }
        body.sidebar-collapsed .content-wrapper {
            margin-right: 0;
        }

        /* Top Mini Header (for notifications only) */
        .top-mini-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 280px;
            height: 60px;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            z-index: 998;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 30px;
            gap: 15px;
            transition: right 0.3s ease;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .modern-sidebar {
                transform: translateX(100%);
            }

            .modern-sidebar.active {
                transform: translateX(0);
            }

            .sidebar-toggle-mobile {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .content-wrapper {
                margin-right: 0;
            }
        }

        @media (max-width: 768px) {
            .modern-sidebar {
                width: 260px;
            }

            .sidebar-logo img {
                height: 55px;
            }
        }

        /* ==================== FOOTER ==================== */
        .main-footer {
            background: linear-gradient(135deg, var(--primary-color), #5a1d87);
            color: var(--white);
            padding: 60px 0 30px;
            margin-top: 80px;
            position: relative;
            overflow: hidden;
        }

        .main-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--secondary-color), #ff6b4a);
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-section {
            margin-bottom: 40px;
        }

        .footer-section h5 {
            color: var(--secondary-color);
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .footer-links a:hover {
            color: var(--secondary-color);
            transform: translateX(-5px);
        }

        .footer-links a i {
            font-size: 14px;
        }

        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-icons a {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 18px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-icons a:hover {
            background: var(--secondary-color);
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(250, 137, 107, 0.4);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 30px;
            margin-top: 40px;
            text-align: center;
        }

        .footer-bottom p {
            color: rgba(255, 255, 255, 0.8);
            margin: 0;
        }

        /* Contact Button */
        .contact-float-btn {
            position: fixed;
            bottom: 30px;
            left: 30px;
            z-index: 999;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--white);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: var(--shadow-hover);
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .contact-float-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 10px 40px rgba(116, 36, 169, 0.4);
        }

        /* Scroll to Top */
        .scroll-to-top {
            position: fixed;
            bottom: 100px;
            left: 30px;
            z-index: 998;
            background: var(--primary-color);
            color: var(--white);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: var(--shadow);
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .scroll-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        .scroll-to-top:hover {
            background: var(--secondary-color);
            transform: translateY(-5px);
        }

        /* Responsive */
        @media (max-width: 991px) {
            .nav-menu {
                display: none;
            }

            .mobile-menu-btn {
                display: flex !important;
            }

            .header-buttons .btn-header:not(.profile-btn) {
                display: none;
            }
        }
        
        @media (min-width: 992px) {
            .mobile-menu {
                display: none !important;
            }
            
            .mobile-menu-btn {
                display: none !important;
            }
        }

        @media (max-width: 768px) {
            .header-container {
                padding: 12px 15px;
            }

            .logo img {
                height: 65px;
            }

            .main-footer {
                padding: 40px 0 20px;
            }

            .contact-float-btn {
                width: 50px;
                height: 50px;
                font-size: 20px;
                bottom: 20px;
                left: 20px;
            }

            .scroll-to-top {
                bottom: 80px;
                left: 20px;
                width: 45px;
                height: 45px;
            }
        }

        /* Preloader */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }

        .preloader.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .preloader img {
            width: 200px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Main Content Spacing */
        main {
            margin-top: 0;
            min-height: 100vh;
            padding: 30px;
        }

        @media (max-width: 991px) {
            main {
                margin-top: 0;
                padding: 20px 15px;
            }
        }
        /* Student Notifications Dropdown */
        .student-notifications-dropdown {
            direction: rtl;
            text-align: right;
        }
        
        .student-notifications-dropdown .notification-item {
            transition: all 0.2s ease;
        }
        
        .student-notifications-dropdown .notification-item:hover {
            background-color: rgba(116, 36, 169, 0.05);
        }
        
        .student-notifications-dropdown .notification-item.unread {
            background-color: rgba(116, 36, 169, 0.08);
            border-right: 3px solid var(--primary-color);
        }
        
        .notifications-dropdown-wrapper .dropdown-menu {
            animation: fadeInDown 0.3s ease;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <?php echo $__env->yieldContent('css'); ?>
</head>

<body>
    <!-- Preloader -->
    <div class="preloader" id="preloader">
        <img src="<?php echo e(asset(logo_path())); ?>" alt="Logo">
    </div>

    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Modern Sidebar -->
    <aside class="modern-sidebar" id="modernSidebar">
        <!-- Logo -->
        <div class="sidebar-logo" style="position: relative;">
            <a href="<?php echo e(url('/')); ?>" aria-label="الرئيسية">
                <img src="<?php echo e(asset(logo_path())); ?>" alt="Logo">
            </a>
            <!-- Desktop Toggle -->
            <button class="sidebar-toggle-desktop" id="sidebarToggleDesktop" aria-label="إظهار/إخفاء القائمة" title="إظهار/إخفاء القائمة">
                <i class="fas fa-angle-double-right"></i>
            </button>
        </div>

        <?php if(Auth::guard('student')->check()): ?>
        <!-- Profile Section -->
        <div class="sidebar-profile">
            <a href="<?php echo e(route('student_profile', ['student_id' => Auth::guard('student')->user()->id])); ?>">
                <?php if(Auth::guard('student')->user()->image): ?>
                    <img src="<?php echo e(url('upload_files/'.Auth::guard('student')->user()->image)); ?>" alt="Profile" class="sidebar-profile-img">
                <?php else: ?>
                    <img src="<?php echo e(url('front/assets/images/avatar.jpg')); ?>" alt="Profile" class="sidebar-profile-img">
                <?php endif; ?>
            </a>
            <h6 class="sidebar-profile-name"><?php echo e(Auth::guard('student')->user()->first_name); ?> <?php echo e(Auth::guard('student')->user()->second_name); ?></h6>
            <p class="sidebar-profile-role">طالب</p>
        </div>
        <?php endif; ?>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <div class="sidebar-nav-item">
                <a href="<?php echo e(url('/')); ?>" class="sidebar-nav-link <?php echo e(request()->is('/') ? 'active' : ''); ?>">
                    <span class="sidebar-nav-icon"><i class="fas fa-home"></i></span>
                    <span class="sidebar-nav-text">الرئيسية</span>
                </a>
            </div>

            <?php if(Auth::guard('student')->check()): ?>
            <div class="sidebar-nav-item">
                <a href="<?php echo e(route('student.dashboard')); ?>" class="sidebar-nav-link <?php echo e(request()->is('dashboard') ? 'active' : ''); ?>">
                    <span class="sidebar-nav-icon"><i class="fas fa-th-large"></i></span>
                    <span class="sidebar-nav-text">لوحة التحكم</span>
                </a>
            </div>
            <?php endif; ?>

            <div class="sidebar-nav-item">
                <a href="<?php echo e(route('about.teacher')); ?>" class="sidebar-nav-link <?php echo e(request()->is('about-teacher') ? 'active' : ''); ?>">
                    <span class="sidebar-nav-icon"><i class="fas fa-user-tie"></i></span>
                    <span class="sidebar-nav-text">عن المدرس</span>
                </a>
            </div>

            <div class="sidebar-nav-item">
                <a href="<?php echo e(route('faq.index')); ?>" class="sidebar-nav-link <?php echo e(request()->is('faq*') ? 'active' : ''); ?>">
                    <span class="sidebar-nav-icon"><i class="fas fa-question-circle"></i></span>
                    <span class="sidebar-nav-text">الأسئلة الشائعة</span>
                </a>
            </div>

            <?php if(Auth::guard('student')->check()): ?>
            <div class="sidebar-nav-item">
                <a href="<?php echo e(route('courses.index')); ?>" class="sidebar-nav-link <?php echo e(request()->is('courses*') ? 'active' : ''); ?>">
                    <span class="sidebar-nav-icon"><i class="fas fa-book"></i></span>
                    <span class="sidebar-nav-text">كورساتنا</span>
                </a>
            </div>

            <div class="sidebar-nav-item">
                <a href="<?php echo e(route('bundles.index')); ?>" class="sidebar-nav-link <?php echo e(request()->is('bundles*') || request()->is('bundle*') ? 'active' : ''); ?>">
                    <span class="sidebar-nav-icon"><i class="fas fa-box"></i></span>
                    <span class="sidebar-nav-text">الحزم التعليمية</span>
                </a>
            </div>

            <div class="sidebar-nav-item">
                <a href="<?php echo e(route('publicExam.index')); ?>" class="sidebar-nav-link <?php echo e(request()->is('public-exam*') ? 'active' : ''); ?>">
                    <span class="sidebar-nav-icon"><i class="fas fa-clipboard-check"></i></span>
                    <span class="sidebar-nav-text">الامتحانات المجانية</span>
                </a>
            </div>

            <?php if(is_payment_method_enabled('activation_codes')): ?>
            <!-- Activation Dropdown -->
            <div class="sidebar-dropdown">
                <button class="sidebar-dropdown-toggle <?php echo e(request()->is('activate-code*') || request()->is('activate-instructions*') ? 'active' : ''); ?>" onclick="toggleDropdown('activationDropdown')">
                    <div style="display: flex; align-items: center;">
                        <span class="sidebar-nav-icon"><i class="fas fa-key"></i></span>
                        <span class="sidebar-nav-text">التفعيل بالكود</span>
                    </div>
                    <i class="fas fa-chevron-down" id="activationDropdownIcon" style="transition: transform 0.3s ease;"></i>
                </button>
                <div class="sidebar-dropdown-content" id="activationDropdown">
                    <a href="<?php echo e(route('activation_code.index')); ?>" class="sidebar-dropdown-link">
                        <i class="fas fa-key"></i>
                        <span>تفعيل كود</span>
                    </a>
                    <a href="<?php echo e(route('student.activate.instructions')); ?>" class="sidebar-dropdown-link">
                        <i class="fas fa-info-circle"></i>
                        <span>شرح التفعيل</span>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <?php if(is_payment_method_enabled('online_payment')): ?>
            <div class="sidebar-nav-item">
                <a href="<?php echo e(route('payment.history')); ?>" class="sidebar-nav-link <?php echo e(request()->is('payment/history*') ? 'active' : ''); ?>">
                    <span class="sidebar-nav-icon"><i class="fas fa-receipt"></i></span>
                    <span class="sidebar-nav-text">سجل المدفوعات</span>
                </a>
            </div>
            <?php endif; ?>

            <div class="sidebar-nav-item">
                <a href="<?php echo e(route('notifications.index')); ?>" class="sidebar-nav-link <?php echo e(request()->is('notifications*') ? 'active' : ''); ?>">
                    <span class="sidebar-nav-icon"><i class="fas fa-bell"></i></span>
                    <span class="sidebar-nav-text">الإشعارات</span>
                    <span class="sidebar-nav-badge notification-badge-sidebar" style="display: none;">0</span>
                </a>
            </div>
            <?php endif; ?>
        </nav>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <!-- Dark Mode Toggle -->
            <div class="sidebar-dark-mode">
                <span>الوضع الليلي</span>
                <button class="dark-mode-toggle-btn" id="darkModeToggle">
                    <i class="fas fa-moon"></i>
                </button>
            </div>

            <?php if(Auth::guard('student')->check()): ?>
            <a href="<?php echo e(route('studentLogout')); ?>" class="sidebar-footer-btn secondary">
                <i class="fas fa-sign-out-alt"></i>
                <span>تسجيل خروج</span>
            </a>
            <?php else: ?>
            <a href="<?php echo e(route('studentLogin')); ?>" class="sidebar-footer-btn primary">
                <i class="fas fa-sign-in-alt"></i>
                <span>تسجيل الدخول</span>
            </a>
            <a href="<?php echo e(route('studentSignup')); ?>" class="sidebar-footer-btn secondary">
                <i class="fas fa-user-plus"></i>
                <span>حساب جديد</span>
            </a>
            <?php endif; ?>
        </div>
    </aside>

    <!-- Mobile Top Toggle will be placed above content -->

    <!-- Top Mini Header removed to eliminate header space -->

    <!-- Main Content Wrapper -->
    <div class="content-wrapper">
        <!-- Mini Header (Menu + Auth Actions) -->
        <div class="mobile-top-toggle" style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
            <div>
                <button id="sidebarToggleMobileTop" class="btn-toggle" aria-label="فتح القائمة" title="فتح القائمة">
                    <i class="fas fa-bars"></i> القائمة
                </button>
            </div>
            <div class="mini-header-actions" style="display: flex; align-items: center; gap: 8px;">
                <?php if(Auth::guard('student')->check()): ?>
                    <a href="<?php echo e(url('/')); ?>" class="btn btn-sm" style="background: rgba(116, 36, 169, 0.08); color: var(--primary-color); border: 1px solid rgba(116,36,169,0.2); border-radius: 10px; padding: 8px 12px; font-weight: 600; text-decoration: none;">
                        <i class="fas fa-home"></i> الرئيسية
                    </a>
                    <a href="<?php echo e(route('student_profile', ['student_id' => Auth::guard('student')->user()->id])); ?>" class="btn btn-sm" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: #fff; border: none; border-radius: 10px; padding: 8px 12px; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 6px;">
                        <?php if(Auth::guard('student')->user()->image): ?>
                            <img src="<?php echo e(url('upload_files/'.Auth::guard('student')->user()->image)); ?>" alt="" style="width: 22px; height: 22px; border-radius: 50%; object-fit: cover; border: 1px solid rgba(255,255,255,0.6);">
                        <?php else: ?>
                            <i class="fas fa-user-circle"></i>
                        <?php endif; ?>
                        <span>حسابي</span>
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('studentLogin')); ?>" class="btn btn-sm" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: #fff; border: none; border-radius: 10px; padding: 8px 12px; font-weight: 600; text-decoration: none;">
                        <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                    </a>
                    <a href="<?php echo e(route('studentSignup')); ?>" class="btn btn-sm" style="background: #fff; color: var(--primary-color); border: 2px solid var(--primary-color); border-radius: 10px; padding: 8px 12px; font-weight: 600; text-decoration: none;">
                        <i class="fas fa-user-plus"></i> حساب جديد
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <!-- Main Content -->
        <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>
    
    <?php if(auth()->guard('student')->check()): ?>
        <?php
            $deactivatedSubscriptions = \App\Models\StudentSubscriptions::where('student_id', auth()->guard('student')->id())
                ->where('is_active', 0)
                ->whereNotNull('deactivation_reason')
                ->with('month')
                ->get();
        ?>
        
        <?php if($deactivatedSubscriptions->count() > 0): ?>
            <div class="modal fade" id="deactivationWarningModal" tabindex="-1" aria-labelledby="deactivationWarningLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content" style="border: 3px solid #dc3545; border-radius: 15px;">
                        <div class="modal-header" style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; border-radius: 12px 12px 0 0;">
                            <h5 class="modal-title" id="deactivationWarningLabel">
                                <i class="fas fa-exclamation-triangle me-2"></i> تنبيه هام بخصوص اشتراكاتك
                            </h5>
                        </div>
                        <div class="modal-body" style="padding: 30px;">
                            <div class="alert alert-danger" style="border-right: 5px solid #dc3545;">
                                <h6 class="mb-3"><i class="fas fa-ban me-2"></i> تم إلغاء تفعيل الكورسات التالية:</h6>
                                <ul class="mb-0" style="font-size: 16px; line-height: 2;">
                                    <?php $__currentLoopData = $deactivatedSubscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>
                                            <strong><?php echo e($sub->month ? $sub->month->name : 'كورس غير معروف'); ?></strong>
                                            <?php if($sub->deactivation_reason): ?>
                                                <br>
                                                <small class="text-muted">السبب: <?php echo e($sub->deactivation_reason); ?></small>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                            
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>للاستفسار أو إعادة التفعيل:</strong>
                                <p class="mb-0 mt-2">
                                    يرجى التواصل مع الإدارة عبر:
                                    <?php if(whatsapp_number()): ?>
                                        <br>
                                        <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', whatsapp_number())); ?>" target="_blank" class="btn btn-success btn-sm mt-2">
                                            <i class="fab fa-whatsapp me-1"></i> واتساب: <?php echo e(whatsapp_number()); ?>

                                        </a>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="dismissDeactivationModal()">
                                <i class="fas fa-check me-1"></i> فهمت
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <script>
                // عرض المودال تلقائياً عند تحميل الصفحة
                document.addEventListener('DOMContentLoaded', function() {
                    // التحقق من أن المودال لم يُعرض في هذه الجلسة
                    if (!sessionStorage.getItem('deactivationModalShown')) {
                        var modal = new bootstrap.Modal(document.getElementById('deactivationWarningModal'));
                        modal.show();
                    }
                });
                
                function dismissDeactivationModal() {
                    // تخزين أن المودال تم عرضه في هذه الجلسة
                    sessionStorage.setItem('deactivationModalShown', 'true');
                }
            </script>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-content">
            <div class="row">
                <div class="col-lg-4 col-md-6 footer-section">
                    <h5><i class="fas fa-graduation-cap me-2"></i><?php echo e(site_name()); ?></h5>
                    <p style="color: rgba(255,255,255,0.9); line-height: 1.8;">
                        <?php echo e(footer_text() ?: ('منصة تعليمية متخصصة في مادة ' . subject_name() . ' للثانوية العامة، نسعى لتقديم أفضل تجربة تعليمية لطلابنا.')); ?>

                    </p>
                    <div class="social-icons">
                        <?php if(social_url('facebook')): ?>
                        <a href="<?php echo e(social_url('facebook')); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        <?php if(social_url('twitter')): ?>
                        <a href="<?php echo e(social_url('twitter')); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                        <?php endif; ?>
                        <?php if(social_url('instagram')): ?>
                        <a href="<?php echo e(social_url('instagram')); ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                        <?php if(whatsapp_number()): ?>
                        <a href="https://wa.me/<?php echo e(str_replace(['+', ' ', '-'], '', whatsapp_number())); ?>" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        <?php endif; ?>
                        <?php
                            $youtubeUrl = social_url('youtube');
                        ?>
                        <?php if(!empty($youtubeUrl) && filter_var($youtubeUrl, FILTER_VALIDATE_URL)): ?>
                        <a href="<?php echo e($youtubeUrl); ?>" target="_blank"><i class="fab fa-youtube"></i></a>
                        <?php endif; ?>
                        <?php if(telegram_url()): ?>
                        <a href="<?php echo e(telegram_url()); ?>" target="_blank"><i class="fab fa-telegram"></i></a>
                        <?php endif; ?>
                        <?php if(social_url('linkedin')): ?>
                        <a href="<?php echo e(social_url('linkedin')); ?>" target="_blank"><i class="fab fa-linkedin"></i></a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 footer-section">
                    <h5>روابط سريعة</h5>
                    <ul class="footer-links">
                        <li><a href="<?php echo e(url('/')); ?>"><i class="fas fa-home"></i> الرئيسية</a></li>
                        <li><a href="<?php echo e(route('courses.index')); ?>"><i class="fas fa-book"></i> الكورسات</a></li>
                        <li><a href="<?php echo e(route('publicExam.index')); ?>"><i class="fas fa-file-alt"></i> الامتحانات المجانية</a></li>
                        <?php if(privacy_policy()): ?>
                        <li><a href="<?php echo e(route('privacy.policy')); ?>"><i class="fas fa-shield-alt"></i> سياسة الخصوصية</a></li>
                        <?php endif; ?>
                        <?php if(terms_of_service()): ?>
                        <li><a href="<?php echo e(route('terms.of.service')); ?>"><i class="fas fa-gavel"></i> شروط الاستخدام</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 footer-section">
                    <h5>السنوات الدراسية</h5>
                    <ul class="footer-links">
                        <?php $__currentLoopData = grades_list(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><a href="<?php echo e(route('studentLogin')); ?>"><i class="fas fa-user-graduate"></i> <?php echo e($grade['name']); ?></a></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php
                            $extraLinks = other_links();
                        ?>
                        <?php if(!empty($extraLinks)): ?>
                            <li style="margin-top: 10px; font-weight: 600;">روابط أخرى</li>
                            <?php $__currentLoopData = $extraLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!empty($url)): ?>
                                <li>
                                    <a href="<?php echo e($url); ?>" target="_blank">
                                        <i class="fas fa-external-link-alt"></i> <?php echo e($label); ?>

                                    </a>
                                </li>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 footer-section">
                    <h5>تواصل معنا</h5>
                    <ul class="footer-links">
                        <?php if(whatsapp_number()): ?>
                        <li><a href="https://wa.me/<?php echo e(str_replace(['+', ' ', '-'], '', whatsapp_number())); ?>" target="_blank"><i class="fab fa-whatsapp"></i> واتساب: <?php echo e(whatsapp_number()); ?></a></li>
                        <?php endif; ?>
                        <?php if(phone_number()): ?>
                        <li><a href="tel:<?php echo e(phone_number()); ?>"><i class="fas fa-phone"></i> <?php echo e(phone_number()); ?></a></li>
                        <?php endif; ?>
                        <?php if(contact_email()): ?>
                        <li><a href="mailto:<?php echo e(contact_email()); ?>"><i class="fas fa-envelope"></i> <?php echo e(contact_email()); ?></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo e(date('Y')); ?> - <?php echo e(teacher_full_name() ?: teacher_name()); ?>. <?php echo e(footer_copyright()); ?></p>
                <?php if(footer_text()): ?>
                <p style="margin-top: 10px; font-size: 14px;"><?php echo e(footer_text()); ?></p>
                <?php endif; ?>
                <div class="mt-3" style="font-size: 12px; opacity: 0.8;">
                    <?php if(privacy_policy()): ?>
                    <a href="<?php echo e(route('privacy.policy')); ?>" style="color: rgba(255,255,255,0.8); text-decoration: none; margin-left: 15px;">سياسة الخصوصية</a>
                    <?php endif; ?>
                    <?php if(terms_of_service()): ?>
                    <a href="<?php echo e(route('terms.of.service')); ?>" style="color: rgba(255,255,255,0.8); text-decoration: none; margin-left: 15px;">شروط الاستخدام</a>
                    <?php endif; ?>
                </div>
                
                <?php
                    $developerText = setting('developer_text', 'برمجة وتطوير بواسطة');
                    $developerFacebook = setting('developer_facebook', '');
                    $developerWhatsapp = setting('developer_whatsapp', '');
                ?>
                <?php if(!empty($developerFacebook) || !empty($developerWhatsapp)): ?>
                <div class="mt-3" style="font-size: 12px; opacity: 0.7; text-align: center;">
                    <span><?php echo e($developerText); ?></span>
                    <?php if(!empty($developerFacebook)): ?>
                        <a href="<?php echo e($developerFacebook); ?>" target="_blank" style="color: rgba(255,255,255,0.8); text-decoration: none; margin: 0 8px;">
                            <i class="fab fa-facebook"></i>
                        </a>
                    <?php endif; ?>
                    <?php if(!empty($developerWhatsapp)): ?>
                        <a href="https://wa.me/<?php echo e(str_replace(['+', ' ', '-'], '', $developerWhatsapp)); ?>" target="_blank" style="color: rgba(255,255,255,0.8); text-decoration: none; margin: 0 8px;">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </footer>
    </div><!-- End Content Wrapper -->

    <!-- Contact Float Button -->
    <?php if(whatsapp_number()): ?>
    <a href="https://wa.me/<?php echo e(str_replace(['+', ' ', '-'], '', whatsapp_number())); ?>" target="_blank" class="contact-float-btn" title="تواصل معنا">
        <i class="fab fa-whatsapp"></i>
    </a>
    <?php endif; ?>

    <!-- Scroll to Top -->
    <div class="scroll-to-top" id="scrollToTop">
        <i class="fas fa-arrow-up"></i>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preloader
        window.addEventListener('load', function() {
            document.getElementById('preloader').classList.add('hidden');
        });

        // Scroll to Top Button visibility
        window.addEventListener('scroll', function() {
            const scrollBtn = document.getElementById('scrollToTop');
            if (!scrollBtn) return;
            scrollBtn.classList.toggle('show', window.scrollY > 300);
        });

        // Scroll to Top
        document.getElementById('scrollToTop').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Mobile Menu Toggle
        function initMobileMenu() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            
            if (!mobileMenuBtn || !mobileMenu) {
                console.error('Mobile menu elements not found');
                return;
            }
            
            // Toggle menu function
            function toggleMenu() {
                mobileMenu.classList.toggle('active');
                const icon = mobileMenuBtn.querySelector('i');
                if (icon) {
                    if (mobileMenu.classList.contains('active')) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-times');
                    } else {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                }
            }
            
            // Button click handler
            mobileMenuBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleMenu();
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(e) {
                if (mobileMenu.classList.contains('active')) {
                    if (!mobileMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                        toggleMenu();
                    }
                }
            });

            // Close menu when clicking on a link
            const mobileMenuLinks = mobileMenu.querySelectorAll('a');
            mobileMenuLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    setTimeout(function() {
                        if (mobileMenu.classList.contains('active')) {
                            toggleMenu();
                        }
                    }, 100);
                });
            });
        }
        
        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMobileMenu);
        } else {
            initMobileMenu();
        }
    </script>

    <script>
        // Sidebar Toggle - Mobile and Desktop
        (function() {
            const sidebar = document.getElementById('modernSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const mobileTopToggle = document.getElementById('sidebarToggleMobileTop');
            const desktopToggle = document.getElementById('sidebarToggleDesktop');

            function toggleSidebar() {
                const isMobile = window.innerWidth <= 991;
                if (isMobile) {
                    sidebar.classList.toggle('active');
                    overlay.classList.toggle('active');
                    // Update mobile icon
                    if (mobileTopToggle) {
                        const icon = mobileTopToggle.querySelector('i');
                        if (icon) {
                            icon.className = sidebar.classList.contains('active') ? 'fas fa-times' : 'fas fa-bars';
                        }
                    }
                } else {
                    document.body.classList.toggle('sidebar-collapsed');
                    // Update desktop icon direction
                    if (desktopToggle) {
                        const icon = desktopToggle.querySelector('i');
                        if (icon) {
                            icon.className = document.body.classList.contains('sidebar-collapsed')
                                ? 'fas fa-angle-double-left'
                                : 'fas fa-angle-double-right';
                        }
                    }
                }
            }

            if (mobileTopToggle) {
                mobileTopToggle.addEventListener('click', toggleSidebar);
            }
            if (desktopToggle) {
                desktopToggle.addEventListener('click', toggleSidebar);
            }
            if (overlay) {
                overlay.addEventListener('click', function() {
                    if (window.innerWidth <= 991) {
                        toggleSidebar();
                    }
                });
            }

            // Close sidebar when clicking a link (mobile only)
            if (sidebar) {
                const sidebarLinks = sidebar.querySelectorAll('.sidebar-nav-link, .sidebar-dropdown-link');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth <= 991 && sidebar.classList.contains('active')) {
                            setTimeout(toggleSidebar, 200);
                        }
                    });
                });
            }
        })();

        // Dropdown Toggle Function
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            const icon = document.getElementById(id + 'Icon');
            
            if (dropdown && icon) {
                dropdown.classList.toggle('active');
                
                // Rotate icon
                if (dropdown.classList.contains('active')) {
                    icon.style.transform = 'rotate(180deg)';
                } else {
                    icon.style.transform = 'rotate(0deg)';
                }
            }
        }

        // Auto-open active dropdown on load
        window.addEventListener('DOMContentLoaded', function() {
            const activeDropdownLink = document.querySelector('.sidebar-dropdown-link.active, .sidebar-dropdown .active');
            if (activeDropdownLink) {
                const dropdown = activeDropdownLink.closest('.sidebar-dropdown');
                if (dropdown) {
                    const toggle = dropdown.querySelector('.sidebar-dropdown-toggle');
                    const content = dropdown.querySelector('.sidebar-dropdown-content');
                    const icon = dropdown.querySelector('.sidebar-dropdown-toggle i:last-child');
                    
                    if (toggle && content) {
                        toggle.classList.add('active');
                        content.classList.add('active');
                        if (icon) {
                            icon.style.transform = 'rotate(180deg)';
                        }
                    }
                }
            }
        });
    </script>
    
    <!-- Admin Enhancements JS (for dark mode) -->
    <script src="<?php echo e(URL::asset('js/admin-enhancements.js')); ?>"></script>
    
    <script>
        // Simple Dark Mode Toggle (Direct Implementation)
        let isToggling = false; // Prevent double toggle
        
        function toggleDarkMode() {
            // Prevent double execution
            if (isToggling) {
                console.log('Toggle already in progress, ignoring...');
                return;
            }
            
            isToggling = true;
            
            const html = document.documentElement;
            let currentTheme = html.getAttribute('data-theme') || localStorage.getItem('theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            console.log('Current theme:', currentTheme);
            console.log('New theme:', newTheme);
            
            // Set new theme directly
            html.setAttribute('data-theme', newTheme);
            html.dataset.theme = newTheme;
            
            if (document.body) {
                document.body.setAttribute('data-theme', newTheme);
                document.body.dataset.theme = newTheme;
            }
            
            localStorage.setItem('theme', newTheme);
            
            // Update all dark mode toggle icons (sidebar + header if exists)
            const darkModeButtons = document.querySelectorAll('.dark-mode-toggle-btn, #darkModeToggle');
            darkModeButtons.forEach(btn => {
                const icon = btn.querySelector('i');
                if (icon) {
                    icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                }
            });
            
            console.log('Theme changed to:', newTheme);
            console.log('HTML data-theme after change:', html.getAttribute('data-theme'));
            
            // Reset flag after a short delay
            setTimeout(() => {
                isToggling = false;
            }, 300);
        }
        
        // Initialize theme on page load (only once)
        if (!window.darkModeInitialized) {
            window.darkModeInitialized = true;
            
            const savedTheme = localStorage.getItem('theme') || 'light';
            const html = document.documentElement;
            
            // Set theme on HTML (override the HTML attribute)
            html.setAttribute('data-theme', savedTheme);
            html.dataset.theme = savedTheme;
            
            if (document.body) {
                document.body.setAttribute('data-theme', savedTheme);
                document.body.dataset.theme = savedTheme;
            }
            
            console.log('Initial theme set to:', savedTheme);
            
            // Add click handlers to all dark mode buttons
            const darkModeButtons = document.querySelectorAll('.dark-mode-toggle-btn, #darkModeToggle');
            darkModeButtons.forEach(btn => {
                if (btn) {
                    // Remove any existing handlers by cloning
                    const newBtn = btn.cloneNode(true);
                    btn.parentNode.replaceChild(newBtn, btn);
                    
                    // Add single event listener
                    newBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        toggleDarkMode();
                        return false;
                    }, { once: false, passive: false });
                    
                    // Update icon
                    const icon = newBtn.querySelector('i');
                    if (icon && savedTheme === 'dark') {
                        icon.className = 'fas fa-sun';
                    }
                }
            });
        }
    </script>
    
    <script>
        // إعداد CSRF Token لجميع AJAX requests
        (function() {
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                window.axios = window.axios || {};
                window.axios.defaults = window.axios.defaults || {};
                window.axios.defaults.headers = window.axios.defaults.headers || {};
                window.axios.defaults.headers.common = window.axios.defaults.headers.common || {};
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
                
                // إعداد jQuery أيضاً إذا كان موجوداً
                if (typeof jQuery !== 'undefined') {
                    jQuery.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': token.content
                        }
                    });
                }
            }
        })();

        // تحديث عدد الإشعارات غير المقروءة و تحميل الإشعارات
        <?php if(Auth::guard('student')->check()): ?>
        (function() {
            function updateNotificationCount() {
                fetch('/notifications/unread-count')
                    .then(response => response.json())
                    .then(data => {
                        const badge = document.querySelector('.notification-badge-nav');
                        if (badge) {
                            if (data.count > 0) {
                                badge.textContent = data.count > 99 ? '99+' : data.count;
                                badge.style.display = 'flex';
                            } else {
                                badge.style.display = 'none';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching notification count:', error);
                    });
            }

            function formatDate(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diff = now - date;
                const seconds = Math.floor(diff / 1000);
                const minutes = Math.floor(seconds / 60);
                const hours = Math.floor(minutes / 60);
                const days = Math.floor(hours / 24);
                
                if (seconds < 60) return 'الآن';
                if (minutes < 60) return `منذ ${minutes} دقيقة`;
                if (hours < 24) return `منذ ${hours} ساعة`;
                if (days < 7) return `منذ ${days} يوم`;
                
                return date.toLocaleDateString('ar-EG', { year: 'numeric', month: 'long', day: 'numeric' });
            }

            let isLoadingNotifications = false;
            let lastLoadTime = 0;
            const LOAD_COOLDOWN = 2000; // 2 seconds cooldown

            function loadNotifications() {
                const notificationsList = document.getElementById('notificationsList');
                if (!notificationsList) return;

                // منع التحميل المتكرر
                const now = Date.now();
                if (isLoadingNotifications || (now - lastLoadTime < LOAD_COOLDOWN)) {
                    return;
                }

                isLoadingNotifications = true;
                lastLoadTime = now;

                // عرض loading
                notificationsList.innerHTML = `
                    <div class="text-center p-3 text-muted" style="font-size: 14px;">
                        <i class="fas fa-spinner fa-spin"></i> جاري التحميل...
                    </div>
                `;

                fetch('/notifications/recent')
                    .then(response => response.json())
                    .then(data => {
                        isLoadingNotifications = false;
                        
                        if (data.notifications && data.notifications.length > 0) {
                            let html = '';
                            data.notifications.forEach(function(notification) {
                                const iconClass = notification.icon || 'fa-bell';
                                const colorClass = notification.color || 'info';
                                const colorMap = {
                                    'success': '#28a745',
                                    'danger': '#dc3545',
                                    'warning': '#ffc107',
                                    'primary': '#1976d2',
                                    'info': '#17a2b8'
                                };
                                const iconColor = colorMap[colorClass] || '#17a2b8';
                                const url = notification.url || '<?php echo e(route("notifications.index")); ?>';
                                const isRead = notification.is_read ? '' : 'unread';
                                const notificationId = notification.id;
                                
                                html += `
                                    <a href="${url}" class="dropdown-item notification-item ${isRead}" data-notification-id="${notificationId}" data-is-read="${notification.is_read}" style="padding: 12px 15px; border-bottom: 1px solid #f0f0f0; text-decoration: none; color: inherit; cursor: pointer;">
                                        <div class="d-flex align-items-start">
                                            <div class="notification-icon me-2" style="color: ${iconColor}; font-size: 18px; margin-top: 2px;">
                                                <i class="fas ${iconClass}"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold" style="font-size: 13px; color: var(--primary-color); margin-bottom: 3px;">${notification.title}</div>
                                                <div class="text-muted" style="font-size: 11px; line-height: 1.4;">${notification.message.length > 60 ? notification.message.substring(0, 60) + '...' : notification.message}</div>
                                                <small class="text-muted" style="font-size: 10px;">${formatDate(notification.created_at)}</small>
                                            </div>
                                            ${!notification.is_read ? '<span class="badge bg-primary rounded-pill notification-badge-item" style="font-size: 8px; margin-right: 5px;">جديد</span>' : ''}
                                        </div>
                                    </a>
                                `;
                            });
                            notificationsList.innerHTML = html;
                            
                            // إضافة event listeners للإشعارات
                            attachNotificationListeners();
                        } else {
                            notificationsList.innerHTML = `
                                <div class="text-center p-4 text-muted" style="font-size: 14px;">
                                    <i class="far fa-bell-slash" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                                    لا توجد إشعارات
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        isLoadingNotifications = false;
                        console.error('Error loading notifications:', error);
                        notificationsList.innerHTML = `
                            <div class="text-center p-3 text-danger" style="font-size: 14px;">
                                <i class="fas fa-exclamation-triangle"></i> حدث خطأ في تحميل الإشعارات
                            </div>
                        `;
                    });
            }

            function attachNotificationListeners() {
                const notificationItems = document.querySelectorAll('.notification-item[data-notification-id]');
                notificationItems.forEach(function(item) {
                    item.addEventListener('click', function(e) {
                        const notificationId = this.getAttribute('data-notification-id');
                        const isRead = this.getAttribute('data-is-read') === 'true';
                        
                        // إذا كان الإشعار غير مقروء، حدده كمقروء
                        if (!isRead && notificationId) {
                            e.preventDefault(); // منع الانتقال المباشر
                            
                            // تحديد الإشعار كمقروء
                            fetch(`/notifications/mark-read/${notificationId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // تحديث حالة الإشعار في الواجهة
                                    this.classList.remove('unread');
                                    this.setAttribute('data-is-read', 'true');
                                    const badge = this.querySelector('.notification-badge-item');
                                    if (badge) {
                                        badge.remove();
                                    }
                                    
                                    // تحديث عدد الإشعارات
                                    updateNotificationCount();
                                    
                                    // الانتقال إلى الرابط
                                    const url = this.getAttribute('href');
                                    if (url && url !== '#') {
                                        window.location.href = url;
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error marking notification as read:', error);
                                // الانتقال إلى الرابط حتى لو فشل التحديث
                                const url = this.getAttribute('href');
                                if (url && url !== '#') {
                                    window.location.href = url;
                                }
                            });
                        }
                    });
                });
            }

            // تحديث عند تحميل الصفحة
            document.addEventListener('DOMContentLoaded', function() {
                updateNotificationCount();
                
                // تحميل الإشعارات عند فتح الـ dropdown
                const dropdown = document.getElementById('studentNotificationsDropdown');
                const dropdownMenu = document.querySelector('.student-notifications-dropdown');
                if (dropdown && dropdownMenu) {
                    let dropdownOpen = false;
                    
                    // استخدام Bootstrap dropdown events
                    dropdownMenu.addEventListener('show.bs.dropdown', function() {
                        dropdownOpen = true;
                        loadNotifications();
                    });
                    
                    dropdownMenu.addEventListener('hide.bs.dropdown', function() {
                        dropdownOpen = false;
                    });
                    
                    // تحميل الإشعارات عند النقر على الزر
                    dropdown.addEventListener('click', function(e) {
                        if (!dropdownOpen) {
                            setTimeout(function() {
                                if (dropdownMenu.classList.contains('show')) {
                                    loadNotifications();
                                }
                            }, 100);
                        }
                    });
                }

                // تحديث عدد الإشعارات كل 30 ثانية (بدون تحديث القائمة)
                setInterval(function() {
                    updateNotificationCount();
                }, 30000);
            });
        })();
        <?php endif; ?>
    </script>


    <?php echo $__env->yieldContent('js'); ?>
</body>

</html>
<?php /**PATH C:\Users\A-Tech\Downloads\archiveGzNa7\resources\views/front/layouts/app.blade.php ENDPATH**/ ?>