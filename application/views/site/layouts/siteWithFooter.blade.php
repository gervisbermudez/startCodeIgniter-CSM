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
        <footer class="footer">
          <div class="container">
            <p class="text-muted">Place sticky footer content here.</p>
          </div>
        </footer>
    </body>
</html>
