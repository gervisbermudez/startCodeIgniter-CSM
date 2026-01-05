@extends('admin.layouts.app')
@section('title', $title)
@section('header')
@include('admin.shared.header')
@endsection
@section('content')
<div class="container">
	<div class="row">
		<div class="col s12">
			<?php
			if (isset($conten)) {
				echo $conten;
			}
			?>
		</div>
	</div>
</div>
@endsection