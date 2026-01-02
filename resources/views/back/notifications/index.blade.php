@extends('back_layouts.master')

@section('title', 'مركز الإشعارات الذكي')

@section('css')
<style>
    /* حاوية الفلاتر */
    .filter-chip {
        padding: 8px 18px;
        border-radius: 50px;
        background: #f1f5f9;
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
        border: 1px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .filter-chip.active {
        background: var(--primary-color);
        color: white;
        box-shadow: 0 4px 12px rgba(155, 95, 255, 0.2);
    }
    .filter-chip:hover:not(.active) {
        background: #e2e8f0;
    }

    /* كروت الإشعارات المبتكرة */
    .notif-item {
        background: white;
        border-radius: 16px;
        margin-bottom: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #f1f5f9;
        position: relative;
    }

    .notif-item.unread {
        background: linear-gradient(to left, #ffffff, #f8faff);
        border-right: 4px solid var(--primary-color);
    }

    .notif-link {
        display: flex;
        padding: 16px;
        text-decoration: none !important;
        color: inherit;
    }

    .icon-box {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    /* الألوان حسب النوع */
    .bg-soft-success { background: #ecfdf5; color: #10b981; }
    .bg-soft-blue { background: #eff6ff; color: #3b82f6; }
    .bg-soft-purple { background: #f5f3ff; color: #8b5cf6; }

    .notif-body { flex-grow: 1; margin-right: 15px; }
    .notif-title { font-weight: 800; color: #1e293b; font-size: 15px; margin-bottom: 3px; }
    .notif-text { color: #64748b; font-size: 13px; line-height: 1.5; margin-bottom: 8px; }

    /* الأكشنز الجانبية */
    .notif-actions {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 8px;
        padding-left: 15px;
        opacity: 0;
        transition: 0.3s;
    }

    .notif-item:hover .notif-actions { opacity: 1; }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: #f8fafc;
        color: #94a3b8;
        transition: 0.2s;
    }
    .action-btn:hover { background: #fee2e2; color: #ef4444; }
    .action-btn.mark-read:hover { background: #dcfce7; color: #22c55e; }

    /* Dark Mode */
    [data-theme="dark"] .notif-item { background: #1a1a27; border-color: #2b2b40; }
    [data-theme="dark"] .notif-title { color: #ffffff; }
    [data-theme="dark"] .notif-text { color: #a2a2c5; }
    [data-theme="dark"] .filter-chip { background: #2b2b40; color: #a2a2c5; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-black mb-1">🔔 مركز الإشعارات</h3>
            <p class="text-muted small">تابع آخر نشاطات منصتك لحظة بلحظة</p>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="d-inline-flex gap-2">
                <form action="{{ route('admin.notifications.markAllRead') }}" method="POST">
                    @csrf
                    <button class="btn btn-white shadow-sm btn-sm fw-bold px-3 border rounded-pill">
                        <i class="fas fa-eye me-1 text-primary"></i> قراءة الكل
                    </button>
                </form>
                <form action="{{ route('admin.notifications.deleteAll') }}" method="POST">
                    @csrf
                    <button class="btn btn-white shadow-sm btn-sm fw-bold px-3 border rounded-pill text-danger">
                        <i class="fas fa-trash-alt me-1"></i> مسح الكل
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mb-4 overflow-auto pb-2" style="white-space: nowrap;">
        <div class="filter-chip active" data-filter="all">الكل</div>
        <div class="filter-chip" data-filter="student_registered">
            <i class="fas fa-user-plus"></i> تسجيل الطلاب
        </div>
        <div class="filter-chip" data-filter="subscription_added">
            <i class="fas fa-credit-card"></i> الاشتراكات
        </div>
    </div>

    <div class="notifications-container">
        @forelse($notifications as $notif)
            @php
                $config = [
                    'student_registered' => ['icon' => 'fa-user-graduate', 'class' => 'bg-soft-success'],
                    'subscription_added' => ['icon' => 'fa-wallet', 'class' => 'bg-soft-blue'],
                    'default' => ['icon' => 'fa-bell', 'class' => 'bg-soft-purple']
                ];
                $style = $config[$notif->type] ?? $config['default'];
            @endphp

            <div class="notif-item {{ !$notif->is_read ? 'unread' : '' }} filter-item" data-type="{{ $notif->type }}">
                <div class="d-flex align-items-center">
                    <a href="{{ $notif->url ?? '#' }}" class="notif-link flex-grow-1">
                        <div class="icon-box {{ $style['class'] }}">
                            <i class="fas {{ $style['icon'] }}"></i>
                        </div>
                        <div class="notif-body">
                            <div class="d-flex justify-content-between">
                                <h6 class="notif-title">{{ $notif->title }}</h6>
                                <span class="text-muted" style="font-size: 11px;">{{ $notif->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="notif-text mb-1">{{ Str::limit($notif->message, 120) }}</p>
                            @if(!$notif->is_read)
                                <span class="badge bg-primary rounded-pill" style="font-size: 9px; padding: 3px 8px;">جديد</span>
                            @endif
                        </div>
                    </a>

                    <div class="notif-actions">
                        @if(!$notif->is_read)
                        <form action="{{ route('admin.notifications.read', $notif->id) }}" method="POST">
                            @csrf
                            <button class="action-btn mark-read" title="تحديد كمقروء"><i class="fas fa-check"></i></button>
                        </form>
                        @endif
                        <form action="{{ route('admin.notifications.destroy', $notif->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="action-btn" title="حذف"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-bell-slash fa-4x text-light"></i>
                </div>
                <h5 class="text-muted">هدوء تام.. لا توجد إشعارات</h5>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>
@endsection

@section('js')
<script>
    // نظام الفلترة اللحظي (Client-side filtering)
    document.querySelectorAll('.filter-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            // تحديث الشكل
            document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');
            const items = document.querySelectorAll('.filter-item');

            items.forEach(item => {
                if (filter === 'all' || item.getAttribute('data-type') === filter) {
                    item.style.display = 'block';
                    item.style.animation = 'fadeIn 0.4s';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // إضافة أنيميشن بسيط عند الفلترة
    const style = document.createElement('style');
    style.innerHTML = `@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }`;
    document.head.appendChild(style);
</script>
@endsection