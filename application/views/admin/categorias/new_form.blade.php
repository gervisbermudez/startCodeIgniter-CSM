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
					<label for="id_cazary">Descripcion de la categoria:</label>
					<div class="input-field">
						<textarea id="id_cazary" v-model="description" name="descripcion_form"></textarea>
					</div>
					<br>
				</div>
				<div class="input-field">
					<select name="tipo_form" v-model="type"  v-on:change="getCategories">
						<option value="0" disabled>Selecciona</option>
						<option v-for="categorie_type in categories_type" :key="categorie_type" :value="categorie_type">
							@{{ categorie_type | capitalize }}
						</option>
					</select>
					<label>Tipo de Categoria</label>
				</div>
				<div class="input-field" v-if="subcategories.length == 0">
					<br>
					<select v-model="parent_id" id="subcategories" name="subcategories">
						<option value="0">Ninguna</option>
						<option v-for="(item, index) in categories" :key="index" :value="item.categorie_id">
							@{{item.name | capitalize}}</option>
					</select>
					<label>Categoria padre: </label>
					<br>
				</div>

				<br>
				Publicar categoria
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
					<a href="<?php echo base_url('admin/categories/'); ?>" class="btn-flat">Cancelar</a>
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
				<p v-if="parent">
					<b>Categoria Padre:</b>: <br>
					<span>@{{parent.name}}</span> <br><br>
				</p>
				<p v-if="subcategories.length">
					<b>Subcategorias:</b> <br>
					<div v-for="categorie in subcategories" :key="categorie.id">
						<span>• @{{ categorie.name }}</span>
					</div>
				</p>
			</div>
		</div>
	</div>
<script>
	const categorie_id = <?=json_encode($categorie_id ? $categorie_id : false);?>;
	const editMode = <?=json_encode($editMode ? $editMode : 'new');?>;
</script>
@endsection

@section('footer_includes')
<script src="{{base_url('public/js/tinymce/js/tinymce/tinymce.min.js')}}"></script>
<script src="{{base_url('public/js/validateForm.min.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('public/js/components/CategoriaNewForm.min.js?v=' . ADMIN_VERSION)}}"></script>
@endsection