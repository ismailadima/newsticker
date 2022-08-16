<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>NEWSTICKER - Login</title>

    <!-- Custom fonts for this template-->
    <link href="/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="/css/sb-admin-2.css" rel="stylesheet">

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9" style="margin-top: 12%">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image">
                                <img src="/img/newsticker-logo.png" 
                                    height="180px" style="margin-top: 2%">
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5 mt-lg-5 mb-lg-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Newsticker</h1>
                                    </div>

                                    @if (\Session::has('msg'))
                                        <div class="alert alert-danger alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                                            {!! \Session::get('msg') !!}
                                        </div>
                                    @endif
        
                                    <form action="/login" method="post" class="user" id="formUser">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" name="username" id="username" placeholder="Enter Username...">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" name="password" id="password" placeholder="Password">
                                        </div>
                                        
                                        {{-- recaptcha google v2 --}}
                                        {{-- <div class="g-recaptcha" data-sitekey="_"></div>
                                        <br/> --}}

                                        <button class="g-recaptcha btn btn-primary btn-user btn-block" 
                                            data-sitekey="{{config('data_config.keys.google_recaptcha_sitekey_v3')}}" 
                                            data-callback='onSubmit' 
                                            data-action='submit'>Login</button>

                                        {{-- <input type="submit" class="btn btn-primary btn-user btn-block" value="Login" /> --}}
                                    </form>
                                    <hr>
                                    {{-- <div class="text-center">
                                        <a class="small" href="register.html">Create an Account!</a>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="/vendor/jquery/jquery.min.js"></script>
    <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="/js/sb-admin-2.min.js"></script>

    <script>
        setTimeout(function(){ $('.grecaptcha-badge').css("right", "14px") }, 3000)

        function onSubmit(token) {
            document.getElementById("formUser").submit();
        }
    </script>

</body>

</html>