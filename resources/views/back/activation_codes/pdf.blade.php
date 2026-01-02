<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أكواد التفعيل - {{ site_name() }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', Arial, sans-serif;
            background: #fff;
            direction: rtl;
        }

        /* ============ Print Settings ============ */
        @media print {
            @page {
                @if($size === 'receipt')
                    size: 80mm auto;
                    margin: 5mm;
                @else
                    size: A4;
                    margin: 10mm;
                @endif
            }
            
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-after: always;
            }
        }

        /* ============ Print Controls ============ */
        .print-controls {
            position: fixed;
            top: 20px;
            left: 20px;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .print-controls button {
            background: linear-gradient(135deg, {{ primary_color() }}, {{ secondary_color() }});
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-family: 'Cairo', sans-serif;
            font-weight: 600;
            font-size: 14px;
            margin: 5px;
            transition: all 0.3s ease;
        }

        .print-controls button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(116, 36, 169, 0.3);
        }

        /* ============ A4 Layout (3x5 = 15 codes per page) ============ */
        @if($size === 'a4')
        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 8mm;
        }

        .code-card {
            width: calc(33.333% - 6px);
            display: inline-block;
            vertical-align: top;
            margin: 3px;
            border: 2px dashed {{ primary_color() }};
            border-radius: 6px;
            padding: 5px;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
            position: relative;
            overflow: hidden;
            page-break-inside: avoid;
            height: 220px;
            box-sizing: border-box;
        }

        .code-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(116, 36, 169, 0.02) 0%, transparent 70%);
            pointer-events: none;
        }

        .card-header {
            text-align: center;
            margin-bottom: 4px;
            border-bottom: 1px solid {{ primary_color() }};
            padding-bottom: 4px;
        }

        .logo {
            max-width: 32px;
            height: auto;
            margin-bottom: 2px;
        }

        .platform-name {
            font-size: 9px;
            font-weight: 700;
            color: {{ primary_color() }};
            margin: 1px 0;
            line-height: 1.1;
        }

        .teacher-name {
            font-size: 7px;
            color: #666;
            margin: 1px 0;
            line-height: 1.1;
        }

        .card-body {
            text-align: center;
            padding: 4px 0;
        }

        .month-info {
            background: linear-gradient(135deg, {{ primary_color() }}, {{ secondary_color() }});
            color: white;
            padding: 3px;
            border-radius: 3px;
            margin-bottom: 4px;
            font-size: 7px;
            font-weight: 600;
            line-height: 1.1;
        }

        .code-display {
            background: white;
            border: 1.5px solid {{ primary_color() }};
            padding: 3px;
            border-radius: 4px;
            margin: 4px 0;
        }

        .code-label {
            font-size: 6px;
            color: #666;
            margin-bottom: 1px;
        }

        .code-value {
            font-size: 10px;
            font-weight: 700;
            color: {{ primary_color() }};
            letter-spacing: 0.3px;
            font-family: 'Courier New', monospace;
            line-height: 1.1;
        }

        .qr-code {
            text-align: center;
            margin: 3px 0;
        }

        .qr-code img {
            width: 55px;
            height: 55px;
        }

        .card-footer {
            text-align: center;
            margin-top: 3px;
            padding-top: 3px;
            border-top: 1px dashed #ddd;
            font-size: 5.5px;
            color: #888;
            line-height: 1.1;
        }

        .website-url {
            color: {{ primary_color() }};
            font-weight: 600;
            font-size: 6px;
        }

        .expire-date {
            font-size: 6px;
            color: #e74c3c;
            margin-top: 2px;
            font-weight: 600;
        }

        @else
        /* ============ Receipt Size (80mm) - One code per page ============ */
        .container {
            width: 80mm;
            margin: 0 auto;
        }

        .code-card {
            width: 100%;
            border: 2px dashed {{ primary_color() }};
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 0;
            background: white;
            page-break-after: always;
        }

        .card-header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px solid {{ primary_color() }};
            padding-bottom: 8px;
        }

        .logo {
            max-width: 50px;
            height: auto;
            margin-bottom: 5px;
        }

        .platform-name {
            font-size: 14px;
            font-weight: 700;
            color: {{ primary_color() }};
            margin: 3px 0;
        }

        .teacher-name {
            font-size: 11px;
            color: #666;
            margin: 3px 0;
        }

        .card-body {
            text-align: center;
            padding: 8px 0;
        }

        .month-info {
            background: {{ primary_color() }};
            color: white;
            padding: 8px;
            border-radius: 5px;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 600;
        }

        .code-display {
            background: #f8f9ff;
            border: 2px solid {{ primary_color() }};
            padding: 10px;
            border-radius: 5px;
            margin: 8px 0;
        }

        .code-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 3px;
        }

        .code-value {
            font-size: 16px;
            font-weight: 700;
            color: {{ primary_color() }};
            letter-spacing: 1px;
            font-family: 'Courier New', monospace;
        }

        .qr-code {
            text-align: center;
            margin: 12px 0;
        }

        .qr-code img {
            width: 100px;
            height: 100px;
        }

        .qr-label {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }

        .card-footer {
            text-align: center;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed #ddd;
            font-size: 9px;
            color: #888;
        }

        .website-url {
            color: {{ primary_color() }};
            font-weight: 600;
            font-size: 10px;
        }

        .expire-date {
            font-size: 10px;
            color: #e74c3c;
            margin-top: 8px;
            font-weight: 600;
        }
        @endif
    </style>
</head>
<body>
    <!-- Print Controls -->
    <div class="print-controls no-print">
        <button onclick="window.print()">🖨️ طباعة</button>
        <button onclick="window.close()">❌ إغلاق</button>
        <div style="margin-top: 10px; font-size: 12px; color: #666;">
            عدد الأكواد: <strong>{{ $codes->count() }}</strong>
        </div>
    </div>

    <!-- Codes Container -->
    <div class="container">
        @foreach($codes as $index => $code)
            <div class="code-card">
                <!-- Header -->
                <div class="card-header">
                    @if(file_exists(public_path(logo_path())))
                        <img src="{{ asset(logo_path()) }}" alt="Logo" class="logo">
                    @endif
                    <div class="platform-name">{{ site_name() }}</div>
                    <div class="teacher-name">{{ teacher_full_name() ?: teacher_name() }}</div>
                </div>

                <!-- Body -->
                <div class="card-body">
                    <div class="month-info">
                        📚 {{ $code->month ? $code->month->name : ($code->bundle ? $code->bundle->name : 'غير محدد') }}
                        @if($code->month && $code->month->grade)
                            <br>
                            <span style="font-size: {{ $size === 'a4' ? '9px' : '11px' }};">{{ $code->month->grade }}</span>
                        @endif
                    </div>

                    <div class="code-display">
                        <div class="code-label">كود التفعيل</div>
                        <div class="code-value">{{ $code->code }}</div>
                    </div>

                    @if($code->expires_at)
                        <div class="expire-date">
                            ⏰ صالح حتى: {{ $code->expires_at->format('Y-m-d') }}
                        </div>
                    @endif

                    <!-- QR Code for activation instructions -->
                    <div class="qr-code">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('student.activate.instructions')) }}" alt="QR Code">
                        <div class="qr-label">امسح للتفعيل</div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="card-footer">
                    <div class="website-url">{{ url('/') }}</div>
                    @if(whatsapp_number())
                        <div style="margin-top: 3px;">
                            📱 {{ whatsapp_number() }}
                        </div>
                    @endif
                </div>
            </div>

            @if($size === 'a4' && ($index + 1) % 15 === 0 && !$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    </div>
</body>
</html>
