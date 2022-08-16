@extends('layout.master')

@section('content')
    <!--begin::Content-->

    <div class="container-fluid">
        @if (\Session::has('msg'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {!! \Session::get('msg') !!}
            </div>
        @endif

        <!-- Page Heading -->
        <h1 class="h3 mb-4 text-gray-800">User Management</h1>

        <div class="row">

            <div class="col-lg-12">

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">User List</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-primary mb-3 float-right pr-5 pl-5" id="newDataBtn">Add</button>
                            </div>
                        </div>
                
                        <div class="table-responsive">
                            <table class="table table-bordered table-checkable" id="tableUser">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Fullname</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Unit</th>
                                    <th>Category</th>
                                    <th>Is MCR</th>
                                    <th>Is Admin</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach($users as $user)
                                        @php
                                        $status = $user->is_status == "Y" ? "Active" : "Non Active";
                                        @endphp
                                        <tr>
                                            <td>{{$no}}</td>
                                            <td>{{$user->name}}</td>
                                            <td>{{$user->username}}</td>
                                            <td>{{$user->user_email}}</td>
                                            <td>{{$user->unit->unit_name}}</td>
                                            <td>{{!empty($user->category->category_name) ? $user->category->category_name : ''}}</td>
                                            <td>{{$user->is_mcr}}</td>
                                            <td>{{$user->is_mcr}}</td>
                                            <td>
                                                <button class="btn btn-primary font-weight-bold mr-2 editBtn" id="editBtn{{$user->id}}" data-id="{{$user->id}}">Edit </button>
                                                <button class="btn btn-danger font-weight-bold mr-2 deleteBtn" id="deleteBtn{{$user->id}}" data-id="{{$user->id}}">Delete</button>
                                            </td>
                                        </tr>
                                        @php $no++; @endphp
                                    @endforeach
                                </tbody>
                            </table>
                            <!--end: Datatable-->
                        </div>
         
                    </div>
                </div>

            </div>

        </div>

    </div>


    {{--    add modal--}}
    <div class="modal fade" tabindex="-1" role="dialog" id="modalUser">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('acl.user.store')}}" method="POST" id="formCreate">
                        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

                        <div class="form-group">
                            <label>Fullname</label>
                            <input type="text" class="form-control" name="name" required placeholder="Enter Value">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control"  name="user_email" required placeholder="Enter Value">
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" required placeholder="Enter Value">
                        </div>

                        <div class="form-group">
                            <label>Category</label>
                            <select name="category_id" class="form-control">
                                <option value="" selected disabled>== Pilih ==</option>
                                @foreach($categories as $key => $data)
                                    <option value="{{$data->id}}"> {{$data->category_name}} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Unit</label>
                            <select name="unit_id" class="form-control">
                                <option value="" selected disabled>== Pilih ==</option>
                                @foreach($units as $key => $data)
                                    <option value="{{$data->id}}"> {{$data->unit_name}} </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Status</label>
                            <select name="is_active" class="form-control">
                                @foreach($statusses as $key => $status)
                                    <option value="{{$key}}"> {{$status}} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Is MCR ?</label>
                            <select name="is_mcr" class="form-control">
                                <option value="Y">Yes</option>
                                <option selected value="N">No</option>
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{--    edit modal--}}
    <div class="modal fade" tabindex="-1" role="dialog" id="modalUserEdit">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modalUserEditContent"></div>
            </div>
        </div>
    </div>

    <!--end::Content-->
@endsection

@section('script')
    <script src="/js/acl/user/index.js?v=sands{{time()}}"></script>
@endsection