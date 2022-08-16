<div class="modal-header">
    <h5 class="modal-title">Update User</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <i aria-hidden="true" class="ki ki-close"></i>
    </button>
</div>
<div class="modal-body">
    <form action="{{route('acl.user.update', ['user' => $user->id])}}" method="POST" id="formUpdate">
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

        <div class="form-group">
            <label>Fullname</label>
            <input type="text" class="form-control" name="name" value="{{$user->name}}" required placeholder="Enter Value">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="text" class="form-control" value="{{$user->user_email}}"   name="user_email" required placeholder="Enter Value">
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" class="form-control" value="{{$user->username}}"  name="username" required placeholder="Enter Value">
        </div>

        <div class="form-group">
            <label>Category</label>
            <select name="category_id" class="form-control">
                <option value="" selected disabled>== Pilih ==</option>
                @foreach($categories as $key => $data)
                @php $selected = ($data->id == $user->category_id) ? " SELECTED " : "" @endphp
                    <option {{$selected}} value="{{$data->id}}"> {{$data->category_name}} </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Unit</label>
            <select name="unit_id" class="form-control">
                <option value="" selected disabled>== Pilih ==</option>
                @foreach($units as $key => $data)
                @php $selected = ($data->id == $user->unit_id) ? " SELECTED " : "" @endphp
                    <option {{$selected}} value="{{$data->id}}"> {{$data->unit_name}} </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label>Status</label>
            <select name="is_active" class="form-control">
                @foreach($statusses as $key => $status)
                @php $selected = ($data->id == $user->is_active) ? " SELECTED " : "" @endphp
                    <option {{$selected}} value="{{$key}}"> {{$status}} </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Is MCR ?</label>
            <select name="is_mcr" class="form-control">
                @php 
                    $list_mcr = [
                        'N' => "No",
                        'Y' => "Yes"
                    ];
                @endphp

                @foreach($list_mcr as $key => $data)
                @php $selected = ($key == $user->is_mcr) ? " SELECTED " : "" @endphp
                    <option {{$selected}} value="{{$key}}"> {{$data}} </option>
                @endforeach
            </select>
        </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary updateBtn">Update</button>
    </form>
</div>