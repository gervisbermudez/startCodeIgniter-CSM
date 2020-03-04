@extends('admin.layouts.app')

@section('title', $title)

@section('header')
@include('admin.shared.header')
@endsection

@section('content')
<div class="container" id="root">
	<div class="row">
		<div class="col s12 center" v-bind:class="{ hide: !loader }">
			<div class="preloader-wrapper big active">
				<div class="spinner-layer spinner-blue-only">
					<div class="circle-clipper left">
						<div class="circle"></div>
					</div>
					<div class="gap-patch">
						<div class="circle"></div>
					</div>
					<div class="circle-clipper right">
						<div class="circle"></div>
					</div>
				</div>
			</div>
		</div>
		<div v-cloak v-if="!loader" class="col s12 m10 l10">
			<div id="initialization" class="section scrollspy">
				<h4 class="section-header">Perfil</h4>
				<div class="input-field">
					<input maxlength="25" type="text" id="username" name="username" value="" required="required"
						v-model="username" @blur="validateField('username');" :class="{
							valid: validFields.username, 
							invalid: !validFields.username
						}" autocomplete="off">
					<label data-error="Username usado" data-success="Username valido">Username</label>
				</div>
				<div class="input-field ">
					<input maxlength="25" id="password" name="password" value="" required="required" type="password"
						v-model="password">
					<label>Contraseña</label>
				</div>
				<div class="input-field">
					<input id="email" type="email" name="email" value="" required="required" maxlength="255"
						v-model="email" @blur="validateField('email');" :class="{
							valid: validFields.email, 
							invalid: !validFields.email
						}" autocomplete="off">
					<label data-error="Email usado" data-success="Valido">Email</label>
				</div>
				<div class="input-field">
					<select name="usergroup_id" v-model="usergroup_id">
						<option v-for="(item, index) in usergroups" :key="index" :value="item.usergroup_id">
							@{{item.name}}</option>
					</select>
					<label>Tipo de usuario:</label>
				</div>
			</div>
			<div id="structure" class="section scrollspy">
				<h4 class="section-header">Información de Perfil</h4>
				<div class="input-field">
					<input maxlength="20" id="nombre" type="text" name="nombre" value=""
						@blur="validateField('nombre');" :class="{
							valid: validFields.nombre, 
							invalid: !validFields.nombre
						}" v-model="user_data.nombre">
					<label>Nombre:</label>
				</div>
				<div class="input-field">
					<input maxlength="20" type="text" id="apellido" name="apellido" value=""
						@blur="validateField('apellido');" :class="{
							valid: validFields.apellido, 
							invalid: !validFields.apellido
						}" v-model="user_data.apellido"><label for="apellido">Apellido:</label>
				</div>
				<div class="input-field">

					<input maxlength="200" type="text" id="direccion" name="direccion" value=""
						@blur="validateField('direccion');" :class="{
							valid: validFields.direccion, 
							invalid: !validFields.direccion
						}" v-model="user_data.direccion"><label for="direccion">Direccion:</label>
				</div>
				<div class="input-field">
					<input maxlength="50" type="text" id="telefono" name="telefono" value=""
						@blur="validateField('telefono');" :class="{
							valid: validFields.telefono, 
							invalid: !validFields.telefono
						}" v-model="user_data.telefono"><label for="telefono">Telefono:</label>
				</div>
				<br><br>
				<div class="switch">
					<label>
						Bloqueado
						<input v-model="status" type="checkbox" id="status" name="status" checked="checked">
						<span class="lever"></span>
						Permitido
					</label>
				</div>
				<br>
				<br>
				<div class="row userform">
					<div class="col s12" id="buttons">
						<a href="<?php echo base_url('admin/usuarios/'); ?>"
							class="btn btn-default waves-effect waves-teal btn-flat">Cancelar</a>
						<button type="submit" :class="{disabled: !btnEnable}"
							class="btn btn-primary waves-effect waves-teal" @click="save();">Guardar</button>
					</div>
				</div>

				<input type="hidden" name="mode" value="" v-model="editMode">
				<input type="hidden" name="id" value="" v-model="user_id">
			</div>
		</div>
		<div v-cloak v-if="!loader" class="col hide-on-small-only m2 l2">
			<ul class="section table-of-contents tabs-wrapper">
				<li><a href="#initialization">Perfil</a></li>
				<li><a href="#structure">Información</a></li>
			</ul>
		</div>
	</div>
</div>
<script>
	const user_id = <?= json_encode($userdata ? $userdata->user_id : false); ?>;
</script>
@endsection