<!DOCTYPE html>
<html lang="en">

	<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="description" content="newsticker">
		<meta name="author" content="MNC Developer">
		<meta name="csrf-token" content="{{ csrf_token() }}" />

		{{--Favicon--}}
		<link rel="apple-touch-icon" sizes="57x57" href="/img/favicon/apple-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="/img/favicon/apple-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="/img/favicon/apple-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="/img/favicon/apple-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="/img/favicon/apple-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="/img/favicon/apple-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="/img/favicon/apple-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="/img/favicon/apple-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="/img/favicon/apple-icon-180x180.png">
		<link rel="icon" type="image/png" sizes="192x192"  href="/img/favicon/android-icon-192x192.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/img/favicon/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="/img/favicon/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/img/favicon/favicon-16x16.png">
		<link rel="manifest" href="/img/favicon/manifest.json">
		<meta name="msapplication-TileColor" content="#ffffff">
		<meta name="msapplication-TileImage" content="/img/favicon/ms-icon-144x144.png">
		<meta name="theme-color" content="#ffffff">

		<title>NEWSTICKER</title>

		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
		
		<!-- Custom fonts for this template -->
		<link href="/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
		<link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
        
		<!-- Custom styles for this template -->
		<link href="/css/sb-admin-2.min.css" rel="stylesheet">
        
		<!-- Custom styles for this page -->
		<link href="/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
		<link href="/vendor/datepicker/bootstrap-datepicker.min.css" rel="stylesheet">
        
        <link href="/vendor/dropzone/dist/dropzone.css" rel="stylesheet" type="text/css">
		<style>
			@font-face {
			font-family: 'ds-digital';
			src: url('/css/DS-DIGIB.TTF');
			}
		</style>
		@yield('style')

	</head>

	<body id="page-top">
		<input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
		
		<!-- Page Wrapper -->
		<div id="wrapper">

			<!-- Sidebar -->
            @include('layout.sidebar')
			<!-- End of Sidebar -->

			<!-- Content Wrapper -->
			<div id="content-wrapper" class="d-flex flex-column">

				<!-- Main Content -->
				<div id="content">

					<!-- Topbar -->
					@include('layout.topbar')
					<!-- End of Topbar -->

					<!-- Begin Page Content -->
					@yield('content')
					<!-- /.container-fluid -->

				</div>
				<!-- End of Main Content -->

				<!-- Footer -->
				<footer class="sticky-footer bg-white">
					<div class="container my-auto">
						<div class="copyright text-center my-auto">
							<span>Copyright &copy; Newsticker {{date('Y')}}</span>
						</div>
					</div>
				</footer>
				<!-- End of Footer -->

			</div>
			<!-- End of Content Wrapper -->

		</div>
		<!-- End of Page Wrapper -->

		<!-- Scroll to Top Button-->
		<a class="scroll-to-top rounded" href="#page-top">
			<i class="fas fa-angle-up"></i>
		</a>

		<!-- Logout Modal-->
		<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
			aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
						<button class="close" type="button" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
					<div class="modal-footer">
						<button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
						<a class="btn btn-primary" href="/logout">Logout</a>
					</div>
				</div>
			</div>
        </div>

        <!-- Script-->
        @include('layout.script')
        <!-- End of Script-->

        @yield('script')
	</body>

</html>