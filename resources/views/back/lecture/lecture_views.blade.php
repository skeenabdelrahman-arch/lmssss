<h2>الطلبة اللي شافوا المحاضرة: {{ $lecture->title }}</h2>

<table border="1" cellpadding="10">
    <tr>
        <th>اسم الطالب</th>
        <th>شاهد المحاضرة؟</th>
    </tr>
    @foreach ($students as $student)
        @php
            $viewed = \App\Models\LectureView::where('student_id', $student->id)
                ->where('lecture_id', $lecture->id)
                ->exists();
        @endphp
        <tr>
            <td>{{ $student->name }}</td>
            <td>{{ $viewed ? '✅ شاهد' : '❌ لم يشاهد' }}</td>
        </tr>
    @endforeach
</table>
