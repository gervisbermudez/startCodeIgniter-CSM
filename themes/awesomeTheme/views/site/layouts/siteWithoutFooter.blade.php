<?=doctype('html5')?>
<html lang="en">
	<head>
        @include('site.shared.head')
        <?php echo analytics_gtag_snippet(); ?>
    </head>
    <body>
        @include('site.shared.navbar')
        <div class="main">
        @yield('content')
        </div>
        <?php echo analytics_client_script(); ?>
    </body>
</html>
