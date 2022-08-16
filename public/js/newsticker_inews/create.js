////////////////////////////////////// INIT
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

////////////////////////////////////// EVENT







////////////////////////////////////// FUNCTION

function upload()
{
    var formData = new FormData();
    var category_id = $('#category_list').val();
    formData.append('category_id', category_id);

    // Attach file
    formData.append('file_upload', $('#file_upload')[0].files[0]);

    $.ajax({
        url: '/newstickers-inews/store',
        data: formData,
        type: 'POST',
        contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
        processData: false, // NEEDED, DON'T OMIT THIS
        error: function(data){
            
            var message = data.responseJSON.message;

            if (data.responseJSON.file_upload != null)
            {
                message = data.responseJSON.file_upload[0];
            }
            
            if (data.responseJSON.file_upload2 != null)
            {
                message = data.responseJSON.file_upload2[0];
            }
            
            Swal.fire({
                icon: 'info',
                title: 'Information',
                text: message
            });

        },
        success: function(data)
        {
            var status = data.status
            var message = data.message

            Swal.fire({
                icon: 'info',
                title: "Information",
                text: message
            })

            if(status == true){
                localStorage.setItem("tabActive", "list")
                setTimeout(function(){
                    window.location.assign('/newstickers-inews')
                }, 1700);
            }

        }

    })


}


