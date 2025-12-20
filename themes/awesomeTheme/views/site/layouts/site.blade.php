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
    
    <!-- Analytics Tracking Script -->
    @if(config('SITEM_TRACK_VISITORS') == 'Si')
    <script src="{{base_url('public/js/analytics-client.min.js?v=' . ADMIN_VERSION)}}"></script>
    @endif
    
    <script src="{{base_url(getThemePublicPath())}}js/jquery.slim.min.js"></script>
    <script src="{{base_url(getThemePublicPath())}}bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
