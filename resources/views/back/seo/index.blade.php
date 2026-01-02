@extends('back_layouts.master')

@section('title', 'تحسين محركات البحث - SEO')

@section('css')
<style>
    /* محاكي معاينة جوجل */
    .google-preview {
        background: white;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #dfe1e5;
        max-width: 600px;
        font-family: arial, sans-serif;
        margin-bottom: 25px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .google-preview .url {
        color: #202124;
        font-size: 14px;
        margin-bottom: 4px;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .google-preview .title {
        color: #1a0dab;
        font-size: 20px;
        line-height: 1.3;
        margin-bottom: 3px;
        display: block;
        cursor: pointer;
    }
    .google-preview .title:hover { text-decoration: underline; }
    .google-preview .desc {
        color: #4d5156;
        font-size: 14px;
        line-height: 1.58;
        word-wrap: break-word;
    }

    /* عداد الحروف */
    .char-counter {
        font-size: 11px;
        float: right;
        font-weight: bold;
    }
    .text-success-count { color: #28a745; }
    .text-danger-count { color: #dc3545; }

    .nav-tabs-modern .nav-link {
        border: none;
        color: #64748b;
        font-weight: 600;
        padding: 12px 20px;
        border-bottom: 3px solid transparent;
    }
    .nav-tabs-modern .nav-link.active {
        color: #9B5FFF;
        background: none;
        border-bottom-color: #9B5FFF;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="page-header-modern mb-4">
        <h4 class="fw-black"><i class="fas fa-search text-primary me-2"></i> تحسين محركات البحث (SEO)</h4>
        <p class="text-muted small">إدارة ظهور منصتك في محركات البحث ومنصات التواصل الاجتماعي</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="modern-card shadow-sm mb-4">
                <ul class="nav nav-tabs nav-tabs-modern mb-4" id="seoTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="general-tab" data-bs-toggle="tab" href="#general" role="tab">الإعدادات العامة</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="analytics-tab" data-bs-toggle="tab" href="#analytics" role="tab">التحليلات والبكسل</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="sitemap-tab" data-bs-toggle="tab" href="#sitemap" role="tab">خريطة الموقع</a>
                    </li>
                </ul>

                <form method="POST" action="{{ route('admin.seo.update') }}">
                    @csrf
                    <div class="tab-content" id="seoTabsContent">
                        
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between">
                                    <span>عنوان الموقع (Meta Title)</span>
                                    <span id="title-count" class="char-counter">0 / 60</span>
                                </label>
                                <input type="text" class="form-control form-control-lg" name="site_title" id="site_title" 
                                       value="{{ config('seo.site_title') }}" placeholder="أدخل عنواناً جذاباً..." required>
                                <small class="text-muted">العنصر الأكثر أهمية لمحركات البحث.</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label d-flex justify-content-between">
                                    <span>وصف الموقع (Meta Description)</span>
                                    <span id="desc-count" class="char-counter">0 / 160</span>
                                </label>
                                <textarea class="form-control" name="site_description" id="site_description" rows="4" 
                                          placeholder="اكتب وصفاً مختصراً يشجع على النقر..." required>{{ config('seo.site_description') }}</textarea>
                                <small class="text-muted">ملخص لمحتوى الموقع يظهر في نتائج البحث.</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">الكلمات المفتاحية (Keywords)</label>
                                <input type="text" class="form-control" name="site_keywords" value="{{ config('seo.site_keywords') }}" 
                                       placeholder="أحياء، ثانوية عامة، مستر فلان...">
                            </div>

                            <div class="mb-4">
                                <label class="form-label">رابط صورة المشاركة (OG Image)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-image"></i></span>
                                    <input type="text" class="form-control" name="og_image" value="{{ config('seo.og_image') }}" placeholder="https://example.com/og.jpg">
                                </div>
                                <small class="text-muted">الصورة التي تظهر عند مشاركة الرابط على فيسبوك وواتساب.</small>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="analytics" role="tabpanel">
                            <div class="alert alert-soft-primary mb-4">
                                <i class="fas fa-info-circle me-2"></i> أدوات التتبع تساعدك على فهم سلوك طلابك ونجاح حملاتك الإعلانية.
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Google Analytics ID (GA4)</label>
                                <input type="text" class="form-control" name="google_analytics" value="{{ config('seo.google_analytics') }}" placeholder="G-XXXXXXXXXX">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Facebook Pixel ID</label>
                                <input type="text" class="form-control" name="facebook_pixel" value="{{ config('seo.facebook_pixel') }}" placeholder="1234567890">
                            </div>
                        </div>

                        <div class="tab-pane fade" id="sitemap" role="tabpanel">
                            <div class="p-4 border rounded-4 bg-light text-center">
                                <i class="fas fa-sitemap fa-3x text-muted mb-3"></i>
                                <h5>إدارة Sitemap.xml</h5>
                                <p class="text-muted small mb-4">تحديث خريطة الموقع يخبر جوجل بالصفحات الجديدة والمعدلة فوراً.</p>
                                
                                <button type="button" onclick="document.getElementById('sitemapForm').submit()" class="btn btn-success px-4 rounded-pill">
                                    <i class="fas fa-sync-alt me-2"></i> تحديث خريطة الموقع الآن
                                </button>

                                @if(file_exists(public_path('sitemap.xml')))
                                <div class="mt-3">
                                    <a href="{{ url('sitemap.xml') }}" target="_blank" class="text-primary fw-bold text-decoration-none small">
                                        <i class="fas fa-external-link-alt me-1"></i> عرض ملف Sitemap الحالي
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>

                    </div>

                    <div class="mt-5 pt-3 border-top text-end">
                        <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">
                            <i class="fas fa-save me-2"></i> حفظ كافة التغييرات
                        </button>
                    </div>
                </form>

                <form id="sitemapForm" method="POST" action="{{ route('admin.seo.generate.sitemap') }}" style="display: none;">@csrf</form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sticky-top" style="top: 20px;">
                <h6 class="fw-bold mb-3"><i class="fab fa-google text-danger me-2"></i> معاينة المظهر في جوجل</h6>
                <div class="google-preview">
                    <span class="url">{{ url('/') }}</span>
                    <span class="title" id="preview-title">عنوان الموقع سيظهر هنا</span>
                    <span class="desc" id="preview-desc">هنا سيظهر وصف موقعك الذي تكتبه، تأكد من جعله جذاباً لزيادة عدد النقرات...</span>
                </div>
                
                <div class="alert alert-soft-warning small border-0">
                    <i class="fas fa-lightbulb me-2"></i> <strong>نصيحة:</strong> المواقع التي تمتلك أوصافاً دقيقة تحصل على زيارات أكثر بنسبة 30%.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function updatePreview() {
        const title = document.getElementById('site_title').value;
        const desc = document.getElementById('site_description').value;

        // تحديث المعاينة
        document.getElementById('preview-title').innerText = title || 'عنوان الموقع سيظهر هنا';
        document.getElementById('preview-desc').innerText = desc || 'هنا سيظهر وصف موقعك الذي تكتبه...';

        // تحديث العدادات
        const titleLen = title.length;
        const descLen = desc.length;

        document.getElementById('title-count').innerText = `${titleLen} / 60`;
        document.getElementById('title-count').className = `char-counter ${titleLen > 60 ? 'text-danger-count' : 'text-success-count'}`;

        document.getElementById('desc-count').innerText = `${descLen} / 160`;
        document.getElementById('desc-count').className = `char-counter ${descLen > 160 ? 'text-danger-count' : 'text-success-count'}`;
    }

    // التنصت على الإدخال
    document.getElementById('site_title').addEventListener('input', updatePreview);
    document.getElementById('site_description').addEventListener('input', updatePreview);

    // تشغيل فوري عند التحميل
    window.onload = updatePreview;
</script>
@endsection