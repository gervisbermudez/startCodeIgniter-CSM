<?=doctype('html5')?>
<html lang="en">
	<head>
        @include('admin.shared.head')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
        <link href="{{base_url('public/css/admin/start.min.css?v=' . ADMIN_VERSION)}}" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
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