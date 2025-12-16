@extends('admin.layouts.app')
@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/css/admin/form.min.css')?>">
@endsection
@section('content')
<div class="container form" id="root">
	<div class="row">
		<div class="col s12">
			<h3 class="page-header">{{$h1}}</h3>
		</div>
	</div>
	<div class="row">
		<div class="col s12 center" v-bind:class="{ hide: !loader }">
		<preloader />
		</div>
		<div id="form" class="col s12" v-bind:class="{'m10': user_id}" v-cloak v-show="!loader">
			<input type="hidden" name="id_form" value="">
			<span class="header grey-text text-darken-2">Datos básicos <i
					class="material-icons left">description</i></span>
			<br>
			<div class="input-field">
				<label for="nombre">Nombre:</label>
				<input type="text" v-model="name" id="nombre" name="nombre_form" required="required" value="">
			</div>
			<br/>
			<div class="input-field">
				<label for="description">Description:</label>
				<input type="text" v-model="description" id="description" name="description_form" required="required" value="">
			</div>
			<p>
				Check all permissions by type:
			</p>
			<div class="row">
				<div class="col s12 m2">
					<p>
						<label>
							<input type="checkbox" @change="checkAll($event, 'CREATE')"/>
							<span>All CREATE</span>
						</label>
					</p>
				</div>
				<div class="col s12 m2">
					<p>
						<label>
							<input type="checkbox" @change="checkAll($event, 'UPDATE')"/>
							<span>All UPDATE</span>
						</label>
					</p>
				</div>
				<div class="col s12 m2">
					<p>
						<label>
							<input type="checkbox" @change="checkAll($event, 'DELETE')"/>
							<span>All DELETE</span>
						</label>
					</p>
				</div>
				<div class="col s12 m2">
					<p>
						<label>
							<input type="checkbox" @change="checkAll($event, 'SELECT')"/>
							<span>All SELECT</span>
						</label>
					</p>
				</div>
			</div>
			<p v-for="(permission, index) in permissions" :key="index">
				<label>
					<input type="checkbox" :value="permission.permission_name" v-model="permission.enabled"/>
					<span>@{{permission.permision_name}}</span>
				</label>
			</p>
			<br/>
			<br />
			Activar Grupo
			<div class="input-field">
				<div class="switch">
					<label>
						No Activo
						<input type="checkbox" name="status_form" value="1" v-model="status">
						<span class="lever"></span>
						Activo
					</label>
				</div>
			</div>
			<br><br>
			<div class="input-field" id="buttons">
				<a href="<?php echo base_url('admin/users/usergroups'); ?>" class="btn-flat">Cancelar</a>
				<button type="submit" class="btn btn-primary" @click="save()" :class="{disabled: !btnEnable}">
					<span><i class="material-icons right">edit</i> Guardar</span>
				</button>
			</div>
		</div>
		<div class="col s12" v-bind:class="{'m2': user_id}" v-cloak v-if="user_id" v-show="!loader">
			<span class="header grey-text text-darken-2">Adicional <i class="material-icons left">description</i></span>
			<p>
				<b>Creado por</b>:
				
			</p>
			<p>
				<b>Creado</b>: <br>
				<span>@{{date_create}}</span> <br><br>
				<b>Modificado</b>: <br>
				<span>@{{date_update}}</span> <br><br>
			</p>
		</div>
	</div>
</div>
<script>
	const usergroup_id = <?=json_encode($usergroup_id ? $usergroup_id : false);?>;
	const editMode = <?=json_encode($editMode ? $editMode : 'new');?>;
</script>
	@include('admin.components.DataSelector')
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/UserPermissionsForm.js?v=' . ADMIN_VERSION)}}"></script>
@endsection