<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
@isset($meta)
<?php echo page_meta($meta); ?>
@endisset
<title>{{$title}}</title>
<!-- Bootstrap Core CSS -->
<?php echo link_tag('public/bootstrap/css/bootstrap.min.css'); ?>
<!-- Custom CSS -->
<?php echo link_tag('public/css/modern-business.css'); ?>
<!-- Custom Fonts -->
<?php echo link_tag('public/font-awesome/css/font-awesome.min.css'); ?>
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
