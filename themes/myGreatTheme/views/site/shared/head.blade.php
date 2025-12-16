<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
@isset($meta)
<?php echo page_meta($meta); ?>
@endisset
<title>{{$title}}</title>
<link rel="alternate" type="application/rss+xml" title="{{config("SITE_TITLE")}} &raquo; Feed" href="{{base_url('feed')}}" />
<!-- Materialize Core CSS -->
<?php echo link_tag(getThemePublicPath() . 'css/materialize.css'); ?>
<!-- Custom CSS -->
<?php echo link_tag(getThemePublicPath() . 'css/style.css'); ?>
<!-- Custom Fonts -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
@section('headers_includes')
@isset($headers_includes)
<?php echo $headers_includes ?>
@endisset
@endsection
