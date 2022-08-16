////////////////////////////////////// INIT
var _token = $('#_token').val();

Dropzone.autoDiscover = false;

$('.datepicker').val(moment().format('DD-MM-yyyy'));

$('.datepicker').datepicker({
    format: 'dd-mm-yyyy',
    todayBtn: 'linked',
    todayHighlight: true
});




$('.dropzone').dropzone({
    autoProcessQueue: false,
    url: '/rt_special/store'
});


if ($('#hdn_status').val() != undefined)
{
    var icon = '';
    var title = '';
    var message = '';

    if ($('#hdn_status').val() == '1')
    {
        var icon = 'success';
        var title = 'Success';
        var message = 'Upload Success';
    }

    if ($('#hdn_status').val() == '2')
    {
        var icon = 'error';
        var title = 'Error';
        var message = 'File Empty';
    }

    Swal.fire({
        icon: icon,
        title: title,
        text: message
    });

}


$('#div_file_upload2').hide();
$('#div_content2').hide();

if($('#category_list').val() == '3')
{
    $('#div_file_upload2').show();
    $('#div_content2').show();
}





////////////////////////////////////// EVENT

$('#category_list').change(function() {

    if($('#category_list option:selected').val() == '3')
    {
        $('#div_file_upload2').show();
    }

});

$('#newsticker_date').change(function(){
    $.ajax({
        url: '/rt_special/check-data-exist-date/'+$(this).val(),
        type: 'GET',
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
        let count = res.data.count
        console.log("COUNT : ", count)
        let text = (count > 0) ? count+" Data sudah diinput pada tanggal tersebut" : "Belum ada data yang diinput pada tanggal tersebut"
        $('#existDataCount').text(text)
        $('.alertExistData').show()
    })
})





////////////////////////////////////// FUNCTION

function upload()
{
    var formData = new FormData();
    var category_id = $('#category_list').val();
    var newsticker_date = $('#newsticker_date').val();
    var program_tv_name = $('#program_tv_name').val();
    
    formData.append('_token', _token);
    formData.append('category_id', category_id);
    formData.append('newsticker_date', newsticker_date);
    formData.append('program_tv_name', program_tv_name);

    // Attach file
    formData.append('file_upload', $('#file_upload')[0].files[0]);

    if (category_id == '3')
    {
        formData.append('file_upload2', $('#file_upload2')[0].files[0]);
    }
    

    $.ajax({
        url: '/rt_special/store',
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
                window.location.assign('/rt_special')
            }
           
        }

    });


}


