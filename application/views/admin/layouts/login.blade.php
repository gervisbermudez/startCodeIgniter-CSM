<?=doctype('html5')?>

<html lang="en">
	<head>
        @include('admin.shared.head')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
        <link href="{{base_url('public/css/admin/login.min.css?v=' . SITEVERSION)}}" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    </head>
    <body>
        @yield('content')
        @include('admin.shared.footer')
    </body>
</html>