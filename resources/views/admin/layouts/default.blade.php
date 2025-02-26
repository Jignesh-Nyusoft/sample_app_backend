<!DOCTYPE html>
<html lang="en">
@include('admin.layouts.header')
<body class="nav-md">
    <div class="section-container message_sec" style="height: auto;padding: 0 10px;">
        @include('admin.includes.flash-message')
    </div>
    @include('admin.layouts.topnavbar')
    @include('admin.layouts.sidebar')
    @yield('content')
    @include('admin.layouts.footer')
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
