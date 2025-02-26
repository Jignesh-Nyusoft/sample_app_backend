<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{asset(App\Helpers\Helper::_get_settings('favicon')) }}"/>
  <title>{{App\Helpers\Helper::_get_settings('meta_title')}}</title>
  <!-- Bootstrap -->
  <link href="{{asset('admin_assets/vendors/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="{{asset('admin_assets/vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
  <!-- NProgress -->
  <link href="{{asset('admin_assets/vendors/nprogress/nprogress.css')}}" rel="stylesheet">
  <!-- bootstrap-wysiwyg -->
  <link href="{{asset('admin_assets/vendors/google-code-prettify/bin/prettify.min.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="{{asset('admin_assets/build/css/custom.min.css')}}">
  <link rel="stylesheet" href="{{asset('admin_assets/css/toastr.min.css')}}">
  <input type="hidden" name="admin_url" value="{{ url('/dashboard') }}" id="admin_url">
  @yield('header_styles')
</head>
