<!DOCTYPE html>
<html lang="ar" dir="rtl" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'خطأ')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Tajawal -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
            position: relative;
        }
        
        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(155, 95, 255, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(122, 53, 255, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(155, 95, 255, 0.2) 0%, transparent 50%);
            animation: float 20s ease-in-out infinite;
            z-index: 0;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        
        .error-container {
            position: relative;
            z-index: 1;
            text-align: center;
            color: white;
            max-width: 600px;
            width: 100%;
        }
        
        .error-code {
            font-size: 150px;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #ffffff 0%, rgba(255, 255, 255, 0.8) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .error-icon {
            font-size: 80px;
            margin-bottom: 30px;
            opacity: 0.9;
            animation: bounce 2s ease-in-out infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        .error-title {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        .error-message {
            font-size: 18px;
            line-height: 1.8;
            margin-bottom: 40px;
            opacity: 0.95;
            text-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        }
        
        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .error-btn {
            padding: 15px 35px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            cursor: pointer;
        }
        
        .error-btn-primary {
            background: white;
            color: #667eea;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .error-btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            background: #f8f9fa;
        }
        
        .error-btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border-color: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }
        
        .error-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
            border-color: rgba(255, 255, 255, 0.5);
        }
        
        /* Dark Mode */
        [data-theme="dark"] body {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
        }
        
        [data-theme="dark"] body::before {
            background: 
                radial-gradient(circle at 20% 50%, rgba(155, 95, 255, 0.2) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(122, 53, 255, 0.2) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(155, 95, 255, 0.15) 0%, transparent 50%);
        }
        
        [data-theme="dark"] .error-code {
            background: linear-gradient(135deg, #9B5FFF 0%, #7A35FF 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        [data-theme="dark"] .error-btn-primary {
            background: #9B5FFF;
            color: white;
        }
        
        [data-theme="dark"] .error-btn-primary:hover {
            background: #7A35FF;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .error-code {
                font-size: 100px;
            }
            
            .error-icon {
                font-size: 60px;
            }
            
            .error-title {
                font-size: 28px;
            }
            
            .error-message {
                font-size: 16px;
            }
            
            .error-btn {
                padding: 12px 25px;
                font-size: 14px;
            }
        }
    </style>
    
    @yield('css')
</head>
<body>
    <div class="error-container">
        @yield('content')
    </div>
    
    <script>
        // Dark Mode Toggle (if needed)
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
    
    @yield('js')
</body>
</html>




