@extends('back_layouts.master')

@section('title', 'إصدارات المحتوى')

@section('content')
<div class="page-header-modern">
    <h4><i class="fas fa-code-branch me-2"></i> إصدارات المحتوى - {{ $lecture->title }}</h4>
</div>

<div class="modern-card">
    <h5 class="mb-4"><i class="fas fa-history me-2"></i> تاريخ الإصدارات</h5>
    <div class="table-responsive">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>الإصدار</th>
                    <th>العنوان</th>
                    <th>تاريخ الإنشاء</th>
                    <th>الحالة</th>
                    <th>العمليات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($versions as $version)
                <tr>
                    <td><span class="badge bg-primary">{{ $version['version'] }}</span></td>
                    <td>{{ $version['title'] }}</td>
                    <td>{{ $version['created_at']->format('Y-m-d H:i') }}</td>
                    <td>
                        @if($version['status'] == 'current')
                        <span class="badge bg-success">الحالي</span>
                        @else
                        <span class="badge bg-secondary">قديم</span>
                        @endif
                    </td>
                    <td>
                        @if($version['status'] != 'current')
                        <button class="btn btn-sm btn-primary" onclick="restoreVersion('{{ $version['version'] }}')">
                            <i class="fas fa-undo"></i> استعادة
                        </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('js')
<script>
    function restoreVersion(version) {
        if (confirm('هل أنت متأكد من استعادة هذا الإصدار؟')) {
            // TODO: Implement version restore
            alert('سيتم إضافة هذه الميزة قريباً');
        }
    }
</script>
@endsection




