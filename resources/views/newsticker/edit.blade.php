@extends('layout.master')

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <!-- Page Heading -->
        {{-- <h1 class="h3 mb-2 text-gray-800">Tables</h1>
        <p class="mb-4">DataTables is a third party plugin that is used to generate the demo table below.
            For more information about DataTables, please visit the <a target="_blank"
                href="https://datatables.net">official DataTables documentation</a>.</p> --}}

        <input type="hidden" name="hdn_newsticker_id" id="hdn_newsticker_id" value="{{ $newsticker->id }}">

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Update Data</h6>
            </div>
            <div class="card-body">
                <form action="/newstickers/store" method="post" name="form_upload" id="form_upload" enctype="multipart/form-data">
                    
                    {{ csrf_field() }}

                    <div class="row form-group">
                        <div class="col-sm-4">
                            <label class="form-label" for="newsticker_date">On Air Date</label>
                        </div>

                        <div class="col-sm-3">
                            @php
                                $arr = explode('-', substr($newsticker->newsticker_date, 0, 10));
                                $newsticker_date = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                                $now = date("d-m-Y");
                                $is_newsticker_today = $now == $newsticker_date ? true : false;
                            @endphp
                            <input type="hidden" id="isNewstickerToday" value="{{$is_newsticker_today}}">
                            <input class="form-control datepicker" name="newsticker_date" id="newsticker_date" value="{{ $newsticker_date }}">
                        </div>

                    </div>

                    <div class="row form-group">
                        <div class="col-sm-4">
                            <label class="form-label" for="category_list">Category</label>
                        </div>

                        <div class="col-sm-8">
                            <select class="form-control" name="category_list" id="category_list">
                                @foreach ($categories as $category)
                                    @php
                                        $selected = '';

                                        if ($category->id == $newsticker->category_id)
                                        {
                                            $selected = 'selected';
                                        }
                                    @endphp

                                    <option value="{{ $category->id }}" {{ $selected }}>{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>                            
                    </div>


                    {{-- <div class="row form-group">
                        <div class="col-sm-12">
                            <div name="file_upload" class="form-group dropzone"></div>
                        </div>
                    </div> --}}

                    <div class="row">
                        <div class="col-sm-12">
                            <nav>
                                <div class="nav nav-tabs justify-content-center" id="nav-tab" role="tablist">
                                    <a class="nav-item nav-link active tabs" id="tab_content" data-toggle="tab" href="#nav-contentsplit" role="tab" aria-controls="nav-contentsplit" aria-selected="true">Content</a>
                                    <a class="nav-item nav-link tabs" id="tab_upload" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Upload</a>
                                    <a class="nav-item nav-link tabs" id="tab_content_all" data-toggle="tab" href="#nav-content-all" role="tab" aria-controls="nav-contennt-all" aria-selected="false">Content View All</a>
                                </div>
                            </nav>
                        </div>
                    </div>

                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-contentsplit" role="tabpanel" aria-labelledby="nav-contentsplit-tab">
                            <div class="mt-3"></div>
                            @include('newsticker._view_newsticker_split')
                            <div class="mb-5"><br></div>
                        </div>

                        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <div class="row form-group" style="margin-top: 20px;">
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
                        </div>

                        <div class="tab-pane fade" id="nav-content-all" role="tabpanel" aria-labelledby="nav-content-all-tab">
                            <div class="row form-group" style="margin-top: 20px;">
                                <div class="col-sm-4">
                                    <label class="form-label" for="category_list">Content</label>
                                </div>

                                <div class="col-sm-8">
                                    <textarea class="form-control" name="txtcontent" id="txtcontent" style="height: 300px;">{{ $newsticker->content }}</textarea>
                                </div>
        
                            </div>
        
                            <div class="row form-group" style="margin-top: 20px; display:none" id="div_content2">
                                <div class="col-sm-4">
                                    <label class="form-label" for="category_list">Content 2</label>
                                </div>

                                <div class="col-sm-8">
                                    <textarea class="form-control" name="txtcontent2" id="txtcontent2" style="height: 300px;">{{ $newsticker->content2 }}</textarea>
                                </div>
        
                            </div>
        
                        </div>
                    </div>



                    


                    <div class="row">
                        <div class="col align-self-center">
                            <a href="{{route('newstickers.index')}}">
                                <button type="button" class="btn btn-danger"> < Kembali</button>
                            </a>
                            <input class="btn btn-primary" type="button" value="Update Data" onclick="update()">
                            <button type="button" style="display:none" class="btn btn-success publishBtn">Publish Now</button>
                        </div>
                    </div>

                </form>

            </div>
        </div>

    </div>
@endsection

@section('script')
    <script src="/js/newsticker/edit.js"></script>
@endsection