@inject('UnitModel', 'App\Unit')
@php
    $user_auth = \Illuminate\Support\Facades\Auth::user();
    //Role MCR ?
    $is_mcr = $user_auth->is_mcr == \App\User::IS_MCR ? true : false;
    //IS ADMIN
    $is_admin = $user_auth->is_admin == \App\User::IS_ADMIN ? true : false;
    //Unit
    $unit_id_user = $user_auth->unit_id;
    $unit_inews = $UnitModel::UNIT_INEWS;

    //menu check
    $newsticker_list = '';
    $newsticker_upload = '';
    $rt_special_list = '';
    $rt_special_upload = '';
    $logs_newsticker = '';
    $newsticker_inews_menu = '';
    
    //menu check admin
    $admin_user = '';

    //parent menu open
    $open_newsticker_collapse = '';
    $open_rt_special_collapse = '';
    $open_logs_collapse = '';
    $open_admin = '';

    switch ( Route::currentRouteName() ){
        case "newstickers.index":
        $newsticker_list = "active";
        $open_newsticker_collapse = "show";
        break;

        case "newstickers.view.create":
        $newsticker_upload = "active";
        $open_newsticker_collapse = "show";
        break;

        case "rt_special.index":
        $rt_special_list = "active";
        $open_rt_special_collapse = "show";
        break;

        case "rt_special.view.create":
        $rt_special_upload = "active";
        $open_rt_special_collapse = "show";
        break;

        case "logs.newstickers.index":
        $logs_newsticker = "active";
        $open_logs_collapse = "show";
        break;

        case "acl.user":
        $admin_user = "active";
        $open_admin = "show";
        break;

        case "newstickers-inews.index":
        $newsticker_inews_menu = "active";
        break;
    }

@endphp
<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/">
{{--        <div class="sidebar-brand-icon rotate-n-15">--}}
{{--            <i class="fas fa-laugh-wink"></i>--}}
{{--        </div>--}}
{{--        <div class="sidebar-brand-text mx-3">NEWSTICKER</div>--}}
        <img src="/img/newsticker-logo.png" alt="newstickermnc" height="120px">
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    {{-- <li class="nav-item">
        <a class="nav-link" href="index.html">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Home</span>
        </a>
        <a class="nav-link" href="index.html">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li> --}}

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Menus
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    @if($unit_id_user == $unit_inews)
    <li class="{{$newsticker_inews_menu}} nav-item">
        <a class="nav-link {{$newsticker_inews_menu}}" href="{{route('newstickers-inews.index')}}">
            <i class="fa fa-newspaper"></i>
            <span>Inews Newsticker</span>
        </a>
    </li>
    @else
    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseNewsticker"
            aria-expanded="true" aria-controls="collapseNewsticker">
            <i class="fa fa-newspaper"></i>
            <span>Running Text</span>
        </a>

        <div id="collapseNewsticker" class="collapse {{$open_newsticker_collapse}}" aria-labelledby="headingNewsticker" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">RT Menu:</h6>
                @if($is_mcr == false)
                    <a class="collapse-item {{ $newsticker_upload }}" href="/newstickers/create">Upload</a>
                @endif

                <a class="collapse-item {{ $newsticker_list }}" href="/newstickers">List</a>
            </div>
        </div>
    </li>
    @endif


    {{-- sementara di hide untuk fitur ini status PENDING--}}
    {{-- <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseRtSpecial"
            aria-expanded="true" aria-controls="collapseRtSpecial">
            <i class="fa fa-newspaper"></i>
            <span>Running Text Special</span>
        </a>

        <div id="collapseRtSpecial" class="collapse {{$open_rt_special_collapse}}" aria-labelledby="headingNewsticker" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">RT Special Program Menu:</h6>
                @if($is_mcr == false)
                    <a class="collapse-item {{ $rt_special_upload }}" href="/rt_special/create">Upload</a>
                @endif

                <a class="collapse-item {{ $rt_special_list }}" href="/rt_special">List</a>
            </div>
        </div>
    </li> --}}

    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseLogs"
           aria-expanded="true" aria-controls="collapseLogs">
            <i class="fa fa-archive"></i>
            <span>Logs</span>
        </a>

        <div id="collapseLogs" class="collapse {{$open_logs_collapse}}" aria-labelledby="headingLogs" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Logs:</h6>
                <a class="collapse-item {{ $logs_newsticker }}" href="/logs/newstickers">Running Text Logs</a>
            </div>
        </div>

    </li>

    @if($is_admin)
    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseAdmin"
           aria-expanded="true" aria-controls="collapseAdmin">
            <i class="fa fa-archive"></i>
            <span>Administrator</span>
        </a>

        <div id="collapseAdmin" class="collapse {{$open_admin}}" aria-labelledby="headingLogs" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Admin Menu:</h6>
                <a class="collapse-item {{ $admin_user }}" href="{{route('acl.user')}}">User</a>
            </div>
        </div>
    </li>
    @endif


{{-- 
    <!-- Heading -->
    <div class="sidebar-heading">
        Interface
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
            aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-cog"></i>
            <span>Components</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Custom Components:</h6>
                <a class="collapse-item" href="buttons.html">Buttons</a>
                <a class="collapse-item" href="cards.html">Cards</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Utilities Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
            aria-expanded="true" aria-controls="collapseUtilities">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Utilities</span>
        </a>
        <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Custom Utilities:</h6>
                <a class="collapse-item" href="utilities-color.html">Colors</a>
                <a class="collapse-item" href="utilities-border.html">Borders</a>
                <a class="collapse-item" href="utilities-animation.html">Animations</a>
                <a class="collapse-item" href="utilities-other.html">Other</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Addons
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
            aria-expanded="true" aria-controls="collapsePages">
            <i class="fas fa-fw fa-folder"></i>
            <span>Pages</span>
        </a>
        <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Login Screens:</h6>
                <a class="collapse-item" href="login.html">Login</a>
                <a class="collapse-item" href="register.html">Register</a>
                <a class="collapse-item" href="forgot-password.html">Forgot Password</a>
                <div class="collapse-divider"></div>
                <h6 class="collapse-header">Other Pages:</h6>
                <a class="collapse-item" href="404.html">404 Page</a>
                <a class="collapse-item" href="blank.html">Blank Page</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Charts -->
    <li class="nav-item">
        <a class="nav-link" href="charts.html">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Charts</span></a>
    </li>

    <!-- Nav Item - Tables -->
    <li class="nav-item active">
        <a class="nav-link" href="tables.html">
            <i class="fas fa-fw fa-table"></i>
            <span>Tables</span></a>
    </li> --}}

    <!-- Divider -->
   <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
    <div class="d-none d-md-inline">
        <span id="time" style="position:fixed;bottom:10px;font-family:'ds-digital';font-weight: bold;font-size: 65px;color: #ffff00f6;">{{date('H:i:s')}}</span>
    </div>
</ul>
<!-- End of Sidebar -->