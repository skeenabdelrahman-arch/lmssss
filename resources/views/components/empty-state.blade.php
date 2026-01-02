@props(['icon' => 'fa-inbox', 'title' => 'لا توجد بيانات', 'message' => 'لم يتم العثور على أي بيانات في الوقت الحالي.', 'action' => null, 'actionText' => null])

<div class="empty-state">
    <div class="empty-state-icon">
        <i class="fas {{ $icon }}"></i>
    </div>
    <div class="empty-state-title">{{ $title }}</div>
    <div class="empty-state-message">{{ $message }}</div>
    @if($action && $actionText)
        <a href="{{ $action }}" class="btn btn-modern btn-modern-primary">
            {{ $actionText }}
        </a>
    @endif
</div>




