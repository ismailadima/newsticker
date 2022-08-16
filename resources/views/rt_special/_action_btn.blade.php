{{--UNTUK MCR VIEW--}}
{{-- @if($is_mcr) --}}

    {{-- @if($item->status_id == \App\Newsticker::STATUS_PUBLISH)
        <span class="badge badge-pill badge-success">PUBLISH</span>
    @endif
    @if($item->status_id == \App\Newsticker::STATUS_NONAKTIF)
        <span class="badge badge-pill badge-danger">NON ACTIVE</span>
    @endif --}}

{{-- @else --}}
    {{--UNTUK NON MCR VIEW--}}
    @if($item->status_id != \App\Newsticker::STATUS_NONAKTIF)
        <input type="button" class="btn btn-primary" name="btn_edit" id="btn_edit"
                data-id="{{ $item->id }}"
                value="Edit"
                onclick="window.location.assign('/rt_special/edit/{{ $item->id }}')">

        @php
        $now = date("Y-m-d");
        $is_date_newsticker_exp = $item->newsticker_date == $now ? false : true;

        $value_publish = 'Publish Now';
        $disabled_publish = '';
        $class_color_btn_publish = ' btn-success ';

        if ($item->status_id == \App\Newsticker::STATUS_PUBLISH)
        {
            $value_publish = 'Published';
            $disabled_publish = 'disabled';
            $class_color_btn_publish = ' btn-outline-success ';
        }

        if($is_date_newsticker_exp ){
            $value_publish = 'Publish Disabled';
            $disabled_publish = 'disabled';
            $class_color_btn_publish = ' btn-outline-danger ';
        }
        @endphp

        <br>
        <input type="button"
            class="btn mt-1 btn_publish {{$class_color_btn_publish}}"
            name="btn_publish"
            id="btn_publish{{ $item->id }}" {{ $disabled_publish }}
            data-id="{{ $item->id }}"
            value="{{ $value_publish }}">

    @else 
        <span class="badge badge-pill badge-danger">NON ACTIVE</span>
    @endif


    {{--Button non aktif newsticker--}}
    @if($item->status_id != \App\Newsticker::STATUS_PUBLISH && $item->status_id != \App\Newsticker::STATUS_NONAKTIF)
        <br>
        <button
                class="nonactiveBtn btn btn-danger mt-1"
                data-id="{{ $item->id }}">
            Non Active
        </button>
    @endif


{{-- @endif --}}
