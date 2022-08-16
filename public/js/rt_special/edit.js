//////////////////////////////////////////// INIT
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
})

var _token = $('#_token').val()

var showPublishBtnAfterDelete = localStorage.getItem("showPublishBtnAfterDelete")

if(showPublishBtnAfterDelete == 'show'){
    $('.publishBtn').show()
    localStorage.removeItem("showPublishBtnAfterDelete")
}

$('.datepicker').datepicker({
    format: 'dd-mm-yyyy'
});


Dropzone.autoDiscover = false;

$('.dropzone').dropzone({
    autoProcessQueue: false,
    url: '/rt_special/store'
});



$('#div_file_upload2').hide();
$('#div_content2').hide();

if($('#category_list').val() == '3')
{
    $('#div_file_upload2').show();
    $('#div_content2').show();
}





//////////////////////////////////////////// EVENT
$('#category_list').change(function() {

    if($('#category_list option:selected').val() == '3')
    {
        $('#div_file_upload2').show();
    }
    else
    {
        $('#div_file_upload2').hide();
    }
})

$('.updateLineBtn').click(function(){
    var newstickerId = $('#hdn_newsticker_id').val()
    let typeContent = $(this).data('type-content')
    let indexData = $(this).data('index-data')
    let contentsArr1 = []
    let contentsArr2 = []

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!'
      }).then((result) => {
        if (result.isConfirmed) {
            if(typeContent == 1){
                $('.contenttext').each(function(k, v){
                    contentsArr1.push($(v).val())
                })
            }else if(typeContent == 2){
                $('.contenttext2').each(function(k, v){
                    contentsArr2.push($(v).val())
                })
            }

            updateLine(contentsArr1, contentsArr2, newstickerId)
            $('.publishBtn').show()
        }
      })
})

$('.publishBtn').click(function(){
    var newstickerId = $('#hdn_newsticker_id').val()
    
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, publish it!'
      }).then((result) => {
        if (result.isConfirmed) {
            rePublishAfterUpdate(newstickerId)
        }
      })
})

$('.deleteLineBtn').click(function(){
    let newstickerId = $('#hdn_newsticker_id').val()
    let typeContent = $(this).data('type-content')
    let indexData = $(this).data('index-data')
    let contentsArr1 = []
    let contentsArr2 = []
    let content = ''

    if(typeContent == 1){
        $('.contenttext').each(function(k, v){
            contentsArr1.push($(v).val())
        })

        content = contentsArr1[indexData]
    }else if(typeContent == 2){
        $('.contenttext2').each(function(k, v){
            contentsArr2.push($(v).val())
        })

        content = contentsArr2[indexData]
    }

    Swal.fire({
        title: 'Are you sure?',
        text: "Harap Publish ulang setelah delete data!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
            deleteLine(newstickerId, indexData, content, typeContent)
            localStorage.setItem("showPublishBtnAfterDelete", "show")
        }
      })

})




//////////////////////////////////////////// EVENT
function update()
{
    var formData = new FormData();
    var category_id = $('#category_list').val();
    var newsticker_id = $('#hdn_newsticker_id').val();
    var newsticker_date = $('#newsticker_date').val();
    var program_tv_name = $('#program_tv_name').val();
    var newsticker_content = '';
    var newsticker_content2 = '';
    var file_upload = '';
    var file_upload2 = '';
    var is_upload = 'N';
    let contentsArr1 = []
    let contentsArr2 = []

    formData.append('_token', _token);
    formData.append('category_id', category_id);
    formData.append('newsticker_date', newsticker_date);
    formData.append('program_tv_name', program_tv_name);
    formData.append('id', newsticker_id);

    // Tab Edit Content Line Active
    if ($('#tab_content').attr('aria-selected') == 'true')
    {
        $('.contenttext').each(function(k, v){
            contentsArr1.push($(v).val())
        })
        $('.contenttext2').each(function(k, v){
            contentsArr2.push($(v).val())
        })

        newsticker_content = JSON.stringify(contentsArr1)
        newsticker_content2 =  JSON.stringify(contentsArr2)
    }
    
    // Tab Edit Content All Active
    if ($('#tab_content_all').attr('aria-selected') == 'true')
    {
        newsticker_content = $('#txtcontent').val();
        newsticker_content2 = $('#txtcontent2').val();
    }

    // Tab Edit Content Upload Active
    if ($('#tab_upload').attr('aria-selected') == 'true')
    {
        file_upload = $('#file_upload')[0].files[0];

        if (category_id == '3')
        {
            file_upload2 = $('#file_upload2')[0].files[0];
        }

        is_upload = 'Y';
    }

    formData.append('content', newsticker_content); 
    formData.append('content2', newsticker_content2); 
    formData.append('file_upload', file_upload);
    formData.append('file_upload2', file_upload2);
    formData.append('is_upload', is_upload);
   
    $.ajax({
        url: '/rt_special/edit/' + newsticker_id,
        data: formData,
        type: 'POST',
        contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
        processData: false, // NEEDED, DON'T OMIT THIS
        error: function(){
            var message = data.responseJSON.message;
            Swal.fire({
                icon: 'info',
                title: 'Information',
                text: message
            })
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
                window.location.assign('/rt_special')
            }
        }
    })


}

function updateLine(contentsArr1, contentsArr2, newstickerId){
    $.ajax({
        url: '/rt_special/update-line/'+newstickerId,
        type: 'POST',
        cache: false,
        data:{
            contentsArr1: contentsArr1,
            contentsArr2: contentsArr2,
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            let errors = XMLHttpRequest.responseText
            Swal.fire({
                icon: 'error',
                title: 'Info',
                text: errors,
            })
        }
    }).done(function (res) {
        if(res.status){
            Swal.fire(
                'Updated!',
                res.message,
                'success'
              )
        }else{
            Swal.fire({
                icon: 'error',
                title: 'Info',
                text: res.message,
            })
        }
    })
}

function rePublishAfterUpdate(newstickerId){
    $.ajax({
        url: '/rt_special/publish',
        data: {
            newsticker_id : newstickerId
        },
        type: 'POST',
        cache: false,
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            let errors = XMLHttpRequest.responseText
            Swal.fire({
                icon: 'error',
                title: 'Info',
                text: errors,
            })
        }
    }).done(function (res) {
        if(res.status == 1){
            Swal.fire(
                'Published!',
                res.message,
                'success'
              )
            $('.publishBtn').hide()
        }else{
            Swal.fire({
                icon: 'error',
                title: 'Info',
                text: res.message,
            })
        }
    })
}

function deleteLine(newstickerId, index, content, typeContent) //typeContent itu konten 1 atau 2
{
    $.ajax({
        url: '/rt_special/delete-line/'+newstickerId,
        type: 'POST',
        data:{
            index: index,
            content: content,
            typeContent: typeContent,
        },
        cache: false,
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            let errors = XMLHttpRequest.responseText
            Swal.fire({
                icon: 'error',
                title: 'Info',
                text: errors,
            })
        }
    }).done(function (res) {
        if(res.status){
            Swal.fire(
                'Deleted!',
                res.message,
                'success'
              )
            window.location.reload()
        }else{
            Swal.fire({
                icon: 'error',
                title: 'Info',
                text: res.message,
            })
        }
    })
}

