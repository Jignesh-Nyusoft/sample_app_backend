

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<link rel="shortcut icon" type="image/x-icon" href="{{asset('admin_assets/production/images/favicon.ico')}}"/>
	{{-- <title>Login - {{ get_settings('site_name') ?? 'Site Title' }}</title> --}}
	<!-- =============== VENDOR STYLES ===============-->

	<!-- Bootstrap -->
	<link href="{{asset('admin_assets/vendors/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">
	<!-- Font Awesome -->
	<link href="{{asset('admin_assets/vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
	<!-- NProgress -->
	<link href="{{asset('admin_assets/vendors/nprogress/nprogress.css')}}" rel="stylesheet">
	<!-- Animate.css -->
	<link href="{{asset('admin_assets/vendors/animate.css/animate.min.css')}}" rel="stylesheet">
	<link rel="stylesheet" href="{{asset('admin_assets/build/css/custom.min.css')}}">
	<link rel="stylesheet" href="{{asset('admin_assets/css/toastr.min.css')}}">
</head>

<body class="login">
	<div class="container">
		<div class="login-card">
		  <h2>Login</h2>
		  <form>
			<label for="username">Username:</label>
			<input type="text" id="username" name="username" required>
	  
			<label for="password">Password:</label>
			<input type="password" id="password" name="password" required>
	  
			<button type="submit">Login</button>
		  </form>
		</div>
	  </div>
	<!-- =============== APP SCRIPTS ===============-->
   <script src="{{asset('admin_assets/js/jquery.min.js')}}"></script>
   <script src="{{asset('admin_assets/js/custom.js')}}"></script>
   <script src="{{asset('admin_assets/js/toastr.min.js')}}"></script>
   <script type="text/javascript">
        @if(Session::has('message'))
        var type = "{{ Session::get('alert-type', 'info') }}";
        switch (type) {
            case 'info':
            toastr.info("{{ Session::get('message') }}");
            break;

            case 'warning':
            toastr.warning("{{ Session::get('message') }}");
            break;

            case 'success':
            toastr.success("{{ Session::get('message') }}");
            break;

            case 'error':
            toastr.error("{{ Session::get('message') }}");
            break;
        }
        @endif
    </script>
</body>
</html>
