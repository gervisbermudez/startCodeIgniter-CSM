@extends('admin.layouts.app')
@section('title', $title)

@section('head_includes')
<link rel="stylesheet" href="<?=base_url('public/css/admin/form.min.css')?>">
@endsection
@section('content')
<div class="container form" id="root">
	<div class="row">
		<div class="col s12">
			<h3 class="page-header"><?= lang('menu_new_menu') ?></h3>
		</div>
	</div>
	<div class="row">
		<div class="col s12 center" v-bind:class="{ hide: !loader }">
		<preloader />
		</div>
		<div id="form" class="col s12" v-bind:class="{'m10': user_id}" v-cloak v-show="!loader">
			<input type="hidden" name="id_form" value="">
			<span class="header grey-text text-darken-2"><?= lang('menu_basic_data') ?> <i
					class="material-icons left">description</i></span>
			<br>
			<div class="input-field">
				<label for="nombre"><?= lang('menu_name') ?>:</label>
				<input type="text" v-model="name" id="nombre" name="nombre_form" required="required" value="">
			</div>
			<br/>
			<div class="input-field">
				<select name="tipo_form" v-model="template">
					<option value="0" disabled>Selecciona</option>
					<option v-for="template in templates" :key="template" :value="template">
						@{{ template }}
					</option>
				</select>
				<label>Template</label>
			</div>
			<br/>
			<div class="input-field">
				<select v-model="position">
				<optgroup v-for="(theme, index) in positions" :label="theme.name" :key="index">
					<option v-for="(menu, i) in theme.menus" :value="theme.theme_folder + '/' + menu" :key="i">@{{menu}}</option>
				</optgroup>
				</select>
				<label>Position</label>
			</div>
			<br />
			<?= lang('menu_items') ?>:
			<br />
			<br />
			<div>
				<a href="#" class="btn" v-on:click="addItem()">{{ lang('menu_add_item') }} <i
						class="material-icons right">add_box</i></a>
			</div>
			<br />
			<ol class="default vertical">
				<li v-for="(item, index) in menu_items" :key="index" :data-id="index" :data-name="item.item_name" :data-menuitem="item">
					<div class="collapsible expandable sorteable menuitem">
						<div class="collapsible-header">
							<i class="material-icons">navigate_next</i>
							<i class="material-icons" v-on:click="removeItem(index, menu_items);">remove_circle</i>
							@{{item.item_name}}
							<i class="material-icons right icon-move">reorder</i>
						</div>
						<div class="collapsible-body">
							<a class="waves-effect waves-light btn" href="#" @click="openPageSelector(item)"><i class="material-icons left">add_to_photos</i> {{ lang('menu_select_page') }}</a>
							<br />
							<br />
							<div class="input-field">
								<label class="active" for="'nombre-' + index">{{ lang('menu_name') }}:</label>
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
								<i class="material-icons prefix" v-if="!isEditable(item)" @click="makeEditable(item);">edit</i>
								<input type="text" v-model="item.item_link" :disabled="!isEditable(item)" :id="'item_link' + index"
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
									<i class="material-icons" v-on:click="removeItem(i, item.subitems);">remove_circle</i>
									@{{subitem.item_name}}
									<i class="material-icons right icon-move">reorder</i>
								</div>
								<div class="collapsible-body">
								<a class="waves-effect waves-light btn" href="#" @click="openPageSelector(item)"><i class="material-icons left">add_to_photos</i> {{ lang('menu_select_page') }}</a>
												<br>
												<br>
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
										<i class="material-icons prefix" v-if="!isEditable(item)" @click="makeEditable(item);">edit</i>
										<input type="text" v-model="subitem.item_link" :disabled="!isEditable(item)" :id="'item_link' + index"
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
			<br>

			Activar Menu
			<div class="input-field">
				<div class="switch">
					<label>
						{{ lang('menu_not_active') }}
						<input type="checkbox" name="status_form" value="1" v-model="status">
						<span class="lever"></span>
						{{ lang('menu_active') }}
					</label>
				</div>
			</div>
			<br><br>
			<div class="input-field" id="buttons">
				<a href="<?php echo base_url('admin/categories/'); ?>" class="btn-flat">{{ lang('cancel') }}</a>
				<button type="submit" class="btn btn-primary" @click="save()" :class="{disabled: !btnEnable}">
					<span><i class="material-icons right">edit</i> {{ lang('save') }}</span>
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
	<data-selector
        :modal="'folderSelectorCopy'"
        :preselected="[]"
		:mode="'folders'"
		:models="['pages']"
        :multiple="false"
        v-on:notify="copyCallcack"
        >
        </data-selector>
</div>
<script>
	const menu_id = <?=json_encode($menu_id ? $menu_id : false);?>;
	const editMode = <?=json_encode($editMode ? $editMode : 'new');?>;
</script>
	@include('admin.components.DataSelectorComponent')
@endsection

@section('footer_includes')
<script src="<?=base_url('resources/components/DataSelector.js');?>"></script>
<script src="{{base_url('resources/components/MenuNewForm.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('public/js/jquery-sortable.js?v=' . ADMIN_VERSION)}}"></script>
@endsection