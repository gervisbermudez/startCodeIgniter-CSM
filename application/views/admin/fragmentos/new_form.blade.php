@extends('admin.layouts.app')
@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/js/fileinput-master/css/fileinput.min.css')?>">
<link rel="stylesheet" href="<?=base_url('public/font-awesome/css/all.min.css')?>">
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
				<div class="input-field">
					<label for="nombre">Nombre:</label>
					<input type="text" v-model="form.fields.name.value" id="nombre" name="nombre_form" required="required"
						value="">
				</div>
				<div id="introduction" class="section scrollspy">
					<label for="id_cazary">Contenido:</label>
					<div class="input-field">
						<textarea id="id_cazary" v-model="description" name="descripcion_form"></textarea>
					</div>
					<br>
				</div>
				<div class="input-field">
					<select name="tipo_form" v-model="type">
						<option value="0" disabled>Selecciona</option>
						<option v-for="fragment_type in fragment_types" :key="fragment_type" :value="fragment_type">
							@{{ fragment_type }}
						</option>
					</select>
					<label>Tipo de Fragmento</label>
				</div>
				<br>
				Publicar Fragmento
				<br>
				<div class="input-field">
					<div class="switch">
						<label>
							No publicado
							<input type="checkbox" name="status_form" value="1" v-model="status">
							<span class="lever"></span>
							Publicado
						</label>
					</div>
				</div>
				<br><br>
				<div class="input-field" id="buttons">
					<a href="<?php echo base_url('admin/Fragments/'); ?>" class="btn-flat">Cancelar</a>
					<button type="submit" class="btn btn-primary" @click="save()" :class="{disabled: !btnEnable}">
						<span><i class="material-icons right">edit</i> Guardar</span>
					</button>
				</div>
			</div>
			<div class="col s12" v-bind:class="{'m2': user_id}" v-cloak v-if="user_id"  v-show="!loader">
			<span class="header grey-text text-darken-2">Adicional <i class="material-icons left">description</i></span>
				<p>
					<b>Creado por</b>:
					<user-info :user="user" />
				</p>
				<p>
					<b>Creado</b>: <br>
					<span>@{{date_create}}</span> <br><br>
					<b>Modificado</b>: <br>
					<span>@{{date_update}}</span> <br><br>
					<b>Publicado</b>: <br>
					<span>@{{date_publish}}</span>
				</p>
			</div>
		</div>
	</div>
<script>
	const fragment_id = <?=json_encode($fragment_id ? $fragment_id : false);?>;
	const editMode = <?=json_encode($editMode ? $editMode : 'new');?>;
</script>
@endsection

@section('footer_includes')
<script src="{{base_url('public/js/tinymce/js/tinymce/tinymce.min.js')}}"></script>
<script src="{{base_url('public/js/validateForm.min.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('public/js/components/FragmentNewForm.min.js?v=' . ADMIN_VERSION)}}"></script>
@endsection