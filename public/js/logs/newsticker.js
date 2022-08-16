"use strict";

var LogNewstickerScript = function() {
    var ajaxCsrfTokenInit = function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    }

    var startInit = function() {
        if($('#newsticker_date').val() == '' || $('#created_date').val() == '')
        {
            $('.datepicker').val(moment().format('DD-MM-yyyy'))
        }

        $('.datepicker').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy',
            todayBtn: 'linked',
            todayHighlight: true
        })

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
    }

    var eventsInit = function() {
        $('.viewLogBtn').click(function(){
            let id  = $(this).data('id')
            let idName = "#modalLog_"+id
            $(idName).modal('show')
        })
    }

    return {
        //main function to initiate the module
        init: function() {
            ajaxCsrfTokenInit()
            startInit()
            eventsInit()
        }
    };
}();

jQuery(document).ready(function() {
    LogNewstickerScript.init();
});
