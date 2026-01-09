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
			<br />
			<div class="input-field">
				<select name="tipo_form" v-model="template">
					<option value="0" disabled>Selecciona</option>
					<option v-for="template in templates" :key="template" :value="template">
						@{{ template }}
					</option>
				</select>
				<label>Template</label>
			</div>
			<div class="row">
				<div class="col s12">
					<p>
						Form Properties
					</p>
					<ul class="collection">
						<li class="collection-item" v-for="(propertie, index) in properties">
							<div class="row">
								<i class="material-icons" @click="removePropertie(properties, index)">remove_circle</i>
								<input class="col s5" type="text" v-model="propertie.name">
								<input class="col s5" type="text" v-model="propertie.value">
							</div>
						</li>
						<li  class="collection-item">
							<button class="btn waves-effect waves-light" name="action" @click="addPropertie(properties);">Add Propertie <i class="material-icons right">add_box</i></button>
						</li>
					</ul>
				</div>
			</div>
			<br />
			Campos del Formulario:
			<br />
			<br />
			<div>
				<a href="#!" class="btn" v-on:click="addItem()">Campo <i class="material-icons right">add_box</i></a>
			</div>
			<br />
			<ol class="default vertical">
				<li v-for="(item, index) in siteform_items" :key="index" :data-id="index" :data-name="item.item_name"
					:data-menuitem="item">
					<div class="collapsible expandable sorteable menuitem">
						<div class="collapsible-header">
							<i class="material-icons">navigate_next</i>
							<i class="material-icons" v-on:click="removeItem(index, siteform_items);">remove_circle</i>
							@{{item.item_name}}
							<i class="material-icons right icon-move">reorder</i>
						</div>
						<div class="collapsible-body">
							<div class="input-field">
								<select name="type" v-model="item.item_type" @change="handlerSelect($event, item);">
									<option v-for="(type, index) in items_types" :key="index" :value="type">
										@{{type | capitalize}}</option>
								</select>
							</div>
							<div class="row" v-if="item.item_type == 'select'">
								<div class="col s12">
									<p>
										Select Options
									</p>
									<div v-for="(propertie, index) in item.data" :key="index" v-if="propertie.name == 'select_options'">
										<div v-for="(val, i) in propertie['value']" :key="i">
											<div class="row">
												<i class="material-icons" @click="removePropertie(propertie['value'], i)">remove_circle</i>
												<input class="col s5" type="text" v-model="val.name">
												<input class="col s5" type="text" v-model="val.value">
											</div>
										</div>
										<button class="btn waves-effect waves-light" name="action" @click="addPropertie(propertie['value']);">Add Propertie <i class="material-icons right">add_box</i></button>
									</div>
								</div>
							</div>
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
								<label class="active" for="'item_placeholder' + index">item_placeholder:</label>
								<input type="text" v-model="item.item_placeholder" :id="'item_placeholder' + index"
									required="required" value="">
							</div>
							<div class="input-field">
								<label class="active" for="'item_title' + index">Title:</label>
								<input type="text" v-model="item.item_title" :id="'item_title' + index"
									required="required" value="">
							</div>
							<div class="input-field">
								<label class="active" for="'item_class' + index">item_class:</label>
								<input type="text" v-model="item.item_class" :id="'item_class' + index"
									required="required" value="">
							</div>
							<div class="row">
								<div class="col s12">
									<p>
										Item Properties
									</p>
									<ul class="collection">
										<li class="collection-item" v-for="(propertie, index) in item.properties">
											<div class="row">
												<i class="material-icons" @click="removePropertie(item.properties, index)">remove_circle</i>
												<input class="col s5" type="text" v-model="propertie.name">
												<input class="col s5" type="text" v-model="propertie.value">
											</div>
										</li>
										<li  class="collection-item">
											<button class="btn waves-effect waves-light" name="action" @click="addPropertie(item.properties);">Add Propertie <i class="material-icons right">add_box</i></button>
										</li>
									</ul>
								</div>
							</div>
							<div class="row">
								<div class="col s12">
									<p>
										Extra Data
									</p>
									<ul class="collection">
										<li class="collection-item" v-for="(propertie, index) in item.data" v-if="propertie.name != 'select_options'">
											<div class="row">
												<i class="material-icons" @click="removePropertie(item.data, index)">remove_circle</i>
												<input class="col s5" type="text" v-model="propertie.name">
												<input class="col s5" type="text" v-model="propertie.value">
											</div>
										</li>
										<li  class="collection-item">
											<button class="btn waves-effect waves-light" name="action" @click="addPropertie(item.data);">Add Data <i class="material-icons right">add_box</i></button>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</li>
			</ol>
			<br>

			Activar Site Form
			<div class="input-field">
				<div class="switch">
					<label>
						<?php echo lang('not_active'); ?>
						<input type="checkbox" name="status_form" value="1" v-model="status">
						<span class="lever"></span>
						<?php echo lang('active'); ?>
					</label>
				</div>
			</div>
			<br><br>
			<div class="input-field" id="buttons">
				<a href="<?php echo base_url('admin/categories/'); ?>" class="btn-flat"><?php echo lang('cancel'); ?></a>
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
				<b><?php echo lang('published'); ?></b>: <br>
				<span>@{{date_publish}}</span>
			</p>
		</div>
	</div>
</div>
<script>
	const siteform_id = <?= json_encode($siteform_id ? $siteform_id : false);?>;
	const editMode = <?= json_encode($editMode ? $editMode : 'new');?>;
</script>
@endsection

@section('footer_includes')
<script src="{{base_url('resources/components/SiteFormNewForm.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('public/js/jquery-sortable.js?v=' . ADMIN_VERSION)}}"></script>
@endsection