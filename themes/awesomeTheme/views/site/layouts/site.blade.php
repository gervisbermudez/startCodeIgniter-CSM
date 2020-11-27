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
</body>
</html>
