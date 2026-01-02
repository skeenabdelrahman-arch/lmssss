{{-- Media Library Picker Component --}}
@props(['name' => 'media', 'type' => 'all', 'value' => '', 'label' => 'اختر من المكتبة', 'accept' => '*'])

<div class="media-picker-wrapper mb-3">
    <label class="form-label">{{ $label }}</label>
    <div class="d-flex gap-2 mb-2">
        <input type="text" 
               class="form-control" 
               id="media_{{ $name }}" 
               name="{{ $name }}" 
               value="{{ $value }}" 
               readonly 
               placeholder="لم يتم اختيار ملف">
        <button type="button" 
                class="btn btn-modern-primary" 
                onclick="openMediaPicker('{{ $name }}', '{{ $type }}', '{{ $accept }}')">
            <i class="fas fa-images me-2"></i> اختر من المكتبة
        </button>
        <button type="button" 
                class="btn btn-modern-secondary" 
                onclick="clearMediaPicker('{{ $name }}')">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div id="media_preview_{{ $name }}" class="media-preview mt-2"></div>
</div>

<style>
.media-preview {
    min-height: 100px;
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    background: #f8f9fa;
}

.media-preview img {
    max-width: 200px;
    max-height: 200px;
    border-radius: 8px;
    margin: 0 auto;
    display: block;
}

.media-preview video {
    max-width: 100%;
    max-height: 300px;
    border-radius: 8px;
    margin: 0 auto;
    display: block;
}
</style>

<script>
// Global message handler for media picker
if (typeof window.mediaPickerHandlers === 'undefined') {
    window.mediaPickerHandlers = {};
}

function openMediaPicker(name, type, accept) {
    // Open Media Library in a modal
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'mediaPickerModal_' + name;
    modal.innerHTML = `
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">اختر من المكتبة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <iframe src="{{ route('admin.media.index') }}?picker=1&type=${type}&accept=${accept}" 
                            style="width: 100%; height: 600px; border: none;"></iframe>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    // Create unique handler for this picker instance
    const handlerId = 'handler_' + name + '_' + Date.now();
    const messageHandler = function(event) {
        if (event.data.type === 'mediaSelected') {
            const fileUrl = event.data.url;
            const fileName = event.data.name;
            
            // تحديث حقل media picker
            const mediaInput = document.getElementById(`media_${name}`);
            if (mediaInput) {
                mediaInput.value = fileUrl;
            }
            
            // إذا كان name هو media_file_url، نحدث أيضاً file_url إذا كان موجوداً
            if (name === 'media_file_url') {
                const fileUrlInput = document.getElementById('file_url');
                if (fileUrlInput) {
                    // فقط إذا كان file_url فارغاً
                    if (!fileUrlInput.value.trim()) {
                        fileUrlInput.value = fileUrl;
                    }
                }
            }
            
            updateMediaPreview(name, fileUrl);
            bsModal.hide();
            modal.remove();
            
            // Remove this handler after use
            window.removeEventListener('message', window.mediaPickerHandlers[handlerId]);
            delete window.mediaPickerHandlers[handlerId];
        }
    };
    
    // Store handler reference
    window.mediaPickerHandlers[handlerId] = messageHandler;
    window.addEventListener('message', messageHandler);
}

function clearMediaPicker(name) {
    // مسح حقل media picker
    const mediaInput = document.getElementById(`media_${name}`);
    if (mediaInput) {
        mediaInput.value = '';
    }
    
    // مسح حقل file_url الأصلي إذا كان موجوداً
    const fileUrlInput = document.getElementById(name);
    if (fileUrlInput && fileUrlInput.id === name) {
        fileUrlInput.value = '';
    }
    
    // مسح المعاينة
    const preview = document.getElementById(`media_preview_${name}`);
    if (preview) {
        preview.innerHTML = '';
    }
}

function updateMediaPreview(name, url) {
    const preview = document.getElementById(`media_preview_${name}`);
    const extension = url.split('.').pop().toLowerCase();
    
    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
        preview.innerHTML = `<img src="${url}" alt="Preview">`;
    } else if (['mp4', 'webm', 'ogg'].includes(extension)) {
        preview.innerHTML = `<video controls><source src="${url}"></video>`;
    } else {
        preview.innerHTML = `<div class="text-muted"><i class="fas fa-file fa-3x"></i><br>${url}</div>`;
    }
}

// Initialize preview if value exists
@if($value)
updateMediaPreview('{{ $name }}', '{{ $value }}');
@endif
</script>




