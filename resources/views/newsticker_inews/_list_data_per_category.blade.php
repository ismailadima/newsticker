
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Latest Data Newsticker</h6>
    </div>
    <div class="card-body">
        @if(!empty($newsticker_category))
        @php
        $date_update = !empty($newsticker_category->updated_at) ? $newsticker_category->updated_at : $newsticker_category->created_at;
        @endphp
        <div class="row">
            <div class="col-10">
                <h4>
                    <span class="badge badge-pill badge-xl badge-warning">  {{$user->category->category_name}}</span>
                    <span class="badge badge-pill badge-xl badge-success"> Updated At : {{$date_update}}</span>
                </h4>
                <div class="mt-3">
                    <textarea name="" readonly class="form-control" cols="50" rows="28">{{$newsticker_category->content}}</textarea>
                </div>
            </div>
            <div class="col-2">
                <a href="{{route('newstickers-inews.view.edit', ['newsticker' => $newsticker_category->id])}}">
                    <button type="button" 
                    style="height: 70px"
                    class="btn btn-block btn-primary mt-5">Update {{$user->category->category_name}} </button>
                </a>
            </div>
        </div>
        @else
        <div class="text-center">
            <h3 >Belum Ada Data</h3>
            <img height="520px" src="/img/undraw_dropdown_menu.png" alt="">
        </div>
        @endif

    </div>
</div>
