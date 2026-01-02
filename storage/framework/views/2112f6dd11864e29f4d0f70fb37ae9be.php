<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title'); ?> - لوحة التحكم</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Tajawal (Arabic) -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    
    <!-- Admin Enhancements CSS -->
    <link rel="stylesheet" href="<?php echo e(URL::asset('css/admin-enhancements.css')); ?>" onerror="console.error('Failed to load admin-enhancements.css')">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/colreorder/1.7.0/css/colReorder.bootstrap5.min.css">
    
    <!-- Lazy Loading JS -->
    <script src="<?php echo e(URL::asset('js/lazy-loading.js')); ?>" defer></script>
    
    <style>
        /* ============================================
           Smart Search Bar - Enhanced Design (Inline)
           ============================================ */
        
        .smart-search-container {
            position: relative;
            width: 100%;
            max-width: 450px;
            margin: 0 20px;
        }
        
        .smart-search-input {
            width: 100%;
            padding: 12px 50px 12px 20px;
            border: 2px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 30px !important;
            font-size: 14px;
            background: rgba(255, 255, 255, 0.1) !important;
            color: white !important;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .smart-search-input::placeholder {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        
        .smart-search-input:focus {
            outline: none !important;
            border-color: rgba(155, 95, 255, 0.6) !important;
            background: rgba(255, 255, 255, 0.15) !important;
            box-shadow: 0 0 0 4px rgba(155, 95, 255, 0.2), 0 4px 20px rgba(0, 0, 0, 0.1) !important;
            transform: translateY(-1px);
        }
        
        .smart-search-icon {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.8) !important;
            pointer-events: none;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .smart-search-input:focus + .smart-search-icon {
            color: #9B5FFF !important;
            transform: translateY(-50%) scale(1.1);
        }
        
        .smart-search-results {
            position: absolute !important;
            top: calc(100% + 10px) !important;
            left: 0 !important;
            right: 0 !important;
            background: white !important;
            border: none !important;
            border-radius: 16px !important;
            margin-top: 0 !important;
            max-height: 500px !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.05) !important;
            display: none !important;
            z-index: 9999 !important;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .smart-search-results.show {
            display: block !important;
        }
        
        .smart-search-results::-webkit-scrollbar {
            width: 6px;
        }
        
        .smart-search-results::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .smart-search-results::-webkit-scrollbar-thumb {
            background: #9B5FFF;
            border-radius: 10px;
        }
        
        .smart-search-results::-webkit-scrollbar-thumb:hover {
            background: #7A35FF;
        }
        
        .smart-search-result-item {
            padding: 0 !important;
            cursor: pointer;
            transition: all 0.2s ease;
            border-bottom: 1px solid #f0f0f0;
            text-decoration: none !important;
            display: flex !important;
            align-items: center !important;
            color: inherit;
            background: transparent;
        }
        
        .smart-search-result-item:first-child {
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }
        
        .smart-search-result-item:last-child {
            border-bottom: none !important;
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
        }
        
        .smart-search-result-item:hover {
            background: linear-gradient(90deg, rgba(155, 95, 255, 0.08) 0%, rgba(155, 95, 255, 0.03) 100%) !important;
            transform: translateX(-3px);
            border-right: 3px solid #9B5FFF !important;
        }
        
        .smart-search-result-item .result-icon {
            width: 40px !important;
            height: 40px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: linear-gradient(135deg, #9B5FFF 0%, #7A35FF 100%) !important;
            color: white !important;
            border-radius: 10px !important;
            margin-left: 15px !important;
            flex-shrink: 0;
            font-size: 16px;
            box-shadow: 0 4px 10px rgba(155, 95, 255, 0.3) !important;
        }
        
        .smart-search-result-item .result-title {
            font-weight: 600 !important;
            color: #2c3e50 !important;
            margin-bottom: 4px;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .smart-search-result-item .result-subtitle {
            font-size: 12px !important;
            color: #7f8c8d !important;
            line-height: 1.3;
        }
        
        .smart-search-results .loading-state {
            padding: 30px 20px;
            text-align: center;
            color: #7f8c8d;
        }
        
        .smart-search-results .loading-state i {
            animation: spin 1s linear infinite;
            margin-left: 10px;
            color: #9B5FFF;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .smart-search-results .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: #95a5a6;
        }
        
        .smart-search-results .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ecf0f1;
            display: block;
        }
        
        .smart-search-results .empty-state p {
            margin: 0;
            font-size: 14px;
        }
        
        .smart-search-results .error-state {
            padding: 30px 20px;
            text-align: center;
            color: #e74c3c;
        }
        
        .smart-search-results .error-state i {
            font-size: 32px;
            margin-bottom: 10px;
            display: block;
        }
        
        /* Dark Mode */
        [data-theme="dark"] .smart-search-input {
            background: rgba(42, 42, 42, 0.8) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .smart-search-input::placeholder {
            color: rgba(155, 95, 255, 0.5) !important;
        }
        
        [data-theme="dark"] .smart-search-input:focus {
            border-color: rgba(155, 95, 255, 0.6) !important;
            background: rgba(42, 42, 42, 0.95) !important;
            box-shadow: 0 0 0 4px rgba(155, 95, 255, 0.2), 0 4px 20px rgba(0, 0, 0, 0.3) !important;
        }
        
        [data-theme="dark"] .smart-search-icon {
            color: rgba(155, 95, 255, 0.8) !important;
        }
        
        [data-theme="dark"] .smart-search-results {
            background: #2a2a2a !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1) !important;
        }
        
        [data-theme="dark"] .smart-search-result-item {
            border-bottom-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        [data-theme="dark"] .smart-search-result-item:hover {
            background: linear-gradient(90deg, rgba(155, 95, 255, 0.15) 0%, rgba(155, 95, 255, 0.05) 100%) !important;
            border-right-color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .smart-search-result-item .result-title {
            color: #FFFFFF !important;
        }
        
        [data-theme="dark"] .smart-search-result-item .result-subtitle {
            color: #9B5FFF !important;
        }
        
        /* ============================================ */
        /* Dark Mode CSS - Custom Colors */
        [data-theme="dark"],
        html[data-theme="dark"],
        body[data-theme="dark"] {
            --bg-primary: #0F0F12 !important;
            --bg-secondary: #1a1a1f !important;
            --bg-tertiary: #25252d !important;
            --text-primary: #FFFFFF !important;
            --text-secondary: #9B5FFF !important;
            --border-color: #2a2a35 !important;
            --accent-color: #9B5FFF !important;
            --accent-hover: #7A35FF !important;
            --shadow-color: #7A35FF !important;
        }
        
        [data-theme="dark"] body,
        body[data-theme="dark"],
        html[data-theme="dark"] body,
        html[data-theme="dark"] {
            background: #0F0F12 !important;
            color: #FFFFFF !important;
        }
        
        /* Force text color on all elements - but not for white cards */
        [data-theme="dark"] *:not(button):not(input):not(select):not(textarea):not(a):not(i):not(.fa):not(.fas):not(.far):not(.fab):not(.fal):not(.modern-card *):not(.card *):not(.stats-card *):not(.notification-card *):not(.course-card *):not(.feature-card *):not(.lecture-card *):not(.reason-card *):not(.stat-card *) {
            color: #FFFFFF !important;
        }
        
        [data-theme="dark"] p:not(.modern-card p):not(.card p):not(.stats-card p):not(.notification-card p):not(.course-card p):not(.feature-card p):not(.lecture-card p):not(.reason-card p):not(.stat-card p),
        [data-theme="dark"] span:not(.badge):not(.btn):not(.icon):not(.modern-card span):not(.card span):not(.stats-card span):not(.notification-card span):not(.course-card span):not(.feature-card span):not(.lecture-card span):not(.reason-card span):not(.stat-card span),
        [data-theme="dark"] div:not(.btn):not(.badge):not(.alert):not(.modern-card):not(.card):not(.stats-card):not(.notification-card):not(.course-card):not(.feature-card):not(.lecture-card):not(.reason-card):not(.stat-card):not(.modern-card div):not(.card div):not(.stats-card div):not(.notification-card div):not(.course-card div):not(.feature-card div):not(.lecture-card div):not(.reason-card div):not(.stat-card div),
        [data-theme="dark"] td:not(.modern-card td):not(.card td):not(.stats-card td):not(.notification-card td):not(.course-card td):not(.feature-card td):not(.lecture-card td):not(.reason-card td):not(.stat-card td),
        [data-theme="dark"] th:not(.modern-card th):not(.card th):not(.stats-card th):not(.notification-card th):not(.course-card th):not(.feature-card th):not(.lecture-card th):not(.reason-card th):not(.stat-card th),
        [data-theme="dark"] li:not(.modern-card li):not(.card li):not(.stats-card li):not(.notification-card li):not(.course-card li):not(.feature-card li):not(.lecture-card li):not(.reason-card li):not(.stat-card li),
        [data-theme="dark"] label:not(.modern-card label):not(.card label):not(.stats-card label):not(.notification-card label):not(.course-card label):not(.feature-card label):not(.lecture-card label):not(.reason-card label):not(.stat-card label),
        [data-theme="dark"] .text-primary:not(.modern-card .text-primary):not(.card .text-primary):not(.stats-card .text-primary):not(.notification-card .text-primary):not(.course-card .text-primary):not(.feature-card .text-primary):not(.lecture-card .text-primary):not(.reason-card .text-primary):not(.stat-card .text-primary),
        [data-theme="dark"] .text-dark:not(.modern-card .text-dark):not(.card .text-dark):not(.stats-card .text-dark):not(.notification-card .text-dark):not(.course-card .text-dark):not(.feature-card .text-dark):not(.lecture-card .text-dark):not(.reason-card .text-dark):not(.stat-card .text-dark) {
            color: #FFFFFF !important;
        }
        
        /* Override for white cards - all text should be purple */
        [data-theme="dark"] .modern-card p,
        [data-theme="dark"] .modern-card span,
        [data-theme="dark"] .modern-card div,
        [data-theme="dark"] .modern-card td,
        [data-theme="dark"] .modern-card th,
        [data-theme="dark"] .modern-card li,
        [data-theme="dark"] .modern-card label,
        [data-theme="dark"] .card p,
        [data-theme="dark"] .card span,
        [data-theme="dark"] .card div,
        [data-theme="dark"] .card td,
        [data-theme="dark"] .card th,
        [data-theme="dark"] .card li,
        [data-theme="dark"] .card label,
        [data-theme="dark"] .stats-card p,
        [data-theme="dark"] .stats-card span,
        [data-theme="dark"] .stats-card div,
        [data-theme="dark"] .stats-card td,
        [data-theme="dark"] .stats-card th,
        [data-theme="dark"] .stats-card li,
        [data-theme="dark"] .stats-card label,
        [data-theme="dark"] .notification-card p,
        [data-theme="dark"] .notification-card span,
        [data-theme="dark"] .notification-card div,
        [data-theme="dark"] .notification-card td,
        [data-theme="dark"] .notification-card th,
        [data-theme="dark"] .notification-card li,
        [data-theme="dark"] .notification-card label,
        [data-theme="dark"] .course-card p,
        [data-theme="dark"] .course-card span,
        [data-theme="dark"] .course-card div,
        [data-theme="dark"] .course-card td,
        [data-theme="dark"] .course-card th,
        [data-theme="dark"] .course-card li,
        [data-theme="dark"] .course-card label,
        [data-theme="dark"] .feature-card p,
        [data-theme="dark"] .feature-card span,
        [data-theme="dark"] .feature-card div,
        [data-theme="dark"] .feature-card td,
        [data-theme="dark"] .feature-card th,
        [data-theme="dark"] .feature-card li,
        [data-theme="dark"] .feature-card label,
        [data-theme="dark"] .lecture-card p,
        [data-theme="dark"] .lecture-card span,
        [data-theme="dark"] .lecture-card div,
        [data-theme="dark"] .lecture-card td,
        [data-theme="dark"] .lecture-card th,
        [data-theme="dark"] .lecture-card li,
        [data-theme="dark"] .lecture-card label,
        [data-theme="dark"] .reason-card p,
        [data-theme="dark"] .reason-card span,
        [data-theme="dark"] .reason-card div,
        [data-theme="dark"] .reason-card td,
        [data-theme="dark"] .reason-card th,
        [data-theme="dark"] .reason-card li,
        [data-theme="dark"] .reason-card label,
        [data-theme="dark"] .stat-card p,
        [data-theme="dark"] .stat-card span,
        [data-theme="dark"] .stat-card div,
        [data-theme="dark"] .stat-card td,
        [data-theme="dark"] .stat-card th,
        [data-theme="dark"] .stat-card li,
        [data-theme="dark"] .stat-card label {
            color: #9B5FFF !important;
        }
        
        /* Override any inline styles - but not for white cards */
        [data-theme="dark"] [style*="color"]:not(.modern-card [style*="color"]):not(.card [style*="color"]):not(.stats-card [style*="color"]):not(.notification-card [style*="color"]):not(.course-card [style*="color"]):not(.feature-card [style*="color"]):not(.lecture-card [style*="color"]):not(.reason-card [style*="color"]):not(.stat-card [style*="color"]) {
            color: #FFFFFF !important;
        }
        
        [data-theme="dark"] .modern-card {
            background: #FFFFFF !important;
            color: #9B5FFF !important;
            border-color: #2a2a35 !important;
        }
        
        [data-theme="dark"] .modern-card *:not(button):not(.btn):not(a):not(i):not(.fa):not(.fas):not(.far):not(.fab):not(.fal):not(.badge):not(.alert),
        [data-theme="dark"] .modern-card p,
        [data-theme="dark"] .modern-card span:not(.badge):not(.btn),
        [data-theme="dark"] .modern-card div:not(.btn):not(.badge),
        [data-theme="dark"] .modern-card td,
        [data-theme="dark"] .modern-card th,
        [data-theme="dark"] .modern-card li,
        [data-theme="dark"] .modern-card label {
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .modern-card h1,
        [data-theme="dark"] .modern-card h2,
        [data-theme="dark"] .modern-card h3,
        [data-theme="dark"] .modern-card h4,
        [data-theme="dark"] .modern-card h5,
        [data-theme="dark"] .modern-card h6 {
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .page-header-modern,
        [data-theme="dark"] .main-footer {
            background: #1a1a1f !important;
            color: #FFFFFF !important;
            border-color: #2a2a35 !important;
        }
        
        [data-theme="dark"] .sidebar {
            background: linear-gradient(180deg, #0F0F12 0%, #1a1a1f 100%) !important;
            border-left: 1px solid #2a2a35 !important;
        }
        
        [data-theme="dark"] .sidebar a {
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .sidebar a:hover {
            background: rgba(155, 95, 255, 0.1) !important;
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .sidebar a.active {
            background: rgba(155, 95, 255, 0.2) !important;
            color: #9B5FFF !important;
            border-right-color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .main-header {
            background: linear-gradient(135deg, #1a1a1f 0%, #25252d 100%) !important;
            border-bottom: 1px solid #2a2a35 !important;
            box-shadow: 0 2px 10px rgba(122, 53, 255, 0.3) !important;
        }
        
        [data-theme="dark"] .main-header .logo,
        [data-theme="dark"] .main-header .logo span,
        [data-theme="dark"] .main-header .logo i {
            color: #FFFFFF !important;
        }
        
        [data-theme="dark"] .content {
            background: #0F0F12 !important;
        }
        
        [data-theme="dark"] .stat-card {
            background: #1a1a1f !important;
            color: #FFFFFF !important;
            border-color: #2a2a35 !important;
        }
        
        [data-theme="dark"] .stat-card:hover {
            background: #25252d !important;
            border-right-color: #9B5FFF !important;
            box-shadow: 0 5px 15px rgba(122, 53, 255, 0.3) !important;
        }
        
        [data-theme="dark"] .stat-card *,
        [data-theme="dark"] .stat-card p,
        [data-theme="dark"] .stat-card span,
        [data-theme="dark"] .stat-card div {
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .stat-value {
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .stat-label {
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .stat-icon.primary {
            background: linear-gradient(135deg, #9B5FFF 0%, #7A35FF 100%) !important;
        }
        
        [data-theme="dark"] .modern-table {
            background: #1a1a1f !important;
            color: #FFFFFF !important;
            border-color: #2a2a35 !important;
        }
        
        [data-theme="dark"] .modern-table thead {
            background: linear-gradient(135deg, #25252d 0%, #2d2d3a 100%) !important;
            color: #FFFFFF !important;
        }
        
        [data-theme="dark"] .modern-table tbody tr {
            border-color: #2a2a35 !important;
            background: #1a1a1f !important;
        }
        
        [data-theme="dark"] .modern-table tbody tr:hover {
            background: #25252d !important;
        }
        
        [data-theme="dark"] .modern-table tbody td {
            color: #FFFFFF !important;
        }
        
        [data-theme="dark"] .modern-table thead th {
            color: #FFFFFF !important;
        }
        
        /* Cards with white background - text color #9B5FFF */
        [data-theme="dark"] .card[style*="background"]:not([style*="background: var"]),
        [data-theme="dark"] .card[style*="background-color"]:not([style*="background-color: var"]),
        [data-theme="dark"] [style*="background: white"],
        [data-theme="dark"] [style*="background-color: white"],
        [data-theme="dark"] [style*="background: #FFFFFF"],
        [data-theme="dark"] [style*="background-color: #FFFFFF"],
        [data-theme="dark"] [style*="background: #fff"],
        [data-theme="dark"] [style*="background-color: #fff"] {
            background: #FFFFFF !important;
        }
        
        /* Force text color on all white cards */
        [data-theme="dark"] .card[style*="background: white"] *,
        [data-theme="dark"] .card[style*="background-color: white"] *,
        [data-theme="dark"] .card[style*="background: #FFFFFF"] *,
        [data-theme="dark"] .card[style*="background-color: #FFFFFF"] *,
        [data-theme="dark"] .card[style*="background: #fff"] *,
        [data-theme="dark"] .card[style*="background-color: #fff"] *,
        [data-theme="dark"] [style*="background: white"] *,
        [data-theme="dark"] [style*="background-color: white"] *,
        [data-theme="dark"] [style*="background: #FFFFFF"] *,
        [data-theme="dark"] [style*="background-color: #FFFFFF"] * {
            color: #9B5FFF !important;
        }
        
        /* All cards - if they have white background, make text purple */
        [data-theme="dark"] .card,
        [data-theme="dark"] .card-body {
            background: #FFFFFF !important;
        }
        
        [data-theme="dark"] .card *:not(button):not(.btn):not(a):not(i):not(.fa):not(.fas):not(.far):not(.fab):not(.fal),
        [data-theme="dark"] .card-body *:not(button):not(.btn):not(a):not(i):not(.fa):not(.fas):not(.far):not(.fab):not(.fal),
        [data-theme="dark"] .card p,
        [data-theme="dark"] .card span,
        [data-theme="dark"] .card div,
        [data-theme="dark"] .card td,
        [data-theme="dark"] .card th,
        [data-theme="dark"] .card li,
        [data-theme="dark"] .card label,
        [data-theme="dark"] .card-body p,
        [data-theme="dark"] .card-body span,
        [data-theme="dark"] .card-body div,
        [data-theme="dark"] .card-body td,
        [data-theme="dark"] .card-body th,
        [data-theme="dark"] .card-body li,
        [data-theme="dark"] .card-body label {
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
        
        [data-theme="dark"] .form-control,
        [data-theme="dark"] .form-select {
            background: #25252d !important;
            border-color: #2a2a35 !important;
            color: #FFFFFF !important;
        }
        
        [data-theme="dark"] .form-control:focus,
        [data-theme="dark"] .form-select:focus {
            background: #25252d !important;
            border-color: #9B5FFF !important;
            color: #FFFFFF !important;
            box-shadow: 0 0 0 0.25rem rgba(155, 95, 255, 0.25) !important;
        }
        
        [data-theme="dark"] .form-control::placeholder,
        [data-theme="dark"] .form-select::placeholder {
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] h1,
        [data-theme="dark"] h2,
        [data-theme="dark"] h3,
        [data-theme="dark"] h4,
        [data-theme="dark"] h5,
        [data-theme="dark"] h6 {
            color: #FFFFFF !important;
        }
        
        [data-theme="dark"] .page-header-modern h4 {
            color: #FFFFFF !important;
        }
        
        [data-theme="dark"] .main-header .user-info,
        [data-theme="dark"] .main-header .user-info span {
            color: #FFFFFF !important;
        }
        
        [data-theme="dark"] .main-header .user-avatar {
            background: rgba(155, 95, 255, 0.2) !important;
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .main-header .logout-btn {
            background: rgba(155, 95, 255, 0.15) !important;
            color: #FFFFFF !important;
        }
        
        [data-theme="dark"] .main-header .logout-btn:hover {
            background: rgba(155, 95, 255, 0.25) !important;
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .btn-modern-primary {
            background: linear-gradient(135deg, #9B5FFF 0%, #7A35FF 100%) !important;
            color: white !important;
        }
        
        [data-theme="dark"] .btn-modern-primary:hover {
            background: linear-gradient(135deg, #7A35FF 0%, #9B5FFF 100%) !important;
            box-shadow: 0 5px 15px rgba(122, 53, 255, 0.4) !important;
        }
        
        [data-theme="dark"] .badge-modern-primary {
            background: linear-gradient(135deg, #9B5FFF 0%, #7A35FF 100%) !important;
        }
        
        [data-theme="dark"] p,
        [data-theme="dark"] span,
        [data-theme="dark"] div,
        [data-theme="dark"] td,
        [data-theme="dark"] th {
            color: inherit;
        }
        
        [data-theme="dark"] .text-muted {
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .text-secondary {
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .stat-label {
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] code {
            background: #25252d !important;
            color: #9B5FFF !important;
            border: 1px solid #2a2a35 !important;
        }
        
        [data-theme="dark"] .alert-modern {
            background: #1a1a1f !important;
            color: #FFFFFF !important;
            border-color: #2a2a35 !important;
        }
        
        [data-theme="dark"] .alert-modern.alert-success {
            background: rgba(40, 167, 69, 0.15) !important;
            border-right: 4px solid #28a745 !important;
            color: #71dd88 !important;
        }
        
        [data-theme="dark"] .alert-modern.alert-danger {
            background: rgba(220, 53, 69, 0.15) !important;
            border-right: 4px solid #dc3545 !important;
            color: #f5c2c7 !important;
        }
        
        [data-theme="dark"] .alert-modern.alert-warning {
            background: rgba(255, 193, 7, 0.15) !important;
            border-right: 4px solid #ffc107 !important;
            color: #ffda6a !important;
        }
        
        [data-theme="dark"] .dark-mode-toggle {
            background: rgba(155, 95, 255, 0.2) !important;
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .dark-mode-toggle:hover {
            background: rgba(155, 95, 255, 0.3) !important;
            color: #9B5FFF !important;
        }
        
        [data-theme="dark"] .notification-item {
            background: #1a1a1f !important;
            border-color: #2a2a35 !important;
        }
        
        [data-theme="dark"] .notification-item:hover {
            background: #25252d !important;
        }
        
        [data-theme="dark"] .notification-item.unread {
            background: rgba(155, 95, 255, 0.1) !important;
            border-right-color: #9B5FFF !important;
        }
    
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            direction: rtl;
            text-align: right;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* ======================== */
        /* ======= HEADER ========= */
        /* ======================== */
        .main-header {
            width: 100%;
            height: 70px;
            background: linear-gradient(135deg, #1512c8ff 0%, #1c1a1dff 100%);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            position: fixed;
            top: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .main-header .logo {
            font-size: 22px;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .main-header .logo i {
            font-size: 28px;
        }

        .main-header .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .main-header .user-info {
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .main-header .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: bold;
        }

        .main-header .logout-btn {
            color: #fff;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 8px;
            background: rgba(255,255,255,0.15);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .main-header .logout-btn:hover {
            background: rgba(255,255,255,0.25);
            color: #fff;
            transform: translateY(-2px);
        }

        .main-header .notification-menu {
            position: relative;
        }

        .main-header .notification-menu .dropdown-menu {
            margin-top: 10px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            border: none;
        }

        .main-header .notification-menu .badge {
            font-size: 10px;
            padding: 2px 6px;
        }

        /* ======================== */
        /* ===== SIDEBAR ========== */
        /* ======================== */
        .sidebar {
            position: fixed;
            top: 70px;
            right: 0;
            width: 260px;
            height: calc(100vh - 70px);
            background: linear-gradient(135deg, #1c1a1dff 0%, #1512c8ff 100%);
            color: #fff;
            padding-top: 20px;
            overflow-y: auto;
            z-index: 999;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.3);
        }

        .sidebar .menu-section {
            padding: 15px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar .menu-section:last-child {
            border-bottom: none;
        }

        .sidebar .menu-title {
            color: rgba(255,255,255,0.5);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 20px 10px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 15px;
            transition: all 0.3s ease;
            border-right: 3px solid transparent;
            gap: 12px;
        }

        .sidebar a i {
            width: 20px;
            font-size: 18px;
            text-align: center;
        }

        .sidebar a:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-right-color: #7424a9;
            transform: translateX(-5px);
        }

        .sidebar a.active {
            background: rgba(116, 36, 169, 0.2);
            color: #fff;
            border-right-color: #7424a9;
            font-weight: 600;
        }

        /* ======================== */
        /* ===== CONTENT ========== */
        /* ======================== */
        .content {
            margin-right: 260px;
            margin-top: 70px;
            padding: 30px;
            min-height: calc(100vh - 70px);
            transition: all 0.3s ease;
        }

        /* ======================== */
        /* ===== FOOTER =========== */
        /* ======================== */
        .main-footer {
            text-align: center;
            padding: 20px;
            margin-top: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            color: #6c757d;
        }

        /* ======================== */
        /* ===== RESPONSIVE ======== */
        /* ======================== */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .content {
                margin-right: 0;
                padding: 20px 15px;
            }

            .main-header {
                padding: 0 15px;
            }

            .main-header .logo {
                font-size: 18px;
            }

            .sidebar-toggle {
                display: block !important;
            }
        }

        .sidebar-toggle {
            display: none;
            background: rgba(255,255,255,0.15);
            border: none;
            color: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 20px;
        }

        /* Modern Cards */
        .modern-card {
            background: white;
        }
        
        [data-theme="dark"] .modern-card {
            background: #FFFFFF !important;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            padding: 25px;
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }

        .modern-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        /* Page Header */
        .page-header-modern {
            background: white;
            border-radius: 15px;
            padding: 25px 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .page-header-modern h4 {
            color: #2c3e50;
            font-weight: 700;
            margin: 0;
            font-size: 24px;
        }

        /* Alerts */
        .alert-modern {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        /* Buttons */
        .btn-modern {
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            text-decoration: none;
        }

        .btn-modern-primary {
            background: linear-gradient(135deg, #7424a9 0%, #9d4edd 100%);
            color: white;
        }

        .btn-modern-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-modern-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        .btn-modern-warning {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: white;
        }

        .btn-modern-info {
            background: linear-gradient(135deg, #17a2b8 0%, #5bc0de 100%);
            color: white;
        }

        .btn-modern-secondary {
            background: #6c757d;
            color: white;
        }

        /* Tables */
        .modern-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .modern-table thead {
            background: linear-gradient(135deg, #7424a9 0%, #9d4edd 100%);
            color: white;
        }

        .modern-table thead th {
            border: none;
            padding: 15px;
            font-weight: 600;
            text-align: center;
        }

        .modern-table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #e9ecef;
        }

        .modern-table tbody tr:hover {
            background: #f8f9fa;
        }

        .modern-table tbody td {
            padding: 15px;
            text-align: center;
            vertical-align: middle;
        }

        /* Badges */
        .badge-modern {
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }

        .badge-modern-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .badge-modern-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        .badge-modern-primary {
            background: linear-gradient(135deg, #7424a9 0%, #9d4edd 100%);
            color: white;
        }

        /* Stat Cards for Dashboard */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-right: 4px solid;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .stat-card.primary { border-right-color: #7424a9; }
        .stat-card.success { border-right-color: #28a745; }
        .stat-card.warning { border-right-color: #ffc107; }
        .stat-card.info { border-right-color: #17a2b8; }
        .stat-card.danger { border-right-color: #dc3545; }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 15px;
        }

        .stat-icon.primary { background: linear-gradient(135deg, #7424a9 0%, #9d4edd 100%); }
        .stat-icon.success { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
        .stat-icon.warning { background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); }
        .stat-icon.info { background: linear-gradient(135deg, #17a2b8 0%, #5bc0de 100%); }
        .stat-icon.danger { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); }

        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin: 10px 0;
        }

        .stat-label {
            color: #6c757d;
            font-size: 14px;
            font-weight: 500;
        }

        /* Form Controls */
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #7424a9;
            box-shadow: 0 0 0 0.25rem rgba(116, 36, 169, 0.25);
            outline: none;
        }

        /* Checkbox Styling */
        .form-check-input {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #7424a9;
        }

        /* Notification Dropdown */
        .notification-dropdown {
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-item {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        .notification-item:hover {
            background: #f8f9fa;
        }

        .notification-item.unread {
            background: #f0f4ff;
            border-right: 3px solid var(--primary-color);
        }

        .notification-item .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        <?php echo $__env->yieldContent('css'); ?>
    </style>
</head>

<body>
    
    <div class="main-header">
        <div style="display: flex; align-items: center; gap: 15px; flex: 1;">
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="logo">
                <i class="fas fa-graduation-cap"></i>
                <span>لوحة التحكم</span>
            </div>
            <!-- Smart Search Bar (desktop) -->
            <div class="smart-search-container d-none d-md-block">
                <input type="text" class="smart-search-input" placeholder="ابحث عن طلاب، محاضرات، امتحانات..." autocomplete="off">
                <i class="fas fa-search smart-search-icon"></i>
                <div class="smart-search-results"></div>
            </div>

            <!-- Mobile Search Button + Hidden Mobile Search Container -->
            <button id="mobileSearchBtn" class="btn btn-sm btn-light d-md-none" style="background: rgba(255,255,255,0.08); color: #fff; border: none; margin-right:8px;">
                <i class="fas fa-search"></i>
            </button>

            <div id="mobileSearchContainer" class="smart-search-container d-md-none" style="display: none; position: absolute; right: 0; left: 0; top: 70px; padding: 12px; background: transparent; z-index: 1050;">
                <div style="max-width: 700px; margin: 0 auto;">
                    <input type="text" class="smart-search-input" placeholder="ابحث عن طلاب، محاضرات، امتحانات..." autocomplete="off">
                    <i class="fas fa-search smart-search-icon"></i>
                    <div class="smart-search-results"></div>
                </div>
            </div>
        </div>
        <div class="user-menu">
            <!-- Dark Mode Toggle Button -->
            <button class="dark-mode-toggle" id="darkModeToggle" style="background: rgba(255,255,255,0.15); border: none; color: white; padding: 8px 12px; border-radius: 8px; cursor: pointer; font-size: 18px; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; min-width: 40px; min-height: 40px;">
                <i class="fas fa-moon"></i>
            </button>
            
            <!-- Notifications -->
            <div class="notification-menu" style="position: relative; margin-left: 20px;">
                <?php
                    try {
                        $unreadCount = \App\Models\Notification::where(function($query) {
                                $query->whereNull('notifiable_type')
                                      ->orWhere('notifiable_type', '!=', \App\Models\Student::class);
                            })
                            ->where('is_read', false)
                            ->count();
                    } catch (\Exception $e) {
                        $unreadCount = 0;
                    }
                ?>
                <a href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: white; font-size: 20px; text-decoration: none; position: relative;">
                    <i class="fas fa-bell"></i>
                    <?php if($unreadCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px; padding: 2px 6px;">
                            <?php echo e($unreadCount > 99 ? '99+' : $unreadCount); ?>

                        </span>
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationsDropdown" style="width: 350px; max-height: 400px; overflow-y: auto; margin-top: 10px;">
                    <li>
                        <div class="dropdown-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">الإشعارات</h6>
                            <?php if($unreadCount > 0): ?>
                                <form action="<?php echo e(route('admin.notifications.markAllRead')); ?>" method="POST" style="display: inline;">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-sm btn-link text-decoration-none" style="font-size: 12px; padding: 0;">تحديد الكل كمقروء</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <?php
                        try {
                            $latestNotifications = \App\Models\Notification::where(function($query) {
                                    $query->whereNull('notifiable_type')
                                          ->orWhere('notifiable_type', '!=', \App\Models\Student::class);
                                })
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
                        } catch (\Exception $e) {
                            $latestNotifications = collect();
                        }
                    ?>
                    <?php if($latestNotifications->count() > 0): ?>
                        <?php $__currentLoopData = $latestNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <a class="dropdown-item notification-item <?php echo e(!$notification->is_read ? 'unread' : ''); ?>" href="<?php echo e($notification->url ?? route('admin.notifications.index')); ?>">
                                <div class="d-flex align-items-start">
                                    <div class="notification-icon me-2" style="color: <?php echo e($notification->color === 'success' ? '#28a745' : ($notification->color === 'danger' ? '#dc3545' : ($notification->color === 'warning' ? '#ffc107' : '#17a2b8'))); ?>;">
                                        <i class="fas <?php echo e($notification->icon ?? 'fa-info-circle'); ?>"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold" style="font-size: 13px;"><?php echo e($notification->title); ?></div>
                                        <div class="text-muted" style="font-size: 11px;"><?php echo e(Str::limit($notification->message, 50)); ?></div>
                                        <small class="text-muted" style="font-size: 10px;"><?php echo e($notification->created_at->diffForHumans()); ?></small>
                                    </div>
                                    <?php if(!$notification->is_read): ?>
                                        <span class="badge bg-primary rounded-pill" style="font-size: 8px;">جديد</span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <li>
                            <div class="dropdown-item text-center text-muted" style="font-size: 13px;">
                                لا توجد إشعارات
                            </div>
                        </li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-center" href="<?php echo e(route('admin.notifications.index')); ?>">
                            <strong>عرض جميع الإشعارات</strong>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <span><?php echo e(Auth::guard('web')->user()->name ?? 'Admin'); ?></span>
            </div>
            <a href="<?php echo e(route('logoutAdmin')); ?>" class="logout-btn" onclick="return confirm('هل أنت متأكد من تسجيل الخروج؟')">
                <i class="fas fa-sign-out-alt"></i>
                <span class="d-none d-md-inline">تسجيل خروج</span>
            </a>
        </div>
    </div>

    
    <div class="sidebar" id="sidebar">
        <div class="menu-section">
            <a href="<?php echo e(url('/admin')); ?>" class="<?php echo e(request()->is('admin') && !request()->is('admin/*') ? 'active' : ''); ?>">
                <i class="fas fa-home"></i>
                <span>الرئيسية</span>
            </a>
        </div>

        <div class="menu-section">
            <div class="menu-title">الطلاب</div>
            <a href="<?php echo e(route('admin.student.create')); ?>" class="<?php echo e(request()->is('admin/add-student') || request()->is('admin/create-student') ? 'active' : ''); ?>">
                <i class="fas fa-user-plus"></i>
                <span>إضافة طالب</span>
            </a>
            <a href="<?php echo e(route('students.all')); ?>" class="<?php echo e(request()->is('students-all*') ? 'active' : ''); ?>">
                <i class="fas fa-users"></i>
                <span>جميع الطلاب</span>
            </a>
            <a href="<?php echo e(route('admin.excel.import.students')); ?>" class="<?php echo e(request()->is('admin/excel/import/students*') ? 'active' : ''); ?>">
                <i class="fas fa-file-excel"></i>
                <span>استيراد الطلاب من Excel</span>
            </a>
            <a href="<?php echo e(route('admin.excel.import.subscriptions')); ?>" class="<?php echo e(request()->is('admin/excel/import/subscriptions*') ? 'active' : ''); ?>">
                <i class="fas fa-file-upload"></i>
                <span>استيراد الاشتراكات من Excel</span>
            </a>
            <a href="<?php echo e(route('admin.excel.deactivate.subscriptions')); ?>" class="<?php echo e(request()->is('admin/excel/deactivate/subscriptions*') ? 'active' : ''); ?>">
                <i class="fas fa-user-times"></i>
                <span>إلغاء تفعيل الاشتراكات جماعياً</span>
            </a>
            <a href="<?php echo e(route('admin.excel.activated.students')); ?>" class="<?php echo e(request()->is('admin/excel/activated-students*') ? 'active' : ''); ?>">
                <i class="fas fa-check-circle"></i>
                <span>الطلاب المفعلين من Excel</span>
            </a>
            <a href="<?php echo e(route('admin.students.all_access.form')); ?>" class="<?php echo e(request()->is('students/all-access') ? 'active' : ''); ?>">
                <i class="fas fa-unlock-alt"></i>
                <span>اشتراك شامل للطلاب</span>
            </a>
        </div>

        <div class="menu-section">
            <div class="menu-title">إدارة النظام</div>
            <?php if(auth()->user()->hasRole('Super Admin')): ?>
            <a href="<?php echo e(route('admin.general_settings.index')); ?>" class="<?php echo e(request()->is('admin/general-settings*') ? 'active' : ''); ?>">
                <i class="fas fa-cog"></i>
                <span>الإعدادات العامة</span>
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('admin.users.index')); ?>" class="<?php echo e(request()->is('admin/users*') ? 'active' : ''); ?>">
                <i class="fas fa-users-cog"></i>
                <span>المستخدمين</span>
            </a>
            <a href="<?php echo e(route('admin.roles.index')); ?>" class="<?php echo e(request()->is('admin/roles*') ? 'active' : ''); ?>">
                <i class="fas fa-user-shield"></i>
                <span>الأدوار</span>
            </a>
        </div>

        <div class="menu-section">
            <div class="menu-title">المحتوى التعليمي</div>
            <a href="<?php echo e(route('month.index')); ?>" class="<?php echo e(request()->is('month*') ? 'active' : ''); ?>">
                <i class="fas fa-calendar-alt"></i>
                <span>الأشهر</span>
            </a>
            <a href="<?php echo e(route('lecture.index')); ?>" class="<?php echo e(request()->is('lecture*') ? 'active' : ''); ?>">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>المحاضرات</span>
            </a>
            <a href="<?php echo e(route('pdf.index')); ?>" class="<?php echo e(request()->is('pdf*') ? 'active' : ''); ?>">
                <i class="fas fa-file-pdf"></i>
                <span>المذكرات</span>
            </a>
            <a href="<?php echo e(route('exam_name.index')); ?>" class="<?php echo e(request()->is('exam_name*') || request()->is('add-question*') ? 'active' : ''); ?>">
                <i class="fas fa-book-open"></i>
                <span>الامتحانات</span>
            </a>
                    <a href="<?php echo e(route('assignments.index')); ?>" class="<?php echo e(request()->is('assignments*') ? 'active' : ''); ?>">
                        <i class="fas fa-tasks"></i>
                        <span>الواجبات</span>
                    </a>

        </div>

        <div class="menu-section">
            <div class="menu-title">الاشتراكات والنتائج</div>
            <a href="<?php echo e(route('student_subscription.index')); ?>" class="<?php echo e(request()->is('student_subscription*') ? 'active' : ''); ?>">
                <i class="fas fa-user-graduate"></i>
                <span>اشتراكات الطلاب</span>
            </a>
            <a href="<?php echo e(route('admin.blocked_students.index')); ?>" class="<?php echo e(request()->is('admin/blocked-students*') ? 'active' : ''); ?>">
                <i class="fas fa-ban"></i>
                <span>الطلاب المحظورين</span>
            </a>
            <?php if(is_payment_method_enabled('online_payment')): ?>
            <a href="<?php echo e(route('admin.payments.index')); ?>" class="<?php echo e(request()->is('admin/payments*') ? 'active' : ''); ?>">
                <i class="fas fa-money-bill-wave"></i>
                <span>المدفوعات</span>
            </a>
            <a href="<?php echo e(route('admin.payments.statistics')); ?>" class="<?php echo e(request()->is('admin/payments/statistics*') ? 'active' : ''); ?>">
                <i class="fas fa-chart-pie"></i>
                <span>إحصائيات المدفوعات</span>
            </a>
            <a href="<?php echo e(route('admin.kashier.index')); ?>" class="<?php echo e(request()->is('admin/kashier*') ? 'active' : ''); ?>">
                <i class="fas fa-credit-card"></i>
                <span>إعدادات Kashier</span>
            </a>
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('publicExam.results')); ?>" class="<?php echo e(request()->is('admin/public-exam-results*') ? 'active' : ''); ?>">
                <i class="fas fa-chart-line"></i>
                <span>نتائج الامتحانات العامة</span>
            </a>
        </div>

        <div class="menu-section">
            <div class="menu-title">الإحصائيات والتقارير</div>
            <a href="<?php echo e(route('admin.analytics.dashboard')); ?>" class="<?php echo e(request()->is('admin/analytics/dashboard') ? 'active' : ''); ?>">
                <i class="fas fa-chart-bar"></i>
                <span>لوحة الإحصائيات</span>
            </a>
            <a href="<?php echo e(route('admin.analytics.top.students')); ?>" class="<?php echo e(request()->is('admin/analytics/top-students*') ? 'active' : ''); ?>">
                <i class="fas fa-trophy"></i>
                <span>أعلى 10 طلاب</span>
            </a>
            <a href="<?php echo e(route('admin.analytics.student.performance')); ?>" class="<?php echo e(request()->is('admin/analytics/student-performance') ? 'active' : ''); ?>">
                <i class="fas fa-user-check"></i>
                <span>أداء الطلاب</span>
            </a>
            <?php if(is_payment_method_enabled('online_payment')): ?>
            <a href="<?php echo e(route('admin.analytics.revenue')); ?>" class="<?php echo e(request()->is('admin/analytics/revenue') ? 'active' : ''); ?>">
                <i class="fas fa-dollar-sign"></i>
                <span>تقارير الإيرادات</span>
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('admin.analytics.content.usage')); ?>" class="<?php echo e(request()->is('admin/analytics/content-usage') ? 'active' : ''); ?>">
                <i class="fas fa-fire"></i>
                <span>استخدام المحتوى</span>
            </a>
        </div>

        <div class="menu-section">
            <div class="menu-title">إدارة المحتوى</div>
            <a href="<?php echo e(route('admin.media.index')); ?>" class="<?php echo e(request()->is('admin/media*') ? 'active' : ''); ?>">
                <i class="fas fa-images"></i>
                <span>مكتبة الوسائط</span>
            </a>
            <a href="<?php echo e(route('admin.content.schedule.index')); ?>" class="<?php echo e(request()->is('admin/content/schedule*') ? 'active' : ''); ?>">
                <i class="fas fa-calendar-alt"></i>
                <span>جدولة المحتوى</span>
            </a>
        </div>

        <div class="menu-section">
            <div class="menu-title">التسويق والترويج</div>
            <a href="<?php echo e(route('admin.discount_codes.index')); ?>" class="<?php echo e(request()->is('admin/discount-codes*') ? 'active' : ''); ?>">
                <i class="fas fa-tag"></i>
                <span>أكواد الخصم والحزم</span>
            </a>
            <?php if(is_payment_method_enabled('activation_codes')): ?>
            <a href="<?php echo e(route('admin.activation_codes.index')); ?>" class="<?php echo e(request()->is('admin/activation-codes*') ? 'active' : ''); ?>">
                <i class="fas fa-key"></i>
                <span>أكواد التفعيل</span>
            </a>
            <?php endif; ?>
            <a href="<?php echo e(route('admin.lecture_restrictions.index')); ?>" class="<?php echo e(request()->is('admin/lecture-restrictions*') ? 'active' : ''); ?>">
                <i class="fas fa-ban"></i>
                <span>قيود المحاضرات</span>
            </a>
            <a href="<?php echo e(route('admin.seo.index')); ?>" class="<?php echo e(request()->is('admin/seo*') ? 'active' : ''); ?>">
                <i class="fas fa-search"></i>
                <span>إعدادات SEO</span>
            </a>
        </div>

        <div class="menu-section">
            <div class="menu-title">الإشعارات</div>
            <a href="<?php echo e(route('admin.notifications.index')); ?>" class="<?php echo e(request()->is('admin/notifications*') ? 'active' : ''); ?>">
                <i class="fas fa-bell"></i>
                <span>الإشعارات</span>
                <?php
                    try {
                        $sidebarUnreadCount = \App\Models\Notification::where(function($query) {
                                $query->whereNull('notifiable_type')
                                      ->orWhere('notifiable_type', '!=', \App\Models\Student::class);
                            })
                            ->where('is_read', false)
                            ->count();
                    } catch (\Exception $e) {
                        $sidebarUnreadCount = 0;
                    }
                ?>
                <?php if($sidebarUnreadCount > 0): ?>
                    <span class="badge bg-danger ms-2" style="font-size: 10px; padding: 2px 6px;"><?php echo e($sidebarUnreadCount); ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    
    <div class="content">
        <?php echo $__env->yieldContent('page-header'); ?>

        <?php echo $__env->yieldContent('content'); ?>
    </div>

    
    <div class="main-footer">
        <p class="mb-0">جميع الحقوق محفوظة © <?php echo e(date('Y')); ?> - منصة مستر حماده مراد</p>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (Full version for AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Admin Enhancements JS -->
    <script src="<?php echo e(URL::asset('js/admin-enhancements.js')); ?>"></script>
    
    <script>
        // Function to apply dark mode styles to white cards (global scope)
        window.applyDarkModeToCards = function() {
            if (document.documentElement.getAttribute('data-theme') === 'dark') {
                console.log('Applying dark mode styles to white cards...');
                // Apply to all white cards
                const cards = document.querySelectorAll('.modern-card, .card, .stats-card, .notification-card, .course-card, .feature-card, .lecture-card, .reason-card, .stat-card');
                console.log('Found', cards.length, 'cards');
                
                cards.forEach(card => {
                    // Force white background and purple text
                    card.style.setProperty('background', '#FFFFFF', 'important');
                    card.style.setProperty('color', '#9B5FFF', 'important');
                    
                    // Apply to all children
                    card.querySelectorAll('*:not(button):not(.btn):not(a):not(i):not(.fa):not(.fas):not(.far):not(.fab):not(.fal):not(.badge):not(.alert):not(.btn-close)').forEach(child => {
                        const tagName = child.tagName.toLowerCase();
                        if (tagName !== 'h1' && tagName !== 'h2' && tagName !== 'h3' && tagName !== 'h4' && tagName !== 'h5' && tagName !== 'h6') {
                            child.style.setProperty('color', '#9B5FFF', 'important');
                        }
                    });
                    
                    // Headings - also purple
                    card.querySelectorAll('h1, h2, h3, h4, h5, h6').forEach(heading => {
                        heading.style.setProperty('color', '#9B5FFF', 'important');
                    });
                });
                
                console.log('Dark mode styles applied to', cards.length, 'cards');
            }
        };
        
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
            
            // Update icon
            const btn = document.getElementById('darkModeToggle');
            if (btn) {
                const icon = btn.querySelector('i');
                if (icon) {
                    icon.className = newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                }
            }
            
            console.log('Theme changed to:', newTheme);
            console.log('HTML data-theme after change:', html.getAttribute('data-theme'));
            
            // Apply styles to cards after toggle
            setTimeout(() => {
                window.applyDarkModeToCards();
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
            
            // Apply dark mode styles to cards if dark mode is active
            if (savedTheme === 'dark') {
                setTimeout(window.applyDarkModeToCards, 100);
                setTimeout(window.applyDarkModeToCards, 500);
            }
            
            // Add click handler to button (only once)
            const btn = document.getElementById('darkModeToggle');
            if (btn) {
                // Remove any existing handlers by cloning
                const newBtn = btn.cloneNode(true);
                btn.parentNode.replaceChild(newBtn, btn);
                
                // Add single event listener only
                newBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    toggleDarkMode();
                    // Apply styles to cards after toggle
                    setTimeout(window.applyDarkModeToCards, 100);
                    return false;
                }, { once: false, passive: false, capture: false });
                
                // Update icon
                const icon = newBtn.querySelector('i');
                if (icon && savedTheme === 'dark') {
                    icon.className = 'fas fa-sun';
                }
            }
        }
        
        // Apply styles when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(window.applyDarkModeToCards, 100);
            });
        } else {
            setTimeout(window.applyDarkModeToCards, 100);
        }
        
        // Apply styles on theme change
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'data-theme') {
                    setTimeout(window.applyDarkModeToCards, 100);
                }
            });
        });
        
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-theme']
        });
    </script>

    <script>
        // Toggle sidebar on mobile
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.querySelector('.sidebar-toggle');
            
            if (window.innerWidth <= 768) {
                if (sidebar && toggleBtn && !sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            if (typeof $ !== 'undefined') {
                $('.alert').fadeOut('slow');
            }
        }, 5000);
    </script>

    <script>
        // Initialize Smart Search for all containers (desktop + mobile)
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, initializing search for containers...');

            const containers = document.querySelectorAll('.smart-search-container');
            if (!containers || containers.length === 0) {
                console.warn('No search containers found');
                return;
            }

            containers.forEach(function(searchContainer) {
                const searchInput = searchContainer.querySelector('.smart-search-input');
                const resultsContainer = searchContainer.querySelector('.smart-search-results');

                if (!searchInput || !resultsContainer) {
                    console.warn('Search input or results container missing for one container');
                    return;
                }

                let searchTimeout;

                searchInput.addEventListener('input', function(e) {
                    clearTimeout(searchTimeout);
                    const query = e.target.value.trim();

                    if (query.length < 2) {
                        resultsContainer.classList.remove('show');
                        resultsContainer.innerHTML = '';
                        return;
                    }

                    searchTimeout = setTimeout(function() {
                        // Show loading
                        resultsContainer.innerHTML = '<div class="loading-state"><i class="fas fa-spinner"></i> جاري البحث...</div>';
                        resultsContainer.classList.add('show');

                        // Perform search (reuse existing endpoint)
                        fetch('/admin/search?q=' + encodeURIComponent(query), {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            credentials: 'same-origin'
                        })
                        .then(function(response) {
                            if (!response.ok) throw new Error('HTTP ' + response.status);
                            return response.json();
                        })
                        .then(function(data) {
                            if (!data.results || data.results.length === 0) {
                                resultsContainer.innerHTML = '<div class="empty-state"><i class="fas fa-search"></i><p>لا توجد نتائج</p></div>';
                                resultsContainer.classList.add('show');
                                return;
                            }

                            let html = '';
                            data.results.forEach(function(item) {
                                html += '<a href="' + item.url + '" class="smart-search-result-item" style="text-decoration: none; display: flex; align-items: center; padding: 15px;">';
                                html += '<div class="result-icon"><i class="fas ' + (item.icon || 'fa-info-circle') + '"></i></div>';
                                html += '<div style="flex: 1;"><div class="result-title">' + item.title + '</div>';
                                html += '<div class="result-subtitle">' + (item.subtitle || '') + '</div></div>';
                                html += '</a>';
                            });

                            resultsContainer.innerHTML = html;
                            resultsContainer.classList.add('show');
                        })
                        .catch(function(error) {
                            console.error('Search error:', error);
                            resultsContainer.innerHTML = '<div class="error-state"><i class="fas fa-exclamation-circle"></i><p>حدث خطأ أثناء البحث</p></div>';
                            resultsContainer.classList.add('show');
                        });
                    }, 300);
                });

                // Close results on outside click for this container
                document.addEventListener('click', function(e) {
                    if (!searchContainer.contains(e.target)) {
                        resultsContainer.classList.remove('show');
                    }
                });
            });

            // Mobile search toggle
            const mobileBtn = document.getElementById('mobileSearchBtn');
            const mobileContainer = document.getElementById('mobileSearchContainer');
            if (mobileBtn && mobileContainer) {
                mobileBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Toggle visibility
                    if (mobileContainer.style.display === 'none' || mobileContainer.style.display === '') {
                        mobileContainer.style.display = 'block';
                        // focus input inside mobile container
                        const input = mobileContainer.querySelector('.smart-search-input');
                        if (input) {
                            input.focus();
                            input.select();
                        }
                    } else {
                        mobileContainer.style.display = 'none';
                    }
                });

                // Hide mobile container on outside click
                document.addEventListener('click', function(e) {
                    if (!mobileContainer.contains(e.target) && !mobileBtn.contains(e.target)) {
                        mobileContainer.style.display = 'none';
                    }
                });
            }

            console.log('Search initialized for', containers.length, 'container(s)');
        });
    </script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/colreorder/1.7.0/js/dataTables.colReorder.min.js"></script>
    
    <!-- Enhanced DataTables Initialization -->
    <script>
        // Auto-initialize DataTables for all tables with id="datatable" (only if not already initialized)
        // Skip auto-initialization if page has custom DataTable initialization (data-custom-init="true")
        $(document).ready(function() {
            // Check if there's a custom DataTable initialization in the page's js section
            // We'll use a small delay to let page-specific scripts run first
            setTimeout(function() {
                // Skip if table has data-custom-init attribute (page will handle initialization)
                if ($('#datatable').length && !$.fn.DataTable.isDataTable('#datatable') && !$('#datatable').data('custom-init')) {
                    var table = $('#datatable').DataTable({
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/ar.json'
                        },
                        dom: '<"row"<"col-md-6"l><"col-md-6"f>>rtip',
                        buttons: [
                            {
                                extend: 'excel',
                                text: '<i class="fas fa-file-excel"></i> Excel',
                                className: 'btn btn-success btn-sm',
                                exportOptions: {
                                    columns: ':visible'
                                }
                            },
                            {
                                extend: 'pdf',
                                text: '<i class="fas fa-file-pdf"></i> PDF',
                                className: 'btn btn-danger btn-sm',
                                exportOptions: {
                                    columns: ':visible'
                                },
                                customize: function(doc) {
                                    doc.defaultStyle.fontSize = 10;
                                    doc.styles.tableHeader.fontSize = 12;
                                    doc.defaultStyle.alignment = 'right';
                                    doc.defaultStyle.direction = 'rtl';
                                }
                            },
                            {
                                extend: 'print',
                                text: '<i class="fas fa-print"></i> طباعة',
                                className: 'btn btn-info btn-sm'
                            }
                        ],
                        colReorder: true,
                        pageLength: 25,
                        responsive: true,
                        order: [],
                        columnDefs: [
                            { orderable: true, targets: '_all' }
                        ],
                        initComplete: function() {
                            // Add export buttons to table wrapper
                            var buttons = table.buttons().container();
                            buttons.appendTo($('#datatable_wrapper .row:first-child .col-md-6:first'));
                        }
                    });
                }
            }, 100); // Small delay to let page-specific scripts initialize first
        });
    </script>
    
    <?php echo $__env->yieldContent('js'); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>

    
    <?php if(auth()->guard('web')->check()): ?>
    <div class="cache-control-panel" style="position: fixed; bottom: 20px; left: 20px; z-index: 9999; background: white; padding: 15px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.2); display: none;" id="cachePanel">
        <h6 class="mb-2"><i class="fas fa-bolt"></i> Cache Control</h6>
        <form action="<?php echo e(route('admin.cache.clear')); ?>" method="POST" style="margin-bottom: 10px;" onsubmit="return confirm('هل أنت متأكد من مسح جميع الـ Cache؟')">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-sm btn-danger w-100">
                <i class="fas fa-trash"></i> Clear All Cache
            </button>
        </form>
        <form action="<?php echo e(route('admin.cache.clearSpecific')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <select name="type" class="form-select form-select-sm mb-2">
                <option value="lectures">Lectures Cache</option>
                <option value="stats">Stats Cache</option>
            </select>
            <button type="submit" class="btn btn-sm btn-warning w-100">
                <i class="fas fa-sync"></i> Clear Specific
            </button>
        </form>
        <button class="btn btn-sm btn-secondary w-100 mt-2" onclick="document.getElementById('cachePanel').style.display='none'">
            <i class="fas fa-times"></i> Close
        </button>
    </div>
    <button onclick="document.getElementById('cachePanel').style.display='block'" 
            style="position: fixed; bottom: 20px; left: 20px; z-index: 9998; background: #9B5FFF; color: white; border: none; padding: 10px 15px; border-radius: 50%; cursor: pointer; box-shadow: 0 3px 10px rgba(0,0,0,0.2);">
        <i class="fas fa-bolt"></i>
    </button>
    <?php endif; ?>
</body>
</html>
<?php /**PATH C:\Users\A-Tech\Downloads\archiveGzNa7\resources\views/back_layouts/master.blade.php ENDPATH**/ ?>