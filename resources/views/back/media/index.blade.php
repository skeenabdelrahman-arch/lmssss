@extends('back_layouts.master')

@section('title', 'مكتبة الوسائط')

@section('css')
<style>
    /* تحسينات الشبكة والعناصر */
    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 25px;
        margin-top: 25px;
    }

    .media-item {
        background: white;
        border-radius: 18px;
        padding: 10px;
        border: 1px solid #f1f5f9;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .media-item:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        border-color: #9B5FFF;
    }

    /* معاينة الملفات */
    .media-preview {
        width: 100%;
        height: 140px;
        border-radius: 14px;
        overflow: hidden;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        position: relative;
    }

    .media-preview img {
        width: 100%; height: 100%; object-fit: cover;
    }

    /* طبقة الإجراءات عند الهوفر */
    .media-overlay {
        position: absolute;
        inset: 0;
        background: rgba(155, 95, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: 0.3s;
        backdrop-filter: blur(2px);
    }

    .media-item:hover .media-overlay { opacity: 1; }

    /* تفاصيل الملف */
    .media-details { padding: 5px; }
    .media-name {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .media-info { font-size: 11px; color: #64748b; margin-top: 2px; }

    /* منطقة الرفع المطورة */
    .upload-zone-premium {
        background: white;
        border: 2px dashed #e2e8f0;
        border-radius: 24px;
        padding: 50px 20px;
        text-align: center;
        transition: 0.3s;
        cursor: pointer;
    }
    .upload-zone-premium:hover, .upload-zone-premium.dragover {
        border-color: #9B5FFF;
        background: rgba(155, 95, 255, 0.02);
    }

    /* شريط التقدم العائم */
    #uploadProgressContainer {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 350px;
        z-index: 9999;
        display: none;
    }
    .progress-toast {
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        padding: 20px;
        border-left: 5px solid #9B5FFF;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="page-header-modern mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-black mb-0"><i class="fas fa-photo-video text-primary me-2"></i> مكتبة الوسائط</h4>
            <p class="text-muted small">إدارة الصور، الفيديوهات والملفات التعليمية</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4" onclick="document.getElementById('fileInput').click()">
            <i class="fas fa-plus me-2"></i> رفع ملفات جديدة
        </button>
    </div>

    <div class="modern-card mb-4 border-0 shadow-sm">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-filter text-muted"></i></span>
                    <select class="form-select border-start-0" id="typeFilter" onchange="filterMedia()">
                        <option value="all">جميع أنواع الملفات</option>
                        <option value="images">الصور الفوتوغرافية</option>
                        <option value="videos">مقاطع الفيديو</option>
                        <option value="documents">المستندات والكتب</option>
                    </select>
                </div>
            </div>
            <div class="col-md-9">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0" id="searchInput" placeholder="ابحث باسم الملف..." onkeyup="filterMedia()">
                </div>
            </div>
        </div>
    </div>

    @if(!request()->get('picker'))
    <div class="upload-zone-premium mb-4" id="uploadZone" ondrop="handleDrop(event)" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" onclick="document.getElementById('fileInput').click()">
        <div class="upload-icon mb-3">
            <i class="fas fa-cloud-upload-alt fa-3x text-primary"></i>
        </div>
        <h5 class="fw-bold">اسحب الملفات وأفلتها هنا</h5>
        <p class="text-muted small">أو اضغط لاختيار الملفات من جهازك (يدعم الصور، الفيديو، PDF)</p>
        <input type="file" id="fileInput" multiple style="display: none;" onchange="uploadFiles(this.files)">
    </div>
    @endif

    <div class="media-grid" id="mediaGrid">
        @forelse($files as $file)
        <div class="media-item" 
             data-type="{{ str_starts_with($file['type'], 'image/') ? 'images' : (str_starts_with($file['type'], 'video/') ? 'videos' : 'documents') }}" 
             data-name="{{ strtolower($file['name']) }}"
             onclick="{{ request()->get('picker') ? "selectMediaFile(this, '{$file['url']}', '{$file['name']}')" : '' }}">
            
            <div class="media-preview">
                @if(str_starts_with($file['type'], 'image/'))
                    <img src="{{ $file['url'] }}" loading="lazy">
                @elseif(str_starts_with($file['type'], 'video/'))
                    <i class="fas fa-play-circle fa-3x text-primary opacity-50"></i>
                @else
                    <i class="fas fa-file-pdf fa-3x text-danger opacity-50"></i>
                @endif

                <div class="media-overlay">
                    <div class="d-flex gap-2">
                        <a href="{{ $file['url'] }}" target="_blank" class="btn btn-light btn-sm rounded-circle shadow-sm"><i class="fas fa-eye text-primary"></i></a>
                        @if(!request()->get('picker'))
                        <button class="btn btn-light btn-sm rounded-circle shadow-sm text-danger" onclick="deleteFile('{{ $file['name'] }}')"><i class="fas fa-trash"></i></button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="media-details">
                <div class="media-name" title="{{ $file['name'] }}">{{ $file['name'] }}</div>
                <div class="media-info d-flex justify-content-between mt-1">
                    <span>{{ strtoupper(explode('/', $file['type'])[1] ?? 'FILE') }}</span>
                    <span>{{ number_format($file['size'] / 1024, 1) }} KB</span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="100" class="opacity-25 mb-3">
            <h5 class="text-muted">المكتبة فارغة حالياً</h5>
        </div>
        @endforelse
    </div>
</div>

<div id="uploadProgressContainer">
    <div class="progress-toast">
        <h6 class="fw-bold mb-3 d-flex align-items-center">
            <span class="spinner-border spinner-border-sm text-primary me-2"></span>
            جاري معالجة الملفات...
        </h6>
        <div id="uploadProgressItems"></div>
    </div>
</div>
@endsection

@section('js')
<script>
    // تحسين فلترة الملفات
    function filterMedia() {
        const type = document.getElementById('typeFilter').value;
        const search = document.getElementById('searchInput').value.toLowerCase();
        const items = document.querySelectorAll('.media-item');
        
        items.forEach(item => {
            const matchesType = type === 'all' || item.dataset.type === type;
            const matchesSearch = item.dataset.name.includes(search);
            item.style.display = (matchesType && matchesSearch) ? 'block' : 'none';
        });
    }

    // رفع الملفات مع Progress Bar احترافي
    function uploadFiles(files) {
        if (!files.length) return;

        const container = document.getElementById('uploadProgressContainer');
        const itemsList = document.getElementById('uploadProgressItems');
        container.style.display = 'block';
        itemsList.innerHTML = '';

        const formData = new FormData();
        Array.from(files).forEach((file, i) => {
            formData.append('files[]', file);
            itemsList.innerHTML += `
                <div class="mb-2">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-truncate" style="max-width: 200px;">${file.name}</span>
                        <b id="up-perc-${i}">0%</b>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div id="up-bar-${i}" class="progress-bar bg-primary progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                    </div>
                </div>
            `;
        });

        const xhr = new XMLHttpRequest();
        xhr.upload.addEventListener('progress', e => {
            const percent = Math.round((e.loaded / e.total) * 100);
            Array.from(files).forEach((_, i) => {
                document.getElementById(`up-bar-${i}`).style.width = percent + '%';
                document.getElementById(`up-perc-${i}`).innerText = percent + '%';
            });
        });

        xhr.onload = () => {
            if (xhr.status === 200) {
                location.reload();
            } else {
                alert('عذراً، حدث خطأ أثناء الرفع.');
                container.style.display = 'none';
            }
        };

        xhr.open('POST', '{{ route("admin.media.upload") }}');
        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
        xhr.send(formData);
    }

    // اختيار الملف في وضع Picker
    function selectMediaFile(el, url, name) {
        document.querySelectorAll('.media-item').forEach(i => i.style.borderColor = '#f1f5f9');
        el.style.borderColor = '#9B5FFF';
        
        if (window.parent) {
            window.parent.postMessage({ type: 'mediaSelected', url, name }, '*');
        }
    }

    // Drag & Drop
    const zone = document.getElementById('uploadZone');
    function handleDragOver(e) { e.preventDefault(); zone.classList.add('dragover'); }
    function handleDragLeave(e) { zone.classList.remove('dragover'); }
    function handleDrop(e) {
        e.preventDefault();
        zone.classList.remove('dragover');
        uploadFiles(e.dataTransfer.files);
    }
</script>
@endsection