{{-- Content 1 --}}
<h3>Content</h3>
<table class="table table-bordered table-hover">
    <thead>
        <th>Content</th>
        <th>Action</th>
    </thead>
    <tbody>
        @foreach($content_split_arr['content'] as $key => $content)
        <tr>
            <td>
                <textarea name="" class="contenttext form-control" id="contenttext-{{$key}}" rows="4">{{$content}}</textarea>
            </td>
            <td width="20%">
                <button type="button" data-index-data="{{$key}}" data-type-content="1" class="btn btn-info mb-2 updateLineBtn"> <i class="fa fa-save"></i> Update Line </button>
                <button type="button" data-index-data="{{$key}}"  data-type-content="1" class="btn btn-danger deleteLineBtn"> <i class="fa fa-trash"></i> Delete Line </button>
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
        <th>Action</th>
    </thead>
    <tbody>
        @foreach($content_split_arr['content2'] as $key => $content)
        <tr>
            <td>
                <textarea name="" class="contenttext2 form-control" id="contenttext2-{{$key}}" rows="4">{{$content}}</textarea>
            </td>
            <td width="20%">
                <button type="button" data-index-data="{{$key}}" data-type-content="2" class="btn btn-info mb-2 updateLineBtn"> <i class="fa fa-save"></i> Update Line </button>
                <button type="button" data-index-data="{{$key}}" data-type-content="2" class="btn btn-danger deleteLineBtn"> <i class="fa fa-trash"></i> Delete Line </button>
            </td>
        </tr>
        @endforeach
  
    </tbody>
</table>
@endif