@extends('front.layouts.app')
@section('title')
{{$pdf->title}} | {{ site_name() }}
@endsection

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
    }

    .pdf-viewer-section {
        padding: 120px 0 40px;
        background: linear-gradient(135deg, {{ hexToRgba(primary_color(), 0.03) }}, {{ hexToRgba(secondary_color(), 0.03) }});
        min-height: calc(100vh - 90px);
    }

    .pdf-viewer-container {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .pdf-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        padding: 25px 30px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .pdf-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        flex: 1;
        min-width: 200px;
    }

    .pdf-header .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
        flex: 1;
    }

    .pdf-header .breadcrumb-item {
        color: rgba(255, 255, 255, 0.8);
    }

    .pdf-header .breadcrumb-item a {
        color: white;
        text-decoration: none;
    }

    .pdf-header .breadcrumb-item.active {
        color: white;
    }

    .pdf-viewer-wrapper {
        position: relative;
        width: 100%;
        height: calc(100vh - 250px);
        min-height: 600px;
        background: #525252;
        overflow: hidden;
    }

    #pdf-canvas-container {
        width: 100%;
        height: 100%;
        overflow: auto;
        background: #525252;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 20px;
        -webkit-overflow-scrolling: touch;
    }

    #pdf-canvas {
        background: white;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        max-width: 100%;
        height: auto;
        display: block;
    }

    .pdf-controls {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.9);
        padding: 12px 20px;
        border-radius: 30px;
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 1000;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    }

    .pdf-controls button {
        background: white;
        border: none;
        color: var(--primary-color);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: all 0.3s ease;
        touch-action: manipulation;
    }

    .pdf-controls button:active {
        transform: scale(0.95);
    }

    .pdf-controls button:hover {
        background: var(--primary-color);
        color: white;
        transform: scale(1.1);
    }

    .pdf-controls button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .pdf-controls .page-info {
        color: white;
        font-weight: 600;
        padding: 0 15px;
        min-width: 100px;
        text-align: center;
        font-size: 14px;
    }

    .pdf-info {
        padding: 25px 30px;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }

    .pdf-info h3 {
        color: var(--primary-color);
        font-size: 1.3rem;
        margin-bottom: 15px;
    }

    .pdf-info p {
        color: #6c757d;
        margin: 0;
        line-height: 1.8;
    }

    .btn-back {
        background: white;
        color: var(--primary-color);
        padding: 10px 25px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 2px solid var(--primary-color);
    }

    .btn-back:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(116, 36, 169, 0.3);
    }

    .loading-state {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: white;
        z-index: 5;
    }

    .loading-state i {
        font-size: 50px;
        margin-bottom: 20px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .error-state {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: white;
        z-index: 5;
    }

    .error-state i {
        font-size: 50px;
        margin-bottom: 20px;
        color: #ff6b6b;
    }

    /* Google Drive iframe styling */
    .pdf-viewer-wrapper iframe {
        width: 100%;
        height: 100%;
        border: none;
    }

    @media (max-width: 768px) {
        .pdf-viewer-section {
            padding: 100px 0 20px;
        }

        .pdf-viewer-container {
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .pdf-header {
            flex-direction: column;
            align-items: flex-start;
            padding: 20px;
            gap: 15px;
        }

        .pdf-header h1 {
            font-size: 1.1rem;
            line-height: 1.4;
        }

        .pdf-header .breadcrumb {
            font-size: 0.85rem;
        }

        .pdf-viewer-wrapper {
            height: calc(100vh - 200px);
            min-height: 400px;
            max-height: 70vh;
        }

        #pdf-canvas-container {
            padding: 10px;
        }

        #pdf-canvas {
            width: 100% !important;
            max-width: 100% !important;
            height: auto !important;
        }

        .pdf-controls {
            position: fixed;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            padding: 10px 15px;
            gap: 8px;
            border-radius: 25px;
            width: auto;
            max-width: 95%;
        }

        .pdf-controls button {
            width: 38px;
            height: 38px;
            font-size: 16px;
            min-width: 38px;
        }

        .pdf-controls .page-info {
            font-size: 13px;
            min-width: 70px;
            padding: 0 8px;
        }

        .pdf-info {
            padding: 20px;
        }

        .pdf-info h3 {
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .pdf-info p {
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .btn-back {
            padding: 8px 20px;
            font-size: 0.9rem;
        }

        .loading-state,
        .error-state {
            padding: 20px;
        }

        .loading-state i,
        .error-state i {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .loading-state p,
        .error-state p {
            font-size: 14px;
        }
    }

    @media (max-width: 480px) {
        .pdf-viewer-section {
            padding: 90px 0 15px;
        }

        .pdf-header {
            padding: 15px;
        }

        .pdf-header h1 {
            font-size: 1rem;
        }

        .pdf-header .breadcrumb {
            font-size: 0.75rem;
        }

        .pdf-viewer-wrapper {
            height: calc(100vh - 180px);
            min-height: 350px;
        }

        #pdf-canvas-container {
            padding: 5px;
        }

        .pdf-controls {
            bottom: 10px;
            padding: 8px 12px;
            gap: 6px;
            border-radius: 20px;
        }

        .pdf-controls button {
            width: 35px;
            height: 35px;
            font-size: 14px;
            min-width: 35px;
        }

        .pdf-controls .page-info {
            font-size: 12px;
            min-width: 60px;
            padding: 0 6px;
        }

        .pdf-info {
            padding: 15px;
        }

        .pdf-info h3 {
            font-size: 1rem;
        }

        .pdf-info p {
            font-size: 0.85rem;
        }
    }

    /* تحسينات للشاشات الصغيرة جداً */
    @media (max-width: 360px) {
        .pdf-controls {
            flex-wrap: wrap;
            justify-content: center;
            width: 90%;
        }

        .pdf-controls .page-info {
            width: 100%;
            margin-top: 5px;
            order: 3;
        }
    }
</style>

<section class="pdf-viewer-section">
    <div class="container">
        <div class="pdf-viewer-container">
            <div class="pdf-header">
                <h1><i class="fas fa-file-pdf me-2"></i>{{$pdf->title}}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="{{route('courses.index')}}">الكورسات</a></li>
                        <li class="breadcrumb-item"><a href="{{route('pdfs', ['month_id' => $pdf->month_id])}}">المذكرات</a></li>
                        <li class="breadcrumb-item active">عرض المذكرة</li>
                    </ol>
                </nav>
            </div>

            <div class="pdf-viewer-wrapper">
                <div class="loading-state" id="loadingState">
                    <i class="fas fa-spinner"></i>
                    <p>جاري تحميل المذكرة...</p>
                </div>

                <div class="error-state" id="errorState" style="display: none;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>حدث خطأ في تحميل المذكرة</p>
                    <p style="font-size: 14px; margin-top: 10px;">يرجى المحاولة مرة أخرى</p>
                </div>

                @if(isset($isGoogleDrive) && $isGoogleDrive)
                    {{-- Google Drive - عرض داخل iframe بدون أي علامات --}}
                    <iframe 
                        id="googleDriveViewer" 
                        src="{{$pdfUrl}}" 
                        style="width: 100%; height: 100%; min-height: 600px; border: none; display: none;"
                        onload="document.getElementById('loadingState').style.display='none'; document.getElementById('googleDriveViewer').style.display='block';"
                        onerror="showError()"
                        allowfullscreen>
                    </iframe>
                @else
                    {{-- ملف محلي أو خارجي - استخدام PDF.js --}}
                    <div id="pdf-canvas-container" style="display: none;">
                        <canvas id="pdf-canvas"></canvas>
                    </div>
                    <div class="pdf-controls" id="pdfControls" style="display: none;">
                        <button id="prevPage" onclick="changePage(-1)">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <span class="page-info">
                            <span id="pageNum">1</span> / <span id="pageCount">1</span>
                        </span>
                        <button id="nextPage" onclick="changePage(1)">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button onclick="zoomOut()" title="تصغير">
                            <i class="fas fa-search-minus"></i>
                        </button>
                        <button onclick="zoomIn()" title="تكبير">
                            <i class="fas fa-search-plus"></i>
                        </button>
                    </div>
                @endif
            </div>

            @if($pdf->description)
            <div class="pdf-info">
                <h3><i class="fas fa-info-circle me-2"></i>عن المذكرة</h3>
                <p>{{$pdf->description}}</p>
            </div>
            @endif
        </div>

        <div class="text-center">
            <a href="{{route('pdfs', ['month_id' => $pdf->month_id])}}" class="btn-back">
                <i class="fas fa-arrow-right"></i>
                العودة إلى المذكرات
            </a>
        </div>
    </div>
</section>

@if(!isset($isGoogleDrive) || !$isGoogleDrive)
<!-- PDF.js from CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    // تعيين worker path
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    let pdfDoc = null;
    let pageNum = 1;
    let pageRendering = false;
    let pageNumPending = null;
    let scale = 1.5;
    const canvas = document.getElementById('pdf-canvas');
    const ctx = canvas.getContext('2d');

    // تحميل PDF
    const pdfUrl = @json($pdfUrl);
    
    // تحديد scale أولي حسب حجم الشاشة
    if (window.innerWidth <= 480) {
        scale = 1.0;
    } else if (window.innerWidth <= 768) {
        scale = 1.2;
    } else {
        scale = 1.5;
    }
    
    pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
        pdfDoc = pdf;
        document.getElementById('pageCount').textContent = pdf.numPages;
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('pdf-canvas-container').style.display = 'flex';
        document.getElementById('pdfControls').style.display = 'flex';
        renderPage(pageNum);
    }).catch(function(error) {
        console.error('Error loading PDF:', error);
        showError();
    });

    // عرض صفحة
    function renderPage(num) {
        pageRendering = true;
        pdfDoc.getPage(num).then(function(page) {
            const viewport = page.getViewport({scale: scale});
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            const renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };
            const renderTask = page.render(renderContext);

            renderTask.promise.then(function() {
                pageRendering = false;
                if (pageNumPending !== null) {
                    renderPage(pageNumPending);
                    pageNumPending = null;
                }
            });
        });

        document.getElementById('pageNum').textContent = num;
        updateButtons();
    }

    // تغيير الصفحة
    function changePage(delta) {
        if (pageRendering) {
            pageNumPending = pageNum + delta;
        } else {
            pageNum += delta;
            if (pageNum < 1) {
                pageNum = 1;
            } else if (pageNum > pdfDoc.numPages) {
                pageNum = pdfDoc.numPages;
            }
            renderPage(pageNum);
        }
    }

    // تحديث أزرار التنقل
    function updateButtons() {
        document.getElementById('prevPage').disabled = (pageNum <= 1);
        document.getElementById('nextPage').disabled = (pageNum >= pdfDoc.numPages);
    }

    // تكبير
    function zoomIn() {
        if (!pdfDoc) return;
        scale += 0.25;
        if (scale > 3) scale = 3; // حد أقصى للتكبير
        renderPage(pageNum);
    }

    // تصغير
    function zoomOut() {
        if (!pdfDoc) return;
        if (scale > 0.5) {
            scale -= 0.25;
            renderPage(pageNum);
        }
    }

    // عرض خطأ
    function showError() {
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('errorState').style.display = 'block';
        if (document.getElementById('pdf-canvas-container')) {
            document.getElementById('pdf-canvas-container').style.display = 'none';
        }
        if (document.getElementById('pdfControls')) {
            document.getElementById('pdfControls').style.display = 'none';
        }
    }

    // منع التحميل المباشر
    document.addEventListener('contextmenu', function(e) {
        if (e.target.closest('.pdf-viewer-wrapper')) {
            e.preventDefault();
        }
    });

    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S')) {
            e.preventDefault();
        }
    });

    // تحسينات للموبايل - منع zoom مزدوج
    let lastTouchEnd = 0;
    document.addEventListener('touchend', function(event) {
        const now = Date.now();
        if (now - lastTouchEnd <= 300) {
            event.preventDefault();
        }
        lastTouchEnd = now;
    }, false);

    // تحسين scroll على الموبايل
    const canvasContainer = document.getElementById('pdf-canvas-container');
    if (canvasContainer) {
        canvasContainer.style.webkitOverflowScrolling = 'touch';
    }

    // تحسين حجم canvas على الموبايل
    function adjustCanvasForMobile() {
        if (window.innerWidth <= 768 && canvas) {
            const containerWidth = canvasContainer ? canvasContainer.offsetWidth : window.innerWidth - 20;
            if (pdfDoc && pageNum) {
                pdfDoc.getPage(pageNum).then(function(page) {
                    const viewport = page.getViewport({scale: 1});
                    const scale = Math.min(containerWidth / viewport.width, 2);
                    if (scale !== window.currentScale) {
                        window.currentScale = scale;
                        renderPage(pageNum);
                    }
                });
            }
        }
    }

    window.addEventListener('resize', function() {
        if (pdfDoc && pageNum) {
            adjustCanvasForMobile();
        }
    });

    // تعديل scale الأولي على الموبايل
    if (window.innerWidth <= 768) {
        scale = 1.2;
    }
</script>
@else
<script>
    function showError() {
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('errorState').style.display = 'block';
        if (document.getElementById('googleDriveViewer')) {
            document.getElementById('googleDriveViewer').style.display = 'none';
        }
    }
</script>
@endif

@endsection
