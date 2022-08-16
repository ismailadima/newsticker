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
    <input type="hidden" id="isMcr" value="{{$is_mcr}}">
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

        @if($is_mcr)
            <div id="accordion">
                <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                    <button class="btn btn-link" 
                            data-toggle="collapse" data-target="#collapseOne" 
                            aria-expanded="true" aria-controls="collapseOne">
                            > Search Data
                    </button>
                    </h5>
                </div>
            
                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        @include('newsticker._list_data')
                    </div>
                </div>

                </div>
            </div> 
            <br>
            @include('newsticker._latest_data')

        @else 
            @include('newsticker._list_data')
        @endif

    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalContentShow" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog mw-100 w-75" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <textarea name="" readonly class="form-control" id="contentModal" cols="50" rows="30"></textarea>
                </p>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
        </div>
    </div>
@endsection


@section('script')
    <script src="/js/newsticker/index.js?ver={{time()}}"></script>
@endsection