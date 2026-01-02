@extends('front.layouts.app')
@section('title')
{{ site_name() }} | الإشعارات
@endsection

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
    }

    .notifications-container {
        max-width: 900px;
        margin: 40px auto;
        padding: 0 15px;
    }

    .notifications-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 20px;
    }

    .notifications-header h2 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin: 0;
    }

    .notifications-header .btn-group {
        display: flex;
        gap: 10px;
    }

    .notifications-header .btn {
        padding: 8px 16px;
        border-radius: 20px;
        border: 1px solid #ddd;
        background: white;
        color: var(--primary-color);
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .notifications-header .btn:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .notification-item {
        background: white;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
        display: flex;
        gap: 15px;
        align-items: flex-start;
    }

    .notification-item:hover {
        box-shadow: 0 5px 20px rgba(116, 36, 169, 0.15);
        border-color: var(--primary-color);
    }

    .notification-item.unread {
        background: rgba(116, 36, 169, 0.05);
        border-color: var(--primary-color);
    }

    .notification-icon {
        width: 50px;
        height: 50px;
        min-width: 50px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    .notification-content {
        flex: 1;
    }

    .notification-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--primary-color);
        margin: 0 0 8px 0;
    }

    .notification-message {
        color: #666;
        font-size: 0.95rem;
        margin: 0 0 10px 0;
        line-height: 1.6;
    }

    .notification-time {
        font-size: 0.85rem;
        color: #999;
    }

    .notification-actions {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .notification-actions a,
    .notification-actions button {
        padding: 6px 12px;
        border: 1px solid #ddd;
        background: white;
        color: #666;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .notification-actions a:hover,
    .notification-actions button:hover {
        background: #f5f5f5;
        border-color: #999;
    }

    .mark-read-btn {
        padding: 8px 12px;
        border: 1px solid #ddd;
        background: white;
        color: var(--primary-color);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .mark-read-btn:hover {
        background: var(--primary-color);
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state-icon {
        font-size: 80px;
        color: #ddd;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        color: #666;
        font-size: 1.5rem;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #999;
        font-size: 1rem;
    }

    @media (max-width: 768px) {
        .notifications-header {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }

        .notifications-header .btn-group {
            width: 100%;
            flex-wrap: wrap;
        }

        .notification-item {
            flex-direction: column;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            font-size: 20px;
        }
    }
</style>

<div class="notifications-container">
    <!-- Header -->
    <div class="notifications-header">
        <h2><i class="fas fa-bell me-2"></i> الإشعارات</h2>
        @if($notifications->total() > 0)
        <div class="btn-group">
            <button class="btn mark-all-btn" id="markAllBtn" onclick="markAllAsRead()">
                <i class="fas fa-check-double me-1"></i> تحديد الكل
            </button>
        </div>
        @endif
    </div>

    <!-- Notifications List -->
    @if($notifications->count() > 0)
        @foreach($notifications as $notification)
        <div class="notification-item {{ !$notification->is_read ? 'unread' : '' }}" data-id="{{ $notification->id }}">
            <div class="notification-icon">
                <i class="fas {{ $notification->icon ?? 'fa-bell' }}"></i>
            </div>
            <div class="notification-content">
                <h4 class="notification-title">{{ $notification->title }}</h4>
                <p class="notification-message">{{ $notification->message }}</p>
                <div class="notification-time">
                    <i class="fas fa-clock me-1"></i>
                    {{ $notification->created_at->locale('ar')->diffForHumans() }}
                </div>
                @if($notification->url)
                <div class="notification-actions">
                    <a href="{{ $notification->url }}" class="btn" style="background: var(--primary-color); color: white; text-decoration: none;">
                        <i class="fas fa-external-link-alt me-1"></i> اذهب إلى الصفحة
                    </a>
                </div>
                @endif
            </div>
            @if(!$notification->is_read)
            <button class="mark-read-btn" onclick="markAsRead({{ $notification->id }}, this)" title="تحديد كمقروءة">
                <i class="fas fa-check"></i>
            </button>
            @endif
        </div>
        @endforeach

        <!-- Pagination -->
        @if($notifications->hasPages())
        <div class="pagination justify-content-center mt-4">
            {{ $notifications->links('pagination::bootstrap-4') }}
        </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-inbox"></i>
            </div>
            <h3>لا توجد إشعارات</h3>
            <p>ليس لديك أي إشعارات حالياً. سيتم إخطارك عندما يحدث شيء مهم!</p>
        </div>
    @endif
</div>

<script>
    function markAsRead(notificationId, btn) {
        fetch('{{ route("notifications.mark.read", "") }}/' + notificationId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = document.querySelector(`[data-id="${notificationId}"]`);
                if (item) {
                    item.classList.remove('unread');
                    btn.style.display = 'none';
                }
            }
        });
    }

    function markAllAsRead() {
        if (!confirm('هل تريد تحديد جميع الإشعارات كمقروءة؟')) {
            return;
        }

        fetch('{{ route("notifications.mark.all.read") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const items = document.querySelectorAll('.notification-item.unread');
                items.forEach(item => {
                    item.classList.remove('unread');
                    const btn = item.querySelector('.mark-read-btn');
                    if (btn) btn.style.display = 'none';
                });
            }
        });
    }
</script>

@endsection
