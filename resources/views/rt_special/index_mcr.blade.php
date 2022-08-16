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
        <ul class="nav nav-tabs" id="myTab" role="tablist">
             <!-- GTV -->
             @if(!empty($latest_data[1])) 
             <li class="nav-item">
               <a class="nav-link" id="gtv-tab" data-toggle="tab" href="#gtv" role="tab" aria-controls="gtv" aria-selected="false">Latest Newsticker GTV</a>
             </li>
             @endif
 
             <!-- MNCTV -->
             @if(!empty($latest_data[3])) 
             <li class="nav-item">
                 <a class="nav-link" id="mnctv-tab" data-toggle="tab" href="#mnctv" role="tab" aria-controls="mnctv" aria-selected="false">Latest Newsticker MNCTV</a>
             </li>
             @endif
 
              <!-- RCTI -->
              @if(!empty($latest_data[4])) 
              <li class="nav-item">
                  <a class="nav-link" id="rcti-tab" data-toggle="tab" href="#rcti" role="tab" aria-controls="rcti" aria-selected="false">Latest Newsticker RCTI</a>
              </li>
              @endif

        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- GTV -->
            @if(!empty($latest_data[1])) 
            <div class="tab-pane fade" id="gtv" role="tabpanel" aria-labelledby="gtv-tab">
                @php 
                $news = !empty($latest_data[1]['news']) ? $latest_data[1]['news'] : null;
                $infotainment = !empty($latest_data[1]['infotainment']) ? $latest_data[1]['infotainment'] : null;
                $promo1 = !empty($latest_data[1]['promo']['promo1']) ? $latest_data[1]['promo']['promo1'] : null;
                $promo2 = !empty($latest_data[1]['promo']['promo2']) ? $latest_data[1]['promo']['promo2'] : null;
                @endphp

                @if(!empty($news))
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title"><b>News</b></h5>
                    <h6 class="card-subtitle mb-2 text-muted"></h6>
                    <p class="card-text"> {{$news}} </p>
                    {{-- <a href="#" class="card-link">Card link</a> --}}
                    </div>
                </div>
                <br>
                @endif

                @if(!empty($infotainment))
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title"><b>Infotainment</b></h5>
                    <h6 class="card-subtitle mb-2 text-muted"></h6>
                    <p class="card-text"> {{$infotainment}} </p>
                    {{-- <a href="#" class="card-link">Card link</a> --}}
                    </div>
                </div>
                <br>
                @endif

                @if(!empty($promo1))
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title"><b>Promo 1</b></h5>
                    <h6 class="card-subtitle mb-2 text-muted"></h6>
                    <p class="card-text"> {{$promo1}} </p>
                    {{-- <a href="#" class="card-link">Card link</a> --}}
                    </div>
                </div>
                <br>
                @endif

                @if(!empty($promo2))
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title"><b>Promo 2</b></h5>
                    <h6 class="card-subtitle mb-2 text-muted"></h6>
                    <p class="card-text"> {{$promo2}} </p>
                    {{-- <a href="#" class="card-link">Card link</a> --}}
                    </div>
                </div>
                <br>
                @endif
            </div>
            @endif
            

            <!-- MNCTV -->
            @if(!empty($latest_data[3])) 
            <div class="tab-pane fade" id="mnctv" role="tabpanel" aria-labelledby="mnctv-tab">
                @php 
                $news = !empty($latest_data[3]['news']) ? $latest_data[3]['news'] : null;
                $infotainment = !empty($latest_data[3]['infotainment']) ? $latest_data[3]['infotainment'] : null;
                $promo1 = !empty($latest_data[3]['promo']['promo1']) ? $latest_data[3]['promo']['promo1'] : null;
                $promo2 = !empty($latest_data[3]['promo']['promo2']) ? $latest_data[3]['promo']['promo2'] : null;
                @endphp

                @if(!empty($news))
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title"><b>News</b></h5>
                    <h6 class="card-subtitle mb-2 text-muted"></h6>
                    <p class="card-text"> {{$news}} </p>
                    {{-- <a href="#" class="card-link">Card link</a> --}}
                    </div>
                </div>
                <br>
                @endif

                @if(!empty($infotainment))
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title"><b>Infotainment</b></h5>
                    <h6 class="card-subtitle mb-2 text-muted"></h6>
                    <p class="card-text"> {{$infotainment}} </p>
                    {{-- <a href="#" class="card-link">Card link</a> --}}
                    </div>
                </div>
                <br>
                @endif

                @if(!empty($promo1))
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title"><b>Promo 1</b></h5>
                    <h6 class="card-subtitle mb-2 text-muted"></h6>
                    <p class="card-text"> {{$promo1}} </p>
                    {{-- <a href="#" class="card-link">Card link</a> --}}
                    </div>
                </div>
                <br>
                @endif

                @if(!empty($promo2))
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title"><b>Promo 2</b></h5>
                    <h6 class="card-subtitle mb-2 text-muted"></h6>
                    <p class="card-text"> {{$promo2}} </p>
                    {{-- <a href="#" class="card-link">Card link</a> --}}
                    </div>
                </div>
                <br>
                @endif
            </div>
            @endif




            <!-- RCTI -->
            @if(!empty($latest_data[4])) 
            <div class="tab-pane fade" id="rcti" role="tabpanel" aria-labelledby="rcti-tab">
                @php 
                $news = !empty($latest_data[4]['news']) ? $latest_data[4]['news'] : null;
                $infotainment = !empty($latest_data[4]['infotainment']) ? $latest_data[4]['infotainment'] : null;
                $promo1 = !empty($latest_data[4]['promo']['promo1']) ? $latest_data[4]['promo']['promo1'] : null;
                $promo2 = !empty($latest_data[4]['promo']['promo2']) ? $latest_data[4]['promo']['promo2'] : null;
                @endphp

                @if(!empty($news))
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title"><b>News</b></h5>
                    <h6 class="card-subtitle mb-2 text-muted"></h6>
                    <p class="card-text"> {{$news}} </p>
                    {{-- <a href="#" class="card-link">Card link</a> --}}
                    </div>
                </div>
                <br>
                @endif

                @if(!empty($infotainment))
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title"><b>Infotainment</b></h5>
                    <h6 class="card-subtitle mb-2 text-muted"></h6>
                    <p class="card-text"> {{$infotainment}} </p>
                    {{-- <a href="#" class="card-link">Card link</a> --}}
                    </div>
                </div>
                <br>
                @endif

                @if(!empty($promo1))
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title"><b>Promo 1</b></h5>
                    <h6 class="card-subtitle mb-2 text-muted"></h6>
                    <p class="card-text"> {{$promo1}} </p>
                    {{-- <a href="#" class="card-link">Card link</a> --}}
                    </div>
                </div>
                <br>
                @endif

                @if(!empty($promo2))
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title"><b>Promo 2</b></h5>
                    <h6 class="card-subtitle mb-2 text-muted"></h6>
                    <p class="card-text"> {{$promo2}} </p>
                    {{-- <a href="#" class="card-link">Card link</a> --}}
                    </div>
                </div>
                <br>
                @endif
            </div>
            @endif


            {{-- Start --}}
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="jumbotron jumbotron-fluid">
                    <div class="container">
                        <h1 class="display-4">Latest Data Newsticker</h1>
                        <p class="lead">Pilih Tab diatas untuk melihat data newsticker yang terpublish</p>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection


@section('script')
    <script src="/js/newsticker/index.js?ver={{time()}}"></script>
@endsection