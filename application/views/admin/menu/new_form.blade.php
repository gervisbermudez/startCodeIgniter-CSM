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
		<div id="form" class="col s12" v-bind:class="{'m10': user_id}" v-cloak v-show="!loader">
			<input type="hidden" name="id_form" value="">
			<span class="header grey-text text-darken-2">Datos básicos <i
					class="material-icons left">description</i></span>
			<div class="input-field">
				<label for="nombre">Nombre:</label>
				<input type="text" v-model="name" id="nombre" name="nombre_form" required="required" value="">
			</div>
			<div class="input-field">
				<select name="tipo_form" v-model="template">
					<option value="0" disabled>Selecciona</option>
					<option v-for="template in templates" :key="template" :value="template">
						@{{ template }}
					</option>
				</select>
				<label>Template</label>
			</div>
			Items del Menu:
			<br />
			<br />
			<div>
				<a href="#!" class="btn" v-on:click="addItem()">Agregar Item <i
						class="material-icons right">add_box</i></a>
			</div>
			<br />
			<ol class="default vertical">
				<li v-for="(item, index) in menu_items" :key="index" :data-id="index" :data-name="item.item_name">
					<div class="collapsible expandable sorteable menuitem">
						<div class="collapsible-header">
							<i class="material-icons">navigate_next</i>
							<i class="material-icons" v-on:click="removeItem(index);">remove_circle</i>
							@{{item.item_name}}
							<i class="material-icons right icon-move">reorder</i>
						</div>
						<div class="collapsible-body">
							<div class="input-field">
								<label class="active" for="'nombre-' + index">Nombre:</label>
								<input type="text" v-model="item.item_name" :id="'nombre-' + index" required="required"
									value="">
							</div>
							<div class="input-field">
								<label class="active" for="'item_label' + index">Label:</label>
								<input type="text" v-model="item.item_label" :id="'item_label' + index"
									required="required" value="">
							</div>
							<div class="input-field">
								<label class="active" for="'item_link' + index">Link:</label>
								<input type="text" v-model="item.item_link" :id="'item_link' + index"
									required="required" value="">
							</div>
							<div class="input-field">
								<label class="active" for="'item_target' + index">Target:</label>
								<input type="text" v-model="item.item_target" :id="'item_target' + index"
									required="required" value="">
							</div>
							<div class="input-field">
								<label class="active" for="'item_title' + index">Title:</label>
								<input type="text" v-model="item.item_title" :id="'item_title' + index"
									required="required" value="">
							</div>
						</div>
					</div>
					<ol>
						<li v-for="(subitem, i) in item.subitems" :key="i" :data-id="i + 1000"
							:data-name="subitem.item_name">
							<div class="collapsible expandable sorteable menuitem">
								<div class="collapsible-header">
									<i class="material-icons">navigate_next</i>
									<i class="material-icons" v-on:click="removeItem(index);">remove_circle</i>
									@{{subitem.item_name}}
									<i class="material-icons right icon-move">reorder</i>
								</div>
								<div class="collapsible-body">
									<div class="input-field">
										<label class="active" for="'nombre-' + index">Nombre:</label>
										<input type="text" v-model="subitem.item_name" :id="'nombre-' + index"
											required="required" value="">
									</div>
									<div class="input-field">
										<label class="active" for="'item_label' + index">Label:</label>
										<input type="text" v-model="subitem.item_label" :id="'item_label' + index"
											required="required" value="">
									</div>
									<div class="input-field">
										<label class="active" for="'item_link' + index">Link:</label>
										<input type="text" v-model="subitem.item_link" :id="'item_link' + index"
											required="required" value="">
									</div>
									<div class="input-field">
										<label class="active" for="'item_target' + index">Target:</label>
										<input type="text" v-model="subitem.item_target" :id="'item_target' + index"
											required="required" value="">
									</div>
									<div class="input-field">
										<label class="active" for="'item_title' + index">Title:</label>
										<input type="text" v-model="subitem.item_title" :id="'item_title' + index"
											required="required" value="">
									</div>
								</div>
							</div>
						</li>
					</ol>
				</li>
			</ol>
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
				<a href="<?php echo base_url('admin/categorias/'); ?>" class="btn-flat">Cancelar</a>
				<button type="submit" class="btn btn-primary" @click="save()" :class="{disabled: !btnEnable}">
					<span><i class="material-icons right">edit</i> Guardar</span>
				</button>
			</div>
		</div>
		<div class="col s12" v-bind:class="{'m2': user_id}" v-cloak v-if="user_id" v-show="!loader">
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
	const menu_id = <?= json_encode($menu_id ? $menu_id : false);?>;
	const editMode = <?= json_encode($editMode ? $editMode : 'new');?>;
</script>
@endsection

@section('footer_includes')
<script src="{{base_url('public/js/components/MenuNewForm.min.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('public/js/jquery-sortable.js?v=' . ADMIN_VERSION)}}"></script>
@endsection