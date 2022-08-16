////////////////////////////////////// INIT
var _token = $('#_token').val();

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ajaxStart(function(){
    $.LoadingOverlay("show");
});
$(document).ajaxStop(function(){
    $.LoadingOverlay("hide");
});

if($('#newsticker_date').val() == '')
{
    $('.datepicker').val(moment().format('DD-MM-yyyy'));
}

$('.datepicker').datepicker({
    autoclose: true,
    format: 'dd-mm-yyyy',
    todayBtn: 'linked',
    todayHighlight: true
});





////////////////////////////////////// EVENT

// Hide the extra content initially, using JS so that if JS is disabled, no problemo:
$('.read-more-content').addClass('hide_content')
$('.read-more-show, .read-more-hide').removeClass('hide_content')

// Set up the toggle effect:
$('.read-more-show').on('click', function(e) {
    $(this).next('.read-more-content').removeClass('hide_content');
    $(this).addClass('hide_content');
    e.preventDefault();
});

// Changes contributed by @diego-rzg
$('.read-more-hide').on('click', function(e) {
    var p = $(this).parent('.read-more-content');
    p.addClass('hide_content');
    p.prev('.read-more-show').removeClass('hide_content'); // Hide only the preceding "Read More"
    e.preventDefault();
});


$('.btn_publish').click(function(){

    var newsticker_id = $(this).attr('data-id');

    // return false;

    $.ajax({
        url: '/newstickers/publish',
        data: {
            newsticker_id : newsticker_id,
            _token: _token
        },
        type: 'POST',
        error: function(){
            alert('aaa');
        },
        success: function(data)
        {
            // console.log(data);

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
                    var message = 'Publishing Success';

                    $('.btn_publish').prop('disabled', false);
                    $('#btn_publish'+newsticker_id).prop('disabled', true);
                }

                if (status == '2')
                {
                    var icon = 'error';
                    var title = 'Error';
                    var message = 'Data cant be found';
                }

                Swal.fire({
                    icon: icon,
                    title: title,
                    text: message
                });

                window.location.assign('/newstickers')
            }



        }

    });
});

$('.nonactiveBtn').click(function(){
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Non Active!'
    }).then((result) => {
        if (result.isConfirmed) {
            let id = $(this).data('id')
            nonactivAction(id)

            Swal.fire(
                'Non Actived!',
                'Your data has been non actived.',
                'success'
            )
        }
    })
})


////////////////////////////////////// FUNCTION
function publish()
{
    $.ajax({
        url: '/newstickers/publish',
        data: {
            newsticker_id : newsticker_id,
            _token: _token
        },
        type: 'POST',
        error: function(){
            alert('aaa');
        },
        success: function(data)
        {
            console.log(data);

            var status = data.status;

            if(status != '')
            {
                var icon = '';
                var title = '';
                var message = '';

                if (status == '1')
                {
                    icon = 'success';
                    title = 'Success';
                    message = 'Publishing Success';
                }

                if (status == '0')
                {
                    icon = 'error';
                    title = 'Error';
                    message = 'Data cant be found';
                }

                Swal.fire({
                    icon: icon,
                    title: title,
                    text: message
                });

                $(this).prop('disabled', true);

                location.reload()
            }



        }

    });
}


function publish_today()
{
    var formData = new FormData();

    $.ajax({
        url: '/newstickers/publish_today',
        data: {
            _token: _token
        },
        type: 'POST',
        error: function(){
            alert('aaa');
        },
        success: function(data)
        {
            console.log(data);

            // return false;

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
                    var message = 'Publishing Success';
                }

                if (status == '0')
                {
                    var icon = 'info';
                    var title = 'Information';
                    var message = 'Data cant be found';
                }

                Swal.fire({
                    icon: icon,
                    title: title,
                    text: message
                });

                $(this).prop('disabled', true);

                location.reload()
            }



        }

    });
}

function nonactivAction(id = null){
    $.ajax({
        url: '/newstickers/nonactive-data/'+id,
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
        location.reload()
    })
}
