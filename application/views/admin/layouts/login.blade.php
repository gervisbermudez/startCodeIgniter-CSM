<?=doctype('html5')?>

<html lang="en">
<head>
    @include('admin.shared.head')
    <link href="{{base_url('public/css/materialize.min.css?v=' . ADMIN_VERSION)}}" rel="stylesheet">
    <link href="{{base_url('public/css/admin/login.min.css?v=' . ADMIN_VERSION)}}" rel="stylesheet">
</head>
<body>
    @yield('content')
    @include('admin.shared.footer_login')
    @yield('footer_includes')
</body>
</html>
