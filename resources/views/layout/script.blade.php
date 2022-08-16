<!-- Bootstrap core JavaScript-->
<script src="/vendor/jquery/jquery.min.js"></script>
<script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="/vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="/js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->
<script src="/vendor/datatables/jquery.dataTables.min.js"></script>
<script src="/vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="/vendor/dropzone/dist/dropzone.js"></script>
<script src="/vendor/sweetalert2/sweetalert2.all.min.js"></script>
<script src="/vendor/datepicker/bootstrap-datepicker.min.js"></script>
<script src="/vendor/moment/moment.min.js"></script>
<script src="/vendor/jquery-loading-overlay/dist/loadingoverlay.min.js"></script>


<!-- Page level custom scripts -->
<script src="/js/demo/datatables-demo.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script type="text/javascript">
    var autoDelete = '{{\Illuminate\Support\Facades\Auth::user()->is_auto_delete == "Y" ? "Y" : "N"}}'
    var is_mcr = '{{\Illuminate\Support\Facades\Auth::user()->is_mcr == \App\User::IS_MCR ? true : false}}'
    var unitId = '{{\Illuminate\Support\Facades\Auth::user()->unit_id }}'
    var $today = $("#time")
    // console.log(isMcr)
    $('#time').hide()
    if(is_mcr){
        $('#time').show()
    }
        // console.log('mcr')
        
    // if(isMcr == 'Y') {
        
    // }
    function showTime(){
        var myTime = $today.html()
        var ss = myTime.split(":")
        var dt = new Date()
        dt.setHours(ss[0])
        dt.setMinutes(ss[1])
        dt.setSeconds(ss[2])
        var dt2 = new Date(dt.valueOf() + 1000)
        var ts = dt2.toTimeString().split(" ")[0]
        $today.html(ts)
        setTimeout(showTime, 1000)
    }
    setTimeout(showTime, 1000)
</script>

