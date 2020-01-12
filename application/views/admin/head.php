<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<title><?php echo "$title"; ?></title>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
		<link href="<?php echo base_url('public/css/admin/start.min.css'); ?>" rel="stylesheet">
		<script type="text/javascript"><?php echo 'var base_url = "' . base_url() . '";'; ?></script>
		<script src="<?php echo base_url('public/js/jquery.js'); ?>"></script>
		<script crossorigin src="https://unpkg.com/react@16/umd/react.development.js"></script>
		<script crossorigin src="https://unpkg.com/react-dom@16/umd/react-dom.development.js"></script>
		<?php
			if (isset($head_includes)) {
				foreach ($head_includes as $value) {
					echo $value;
				}
			}
		?>
	</head>
<body>