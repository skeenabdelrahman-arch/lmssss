<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css?family=Cairo:400,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        body { direction: rtl; font-family: 'Cairo', sans-serif; margin:0; padding:0; background:#f4f6f9;}
        
        /* Sidebar */
        .side-menu-fixed {
            width: 220px; position: fixed; right:0; top:0; height:100%; background:#2c3e50; padding-top:60px; overflow-y:auto;
            box-shadow:-2px 0 5px rgba(0,0,0,0.1);
        }
        .side-menu-fixed ul { list-style:none; padding:0; margin:0; }
        .side-menu-fixed ul li a {
            display:flex; align-items:center; padding:12px 20px; color:#ecf0f1; text-decoration:none; font-size:16px;
            border-radius:5px; margin:5px 10px; transition: all 0.3s;
        }
        .side-menu-fixed ul li a i { margin-left:10px; font-size:18px; }
        .side-menu-fixed ul li a.active,
        .side-menu-fixed ul li a:hover { background:#1abc9c; color:#fff; transform:translateX(-5px); box-shadow:0 4px 10px rgba(0,0,0,0.2); }

        /* Content Wrapper */
        .content-wrapper { margin-right:220px; padding:20px; min-height:100vh; }

        /* Header */
        .main-header { position:fixed; top:0; right:0; left:0; height:60px; background:#1abc9c; display:flex; align-items:center; justify-content:space-between; padding:0 20px; color:#fff; z-index:1000; box-shadow:0 2px 5px rgba(0,0,0,0.1);}
        .main-header .brand-logo img { height:40px; }
        .main-header .user-avatar img { width:40px; height:40px; border-radius:50%; }

        /* Footer */
        footer { background:#fff; padding:10px 20px; text-align:center; box-shadow:0 -1px 5px rgba(0,0,0,0.1); }

        /* Responsive */
        @media (max-width:768px) {
            .side-menu-fixed { width:60px; padding-top:60px; }
            .side-menu-fixed ul li a { justify-content:center; font-size:0; padding:12px 0; }
            .side-menu-fixed ul li a i { margin:0; font-size:20px; }
            .content-wrapper { margin-right:60px; }
        }
    </style>

    @yield('css')
</head>
<body>
    <!-- Sidebar -->
    <div class="side-menu-fixed">
        <ul class="nav side-menu" id="sidebarnav">
            <li><a href="{{ route('home') }}" class="active"><i class="fas fa-home"></i> الرئيسية</a></li>
            <li><a href="{{ route('students.male') }}"><i class="fas fa-male"></i> طلاب ذكور</a></li>
            <li><a href="{{ route('students.female') }}"><i class="fas fa-female"></i> طلاب إناث</a></li>
            <li><a href="#"><i class="fas fa-user-plus"></i> إضافة طالب جديد</a></li>
            <li><a href="{{ route('lecture.index') }}"><i class="fas fa-chalkboard-teacher"></i> المحاضرات</a></li>
            <li><a href="{{ route('exam_name.index') }}"><i class="fas fa-book-open"></i> الامتحانات</a></li>
            <li><a href="{{ route('pdf.index') }}"><i class="fas fa-file-pdf"></i> الملفات</a></li>
            <li><a href="{{ route('student_subscription.index') }}"><i class="fas fa-user-graduate"></i> الاشتراكات</a></li>
        </ul>
    </div>

    <!-- Header -->
    <header class="main-header">
        <div class="brand-logo">
            <a href="{{ route('home') }}"><img src="{{ asset('front/assets/images/لوجو.png') }}" alt="Logo"></a>
        </div>
        <div class="user-avatar">
            <img src="{{ asset('front/assets/images/لوجو.png') }}" alt="User">
        </div>
    </header>

    <!-- Main Content -->
    <div class="content-wrapper">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer>
        &copy; مستر سامح {{ date('Y') }} جميع الحقوق محفوظة
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('js')
</body>
</html>
