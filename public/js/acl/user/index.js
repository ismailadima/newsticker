"use strict";
var DatatableUser = function() {
    var ajaxCsrfTokenInit = function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    }

    var tableInit = function() {
        var table = $('#tableUser');

        // begin first table
        table.DataTable({
            responsive: true,

            lengthMenu: [5, 10, 25, 50],

            pageLength: 10,

            language: {
                'lengthMenu': 'Display _MENU_',
            },

            // Order settings
            order: [[0, 'asc']],
        });

    };

    var eventsInit = function() {
        $('#newDataBtn').click(function () {
            $('#modalUser').modal('show')
        })

        $('#tableUser tbody').on('click', '.editBtn', function () {
            let id = $(this).data('id')
            $.ajax({
                url: '/acl/user/'+id,
                type: 'GET',
                dataType:'html',
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
                $('.modalUserEditContent').html(res)
                $('#modalUserEdit').modal('show')
                submitUpdateBtn()
            })
        })

        $('#tableUser tbody').on('click', '.deleteBtn', function () {
            let id = $(this).data('id')

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    deleteData(id)
                }
            })
        })

        function submitUpdateBtn(){
            $('.updateBtn').click(function(){
                let form = $('#formUpdate')
                form.submit()
            })
        }

        function deleteData(id){
            $.ajax({
                url: '/acl/user/'+id,
                type: 'DELETE',
                cache: false,
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    let errors = XMLHttpRequest.responseText
                    Swal.fire({
                        icon: 'error',
                        title: 'Info',
                        text: errors
                    })
                }
            }).done(function (res) {
                let status = res.status,
                    message = res.message

                Swal.fire({
                    icon: 'info',
                    title: 'Info',
                    text: message
                })

                if(status){
                    location.reload()
                }
            })
        }
    }

    return {
        //main function to initiate the module
        init: function() {
            ajaxCsrfTokenInit()
            tableInit()
            eventsInit()
        }
    };
}();

jQuery(document).ready(function() {
    DatatableUser.init();
});
