@extends('front.layouts.app')
@section('title')
المذكرات | {{ site_name() }}
@endsection

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
        --primary-light: #b05ee7;
    }

    .pdfs-section {
        padding: 120px 0 80px;
        background: linear-gradient(135deg, rgba(116, 36, 169, 0.03), rgba(250, 137, 107, 0.03));
        min-height: calc(100vh - 90px);
    }

    .page-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 15px;
    }

    .page-header .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
        justify-content: center;
    }

    .page-header .breadcrumb-item a {
        color: var(--secondary-color);
        text-decoration: none;
    }

    .pdfs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
    }

    .pdf-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        text-decoration: none;
        display: block;
        border: 2px solid transparent;
    }

    .pdf-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 50px rgba(116, 36, 169, 0.2);
        border-color: var(--primary-color);
        text-decoration: none;
    }

    .pdf-icon-wrapper {
        height: 200px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .pdf-icon-wrapper::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.2), transparent);
        transition: transform 0.5s ease;
    }

    .pdf-card:hover .pdf-icon-wrapper::before {
        transform: rotate(180deg);
    }

    .pdf-icon {
        font-size: 80px;
        color: white;
        position: relative;
        z-index: 1;
        transition: transform 0.3s ease;
    }

    .pdf-card:hover .pdf-icon {
        transform: scale(1.1) rotate(-5deg);
    }

    .pdf-content {
        padding: 25px;
    }

    .pdf-content h5 {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 10px;
        transition: color 0.3s ease;
    }

    .pdf-card:hover .pdf-content h5 {
        color: var(--secondary-color);
    }

    .pdf-content p {
        color: #6c757d;
        font-size: 0.95rem;
        margin: 0;
    }

    .download-badge {
        display: inline-block;
        padding: 5px 15px;
        background: rgba(116, 36, 169, 0.1);
        color: var(--primary-color);
        border-radius: 20px;
        font-size: 0.85rem;
        margin-top: 10px;
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    }

    .empty-state i {
        font-size: 100px;
        color: var(--primary-light);
        margin-bottom: 30px;
    }

    .empty-state h3 {
        color: var(--primary-color);
        margin-bottom: 15px;
        font-size: 1.8rem;
    }

    .empty-state p {
        color: #6c757d;
        font-size: 1.1rem;
        margin-bottom: 30px;
    }

    .btn-home {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 12px 30px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .btn-home:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(116, 36, 169, 0.3);
        color: white;
    }

    @media (max-width: 768px) {
        .pdfs-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .page-header h1 {
            font-size: 2rem;
        }
    }
</style>

<section class="pdfs-section">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-file-pdf me-2"></i>المذكرات</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="{{route('courses.index')}}">الكورسات</a></li>
                    <li class="breadcrumb-item active">المذكرات</li>
                </ol>
            </nav>
        </div>

        @if($pdfs->isEmpty())
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3>لا توجد مذكرات حالياً</h3>
                <p>نعمل دائماً على تحديث المحتوى الخاص بنا، يرجى التحقق لاحقاً.</p>
                <a href="{{url('/')}}" class="btn-home">
                    <i class="fas fa-home me-2"></i>العودة إلى الرئيسية
                </a>
            </div>
        @else
            <div class="pdfs-grid">
                @foreach($pdfs as $pdf)
                    <a href="{{route('pdf.view', $pdf->id)}}" class="pdf-card">
                        <div class="pdf-icon-wrapper">
                            <i class="fas fa-file-pdf pdf-icon"></i>
                        </div>
                        <div class="pdf-content">
                            <h5>{{$pdf->title}}</h5>
                            @if($pdf->description)
                                <p>{{ Str::limit($pdf->description, 80) }}</p>
                            @endif
                            <span class="download-badge">
                                <i class="fas fa-eye me-1"></i>عرض
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

@endsection
