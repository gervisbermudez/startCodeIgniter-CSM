<?=doctype('html5')?>
<html lang="en">
	<head>
        @include('admin.shared.head')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
        <link href="{{base_url('public/css/admin/start.min.css?v=' . SITEVERSION)}}" rel="stylesheet">
        <script crossorigin src="https://unpkg.com/react@16/umd/react.development.js"></script>
        <script crossorigin src="https://unpkg.com/react-dom@16/umd/react-dom.development.js"></script>
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