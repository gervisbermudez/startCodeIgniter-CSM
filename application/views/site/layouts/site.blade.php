<?=doctype('html5')?>
<html lang="en">
	<head>
        @include('site.shared.head')
        @yield('header_includes')
    </head>
    <body>
        @include('site.shared.navbar')
        <div class="main">
        @yield('content')
        </div>
        @include('site.shared.footer')
        <!-- Bootstrap core JavaScript -->
        <script src="public/jquery/jquery.min.js"></script>
        <script src="public/bootstrap/js/bootstrap.bundle.min.js"></script>
        @yield('footer_includes')
    </body>
</html>