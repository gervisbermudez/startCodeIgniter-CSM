<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
@isset($meta)
<?php echo page_meta($meta); ?>
@endisset
<title>{{$title}}</title>
<!-- favicon --> 
<link rel="apple-touch-icon" sizes="180x180" href="{{base_url(getThemePublicPath())}}img/favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="{{base_url(getThemePublicPath())}}img/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="{{base_url(getThemePublicPath())}}img/favicon/favicon-16x16.png">
<link rel="mask-icon" href="{{base_url(getThemePublicPath())}}img/favicon/safari-pinned-tab.svg" color="#5bbad5">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">
<!-- CSS -->
<?php echo link_tag(getThemePublicPath() . 'css/open-iconic-bootstrap.min.css'); ?>
<?php echo link_tag(getThemePublicPath() . 'css/animate.css'); ?>
<?php echo link_tag(getThemePublicPath() . 'css/owl.carousel.min.css'); ?>
<?php echo link_tag(getThemePublicPath() . 'css/owl.theme.default.min.css'); ?>
<?php echo link_tag(getThemePublicPath() . 'css/magnific-popup.css'); ?>
<?php echo link_tag(getThemePublicPath() . 'css/aos.css'); ?>
<?php echo link_tag(getThemePublicPath() . 'css/ionicons.min.css'); ?>
<?php echo link_tag(getThemePublicPath() . 'css/flaticon.css'); ?>
<?php echo link_tag(getThemePublicPath() . 'css/icomoon.css'); ?>
<?php echo link_tag(getThemePublicPath() . 'css/style.css'); ?>
<link rel="alternate" type="application/rss+xml" title="{{config("SITE_TITLE")}} &raquo; Feed" href="{{base_url('feed')}}" />
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
