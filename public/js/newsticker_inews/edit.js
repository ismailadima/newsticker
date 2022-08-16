//////////////////////////////////////////// INIT
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
})


var _token = $('#_token').val()
var isNewstickerToday = $('#isNewstickerToday').val()

$('.datepicker').datepicker({
    format: 'dd-mm-yyyy'
});

$('.deleteLineClass').hide();

$('#div_file_upload2').hide();
$('#div_content2').hide();

// if($('#category_list').val() == '3')
// {
//     $('#div_file_upload2').show();
//     $('#div_content2').show();
// }

if(autoDelete == 'Y'){
    $('.deleteLineClass').show()    
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

$('.enableTimeBtn').click(function(){
    let indexData = $(this).data('index-data')
    var today = new Date()
    let typeContent = $(this).data('type-content')
    if(typeContent == 1){
        $('#hour-'+indexData).val(today.getHours())
        $('#minute-'+indexData).val(today.getMinutes())
        $('#hour-'+indexData).prop('disabled',false)
        $('#minute-'+indexData).prop('disabled',false)
    } else {
        $('#hour2-'+indexData).val(today.getHours())
        $('#minute2-'+indexData).val(today.getMinutes())
        $('#hour2-'+indexData).prop('disabled',false)
        $('#minute2-'+indexData).prop('disabled',false)
    }
})

$('.disableTimeBtn').click(function(){
    let indexData = $(this).data('index-data')
    let typeContent = $(this).data('type-content')
    if(typeContent == 1){
        $('#hour-'+indexData).val('')
        $('#minute-'+indexData).val('')
        $('#hour-'+indexData).prop('disabled',true)
        $('#minute-'+indexData).prop('disabled',true)
    } else {
        $('#hour2-'+indexData).val('')
        $('#minute2-'+indexData).val('')
        $('#hour2-'+indexData).prop('disabled',true)
        $('#minute2-'+indexData).prop('disabled',true)
    }
})

$('.btnUpdate').on('click', function(){
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
                update()
            }
      }) 
})

$('.updateLineBtn').click(function(){
    var newstickerId = $('#hdn_newsticker_id').val()
    let typeContent = $(this).data('type-content')
    let indexData = $(this).data('index-data')
    let idTableDelete = $(this).data('id-table')
    let contentsArr1 = []
    let contentsArr2 = []
    var timeDeletedValue = null
    var statusError = 0;

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
                var isDisabled = $('#hour-'+indexData).prop('disabled')
                if(isDisabled == false){
                    let hourTemp = $('#hour-'+indexData).val()
                    let minuteTemp = $('#minute-'+indexData).val()
                    if(hourTemp == '' || minuteTemp == '' || hourTemp > 23 || minuteTemp > 59){
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Please input time deleted correctly or clear time deleted input!'
                        })
                        statusError = 1
                    }
                    timeDeletedValue = hourTemp+':'+minuteTemp+':00'
                }
            }else if(typeContent == 2){
                $('.contenttext2').each(function(k, v){
                    contentsArr2.push($(v).val())
                })
                var isDisabled = $('#hour2-'+indexData).prop('disabled')
                if(isDisabled == false){
                    let hourTemp = $('#hour2-'+indexData).val()
                    let minuteTemp = $('#minute2-'+indexData).val()
                    if(hourTemp == '' || minuteTemp == '' || hourTemp > 23 || minuteTemp > 59){
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Please input time deleted correctly or clear time deleted input!'
                        })
                        statusError = 1
                    }
                    timeDeletedValue = hourTemp+':'+minuteTemp+':00'
                }
            }

            if(statusError > 0){
                return false
            }
            updateLine(contentsArr1, contentsArr2, newstickerId, indexData, typeContent, idTableDelete, timeDeletedValue)

        }
      })
})


$('.deleteLineBtn').click(function(){
    let newstickerId = $('#hdn_newsticker_id').val()
    let typeContent = $(this).data('type-content')
    let indexData = $(this).data('index-data')
    let idDeleted = $(this).data('id-table')
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
        // text: "Harap Publish ulang setelah delete data!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
            deleteLine(newstickerId, indexData, content, typeContent,idDeleted)
        }
      })

})




//////////////////////////////////////////// EVENT
function update()
{
    var formData = new FormData();
    var category_id = $('#category_list').val();
    var newsticker_id = $('#hdn_newsticker_id').val();
    var newsticker_content = '';
    var newsticker_content2 = '';
    var time_deleted1 = '';
    var time_deleted2 = '';
    var file_upload = '';
    var file_upload2 = '';
    var is_upload = 'N';
    let contentsArr1 = []
    let contentsArr2 = []
    let timeDeletedArr1 = []
    let timeDeletedArr2 = []
    var statusError = 0;

    formData.append('_token', _token);
    formData.append('category_id', category_id);
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

        $('.hourDeleted').each(function(k,v){
            var isDisabled = $('#hour-'+k).prop('disabled')
            // console.log(k)
            if(isDisabled == false){
                let hourTemp = $('#hour-'+k).val()
                let minuteTemp = $('#minute-'+k).val()
                if(hourTemp == '' || minuteTemp == '' || hourTemp > 23 || minuteTemp > 59){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please input time deleted correctly or clear time deleted input!'
                    })
                    statusError = 1
                }
                let tempTime = hourTemp+':'+minuteTemp+':00'
                timeDeletedArr1.push(tempTime)
            } else {
                timeDeletedArr1.push(null)
            }
        })

        $('.hourDeleted2').each(function(k,v){
            var isDisabled = $('#hour2-'+k).prop('disabled')
            if(isDisabled == false){
                let hourTemp = $('#hour2-'+k).val()
                let minuteTemp = $('#minute2-'+k).val()
                if(hourTemp == '' || minuteTemp == '' || hourTemp > 23 || minuteTemp > 59){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please input time deleted correctly or clear time deleted input!'
                    })
                    statusError = 1
                }
                let tempTime = hourTemp+':'+minuteTemp+':00'
                timeDeletedArr2.push(tempTime)
            } else {
                timeDeletedArr2.push(null)
            }
        })

        time_deleted1 = JSON.stringify(timeDeletedArr1)
        time_deleted2 = JSON.stringify(timeDeletedArr2)
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
    formData.append('timeDeleted1',time_deleted1)
    formData.append('timeDeleted2',time_deleted2)
    formData.append('file_upload', file_upload);
    formData.append('file_upload2', file_upload2);
    formData.append('is_upload', is_upload);

    if(statusError > 0){
        return false
    }

    $.ajax({
        url: '/newstickers-inews/edit/' + newsticker_id,
        data: formData,
        type: 'POST',
        contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
        processData: false, // NEEDED, DON'T OMIT THIS
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            let errors = XMLHttpRequest.responseText
            Swal.fire({
                icon: 'error',
                title: 'Info',
                text: errors,
            })
        },
        success: function(data)
        {
            var status = data.status;

            if(status != '')
            {
                var icon = '';
                var title = '';
                var message = '';

                if (status == '1')
                {
                    var icon = 'success';
                    var title = 'Success';
                    var message = 'Upload Success';
                }

                if (status == '2')
                {
                    var icon = 'error';
                    var title = 'Error';
                    var message = 'File Empty';
                }

                Swal.fire({
                    icon: icon,
                    title: title,
                    text: message
                })

                if (status == '1')
                {
                    window.location.assign('/newstickers-inews')
                }

            }
        }
    })


}

function updateLine(contentsArr1, contentsArr2, newstickerId, indexData, typeContent, idTableDelete, timeDeletedValue){
    $.ajax({
        url: '/newstickers-inews/update-line/'+newstickerId,
        type: 'POST',
        cache: false,
        data:{
            contentsArr1: contentsArr1,
            contentsArr2: contentsArr2,
            idTableDelete: idTableDelete,
            timeDeletedValue: timeDeletedValue,
            index: indexData,
            typeContent: typeContent
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
            
              setTimeout(() => {
                location.reload()
              }, 1200);
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
        url: '/newstickers/publish',
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
        }else{
            Swal.fire({
                icon: 'error',
                title: 'Info',
                text: res.message,
            })
        }
    })
}

function deleteLine(newstickerId, index, content, typeContent, idDeleted) //typeContent itu konten 1 atau 2
{
    $.ajax({
        url: '/newstickers-inews/delete-line/'+newstickerId,
        type: 'POST',
        data:{
            index: index,
            content: content,
            typeContent: typeContent,
            idDeleted: idDeleted,
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
            
            setTimeout(() => {
                location.reload()
            }, 1200);
        }else{
            Swal.fire({
                icon: 'error',
                title: 'Info',
                text: res.message,
            })
        }
    })
}

