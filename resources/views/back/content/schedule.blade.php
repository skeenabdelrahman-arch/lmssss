@extends('back_layouts.master')

@section('title', 'إدارة جدول النشر الذكي')

@section('css')
<style>
    :root {
        --primary-glow: rgba(155, 95, 255, 0.15);
    }

    /* كروت الجدولة المطورة */
    .schedule-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
    }

    .schedule-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.06);
        border-color: #9B5FFF;
    }

    .lecture-icon {
        width: 50px;
        height: 50px;
        background: var(--primary-glow);
        color: #9B5FFF;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    /* العداد التنازلي */
    .timer-box {
        background: #f8fafc;
        padding: 10px 20px;
        border-radius: 12px;
        min-width: 150px;
        text-align: center;
    }

    .countdown-timer {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 800;
        font-size: 1.1rem;
        color: #1e293b;
        display: block;
    }

    /* حالة النبض */
    .status-pulse {
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        display: inline-block;
        margin-left: 8px;
        animation: pulse-green 2s infinite;
    }

    @keyframes pulse-green {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    /* مدخلات الفورم */
    .premium-input {
        border-radius: 12px;
        padding: 12px;
        border: 1px solid #e2e8f0;
    }
    
    .premium-input:focus {
        border-color: #9B5FFF;
        box-shadow: 0 0 0 4px var(--primary-glow);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-black text-dark mb-1">📅 جدول النشر الآلي</h3>
            <p class="text-muted small mb-0">تحكم في مواعيد ظهور المحاضرات للطلاب بدقة الثواني</p>
        </div>
        @if($hasScheduledColumn)
        <span class="badge bg-white shadow-sm text-dark p-2 px-3 border rounded-pill">
            <i class="fas fa-server text-success me-1"></i> خادم الجدولة متصل
        </span>
        @endif
    </div>

    @if(!$hasScheduledColumn)
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
        <div class="d-flex">
            <i class="fas fa-database fa-2x me-3"></i>
            <div>
                <h6 class="fw-bold">تحديث قاعدة البيانات مطلوب!</h6>
                <p class="mb-0 small">يجب تشغيل <code class="bg-dark text-white p-1 px-2 rounded">php artisan migrate</code> لتفعيل هذه الصفحة.</p>
            </div>
        </div>
    </div>
    @endif

    <div class="row g-4">
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-4"><i class="fas fa-plus-circle text-primary me-2"></i> موعد جديد</h5>
                
                <form method="POST" action="{{ route('admin.content.schedule.store', 0) }}" id="scheduleForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">المحاضرة المراد جدولتها</label>
                        <select class="form-select premium-input select2" name="lecture_id" id="lectureSelect" required>
                            <option value="">-- ابحث عن اسم المحاضرة --</option>
                            @foreach($all_lectures as $lecture)
                            <option value="{{ $lecture->id }}">{{ $lecture->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">تاريخ ووقت الظهور</label>
                        <input type="datetime-local" class="form-control premium-input" name="scheduled_at" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm">
                        <i class="fas fa-clock me-2"></i> تأكيد الجدولة
                    </button>
                </form>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold mb-4">المواعيد القائمة <span class="badge bg-light text-primary ms-2">{{ count($scheduled_lectures) }}</span></h5>

                <div id="scheduledList">
                    @forelse($scheduled_lectures as $lecture)
                    <div class="schedule-card">
                        <div class="d-flex align-items-center">
                            <div class="lecture-icon me-3">
                                <i class="fas fa-video"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">{{ $lecture->title }}</h6>
                                <span class="text-muted small">
                                    <i class="far fa-calendar-alt me-1"></i> {{ $lecture->scheduled_at->format('Y-m-d | h:i A') }}
                                </span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="timer-box border">
                                <span class="status-pulse"></span>
                                <span class="countdown-timer" 
                                      id="timer-{{ $lecture->id }}" 
                                      data-time="{{ $lecture->scheduled_at->toIso8601String() }}">
                                    00:00:00
                                </span>
                                <small class="text-muted" style="font-size: 9px;">الوقت المتبقي</small>
                            </div>

                            <button class="btn btn-outline-danger btn-sm rounded-circle" 
                                    onclick="cancelSchedule({{ $lecture->id }})" 
                                    title="إلغاء الجدولة">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-light mb-3"></i>
                        <h6 class="text-muted">لا يوجد محاضرات في قائمة الانتظار حالياً</h6>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // 1. تحديث رابط الفورم ديناميكياً
    document.getElementById('lectureSelect').addEventListener('change', function() {
        const form = document.getElementById('scheduleForm');
        const lectureId = this.value;
        const url = "{{ route('admin.content.schedule.store', ':id') }}";
        form.action = url.replace(':id', lectureId);
    });

    // 2. محرك العداد التنازلي الحي
    function initCountdowns() {
        const timers = document.querySelectorAll('.countdown-timer');
        
        setInterval(() => {
            timers.forEach(timer => {
                const target = new Date(timer.getAttribute('data-time')).getTime();
                const now = new Date().getTime();
                const diff = target - now;

                if (diff <= 0) {
                    timer.innerHTML = '<span class="text-success small">تم النشر ✓</span>';
                    timer.closest('.timer-box').style.borderColor = '#10b981';
                    return;
                }

                const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const s = Math.floor((diff % (1000 * 60)) / 1000);

                let text = "";
                if (d > 0) text += `${d}d `;
                text += `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                
                timer.innerText = text;
            });
        }, 1000);
    }

    // 3. إلغاء الجدولة
    function cancelSchedule(id) {
        if (confirm('هل أنت متأكد من إلغاء جدولة هذه المحاضرة؟')) {
            // تنفيذ طلب الإلغاء هنا (AJAX أو Form Submit)
            alert('تم طلب الإلغاء للمحاضرة رقم: ' + id);
        }
    }

    // تشغيل العداد عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', initCountdowns);
</script>
@endsection