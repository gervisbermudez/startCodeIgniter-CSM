<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<?php $meta = array(
		array('name' => 'Content-type','content' => 'text/html; charset=utf-8', 'type' => 'equiv'),
		array('name' => 'viewport','content' => 'width=device-width, initial-scale=1'),
		array('name' => 'robots','content' => 'no-cache'),
		array('name' => 'description','content' => 'My Great Site'),
		array('name' => 'keywords','content' => 'love, passion, intrigue, deception')
		);
		echo meta($meta);
		?>
		<title><?php echo $title; ?></title>
		<!-- Bootstrap Core CSS -->
		<?php echo link_tag('css/bootstrap.min.css'); ?>
		<!-- Custom CSS -->
		<?php echo link_tag('css/modern-business.css'); ?>
		<!-- Custom Fonts -->
		<?php echo link_tag('font-awesome/css/font-awesome.min.css'); ?>
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
<body>