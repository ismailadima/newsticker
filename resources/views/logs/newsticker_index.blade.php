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

        /*timeline*/
        ul.timeline {
            list-style-type: none;
            position: relative;
        }
        ul.timeline:before {
            content: ' ';
            background: #d4d9df;
            display: inline-block;
            position: absolute;
            left: 29px;
            width: 2px;
            height: 100%;
            z-index: 400;
        }
        ul.timeline > li {
            margin: 20px 0;
            padding-left: 20px;
        }
        ul.timeline > li:before {
            content: ' ';
            background: white;
            display: inline-block;
            position: absolute;
            border-radius: 50%;
            border: 3px solid #22c0e8;
            left: 20px;
            width: 20px;
            height: 20px;
            z-index: 400;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        @if (\Session::has('msg'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {!! \Session::get('msg') !!}
            </div>
        @endif


        <!-- Page Heading -->
        <h1 class="h3 mb-4 text-gray-800">Newsticker Logs</h1>

        <div class="row">
            <div class="col-lg-3">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Search</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{route('logs.newstickers.search')}}"
                              method="post" name="form_search" id="form_search">
                            {{ csrf_field() }}

                            @if($is_user_inews)
                            <div class="form-group">
                                <label>Date</label>
                                <input class="form-control datepicker"
                                       name="created_date"
                                       value="{{old('created_date', $request->created_date) }}"
                                       id="created_date">
                            </div>
                            @else 
                            <div class="form-group">
                                <label>Date</label>
                                <input class="form-control datepicker"
                                       name="newsticker_date"
                                       value="{{old('newsticker_date', $request->newsticker_date) }}"
                                       id="newsticker_date">
                            </div>
                            @endif

                            @if(!$is_user_inews)
                            <div class="form-group">
                                <label>Status</label>
                                <select name="list_status" id="list_status" class="form-control">
                                    <option value="">-- ALL --</option>
                                    @foreach ($statuses as $item)
                                        @php
                                        $status_search = !empty($request->list_status) ? $request->list_status : null;
                                        $selected = (!empty($status_search) && $item->id == $status_search) ? " SELECTED " : "";
                                        @endphp
                                        <option {{$selected}} value="{{ $item->id }}">{{ $item->status_name }} </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="form-group">
                                <label>Unit</label>
                                <select name="list_unit" id="list_unit" class="form-control">
                                    @if(session('unit_id_sess') <> '5')
                                        <option value="{{ session('unit_id_sess') }}">{{ session('unit_name_sess') }}</option>
                                    @else 
                                        <option value="">-- ALL --</option>
                                        @foreach ($units as $item)
                                            @php
                                                $status_search = !empty($request->list_unit) ? $request->list_unit : null;
                                                $selected = (!empty($status_search) && $item->id == $status_search) ? " SELECTED " : "";
                                            @endphp
                                        <option {{$selected}} value="{{ $item->id }}">{{ $item->unit_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Category</label>
                                <select name="list_category" id="list_category" class="form-control">
                                    @if(session('unit_id_sess') <> \App\Unit::UNIT_ALL && $is_mcr  == false)
                                    <option value="{{ session('category_id_sess') }}">{{ session('category_name_sess') }}</option>
                                    @else
                                    <option value="">-- ALL --</option>

                                    @foreach ($categories as $item)
                                        @php
                                        $category_search = !empty($request->list_category) ? $request->list_category : null;
                                        $selected = (!empty($category_search) && $item->id == $category_search) ? " SELECTED " : "";
                                        @endphp
                                        <option {{$selected}} value="{{ $item->id }}">{{ $item->category_name }}</option>
                                    @endforeach

                                    @endif
                                </select>
                            </div>

                            <button type="submit" class="btn btn-facebook btn-block">
                                <i class="fa fa-search"></i> Search
                            </button>
                        </form>
                    </div>
                </div>
            </div>


            <div class="col-lg-9">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">List</h6>
                    </div>
                    <div class="card-body">
                        @if(empty($newsticker_data) || count($newsticker_data) < 1)
                            <div class="noDataSign"> == No Data == </div>
                        @else
                
                            <p>{{count($newsticker_data)}} Data Ditemukan </p>
                            @foreach($newsticker_data as $data)
                                @php
                                    $date_log = !empty($data->newsticker_date) ? date('d-M-Y', strtotime($data->newsticker_date)) : date('d-M-Y', strtotime($data->created_at));
                                @endphp
                                <div class="col-xl-12 col-md-12 mb-4">
                                    <div class="card border-left-success shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                        {{ $date_log }}</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data->unit->unit_name }} | {{ $data->category->category_name }} </div>
                                                    <br>
                                                    <div class="h5 mb-0 text-gray-800">Content 1</div>
                                                    <div class="h5 mb-0 text-gray-800 text-xs">{{$data->content}}</div>
                                                    <br>
                                                    <div class="h5 mb-0 text-gray-800">Content 2</div>
                                                    <div class="h5 mb-0 text-gray-800 text-xs">{{$data->content2}}</div>
                                                </div>
                                                <div class="col-auto">
                                                    <button class="btn btn-success viewLogBtn"
                                                            data-id="{{$data->id}}">
                                                        <i class="fa fa-eye"></i> View Log
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal Log--}}
                                <div class="modal fade" tabindex="-1" role="dialog" id="modalLog_{{$data->id}}">
                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Log Newsticker {{ $date_log }}</h5>

                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="container mt-5 mb-5">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <ul class="timeline">
                                                                @php
                                                                    $data_logs = $data->logs->sortByDesc('created_at');
                                                                @endphp
                                                                @foreach ($data_logs as $item)
                                                                    @php
                                                                        $head_caption = "";
                                                                        if($item->action_type == \App\LogNewsticker::STATUS_CREATE){
                                                                            $head_caption = "Created By : ";
                                                                        }else if($item->action_type == \App\LogNewsticker::STATUS_UPDATE){
                                                                            $head_caption = "Updated By : ";
                                                                        }else if($item->action_type == \App\LogNewsticker::STATUS_PUBLISH){
                                                                            $head_caption = "Published By : ";
                                                                        }

                                                                        $head_caption .= !empty($item->user->name) ? $item->user->name : "System";
                                                                        $head_caption .= " (".$item->action_type. ")";

                                                                        $unit_name = !empty($item->unit->unit_name) ? $item->unit->unit_name : "/";
                                                                        $category_name = !empty($item->category->category_name) ? $item->category->category_name : "/";
                                                                    @endphp
                                                                    <li>
                                                                        <a href="#"> {{$head_caption}} </a>
                                                                        <a href="#" class="float-right">{{date('d-M-Y H:i:s', strtotime($item->created_at))}}</a>
                                                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $unit_name }} | {{ $category_name }} </div>
                                                                        <br>
                                                                        <div class="h5 mb-0 text-gray-800">Content 1</div>
                                                                        <div class="h5 mb-0 text-gray-800 text-xs">
                                                                            @if(strlen($item->content) > 50)
                                                                                {{substr($item->content,0,50)}}
                                                                                <span class="read-more-show hide_content">... More<i class="fa fa-angle-down"></i></span>
                                                                                <span class="read-more-content"> {{substr($item->content,50,strlen($item->content))}}
                                                                                <span class="read-more-hide hide_content">Less <i class="fa fa-angle-up"></i></span> </span>
                                                                            @else
                                                                                {{$item->content}}
                                                                            @endif
                                                                        </div>
                                                                        <br>
                                                                        <div class="h5 mb-0 text-gray-800">Content 2</div>
                                                                        <div class="h5 mb-0 text-gray-800 text-xs">
                                                                            @if(strlen($item->content2) > 50)
                                                                                {{substr($item->content2,0,50)}}
                                                                                <span class="read-more-show hide_content">... More<i class="fa fa-angle-down"></i></span>
                                                                                <span class="read-more-content"> {{substr($item->content2,50,strlen($item->content2))}}
                                                                                <span class="read-more-hide hide_content">Less <i class="fa fa-angle-up"></i></span> </span>
                                                                            @else
                                                                                {{$item->content2}}
                                                                            @endif
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                    </div>
                </div>
            </div>


        </div>

    </div>
@endsection


@section('script')
    <script src="/js/logs/newsticker.js?v={{time()}}"></script>
@endsection