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
        @if (\Session::has('msg'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                {!! \Session::get('msg') !!}
            </div>
        @endif

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">INEWS Newsticker</h1>
        </div>

        @if(!$is_mcr)
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link" id="upload-tab" data-toggle="tab" href="#upload-content" role="tab" aria-controls="upload" aria-selected="true">
                    <span class="material-icons">upload_file</span> Upload
                </a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" id="latestdata-tab" data-toggle="tab" href="#latestdata-content" role="tab" aria-controls="latestdata" aria-selected="false">
                <span class="material-icons">bubble_chart</span> Latest Data
              </a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade" id="upload-content" role="tabpanel" aria-labelledby="upload-tab">
               @include('newsticker_inews._upload')
            </div>
            <div class="tab-pane fade show active" id="latestdata-content" role="tabpanel" aria-labelledby="latestdata-tab">
               @include('newsticker_inews._list_data_per_category')
            </div>
        </div>
        @else  
            @include('newsticker_inews._latest_data')
        @endif

    </div>
@endsection


@section('script')
    <script src="/js/newsticker_inews/index.js?ver=sands{{time()}}"></script>
    <script src="/js/newsticker_inews/create.js?ver=sands{{time()}}"></script>
@endsection