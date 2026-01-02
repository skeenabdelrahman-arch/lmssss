<!-- Title -->
<title>@yield("title")</title>

<!-- Favicon -->
<link rel="shortcut icon" href="{{ URL::asset('back/assets/images/favicon.ico') }}" type="image/x-icon" />

<!-- Font -->
<link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Poppins:200,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900">
@yield('css')
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        direction: rtl;
        text-align: right;
        background: #f5f7fa;
    }

    .wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .content-wrapper {
        flex: 1;
        padding: 20px;
        background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
        margin-right: 260px;
        margin-top: 65px;
        transition: all 0.3s ease;
    }

    /* Modern Cards */
    .modern-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        padding: 25px;
        margin-bottom: 25px;
        transition: all 0.3s ease;
        border: none;
    }

    .modern-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    }

    /* Dashboard Stats Cards */
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border-right: 4px solid;
        position: relative;
        overflow: hidden;
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

    /* Modern Buttons */
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

    .btn-modern a {
        color: inherit;
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
        background: linear-gradient(135deg, #6c757d 0%, #868e96 100%);
        color: white;
    }

    /* Modern Tables */
    .modern-table {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .modern-table table {
        margin: 0;
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
        transform: scale(1.01);
    }

    .modern-table tbody td {
        padding: 15px;
        text-align: center;
        vertical-align: middle;
    }

    /* Badge Modern */
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

    /* Alerts Modern */
    .alert-modern {
        border-radius: 12px;
        border: none;
        padding: 15px 20px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    /* Hide Old Sidebar */
    .side-menu-bg,
    .scrollbar {
        display: none !important;
    }

    /* Modern Sidebar */
    .side-menu-fixed {
        position: fixed;
        right: 0;
        top: 65px;
        width: 260px;
        height: calc(100vh - 65px);
        background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
        z-index: 1000;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        overflow-y: auto;
        transition: transform 0.3s ease;
    }
    
    /* Mobile Sidebar Overlay */
    .side-menu-fixed::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.5);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: -1;
    }
    
    @media (max-width: 992px) {
        .side-menu-fixed::before {
            z-index: 999;
        }
        
        .side-menu-fixed.show::before {
            opacity: 1;
            visibility: visible;
        }
    }

    .side-menu {
        list-style: none;
        padding: 20px 0;
        margin: 0;
    }

    .side-menu li {
        margin: 5px 0;
    }

    .side-menu li a {
        display: flex;
        align-items: center;
        padding: 10px 18px;
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        transition: all 0.3s ease;
        border-right: 3px solid transparent;
        font-size: 15px;
    }

    .side-menu li a:hover,
    .side-menu li a.active {
        background: rgba(255,255,255,0.1);
        color: white;
        border-right-color: #7424a9;
        transform: translateX(-5px);
    }

    .side-menu li a i {
        width: 22px;
        margin-left: 10px;
        font-size: 16px;
    }

    .side-menu .menu-title {
        color: rgba(255,255,255,0.5);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 20px 20px 10px;
        margin: 0;
    }

    /* Modern Header */
    .admin-header {
        background: linear-gradient(135deg, #7424a9 0%, #9d4edd 100%);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        height: 65px;
        padding: 0 20px;
        z-index: 1050;
        display: flex;
        align-items: center;
    }
    
    .admin-header .container-fluid {
        display: flex;
        align-items: center;
        width: 100%;
        max-width: 100%;
        padding: 0 15px;
    }

    .admin-header .navbar-brand {
        display: flex;
        align-items: center;
        font-size: 18px;
        font-weight: bold;
        margin: 0;
    }

    .admin-header .navbar-brand img {
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        width: 45px;
        height: 45px;
    }

    .admin-header .nav-link {
        transition: all 0.3s ease;
    }

    .admin-header .nav-link:hover {
        opacity: 0.8;
        transform: translateY(-2px);
    }

    .admin-header .dropdown-menu {
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        border: none;
        margin-top: 10px;
    }

    .admin-header .dropdown-item {
        padding: 10px 20px;
        transition: all 0.3s ease;
    }

    .admin-header .dropdown-item:hover {
        background: #f8f9fa;
        transform: translateX(-5px);
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
    
    /* Auto wrap tables in responsive div */
    .modern-card table:not(.no-responsive),
    .content-wrapper table:not(.no-responsive) {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    /* DataTables Responsive */
    .dataTables_wrapper {
        overflow-x: auto;
    }
    
    /* Action buttons in tables */
    .table .btn-group {
        display: flex;
        gap: 5px;
        flex-wrap: nowrap;
    }
    
    /* Fix long text in tables */
    .table td {
        word-wrap: break-word;
        max-width: 300px;
    }
    
    /* Notification badge */
    .badge.bg-danger {
        font-size: 10px;
        padding: 3px 6px;
    }
    
    @media (max-width: 768px) {
        .modern-card table:not(.no-responsive),
        .content-wrapper table:not(.no-responsive) {
            font-size: 12px;
        }
        
        .modern-card table:not(.no-responsive) th,
        .modern-card table:not(.no-responsive) td,
        .content-wrapper table:not(.no-responsive) th,
        .content-wrapper table:not(.no-responsive) td {
            padding: 8px 5px;
            white-space: nowrap;
        }
        
        /* Smaller action buttons */
        .table .btn {
            padding: 4px 8px;
            font-size: 11px;
        }
        
        .table .btn i {
            font-size: 12px;
        }
        
        /* Hide button text, keep icons */
        .table .btn .d-none.d-md-inline,
        .table .btn span:not(.badge) {
            display: none !important;
        }
        
        /* Search boxes */
        .search-box-wrapper {
            min-width: 200px;
            max-width: 100%;
        }
        
        .table-header-actions {
            flex-direction: column;
            align-items: stretch;
        }
        
        .table-header-actions > * {
            width: 100%;
        }
        
        /* Fix table max width */
        .table td {
            max-width: 150px;
        }
    }
    
    /* Auto wrap tables in responsive div */
    .modern-card table:not(.no-responsive),
    .content-wrapper table:not(.no-responsive) {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    /* DataTables Responsive */
    .dataTables_wrapper {
        overflow-x: auto;
    }
    
    /* Action buttons in tables */
    .table .btn-group {
        display: flex;
        gap: 5px;
        flex-wrap: nowrap;
    }
    
    @media (max-width: 768px) {
        .modern-card table:not(.no-responsive),
        .content-wrapper table:not(.no-responsive) {
            font-size: 12px;
        }
        
        .modern-card table:not(.no-responsive) th,
        .modern-card table:not(.no-responsive) td,
        .content-wrapper table:not(.no-responsive) th,
        .content-wrapper table:not(.no-responsive) td {
            padding: 8px 5px;
            white-space: nowrap;
        }
        
        /* Smaller action buttons */
        .table .btn {
            padding: 4px 8px;
            font-size: 11px;
        }
        
        .table .btn i {
            font-size: 12px;
        }
        
        /* Hide button text, keep icons */
        .table .btn .d-none.d-md-inline,
        .table .btn span:not(.badge) {
            display: none !important;
        }
        
        /* Search boxes */
        .search-box-wrapper {
            min-width: 200px;
            max-width: 100%;
        }
        
        .table-header-actions {
            flex-direction: column;
            align-items: stretch;
        }
        
        .table-header-actions > * {
            width: 100%;
        }
    }
    /* Responsive */
    @media (max-width: 768px) {
        .content-wrapper {
            margin-right: 0;
            margin-top: 60px;
            padding: 10px;
        }
        
        .side-menu-fixed {
            transform: translateX(100%);
            transition: transform 0.3s ease;
            z-index: 1040;
            top: 60px;
            height: calc(100vh - 60px);
        }   height: calc(100vh - 70px);
        }
        
        .side-menu-fixed.show {
            transform: translateX(0);
        }
        
        /* Tables Responsive */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table {
            font-size: 12px;
            min-width: 600px;
        }
        
        .table th,
        .table td {
            padding: 8px 5px;
            white-space: nowrap;
        }
        
        /* Cards */
        .modern-card {
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
        }
        
        .stat-card {
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }
        
        .stat-value {
            font-size: 24px;
        }
        
        /* Forms */
        .form-control,
        .form-select,
        textarea.form-control {
            font-size: 14px;
            padding: 8px 12px;
        }
        
        .btn {
            padding: 8px 15px;
            font-size: 14px;
        }
        
        /* Grid Adjustments */
        .row.g-3 {
            --bs-gutter-x: 0.5rem;
            --bs-gutter-y: 0.5rem;
        }
        /* Header */
        .admin-header {
            padding: 8px 12px;
            height: 60px;
            min-height: 60px;
        }
        
        .admin-header .navbar-brand {
            font-size: 15px;
        }
        
        .admin-header .navbar-brand img {
            width: 38px !important;
            height: 38px !important;
        }   height: 40px !important;
        }
        
        .admin-header .nav-link {
            padding: 5px 10px;
            font-size: 14px;
        }
        
        /* Modals */
        .modal-dialog {
            margin: 10px;
            max-width: calc(100% - 20px);
        }
        
        .modal-body {
            padding: 15px;
        }
        
        .modal-title {
            font-size: 18px;
        }
        
        /* Page Headers */
        .page-header-modern {
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        
        .page-header-modern h4 {
            font-size: 18px;
        }
        
        /* Hide text on small buttons */
        .btn-sm .d-none.d-md-inline {
            display: none !important;
        }
        
        /* Alerts */
        .alert {
            font-size: 13px;
            padding: 10px;
        }
        
        /* Images */
        img {
            max-width: 100%;
            height: auto;
        }
        
        /* Overflow prevention */
        body {
            overflow-x: hidden;
        }
        
        .container-fluid {
            padding-left: 10px;
            padding-right: 10px;
        }
        
        /* Dropdown menus */
        .dropdown-menu {
            max-width: calc(100vw - 20px);
            font-size: 14px;
        }
        
        /* Notification dropdown */
        .notification-dropdown {
    /* Extra Small Devices (Portrait Phones) */
    @media (max-width: 576px) {
        .content-wrapper {
            padding: 8px;
            margin-top: 55px;
        }
        
        .side-menu-fixed {
            top: 55px;
            height: calc(100vh - 55px);
        }tra Small Devices (Portrait Phones) */
    @media (max-width: 576px) {
        .content-wrapper {
            padding: 8px;
            margin-top: 65px;
        }
        
        .modern-card {
            padding: 12px;
        }
        
        .table {
            font-size: 11px;
            min-width: 500px;
        }
        
        .btn {
            padding: 6px 12px;
            font-size: 13px;
        }
        
        .form-control,
        .form-select {
            font-size: 13px;
            padding: 6px 10px;
        }
        
        h1 { font-size: 1.5rem; }
        h2 { font-size: 1.3rem; }
        h3 { font-size: 1.2rem; }
        h4 { font-size: 1.1rem; }
        h5 { font-size: 1rem; }
        h6 { font-size: 0.9rem; }
        
        /* Stack buttons vertically on very small screens */
        .d-flex.gap-2,
        .d-flex.gap-3 {
            flex-direction: column;
            gap: 10px !important;
        }
        
        .d-flex.gap-2 .btn,
        /* Adjust admin header for very small screens */
        .admin-header {
            height: 55px;
            min-height: 55px;
            padding: 6px 10px;
        }
        
        .admin-header .navbar-brand {
            font-size: 13px;
        }
        
        .admin-header .navbar-brand span {
            font-size: 13px;
        }
        
        .admin-header .navbar-brand img {
            width: 35px !important;
            height: 35px !important;
        }
        
        /* Adjust admin header for very small screens */
        .admin-header {
            min-height: 60px;
            padding: 8px 10px;
        }
        
        .admin-header .navbar-brand span {
            font-size: 14px;
        }
    }
    
    /* Large Tablets and Below */
    @media (max-width: 992px) {
        .content-wrapper {
            margin-right: 0;
        }
        
        .side-menu-fixed {
            transform: translateX(100%);
        }
        
        /* Make columns full-width on tablets */
        .row .col-md-6,
        .row .col-lg-4,
        .row .col-lg-3,
        .row .col-md-4,
        .row .col-md-3,
        .row .col-lg-6 {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 15px;
        }
    }
    
    /* Medium tablets */
    @media (min-width: 768px) and (max-width: 991px) {
        .row .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
    
    /* Additional responsive utilities */
    @media (max-width: 992px) {
        /* Navbar items spacing */
        .navbar-nav .nav-item {
            margin: 0 5px;
        }
        
        .navbar-nav .nav-link {
            padding: 8px 10px !important;
        }
    }
    
    @media (max-width: 768px) {
        /* Compact stat cards */
        .stat-card .stat-label {
            font-size: 12px;
        }
        
        /* Smaller form labels */
        .form-label {
            font-size: 13px;
            margin-bottom: 5px;
        }
        
        /* Compact pagination */
        .pagination {
            font-size: 12px;
        }
        
        .pagination .page-link {
            padding: 5px 10px;
        }
        
        /* Alert adjustments */
        .alert-modern {
            padding: 12px 15px;
            font-size: 13px;
        }
        
        /* Card headers */
        .card-header {
            padding: 10px 15px;
            font-size: 14px;
        }
        
        /* Better button groups */
        .btn-group-sm > .btn,
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
    }
    
    @media (max-width: 576px) {
        /* Very compact elements */
        .page-header-modern h4 {
            font-size: 16px;
        }
        
        .stat-card .stat-value {
            font-size: 20px;
        }
        
        .stat-card .stat-label {
            font-size: 11px;
        }
        
        /* Minimal padding for cards */
        .card-body {
            padding: 10px;
        }
        
        /* Smaller select dropdowns */
        .form-select {
            font-size: 12px;
            padding: 6px 10px;
        }
        
        /* Compact list groups */
        .list-group-item {
            padding: 8px 12px;
            font-size: 13px;
        }
    }
</style>
<!--- Style css -->
<link href="{{ URL::asset('back/assets/css/style.css') }}" rel="stylesheet">

<!--- Style css -->
@if (App::getLocale() == 'ar')
    <link href="{{ URL::asset('back/assets/css/ltr.css') }}" rel="stylesheet">
@else
    <link href="{{ URL::asset('back/assets/css/rtl.css') }}" rel="stylesheet">
@endif
