@extends('layout.master')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <!-- Page Heading -->
        {{-- <h1 class="h3 mb-2 text-gray-800">Tables</h1>
        <p class="mb-4">DataTables is a third party plugin that is used to generate the demo table below.
            For more information about DataTables, please visit the <a target="_blank"
                href="https://datatables.net">official DataTables documentation</a>.</p> --}}

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Running Text Special Program</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning alert-dismissible alertExistData" style="display: none" role="alert">
                    <strong> <span id="existDataCount"></span> </strong>
                    {{-- <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> --}}
                </div>

                <form action="/rt_special/store" method="post" name="form_upload" id="form_upload" enctype="multipart/form-data">
                    
                    {{ csrf_field() }}

                    <div class="row form-group">
                        <div class="col-sm-4">
                            <label class="form-label" for="newsticker_date">Date</label>
                        </div>

                        <div class="col-sm-3">
                            <input class="form-control datepicker" name="newsticker_date" id="newsticker_date">
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-sm-4">
                            <label class="form-label" for="category_list">Category</label>
                        </div>

                        <div class="col-sm-8">
                            <select class="form-control" name="category_list" id="category_list">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-sm-4">
                            <label class="form-label" for="type_program">Type Program</label>
                        </div>

                        <div class="col-sm-8">
                            <input type="text" disabled value="{{\App\RunningTextType::TYPE_SPECIAL_PROGRAM_STR}}" class="form-control">
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-sm-4">
                            <label class="form-label" for="program_tv_name">Program Tv Name</label>
                        </div>

                        <div class="col-sm-8">
                            <input type="text" required name="program_tv_name" id="program_tv_name" class="form-control">
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-sm-4">
                            <label class="form-label" for="category_list">File</label>
                        </div>


                        <div class="col-sm-8">
                            <input type="file" class="form-control" name="file_upload" id="file_upload">
                        </div>

                    </div>


                    <div class="row form-group" style="display: none" id="div_file_upload2">
                        <div class="col-sm-4">
                            <label class="form-label" for="category_list">File 2</label>
                        </div>


                        <div class="col-sm-8">
                            <input type="file" class="form-control" name="file_upload2" id="file_upload2">
                        </div>

                    </div>


                    <div class="row">
                        <div class="col align-self-center">
                            <input class="btn btn-primary btn-block mt-3" type="button" value="Upload" onclick="upload()">
                        </div>
                    </div>

                </form>

            </div>
        </div>

    </div>
@endsection

@section('script')
    <script src="/js/rt_special/create.js"></script>
@endsection