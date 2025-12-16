<?=doctype('html5')?>
<html lang="{{config('SITE_LANGUAGE')}}">
<head>
    @include('site.shared.head')
    @yield('headers_includes')
</head>
<body>
    @include('site.shared.navbar')
    <div class="main">
        @yield('content')
    </div>
    @include('site.shared.footer')
    @yield('footer_includes')
    <script src="{{base_url(getThemePublicPath())}}js/jquery.slim.min.js"></script>
    <script src="{{base_url(getThemePublicPath())}}bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
