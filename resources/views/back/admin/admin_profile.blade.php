@extends('back_layouts.master')
@section('css')

@section('title')
    بروفايل الادمن
@stop
@endsection
@section('page-header')
<!-- breadcrumb -->
<div class="page-title">
    <div class="row">
        <div class="col-sm-6">
            <h4 class="mb-0"> بروفايل الادمن </h4>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb pt-0 pr-0 float-left float-sm-right ">
                <li class="breadcrumb-item"><a href="{{url('/')}}" class="default-color">لوحة التحكم</a></li>
                <li class="breadcrumb-item active">بروفايل الادمن</li>
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

                <br> <br>
                <div class="table-responsive">
                    <table id="datatable" class="table  table-hover table-sm table-bordered p-0" data-page-length="50"
                        style="text-align: center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th> الاسم</th>
                                <th>الايميل</th>
                                <th>العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>{{ $admin->name }}</td>
                                    <td>{{ $admin->email }}</td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                            data-target="#edit{{$admin->id}}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <a onclick="return confirm('هل انت متاكد من الحذف ؟ ')" href="{{url('admin/delete/'.$admin->id)}}" class="btn btn-danger btn-sm" ><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                {{-- edit modal  --}}
                                <div class="modal fade" id="edit{{$admin->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title" id="exampleModalLabel">
                                                تعديل بيانات الادمن
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">

                                            <form class=" row mb-30" action="{{route('admin_profile_update',$admin->id)}}" method="POST">
                                                @csrf
                                                <div class="card-body">
                                                    <div class="repeater">
                                                        <div>
                                                            <div>
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <label for="Name"
                                                                            class="mr-sm-2">الاسم
                                                                            :</label>
                                                                        <input class="form-control" type="text" name="name"  value="{{$admin->name}}" required/>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label for="Name"
                                                                            class="mr-sm-2">الايميل
                                                                            :</label>
                                                                        <input class="form-control" type="email" name="email"  value="{{$admin->email}}" required/>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label for="Name"
                                                                            class="mr-sm-2">كلمة السر
                                                                            :</label>
                                                                        <input class="form-control" type="password" name="password"  placeholder="اتركها فارغة ان لم ترغب في تغييرها"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">الغاء</button>
                                                            <button type="submit"
                                                                class="btn btn-success">تعديل</button>
                                                        </div>


                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- row closed -->
@endsection
@section('js')

@endsection
