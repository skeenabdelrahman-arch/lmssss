@extends('back_layouts.master')

@section('title')
إضافة قيد محاضرات
@endsection

@section('content')
<div class="page-header">
    <h2><i class="fas fa-plus me-2"></i> إضافة قيد محاضرات</h2>
    <a href="{{ route('admin.lecture_restrictions.index') }}" class="btn btn-modern-secondary">
        <i class="fas fa-arrow-right me-2"></i> العودة
    </a>
</div>

<div class="modern-card">
    <form method="POST" action="{{ route('admin.lecture_restrictions.store') }}">
        @csrf
        
        <div class="mb-3">
            <label class="form-label">الطلاب <span class="text-danger">*</span></label>
            <div class="mb-2">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllStudents()">تحديد الكل</button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllStudents()">إلغاء التحديد</button>
                <input type="text" class="form-control form-control-sm mt-2" id="studentSearch" placeholder="ابحث عن طالب...">
            </div>
            <div class="card-modern p-3" style="max-height: 400px; overflow-y: auto; background: #f8f9fa;">
                <div class="row" id="studentsContainer">
                    @foreach($students as $student)
                        <div class="col-md-6 mb-2 student-item" data-name="{{ strtolower($student->first_name . ' ' . $student->second_name . ' ' . $student->third_name . ' ' . $student->forth_name) }}" data-phone="{{ $student->phone }}">
                            <div class="form-check">
                                <input class="form-check-input student-checkbox" type="checkbox" name="student_ids[]" value="{{ $student->id }}" id="student_{{ $student->id }}">
                                <label class="form-check-label" for="student_{{ $student->id }}">
                                    <strong>{{ $student->first_name }} {{ $student->second_name }} {{ $student->third_name }} {{ $student->forth_name }}</strong>
                                    <small class="text-muted d-block">{{ $student->phone }}</small>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @error('student_ids')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">الكورس <span class="text-danger">*</span></label>
                <select class="form-select" name="month_id" id="month_id" required>
                    <option value="">اختر الكورس</option>
                    @foreach($months as $month)
                        <option value="{{ $month->id }}">
                            {{ $month->name }} - {{ $month->grade }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">المحاضرات المراد حظرها <span class="text-danger">*</span></label>
            <div id="lectures-container" class="card-modern p-3" style="max-height: 400px; overflow-y: auto; background: #f8f9fa;">
                <p class="text-muted text-center">اختر الكورس أولاً لعرض المحاضرات</p>
            </div>
            @error('lectures')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">سبب الحظر (اختياري)</label>
            <textarea class="form-control" name="reason" rows="3" placeholder="مثل: محتوى متقدم - اشتراك أساسي فقط">{{ old('reason') }}</textarea>
            <small class="text-muted">هذا السبب سيظهر للأدمن فقط</small>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>ملاحظة:</strong> يمكنك اختيار أكثر من طالب وأكثر من محاضرة في نفس الوقت. سيتم تطبيق القيود على جميع الطلاب المحددين للمحاضرات المحددة.
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-modern-primary">
                <i class="fas fa-save me-2"></i> حفظ القيود
            </button>
        </div>
    </form>
</div>

<script>
// تحديد كل الطلاب
function selectAllStudents() {
    document.querySelectorAll('.student-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
}

// إلغاء تحديد كل الطلاب
function deselectAllStudents() {
    document.querySelectorAll('.student-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
}

// البحث في الطلاب
document.addEventListener('DOMContentLoaded', function() {
    const studentSearch = document.getElementById('studentSearch');
    if (studentSearch) {
        studentSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const studentItems = document.querySelectorAll('.student-item');
            
            studentItems.forEach(item => {
                const name = item.getAttribute('data-name');
                const phone = item.getAttribute('data-phone');
                
                if (name.includes(searchTerm) || phone.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});

document.getElementById('month_id').addEventListener('change', function() {
    const monthId = this.value;
    const container = document.getElementById('lectures-container');
    
    if (!monthId) {
        container.innerHTML = '<p class="text-muted text-center">اختر الكورس أولاً لعرض المحاضرات</p>';
        return;
    }
    
    container.innerHTML = '<p class="text-center"><i class="fas fa-spinner fa-spin"></i> جاري التحميل...</p>';
    
    fetch(`{{ route('admin.lecture_restrictions.api.lectures', '') }}/${monthId}`)
        .then(response => response.json())
        .then(lectures => {
            if (lectures.length === 0) {
                container.innerHTML = '<p class="text-muted text-center">لا توجد محاضرات في هذا الكورس</p>';
                return;
            }
            
            let html = '<div class="mb-2"><button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">تحديد الكل</button> <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">إلغاء التحديد</button></div>';
            html += '<div class="row">';
            
            lectures.forEach(lecture => {
                html += `
                    <div class="col-md-6 mb-2">
                        <div class="form-check">
                            <input class="form-check-input lecture-checkbox" type="checkbox" name="lectures[]" value="${lecture.id}" id="lecture_${lecture.id}">
                            <label class="form-check-label" for="lecture_${lecture.id}">
                                <i class="fas fa-video me-1"></i> ${lecture.title}
                            </label>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = '<p class="text-danger text-center">حدث خطأ في تحميل المحاضرات</p>';
        });
});

function selectAll() {
    document.querySelectorAll('.lecture-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAll() {
    document.querySelectorAll('.lecture-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
}
</script>
@endsection
