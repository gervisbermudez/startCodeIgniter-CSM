<?=doctype('html5')?>
<html lang="en">
	<head>
        @include('admin.shared.head')
    </head>
    <body>
        @include('admin.shared.navbar')
        <div class="main">
        @yield('header')
        @yield('content')
        </div>
        @include('admin.shared.footer')
    </body>
</html>