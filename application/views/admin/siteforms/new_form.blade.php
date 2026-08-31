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
			<span class="header grey-text text-darken-2"><i class="material-icons left">description</i>{{ lang('siteforms_basic') }}</span>
			<br>
			<div class="input-field">
				<label for="nombre">{{ lang('siteforms_name') }}</label>
				<input type="text" v-model="name" id="nombre" name="nombre_form" required="required" value="">
			</div>
			<p class="grey-text">{{ lang('siteforms_name_help') }}</p>
			<div class="input-field">
				<select name="tipo_form" v-model="template">
					<option value="" disabled>{{ lang('siteforms_select_template') }}</option>
					<option v-for="template in templates" :key="template" :value="template">
						@{{ template }}
					</option>
				</select>
				<label>{{ lang('siteforms_template') }}</label>
			</div>
			<div class="row">
				<div class="col s12">
					<a href="#!" class="btn-flat" @click.prevent="showAdvanced = !showAdvanced">{{ lang('siteforms_advanced') }}</a>
					<div v-show="showAdvanced">
						<p>{{ lang('siteforms_properties') }}</p>
						<p class="grey-text">{{ lang('siteforms_properties_help') }}</p>
						<ul class="collection">
							<li class="collection-item" v-for="(propertie, index) in properties">
								<div class="row">
									<i class="material-icons" @click="removePropertie(properties, index)">remove_circle</i>
									<input class="col s5" type="text" v-model="propertie.name" :placeholder="'{{ lang('siteforms_item_name') }}'">
									<input class="col s5" type="text" v-model="propertie.value" :placeholder="'{{ lang('siteforms_option_value') }}'">
								</div>
							</li>
							<li class="collection-item">
								<button type="button" class="btn waves-effect waves-light" @click="addPropertie(properties);">{{ lang('siteforms_add_attribute') }} <i class="material-icons right">add_box</i></button>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<p>{{ lang('siteforms_fields') }}</p>
			<div>
				<a href="#!" class="btn" v-on:click="addItem()">{{ lang('siteforms_add_field') }} <i class="material-icons right">add_box</i></a>
			</div>
			<br />
			<ol class="default vertical">
				<li v-for="(item, index) in siteform_items" :key="index" :data-id="index" :data-name="item.item_name">
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
									<option v-for="(type, typeIndex) in items_types" :key="typeIndex" :value="type">
										@{{type | capitalize}}</option>
								</select>
							</div>
							<div class="row" v-if="item.item_type == 'select'">
								<div class="col s12">
									<p>{{ lang('siteforms_select_options') }}</p>
									<div v-for="(propertie, pIndex) in item.data" :key="pIndex" v-if="propertie.name == 'select_options'">
										<div v-for="(val, i) in propertie['value']" :key="i">
											<div class="row">
												<i class="material-icons" @click="removePropertie(propertie['value'], i)">remove_circle</i>
												<input class="col s5" type="text" v-model="val.name" placeholder="{{ lang('siteforms_option_label') }}">
												<input class="col s5" type="text" v-model="val.value" placeholder="{{ lang('siteforms_option_value') }}">
											</div>
										</div>
										<button type="button" class="btn waves-effect waves-light" @click="addPropertie(propertie['value']);">{{ lang('siteforms_add_option') }} <i class="material-icons right">add_box</i></button>
									</div>
								</div>
							</div>
							<div class="input-field">
								<label class="active" :for="'nombre-' + index">{{ lang('siteforms_item_name') }}</label>
								<input type="text" v-model="item.item_name" :id="'nombre-' + index" required="required">
							</div>
							<div class="input-field">
								<label class="active" :for="'item_label' + index">{{ lang('siteforms_item_label') }}</label>
								<input type="text" v-model="item.item_label" :id="'item_label' + index" required="required">
							</div>
							<div class="input-field">
								<label class="active" :for="'item_placeholder' + index">{{ lang('siteforms_item_placeholder') }}</label>
								<input type="text" v-model="item.item_placeholder" :id="'item_placeholder' + index">
							</div>
							<div class="input-field">
								<label class="active" :for="'item_title' + index">{{ lang('siteforms_item_title') }}</label>
								<input type="text" v-model="item.item_title" :id="'item_title' + index">
							</div>
							<div class="input-field">
								<label class="active" :for="'item_class' + index">{{ lang('siteforms_item_class') }}</label>
								<input type="text" v-model="item.item_class" :id="'item_class' + index">
							</div>
							<div class="row">
								<div class="col s12">
									<p>{{ lang('siteforms_item_properties') }}</p>
									<ul class="collection">
										<li class="collection-item" v-for="(propertie, propIndex) in item.properties">
											<div class="row">
												<i class="material-icons" @click="removePropertie(item.properties, propIndex)">remove_circle</i>
												<input class="col s5" type="text" v-model="propertie.name">
												<input class="col s5" type="text" v-model="propertie.value">
											</div>
										</li>
										<li class="collection-item">
											<button type="button" class="btn waves-effect waves-light" @click="addPropertie(item.properties);">{{ lang('siteforms_add_attribute') }} <i class="material-icons right">add_box</i></button>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</li>
			</ol>
			<br>
			{{ lang('siteforms_activate') }}
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
			<p v-if="!btnEnable" class="grey-text">{{ lang('siteforms_save_hint') }}</p>
			<div class="input-field" id="buttons">
				<a href="<?php echo base_url('admin/siteforms'); ?>" class="btn-flat"><?php echo lang('cancel'); ?></a>
				<button type="button" class="btn btn-primary" @click="save()" :class="{disabled: !btnEnable}" :disabled="!btnEnable">
					<span><i class="material-icons right">save</i> <?php echo lang('save'); ?></span>
				</button>
			</div>
		</div>
		<div class="col s12" v-bind:class="{'m2': user_id}" v-cloak v-if="user_id" v-show="!loader">
			<span class="header grey-text text-darken-2"><i class="material-icons left">description</i>{{ lang('siteforms_additional') }}</span>
			<p>
				<b>{{ lang('siteforms_created_by') }}</b>:
				<user-info :user="user" />
			</p>
			<p>
				<b>{{ lang('siteforms_created') }}</b>: <br>
				<span>@{{date_create}}</span> <br><br>
				<b>{{ lang('siteforms_updated') }}</b>: <br>
				<span>@{{date_update}}</span>
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
@include('admin.siteforms.siteforms_i18n')
<script src="{{base_url('resources/components/SiteFormNewForm.js?v=' . ADMIN_VERSION)}}"></script>
<script src="{{base_url('public/js/jquery-sortable.js?v=' . ADMIN_VERSION)}}"></script>
@endsection
