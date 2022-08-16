{{-- Content 1 --}}
<h3>Content</h3>
<table class="table table-bordered table-hover">
    <thead>
        <th>Content</th>
        <th class="deleteLineClass">Time Deleted</th>
        <th>Action</th>
    </thead>
    <tbody>
        @foreach($content_split_arr['content'] as $key => $content)
        <tr>
            <td>
                <textarea name="" class="contenttext form-control" id="contenttext-{{$key}}" rows="4">{{$content}}</textarea>
            </td>

            {{-- DELETE LINE HIDE --}}
            {{-- <td width="30%" class="deleteLineClass">
                <table align="center" style="border: none !important;">
                    <tr>
                        @php
                            $hour = '';
                            $minute = '';
                            $disable = 'disabled';
                            $idDeleted = '';
                            if(!empty($deleted_id['content1'][$key])){
                                // dd($deleted_id['content1'][$key]['id']);
                                $idDeleted = $deleted_id['content1'][$key]['id'];
                            }
                            // dd($idDeleted)
                            if(!empty($deleted_id['content1'][$key]['time_deleted'])){
                                $hour = date('H',strtotime($deleted_id['content1'][$key]['time_deleted']));
                                $minute = date('i',strtotime($deleted_id['content1'][$key]['time_deleted']));
                                $disable = '';
                            }
                        @endphp
                        <td>
                        <input type="number"  class="hourDeleted" id="hour-{{$key}}" min="0" max="23" value="{{$hour}}" {{$disable}}> : 
                            <input type="number" class="minuteDeleted" id="minute-{{$key}}" min="0" max="59" value="{{$minute}}" {{$disable}}>
                        </td>
                        <td>
                            <button type="button" data-index-data="{{$key}}" data-type-content="1" class="btn btn-success mb-2 enableTimeBtn"> <i class="fa fa-stopwatch"> Set</i></button>
                            <button type="button" data-index-data="{{$key}}" data-type-content="1" class="btn btn-warning mb-2 disableTimeBtn"> <i class="fa fa-times"> Clear</i></button>
                        </td>
                    </tr>
                </table>
            </td> --}}

            <td width="20%">
                <button type="button" 
                    data-index-data="{{$key}}" 
                    {{-- data-id-table="{{$idDeleted}}"  --}}
                    data-type-content="1" class="btn btn-info mb-2 updateLineBtn"> <i class="fa fa-save"></i> Update Line </button>
                <button type="button" 
                    data-index-data="{{$key}}"  
                    {{-- data-id-table="{{$idDeleted}}"  --}}
                    data-type-content="1" class="btn btn-danger mb-2 deleteLineBtn"> <i class="fa fa-trash"></i> Delete Line </button>
            </td>
        </tr>
        @endforeach
  
    </tbody>
</table>


{{-- Content 2 --}}
@if(count($content_split_arr['content2']) > 0)
<br>
<hr>
<br>
<h3>Content 2 </h3>
<table class="table table-bordered table-hover">
    <thead>
        <th>Content</th>
        <th class="deleteLineClass">Time Deleted</th>
        <th>Action</th>
    </thead>
    <tbody>
        @foreach($content_split_arr['content2'] as $key => $content)
        <tr>
            <td>
                <textarea name="" class="contenttext2 form-control" id="contenttext2-{{$key}}" rows="4">{{$content}}</textarea>
            </td>

            {{-- DELETE LINE HIDE --}}
            {{-- <td width="30%" class="deleteLineClass">
                    <table align="center" style="border: none !important;">
                        <tr>
                                @php
                                $hour = '';
                                $minute = '';
                                $disable = 'disabled';
                                $idDeleted = '';
                                if(!empty($deleted_id['content2'])){
                                    $idDeleted = $deleted_id['content2'][$key]['id'];
                                }
                                // dd($idDeleted);
                                if(!empty($deleted_id['content2'][$key]['time_deleted'])){
                                    $hour = date('H',strtotime($deleted_id['content2'][$key]['time_deleted']));
                                    $minute = date('i',strtotime($deleted_id['content2'][$key]['time_deleted']));
                                    $disable = '';
                                }
                            @endphp
                            <td>
                            <input type="number"  class="hourDeleted2" id="hour2-{{$key}}" min="0" max="23" value="{{$hour}}" {{$disable}}> : 
                                <input type="number" class="minuteDeleted2" id="minute2-{{$key}}" min="0" max="59" value="{{$minute}}" {{$disable}}>
                            </td>
                            <td>
                                <button type="button" data-index-data="{{$key}}" data-type-content="2" class="btn btn-success mb-2 enableTimeBtn"> <i class="fa fa-stopwatch"> Set</i></button>
                                <button type="button" data-index-data="{{$key}}" data-type-content="2" class="btn btn-warning mb-2 disableTimeBtn"> <i class="fa fa-times"> Clear</i></button>
                            </td>
                        </tr>
                    </table>
                </td> --}}

            <td width="20%">
                <button type="button" data-index-data="{{$key}}" 
                    {{-- data-id-table="{{$idDeleted}}"  --}}
                    data-type-content="2" class="btn btn-info mb-2 updateLineBtn"> <i class="fa fa-save"></i> Update Line </button>
                <button type="button" data-index-data="{{$key}}" 
                    {{-- data-id-table="{{$idDeleted}}" --}}
                    data-type-content="2" class="btn btn-danger deleteLineBtn"> <i class="fa fa-trash"></i> Delete Line </button>
            </td>
        </tr>
        @endforeach
  
    </tbody>
</table>
@endif