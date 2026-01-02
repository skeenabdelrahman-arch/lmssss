@extends('back_layouts.master')
@section('css')

@section('title')
    نتائج الامتحان العام: {{ $exam->exam_title }}
@stop
@endsection
@section('page-header')
<!-- breadcrumb -->
<div class="page-title">
    <div class="row">
        <div class="col-sm-6">
            <h4 class="mb-0">نتائج الامتحان العام: {{ $exam->exam_title }}</h4>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb pt-0 pr-0 float-left float-sm-right ">
                <li class="breadcrumb-item"><a href="{{url('/')}}" class="default-color">لوحة التحكم</a></li>
                <li class="breadcrumb-item"><a href="{{ route('publicExam.results') }}" class="default-color">الامتحانات العامة</a></li>
                <li class="breadcrumb-item active">نتائج الامتحان</li>
            </ol>
        </div>
    </div>
</div>
<!-- breadcrumb -->
@endsection
@section('content')
<!-- row -->
<div class="row">
    <div class="col-md-12 mb-30">
        <div class="card card-statistics h-100">
            <div class="card-body">

                @if(session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>{{ session()->get('error') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @elseif(session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>{{ session()->get('success') }}</strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="mb-3">
                    <a href="{{ route('publicExam.results') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-right"></i> العودة للقائمة
                    </a>
                    <a href="{{ route('publicExam.take', $exam->id) }}" target="_blank" class="btn btn-success">
                        <i class="fa fa-link"></i> رابط الامتحان
                    </a>
                </div>

                <div class="alert alert-info">
                    <h5>معلومات الامتحان:</h5>
                    <p class="mb-0">
                        <strong>العنوان:</strong> {{ $exam->exam_title }}<br>
                        <strong>الوصف:</strong> {{ $exam->exam_description ?? 'لا يوجد وصف' }}<br>
                        <strong>وقت الامتحان:</strong> {{ $exam->exam_time }} دقيقة<br>
                        <strong>عدد المتقدمين:</strong> {{ $results->count() }}
                    </p>
                </div>

                <br>
                <div class="table-responsive">
                    <table id="datatable" class="table table-hover table-sm table-bordered p-0" data-page-length="50"
                        style="text-align: center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم المتقدم</th>
                                <th>الدرجة</th>
                                <th>الدرجة الكلية</th>
                                <th>النسبة المئوية</th>
                                <th>التاريخ والوقت</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($results as $result)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $result->student_name }}</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $result->student_degree >= ($result->total_degree / 2) ? 'success' : 'danger' }}">
                                            {{ $result->student_degree }}
                                        </span>
                                    </td>
                                    <td>{{ $result->total_degree }}</td>
                                    <td>
                                        <span class="badge badge-{{ $result->percentage >= 50 ? 'success' : 'warning' }}">
                                            {{ $result->percentage }}%
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($result->created_at)->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <p class="text-muted">لا توجد نتائج لهذا الامتحان بعد</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($results->count() > 0)
                <div class="mt-4">
                    <h5>إحصائيات:</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6>عدد المتقدمين</h6>
                                    <h3>{{ $results->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6>متوسط الدرجات</h6>
                                    <h3>{{ number_format($results->avg('student_degree'), 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6>متوسط النسبة</h6>
                                    <h3>{{ number_format($results->avg('percentage'), 2) }}%</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6>أعلى درجة</h6>
                                    <h3>{{ $results->max('student_degree') }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- row closed -->
@endsection
@section('js')

@endsection

