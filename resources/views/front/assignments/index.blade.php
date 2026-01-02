@extends('front.layouts.app')

@section('title')
الواجبات
@stop

@section('content')
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
        --primary-light: #b05ee7;
    }

    .assignments-section {
        padding: 120px 0 80px;
        background: linear-gradient(135deg, rgba(116,36,169,0.03), rgba(250,137,107,0.03));
        min-height: calc(100vh - 90px);
    }

    .page-header { text-align:center; margin-bottom:40px; }
    .page-header h1 { font-size:2.5rem; font-weight:700; color: var(--primary-color); margin-bottom: 15px; }

    .assignments-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap:30px; }

    .assignment-card {
        background: white;
        border-radius: 20px;
        padding: 28px;
        transition: all .3s ease;
        box-shadow: 0 5px 20px rgba(0,0,0,.08);
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .assignment-card[data-status="graded"] { background: linear-gradient(135deg, rgba(40,167,69,0.08), rgba(255,255,255,0.9)); }
    .assignment-card[data-status="late"],
    .assignment-card[data-status="pending"] { background: linear-gradient(135deg, rgba(23,162,184,0.07), rgba(255,255,255,0.95)); }
    .assignment-card[data-status="overdue"] { background: linear-gradient(135deg, rgba(220,53,69,0.08), rgba(255,255,255,0.95)); }

    .assignment-card:hover { transform: translateY(-10px); box-shadow: 0 15px 50px rgba(116,36,169,.2); border-color: var(--primary-color); }

    .assignment-badge { position:absolute; top:15px; right:15px; padding:8px 15px; border-radius:20px; font-size:.85rem; font-weight:600; color:white; }
    .badge-graded { background:#28a745; }
    .badge-late { background:#ffc107; color:#212529; }
    .badge-pending { background:#17a2b8; }
    .badge-overdue { background:#dc3545; }
    .badge-new { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); }

    .assignment-icon { text-align:center; margin: 15px 0 10px; }
    .assignment-icon i { font-size:48px; color: var(--primary-color); }

    .assignment-title { font-size:1.25rem; font-weight:700; color: var(--primary-color); text-align:center; margin-bottom:12px; }

    .assignment-info { display:flex; justify-content:space-around; margin: 18px 0; padding: 12px; background:#f8f9fa; border-radius:12px; }
    .assignment-info-item { text-align:center; }
    .assignment-info-item i { color: var(--secondary-color); margin-bottom:6px; }
    .assignment-info-item span { display:block; color: var(--primary-color); font-weight:600; font-size:0.9rem; }

    .assignment-footer { display:flex; justify-content:space-between; align-items:center; margin-top:18px; gap:10px; }
    .meta { color:#6c757d; font-size:.95rem; }

    .assignment-btn { width: 100%; padding: 12px; border-radius: 10px; font-weight: 600; text-decoration: none; display: block; text-align: center; transition: all 0.3s ease; border: none; cursor: pointer; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; }
    .assignment-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(116,36,169,0.3); color: white; }

    .empty-state { text-align: center; padding: 80px 20px; background: white; border-radius: 20px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08); }
    .empty-state i { font-size: 100px; color: var(--primary-light); margin-bottom: 30px; }
    .empty-state h3 { color: var(--primary-color); margin-bottom: 15px; font-size: 1.8rem; }
</style>

<div class="assignments-section">
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-tasks"></i> الواجبات</h1>
            <a href="{{ route('month.content') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-right"></i> رجوع لمحتوى الشهر</a>
        </div>

        @if($assignments->isEmpty())
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <h3>لا توجد واجبات متاحة حالياً</h3>
                <p class="text-muted mb-0">سيتم إظهار الواجبات الجديدة هنا فور إضافتها</p>
            </div>
        @else
            <div class="assignments-grid">
                @foreach($assignments as $assignment)
                    @php $submission = $assignment->getSubmissionForStudent(Auth::guard('student')->id()); @endphp
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
                    <div class="assignment-card" data-status="{{ $status }}">
                        <div class="assignment-badge {{ $badgeClass }}">{{ $statusLabel }}</div>

                        <div class="assignment-icon"><i class="fas fa-clipboard-check"></i></div>
                        <div class="assignment-title">{{ $assignment->title }}</div>

                        @if($assignment->description)
                            <p class="text-muted text-center">{{ Str::limit($assignment->description, 120) }}</p>
                        @endif

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

                        <div class="assignment-footer">
                            <div class="text-muted">
                                @if($submission && $submission->status == 'graded')
                                    <strong>{{ $submission->marks }}</strong> / {{ $assignment->total_marks }}
                                @elseif(!$submission)
                                    
                                @endif
                            </div>
                            <a href="{{ route('student.assignments.show', $assignment->id) }}" class="assignment-btn">
                                <i class="fas fa-eye"></i> عرض التفاصيل
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@stop
