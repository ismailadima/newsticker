<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Upload Newsticker</h6>
    </div>
    <div class="card-body">
        <div class="alert alert-warning alert-dismissible alertExistData" style="display: none" role="alert">
            <strong> <span id="existDataCount"></span> </strong> 
        </div>

        <div class="text-center">
            <img height="340px" src="/img/undraw_going_up.png" alt="">
        </div>


        <p class="mb-4">
            <b>
                Harap upload file berekstensi .txt , <br>pastikan bahwa konten / text pada file tersebut sudah sesuai dengan format yang ditentukan :)
            </b>
        </p>

        <form>
            <div class="row form-group">
                <div class="col-sm-4">
                    <label class="form-label" for="category_list">Category</label>
                </div>

                <div class="col-sm-8">
                    <select class="form-control" name="category_list" id="category_list">
                        @foreach ($categories as $category)
                            {{-- only in same category with user --}}
                            @if(!empty($category) && $category->id == $user->category_id)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row form-group">
                <div class="col-sm-4">
                    <label class="form-label" for="category_list">File</label>
                </div>


                <div class="col-sm-8">
                    <input type="file" class="form-control" accept=".txt" name="file_upload" id="file_upload">
                </div>

            </div>


            <div class="row">
                <div class="col align-self-center">
                    <input class="btn btn-primary btn-block mt-3" 
                    style="height: 50px"
                    type="button" value="Upload" onclick="upload()">
                </div>
            </div>

        </form>

    </div>
</div>