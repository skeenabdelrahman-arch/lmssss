<div class="container-fluid">
    <div class="row">
        <!-- Modern Sidebar -->
        <div class="side-menu-fixed">
            <ul class="side-menu" id="sidebarnav">
                <!-- Dashboard -->
                <li>
                    <a href="{{url('admin')}}" class="{{ request()->is('admin') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>الرئيسية</span>
                    </a>
                </li>
                
                <!-- Menu Title -->
                <li class="menu-title">حماده مراد</li>
                
                <!-- Months -->
                <li>
                    <a href="{{route('month.index')}}" class="{{ request()->is('month*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>الأشهر</span>
                    </a>
                </li>
                
                <!-- Lectures -->
                <li>
                    <a href="{{route('lecture.index')}}" class="{{ request()->is('lecture*') ? 'active' : '' }}">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>المحاضرات</span>
                    </a>
                </li>
                
                <!-- PDFs -->
                <li>
                    <a href="{{route('pdf.index')}}" class="{{ request()->is('pdf*') ? 'active' : '' }}">
                        <i class="fas fa-file-pdf"></i>
                        <span>المذكرات</span>
                    </a>
                </li>

                <!-- Assignments -->
                <li>
                    <a href="{{route('assignments.index')}}" class="{{ request()->is('admin/assignments*') ? 'active' : '' }}">
                        <i class="fas fa-tasks"></i>
                        <span>الواجبات</span>
                    </a>
                </li>
                
                <!-- Exams -->
                <li>
                    <a href="{{route('exam_name.index')}}" class="{{ request()->is('exam_name*') || request()->is('add-question*') ? 'active' : '' }}">
                        <i class="fas fa-book-open"></i>
                        <span>الامتحانات</span>
                    </a>
                </li>
                
                <!-- Public Exam Results -->
                <li>
                    <a href="{{route('publicExam.results')}}" class="{{ request()->is('admin/public-exam-results*') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i>
                        <span>نتائج الامتحانات العامة</span>
                    </a>
                </li>
                
                <!-- Top Students -->
                <li>
                    <a href="{{route('admin.analytics.top.students')}}" class="{{ request()->is('admin/analytics/top-students*') ? 'active' : '' }}">
                        <i class="fas fa-trophy"></i>
                        <span>أعلى 10 طلاب</span>
                    </a>
                </li>
                
                <!-- Student Subscriptions -->
                <li>
                    <a href="{{route('student_subscription.index')}}" class="{{ request()->is('student_subscription*') ? 'active' : '' }}">
                        <i class="fas fa-user-graduate"></i>
                        <span>اشتراكات الطلاب</span>
                    </a>
                </li>

                <!-- All Access Students -->
                <li>
                    <a href="{{ route('admin.students.all_access.form') }}" class="{{ request()->is('students/all-access') ? 'active' : '' }}">
                        <i class="fas fa-unlock-alt"></i>
                        <span>اشتراك شامل للطلاب</span>
                    </a>
                </li>

                <!-- Notifications -->
                <li>
                    <a href="{{route('admin.notifications.index')}}" class="{{ request()->is('admin/notifications*') ? 'active' : '' }}">
                        <i class="fas fa-bell"></i>
                        <span>الإشعارات</span>
                        @php
                            $unreadCount = \App\Models\Notification::where(function($query) {
                                    $query->whereNull('notifiable_type')
                                          ->orWhere('notifiable_type', '!=', \App\Models\Student::class);
                                })
                                ->where('is_read', false)
                                ->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>
        <!-- Sidebar End -->

        <!--=================================
