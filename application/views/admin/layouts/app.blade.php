<?=doctype('html5')?>
<html lang="en">
	<head>
        @include('admin.shared.head')
        <link href="{{base_url('public/css/materialize.min.css?v=' . ADMIN_VERSION)}}" rel="stylesheet">
        <link href="{{base_url('public/css/admin/start.min.css?v=' . ADMIN_VERSION)}}" rel="stylesheet">
        <script>window.BASEURL = "{{base_url('')}}";</script>
        @yield('head_includes')
    </head>
    <body>
        @include('admin.shared.navbar')
        @include('admin.shared.sidenav')
        <div class="main">
        @yield('header')
        @yield('content')
        </div>
        @include('admin.shared.footer')
        @yield('footer_includes')
    </body>
</html>