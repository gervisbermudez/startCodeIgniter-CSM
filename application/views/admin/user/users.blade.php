@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@include('admin.shared.header')
@endsection

@section('content')
<div class="container">
	<div class="row">
		<div class="col s12">
			<div class="row" id="root" >
				<div class="col s12 center" v-bind:class="{ hide: !loader }">
					<div class="preloader-wrapper big active">
						<div class="spinner-layer spinner-blue-only">
						  <div class="circle-clipper left">
							<div class="circle"></div>
						  </div><div class="gap-patch">
							<div class="circle"></div>
						  </div><div class="circle-clipper right">
							<div class="circle"></div>
						  </div>
						</div>
					  </div>
				</div>
				<div class="col s12 m4" v-cloak v-if="!loader"  v-for="user in users">
					<user-card :user="user"/>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="fixed-action-btn" style="bottom: 45px; right: 24px;">
	<a class="btn-floating btn-large red waves-effect waves-teal btn-flat new tooltipped" data-position="left"
		data-delay="50" data-tooltip="Crear Usuario" href="{{base_url('admin/usuarios/agregar/')}} ">
		<i class="large material-icons">add</i>
	</a>
</div>
<script type="text/x-template" id="user-card-template">
    <div class="card user-card">
		<div class="card-image">
			<div class="card-image-container">
				<img v-if="user.user_data.avatar"
					:src="getAvatarUrl()">
				<img v-else src="https://materializecss.com/images/sample-1.jpg" />
			</div>
			<span class="card-title">@{{user.user_data.nombre + ' ' + user.user_data.apellido}}</span>
				<a
				class="btn-floating halfway-fab waves-effect waves-light"
				:href="getUserUrl()">
				<i class="material-icons">visibility</i>
			</a>
		</div>
		<div class="card-content">
			<div>
				<div class="card-info"><i class="material-icons">account_box</i> @{{user.role}}<br></div>
				<div class="card-info"><i class="material-icons">access_time</i> @{{user.lastseen}}<br>
				</div>
				<div class="card-info"><i class="material-icons">local_phone</i> @{{user.user_data.telefono}}</div>
			</div>
		</div>
	</div>
</script>
@endsection