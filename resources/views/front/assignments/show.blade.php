@extends('front.layouts.app')

@section('title')
{{ $assignment->title }}
@stop

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
        --primary-light: #b05ee7;
    }

    .assignment-section {
        padding: 120px 0 80px;
        background: linear-gradient(135deg, rgba(116,36,169,0.03), rgba(250,137,107,0.03));
        min-height: calc(100vh - 90px);
    }

    .assignment-card {
        background:white;
        border-radius:20px;
        padding:30px;
        box-shadow:0 10px 35px rgba(0,0,0,.08);
        border:2px solid transparent;
        position:relative;
        overflow:hidden;
    }

    .assignment-card[data-status="graded"] { background: linear-gradient(135deg, rgba(40,167,69,0.08), rgba(255,255,255,0.94)); }
    .assignment-card[data-status="late"],
    .assignment-card[data-status="pending"] { background: linear-gradient(135deg, rgba(23,162,184,0.07), rgba(255,255,255,0.95)); }
    .assignment-card[data-status="overdue"] { background: linear-gradient(135deg, rgba(220,53,69,0.08), rgba(255,255,255,0.95)); }

    .assignment-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:15px; }
    .assignment-title { font-size:1.5rem; font-weight:700; color: var(--primary-color); }

    .assignment-badge { padding:8px 14px; border-radius:20px; font-weight:600; font-size:.9rem; color:white; }
    .badge-graded { background:#28a745; }
    .badge-late { background:#ffc107; color:#212529; }
    .badge-pending { background:#17a2b8; }
    .badge-overdue { background:#dc3545; }
    .badge-new { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); }

    .assignment-icon { text-align:center; margin: 10px 0 20px; }
    .assignment-icon i { font-size:54px; color: var(--primary-color); }

    .assignment-info { display:flex; justify-content:space-around; margin: 18px 0; padding: 14px; background:#f8f9fa; border-radius:12px; }
    .assignment-info-item { text-align:center; }
    .assignment-info-item i { color: var(--secondary-color); margin-bottom:6px; }
    .assignment-info-item span { display:block; color: var(--primary-color); font-weight:600; font-size:0.95rem; }

    .divider { height:1px; background: #f0f0f0; margin: 24px 0; }

    .card-section-title { font-weight:700; color: var(--primary-color); margin-bottom:10px; }

    .submission-summary { background: #e8f5ff; border-radius: 12px; padding: 15px; }
    .submission-graded { background: #28a745; color:white; border-radius:12px; padding:16px; }

    .action-btn { width:100%; padding: 12px; border-radius: 10px; font-weight: 600; text-decoration: none; display: inline-block; text-align: center; transition: all 0.3s ease; border: none; cursor: pointer; }
    .action-btn.primary { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color:white; }
    .action-btn.primary:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(116,36,169,0.3); color:white; }
    .action-btn.outline { border:2px solid var(--primary-color); color: var(--primary-color); background:white; }
    .action-btn.outline:hover { background: var(--primary-color); color:white; }

    .footer-actions { display:flex; gap:12px; flex-wrap:wrap; justify-content:space-between; }

    .form-note { font-size:0.9rem; color:#6c757d; }
</style>

@php
    $status = $submission ? $submission->status : ($assignment->isOverdue() ? 'overdue' : 'new');
    $statusLabel = [
        'graded' => 'تم التصحيح',
        'late' => 'متأخر',
        'pending' => 'تم الإرسال',
        'overdue' => 'انتهى الموعد',
        'new' => 'جديد',
    ][$status] ?? 'جديد';
    $badgeClass = [
        'graded' => 'badge-graded',
        'late' => 'badge-late',
        'pending' => 'badge-pending',
        'overdue' => 'badge-overdue',
        'new' => 'badge-new',
    ][$status] ?? 'badge-new';
@endphp

<div class="assignment-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="assignment-card" data-status="{{ $status }}">
                    <div class="assignment-header">
                        <div class="assignment-title"><i class="fas fa-clipboard-check"></i> {{ $assignment->title }}</div>
                        <div class="assignment-badge {{ $badgeClass }}">{{ $statusLabel }}</div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <strong>{{ session('success') }}</strong>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong>{{ session('error') }}</strong>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    <div class="assignment-icon"><i class="fas fa-file-alt"></i></div>

                    <div class="assignment-info">
                        <div class="assignment-info-item">
                            <i class="fas fa-star"></i>
                            <span>{{ $assignment->total_marks }} درجة</span>
                        </div>
                        <div class="assignment-info-item">
                            <i class="fas fa-calendar"></i>
                            <span>{{ $assignment->deadline ? $assignment->deadline->format('Y-m-d H:i') : 'بدون موعد' }}</span>
                        </div>
                        <div class="assignment-info-item">
                            <i class="fas fa-flag-checkered"></i>
                            <span>{{ $statusLabel }}</span>
                        </div>
                    </div>

                    <div class="card-section-title">تفاصيل الواجب</div>
                    @if($assignment->description)
                        <p class="text-muted">{{ $assignment->description }}</p>
                    @else
                        <p class="text-muted">لا يوجد وصف متاح.</p>
                    @endif

                    @if($assignment->file_path)
                        <a href="{{ Storage::url($assignment->file_path) }}" target="_blank" class="action-btn outline mb-3" style="display:inline-block; width:auto;">
                            <i class="fas fa-download"></i> تحميل ملف الواجب
                        </a>
                    @endif

                    <div class="divider"></div>

                    @if($submission)
                        <div class="submission-summary">
                            <h5 class="mb-2"><i class="fas fa-check-circle"></i> تم إرسال الإجابة</h5>
                            <p class="mb-2"><strong>تاريخ الإرسال:</strong> {{ $submission->submitted_at->format('Y-m-d H:i') }}</p>
                            @if($submission->notes)
                                <p class="mb-2"><strong>ملاحظاتك:</strong> {{ $submission->notes }}</p>
                            @endif

                            @if($submission->status == 'graded')
                                <div class="submission-graded mt-3">
                                    <h5 class="mb-2"><i class="fas fa-trophy"></i> تم التصحيح</h5>
                                    <p class="mb-1"><strong>الدرجة:</strong> {{ $submission->marks }} / {{ $assignment->total_marks }}</p>
                                    <p class="mb-1"><strong>النسبة:</strong> {{ $submission->getPercentage() }}%</p>
                                    @if($submission->feedback)
                                        <p class="mb-0"><strong>ملاحظات المدرس:</strong> {{ $submission->feedback }}</p>
                                    @endif
                                </div>
                            @endif

                            @if($submission->file_path)
                                <div class="mt-3">
                                    <h6 class="mb-2"><i class="fas fa-images"></i> الملفات المرفوعة:</h6>
                                    @php
                                        $files = json_decode($submission->file_path, true) ?: [$submission->file_path];
                                    @endphp
                                    <div class="row">
                                        @foreach($files as $file)
                                            @php
                                                $extension = pathinfo($file, PATHINFO_EXTENSION);
                                                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                $fileUrl = route('storage.file', str_replace(DIRECTORY_SEPARATOR, '/', $file));
                                                $fileExists = Storage::disk('public')->exists($file);
                                            @endphp
                                            <div class="col-md-3 mb-2">
                                                @if($fileExists && $isImage)
                                                    <a href="{{ $fileUrl }}" target="_blank" data-lightbox="images">
                                                        <img src="{{ $fileUrl }}" class="img-thumbnail" style="height: 150px; width: 100%; object-fit: cover;">
                                                    </a>
                                                @elseif(!$fileExists)
                                                    <div class="alert alert-warning mb-0 p-2">
                                                        <small>الملف غير موجود</small>
                                                    </div>
                                                @else
                                                    <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-file"></i> ملف {{ $loop->iteration }}
                                                    </a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($submission->status != 'graded')
                                <form action="{{ route('student.assignments.submission.delete', $submission->id) }}" method="POST" class="mt-3">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn outline" style="width:auto;" onclick="return confirm('هل أنت متأكد من حذف إجابتك؟')">
                                        <i class="fas fa-trash"></i> حذف الإجابة
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        @if($assignment->isOverdue())
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> عذراً، انتهى الموعد النهائي لهذا الواجب
                            </div>
                        @else
                            <div class="card-section-title">إرسال الإجابة</div>
                            <form id="assignment-upload-form" action="{{ route('student.assignments.submit', $assignment->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                @if(!$assignment->questions || $assignment->questions->count() === 0)
                                    <div class="form-group">
                                        <label for="notes">ملاحظات (اختياري)</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                  id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif

                                @if($assignment->questions && $assignment->questions->count())
                                    <div class="card-section-title"><i class="fas fa-question-circle"></i> أسئلة الواجب</div>
                                    @foreach($assignment->questions as $q)
                                        @php
                                            $questionTypeIcon = [
                                                'mcq_single' => 'fas fa-dot-circle',
                                                'mcq_multi' => 'fas fa-check-square',
                                                'essay' => 'fas fa-file-alt'
                                            ][$q->type] ?? 'fas fa-question';
                                            
                                            $questionTypeName = [
                                                'mcq_single' => 'اختيار من متعدد',
                                                'mcq_multi' => 'اختيارات متعددة',
                                                'essay' => 'سؤال مقالي'
                                            ][$q->type] ?? $q->type;
                                            
                                            $questionColor = [
                                                'mcq_single' => '#17a2b8',
                                                'mcq_multi' => '#28a745',
                                                'essay' => '#fd7e14'
                                            ][$q->type] ?? '#6c757d';
                                        @endphp
                                        <div class="mb-4 p-4 border-0 shadow-sm" style="background: #fff; border-radius: 15px; border-right: 5px solid {{ $questionColor }};">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div style="flex: 1;">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span style="background: {{ $questionColor }}; color: white; width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: bold; margin-left: 10px;">{{ $loop->iteration }}</span>
                                                        <span style="font-size: 1.1rem; font-weight: 600; color: #333;">{{ $q->question_text }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2" style="flex-wrap: wrap; gap: 8px;">
                                                        <small style="background: {{ $questionColor }}22; color: {{ $questionColor }}; padding: 4px 12px; border-radius: 20px; font-weight: 600;">
                                                            <i class="{{ $questionTypeIcon }}"></i> {{ $questionTypeName }}
                                                        </small>
                                                        <small style="background: #f8f9fa; color: #495057; padding: 4px 12px; border-radius: 20px; font-weight: 600;">
                                                            <i class="fas fa-star"></i> {{ $q->max_marks }} درجة
                                                        </small>
                                                        @if($q->is_required)
                                                            <small style="background: #dc354522; color: #dc3545; padding: 4px 12px; border-radius: 20px; font-weight: 600;">
                                                                <i class="fas fa-exclamation-circle"></i> إلزامي
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            @if(in_array($q->type, ['mcq_single', 'mcq_multi']))
                                                <div class="mt-3" style="padding-right: 10px;">
                                                    @foreach($q->options as $opt)
                                                        <div class="custom-control {{ $q->type === 'mcq_single' ? 'custom-radio' : 'custom-checkbox' }} mb-2" style="padding: 12px 15px; background: #f8f9fa; border-radius: 10px; transition: all 0.3s; cursor: pointer;" onmouseover="this.style.background='#e9ecef'" onmouseout="this.style.background='#f8f9fa'">
                                                            <input
                                                                type="{{ $q->type === 'mcq_single' ? 'radio' : 'checkbox' }}"
                                                                class="custom-control-input"
                                                                id="q{{ $q->id }}opt{{ $opt->id }}"
                                                                name="question_answers[{{ $q->id }}][selected_options][]"
                                                                value="{{ $opt->id }}"
                                                                style="cursor: pointer;">
                                                            <label class="custom-control-label" for="q{{ $q->id }}opt{{ $opt->id }}" style="cursor: pointer; font-weight: 500; color: #495057;">{{ $opt->option_text }}</label>
                                                        </div>
                                                    @endforeach
                                                    @error('question_answers.' . $q->id . '.selected_options')
                                                        <div class="alert alert-danger mt-2 py-2" style="border-radius: 8px;"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            @elseif($q->type === 'essay')
                                                <div class="mt-3">
                                                    @if($q->allow_text)
                                                        <div class="form-group mb-3">
                                                            <label style="font-weight: 600; color: #495057;"><i class="fas fa-pen"></i> إجابة نصية</label>
                                                            <textarea class="form-control @error('question_answers.' . $q->id . '.answer_text') is-invalid @enderror" 
                                                                      name="question_answers[{{ $q->id }}][answer_text]" 
                                                                      rows="4" 
                                                                      placeholder="اكتب إجابتك هنا..."
                                                                      style="border-radius: 10px; border: 2px solid #e9ecef; padding: 12px;"></textarea>
                                                            @error('question_answers.' . $q->id . '.answer_text')
                                                                <span class="invalid-feedback"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    @endif
                                                    @if($q->allow_file)
                                                        <div class="form-group">
                                                            <label style="font-weight: 600; color: #495057;"><i class="fas fa-paperclip"></i> مرفق للسؤال</label>
                                                            <div class="custom-file">
                                                                <input type="file" 
                                                                       class="custom-file-input @error('question_answers.' . $q->id . '.attachment') is-invalid @enderror" 
                                                                       id="file_q{{ $q->id }}"
                                                                       name="question_answers[{{ $q->id }}][attachment]" 
                                                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                                                       onchange="updateFileName(this, {{ $q->id }})">
                                                                <label class="custom-file-label" for="file_q{{ $q->id }}" id="label_q{{ $q->id }}" style="border-radius: 10px; border: 2px dashed #e9ecef;">اختر ملف...</label>
                                                            </div>
                                                            <small class="form-text text-muted"><i class="fas fa-info-circle"></i> يمكنك رفع: PDF, Word, أو صورة</small>
                                                            @error('question_answers.' . $q->id . '.attachment')
                                                                <div class="invalid-feedback d-block"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif

                                <div class="form-group">
                                    <label for="file_path">ملفات الإجابة (صور) @if(!$assignment->questions || $assignment->questions->count() === 0)<span class="text-danger">*</span>@else<span class="text-muted">(اختياري)</span>@endif</label>
                                    <input type="file" class="form-control @error('file_path') is-invalid @enderror" 
                                           id="file_path" name="file_path[]" accept=".jpg,.jpeg,.png,.pdf" multiple {{ (!$assignment->questions || $assignment->questions->count() === 0) ? 'required' : '' }}>
                                    @error('file_path')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <div class="form-note">يمكنك اختيار أكثر من صورة. الحد: 20 ميجا لكل ملف.</div>
                                    <div id="preview-images" class="mt-3" style="display:none;">
                                        <div class="row" id="preview-container"></div>
                                    </div>
                                </div>

                                <div id="upload-error" class="alert alert-danger" style="display:none;"></div>

                                <div id="upload-progress" style="display:none; margin: 15px 0;">
                                    <div class="progress" style="height: 25px; border-radius: 10px;">
                                        <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" 
                                             role="progressbar" style="width: 0%; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));">
                                            <span id="progress-text" style="font-weight: 600;">0%</span>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" id="submit-btn" class="action-btn primary mt-2">
                                    <i class="fas fa-paper-plane"></i> إرسال الإجابة
                                </button>
                            </form>

                            <script>
                            // Update file name label
                            function updateFileName(input, questionId) {
                                const fileName = input.files[0]?.name || 'اختر ملف...';
                                document.getElementById('label_q' + questionId).textContent = fileName;
                            }
                            
                            // Preview images before upload
                            document.getElementById('file_path').addEventListener('change', function(e) {
                                const previewContainer = document.getElementById('preview-container');
                                const previewSection = document.getElementById('preview-images');
                                previewContainer.innerHTML = '';
                                
                                if (this.files.length > 0) {
                                    previewSection.style.display = 'block';
                                    Array.from(this.files).forEach(file => {
                                        if (file.type.startsWith('image/')) {
                                            const reader = new FileReader();
                                            reader.onload = function(e) {
                                                const col = document.createElement('div');
                                                col.className = 'col-md-3 mb-2';
                                                col.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="height: 150px; object-fit: cover;">`;
                                                previewContainer.appendChild(col);
                                            };
                                            reader.readAsDataURL(file);
                                        }
                                    });
                                } else {
                                    previewSection.style.display = 'none';
                                }
                            });

                            // Upload with progress + JSON feedback
                            (function() {
                                const form = document.getElementById('assignment-upload-form');
                                const progressDiv = document.getElementById('upload-progress');
                                const progressBar = document.getElementById('progress-bar');
                                const progressText = document.getElementById('progress-text');
                                const submitBtn = document.getElementById('submit-btn');
                                const errorBox = document.getElementById('upload-error');

                                const resetState = () => {
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> إرسال الإجابة';
                                    progressDiv.style.display = 'none';
                                };

                                form.addEventListener('submit', function(e) {
                                    e.preventDefault();

                                    errorBox.style.display = 'none';
                                    errorBox.textContent = '';

                                    const formData = new FormData(form);
                                    progressDiv.style.display = 'block';
                                    submitBtn.disabled = true;
                                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الرفع...';

                                    const xhr = new XMLHttpRequest();
                                    xhr.responseType = 'json';
                                    xhr.open('POST', form.action);
                                    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('input[name="_token"]').value);
                                    xhr.setRequestHeader('Accept', 'application/json');
                                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                                    xhr.upload.addEventListener('progress', function(event) {
                                        if (event.lengthComputable) {
                                            const percentComplete = (event.loaded / event.total) * 100;
                                            progressBar.style.width = percentComplete + '%';
                                            progressText.textContent = Math.round(percentComplete) + '%';
                                        }
                                    });

                                    xhr.addEventListener('load', function() {
                                        if (xhr.status >= 200 && xhr.status < 400) {
                                            window.location.reload();
                                            return;
                                        }

                                        const response = xhr.response || {};
                                        const validationMessages = response.errors ? Object.values(response.errors).flat().join(' ') : '';
                                        const message = validationMessages || response.message || 'حدث خطأ أثناء الرفع';

                                        errorBox.textContent = message;
                                        errorBox.style.display = 'block';
                                        resetState();
                                    });

                                    xhr.addEventListener('error', function() {
                                        errorBox.textContent = 'تعذر الاتصال بالخادم، حاول مرة أخرى.';
                                        errorBox.style.display = 'block';
                                        resetState();
                                    });

                                    xhr.send(formData);
                                });
                            })();
                            </script>
                        @endif
                    @endif

                    <div class="divider"></div>

                    <div class="footer-actions">
                        <a href="{{ route('student.assignments.index', $assignment->month_id) }}" class="action-btn outline" style="width:auto;">
                            <i class="fas fa-arrow-right"></i> رجوع للقائمة
                        </a>
                        @if($assignment->file_path)
                            <a href="{{ Storage::url($assignment->file_path) }}" target="_blank" class="action-btn primary" style="width:auto;">
                                <i class="fas fa-download"></i> تحميل ملف الواجب
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
