        <!-- Modern Header -->
        <nav class="admin-header navbar navbar-expand-lg fixed-top">
            <div class="container-fluid px-2 px-md-3">
                <!-- Mobile Menu Toggle (Sidebar) -->
                <button class="btn btn-link text-white d-lg-none p-0 me-1" type="button" id="sidebarToggle" style="border: none; font-size: 18px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- Logo -->
                <a class="navbar-brand d-flex align-items-center flex-grow-1" href="{{url('admin')}}" style="margin: 0; padding: 0;">
                    <img src="front/assets/images/لوجو.png" alt="Logo" class="me-2" style="width: 35px; height: 35px;">
                    <span class="d-none d-sm-inline text-white fw-bold" style="font-size: 14px;">منصة مستر سامح</span>
                </a>

                <!-- Toggle Button for User Menu -->
                <button class="navbar-toggler p-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" style="border: none; color: white; font-size: 16px; width: 35px; height: 35px;">
                    <i class="fas fa-ellipsis-v"></i>
                </button>

                <!-- Right Side -->
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav align-items-center">
                        <!-- Notifications -->
                        <li class="nav-item dropdown">
                            <a class="nav-link position-relative p-2" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: white; font-size: 16px;">
                                <i class="fas fa-bell"></i>
                                @php
                                    $unreadCount = \App\Models\Notification::where(function($query) {
                                            $query->whereNull('notifiable_type')
                                                  ->orWhere('notifiable_type', '!=', \App\Models\Student::class);
                                        })
                                        ->where('is_read', false)
                                        ->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                    </span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationsDropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                                <li>
                                    <div class="dropdown-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">الإشعارات</h6>
                                        @if($unreadCount > 0)
                                            <form action="{{ route('admin.notifications.markAllRead') }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-link text-decoration-none" style="font-size: 12px; padding: 0;">تحديد الكل كمقروء</button>
                                            </form>
                                        @endif
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                @php
                                    $latestNotifications = \App\Models\Notification::where(function($query) {
                                            $query->whereNull('notifiable_type')
                                                  ->orWhere('notifiable_type', '!=', \App\Models\Student::class);
                                        })
                                        ->orderBy('created_at', 'desc')
                                        ->take(5)
                                        ->get();
                                @endphp
                                @if($latestNotifications->count() > 0)
                                    @foreach($latestNotifications as $notification)
                                    <li>
                                        <a class="dropdown-item notification-item {{ !$notification->is_read ? 'unread' : '' }}" href="{{ route('admin.notifications.index') }}">
                                            <div class="d-flex align-items-start">
                                                <div class="notification-icon me-2" style="color: {{ $notification->color === 'success' ? '#28a745' : ($notification->color === 'danger' ? '#dc3545' : ($notification->color === 'warning' ? '#ffc107' : '#17a2b8')) }};">
                                                    <i class="fas {{ $notification->icon ?? 'fa-info-circle' }}"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold" style="font-size: 13px;">{{ $notification->title }}</div>
                                                    <div class="text-muted" style="font-size: 11px;">{{ Str::limit($notification->message, 50) }}</div>
                                                    <small class="text-muted" style="font-size: 10px;">{{ $notification->created_at->diffForHumans() }}</small>
                                                </div>
                                                @if(!$notification->is_read)
                                                    <span class="badge bg-primary rounded-pill" style="font-size: 8px;">جديد</span>
                                                @endif
                                            </div>
                                        </a>
                                    </li>
                                    @endforeach
                                @else
                                    <li>
                                        <div class="dropdown-item text-center text-muted" style="font-size: 13px;">
                                            لا توجد إشعارات
                                        </div>
                                    </li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-center" href="{{ route('admin.notifications.index') }}">
                                        <strong>عرض جميع الإشعارات</strong>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        <!-- Fullscreen -->
                        <li class="nav-item">
                            <a class="nav-link p-2" href="#" id="btnFullscreen" style="color: white; font-size: 16px;">
                                <i class="fas fa-expand"></i>
                            </a>
                        </li>

                        <!-- User Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle p-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: white; font-size: 14px;">
                                <img src="front/assets/images/لوجو.png" alt="User" style="width: 35px; height: 35px; border-radius: 50%; border: 2px solid white; margin-left: 8px;">
                                <span class="d-none d-lg-inline">{{Auth::guard('web')->user()->name}}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <div class="dropdown-header">
                                        <h6 class="mb-0">{{Auth::guard('web')->user()->name}}</h6>
                                        <small class="text-muted">{{Auth::guard('web')->user()->email}}</small>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{url('admin-profile')}}">
                                        <i class="fas fa-user me-2 text-warning"></i> الملف الشخصي
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logoutAdmin') }}">
                                        <i class="fas fa-sign-out-alt me-2 text-danger"></i> تسجيل خروج
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Header End -->
