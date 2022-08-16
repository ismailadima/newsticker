@extends('layout.master')

@section('style')
    <style type="text/css">
        .read-more-show{
            cursor:pointer;
            color: #ed8323;
        }
        .read-more-hide{
            cursor:pointer;
            color: #ed8323;
        }

        .hide_content{
            display: none;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <!-- Page Heading -->
        {{-- <h1 class="h3 mb-2 text-gray-800">NEWSTICKERS</h1>
        <p class="mb-4">DataTables is a third party plugin that is used to generate the demo table below.
            For more information about DataTables, please visit the <a target="_blank"
                href="https://datatables.net">official DataTables documentation</a>.</p> --}}

        <!-- DataTales Example -->

        <div class="card">

        </div>

        <!-- Collapsable Card -->
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse"
               role="button" aria-expanded="true" aria-controls="collapseCardExample">
                <h6 class="m-0 font-weight-bold text-primary">Running Text Special Filter</h6>
            </a>
            <!-- Card Content - Collapse -->
            <div class="collapse show" id="collapseCardExample">
                <div class="card-body">
                    <form action="/rt_special" method="post" name="form_search" id="form_search">

                        {{ csrf_field() }}

                        <div class="row form-group">
                            <div class="col-sm-1">
                                <label class="form-label" for="newsticker_date">Date</label>
                            </div>

                            <div class="col-sm-2">
                                <input class="form-control datepicker"
                                       name="newsticker_date"
                                       id="newsticker_date"
                                       value="{{ $newsticker_date_selected }}">
                            </div>

                            <div class="col-sm-1 ml-3">
                                <label class="form-label" for="list_status">Status</label>
                            </div>

                            <div class="col-sm-2">

                                <select name="list_status" id="list_status" class="form-control">
                                    <option value="">-- ALL --</option>
                                    @foreach ($statuses as $item)
                                        @php
                                            $selected = $status_selected == $item->id ? " SELECTED " : "";
                                        @endphp

                                        <option {{$selected}} value="{{ $item->id }}">{{ $item->status_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-1 ml-3">
                                <label class="form-label" for="list_unit">Unit</label>
                            </div>
                
                            <div class="col-sm-2">
                                <select name="list_unit" id="list_unit" class="form-control">
                                    @if(session('unit_id_sess') <> \App\Unit::UNIT_ALL && $is_mcr  == false)
                                        <option value="{{ session('unit_id_sess') }}">{{ session('unit_name_sess') }}</option>
                                    @else 
                                        <option value="">-- ALL --</option>
                                        @foreach ($units as $item)
                                            @php 
                                                $selected = ($item->id == $unit_selected) ? " SELECTED " : "";
                                            @endphp
                                            <option {{$selected}} value="{{ $item->id }}">{{ $item->unit_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                        </div>

                        <div class="row form-group">
                            <div class="col-sm-1">
                                <label class="form-label" for="list_category">Category</label>
                            </div>

                            <div class="col-sm-2">
                                <select name="list_category" id="list_category" class="form-control">
                                    @if(session('unit_id_sess') <> \App\Unit::UNIT_ALL && $is_mcr  == false)
                                    <option value="{{ session('category_id_sess') }}">{{ session('category_name_sess') }}</option>
                                    @else
                                    <option value="">-- ALL --</option>
                                    @foreach ($categories as $item)
                                        @php 
                                            $selected = ($item->id == $category_selected) ? " SELECTED " : "";
                                        @endphp
                                        <option {{$selected}} value="{{ $item->id }}">{{ $item->category_name }}</option>
                                    @endforeach
                                    
                                    @endif
                                </select>
                            </div>
                            
                            <div class="col-sm-2"></div>


                            <div class="col-sm-2">
                                <input type="submit" class="btn btn-primary btn-block" name="btn_search" id="btn_search" value="Search">
                            </div>

                            <div class="col-sm-2">
                                <input type="reset" class="btn btn-secondary btn-block" name="btn_reset" id="btn_reset" value="Reset">
                            </div>

                        </div>
                    </form>

                    <hr>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Running Text Special List</h6>
            </div>
            <div class="card-body">
                <div class="row form-group">
                    @if($is_mcr == false)
                    <a href="/rt_special/create" class="btn btn-primary btn-icon-split btn-primary ml-2">
                                        <span class="icon text-white-50">
                                            <i class="fa fa-plus"></i>
                                        </span>
                        <span class="text" style="padding-left: 60px; padding-right: 60px">Create</span>
                    </a>
                    @endif
                </div>
                
                <br>

                <div class="table-responsive">

                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Unit Name</th>
                                <th>Program Name</th>
                                <th>Category</th>
                                <th style="width: 9%">Date</th>
                                <th style="width: 30%">Content</th>
                                @if($is_promo || $is_mcr)
                                <th style="width: 30%">Content2</th>
                                @endif
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($newstickers as $item)
                                @php
                                    //$arr = explode('-', substr($item->newsticker_date, 0, 10));
                                    //$newsticker_date = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                                    $newsticker_date = date("d-m-Y" ,strtotime($item->newsticker_date));
                                    $is_nonaktif = $item->status_id == \App\Newsticker::STATUS_NONAKTIF ? true : false;
                                    $color_tr = "";
                                    if($is_nonaktif){
                                       $color_tr = "#ffe227";
                                    }

                                    $is_publish = $item->status_id == \App\Newsticker::STATUS_PUBLISH ? true : false;
                                    if($is_publish && $is_mcr){
                                       $color_tr = "#cdfffc";
                                    }
                                @endphp

                                <tr style="background-color: {{$color_tr}}">
                                    <td>{{ $item->unit->unit_name }}</td>
                                    <td>{{ $item->program_tv_name }}</td>
                                    <td>{{ $item->category->category_name }}</td>
                                    <td>{{ $newsticker_date }}</td>
                                    <td>
                                        @if(strlen($item->content) > 50)
                                            {{substr($item->content,0,50)}}<span class="read-more-show hide_content">... More<i class="fa fa-angle-down"></i></span><span class="read-more-content">{{substr($item->content,50,strlen($item->content))}}
                                            <span class="read-more-hide hide_content">Less <i class="fa fa-angle-up"></i></span> </span>
                                        @else
                                            {{$item->content}}
                                        @endif
                                    </td>
                                    
                                    @if($is_promo || $is_mcr)
                                    <td>
                                        @if(strlen($item->content2) > 50)
                                            {{substr($item->content2,0,50)}}<span class="read-more-show hide_content">... More<i class="fa fa-angle-down"></i></span><span class="read-more-content">{{substr($item->content2,50,strlen($item->content2))}}
                                            <span class="read-more-hide hide_content">Less <i class="fa fa-angle-up"></i></span> </span>
                                        @else
                                            {{$item->content2}}
                                        @endif
                                    </td>
                                    @endif

                                    <td>
                                        @include('rt_special._action_btn')
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection


@section('script')
    <script src="/js/rt_special/index.js?ver={{time()}}"></script>
@endsection