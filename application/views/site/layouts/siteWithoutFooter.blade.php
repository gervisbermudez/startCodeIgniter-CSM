<?=doctype('html5')?>
<html lang="en">
	<head>
        @include('site.shared.head')
    </head>
    <body>
        @include('site.shared.navbar')
        <div class="main">
        @yield('content')
        </div>
    </body>
</html>
