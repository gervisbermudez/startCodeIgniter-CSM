<?=doctype('html5')?>
<html lang="en">
	<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="apple-touch-icon" sizes="57x57" href="{{base_url('public/img/admin/favicon')}}/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="{{base_url('public/img/admin/favicon')}}/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="{{base_url('public/img/admin/favicon')}}/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="{{base_url('public/img/admin/favicon')}}/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="{{base_url('public/img/admin/favicon')}}/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="{{base_url('public/img/admin/favicon')}}/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="{{base_url('public/img/admin/favicon')}}/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="{{base_url('public/img/admin/favicon')}}/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="{{base_url('public/img/admin/favicon')}}/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="{{base_url('public/img/admin/favicon')}}/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="{{base_url('public/img/admin/favicon')}}/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="{{base_url('public/img/admin/favicon')}}/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="{{base_url('public/img/admin/favicon')}}/favicon-16x16.png">
        <link rel="manifest" href="{{base_url('public/img/admin/favicon')}}/manifest.json{{'?v=' . SITEVERSION}}">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="{{base_url('public/img/admin/favicon')}}/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
        <title>@yield('title')</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
        <link href="{{base_url('public/css/admin/login.min.css?v=' . SITEVERSION)}}" rel="stylesheet">
    </head>
    <body>
        @yield('content')
        @include('admin.shared.footer')
    </body>
</html>